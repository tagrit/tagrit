<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 *  Internal pdf
 */
class Internal_pdf extends App_pdf {
	protected $internal;

	/**
	 * construct
	 * @param object
	 */
	public function __construct($internal) {

		$internal = hooks()->apply_filters('request_html_pdf_data', $internal);
		$GLOBALS['internal_pdf'] = $internal;

		parent::__construct();

		$this->internal = $internal;

		$this->SetTitle('internal');

		# Don't remove these lines - important for the PDF layout
		$this->internal = $this->fix_editor_html($this->internal);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('internal', $this->internal);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'internal';
	}

	/**
	 * file path
	 * @return
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/warehouse/views/manage_internal_delivery/internalpdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}