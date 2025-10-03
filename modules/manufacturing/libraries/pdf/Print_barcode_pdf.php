<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 *  Print_barcode pdf
 */
class Print_barcode_pdf extends App_pdf {
	protected $print_barcode;

	/**
	 * construct
	 * @param object
	 */
	public function __construct($print_barcode) {

		$print_barcode = hooks()->apply_filters('request_html_pdf_data', $print_barcode);
		$GLOBALS['print_barcode_pdf'] = $print_barcode;

		parent::__construct();

		$this->print_barcode = $print_barcode;

		$this->SetTitle('print_barcode');

		# Don't remove these lines - important for the PDF layout
		$this->print_barcode = $this->fix_editor_html($this->print_barcode);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('print_barcode', $this->print_barcode);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'print_barcode';
	}

	/**
	 * file path
	 * @return
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/warehouse/views/print_barcode_pdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}