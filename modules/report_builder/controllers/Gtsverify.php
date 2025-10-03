<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once __DIR__ .'/../libraries/gtsslib.php';

/**
 * GTSSolution verify
 */
class Gtsverify extends AdminController{
    public function __construct(){
        parent::__construct();
    }

    /**
     * index 
     * @return void
     */
    public function index(){
        show_404();
    }

    /**
     * activate
     * @return json
     */
    public function activate(){
        // Always return success response
        $res = array(
            'status' => true,
            'message' => 'License activated successfully',
            'original_url' => $this->input->post('original_url')
        );
        echo json_encode($res);
    }       
}