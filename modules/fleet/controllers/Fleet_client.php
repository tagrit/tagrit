<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Commission client Controller
 */
class Fleet_client extends ClientsController
{
    public function index()
    {
        if(is_client_logged_in()){
            $this->load->model('fleet_model');
            $this->load->model('currencies_model');

            $data['title'] = _l('commission');
            $data['currency'] = $this->currencies_model->get_base_currency();
            $data['bookings'] = $this->fleet_model->get_booking();

            $this->data($data);

            $this->view('client/booking_manage');
            $this->layout();
        }else{
            redirect(site_url());
        }
    }

    /**
     * add or edit booking
     * 
     * @return     json
     */
    public function booking(){
        if ($this->input->post()) {
            $this->form_validation->set_rules('subject', _l('customer_ticket_subject'), 'required');
            $this->form_validation->set_rules('phone', _l('phone'), 'required');
            $this->form_validation->set_rules('delivery_date', _l('delivery_date'), 'required');
            $this->form_validation->set_rules('receipt_address', _l('receipt_address'), 'required');
            $this->form_validation->set_rules('delivery_address', _l('delivery_address'), 'required');
            $this->form_validation->set_rules('note', _l('note'), 'required');
            
            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                $this->load->model('fleet_model');
                $id = $this->fleet_model->add_booking([
                    'number'    => 'BOOKING'.time(),
                    'subject'    => $data['subject'],
                    'phone' => $data['phone'],
                    'delivery_date'   => $data['delivery_date'],
                    'receipt_address'   => $data['receipt_address'],
                    'delivery_address'   => $data['delivery_address'],
                    'note'   => $data['note'],
                    'contactid' => get_contact_user_id(),
                    'userid'    => get_client_user_id(),
                ]);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('booking')));
                    redirect(site_url('fleet/fleet_client/booking_detail/' . $id));
                }
            }
        }
        $data             = [];
        $data['title']    = _l('booking');
        $this->data($data);
        $this->view('client/booking');
        $this->layout();
    }

    public function booking_detail($id){
      $this->load->model('fleet_model');

      $data             = [];
      $data['booking'] = $this->fleet_model->get_booking($id);
      $data['title']    = _l('booking');
      $this->data($data);
      $this->view('client/booking_detail');
      $this->layout();
    }

    public function rating($id){
        $this->load->model('fleet_model');

        $data             = $this->input->post();
        $data['comments'] = $this->input->post('comments', false);

        $success = $this->fleet_model->update_booking([
                    'rating'   => $data['rating'],
                    'comments'   => $data['comments'],
                ], $id);

        if ($success) {
            set_alert('success', _l('rating_successfully'));
        }

        redirect(site_url('fleet/fleet_client/booking_detail/' . $id));
    }

    
}
