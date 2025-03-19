<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/RestController.php';
require_once __DIR__.'/../vendor/autoload.php';
use flutexAdminApi\RestController;

class Profile extends RestController
{
    protected $staffInfo;

    public function __construct()
    {
        parent::__construct();
        register_language_files('flutex_admin_api');
        load_admin_language();
        
        $this->load->helper('flutex_admin_api');
        if (!isset(isAuthorized()['status'])) {
            $this->response(isAuthorized()['response'], isAuthorized()['response_code']);
        }

        $this->staffInfo = isAuthorized();
    }
     
    public function profile_get()
    {
        // Staff Information
        $staffID = $this->staffInfo['data']->staff_id;
        $staff = $this->db->where('staffid', $staffID)->get(db_prefix() . 'staff')->row();
        $staff_data = array(
            'id'=> $staff->staffid,
            'email' => $staff->email,
            'profile_image' => staff_profile_image_url($staff->staffid),
        );
        $this->response(['message' => _l('data_retrieved_successfully'),'data' => $staff_data], RestController::HTTP_OK);
    }
     
    public function logout_get()
    {
        $this->db->update(db_prefix() . 'staff', ['flutex_api_key' => NULL], ['staffid' => $this->staffInfo['data']->staff_id]);
        $this->response(['message' => _l('logged_out_successfully')], RestController::HTTP_OK);
    }
}