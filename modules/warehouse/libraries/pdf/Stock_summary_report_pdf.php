<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 * Stock summary report pdf
 */
class Stock_summary_report_pdf extends App_pdf {
	protected $stock_summary_report;
	public $font_size = '';
	public $size = 9;

	/**
	 * get font size
	 * @return
	 */
	public function get_font_size() {
		return $this->font_size;
	}

	/**
	 * set font size
	 * @param
	 */
	public function set_font_size($size) {
		$this->font_size = 8;

		return $this;
	}

	/**
	 * construct
	 * @param
	 */
	public function __construct($stock_summary_report) {

		$stock_summary_report = hooks()->apply_filters('request_html_pdf_data', $stock_summary_report);
		$GLOBALS['stock_summary_report_pdf'] = $stock_summary_report;

		parent::__construct();

		$this->stock_summary_report = $stock_summary_report;

		$this->SetTitle('stock_summary_report');

		# Don't remove these lines - important for the PDF layout
		$this->stock_summary_report = $this->fix_editor_html($this->stock_summary_report);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('stock_summary_report', $this->stock_summary_report);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'stock_summary_report';
	}

	/**
	 * file path
	 * @return
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/warehouse/views/report/stock_summary_reportpdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}