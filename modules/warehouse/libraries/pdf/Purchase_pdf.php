<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 * Purchase pdf
 */
class Purchase_pdf extends App_pdf {
	protected $purchase;
	/**
	 * construct
	 * @param
	 */
	public function __construct($purchase) {

		$purchase = hooks()->apply_filters('request_html_pdf_data', $purchase);
		$GLOBALS['purchase_pdf'] = $purchase;

		parent::__construct();

		$this->purchase = $purchase;

		$this->SetTitle('purchase');

		# Don't remove these lines - important for the PDF layout
		$this->purchase = $this->fix_editor_html($this->purchase);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('purchase', $this->purchase);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'purchase';
	}

	/**
	 * file path
	 * @return string
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/warehouse/views/manage_goods_receipt/purchasepdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}