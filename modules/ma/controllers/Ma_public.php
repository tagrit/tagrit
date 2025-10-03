<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ma_public extends ClientsController
{
    public function index($a)
    {
        show_404();
    }

    /**
     * email tracking open
     * @param  [type] $hash 
     * @return [type]       
     */
    public function images($hash)
    {
        //THIS RETURNS THE IMAGE
        header('Content-Type: image/gif');
        readfile(module_dir_path('ma').'/tracking.gif');

        $hash = str_replace('.jpg', '',$hash);

        $this->db->where('hash', $hash);

        $this->db->where('open', 0);

        $this->db->update(db_prefix() . 'ma_email_logs', ['open' => 1, 'open_time' => date("Y-m-d H:i:s")]);

        die;
    }

    /**
     * download asset
     * @param  [type] $folder_indicator [description]
     * @param  string $attachmentid     [description]
     * @return [type]                   [description]
     */
    public function download_file($folder_indicator, $attachmentid = '')
    {   
        $this->load->helper('download');
        $this->load->model('ma_model');

        $path = '';
        if ($folder_indicator == 'ma_asset') {
            $this->db->where('rel_id', $attachmentid);
            $this->db->where('rel_type', 'ma_asset');
            $file = $this->db->get(db_prefix() . 'files')->row();
            $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $file->rel_id . '/' . $file->file_name;

            $this->ma_model->download_asset($attachmentid);
        }else {
            die('folder not specified');
        }

        force_download($path, null);
    }

    /**
     * email tracking click
     * @param  [type] $hash [description]
     * @return [type]       [description]
     */
    public function click($hash)
    {
        $url = $this->input->get('href');
        $confirm = $this->input->get('confirm');
        $data_update = ['click' => 1, 'click_time' => date("Y-m-d H:i:s")];
        if($confirm != ''){
            $data_update['confirm'] = $confirm;
        }

        $this->db->where('hash', $hash);
        $email_log = $this->db->get(db_prefix() . 'ma_email_logs')->row();

        if($email_log){
            $this->db->where('hash', $hash);
            $this->db->where('click', 0);
            $this->db->update(db_prefix() . 'ma_email_logs', $data_update);

            $this->db->insert(db_prefix() . 'ma_email_click_logs', [
                'client_id' => $email_log->client_id,
                'lead_id' => $email_log->lead_id,
                'campaign_id' => $email_log->campaign_id,
                'email_id' => $email_log->email_id,
                'url' => $url,
                'time' => date('Y-m-d H:i:s'),
            ]);
        }

        header("Location: ".$url, TRUE, 301);
        die;
    }

    /**
     * email tracking download asset
     * @param  string $hash
     * @return      
     */
    public function asset($hash)
    {   
        $this->db->where('hash', $hash);
        $asset_log = $this->db->get(db_prefix() . 'ma_asset_logs')->row();

        $path = '';
        if($asset_log){
            $this->load->helper('download');
            $this->load->model('ma_model');

            $this->db->where('hash', $hash);
            $this->db->where('download', 0);
            $this->db->update(db_prefix() . 'ma_asset_logs', ['download' => 1, 'download_time' => date("Y-m-d H:i:s")]);
            if ($this->db->affected_rows() > 0) {
                $this->db->where('id', $asset_log->asset_id);
                $asset = $this->db->get(db_prefix() . 'ma_assets')->row();

                $this->db->insert(db_prefix().'ma_point_action_logs', [
                    'campaign_id' => $asset_log->campaign_id, 
                    'lead_id' => $asset_log->lead_id, 
                    'client_id' => $asset_log->client_id, 
                    'point_action_id' => 0, 
                    'point' => $asset->change_points,
                    'dateadded' => date('Y-m-d H:i:s'), 
                ]);
            }

            $this->db->where('rel_id', $asset_log->asset_id);
            $this->db->where('rel_type', 'ma_asset');
            $file = $this->db->get(db_prefix() . 'files')->row();
            $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $file->rel_id . '/' . $file->file_name;

            $this->ma_model->download_asset($asset_log->asset_id, $asset_log->id);
        }else {
            die('folder not specified');
        }

        force_download($path, null);
    }

    /**
     * email unsubscribe
     * @param  [type] $hash [description]
     * @return [type]       [description]
     */
    public function unsubscribe($hash)
    {
        $data = [];

        $this->db->where('hash', $hash);
        $email_log = $this->db->get(db_prefix() . 'ma_email_logs')->row();

        if($email_log){
            if ($email_log->lead_id) {
                $this->db->where('id', $email_log->lead_id);
                $this->db->update(db_prefix() . 'leads', ['ma_unsubscribed' => 1]);
            }else{
                $this->db->where('userid', $email_log->client_id);
                $this->db->update(db_prefix() . 'clients', ['ma_unsubscribed' => 1]);
            }
        }

        $this->load->view('unsubscribe', $data);
    }
}