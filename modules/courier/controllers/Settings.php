<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
        $this->load->model('DimensionalFactor_model');
        $this->load->library('form_validation');

    }

    public function main()
    {

        $group = $this->input->get('group', true) ?? 'customization';
        $data['group'] = $group;

        switch ($group) {
            case 'customization':
                $data['title'] = _l('Dashboard');
                $data['dimensional_factor'] = $this->DimensionalFactor_model->get();
                $data['group_content'] = $this->load->view('settings/customization', $data, true);
                break;

            default:
                $data['group_content'] = $this->load->view('customization', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('settings/main', $data);
        }

    }

    public function customization()
    {
        $this->load->view('settings/customization');
    }


    public function dimensional_factor()
    {



        $this->form_validation->set_rules('default', 'Domestic/Courier', 'required');
        $this->form_validation->set_rules('air_consolidation', 'Air Consolidation', 'required');
        $this->form_validation->set_rules('air_freight', 'Air Freight', 'required');
        $this->form_validation->set_rules('sea_lcl', 'Sea Consolidation', 'required');


        if ($this->form_validation->run() === FALSE) {

            $data['dimensional_factor'] = $this->DimensionalFactor_model->get();
            $this->load->view('settings/main',$data);

        } else {

            if ($this->input->post('default')) {
                $this->DimensionalFactor_model->update_by_name('default', [
                    'value' => $this->input->post('default')
                ]);
            }

            if ($this->input->post('air_consolidation')) {
                $this->DimensionalFactor_model->update_by_name('air_consolidation', [
                    'value' => $this->input->post('air_consolidation')
                ]);
            }

            if ($this->input->post('air_freight')) {
                $this->DimensionalFactor_model->update_by_name('air_freight', [
                    'value' => $this->input->post('air_freight')
                ]);
            }

            if ($this->input->post('sea_consolidation')) {
                $this->DimensionalFactor_model->update_by_name('sea_consolidation', [
                    'value' => $this->input->post('sea_consolidation')
                ]);
            }

            set_alert('success', 'Dimensional Factors updated successfully.');
            redirect('admin/courier/settings/main');

        }
    }

}