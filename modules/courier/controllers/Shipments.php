<?php

use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

defined('BASEPATH') or exit('No direct script access allowed');

class Shipments extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('courier/courier'); // Load the helper specific to the courier module
        $this->load->model('Shipment_model');
        $this->load->model('ShipmentRecipient_model');
        $this->load->model('ShipmentSender_model');
        $this->load->model('Client_model');
        $this->load->model('ShipmentPackage_model');
        $this->load->model('CourierCompany_model');
        $this->load->model('ShipmentCompany_model');
        $this->load->model('ShipmentRecipientCompany_model');
        $this->load->model('ShipmentFCLPackage_model');
        $this->load->model('CommercialValueItems_model');
        $this->load->model('ShipmentStatus_model');
        $this->load->model('PickupContact_model');
        $this->load->model('Pickup_model');
        $this->load->model('ShipmentStop_model');
        $this->load->model('Delivery_model');
        $this->load->model('Manifest_model');
        $this->load->model('CountryState_model');
        $this->load->model('DimensionalFactor_model');
        $this->load->model('Driver_model');
        $this->load->model('DestinationOffice_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('Agent_model');

        spl_autoload_register([$this, 'barcodeAutoloader']);

    }


    public function main()
    {

        $group = $this->input->get('group', true) ?? 'dashboard';
        $data['group'] = $group;

        $staff_id = get_staff_user_id();
        $statuses = ['1', '2', '3', '4', '5', '6', '7'];
        $shipment_counts = [];

        foreach ($statuses as $status) {

            $shipment_counts[$status] = $this->Shipment_model->get_shipment_count_by_status($status, $staff_id);

            if (staff_can('view_all_shipments', 'courier-shipments')) {

                $shipment_counts[$status] = $this->Shipment_model->get_shipment_count_by_status($status);
            }

        }

        $data['shipment_counts'] = $shipment_counts;

        switch ($group) {
            case 'dashboard':
                $data['title'] = 'Dashboard';
                $data['group_content'] = $this->load->view('shipments/dashboard', $data, true);
                break;

            case 'create_shipment':
                $data['title'] = 'Create Shipment';
                $data['group_content'] = $this->load->view('shipments/create', $data, true);
                break;

            case 'list_shipments':
                $data['title'] = 'List Shipments';

                $data['shipment_details'] = $this->Shipment_model->get_shipments_details($staff_id);

                if (staff_can('view_all_shipments', 'courier-shipments')) {
                    $data['shipment_details'] = $this->Shipment_model->get_shipments_details();
                }

                $data['group_content'] = $this->load->view('shipments/index', $data, true);
                break;

            default:
                $data['group_content'] = $this->load->view('dashboard', [], true);
                break;
        }

        if ($this->router->fetch_method() == 'main' && !$this->input->is_ajax_request()) {
            $this->load->view('shipments/main', $data);
        }

    }

    public function dashboard()
    {
        $this->load->view('shipments/dashboard');
    }

    public function clear_filters()
    {
        // Clear the session data
        $this->session->unset_userdata('shipment_details');
        $this->session->unset_userdata('filterDateRange');
        $this->session->unset_userdata('status_id');
        $this->session->unset_userdata('staff_id');
        $this->session->unset_userdata('no_shipments');


        if ($this->input->is_ajax_request()) {
            echo json_encode([]);
            return;
        }

    }


    public function filter_shipments()
    {

        // Remove the shipment details from the session
        $this->session->unset_userdata('shipment_details');

        // Get filter inputs
        $type = $this->input->post('type');
        $this->session->set_userdata('type', $type);

        $mode = $this->input->post('mode'); // Changed to POST
        $mode_type = $this->input->post('mode_type'); // Changed to POST

        $data = [];

        if (empty($this->input->post('filterDateRange')) &&
            ($this->input->post('status_id') == '0') &&
            ($this->input->post('staff_id') == '0')) {

            $this->clear_filters();

            if ($type !== 'domestic') {
                redirect('admin/courier/shipments' . '?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type);
            } else {
                redirect('admin/courier/shipments' . '?type=' . $type);
            }
        }


        if (!empty($this->input->post('filterDateRange')) || $this->input->post('status_id') !== '0' || $this->input->post('staff_id') !== '0') {

            // Handle the date range filter
            $startDate = null;
            $endDate = null;

            if (!empty($this->input->post('filterDateRange'))) {
                $dateRange = $this->input->post('filterDateRange');
                $dates = explode(" to ", $dateRange);
                $startDate = $dates[0];
                $endDate = isset($dates[1]) ? $dates[1] : $dates[0];
            }

            // Handle status and other parameters
            $staff_id = get_staff_user_id();
            $status_id = $this->input->post('status_id');
            $filter_staff_id = $this->input->post('staff_id');
            $is_view_all = staff_can('view_all_shipments', 'courier-shipments');
            $staff_id_param = $is_view_all ? null : $staff_id;

            // Filter shipment details
            $data['shipment_details'] = $this->Shipment_model->filter_shipment_details(
                $staff_id_param,
                !empty($status_id) && $status_id != '0' ? $status_id : null,
                !empty($filter_staff_id) && $filter_staff_id != '0' ? $filter_staff_id : null,
                $startDate,
                $endDate,
                $type,
                $mode,
                $mode_type
            );

            $this->session->set_userdata('filterDateRange', $this->input->post('filterDateRange'));
            $this->session->set_userdata('status_id', $this->input->post('status_id'));
            $this->session->set_userdata('staff_id', $this->input->post('staff_id'));


            if (empty($data['shipment_details'])) {
                $this->session->set_userdata('no_shipments', true);
            } else {

                $this->session->set_userdata('no_shipments', false);
                $this->session->set_userdata('shipment_details', $data['shipment_details']);
            }


            if ($type !== 'domestic') {
                redirect('admin/courier/shipments' . '?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type);
            } else {
                redirect('admin/courier/shipments' . '?type=' . $type);
            }

        } else {

            $url = 'courier/shipments?type=';

            // Set session data
            $type = $this->session->userdata('type');

            $url = $url . $type;

            if ($this->session->userdata('mode') !== null) {
                $mode = $this->session->userdata('mode');
                $mode_type = $this->session->userdata('mode_type');
                $url = $url . '&mode=' . $mode . '&mode_type=' . $mode_type;
            }

            $this->session->set_userdata('shipment_details', $data['shipment_details']);

            set_alert('danger', 'Please select at least one filter');
            redirect('admin/' . $url);
        }
    }


    public function index()
    {

        // Set session data
        $type = $this->input->get('type');
        $this->session->set_userdata('type', $type);

        $mode = null;
        $mode_type = null;

        if ($this->input->get('mode') !== null) {
            $mode = $this->input->get('mode');
            $this->session->set_userdata('mode', $mode);
            $this->session->set_userdata('mode_type', $this->input->get('mode_type'));
        }

        if ($this->input->get('mode_type') !== 'none') {
            $mode_type = $this->input->get('mode_type');
        }

        $staff_id = get_staff_user_id();

        $data['shipment_details'] = $this->Shipment_model->get_shipments_details($staff_id, $type, $mode, $mode_type);

        if (staff_can('view_all_shipments', 'courier-shipments')) {
            $data['shipment_details'] = $this->Shipment_model->get_shipments_details($staff_id = null, $type, $mode, $mode_type);
        }

        if (!empty($this->session->userdata('shipment_details'))) {
            $data['shipment_details'] = $this->session->userdata('shipment_details');
        }

        // Check if no shipments were found
        $data['no_shipments'] = $this->session->userdata('no_shipments') ?? false;

        $data['agents'] = $this->Agent_model->get();

        $data['countries'] = $this->Shipment_model->get_countries();

        $this->load->view('shipments/index', $data);
    }

    public function create()
    {
        $data['drivers'] = $this->Driver_model->get();
        $data['dimensional_factor'] = $this->DimensionalFactor_model->get();
        $data['countries'] = $this->filterSenderCountries();
        $data['currencies'] = $this->Shipment_model->get_currencies();
        $data['type'] = $this->input->get('type', true) ?? 'international';
        $data['mode'] = $this->input->get('mode', true) ?? 'none';
        $data['mode_type'] = $this->input->get('mode_type', true) ?? 'none';
        $data['recipient_countries'] = $this->Shipment_model->get_countries();
        $data['user_country'] = $this->getStaffCountry();

        $this->load->view('shipments/create', $data);

    }

    private function validatePickup()
    {
        if ($this->input->post('hasPickup') !== null) {
            $this->form_validation->set_rules('pickup_contact_first_name', 'First Name', 'required');
            $this->form_validation->set_rules('pickup_contact_last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('pickup_contact_phone_number', 'Phone Number', 'required');
            $this->form_validation->set_rules('pickup_contact_email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('pickup_date', 'Pickup Date', 'required');
            $this->form_validation->set_rules('pickup_start_time', 'Pickup Start Time', 'required');
            $this->form_validation->set_rules('pickup_end_time', 'Pickup End Time', 'required');
            $this->form_validation->set_rules('pickup_country_id', 'Country', 'required');
            $this->form_validation->set_rules('pickup_state_id', 'State', 'required');
            $this->form_validation->set_rules('pickup_address', 'Address', 'required');
            $this->form_validation->set_rules('pickup_zipcode', 'Postal/Zip Code', 'required');
            $this->form_validation->set_rules('pickup_vehicle_type', 'Vehicle Type', 'required');
            $this->form_validation->set_rules('pickup_driver_id', 'Driver', 'required');
        }
    }


    private function validateCompany()
    {
        if ($this->input->post('sender_type') === 'company') {
            $this->form_validation->set_rules('company_name', 'Company Name', 'required');
            $this->form_validation->set_rules('contact_name', 'Contact Name', 'required');
            $this->form_validation->set_rules('contact_phone', 'Contact Person Phone Number', 'required');
            $this->form_validation->set_rules('contact_email', 'Contact Person Email', 'required');
            $this->form_validation->set_rules('contact_address', 'Address', 'required');
            $this->form_validation->set_rules('contact_zipcode', 'Postal/Zip Code', 'required');

            if ($this->input->post('type') === 'international') {
                $this->form_validation->set_rules('contact_state_id', 'Contact Person State ', 'required');
                $this->form_validation->set_rules('contact_country_id', 'Contact Person Country', 'required');
            }

        }

        if ($this->input->post('recipient_type') === 'company') {
            $this->form_validation->set_rules('recipient_company_name', 'Company Name', 'required');
            $this->form_validation->set_rules('recipient_contact_name', 'Contact Name', 'required');
            $this->form_validation->set_rules('recipient_contact_phone', 'Contact Person Phone Number', 'required');
            $this->form_validation->set_rules('recipient_contact_email', 'Contact Person Email', 'required');
            $this->form_validation->set_rules('recipient_contact_address', 'Address', 'required');
            $this->form_validation->set_rules('recipient_contact_zipcode', 'Postal/Zip Code', 'required');

            if ($this->input->post('type') === 'international') {
                $this->form_validation->set_rules('recipient_contact_state_id', 'Contact Person State ', 'required');
                $this->form_validation->set_rules('recipient_contact_country_id', 'Contact Person Country', 'required');
            }

        }

    }


    private function validateSender()
    {
        if ($this->input->post('sender_type') === 'individual') {

            $this->form_validation->set_rules('sender_first_name', 'Sender First Name', 'required');
            $this->form_validation->set_rules('sender_last_name', 'Sender Last Name', 'required');
            $this->form_validation->set_rules('sender_phone_number', 'Sender Phone Number', 'required');
            $this->form_validation->set_rules('sender_email', 'Sender Email', 'required|valid_email');
            $this->form_validation->set_rules('sender_address', 'Sender Address', 'required');
            $this->form_validation->set_rules('sender_zipcode', 'Sender Postal/Zip Code', 'required');

            if ($this->input->post('type') === 'international') {
                $this->form_validation->set_rules('sender_country_id', 'Sender Country', 'required');
                $this->form_validation->set_rules('sender_state_id', 'Sender State', 'required');
            }

        }

    }

    private function validateRecipient()
    {
        if ($this->input->post('recipient_type') === 'individual') {

            $this->form_validation->set_rules('recipient_first_name', 'Recipient First Name', 'required');
            $this->form_validation->set_rules('recipient_last_name', 'Recipient Last Name', 'required');
            $this->form_validation->set_rules('recipient_phone_number', 'Recipient Phone Number', 'required');
            $this->form_validation->set_rules('recipient_email', 'Recipient Email', 'required|valid_email');
            $this->form_validation->set_rules('recipient_address', 'Recipient Address', 'required');
            $this->form_validation->set_rules('recipient_zipcode', 'Recipient Postal/Zip Code', 'required');

            if ($this->input->post('type') === 'international') {
                $this->form_validation->set_rules('recipient_country_id', 'Recipient Country', 'required');
                $this->form_validation->set_rules('recipient_state_id', 'Recipient State', 'required');
            }

        }
    }

    private function validateFCLPackages()
    {
        $itemCount = count(set_value('amount', []));

        for ($i = 0; $i < $itemCount; $i++) {
            $this->form_validation->set_rules("amount[$i]", 'Quantity', 'required|numeric');
            $this->form_validation->set_rules("package_description[$i]", 'Package Description', 'required');
            $this->form_validation->set_rules("fcl_options[$i]", 'FCL Option', 'required');
        }
    }

    private function validateNonFCLPackages()
    {
        // Get the count of the amounts input; you can replace this with the actual source of data
        $amountCount = count(set_value('amount', [])) ?: 1;

        for ($i = 0; $i < $amountCount; $i++) {
            $this->form_validation->set_rules("amount[$i]", 'Amount', 'required|numeric');
            $this->form_validation->set_rules("package_description[$i]", 'Package Description', 'required');
            $this->form_validation->set_rules("weight[$i]", 'Weight', 'required|numeric');
            $this->form_validation->set_rules("length[$i]", 'Length', 'required|numeric');
            $this->form_validation->set_rules("width[$i]", 'Width', 'required|numeric');
            $this->form_validation->set_rules("height[$i]", 'Height', 'required|numeric');
            $this->form_validation->set_rules("weight_vol[$i]", 'Weight Volume', 'required|numeric');
            $this->form_validation->set_rules("chargeable_weight[$i]", 'Chargeable Weight', 'required|numeric');
        }
    }


    private function validateCommercialValueItems()
    {

        if ($this->input->post('hasCommercialInvoiceAttachment') !== null) {
            if (empty($_FILES['commercial_invoice_file']['name'])) {
                $this->form_validation->set_rules('commercial_invoice_file', 'Attachment', 'required');
            } else {
                if ($_FILES['commercial_invoice_file']['error'] !== UPLOAD_ERR_OK) {
                    $this->form_validation->set_rules('commercial_invoice_file', 'Attachment', 'required');
                }
            }
        } else {

            $itemCount = count(set_value('commodity_quantity', []));

            for ($i = 0; $i < $itemCount; $i++) {
                $this->form_validation->set_rules("commodity_quantity[$i]", 'Quantity', 'required|numeric');
                $this->form_validation->set_rules("commodity_description[$i]", 'Item Description', 'required');
                $this->form_validation->set_rules("declared_value[$i]", 'Declared Value', 'required|numeric');
            }

        }

    }

    private function validateShipment()
    {
        $this->form_validation->set_rules('shipping_mode', 'Shipping Mode', 'required');
        $this->form_validation->set_rules('courier_company_id', 'Courier Company', 'required|numeric');
    }

    // Controller method to handle recipient data
    public function store_recipient_data()
    {
        $recipient_id = $this->add_recipient();

        if ($recipient_id === false) {
            set_alert('danger', 'Failed to add recipient.');
            redirect('admin/courier/pickups/create');
        }

        return $recipient_id;
    }

// New private method to add recipient
    private function add_recipient()
    {
        // Determine address type
        $address_type = $this->input->post('recipient_address_type') === 'zip_code' ? 'zip_code' : 'postal_code';

        // Prepare recipient data
        $recipient_data = [
            'first_name' => $this->input->post('recipient_first_name'),
            'last_name' => $this->input->post('recipient_last_name'),
            'phone_number' => $this->input->post('recipient_country_code') . $this->input->post('recipient_phone_number'),
            'email' => $this->input->post('recipient_email'),
            'address' => $this->input->post('recipient_address'),
            'zipcode' => $this->input->post('recipient_zipcode'),
            'address_type' => $address_type,
            'state_id' => $this->input->post('recipient_state_id') ?: NULL,
            'country_id' => $this->input->post('recipient_country_id') ?: NULL
        ];

        // Store recipient data
        return $this->ShipmentRecipient_model->add($recipient_data);
    }

    // Controller method to handle sender data
    public function store_sender_data()
    {
        $sender_id = $this->add_sender();

        if ($sender_id === false) {
            set_alert('danger', 'Failed to add sender.');
            redirect('admin/courier/pickups/create');
        }

        return $sender_id;
    }

    // New private method to add sender
    private function add_sender()
    {
        // Determine address type
        $address_type = $this->input->post('sender_address_type') === 'zip_code' ? 'zip_code' : 'postal_code';

        // Prepare sender data
        $sender_data = [
            'first_name' => $this->input->post('sender_first_name'),
            'last_name' => $this->input->post('sender_last_name'),
            'phone_number' => $this->input->post('sender_country_code') . $this->input->post('sender_phone_number'),
            'email' => $this->input->post('sender_email'),
            'address' => $this->input->post('sender_address'),
            'zipcode' => $this->input->post('sender_zipcode'),
            'address_type' => $address_type,
            'state_id' => $this->input->post('sender_state_id') ?: NULL,
            'country_id' => $this->input->post('sender_country_id') ?: NULL
        ];

        // Store sender data
        return $this->ShipmentSender_model->add($sender_data);
    }

    // Controller method to handle client data
    public function store_client_data()
    {
        $client_id = $this->add_client();

        if ($client_id === false) {
            set_alert('danger', 'Failed to add client.');
            redirect('admin/courier/pickups/create');
        }

        return $client_id;
    }


    // Custom autoloader for the Barcode library
    private function barcodeAutoloader($class)
    {
        // Base directory for the Barcode library
        $base_dir = FCPATH . 'modules/courier/libraries/php-barcode-generator-main/src/';

        // Replace namespace prefix and backslashes, then append with .php
        $file = $base_dir . str_replace(['Picqer\\Barcode\\', '\\'], ['', '/'], $class) . '.php';

        // Include the file if it exists
        if (file_exists($file)) {
            require_once $file;
        }
    }


    public function generate_barcode($code)
    {
        // Instantiate the Barcode Generator
        $generator = new BarcodeGeneratorPNG();

        // Generate the Barcode PNG
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);

        // Define the directory path where you want to store the barcode
        $directory = FCPATH . 'modules/courier/assets/barcodes/';

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Define the file path
        $filePath = $directory . $code . '.png';

        // Save the barcode image to the file
        file_put_contents($filePath, $barcode);

        // Return the relative URL to the barcode image
        return base_url('modules/courier/assets/barcodes/' . $code . '.png');
    }

    private function add_client()
    {

        if (!empty($this->input->post('sender_first_name'))) {
            // Prepare client data
            $client_data = array(
                'company' => $this->input->post('sender_first_name') . ' ' . $this->input->post('sender_last_name'),
                'phonenumber' => $this->input->post('sender_phone_number'),
                'address' => $this->input->post('sender_address'),
                'zip' => $this->input->post('sender_zipcode'),
            );
        } else {

            $client_data = array(
                'company' => $this->input->post('company_name'),
                'phonenumber' => $this->input->post('contact_phone'),
                'address' => $this->input->post('contact_address'),
                'zip' => $this->input->post('contact_zipcode'),
            );

        }

        // Insert client data into the database
        return $this->Client_model->insert_client($client_data);
    }


    // Controller method to handle shipment data
    public function store_shipment_data($client_id, $sender_id, $recipient_id, $waybill_number, $recipient_company_id)
    {

        // Prepare shipment data
        $shipment_id = $this->add_shipment($client_id, $sender_id, $recipient_id, $waybill_number, $recipient_company_id);

        if ($shipment_id === false) {
            set_alert('danger', 'Failed to add shipment.');
            redirect('admin/courier/pickups/create');
        }

        return $shipment_id;
    }


    private function add_shipment($client_id, $sender_id, $recipient_id, $waybill_number, $recipient_company_id)
    {


        // Get shipping mode and convert to lowercase
        $shipping_mode_lower = strtolower($this->input->post('shipping_mode'));

        $packaging_charges = 0;

        if (!empty($this->input->post('packaging_charges'))) {
            $packaging_charges = $this->input->post('packaging_charges');
        }

        // Prepare shipment data
        $shipment_data = [
            'status_id' => 1,
            'export' => $this->input->post('export_import') === 'export' ? 1 : 0,
            'import' => $this->input->post('export_import') === 'import' ? 1 : 0,
            'shipping_mode' => $this->input->post('shipping_mode'),
            'shipping_category' => $this->input->post('type'),
            'tracking_id' => $waybill_number,
            'waybill_number' => $waybill_number,
            'courier_company_id' => $this->input->post('courier_company_id'),
            'sender_id' => $this->input->post('sender_type') === 'individual' ? $sender_id : NULL,
            'fcl_shipment' => str_contains($shipping_mode_lower, 'sea') && str_contains($shipping_mode_lower, 'fcl') ? 1 : 0,
            'recipient_id' => $this->input->post('recipient_type') === 'individual' ? $recipient_id : NULL,
            'company_id' => $this->input->post('sender_type') === 'company' ? $client_id : NULL,
            'recipient_company_id' => $this->input->post('recipient_type') === 'company' ? $recipient_company_id : NULL,
            'staff_id' => get_staff_user_id(),
            'packaging_charges' => $packaging_charges,
            'created_at' => date('Y-m-d H:i:s'),
            'company_type' => $this->input->post('company_type'),
        ];

        $shipment_id = $this->Shipment_model->add($shipment_data);

        // Record the status change in the shipment_status_histories table
        $this->db->insert(db_prefix() . '_shipment_status_history', [
            'shipment_id' => $shipment_id,
            'status_id' => 1,
            'changed_at' => date('Y-m-d H:i:s'),
        ]);

        // Insert shipment data into the database
        return $shipment_id;
    }

    // Controller method to handle pickup data
    public function store_pickup_data($shipment_id)
    {
        if ($this->input->post('hasPickup') !== null) {
            // Store contact person data
            $contact_person_data = [
                'first_name' => $this->input->post('pickup_contact_first_name'),
                'last_name' => $this->input->post('pickup_contact_last_name'),
                'phone_number' => $this->input->post('pickup_country_code') . $this->input->post('pickup_contact_phone_number'),
                'email' => $this->input->post('pickup_contact_email')
            ];

            $contact_id = $this->PickupContact_model->add($contact_person_data);

            if ($contact_id === false) {
                set_alert('danger', 'Failed to add pickup contact person.');
                redirect('admin/courier/pickups/create');
            }

            // Store pickup data
            $pickup_data = [
                'pickup_date' => strtoupper($this->input->post('pickup_date')),
                'pickup_start_time' => strtoupper($this->input->post('pickup_start_time')),
                'pickup_end_time' => strtoupper($this->input->post('pickup_end_time')), 'country_id' => $this->input->post('pickup_country_id'),
                'state_id' => $this->input->post('pickup_state_id'),
                'address' => $this->input->post('pickup_address'),
                'pickup_zip' => $this->input->post('pickup_zipcode'),
                'address_type' => $this->input->post('pickup_address_type'),
                'vehicle_type' => strtoupper($this->input->post('pickup_vehicle_type')),
                'contact_person_id' => $contact_id,
                'shipment_id' => $shipment_id,
                'staff_id' => get_staff_user_id(),
                'driver_id' => $this->input->post('pickup_driver_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $pickup_id = $this->Pickup_model->add($pickup_data);

            if ($pickup_id === false) {
                set_alert('danger', 'Failed to add pickup.');
                redirect('admin/courier/pickups/create');
            }
        }
    }

    private function process_invoice_and_packages($shipment_id, $waybill_number, $shipping_mode, $client_id, $mode_type, $data, $commercial_value_data = null)
    {

        $data['sender_address_type'] = str_replace('_', ' ', $this->input->post('sender_address_type'));
        $data['recipient_address_type'] = str_replace('_', ' ', $this->input->post('sender_address_type'));

        if ($this->input->post('sender_type') === 'company') {
            $data['sender_address'] = $this->input->post('contact_address');
            $data['sender_address_type'] = str_replace('_', ' ', $this->input->post('contact_address_type'));
            $data['sender_zipcode'] = $this->input->post('contact_zipcode');
            $data['sender_country_id'] = !is_null($this->input->post('contact_country_id')) ? $this->input->post('contact_country_id') : 0;
        }

        // Create invoice data
        $invoice_data = [
            'clientid' => $client_id,
            'number' => get_option('next_invoice_number'),
            'date' => date('Y-m-d'),
            'duedate' => date('Y-m-d', strtotime('+30 days')),
            'currency' => $this->input->post('currency_id'),
            'subtotal' => 10,
            'total' => 10,
            'status' => 1, // Unpaid
            'billing_street' => $data['sender_address'],
            'billing_zip' => $data['sender_zipcode'],
            'billing_country' => !is_null($data['sender_country_id']) ? $data['sender_country_id'] : 0,
        ];

        $sender_country_id = !is_null($data['sender_country_id']) ? $data['sender_country_id'] : 0;
        $receiver_country_id = !is_null($data['recipient_country_id']) ? $data['recipient_country_id'] : 0;

        $sender_country = $this->CountryState_model->get_country_name_by_id($sender_country_id) ?? '';
        $receiver_country = $this->CountryState_model->get_country_name_by_id($receiver_country_id) ?? '';


        // Create the invoice
        $invoice_id = $this->invoices_model->add($invoice_data);
        $total = 0;

        // Iterate over each package and insert into the database
        foreach ($data['quantities'] as $i => $quantity) {

            $total += $quantity * $data['chargeable_weights'][$i];

            // Add package data based on mode type
            if ($mode_type === 'fcl') {
                $package_data = [
                    'shipment_id' => $shipment_id,
                    'quantity' => $quantity,
                    'description' => $data['descriptions'][$i],
                    'fcl_option' => $data['fcl_options'][$i],
                ];

                $this->ShipmentFCLPackage_model->add($package_data);
            } else {
                $package_data = [
                    'shipment_id' => $shipment_id,
                    'quantity' => $quantity,
                    'description' => $data['descriptions'][$i],
                    'weight' => $data['weights'][$i],
                    'length' => $data['lengths'][$i],
                    'width' => $data['widths'][$i],
                    'height' => $data['heights'][$i],
                    'weight_volume' => $data['weight_volumes'][$i],
                    'chargeable_weight' => $data['chargeable_weights'][$i],
                ];

                $this->ShipmentPackage_model->add($package_data);
            }
        }

        // Add invoice item
        $invoice_item = [
            'description' => 'WAYBILL - ' . strtoupper($waybill_number) . "\n\n",
            'long_description' => '<strong>SHIPPING MODE - </strong>' . strtoupper($shipping_mode) . "\n\n" . '<strong>FROM :</strong> ' . strtoupper($sender_country) . " " . strtoupper($data['sender_address']) . ', ' . ucfirst($data['sender_address_type']) . ' ' . strtoupper($data['sender_zipcode']) . "\n\n" . '<strong>TO : </strong> ' . strtoupper($receiver_country) . " " . strtoupper($data['recipient_address']) . ', ' . ucfirst($data['recipient_address_type']) . ' ' . strtoupper($data['recipient_zipcode']),
            'qty' => $total,
            'rate' => 7,
            'item_order' => 1, // Order of the item in the invoice
            'rel_id' => $invoice_id,
            'rel_type' => 'invoice',
            'unit' => 'kgs', // Unit of measure
        ];
        $this->Shipment_model->add_invoice_item($invoice_item);

        $packaging_charges = 0;

        if (!empty($this->input->post('packaging_charges'))) {
            $packaging_charges = $this->input->post('packaging_charges');
        }

        // Add invoice item
        $invoice_item = [
            'description' => 'PACKAGING',
            'long_description' => 'Packaging Charges',
            'qty' => 1,
            'rate' => $packaging_charges,
            'item_order' => 1,
            'rel_id' => $invoice_id,
            'rel_type' => 'invoice',
            'unit' => '',
        ];
        $this->Shipment_model->add_invoice_item($invoice_item);


        if ($commercial_value_data['commodity_quantity'] !== null) {
            //store commercial values for the items...
            foreach ($commercial_value_data['commodity_quantity'] as $i => $quantity) {
                $item_data = [
                    'shipment_id' => $shipment_id,
                    'quantity' => $quantity,
                    'description' => $commercial_value_data['commodity_description'][$i],
                    'declared_value' => $commercial_value_data['declared_value'][$i]
                ];
                $this->CommercialValueItems_model->add($item_data);
            }
        } else {
            $commercial_invoice_url = $this->upload_commercial_invoice_attachment();
            $this->Shipment_model->update($shipment_id, [
                'commercial_invoice_url' => $commercial_invoice_url
            ]);
        }


        // Update the invoice with subtotal and total
        $this->Shipment_model->update_invoice($invoice_id, [
            'subtotal' => $total,
            'total' => $total,
        ]);

        // Update the shipment with the invoice ID
        $this->Shipment_model->update($shipment_id, [
            'invoice_id' => $invoice_id
        ]);

        return $invoice_id;
    }


    // Function to store company details if necessary
    private function store_company_if_needed($sender_type)
    {
        // Initialize company ID
        $company_id = '';

        // Store company details if the sender is a company
        if ($sender_type === 'company') {
            $company_data = [
                'company_name' => $this->input->post('company_name'),
                'contact_person_name' => $this->input->post('contact_name'),
                'contact_person_phone_number' => $this->input->post('contact_country_code') . $this->input->post('contact_phone'),
                'contact_person_email' => $this->input->post('contact_email'),
                'contact_state_id' => $this->input->post('contact_state_id'),
                'contact_country_id' => $this->input->post('contact_country_id'),
                'contact_address_type' => $this->input->post('contact_address_type'),
                'contact_address' => $this->input->post('contact_address'),
                'contact_zipcode' => $this->input->post('contact_zipcode'),
            ];

            $company_id = $this->ShipmentCompany_model->add($company_data);

        }


        return $company_id;
    }


    // Function to store recipient company details if necessary
    private function store_recipient_company_if_needed($recipient_type)
    {
        // Initialize company ID
        $recipient_company_id = '';

        // Store company details if the sender is a company
        if ($recipient_type === 'company') {
            $company_data = [
                'recipient_company_name' => $this->input->post('recipient_company_name'),
                'recipient_contact_person_name' => $this->input->post('recipient_contact_name'),
                'recipient_contact_person_phone_number' => $this->input->post('recipient_contact_country_code') . $this->input->post('recipient_contact_phone'),
                'recipient_contact_person_email' => $this->input->post('recipient_contact_email'),
                'recipient_contact_state_id' => $this->input->post('recipient_contact_state_id'),
                'recipient_contact_country_id' => $this->input->post('recipient_contact_country_id'),
                'recipient_contact_address_type' => $this->input->post('recipient_contact_address_type'),
                'recipient_contact_address' => $this->input->post('recipient_contact_address'),
                'recipient_contact_zipcode' => $this->input->post('recipient_contact_zipcode'),
            ];

            $recipient_company_id = $this->ShipmentRecipientCompany_model->add($company_data);

        }

        return $recipient_company_id;
    }


    public function upload_commercial_invoice_attachment()
    {
        $upload_path = FCPATH . 'modules/courier/assets/commercial_invoices/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $file_name = time() . '_' . $_FILES['commercial_invoice_file']['name'];
        $file_path = $upload_path . $file_name;

        if (move_uploaded_file($_FILES['commercial_invoice_file']['tmp_name'], $file_path)) {
            return 'modules/courier/assets/commercial_invoices/' . $file_name;
        } else {
            set_alert('danger', 'File upload failed.');
        }

    }


    public function filterSenderCountries()
    {
        $countries = [];

        if (is_admin()) {
            $countries = $this->Shipment_model->get_countries();
        } else {
            $staff_id = get_staff_user_id();
            $country_id = $this->db->select('country_id')
                ->from(db_prefix() . '_courier_audit_logs')
                ->where('staff_id', $staff_id)
                ->get()
                ->row();
            if ($country_id) {
                $countries = $this->Shipment_model->get_countries($country_id->country_id);
            } else {
                $countries = $this->Shipment_model->get_countries();
            }
        }

        return $countries;
    }

    public function getStaffCountry()
    {

        $staff_id = get_staff_user_id();
        $this->db->select('email');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('staffid', $staff_id);
        $query = $this->db->get();
        $email = $query->row()->email;


        $this->db->select('value');
        $this->db->from(db_prefix() . 'staff s');
        $this->db->join(db_prefix() . 'customfieldsvalues c', 's.staffid = c.relid');
        $this->db->where('s.email', $email);

        $query = $this->db->get();

        // Check if we got a result
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $short_name = $row->value;

            $this->db->from(db_prefix() . 'countries');
            $this->db->where('short_name', $short_name);
            $country_query = $this->db->get();

            if ($country_query->num_rows() > 0) {
                return $country_query->row();
            } else {
                return null;
            }
        } else {
            return null;
        }


    }

    private function prepare_shipment_data($company_id, $sender_id, $recipient_id, $waybill_number, $recipient_company_id)
    {

        if (!empty($company_id) && !empty($recipient_company_id)) {
            return $this->store_shipment_data(
                $company_id,
                NULL,
                $recipient_id,
                $waybill_number,
                $recipient_company_id
            );
        }


        if (!empty($company_id) && empty($recipient_company_id)) {
            return $this->store_shipment_data(
                $company_id,
                NULL,
                $recipient_id,
                $waybill_number,
                NULL
            );
        }

        if (empty($company_id) && !empty($recipient_company_id)) {
            return $this->store_shipment_data(
                NULL,
                $sender_id,
                $recipient_id,
                $waybill_number,
                $recipient_company_id
            );
        }

        if (empty($company_id) && empty($recipient_company_id)) {
            return $this->store_shipment_data(
                NULL,
                $sender_id,
                $recipient_id,
                $waybill_number,
                NULL
            );
        }

    }


    /**
     * @throws Exception
     */
    public function store()
    {

        $this->validatePickup();
        $this->validateCompany();
        $this->validateSender();
        $this->validateRecipient();

        if ($this->input->post('mode_type') === 'fcl') {
            $this->validateFCLPackages();
        } else {
            $this->validateNonFCLPackages();
        }


        $this->validateCommercialValueItems();
        $this->validateShipment();
        $this->form_validation->set_rules('packaging_charges', 'Packaging Charges', 'required|numeric');


        if ($this->form_validation->run() === FALSE) {

            $data = [
                'currencies' => $this->Shipment_model->get_currencies(),
                'countries' => $this->filterSenderCountries(),
                'type' => $this->input->post('type'),
                'mode' => $this->input->post('mode'),
                'mode_type' => $this->input->post('mode_type'),
                'show_pickup_section' => $this->input->post('hasPickup') !== null,
                'show_company_section' => $this->input->post('sender_type') === 'company',
                'show_recipient_company_section' => $this->input->post('recipient_type') === 'company',
                'dimensional_factor' => $this->DimensionalFactor_model->get(),
                'show_commercial_value_attachment_section' => $this->input->post('hasCommercialInvoiceAttachment') !== null,

                // Pass selected country values
                'recipient_country_id' => $this->input->post('recipient_country_id'),
                'recipient_contact_country_id' => $this->input->post('recipient_contact_country_id'),
                'sender_country_id' => $this->input->post('sender_country_id'),
                'contact_country_id' => $this->input->post('contact_country_id'),
                'pickup_country_id' => $this->input->post('pickup_country_id'),

                // Pass selected state values
                'recipient_state_id' => $this->input->post('recipient_state_id'),
                'sender_state_id' => $this->input->post('sender_state_id'),
                'contact_state_id' => $this->input->post('contact_state_id'),
                'recipient_contact_state_id' => $this->input->post('recipient_contact_state_id'),
                'pickup_state_id' => $this->input->post('pickup_country_id'),
                'drivers' => $this->Driver_model->get(),
                'recipient_countries' => $this->Shipment_model->get_countries(),
            ];

            $this->load->view('shipments/create', $data);

        } else {

            // Attempt to insert data
            try {

                $company_id = $this->store_company_if_needed($this->input->post('sender_type'));
                $recipient_company_id = $this->store_recipient_company_if_needed($this->input->post('recipient_type'));
                $recipient_id = $this->store_recipient_data();
                $sender_id = $this->store_sender_data();
                $client_id = $this->store_client_data();
                $waybill_number = $this->generateWaybillNumber();
                $shipment_id = $this->prepare_shipment_data($company_id, $sender_id, $recipient_id, $waybill_number, $recipient_company_id);
                $this->store_pickup_data($shipment_id);

                // Collect POST data
                $data = [
                    'sender_address' => $this->input->post('sender_address'),
                    'sender_zipcode' => $this->input->post('sender_zipcode'),
                    'sender_country_id' => $this->input->post('sender_country_id'),
                    'recipient_address' => $this->input->post('recipient_address'),
                    'recipient_zipcode' => $this->input->post('recipient_zipcode'),
                    'recipient_country_id' => $this->input->post('recipient_country_id'),
                    'quantities' => $this->input->post('amount'),
                    'descriptions' => $this->input->post('package_description'),
                ];

                $commercial_value_data = null;

                //commercial Value Data
                if ($this->input->post('hasCommercialInvoiceAttachment') === null) {
                    $commercial_value_data['commodity_quantity'] = $this->input->post('commodity_quantity');
                    $commercial_value_data['commodity_description'] = $this->input->post('commodity_description');
                    $commercial_value_data['declared_value'] = $this->input->post('declared_value');
                }

                //Package Data
                if ($this->input->post('mode_type') === 'fcl') {
                    $data['fcl_options'] = $this->input->post('fcl_options');
                    $invoice_id = $this->process_invoice_and_packages($shipment_id, $waybill_number, $this->input->post('shipping_mode'), $client_id, 'fcl', $data, $commercial_value_data);
                } else {
                    $data['weights'] = $this->input->post('weight');
                    $data['lengths'] = $this->input->post('length');
                    $data['widths'] = $this->input->post('width');
                    $data['heights'] = $this->input->post('height');
                    $data['weight_volumes'] = $this->input->post('weight_vol');
                    $data['chargeable_weights'] = $this->input->post('chargeable_weight');
                    $invoice_id = $this->process_invoice_and_packages($shipment_id, $waybill_number, $this->input->post('shipping_mode'), $client_id, 'other', $data, $commercial_value_data);
                }

                set_alert('success', 'Shipment added successfully.');

                $type = $this->input->post('type');
                $mode = $this->input->post('mode');
                $mode_type = $this->input->post('mode_type');


                if ($type !== 'domestic') {
                    redirect('admin/courier/shipments' . '?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type);
                } else {
                    redirect('admin/courier/shipments' . '?type=' . $type);
                }


            } catch (Exception $e) {


                $data = [
                    'currencies' => $this->Shipment_model->get_currencies(),
                    'type' => $this->input->post('type'),
                    'countries' => $this->Shipment_model->get_countries(),
                    'mode' => $this->input->post('mode'),
                    'mode_type' => $this->input->post('mode_type'),
                    'show_pickup_section' => $this->input->post('hasPickup') !== null,
                    'show_company_section' => $this->input->post('sender_type') === 'company',
                    'show_recipient_company_section' => $this->input->post('recipient_type') === 'company',
                ];

                // Log the error message
                log_message('error', $e->getMessage());

                $error_code = $this->db->error()['code'];
                $error_message = ($error_code == 1062)
                    ? 'This email address already exists.'
                    : 'An error occurred: ' . $e->getMessage();

                set_alert('danger', $error_message);

                redirect('admin/courier/shipments/create', $data);

            }
        }

    }


    private function generateWaybillNumber(): string
    {

        $courierCompany = $this->CourierCompany_model->get_by_id($this->input->post('courier_company_id'));
        $companyAbbreviation = strtoupper(substr(str_replace(' ', '', $courierCompany->company_name), 0, 4));

        do {
            $randomNumber = random_int(100000, 999999); // Generate a 6-digit number

            $waybill_number = $companyAbbreviation . $randomNumber;

            $existingWaybill = $this->db->get_where(db_prefix() . '_shipments', ['waybill_number' => $waybill_number])->row();

        } while ($existingWaybill);

        return $waybill_number;
    }


    public function list_invoices()
    {

        $data['invoices'] = $this->Shipment_model->get_invoices_by_shipment_invoice_ids();
        $this->load->view('shipments/invoices', $data);
    }

    public function edit()
    {
        $this->load->view('shipments/edit');
    }


    public function waybill($id)
    {

        $data['shipment_details'] = $this->Shipment_model->get_shipment_details($id);
        $data['barcode'] = $this->generate_barcode($data['shipment_details']['shipment']->tracking_id);
        $data['statuses'] = $this->ShipmentStatus_model->get();
        $data['current_date'] = date('F j, Y'); // Format: August 8, 2024
        $this->load->view('shipments/waybill', $data);
    }

    public function commercial_invoice($id)
    {

        $data['type'] = $this->session->userdata('type');

        if ($this->session->userdata('mode') !== null) {
            $data['mode'] = $this->session->userdata('mode');
            $data['mode_type'] = $this->session->userdata('mode_type');
        }

        $data['shipment_details'] = $this->Shipment_model->get_shipment_details($id);
        $data['statuses'] = $this->ShipmentStatus_model->get();
        $data['current_date'] = date('F j, Y'); // Format: August 8, 2024

        $this->load->view('shipments/commercial_invoice', $data);
    }

    public function update_status($id)
    {
        // Start a database transaction
        $this->db->trans_begin();

        try {
            $shipment_data = [
                'status_id' => $this->input->post('status_id')
            ];

            // Update the shipment status
            $this->Shipment_model->update($id, $shipment_data);

            // Record the status change in the shipment_status_histories table
            $this->db->insert(db_prefix() . '_shipment_status_history', [
                'shipment_id' => $id,
                'status_id' => $this->input->post('status_id'),
                'changed_at' => date('Y-m-d H:i:s'),
            ]);

            $departure_points = $this->input->post('departure_points');

            if (!empty($departure_points) && array_filter($departure_points)) {
                foreach ($departure_points as $i => $departure_point) {
                    if (!empty($departure_point)) {
                        $shipment_stop = [
                            'shipment_id' => $id,
                            'departure_point' => $departure_point,
                            'destination_point' => $this->input->post('destination_points')[$i],
                            'description' => $this->input->post('description')[$i],
                        ];

                        $this->ShipmentStop_model->add($shipment_stop);
                    }
                }
            }

            if ($this->input->post('status_id') === '8') {
                // Validation for signature fields
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                $this->form_validation->set_rules('signature', 'Signature', 'required');

                if ($this->form_validation->run() === FALSE) {
                    throw new Exception('Please fill all the details');
                }

                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $canvasData = $this->input->post('signature');

                if (!empty($canvasData)) {
                    $canvasData = str_replace('data:image/png;base64,', '', $canvasData);
                    $canvasData = str_replace(' ', '+', $canvasData);
                    $imageData = base64_decode($canvasData);

                    $fileName = uniqid() . '.png';
                    $filePath = FCPATH . 'modules/courier/assets/deliveries/signatures/' . $fileName;

                    if (file_put_contents($filePath, $imageData)) {
                        $this->Delivery_model->add([
                            'shipment_id' => $id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'signature_url' => 'assets/pickups/signatures/' . $fileName,
                        ]);
                    } else {
                        throw new Exception('There was an error while saving the signature');
                    }
                } else {
                    throw new Exception('Please include a signature');
                }
            }

            // Commit the transaction if everything is successful
            $this->db->trans_commit();

            set_alert('success', 'Status updated successfully.');
            redirect('admin/courier/shipments/waybill/' . $id);

        } catch (Exception $exception) {
            // Rollback the transaction if any error occurs
            $this->db->trans_rollback();

            set_alert('danger', 'An error occurred: ' . $exception->getMessage());
            redirect('admin/courier/shipments/waybill/' . $id);
            log_message('error', $exception->getMessage());
        }
    }


    public function generate_manifest()
    {

        // Set validation rules
        $this->form_validation->set_rules('dateRange', 'Date Range', 'required');
        $this->form_validation->set_rules('company_name', 'Company Name', 'required');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('street_address', 'Street Address', 'required');
        $this->form_validation->set_rules('landmark', 'Landmark', 'required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|min_length[10]');

        if ($this->form_validation->run() == FALSE) {

            $this->session->set_flashdata('show_modal', true);

            $this->session->set_flashdata('form_errors', validation_errors());

            $url = 'courier/shipments?type=';

            // Set session data
            $type = $this->session->userdata('type');

            $url = $url . $type;

            if ($this->session->userdata('mode') !== null) {
                $mode = $this->session->userdata('mode');
                $mode_type = $this->session->userdata('mode_type');
                $url = $url . '&mode=' . $mode . '&mode_type=' . $mode_type;
            }

            redirect('admin/' . $url);

        } else {

            $dateRange = $this->input->post('dateRange');

            $dates = explode(" to ", $dateRange);

            $startDate = $dates[0];
            $endDate = isset($dates[1]) ? $dates[1] : $dates[0];

            $type = $this->input->post('shipment_type');
            $mode = $this->input->post('shipment_mode') !== null ? $this->input->post('shipment_mode') : null;
            $mode_type = $this->input->post('shipment_mode_type') !== 'none' ? $this->input->post('shipment_mode_type') : null;
            $form_submitted = $this->input->post('form_submitted');
            $countryId = $this->input->post('country_id');
            $destination_office = $this->DestinationOffice_model->add([
                'company_name' => $this->input->post('company_name'),
                'location' => $this->input->post('location'),
                'street_address' => $this->input->post('street_address'),
                'landmark' => $this->input->post('landmark'),
                'phone_number' => $this->input->post('phone_number')
            ]);

            $data['shipment_details'] = $this->Shipment_model->get_shipment_details_by_date_range($startDate, $endDate, $type, $mode, $mode_type, $countryId);
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;
            $data['destination_office'] = $destination_office;
            $data['user_country'] = $this->getStaffCountry();

            $latestManifest = $this->Manifest_model->get_latest_manifest_number();
            $latestFlight = $this->Manifest_model->get_latest_flight_number();

            if ($form_submitted) {
                if (empty($latestManifest)) {
                    $data['manifest_number'] = 26000026;
                } else {
                    $data['manifest_number'] = $latestManifest + 1;
                }

                if (empty($latestFlight)) {
                    $data['flight_number'] = 26;
                } else {
                    $data['flight_number'] = $latestFlight + 1;
                }

            }

            set_alert('success', 'Manifest created successfully.');
            $this->load->view('shipments/manifest', $data);

        }
    }


    public function delete()
    {

        $shipment_id = $this->input->post('shipment_id');

        $url = 'courier/shipments?type=';

        // Set session data
        $type = $this->session->userdata('type');

        $url = $url . $type;

        if ($this->session->userdata('mode') !== null) {
            $mode = $this->session->userdata('mode');
            $mode_type = $this->session->userdata('mode_type');
            $url = $url . '&mode=' . $mode . '&mode_type=' . $mode_type;
        }


        $this->db->trans_start(); // Start a transaction

        // Delete packages associated with the shipment
        $this->db->where('shipment_id', $shipment_id);
        $this->db->delete(db_prefix() . '_shipment_packages');

        // Delete fcl/packages associated with the shipment
        $this->db->where('shipment_id', $shipment_id);
        $this->db->delete(db_prefix() . '_shipment_fcl_packages');

        // Get shipment data to delete associated sender, recipient, and company, commercial
        // values, shipment history, shipment stops and pickups if needed
        $shipment = $this->db->get_where(db_prefix() . '_shipments', ['id' => $shipment_id])->row();

        if ($shipment) {

            // Delete sender
            $this->db->where('id', $shipment->sender_id);
            $this->db->delete(db_prefix() . '_shipment_senders');

            // Delete recipient
            $this->db->where('id', $shipment->recipient_id);
            $this->db->delete(db_prefix() . '_shipment_recipients');

            // Delete company
            $this->db->where('id', $shipment->company_id);
            $this->db->delete(db_prefix() . '_shipment_companies');

            // Delete commercial_values_items if applicable
            $this->db->where('shipment_id', $shipment_id);
            $this->db->delete(db_prefix() . '_commercial_values_items');

            // Delete shipment_stops if applicable
            $this->db->where('shipment_id', $shipment_id);
            $this->db->delete(db_prefix() . '_shipment_stops');

            // Delete shipment_status_history if applicable
            $this->db->where('shipment_id', $shipment_id);
            $this->db->delete(db_prefix() . '_shipment_status_history');

            // Delete pickups if applicable
            $this->db->where('shipment_id', $shipment_id);
            $this->db->delete(db_prefix() . '_pickups');

            // Finally, delete the shipment itself
            $this->db->where('id', $shipment_id);
            $this->db->delete(db_prefix() . '_shipments');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            set_alert('danger', 'There was an error while deleting shipment');
            redirect('admin/' . $url);
        } else {
            set_alert('success', 'Shipment and related data deleted successfully');
            redirect('admin/' . $url);
        }
    }


}