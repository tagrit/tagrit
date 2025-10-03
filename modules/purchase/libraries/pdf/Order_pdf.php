<?php

defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'libraries/pdf/App_pdf.php';

class Order_pdf extends App_pdf
{
	protected $order_return;

	private $order_return_number;

	public function __construct($order_return, $tag = '')
	{
		$this->load_language($order_return->clientid);
		$order_return                = hooks()->apply_filters('order_return_html_pdf_data', $order_return);
		$GLOBALS['order_return_pdf'] = $order_return;

		parent::__construct();

		$this->order_return        = $order_return;
		$this->order_return_number = $this->order_return->order_return_number.' - '.$this->order_return->order_return_name;

		$this->SetTitle($this->order_return->order_return_number.' - '.$this->order_return->order_return_name);
	}

	public function prepare()
	{

		$this->set_view_vars([
			'order_return_number' => $this->order_return_number,
			'order_return'        => $this->order_return,
		]);

		return $this->build();
	}

	protected function type()
	{
		return 'order_return';
	}

	protected function file_path()
	{
		$actualPath = APP_MODULES_PATH . '/purchase/views/return_orders/order_return_pdf.php';
		return $actualPath;
	}

}
