<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Pur_estimate_pdf extends App_pdf
{
    protected $pur_estimate;
    private $id;

    public function __construct($pur_estimate,$id)
    {
        $pur_estimate                = hooks()->apply_filters('request_html_pdf_data', $pur_estimate);
        $GLOBALS['pur_estimate_pdf'] = $pur_estimate;

        parent::__construct();

        $this->pur_estimate = $pur_estimate;

        $this->SetTitle(format_pur_estimate_number($id));
        # Don't remove these lines - important for the PDF layout
        $this->pur_estimate = $this->fix_editor_html($this->pur_estimate);
    }

    public function prepare()
    {
        $this->set_view_vars('pur_estimate', $this->pur_estimate);

        return $this->build();
    }

    protected function type()
    {
        return 'pur_estimate';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/quotations/pur_estimatepdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}