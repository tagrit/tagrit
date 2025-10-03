<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * File sharing model
 */
class File_sharing_model extends App_Model {
	/**
	 * change staff permissions
	 * @param  integer $id
	 * @param  integer $status
	 * @return boolean
	 */
	public function change_staff_permissions($id, $status) {
		$this->db->where('staffid', $id);
		$this->db->update(db_prefix() . 'staff', [
			'active' => $status,
		]);

		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add new share
	 * @param array $data
	 * @return integer
	 */
	public function add_new_share($data) {

		if (isset($data['id'])) {
			unset($data['id']);
		}

		if (isset($data['public_link'])) {
			unset($data['public_link']);
		}

		if (!isset($data['_is_read'])) {
			$data['is_read'] = 0;
		} else {
			$data['is_read'] = 1;
			unset($data['_is_read']);
		}

		if (!isset($data['_is_write'])) {
			$data['is_write'] = 0;
		} else {
			$data['is_write'] = 1;
			unset($data['_is_write']);
		}

		if (!isset($data['_is_delete'])) {
			$data['is_delete'] = 0;
		} else {
			$data['is_delete'] = 1;
			unset($data['_is_delete']);
		}

		if (!isset($data['_is_upload'])) {
			$data['is_upload'] = 0;
		} else {
			$data['is_upload'] = 1;
			unset($data['_is_upload']);
		}

		if (!isset($data['_is_download'])) {
			$data['is_download'] = 0;
		} else {
			$data['is_download'] = 1;
			unset($data['_is_download']);
		}

		if ($data['type'] == 'fs_public') {
			if (isset($data['_public_is_download'])) {
				$data['is_download'] = 1;
				unset($data['_public_is_download']);
			}
		} else {
			if (isset($data['_public_is_download'])) {
				unset($data['_public_is_download']);
			}
		}

		if (!isset($data['expiration_date_apply'])) {
			$data['expiration_date_apply'] = 0;
		}

		if (!isset($data['expiration_date_delete'])) {
			$data['expiration_date_delete'] = 0;
		}

		if (!isset($data['download_limits_apply'])) {
			$data['download_limits_apply'] = 0;
		}

		if (!isset($data['download_limits_delete'])) {
			$data['download_limits_delete'] = 0;
		}

		if (isset($data['role'])) {
			$role = $data['role'];
			unset($data['role']);
		}

		if (isset($data['staff'])) {
			$staff = $data['staff'];
			unset($data['staff']);
		}

		if (isset($data['customer'])) {
			$customer = $data['customer'];
			unset($data['customer']);
		}

		if (isset($data['customer_group'])) {
			$customer_group = $data['customer_group'];
			unset($data['customer_group']);
		}

		if (isset($data['password'])) {
			if ($data['password'] != '') {
				$data['password'] = $this->AES_256_Encrypt($data['password']);
			}
		}

		if (!isset($data['hash_share']) || $data['hash_share'] == '') {
			$data['hash_share'] = $this->generate_hash();
		}
		
		$data['created_at'] = get_staff_user_id();
		$data['expiration_date'] = to_sql_date($data['expiration_date']);
		$data['inserted_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->db->insert(db_prefix() . 'fs_sharings', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			if (isset($role)) {
				foreach ($role as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
						'share_id' => $insert_id,
						'type' => 'role',
						'value' => $value,
					]);
				}
			}
			if (isset($staff)) {
				foreach ($staff as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
						'share_id' => $insert_id,
						'type' => 'staff',
						'value' => $value,
					]);
				}
			}
			if (isset($customer_group)) {
				foreach ($customer_group as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
						'share_id' => $insert_id,
						'type' => 'customer_group',
						'value' => $value,
					]);
				}
			}
			if (isset($customer)) {
				foreach ($customer as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
						'share_id' => $insert_id,
						'type' => 'customer',
						'value' => $value,
					]);
				}
			}

			if (get_option('fs_global_notification') == 1) {
				$link = 'file_sharing/manage';
				if ($data['type'] == 'fs_staff') {
					$list_staffs = $this->get_staff_by_sharing($insert_id);
					foreach ($list_staffs as $value) {
						$this->notifications($value['staffid'], $link, _l('just_shared_files_with_you'));
					}
				}
			}
			if (get_option('fs_global_email') == 1) {
				if ($data['type'] == 'fs_staff') {
					$list_staffs = $this->get_staff_by_sharing($insert_id);
					foreach ($list_staffs as $value) {
						$data_email = (object) [];
						$data_email->sender = get_staff_full_name();
						$data_email->receiver = $value['firstname'] . ' ' . $value['lastname'];
						$data_email->file_name = $data['name'];
						$data_email->share_link = admin_url('file_sharing/manage');
						$data_email->mail_to = $value['email'];

						$template = mail_template('fs_share_staff', 'file_sharing', $data_email);
						$template->send();
					}
				} elseif ($data['type'] == 'fs_client') {
					$list_clients = $this->get_client_by_sharing($insert_id);
					foreach ($list_clients as $value) {
						$data_email = (object) [];
						$data_email->sender = get_staff_full_name();
						$data_email->receiver = $value['company'];
						$data_email->file_name = $data['name'];
						$data_email->share_link = site_url('file_sharing/file_sharing_client');
						$data_email->mail_to = $value['email'];

						$template = mail_template('fs_share_client', 'file_sharing', $data_email);
						$template->send();
					}
				}
			}
			return $insert_id;
		}

		return false;
	}

	/**
	 * update sharing
	 * @param  object $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_sharing($data, $id) {
		$affectedRows = 0;

		if (isset($data['public_link'])) {
			unset($data['public_link']);
		}

		if (!isset($data['is_read'])) {
			$data['is_read'] = 0;
		}

		if (!isset($data['is_write'])) {
			$data['is_write'] = 0;
		}

		if (!isset($data['is_delete'])) {
			$data['is_delete'] = 0;
		}

		if (!isset($data['is_upload'])) {
			$data['is_upload'] = 0;
		}

		if (!isset($data['is_download'])) {
			$data['is_download'] = 0;
		}

		if ($data['type'] == 'fs_public') {
			if (isset($data['public_is_download'])) {
				$data['is_download'] = 1;
				unset($data['public_is_download']);
			}
		} else {
			if (isset($data['public_is_download'])) {
				unset($data['public_is_download']);
			}
		}

		if (!isset($data['download_limits_delete'])) {
			$data['download_limits_delete'] = 0;
		}

		if (!isset($data['expiration_date_delete'])) {
			$data['expiration_date_delete'] = 0;
		}

		if (!isset($data['expiration_date_apply'])) {
			$data['expiration_date_apply'] = 0;
		}

		if (!isset($data['download_limits_apply'])) {
			$data['download_limits_apply'] = 0;
		}

		if (isset($data['role'])) {
			$role = $data['role'];
			unset($data['role']);
		}

		if (isset($data['staff'])) {
			$staff = $data['staff'];
			unset($data['staff']);
		}

		if (isset($data['customer'])) {
			$customer = $data['customer'];
			unset($data['customer']);
		}

		if (isset($data['customer_group'])) {
			$customer_group = $data['customer_group'];
			unset($data['customer_group']);
		}

		if (isset($data['password'])) {
			if ($data['password'] != '') {
				$data['password'] = $this->AES_256_Encrypt($data['password']);
			}
		}

		$data['expiration_date'] = to_sql_date($data['expiration_date']);
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'fs_sharings', $data);

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		$this->db->where('share_id', $id);
		$this->db->delete(db_prefix() . 'fs_sharing_relationship');

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		if (isset($role)) {
			foreach ($role as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
					'share_id' => $id,
					'type' => 'role',
					'value' => $value,
				]);
			}
		}
		if (isset($staff)) {
			foreach ($staff as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
					'share_id' => $id,
					'type' => 'staff',
					'value' => $value,
				]);
			}
		}
		if (isset($customer_group)) {
			foreach ($customer_group as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
					'share_id' => $id,
					'type' => 'customer_group',
					'value' => $value,
				]);
			}
		}
		if (isset($customer)) {
			foreach ($customer as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_sharing_relationship', [
					'share_id' => $id,
					'type' => 'customer',
					'value' => $value,
				]);
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * add new share
	 * @param array $data
	 * @return integer
	 */
	public function add_new_config($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}

		if (isset($data['role'])) {
			$role = $data['role'];
			unset($data['role']);
		}

		if (isset($data['staff'])) {
			$staff = $data['staff'];
			unset($data['staff']);
		}

		if (isset($data['customer'])) {
			$customer = $data['customer'];
			unset($data['customer']);
		}

		if (isset($data['customer_group'])) {
			$customer_group = $data['customer_group'];
			unset($data['customer_group']);
		}

		if (!isset($data['is_read'])) {
			$data['is_read'] = 0;
		}

		if (!isset($data['is_write'])) {
			$data['is_write'] = 0;
		}

		if (!isset($data['is_delete'])) {
			$data['is_delete'] = 0;
		}

		if (!isset($data['is_upload'])) {
			$data['is_upload'] = 0;
		}

		if (!isset($data['is_download'])) {
			$data['is_download'] = 0;
		}

		$data['created_at'] = get_staff_user_id();
		$data['inserted_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->db->insert(db_prefix() . 'fs_setting_configuration', $data);

		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			if (isset($role)) {
				foreach ($role as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
						'configuration_id' => $insert_id,
						'rel_type' => 'role',
						'rel_id' => $value,
					]);
				}
			}
			if (isset($staff)) {
				foreach ($staff as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
						'configuration_id' => $insert_id,
						'rel_type' => 'staff',
						'rel_id' => $value,
					]);
				}
			}
			if (isset($customer_group)) {
				foreach ($customer_group as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
						'configuration_id' => $insert_id,
						'rel_type' => 'customer_group',
						'rel_id' => $value,
					]);
				}
			}
			if (isset($customer)) {
				foreach ($customer as $key => $value) {
					$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
						'configuration_id' => $insert_id,
						'rel_type' => 'customer',
						'rel_id' => $value,
					]);
				}
			}

			return $insert_id;
		}

		return false;
	}

	/**
	 * delete config
	 * @param integer $id
	 * @return boolean
	 */

	public function delete_config($id) {
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'fs_setting_configuration');
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
	}

	/**
	 * edit configuration
	 * @param array $data
	 * @return integer
	 */
	public function edit_new_config($data, $id) {
		$affectedRows = 0;

		if (isset($data['id'])) {
			unset($data['id']);
		}

		if (isset($data['role'])) {
			$role = $data['role'];
			unset($data['role']);
		}

		if (isset($data['staff'])) {
			$staff = $data['staff'];
			unset($data['staff']);
		}

		if (isset($data['customer'])) {
			$customer = $data['customer'];
			unset($data['customer']);
		}

		if (isset($data['customer_group'])) {
			$customer_group = $data['customer_group'];
			unset($data['customer_group']);
		}

		if (!isset($data['is_read'])) {
			$data['is_read'] = 0;
		}

		if (!isset($data['is_write'])) {
			$data['is_write'] = 0;
		}

		if (!isset($data['is_delete'])) {
			$data['is_delete'] = 0;
		}

		if (!isset($data['is_upload'])) {
			$data['is_upload'] = 0;
		}

		if (!isset($data['is_download'])) {
			$data['is_download'] = 0;
		}

		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'fs_setting_configuration', $data);
		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		$this->db->where('configuration_id', $id);
		$this->db->where('rel_type', 'role');
		$this->db->delete(db_prefix() . 'fs_setting_configuration_relationship');

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		$this->db->where('configuration_id', $id);
		$this->db->where('rel_type', 'staff');
		$this->db->delete(db_prefix() . 'fs_setting_configuration_relationship');

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		$this->db->where('configuration_id', $id);
		$this->db->where('rel_type', 'customer');
		$this->db->delete(db_prefix() . 'fs_setting_configuration_relationship');

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		$this->db->where('configuration_id', $id);
		$this->db->where('rel_type', 'customer_group');
		$this->db->delete(db_prefix() . 'fs_setting_configuration_relationship');

		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}

		if (isset($role)) {
			foreach ($role as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
					'configuration_id' => $id,
					'rel_type' => 'role',
					'rel_id' => $value,
				]);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedRows++;
				}
			}
		}
		if (isset($staff)) {
			foreach ($staff as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
					'configuration_id' => $id,
					'rel_type' => 'staff',
					'rel_id' => $value,
				]);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedRows++;
				}
			}
		}
		if (isset($customer_group)) {
			foreach ($customer_group as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
					'configuration_id' => $id,
					'rel_type' => 'customer_group',
					'rel_id' => $value,
				]);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedRows++;
				}
			}
		}
		if (isset($customer)) {
			foreach ($customer as $key => $value) {
				$this->db->insert(db_prefix() . 'fs_setting_configuration_relationship', [
					'configuration_id' => $id,
					'rel_type' => 'customer',
					'rel_id' => $value,
				]);
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$affectedRows++;
				}
			}
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * add new share
	 * @param array $data
	 * @return integer
	 */
	public function edit_new_share($data) {

		if (is_array($data['rel_id'])) {
			$data['rel_id'] = implode(',', $data['rel_id']);
		}

		if (isset($data['rel_group'])) {
			$data['rel_group'] = implode(',', $data['rel_group']);
			//remove all space
			$data['rel_group'] = preg_replace('/\s/', '', $data['rel_group']);
		}

		//remove all space
		$data['rel_id'] = preg_replace('/\s/', '', $data['rel_id']);

		$data['created_at'] = get_staff_user_id();
		$data['expiration_date'] = date('Y-m-d H:i:s', strtotime($data['expiration_date']));

		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'fs_setting_configuration', $data);

		return true;
	}

	/**
	 * generate hash
	 * @return string
	 */
	public function generate_hash() {
		$length = 6;
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$password .= mb_substr($chars, $index, 1);
		}
		$this->db->where('hash_share', $password);
		$fs = $this->db->get(db_prefix() . 'fs_sharings')->result_array();
		if ($fs) {
			return $this->generate_hash();
		}

		return $password;
	}

	/**
	 * update general setting
	 *
	 * @param      array   $data   The data
	 *
	 * @return     boolean
	 */
	public function update_setting($data) {

		$affectedRows = 0;
		if (!isset($data['fs_allow_file_editing'])) {
			$data['fs_allow_file_editing'] = 0;
		}

		if (!isset($data['fs_permisstion_staff_view'])) {
			$data['fs_permisstion_staff_view'] = 0;
		}
		if (!isset($data['fs_permisstion_staff_upload_and_override'])) {
			$data['fs_permisstion_staff_upload_and_override'] = 0;
		}
		if (!isset($data['fs_permisstion_staff_delete'])) {
			$data['fs_permisstion_staff_delete'] = 0;
		}
		if (!isset($data['fs_permisstion_staff_upload'])) {
			$data['fs_permisstion_staff_upload'] = 0;
		}
		if (!isset($data['fs_permisstion_staff_download'])) {
			$data['fs_permisstion_staff_download'] = 0;
		}
		if (!isset($data['fs_permisstion_staff_share'])) {
			$data['fs_permisstion_staff_share'] = 0;
		}
		if (!isset($data['fs_permisstion_client_view'])) {
			$data['fs_permisstion_client_view'] = 0;
		}
		if (!isset($data['fs_permisstion_client_upload_and_override'])) {
			$data['fs_permisstion_client_upload_and_override'] = 0;
		}

		if (!isset($data['fs_permisstion_client_delete'])) {
			$data['fs_permisstion_client_delete'] = 0;
		}

		if (!isset($data['fs_permisstion_client_upload'])) {
			$data['fs_permisstion_client_upload'] = 0;
		}
		if (!isset($data['fs_permisstion_client_download'])) {
			$data['fs_permisstion_client_download'] = 0;
		}

		if (!isset($data['fs_global_notification'])) {
			$data['fs_global_notification'] = 0;
		}
		if (!isset($data['fs_global_email'])) {
			$data['fs_global_email'] = 0;
		}
		if (!isset($data['fs_client_visible'])) {
			$data['fs_client_visible'] = 0;
		}

		if (!isset($data['fs_permisstion_staff_share_to_client'])) {
			$data['fs_permisstion_staff_share_to_client'] = 0;
		}

		if (isset($data['fs_the_administrator_of_the_public_folder'])) {
			$data['fs_the_administrator_of_the_public_folder'] = implode(',', $data['fs_the_administrator_of_the_public_folder']);
		}else{
			$data['fs_the_administrator_of_the_public_folder'] = '';
		}

		foreach ($data as $key => $value) {
			$this->db->where('name', $key);
			$this->db->update(db_prefix() . 'options', [
				'value' => $value,
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get file share
	 * @param  string $hash
	 * @return object
	 */
	public function get_file_share($hash, $only_data = false) {
		$CI = &get_instance();

		if (is_client_logged_in()) {
			$this->db->where('customer_id', get_client_user_id());
			$groups = $this->db->get(db_prefix() . 'customer_groups')->row();
			$where_group = '';
			foreach ($groups as $key => $value) {
				if ($where_group == '') {
					$where_group = '(select GROUP_CONCAT(value SEPARATOR ",") from ' . db_prefix() . 'fs_sharing_relationship where value = ' . $value . ' and type = "customer_group" and share_id = ' . db_prefix() . 'fs_sharings.id) != ""';
				} else {
					$where_group .= ' OR (select GROUP_CONCAT(value SEPARATOR ",") from ' . db_prefix() . 'fs_sharing_relationship where value = ' . $value . ' and type = "customer_group" and share_id = ' . db_prefix() . 'fs_sharings.id) != ""';
				}
			}

			if ($where_group != '') {
				$where_group = '(' . $where_group . ')';
			} else {
				$where_group = '1=0';
			}

			$where_customer_group = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "customer_group")';

			$where_customer = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "customer")';

			$CI->db->where('type = "fs_client"');

			$CI->db->where('IF(' . $where_customer_group . ' != "", ' . $where_group . ', 1=1)');
			$CI->db->where('IF(' . $where_customer . ' != "", find_in_set(' . get_client_user_id() . ',' . $where_customer . '), 1=1)');

		} else {
			$this->db->where('staffid', get_staff_user_id());
			$staff = $this->db->get(db_prefix() . 'staff')->row();

			$where_role = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "role")';
			$where_staff = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "staff")';

			$CI->db->where('type = "fs_staff"');
			if ($staff->role != '') {
				$CI->db->where('IF(' . $where_role . ' != "", find_in_set(' . $staff->role . ',' . $where_role . '), 1=1)');
			} else {
				$CI->db->where('(' . $where_role . ' = "")');
			}
			$CI->db->where('IF(' . $where_staff . ' != "", find_in_set(' . $staff->staffid . ',' . $where_staff . '), 1=1)');
		}

		$file_share = $CI->db->get(db_prefix() . 'fs_sharings')->result_array();
		if ($only_data) {
			return $file_share;
		}
		$data_return = [];

		foreach ($file_share as $key => $value) {
			if ($value['expiration_date_apply'] == 1 && strtotime($value['expiration_date']) < strtotime(date('Y-m-d'))) {
				unset($file_share[$key]);
				continue;
			} elseif ($value['download_limits_apply'] == 1 && $value['download_limits'] <= $value['downloads']) {
				unset($file_share[$key]);
			} elseif ($value['has_been_deleted'] == 1) {
				unset($file_share[$key]);
				continue;
			}
		}

		foreach ($file_share as $key => $value) {
			foreach ($file_share as $k => $val) {
				if (!(strpos($val['url'], $value['url']) === false) && $value['id'] != $val['id']) {
					unset($file_share[$k]);
				}
			}
		}

		foreach ($file_share as $key => $value) {
			if ($value['expiration_date_apply'] == 1 && strtotime($value['expiration_date']) < strtotime(date('Y-m-d'))) {
				continue;
			} elseif ($value['download_limits_apply'] == 1 && $value['download_limits'] <= $value['downloads']) {
				continue;
			} elseif ($value['has_been_deleted'] == 1) {
				continue;
			}

			$data_return[] = [
				'isowner' => $value['isowner'] ? true : false,
				'mime' => $value['mime'],
				'ts' => time(),
				'read' => $value['read'],
				'write' => $value['write'],
				'locked' => $value['locked'],
				'size' => $value['size'],
				'hash' => $value['hash'],
				'name' => $value['name'],
				'url' => $value['url'],
				'phash' => $hash,
			];
		}
		return $data_return;
	}

	/**
	 * Function to get the client IP address
	 * @return string
	 */
	public function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}

	/**
	 * get file share by hash
	 * @param  string $hash_share
	 * @return object
	 */
	public function get_file_share_by_hash($hash_share) {
		$this->db->where('hash_share', $hash_share);
		return $this->db->get(db_prefix() . 'fs_sharings')->row();
	}

	/**
	 * download file
	 * @param  string $hash_share
	 * @return boolean
	 */
	public function download_file($hash_share) {
		$browser = $this->getBrowser();

		$this->db->insert(db_prefix() . 'fs_downloads', [
			'ip' => $this->get_client_ip(),
			'browser_name' => $browser['name'],
			'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'hash_share' => $hash_share,
			'time' => date('Y-m-d H:i:s'),
		]);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			return true;
		}

		return false;
	}

	/**
	 * get file downloads
	 * @param  string $id
	 * @param  array  $where
	 * @return array or object
	 */
	public function get_file_downloads($id = '', $where = []) {
		$this->db->where($where);
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'fs_downloads')->row();
		}

		$this->db->order_by('id', 'desc');
		return $this->db->get(db_prefix() . 'fs_downloads')->result_array();
	}

	/**
	 * get Browser info
	 * @return array
	 */
	public function getBrowser() {
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version = "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		} elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		} elseif (preg_match('/Firefox/i', $u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		} elseif (preg_match('/coc_coc_browser/i', $u_agent)) {
			$bname = 'Cốc Cốc';
			$ub = "coc_coc_browser";
		} elseif (preg_match('/Chrome/i', $u_agent)) {
			$bname = 'Google Chrome';
			$ub = "Chrome";
		} elseif (preg_match('/Safari/i', $u_agent)) {
			$bname = 'Apple Safari';
			$ub = "Safari";
		} elseif (preg_match('/Opera/i', $u_agent)) {
			$bname = 'Opera';
			$ub = "Opera";
		} elseif (preg_match('/Netscape/i', $u_agent)) {
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}

		// check if we have a number
		if ($version == null || $version == "") {$version = "?";}

		return array(
			'userAgent' => $u_agent,
			'name' => $bname,
			'version' => $version,
			'platform' => $platform,
			'pattern' => $pattern,
		);
	}

	/**
	 * check format date Y-m-d
	 *
	 * @param      String   $date   The date
	 *
	 * @return     boolean
	 */
	public function check_format_date($date) {
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * get staff config
	 * @param  integer $staffid
	 * @return object
	 */
	public function get_staff_config($staffid) {
		$this->db->where('staffid', $staffid);
		$staff = $this->db->get(db_prefix() . 'staff')->row();
		$where_role = '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM ' . db_prefix() . 'fs_setting_configuration_relationship WHERE configuration_id=' . db_prefix() . 'fs_setting_configuration.id and rel_type = "role")';
		if ($staff->role != '') {
			$this->db->where('IF(' . $where_role . ' != "", find_in_set(' . $staff->role . ',' . $where_role . '), 1=1)');
		} else {
			$this->db->where('(' . $where_role . ' = "")');
		}

		$where_staff = '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM ' . db_prefix() . 'fs_setting_configuration_relationship WHERE configuration_id=' . db_prefix() . 'fs_setting_configuration.id and rel_type = "staff")';
		$this->db->where('type = "fs_staff"');
		$this->db->where('IF(' . $where_staff . ' != "", find_in_set(' . $staffid . ',' . $where_staff . '), 1=1)');
		return $this->db->get(db_prefix() . 'fs_setting_configuration')->row();
	}

	/**
	 * delete sharing
	 * @param integer $id
	 * @return boolean
	 */

	public function delete_sharing($id) {
		$this->db->where('id', $id);
		$sharing = $this->db->get(db_prefix() . 'fs_sharings')->row();

		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'fs_sharings');
		if ($this->db->affected_rows() > 0) {

			$this->db->where('hash_share', $sharing->hash_share);
			$this->db->delete(db_prefix() . 'fs_downloads');

			return true;
		}
		return false;
	}

	/**
	 * get sharing by staff
	 * @return array
	 */
	public function get_sharing_by_staff() {
		if (!is_admin()) {
			$this->db->where('created_at', get_staff_user_id());
		}

		return $this->db->get(db_prefix() . 'fs_sharings')->result_array();

	}

	/**
	 * get client config
	 * @param  integer $clientid
	 * @return object
	 */
	public function get_client_config($clientid) {
		$this->db->where('customer_id', $clientid);
		$groups = $this->db->get(db_prefix() . 'customer_groups')->result_array();

		$where_group = '';
		foreach ($groups as $key => $value) {
			$where = '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM ' . db_prefix() . 'fs_setting_configuration_relationship WHERE configuration_id=' . db_prefix() . 'fs_setting_configuration.id and rel_type = "customer_group")';
			if ($where_group == '') {
				$where_group = 'IF(' . $where . ' != "", find_in_set(' . $value['groupid'] . ',' . $where . '), 1=1)';
			} else {
				$where_group .= ' or IF(' . $where . ' != "", find_in_set(' . $value['groupid'] . ',' . $where . '), 1=1)';
			}
		}

		$where_client = '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM ' . db_prefix() . 'fs_setting_configuration_relationship WHERE configuration_id=' . db_prefix() . 'fs_setting_configuration.id and rel_type = "customer")';

		$this->db->where('type = "fs_client"');
		if ($where_group != '') {
			$this->db->where('(' . $where_group . ')');
		} else {
			$this->db->where('(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM ' . db_prefix() . 'fs_setting_configuration_relationship WHERE configuration_id=' . db_prefix() . 'fs_setting_configuration.id and rel_type = "customer_group") = ""');
		}

		$this->db->where('IF(' . $where_client . ' != "", find_in_set(' . $clientid . ',' . $where_client . '), 1=1)');
		return $this->db->get(db_prefix() . 'fs_setting_configuration')->row();
	}

	/**
	 * get data sharing chart
	 *
	 * @param      string  $year   The year
	 *
	 * @return     array
	 */
	public function sharing_chart($filter = [], $where = '') {
		if (isset($filter['year'])) {
			$date_minus = date($filter['year'] . "-01-01");
		} else {
			$date_minus = date("Y-01-01");
		}
		$data = [];
		$month = [];

		if (!is_admin()) {
			$where = 'created_at = ' . get_staff_user_id();
		}

		if (isset($filter['staff_filter'])) {
			if ($where == '') {
				$where = 'find_in_set(' . db_prefix() . 'fs_sharings.created_at, "' . implode(',', $filter['staff_filter']) . '")';
			} else {
				$where .= ' AND find_in_set(' . db_prefix() . 'fs_sharings.created_at, "' . implode(',', $filter['staff_filter']) . '")';
			}
		}

		if (isset($filter['hash_share'])) {
			if ($where == '') {
				$where = 'find_in_set(' . db_prefix() . 'fs_sharings.id, "' . implode(',', $filter['hash_share']) . '")';
			} else {
				$where .= ' AND find_in_set(' . db_prefix() . 'fs_sharings.id, "' . implode(',', $filter['hash_share']) . '")';
			}
		}

		if (isset($filter['type'])) {
			$where_type = '';
			foreach ($filter['type'] as $key => $value) {
				if ($where_type == '') {
					$where_type = db_prefix() . 'fs_sharings.type = "' . $value . '"';
				} else {
					$where_type .= ' or ' . db_prefix() . 'fs_sharings.type = "' . $value . '"';
				}
			}
			if ($where_type != '') {
				if ($where == '') {
					$where = '(' . $where_type . ')';
				} else {
					$where .= ' AND (' . $where_type . ')';
				}
			}
		}

		for ($i = 0; $i < 12; $i++) {
			$count = 0;
			if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
				$this->db->where($where);
			}
			$this->db->where(array('year(inserted_at)' => date('Y', strtotime($date_minus)), 'month(inserted_at)' => date('m', strtotime($date_minus))));

			$count = $this->db->count_all_results(db_prefix() . 'fs_sharings');

			if ($count) {
				$data[] = (double) $count;
			} else {
				$data[] = 0;
			}
			$month[] = date("M Y", strtotime($date_minus));
			$date_minus = date("Y-m-d", strtotime($date_minus . " +1 month"));
		}

		return ['data' => $data, 'month' => $month];
	}

	/**
	 * get data download chart
	 *
	 * @param      string  $year   The year
	 *
	 * @return     array
	 */
	public function download_chart($filter = [], $where = '') {
		if (isset($filter['year'])) {
			$date_minus = date($filter['year'] . "-01-01");
		} else {
			$date_minus = date("Y-01-01");
		}
		$data = [];
		$month = [];

		if (!is_admin()) {
			$where = 'created_at = ' . get_staff_user_id();
		}

		if (isset($filter['staff_filter'])) {
			if ($where == '') {
				$where = 'find_in_set(' . db_prefix() . 'fs_sharings.created_at, "' . implode(',', $filter['staff_filter']) . '")';
			} else {
				$where .= ' AND find_in_set(' . db_prefix() . 'fs_sharings.created_at, "' . implode(',', $filter['staff_filter']) . '")';
			}
		}

		if (isset($filter['hash_share'])) {
			if ($where == '') {
				$where = 'find_in_set(' . db_prefix() . 'fs_sharings.id,"' . implode(',', $filter['hash_share']) . '")';
			} else {
				$where .= ' AND find_in_set(' . db_prefix() . 'fs_sharings.id,"' . implode(',', $filter['hash_share']) . '")';
			}
		}
		for ($i = 0; $i < 12; $i++) {
			$where_time = 'date_format(time, "%Y") = \'' . date('Y', strtotime($date_minus)) . '\' AND date_format(time, "%m") = \'' . date('m', strtotime($date_minus)) . '\'';
			$count = 0;

			$this->db->where($where_time);

			if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
				$this->db->where($where . ' and 1=1');
			}

			$this->db->join(db_prefix() . 'fs_sharings', db_prefix() . 'fs_sharings.hash_share = ' . db_prefix() . 'fs_downloads.hash_share', 'left');

			$count = $this->db->count_all_results(db_prefix() . 'fs_downloads');

			if ($count) {
				$data[] = (double) $count;
			} else {
				$data[] = 0;
			}
			$month[] = date("M Y", strtotime($date_minus));
			$date_minus = date("Y-m-d", strtotime($date_minus . " +1 month"));
		}

		return ['data' => $data, 'month' => $month];
	}

	/**
	 * get global extension
	 * @return array
	 */
	public function get_global_extension() {

		$file = FCPATH . 'modules/file_sharing/assets/plugins/elFinder-2.1.57/php/mime.types';
		$mime_map = [];
		if ($file && file_exists($file)) {
            $mimecf = file($file);

            foreach ($mimecf as $line_num => $line) {
                if (!preg_match('/^\s*#/', $line)) {
                    $mime = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
                    for ($i = 1, $size = count($mime); $i < $size; $i++) {
                        $mime_map[] = ['type' => $mime[$i], 'mime' => $mime[0]];
                    }
                }
            }
        }

		$fs_global_extension = explode(',', get_option('fs_global_extension'));
		$mime = [];
		foreach ($fs_global_extension as $value) {
			$value = str_replace('.', '', strtolower($value));
			if ($value == 'jpg') {
				$mime[] = 'image/jpeg';
				continue;
			} elseif ($value == 'apk') {
				$mime[] = 'application/java-archive';
			}
			foreach ($mime_map as $k => $val) {
				if ($value == $val['type']) {
					$mime[] = $val['mime'];
				}
			}
		}

		return $mime;
	}

	/**
	 * notifications
	 * @param  integer $id_staff
	 * @param  string $link
	 * @param  string $description
	 */
	public function notifications($id_staff, $link, $description) {
		$notifiedUsers = [];
		$id_userlogin = get_staff_user_id();

		$notified = add_notification([
			'fromuserid' => $id_userlogin,
			'description' => $description,
			'link' => $link,
			'touserid' => $id_staff,
			'additional_data' => serialize([
				$description,
			]),
		]);
		if ($notified) {
			array_push($notifiedUsers, $id_staff);
		}
		pusher_trigger_notification($notifiedUsers);
	}

	/**
	 * get staff by sharing
	 * @param  integer $sharing_id
	 * @return object
	 */
	public function get_staff_by_sharing($sharing_id) {
		$this->db->where('share_id', $sharing_id);
		$this->db->where('(type = "role" or type = "staff")');
		$sharing_relationship = $this->db->get(db_prefix() . 'fs_sharing_relationship')->result_array();

		$where = '';
		$staffs = [];
		$roles = [];
		foreach ($sharing_relationship as $key => $value) {
			if ($value['type'] == 'staff') {
				$staffs[] = $value['value'];
			} else {
				$roles[] = $value['value'];
			}
		}

		if (implode(',', $staffs) != '') {
			$where .= '1=1 AND find_in_set(staffid,"' . implode(',', $staffs) . '")';
		}

		if (implode(',', $roles) != '') {
			if ($where == '') {
				$where .= 'find_in_set(role,"' . implode(',', $roles) . '")';
			} else {
				$where .= ' AND find_in_set(role,"' . implode(',', $roles) . '")';
			}
		}

		if ($where != '') {
			$this->db->where($where);
		}

		return $this->db->get(db_prefix() . 'staff')->result_array();
	}

	/**
	 * get client by sharing
	 * @param  integer $sharing_id
	 * @return object
	 */
	public function get_client_by_sharing($sharing_id) {
		$this->db->where('share_id', $sharing_id);
		$this->db->where('(type = "customer_group" or type = "customer")');
		$sharing_relationship = $this->db->get(db_prefix() . 'fs_sharing_relationship')->result_array();

		$where = '';
		$customers = [];
		$customer_groups = [];
		foreach ($sharing_relationship as $key => $value) {
			if ($value['type'] == 'customer') {
				$customers[] = $value['value'];
			} else {
				$customer_groups[] = $value['value'];
			}
		}

		if (implode(',', $customers) != '') {
			$where .= '1=1 AND find_in_set(' . db_prefix() . 'clients.userid,"' . implode(',', $customers) . '")';
		}

		if (count($customer_groups) > 0) {
			$where_group = '';
			foreach ($customer_groups as $key => $value) {
				if ($where_group == '') {
					$where_group = db_prefix() . 'clients.userid IN (select customer_id from ' . db_prefix() . 'customer_groups where groupid = ' . $value . ')';
				} else {
					$where_group .= ' OR ' . db_prefix() . 'clients.userid IN (select customer_id from ' . db_prefix() . 'customer_groups where groupid = ' . $value . ')';
				}
			}
			if ($where == '') {
				$where = '(' . $where_group . ')';
			} else {
				$where .= ' AND (' . $where_group . ')';
			}
		}

		if ($where != '') {
			$this->db->where($where);
		}

		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'clients.userid = ' . db_prefix() . 'contacts.userid', 'left');
		return $this->db->get(db_prefix() . 'clients')->result_array();
	}

	/**
	 * send mail to public
	 * @param  array $data
	 * @return boolean
	 */
	public function send_mail_to_public($data) {
		$list_emails = explode(',', $data['email']);
		$this->db->where('id', $data['id']);
		$sharing = $this->db->get(db_prefix() . 'fs_sharings')->row();
		$sent = false;
		foreach ($list_emails as $value) {

			$data = (object) [];
			$data->sender = get_staff_full_name();
			$data->receiver = trim($value);
			$data->file_name = $sharing->name;
			$data->share_link = site_url('file_sharing/' . $sharing->hash_share);
			$data->mail_to = trim($value);

			$template = mail_template('fs_share_public', 'file_sharing', $data);
			if ($template->send()) {
				$sent = true;
			}
		}

		return $sent;
	}

	/**
	 * AES_256 Encrypt
	 * @param string $str
	 * @return string
	 */
	function AES_256_Encrypt($str) {
		$key = get_option('file_sharing_security');
		if ($key == '' || $key == null) {
			$key = '?3HTtb?HgTA@%7zm';
		}
		$method = 'aes-256-cbc';
		$key = substr(hash('sha256', $key, true), 0, 32);
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		return base64_encode(openssl_encrypt($str, $method, $key, OPENSSL_RAW_DATA, $iv));
	}
	/**
	 * AES_256 Decrypt
	 * @param string $str
	 * @return string
	 */
	function AES_256_Decrypt($str) {
		$key = get_option('file_sharing_security');
		if ($key == '' || $key == null) {
			$key = '?3HTtb?HgTA@%7zm';
		}
		$method = 'aes-256-cbc';
		$key = substr(hash('sha256', $key, true), 0, 32);
		$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
		return openssl_decrypt(base64_decode($str), $method, $key, OPENSSL_RAW_DATA, $iv);
	}

	public function check_staff_sharing_by_target($target) {
		$this->db->where('hash', $target);
		$sharings = $this->db->get(db_prefix() . 'fs_sharings')->result_array();

		foreach ($sharings as $key => $value) {
			$staffs = $this->get_staff_by_sharing($value['id']);

			foreach ($staffs as $key => $value) {
				if (get_staff_user_id() == $value['staffid']) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * get file share by target
	 * @param  string $hash
	 * @return object
	 */
	public function get_file_share_by_target($target, $only_data = false) {
		$CI = &get_instance();

		if (is_client_logged_in()) {
			$this->db->where('customer_id', get_client_user_id());
			$groups = $this->db->get(db_prefix() . 'customer_groups')->row();
			$where_group = '';
			foreach ($groups as $key => $value) {
				if ($where_group == '') {
					$where_group = 'count(select value from ' . db_prefix() . 'fs_sharing_relationship where value = ' . $value . ' and type = "customer_group" and share_id = ' . db_prefix() . 'fs_sharings.id) > 0';
				} else {
					$where_group .= ' OR count(select value from ' . db_prefix() . 'fs_sharing_relationship where value = ' . $value . ' and type = "customer_group" and share_id = ' . db_prefix() . 'fs_sharings.id) > 0';
				}
			}

			if ($where_group != '') {
				$where_group = '(' . $where_group . ')';
			} else {
				$where_group = '1=0';
			}

			$where_customer_group = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "customer_group")';

			$where_customer = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "customer")';

			$CI->db->where('type = "fs_client"');

			$CI->db->where('IF(' . $where_customer_group . ' != "", ' . $where_group . ', 1=1)');
			$CI->db->where('IF(' . $where_customer . ' != "", find_in_set(' . get_client_user_id() . ',' . $where_customer . '), 1=1)');

		} else {
			$this->db->where('staffid', get_staff_user_id());
			$staff = $this->db->get(db_prefix() . 'staff')->row();

			$where_role = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "role")';
			$where_staff = '(SELECT GROUP_CONCAT(value SEPARATOR ",") FROM ' . db_prefix() . 'fs_sharing_relationship WHERE share_id=' . db_prefix() . 'fs_sharings.id and ' . db_prefix() . 'fs_sharing_relationship.type = "staff")';

			$CI->db->where('type = "fs_staff"');
			if ($staff->role != '') {
				$CI->db->where('IF(' . $where_role . ' != "", find_in_set(' . $staff->role . ',' . $where_role . '), 1=1)');
			} else {
				$CI->db->where('(' . $where_role . ' = "")');
			}
			$CI->db->where('IF(' . $where_staff . ' != "", find_in_set(' . $staff->staffid . ',' . $where_staff . '), 1=1)');
		}
		$CI->db->where('hash', $target);
		$file_share = $CI->db->get(db_prefix() . 'fs_sharings')->result_array();

		if($only_data){
			return $file_share;
		}

		$data_return = [];
		foreach ($file_share as $key => $value) {
			$data_return = [
				'isowner' => $value['isowner'] ? true : false,
				'ts' => intval($value['ts']),
				'mime' => $value['mime'],
				'read' => intval($value['read']),
				'write' => intval($value['write']),
				'size' => intval($value['size']),
				'hash' => $value['hash'],
				'name' => $value['name'],
				'phash' => $value['phash'],
				'volumeid' => 'l1_',
			];
			break;
		}
		return $data_return;
	}

	/**
	 * get By Language
	 * @param  string $language
	 * @return string
	 */
	public function getByLanguage($language = 'english') {
		$locale = 'en';
		if ($language == '') {
			return $locale;
		}

		$locales = get_locales();

		if (isset($locales[$language])) {
			$locale = $locales[$language];
		} elseif (isset($locales[ucfirst($language)])) {
			$locale = $locales[ucfirst($language)];
		} else {
			foreach ($locales as $key => $val) {
				$key = strtolower($key);
				$language = strtolower($language);
				if (strpos($key, $language) !== false) {
					$locale = $val;
					// In case $language is bigger string then $key
				} elseif (strpos($language, $key) !== false) {
					$locale = $val;
				}
			}
		}

		return $this->getElFinderLangKey($locale);
	}

	/**
	 * get ElFinder Lang Key
	 * @param  string $locale
	 * @return string
	 */
	public function getElFinderLangKey($locale) {
		if ($locale == 'ja') {
			$locale = 'jp';
		} elseif ($locale == 'pt') {
			$locale = 'pt_BR';
		} elseif ($locale == 'ug') {
			$locale = 'ug_CN';
		} elseif ($locale == 'zh') {
			$locale = 'zh_TW';
		}

		return $locale;
	}
}
