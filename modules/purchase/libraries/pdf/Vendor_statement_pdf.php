<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Vendor_statement_pdf extends App_pdf
{
    protected $statement;

    public function __construct($statement)
    {
        $this->load_language($statement['vendor_id']);

        $GLOBALS['statement_pdf'] = $statement;

        parent::__construct();

        $this->statement = $statement;

        $this->SetTitle(_l('account_summary'));
    }

    public function prepare()
    {
        $this->set_view_vars([
            'statement' => $this->statement,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'statement';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_statementpdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/vendors/vendor_statement_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
