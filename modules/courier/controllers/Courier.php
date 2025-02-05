<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Courier extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CountryState_model');
        $this->load->model('Pickup_model');
        $this->load->model('Shipment_model');
        $this->load->model('CourierCompany_model');
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
    }

    public function dashboard()
    {
        $staff_id = get_staff_user_id();

        $data['pickup_counts'] = $this->Pickup_model->get_pickup_count_by_status(null, $staff_id);
        $data['shipment_counts'] = $this->Shipment_model->get_shipment_count_by_status(null, $staff_id);

        if (staff_can('view_all_pickups', 'courier-pickups')) {

            $data['pickup_counts'] = $this->Pickup_model->get_pickup_count_by_status();
        }

        if (staff_can('view_all_shipments', 'courier-shipments')) {

            $data['shipment_counts'] = $this->Shipment_model->get_shipment_count_by_status();
        }

        $data['courier_company_counts'] = $this->CourierCompany_model->get_company_count_by_type();
        $this->load->view('dashboard', $data);
    }

    public function states()
    {
        $country_id = $this->input->post('country_id');

        if ($country_id) {
            $this->db->where('country_id', $country_id);
            $query = $this->db->get(db_prefix() . '_country_states');
            $states = $query->result_array();
            echo json_encode(['states' => $states, 'country_code' => $this->getCountryCode($country_id)]);
        } else {
            echo json_encode([]);
        }
    }


    public function getCountryCode($country_id)
    {
        if (!is_null($country_id)) {
            $country = $this->db->get_where(db_prefix().'countries', ['country_id' => $country_id])->row(); // Using CodeIgniter's database query
            return $country ? $country->calling_code : null;
        }
        return null;
    }


}