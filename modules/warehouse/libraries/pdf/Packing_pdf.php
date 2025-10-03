<?php

defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'libraries/pdf/App_pdf.php';

class Packing_pdf extends App_pdf
{
	protected $packing_list;

	private $packing_list_number;

	public function __construct($packing_list, $tag = '')
	{
		$this->load_language($packing_list->clientid);
		$packing_list                = hooks()->apply_filters('packing_list_html_pdf_data', $packing_list);
		$GLOBALS['packing_list_pdf'] = $packing_list;

		parent::__construct();

		$this->packing_list        = $packing_list;
		$this->packing_list_number = $this->packing_list->packing_list_number.' - '.$this->packing_list->packing_list_name;

		$this->SetTitle($this->packing_list->packing_list_number.' - '.$this->packing_list->packing_list_name);
	}

	public function prepare()
	{

		$this->set_view_vars([
			'packing_list_number' => $this->packing_list_number,
			'packing_list'        => $this->packing_list,
		]);

		return $this->build();
	}

	protected function type()
	{
		return 'packing_list';
	}

	protected function file_path()
	{
		$actualPath = APP_MODULES_PATH . '/warehouse/views/packing_lists/packing_list_pdf.php';
		return $actualPath;
	}

}
