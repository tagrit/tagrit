<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Debit_note_pdf extends App_pdf
{
    protected $debit_note;

    private $debit_note_number;

    public function __construct($debit_note)
    {
        $GLOBALS['debit_note_pdf'] = $debit_note;

        parent::__construct();


        $this->debit_note        = $debit_note;
        $this->debit_note_number = format_debit_note_number($this->debit_note->id);

        $this->SetTitle($this->debit_note_number);
    }

    public function prepare()
    {
        $this->with_number_to_word($this->debit_note->vendorid);

        $this->set_view_vars([
            'status'             => $this->debit_note->status,
            'debit_note_number' => $this->debit_note_number,
            'debit_note'        => $this->debit_note,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'debit_note';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_credit_note_pdf.php';
        $actualPath = APP_MODULES_PATH . '/purchase/views/debit_notes/debit_notepdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
