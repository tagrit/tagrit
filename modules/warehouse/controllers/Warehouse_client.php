<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a warehouse client.
 */
class Warehouse_client extends ClientsController
{

	/**
	 * __construct description
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('warehouse_model');
	}

	public function shipments()
	{
		if (!is_client_logged_in()) {
			set_alert('warning', _l('access_denied'));
			redirect(site_url());
		}

		$client_id = get_client_user_id();

		$data['shipments'] = $this->warehouse_model->get_shipment_by_client($client_id);
		$data['title']    = _l('wh_shipments');
		$this->data($data);
		$this->view('client_shipments/manage');
		$this->layout();
	}

	public function shipment_detail($id)
	{

		$this->load->model('omni_sales/omni_sales_model');
		
		$shipment = $this->warehouse_model->get_shipment_by_delivery($id);
		if (!$shipment) {
			set_alert('warning', _l('shipment_not_found'));
			redirect(site_url());
		}

		$id = $shipment->id;

		$data = [];
		$data['title']          = $shipment->shipment_number;
		$data['shipment']          = $shipment;
		$data['order_id']          = $id;

		$data['goods_delivery'] = $this->warehouse_model->get_goods_delivery($id);

		if(is_numeric($data['goods_delivery']->customer_code)){
			$data['get_client'] = get_client($data['goods_delivery']->customer_code);
		}

		if(isset($data['goods_delivery']) && $data['goods_delivery'] && is_numeric($data['goods_delivery']->invoice_id)){
			$this->load->model('invoices_model');
			$invoices = $this->invoices_model->get($data['goods_delivery']->invoice_id);
			$data['invoices'] = $invoices;
		}

		//get activity log
		$data['arr_activity_logs'] = $this->warehouse_model->wh_client_get_shipment_activity_log($shipment->id);
		$wh_shipment_status = wh_shipment_status();
		$shipment_staus_order='';
		foreach ($wh_shipment_status as $shipment_status) {
			if($shipment_status['name'] ==  $data['shipment']->shipment_status){
				$shipment_staus_order = $shipment_status['order'];
			}
		}

		foreach ($wh_shipment_status as $shipment_status) {
			if((int)$shipment_status['order'] <= (int)$shipment_staus_order){
				$data[$shipment_status['name']] = ' completed';
			}else{
				$data[$shipment_status['name']] = '';
			}
		}
		$data['shipment_staus_order'] = $shipment_staus_order;

		//get delivery note
			$this->db->where('id', $id);
			$data['goods_deliveries'] = $this->db->get(db_prefix() . 'goods_delivery')->result_array();
			$data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($id);

		$this->data($data);
		$this->view('client_shipments/shipment_details');
		$this->layout();
	}

	public function shipment_detail_hash($hash = '')
	{

		$this->load->model('omni_sales/omni_sales_model');
		
		$shipment = $this->warehouse_model->get_shipment_by_hash($hash);
		if (!$shipment) {
			set_alert('warning', _l('shipment_not_found'));
			redirect(site_url());
		}

		$id = $shipment->goods_delivery_id;

		$data = [];
		$data['title']          = $shipment->shipment_number;
		$data['shipment']          = $shipment;
		$data['order_id']          = $id;

		$data['goods_delivery'] = $this->warehouse_model->get_goods_delivery($id);

		if(is_numeric($data['goods_delivery']->customer_code)){
			$data['get_client'] = get_client($data['goods_delivery']->customer_code);
		}

		if(isset($data['goods_delivery']) && $data['goods_delivery'] && is_numeric($data['goods_delivery']->invoice_id)){
			$this->load->model('invoices_model');
			$invoices = $this->invoices_model->get($data['goods_delivery']->invoice_id);
			$data['invoices'] = $invoices;
		}

		//get activity log
		$data['arr_activity_logs'] = $this->warehouse_model->wh_client_get_shipment_activity_log($shipment->id);
		$wh_shipment_status = wh_shipment_status();
		$shipment_staus_order='';
		foreach ($wh_shipment_status as $shipment_status) {
			if($shipment_status['name'] ==  $data['shipment']->shipment_status){
				$shipment_staus_order = $shipment_status['order'];
			}
		}

		foreach ($wh_shipment_status as $shipment_status) {
			if((int)$shipment_status['order'] <= (int)$shipment_staus_order){
				$data[$shipment_status['name']] = ' completed';
			}else{
				$data[$shipment_status['name']] = '';
			}
		}
		$data['shipment_staus_order'] = $shipment_staus_order;

		//get delivery note
			$this->db->where('id', $id);
			$data['goods_deliveries'] = $this->db->get(db_prefix() . 'goods_delivery')->result_array();
			$data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($id);

		$this->data($data);
		$this->view('client_shipments/shipment_details');
		$this->layout();
	}

	/**
	 * stock export pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function stock_export_pdf($id) {
		if (!$id) {
			redirect(admin_url('warehouse/manage_goods_delivery/manage_delivery'));
		}

		$stock_export = $this->warehouse_model->get_stock_export_pdf_html($id);

		try {
			$pdf = $this->warehouse_model->stock_export_pdf($stock_export);

		} catch (Exception $e) {
			echo new_html_entity_decode($e->getMessage());
			die;
		}

		$type = 'D';
		ob_end_clean();

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output('goods_delivery_'.strtotime(date('Y-m-d H:i:s')).'.pdf', $type);
	}

	/**
	 * client update shipment status
	 * @param  [type] $status      
	 * @param  [type] $shipment_id 
	 * @return [type]              
	 */
	public function client_update_shipment_status($status, $shipment_id)
	{	
		$this->db->where('id', $shipment_id);
		$this->db->update(db_prefix().'wh_omni_shipments', ['shipment_status' => $status]);

		//get shipment
		$this->db->where('id', $shipment_id);
		$shipment = $this->db->get(db_prefix() . 'wh_omni_shipments')->row();

		if($shipment && $shipment->goods_delivery_id){
			if(is_numeric($shipment->goods_delivery_id)){
				$arr_packing_list_id = [];
				$new_status = 'delivery_in_progress';
				//get packing list
				$packing_lists = $this->warehouse_model->get_packing_list_by_deivery_note($shipment->goods_delivery_id);
				if(count($packing_lists) > 0){
					foreach ($packing_lists as $value) {
					    $arr_packing_list_id[] = $value['id'];
					}
				}

				if($status == 'product_dispatched'){
					$new_status = 'delivery_in_progress';
				}elseif($status == 'product_delivered'){
					$new_status = 'delivered';
				}

				$this->db->where('id', $shipment->goods_delivery_id);
				$this->db->update(db_prefix().'goods_delivery', ['delivery_status' => $new_status]);

				if(count($arr_packing_list_id) > 0){
					$this->db->where('id IN ('.implode(',', $arr_packing_list_id).')');
					$this->db->update(db_prefix().'wh_packing_lists', ['delivery_status' => $new_status]);
				}
			}
		}

		//create activity log for shipment
		$shipment_log = _l($status);
		$this->warehouse_model->log_wh_activity($shipment_id, 'shipment', $shipment_log);

		set_alert('success', _l('updated_successfully'));
		redirect(site_url('warehouse/warehouse_client/shipment_detail/'.$shipment->goods_delivery_id));
	}

	/**
	 * client packing list pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function client_packing_list_pdf($id)
	{
		if (!$id) {
			redirect(admin_url('warehouse/packing_lists/manage_packing_list'));
		}
		$this->load->model('clients_model');
		$this->load->model('currencies_model');

		$packing_list_number = '';
		$packing_list = $this->warehouse_model->get_packing_list($id);
		$packing_list->client = $this->clients_model->get($packing_list->clientid);
		$packing_list->packing_list_detail = $this->warehouse_model->get_packing_list_detail($id);
		$packing_list->base_currency = $this->currencies_model->get_base_currency();
		$packing_list->tax_data = $this->warehouse_model->get_html_tax_packing_list($id);


		if($packing_list){
			$packing_list_number .= $packing_list->packing_list_number.' - '.$packing_list->packing_list_name;
		}
		try {
			$pdf = $this->warehouse_model->packing_list_pdf($packing_list);

		} catch (Exception $e) {
			echo new_html_entity_decode($e->getMessage());
			die;
		}

		$type = 'D';
		ob_end_clean();

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output(mb_strtoupper(slug_it($packing_list_number)).'.pdf', $type);
	}

}