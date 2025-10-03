<?php
defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('after_email_templates', 'add_file_sharing_email_templates');

if ( ! function_exists('file_sharing_set_realpath'))
{
	/**
	 * Set Realpath
	 *
	 * @param	string
	 * @param	bool	checks to see if the path exists
	 * @return	string
	 */
	function file_sharing_set_realpath($path, $check_existance = FALSE)
	{
		// Security check to make sure the path is NOT a URL. No remote file inclusion!
		if (preg_match('#^(http:\/\/|https:\/\/|www\.|ftp|php:\/\/)#i', $path) OR filter_var($path, FILTER_VALIDATE_IP) === $path)
		{
			show_error('The path you submitted must be a local server path, not a URL');
		}
		// Resolve the path
		if (realpath($path) !== FALSE)
		{
			$path = realpath($path);
		}
		elseif ($check_existance && ! is_dir($path) && ! is_file($path))
		{
			show_error('Not a valid path: '.$path);
		}
		// Add a trailing slash, if this is a directory
		return is_dir($path) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
	}

	/**
	 * Simple function to demonstrate how to control file access using "accessControl" callback.
	 * This method will disable accessing files/folders starting from '.' (dot)
	 *
	 * @param  string  $attr  attribute name (read|write|locked|hidden)
	 * @param  string  $path  file path relative to volume root directory started with directory separator
	 * @return bool|null
	 **/
	function rwaccess($attr, $path, $data, $volume, $isDir, $relpath) {
	  return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
	    ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
	    :  null;                                    // else elFinder decide it itself
	}

	function roaccess($attr, $path, $data, $volume, $isDir, $relpath) {
	  return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
	    ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
	    : ($attr == 'read' || $attr == 'locked');   // else read only
	}

}
/**
 * [fs_new_file_database description]
 * @param  [type] $path [description]
 * @param  string $hash [description]
 * @return [type]       [description]
 */
function fs_new_file_database($path, $hash = "", $id = '', $type = '', $dir = ''){
	$CI = & get_instance();
	$date = new DateTime();
	$fileid = $id == '' ? $date->getTimestamp() : $id;
	$CI->db->select('fileid');
	$array_fileid = $CI->db->get(db_prefix() . 'fs_file_overview')->result_array();
	$data = [];
	$data['fileid'] = $fileid;	
	$data['type'] = $type == "" ? "" : $type;	
	$data['path'] = $path;
	$data['hash'] = $hash != '' ? $hash : "";
	$data['dir'] = $dir;
	$data['hash_share'] = $CI->file_sharing_model->generate_hash();
	$data['created_at'] = get_staff_user_id();
	if($array_fileid){
		$_fileid = [];
		foreach ($array_fileid as $keyfileid => $valuefileid) {
			$_fileid[] = $valuefileid['fileid'];
		}
		if(in_array($fileid, $_fileid)){
			if($dir == 1){
				$data['fileid'] = $fileid.rand(1,100);	
				$CI->db->insert(db_prefix() . 'fs_file_overview', $data);
			}else{
				$CI->db->where('fileid', $fileid);
				//check exits 
				$count = count($CI->db->get(db_prefix() . 'fs_file_overview')->result_array());
				if($count == 0){
					$CI->db->insert(db_prefix() . 'fs_file_overview', $data);
				}else{
					$data_update = [];
					$data_update['path'] = $path;
					$data_update['hash'] = $hash != '' ? $hash : "";
					$CI->db->where('fileid', $fileid);
					$CI->db->update(db_prefix() . 'fs_file_overview', $data_update);
				}
			}
		}else{
			$CI->db->insert(db_prefix() . 'fs_file_overview', $data);
		}
	}else{
		if($data['hash'] != ''){
			$CI->db->where('fileid', $fileid);
				//check exits 
			$count = count($CI->db->get(db_prefix() . 'fs_file_overview')->result_array());
			if($count == 0){
				$CI->db->insert(db_prefix() . 'fs_file_overview', $data);
			}else{
				$data_update = [];
				$data_update['path'] = $path;
				$data_update['hash'] = $hash != '' ? $hash : "";
				$CI->db->where('fileid', $fileid);
				$CI->db->update(db_prefix() . 'fs_file_overview', $data);
			}
		}else{
			$CI->db->insert(db_prefix() . 'fs_file_overview', $data);
		}
	}
}
function fs_role_name($roleid){
	$CI = & get_instance();
	$CI->load->model('roles_model');
	$name_role = '';
	if(is_numeric($roleid)){
		$name_role = $CI->roles_model->get($roleid)->name;
		return $name_role;
	}
	return $name_role;
}


function fs_group_name($gr_id){
	$CI = & get_instance();
	$CI->load->model('clients_model');
	$name_group = '';
	if(is_numeric($gr_id)){
		$name_group = $CI->clients_model->get_groups($gr_id)->name;
		return $name_group;
	}
	return $name_group;
}

if (!function_exists('add_file_sharing_email_templates')) {
    /**
     * Init appointly email templates and assign languages
     * @return void
     */
    function add_file_sharing_email_templates() {
        $CI = &get_instance();

        $data['file_sharing_templates'] = $CI->emails_model->get(['type' => 'file_sharing', 'language' => 'english']);

        $CI->load->view('file_sharing/email_templates', $data);
    }
}

function check_permission(){
	return true;
}
