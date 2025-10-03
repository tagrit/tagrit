<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class File_sharing_public extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('file_sharing_model');
    }

    /**
     * index 
     * @param  string $hash  
     * @return view       
     */
    public function index($hash){
        $data['file'] = $this->file_sharing_model->get_file_share_by_hash($hash);
        $this->db->where('hash_share', $hash);
        $total_rows = $this->db->count_all_results(db_prefix() . 'fs_downloads');
     
        if($data['file']){
            if($data['file']->download_limits <= $total_rows && $data['file']->download_limits != 0){
                $data['hidden'] = true;
            }

            if($data['file']->expiration_date < date('Y-m-d') && $data['file']->expiration_date != ''){
                if($data['file']->expiration_date_delete == 1){
                    if(file_exists(str_replace(site_url(),FCPATH, $data['file']->url))){
                        unlink(str_replace(site_url(),FCPATH, $data['file']->url));
                        $this->db->where('hash', $data['file']->hash);
                        $this->db->update(db_prefix().'fs_sharings', ['has_been_deleted' => 1]);
                    }
                }
                
                $data['hidden'] = true;
            }
        }
        
        $this->load->view('public/file_sharing_public', $data);
    }

    /**
     * download file
     * @return json
     */
    public function download(){
        $data = $this->input->post();
        $file = $this->file_sharing_model->get_file_share_by_hash($data['hash_share']);
        if($file){
            $success = $this->file_sharing_model->download_file($data['hash_share']);
            $response = ['success' => $success];
            if($success){
                $response['url'] = $file->url;

                $this->db->where('hash_share', $data['hash_share']);
                $this->db->update(db_prefix() . 'fs_sharings', ['downloads' => $file->downloads + 1]);

                $this->db->where('hash_share', $data['hash_share']);
                $total_rows = $this->db->count_all_results(db_prefix() . 'fs_downloads');
                if($file->download_limits <= $total_rows && $file->download_limits != 0){
                    if($file->download_limits_delete == 1){
                        if(file_exists(str_replace(site_url(),FCPATH, $file->url))){
                            unlink(str_replace(site_url(),FCPATH, $file->url));
                            $this->db->where('hash', $file->hash);
                            $this->db->update(db_prefix().'fs_sharings', ['has_been_deleted' => 1]);
                        }
                    }

                    $response['hidden'] = true;
                }
            }
        }
        echo json_encode($response);
    }

    /**
     * check download
     * @return json
     */
    public function check_download(){
                
        $data = $this->input->post();
        $file = $this->file_sharing_model->get_file_share_by_hash($data['hash_share']);
        if($file){
            if(($file->password != '' && $this->file_sharing_model->AES_256_Decrypt($file->password) == $data['password']) || $file->password == ''){
                $path = '';
                if($file->mime == 'directory'){
                    $path = str_replace(site_url(),FCPATH,$file->url);
                }
                $response = ['success' => true, 'url' => $file->url, 'mime' => $file->mime, 'path' => $path];
            }else{
                $response = ['success' => false, 'message' => _l('incorrect_password')];
            }
        }
        echo json_encode($response);
    }

    /**
     * download directory
     * @param  string $hash_share 
     */
    public function download_directory($hash_share){
        $file = $this->file_sharing_model->get_file_share_by_hash($hash_share);

        if($file){
            $path = str_replace(site_url(),FCPATH,$file->url);

            $this->load->library('zip');
            $this->zip->read_dir($path, false);
            $this->zip->download($file->name.'.zip');
            $this->zip->clear_data();
        }
    }

    /**
     * share downloaded
     * @return boolean 
     */
    public function share_downloaded(){
        $data = $this->input->post();

        if (is_client_logged_in()) {
            $client_id = get_client_user_id();
            $this->db->where('hash', $data['hash']);
            $this->db->where('type', 'fs_client');
            $sharings = $this->db->get(db_prefix().'fs_sharings')->result_array();

            foreach ($sharings as $share) {
                if($share['download_limits'] == $share['downloads'] && $share['download_limits'] != 0){
                    if($share['download_limits_delete'] == 1){
                        if(file_exists(str_replace(site_url(),FCPATH, $share['url']))){
                            unlink(str_replace(site_url(),FCPATH, $share['url']));
                            $this->db->where('hash', $share['hash']);
                            $this->db->update(db_prefix().'fs_sharings', ['has_been_deleted' => 1]);
                        }
                    }
                }else{
                    $clients = $this->file_sharing_model->get_client_by_sharing($share['id']);
                    foreach ($clients as $client) {
                        if($client_id == $client['userid']){
                            $this->file_sharing_model->download_file($share['hash_share']);

                            $this->db->where('id', $share['id']);
                            $this->db->update(db_prefix().'fs_sharings', ['downloads' => $share['downloads'] + 1]);

                            return true;
                        }
                    }
                }
            }
        }elseif (is_staff_logged_in()) {
            $staff_id = get_staff_user_id();

            $this->db->where('hash', $data['hash']);
            $this->db->where('type', 'fs_staff');
            $sharings = $this->db->get(db_prefix().'fs_sharings')->result_array();

            foreach ($sharings as $share) {
                if($share['downloads'] == $share['download_limits'] && $share['download_limits'] != 0){
                    if($share['download_limits_delete'] == 1){
                        if(file_exists(str_replace(site_url(),FCPATH, $share['url']))){
                            unlink(str_replace(site_url(),FCPATH, $share['url']));
                            $this->db->where('hash', $share['hash']);
                            $this->db->update(db_prefix().'fs_sharings', ['has_been_deleted' => 1]);
                        }
                    }
                }else{
                    $staffs = $this->file_sharing_model->get_staff_by_sharing($share['id']);
                    foreach ($staffs as $staff) {
                        if($staff_id == $staff['staffid']){
                            $this->file_sharing_model->download_file($share['hash_share']);

                            $this->db->where('id', $share['id']);
                            $this->db->update(db_prefix().'fs_sharings', ['downloads' => $share['downloads'] + 1]);

                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * get url by hash
     * @return json 
     */
    public function get_url_by_hash()
    {
        $hash = $this->input->get('hash');

        $h = substr($hash, 3);
        $url = base64_decode(strtr($h, '-_.', '+/='));
        $url = str_replace('(', '%28', $url);
        $url = str_replace(')', '%29', $url);

        echo json_encode(str_replace(' ','%20',site_url('modules/file_sharing/uploads/'.FILE_SHARING_FOLDER_NAME.'/'.$url)));
    }

    /**
     * download file
     * @param  string $hash_share 
     */
    public function download_file($hash_share){
        $this->load->helper('download');

        $file = $this->file_sharing_model->get_file_share_by_hash($hash_share);

        if($file){
            $path = str_replace(site_url(),FCPATH,$file->url);

            force_download($path, null);
        }
    }

    
}