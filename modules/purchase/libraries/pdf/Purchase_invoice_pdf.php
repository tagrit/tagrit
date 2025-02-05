<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Purchase_invoice_pdf extends App_pdf
{
    protected $pur_invoice;

    public function __construct($pur_invoice)
    {
        $pur_invoice                = hooks()->apply_filters('request_html_pdf_data', $pur_invoice);


        parent::__construct();

        $this->pur_invoice = $pur_invoice;

        $this->SetTitle(_l('pur_invoice'));
        # Don't remove these lines - important for the PDF layout
        $this->pur_invoice = $this->fix_editor_html($this->pur_invoice);
    }

    public function prepare()
    {
        $this->set_view_vars('pur_invoice', $this->pur_invoice);

        return $this->build();
    }

    protected function type()
    {
        return 'pur_invoice';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/invoices/pur_invoicepdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}