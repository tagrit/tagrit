<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Po_voucher_pdf extends App_pdf
{
    protected $po_voucher;

    public function __construct($po_voucher)
    {
        $po_voucher                = hooks()->apply_filters('request_html_pdf_data', $po_voucher);
        $GLOBALS['po_voucher_pdf'] = $po_voucher;

        parent::__construct();

        $this->po_voucher = $po_voucher;

        $this->SetTitle('po_voucher');
        # Don't remove these lines - important for the PDF layout
        $this->po_voucher = $this->fix_editor_html($this->po_voucher);
    }

    public function prepare()
    {
        $this->set_view_vars('po_voucher', $this->po_voucher);

        return $this->build();
    }

    protected function type()
    {
        return 'po_voucher';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_povoucherpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/purchase_order/po_voucherpdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}