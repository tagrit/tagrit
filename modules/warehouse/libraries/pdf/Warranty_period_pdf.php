<?php

defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'libraries/pdf/App_pdf.php';

class Warranty_period_pdf extends App_pdf
{
	protected $warranty_period;

	public function __construct($warranty_period, $tag = '')
	{
		// $this->load_language($warranty_period->clientid);
		$warranty_period                = hooks()->apply_filters('warranty_period_html_pdf_data', $warranty_period);
		$GLOBALS['warranty_period_pdf'] = $warranty_period;

		parent::__construct();

		$this->warranty_period        = $warranty_period;

		$this->SetTitle(_l('wh_warranty_period_report'));
	}

	public function prepare()
	{

		$this->set_view_vars([
			'warranty_period'        => $this->warranty_period,
		]);

		return $this->build();
	}

	protected function type()
	{
		return 'warranty_period';
	}

	protected function file_path()
	{
		$actualPath = APP_MODULES_PATH . '/warehouse/views/report/warranty_period_report_pdf.php';
		return $actualPath;
	}

}
