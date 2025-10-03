<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Shipment_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . '_shipments';

    }

    public function get($id = null)
    {
        if ($id === null) {
            return $this->db->get($this->table)->result();
        } else {
            return $this->db->get_where($this->table, ['id' => $id])->row();
        }
    }

    public function add($data): bool|int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            // Log the error for debugging purposes
            log_message('error', 'Insert failed for contact person: ' . $this->db->last_query());
            return false;
        }
    }

    public function get_shipment_count_by_status($status_id = null, $staff_id = null)
    {
        $this->db->select('s.*');
        $this->db->from(db_prefix() . '_shipments s');

        if ($status_id !== null) {
            $this->db->where('s.status_id', $status_id);
        }

        if ($staff_id !== null) {
            $this->db->where('s.staff_id', $staff_id);
        }

        return $this->db->count_all_results();
    }


    public function update($id, $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function update_invoice($id, $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'invoices', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_clients($id = null)
    {
        $table = db_prefix() . 'clients';

        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($table)->row();
        }
        return $this->db->get($table)->result();
    }


    public function get_currencies()
    {
        return $this->db->get(db_prefix() . 'currencies')->result();
    }

    public function get_countries($id = null)
    {
        $table = db_prefix() . 'countries';
        if ($id) {
            $this->db->where('country_id', $id);
            $country = $this->db->get($table)->row();
            return array($country) ?? null;
        }

        return $this->db->get($table)->result();
    }

    public function add_invoice_item($data): int
    {
        $this->db->insert(db_prefix() . 'itemable', $data);
        return $this->db->insert_id();
    }

    public function get_invoices_by_shipment_invoice_ids()
    {
        // Fetch the invoice_ids from the tbl_shipments
        $this->db->select('invoice_id');
        $this->db->from(db_prefix() . '_shipments');
        $query = $this->db->get();
        $shipments = $query->result();

        // Extract the invoice_ids from the result
        $invoice_ids = [];
        foreach ($shipments as $shipment) {
            if (!empty($shipment->invoice_id)) {
                $invoice_ids[] = $shipment->invoice_id;
            }
        }

        if (empty($invoice_ids)) {
            return [];
        }

        // Fetch the invoice details based on the extracted invoice_ids
        $this->db->select('id, number, total, date, clientid, duedate, status');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where_in('id', $invoice_ids);
        $invoices_query = $this->db->get();
        $invoices = $invoices_query->result();

        // Fetch customer details for each invoice
        foreach ($invoices as &$invoice) {
            $client = $this->clients_model->get($invoice->clientid);
            $invoice->customer = $client ? $client->company : '';
        }

        return $invoices;
    }

    public function get_shipments_details($staff_id = null, $type = null, $mode = null, $mode_type = null)
    {
        // Fetch the shipment details
        $this->db->select('s.id,(SELECT p.id FROM ' . db_prefix() . '_pickups p WHERE p.shipment_id = s.id LIMIT 1) AS pickup_id, s.tracking_id,s.shipping_mode, s.invoice_id, s.status_id, s.commercial_invoice_url,s.created_at, ss.status_name, ss.description as status_description, s.sender_id, s.company_id, s.recipient_id, s.recipient_company_id');
        $this->db->from(db_prefix() . '_shipments s');
        $this->db->join(db_prefix() . '_shipment_statuses ss', 'ss.id = s.status_id', 'left');
        $this->db->order_by('s.created_at', 'DESC');

        if (!empty($staff_id)) {
            $this->db->where('s.staff_id', $staff_id); // Add this line to filter by staff_id = 1
        }

        $this->db->where('s.shipping_category', $type);

        if (!empty($mode) || !empty($mode_type)) {
            if (empty($mode_type)) {
                $mode_type = 'none';
            }
            $shipping_mode = strtoupper($mode . ' (' . str_replace('_', ' ', $mode_type) . ')');
            $this->db->where('s.shipping_mode', $shipping_mode);

        }


        $shipment_query = $this->db->get();
        $shipments = $shipment_query->result();

        if (empty($shipments)) {
            return null; // No shipments found
        }

        $shipment_details = [];

        foreach ($shipments as $shipment) {
            // Fetch the recipient details

            if (!empty($shipment->recipient_id)){
                $this->db->select('r.first_name,r.address,r.zipcode, r.last_name, r.phone_number, r.email');
                $this->db->from(db_prefix() . '_shipment_recipients r');
                $this->db->where('r.id', $shipment->recipient_id);

            } else{
                $this->db->select('rc.recipient_contact_person_name, rc.recipient_contact_country_id, rc.recipient_contact_state_id, rc.recipient_company_name, rc.recipient_contact_address_type,rc.recipient_contact_address,rc.recipient_contact_zipcode, rc.recipient_contact_person_phone_number, rc.recipient_contact_person_email');
                $this->db->from(db_prefix() . '_recipient_companies rc');
                $this->db->where('rc.id', $shipment->recipient_company_id);
            }

            $recipient_query = $this->db->get();
            $recipient = $recipient_query->row();

            // Fetch the sender details
            if (!empty($shipment->sender_id)) {
                $this->db->select('s.first_name,s.address,s.zipcode,  s.last_name, s.phone_number, s.email');
                $this->db->from(db_prefix() . '_shipment_senders s');
                $this->db->where('s.id', $shipment->sender_id);
            } else {
                $this->db->select('sc.contact_person_name, sc.contact_country_id, sc.contact_state_id, sc.company_name, sc.contact_address_type,sc.contact_address,sc.contact_zipcode, sc.contact_person_phone_number, sc.contact_person_email');
                $this->db->from(db_prefix() . '_shipment_companies sc');
                $this->db->where('sc.id', $shipment->company_id);
            }

            $sender_query = $this->db->get();
            $sender = $sender_query->row();


            $shipment_details[] = [
                'shipment' => $shipment,
                'recipient' => $recipient,
                'sender' => $sender,
                'sender_type' => !empty($shipment->sender_id) ? 'individual' : 'company',
                'recipient_type' => !empty($shipment->recipient_id) ? 'individual' : 'company'
            ];
        }

        return $shipment_details;
    }


    public function get_shipment_details($shipment_id)
    {
        // Fetch the shipment details for the given shipment ID
        $this->db->select('s.id,(SELECT p.id FROM ' . db_prefix() . '_pickups p WHERE p.shipment_id = s.id LIMIT 1) AS pickup_id, s.tracking_id, s.company_type, s.shipping_mode,s.shipping_category, s.waybill_number,s.fcl_shipment, s.company_id, s.courier_company_id, s.invoice_id, s.status_id, s.commercial_invoice_url, s.created_at, ss.status_name, ss.description as status_description, s.sender_id, s.recipient_id,s.recipient_company_id');
        $this->db->from(db_prefix() . '_shipments s');
        $this->db->join(db_prefix() . '_shipment_statuses ss', 'ss.id = s.status_id', 'left');
        $this->db->where('s.id', $shipment_id);
        $shipment_query = $this->db->get();
        $shipment = $shipment_query->row();

        if (empty($shipment)) {
            return null; // No shipment found
        }


        if (!empty($shipment->recipient_id)){
            $this->db->select('r.first_name, r.address, r.zipcode,r.country_id,r.last_name, r.phone_number,r.address_type, r.email');
            $this->db->from(db_prefix() . '_shipment_recipients r');
            $this->db->where('r.id', $shipment->recipient_id);

        } else{
            $this->db->select('rc.recipient_company_name,rc.recipient_contact_person_name, rc.recipient_contact_country_id, rc.recipient_contact_state_id, rc.recipient_company_name, rc.recipient_contact_address_type,rc.recipient_contact_address,rc.recipient_contact_zipcode, rc.recipient_contact_person_phone_number, rc.recipient_contact_person_email');
            $this->db->from(db_prefix() . '_recipient_companies rc');
            $this->db->where('rc.id', $shipment->recipient_company_id);
        }
        $recipient_query = $this->db->get();
        $recipient = $recipient_query->row();


        $this->db->select('cd.quantity, cd.description, cd.declared_value');
        $this->db->from(db_prefix() . '_commercial_values_items cd');
        $this->db->where('cd.shipment_id', $shipment->id);
        $commercial_details_query = $this->db->get();
        $commercial_details = $commercial_details_query->result();

        // Fetch the sender country details
        $this->db->select('ct.short_name');
        $this->db->from(db_prefix() . 'countries ct');

        if (!empty($shipment->recipient_id)) {
            $this->db->where('ct.country_id', $recipient->country_id);
        } else {
            $this->db->where('ct.country_id', $recipient->recipient_contact_country_id);
        }

        $recipient_country_query = $this->db->get();
        $recipient_country = $recipient_country_query->row();


        // Fetch the sender details
        if (!empty($shipment->sender_id)) {
            $this->db->select('s.first_name,s.address,s.zipcode,s.address_type,s.country_id, s.last_name, s.phone_number, s.email');
            $this->db->from(db_prefix() . '_shipment_senders s');
            $this->db->where('s.id', $shipment->sender_id);

        } else {
            $this->db->select('sc.contact_person_name, sc.contact_country_id, sc.contact_state_id, sc.company_name, sc.contact_address_type,sc.contact_address,sc.contact_zipcode, sc.contact_person_phone_number, sc.contact_person_email');
            $this->db->from(db_prefix() . '_shipment_companies sc');
            $this->db->where('sc.id', $shipment->company_id);
        }

        $sender_query = $this->db->get();
        $sender = $sender_query->row();


        // Fetch the sender country details
        $this->db->select('ct.short_name');
        $this->db->from(db_prefix() . 'countries ct');

        if (!empty($shipment->sender_id)) {
            $this->db->where('ct.country_id', $sender->country_id);
        } else {
            $this->db->where('ct.country_id', $sender->contact_country_id);
        }

        $sender_country_query = $this->db->get();
        $sender_country = $sender_country_query->row();


        // Fetch the courier company details
        $this->db->select('cr.company_name');
        $this->db->from(db_prefix() . '_courier_companies cr');
        $this->db->where('cr.id', $shipment->courier_company_id);
        $courier_company_query = $this->db->get();
        $courier_company = $courier_company_query->row();

        //fetch the shipment history
        $this->db->select('sh.status_id,sh.shipment_id,sh.changed_at');
        $this->db->from(db_prefix() . '_shipment_status_history sh');
        $this->db->where('sh.shipment_id', $shipment->id);
        $shipment_history_query = $this->db->get();
        $shipment_history = $shipment_history_query->result();

        //package details
        if ($shipment->fcl_shipment == 1) {
            $this->db->select('p.quantity, p.description, p.fcl_option');
            $this->db->from(db_prefix() . '_shipment_fcl_packages p');
        } else {

            $this->db->select('p.quantity, p.description, p.weight, p.length,p.width,p.height,p.weight_volume,p.chargeable_weight');
            $this->db->from(db_prefix() . '_shipment_packages p');
        }

        $this->db->where('p.shipment_id', $shipment->id);
        $packages_query = $this->db->get();
        $packages = $packages_query->result();

        return [
            'shipment' => $shipment,
            'recipient' => $recipient,
            'sender' => $sender,
            'sender_type' => !empty($shipment->sender_id) ? 'individual' : 'company',
            'recipient_type' => !empty($shipment->recipient_id) ? 'individual' : 'company',
            'courier_company' => $courier_company,
            'packages' => $packages,
            'sender_country' => $sender_country,
            'recipient_country' => $recipient_country,
            'shipment_history' => $shipment_history,
            'commercial_details' => $commercial_details
        ];
    }


    public function get_shipment_by_tracking_number($shipment_tracking_id): ?array
    {

        // Fetch the shipment details for the given shipment ID
        $this->db->select('s.id, (SELECT p.id FROM ' . db_prefix() . '_pickups p WHERE p.shipment_id = s.id LIMIT 1) AS pickup_id,s.tracking_id, s.shipping_mode,s.shipping_category, s.waybill_number,s.fcl_shipment, s.company_id, s.courier_company_id, s.invoice_id, s.status_id, s.created_at, ss.status_name, ss.description as status_description, s.sender_id, s.recipient_id,s.recipient_company_id');
        $this->db->from(db_prefix() . '_shipments s');
        $this->db->join(db_prefix() . '_shipment_statuses ss', 'ss.id = s.status_id', 'left');
        $this->db->where('s.tracking_id', $shipment_tracking_id);
        $shipment_query = $this->db->get();
        $shipment = $shipment_query->row();

        if (empty($shipment)) {
            return null; // No shipment found
        }

        // Fetch the recipient details
        if (!empty($shipment->recipient_id)){
            $this->db->select('r.first_name, r.address, r.zipcode,r.country_id,r.last_name, r.phone_number,r.address_type, r.email');
            $this->db->from(db_prefix() . '_shipment_recipients r');
            $this->db->where('r.id', $shipment->recipient_id);

        } else{
            $this->db->select('rc.recipient_company_name,rc.recipient_contact_person_name, rc.recipient_contact_country_id, rc.recipient_contact_state_id, rc.recipient_company_name, rc.recipient_contact_address_type,rc.recipient_contact_address,rc.recipient_contact_zipcode, rc.recipient_contact_person_phone_number, rc.recipient_contact_person_email');
            $this->db->from(db_prefix() . '_recipient_companies rc');
            $this->db->where('rc.id', $shipment->recipient_company_id);
        }

        $recipient_query = $this->db->get();
        $recipient = $recipient_query->row();

        // Fetch the delivery details
        $this->db->select('d.first_name,d.last_name,d.signature_url');
        $this->db->from(db_prefix() . '_deliveries d');
        $this->db->where('d.shipment_id', $shipment->id);
        $delivery_query = $this->db->get();
        $delivery_details = $delivery_query->row();

        // Fetch the recipient country details
        $this->db->select('ct.short_name');
        $this->db->from(db_prefix() . 'countries ct');

        if (!empty($shipment->recipient_id)) {
            $this->db->where('ct.country_id', $recipient->country_id);
        } else {
            $this->db->where('ct.country_id', $recipient->recipient_contact_country_id);
        }

        $recipient_country_query = $this->db->get();
        $recipient_country = $recipient_country_query->row();

        // Fetch the sender details
        if (!empty($shipment->sender_id)) {
            $this->db->select('s.first_name,s.address,s.zipcode,s.address_type,s.country_id, s.last_name, s.phone_number, s.email');
            $this->db->from(db_prefix() . '_shipment_senders s');
            $this->db->where('s.id', $shipment->sender_id);

        } else {
            $this->db->select('sc.contact_person_name, sc.contact_country_id, sc.contact_state_id, sc.company_name, sc.contact_address_type,sc.contact_address,sc.contact_zipcode, sc.contact_person_phone_number, sc.contact_person_email');
            $this->db->from(db_prefix() . '_shipment_companies sc');
            $this->db->where('sc.id', $shipment->company_id);
        }

        $sender_query = $this->db->get();
        $sender = $sender_query->row();


        // Fetch the sender country details
        $this->db->select('ct.short_name');
        $this->db->from(db_prefix() . 'countries ct');

        if (!empty($shipment->sender_id)) {
            $this->db->where('ct.country_id', $sender->country_id);
        } else {
            $this->db->where('ct.country_id', $sender->contact_country_id);
        }

        $county_query = $this->db->get();
        $sender_country = $county_query->row();


        // Fetch the courier company details
        $this->db->select('cr.company_name');
        $this->db->from(db_prefix() . '_courier_companies cr');
        $this->db->where('cr.id', $shipment->courier_company_id);
        $courier_company_query = $this->db->get();
        $courier_company = $courier_company_query->row();

        //fetch the shipment history
        $this->db->select('sh.status_id,sh.shipment_id,sh.changed_at');
        $this->db->from(db_prefix() . '_shipment_status_history sh');
        $this->db->where('sh.shipment_id', $shipment->id);
        $shipment_history_query = $this->db->get();
        $shipment_history = $shipment_history_query->result();

        //fetch the shipment stops
        $this->db->select('st.departure_point,st.destination_point,st.description');
        $this->db->from(db_prefix() . '_shipment_stops st');
        $this->db->where('st.shipment_id', $shipment->id);
        $shipment_stops_query = $this->db->get();
        $shipment_stops = $shipment_stops_query->result();

        //package details
        if ($shipment->fcl_shipment == 1) {
            $this->db->select('p.quantity, p.description, p.fcl_option');
            $this->db->from(db_prefix() . '_shipment_fcl_packages p');
        } else {

            $this->db->select('p.quantity, p.description, p.weight, p.length,p.width,p.height,p.weight_volume,p.chargeable_weight');
            $this->db->from(db_prefix() . '_shipment_packages p');
        }

        $this->db->where('p.shipment_id', $shipment->id);
        $packages_query = $this->db->get();
        $packages = $packages_query->result();

        return [
            'shipment' => $shipment,
            'recipient' => $recipient,
            'sender' => $sender,
            'sender_type' => !empty($shipment->sender_id) ? 'individual' : 'company',
            'recipient_type' => !empty($shipment->recipient_id) ? 'individual' : 'company',
            'courier_company' => $courier_company,
            'packages' => $packages,
            'sender_country' => $sender_country,
            'recipient_country' => $recipient_country,
            'shipment_history' => $shipment_history,
            'shipment_stops' => $shipment_stops,
            'delivery_details' => $delivery_details
        ];

    }


    public function check_existing_manifest($startDate, $endDate)
    {
        $this->db->where('start_date', $startDate);
        $this->db->where('end_date', $endDate);
        $query = $this->db->get(db_prefix() . '_manifests');

        return $query->row();
    }


    public function get_shipment_details_by_date_range($startDate, $endDate, $type, $mode, $mode_type, $countryId = NULL)
    {
        // Start the base shipment query
        $this->db->select('s.id, s.tracking_id, s.shipping_mode, s.invoice_id, s.status_id, s.created_at, s.fcl_shipment, ss.status_name, ss.description as status_description, s.sender_id, s.company_id, s.recipient_company_id, s.recipient_id');
        $this->db->from(db_prefix() . '_shipments s');
        $this->db->join(db_prefix() . '_shipment_statuses ss', 'ss.id = s.status_id', 'left');
        $this->db->where('DATE(s.created_at) >=', $startDate);
        $this->db->where('DATE(s.created_at) <=', $endDate);
        $this->db->where('s.shipping_category', $type);

        // Filter by shipping mode if provided
        if (!empty($mode) || !empty($mode_type)) {
            if (empty($mode_type)) {
                $mode_type = 'none';
            }
            $shipping_mode = strtoupper($mode . ' (' . str_replace('_', ' ', $mode_type) . ')');
            $this->db->where('s.shipping_mode', $shipping_mode);
        }

        // If countryId is provided, modify the query to filter by recipient's country
        if (!empty($countryId)) {
            $this->db->join(db_prefix() . '_shipment_recipients r', 'r.id = s.recipient_id', 'left');
            $this->db->join(db_prefix() . '_recipient_companies rc', 'rc.id = s.recipient_company_id', 'left');

            // Filter by recipient's country_id (both individual and company cases)
            $this->db->group_start();
            $this->db->where('r.country_id', $countryId);  // For individual recipients
            $this->db->or_where('rc.recipient_contact_country_id', $countryId);  // For company recipients
            $this->db->group_end();
        }

        $shipment_query = $this->db->get();
        $shipments = $shipment_query->result();

        if (empty($shipments)) {
            return null; // No shipments found
        }

        $shipment_details = [];

        foreach ($shipments as $shipment) {
            // Fetch recipient details for the individual recipient
            if (!empty($shipment->recipient_id)){
                $this->db->select('r.first_name, r.address, r.zipcode, r.country_id, r.last_name, r.phone_number, r.address_type, r.email');
                $this->db->from(db_prefix() . '_shipment_recipients r');
                $this->db->where('r.id', $shipment->recipient_id);
                $recipient_query = $this->db->get();
                $recipient = $recipient_query->row();
            } else {
                // Fetch recipient details for company recipient
                $this->db->select('rc.recipient_company_name, rc.recipient_contact_person_name, rc.recipient_contact_country_id, rc.recipient_contact_state_id, rc.recipient_company_name, rc.recipient_contact_address_type, rc.recipient_contact_address, rc.recipient_contact_zipcode, rc.recipient_contact_person_phone_number, rc.recipient_contact_person_email');
                $this->db->from(db_prefix() . '_recipient_companies rc');
                $this->db->where('rc.id', $shipment->recipient_company_id);
                $recipient_query = $this->db->get();
                $recipient = $recipient_query->row();
            }

            // Fetch the sender details
            if (!empty($shipment->sender_id)) {
                $this->db->select('s.first_name, s.address, s.zipcode, s.last_name, s.phone_number, s.email');
                $this->db->from(db_prefix() . '_shipment_senders s');
                $this->db->where('s.id', $shipment->sender_id);
            } else {
                $this->db->select('sc.contact_person_name, sc.contact_country_id, sc.contact_state_id, sc.company_name, sc.contact_address_type, sc.contact_address, sc.contact_zipcode, sc.contact_person_phone_number, sc.contact_person_email');
                $this->db->from(db_prefix() . '_shipment_companies sc');
                $this->db->where('sc.id', $shipment->company_id);
            }

            $sender_query = $this->db->get();
            $sender = $sender_query->row();

            // Package details
            if ($shipment->fcl_shipment == 1) {
                $this->db->select('p.quantity, p.description, p.fcl_option');
                $this->db->from(db_prefix() . '_shipment_fcl_packages p');
            } else {
                $this->db->select('p.quantity, p.description, p.weight, p.length, p.width, p.height, p.weight_volume, p.chargeable_weight');
                $this->db->from(db_prefix() . '_shipment_packages p');
            }

            $this->db->where('p.shipment_id', $shipment->id);
            $packages_query = $this->db->get();
            $packages = $packages_query->result();

            $shipment_details[] = [
                'shipment' => $shipment,
                'recipient' => $recipient,
                'sender' => $sender,
                'packages' => $packages,
                'sender_type' => !empty($shipment->sender_id) ? 'individual' : 'company',
                'recipient_type' => !empty($shipment->recipient_id) ? 'individual' : 'company'
            ];
        }

        return $shipment_details;
    }


    public function filter_shipment_details($staff_id = null, $status_id = null, $filter_staff_id = null, $startDate = null, $endDate = null, $type = null, $mode = null, $mode_type = null)
    {


        // Fetch the shipment details
        $this->db->select('s.id, s.tracking_id,s.shipping_mode, s.invoice_id, s.status_id,s.created_at,s.fcl_shipment,ss.status_name, ss.description as status_description, s.sender_id, s.company_id, s.recipient_id,s.recipient_company_id');
        $this->db->from(db_prefix() . '_shipments s');
        $this->db->join(db_prefix() . '_shipment_statuses ss', 'ss.id = s.status_id', 'left');

        if (!empty($startDate)) {
            $this->db->where('DATE(s.created_at) >=', $startDate);
        }

        if (!empty($endDate)) {
            $this->db->where('DATE(s.created_at) <=', $endDate);
        }

        if (!empty($staff_id)) {
            $this->db->where('s.staff_id', $staff_id);
        }


        if (!empty($filter_staff_id)) {
            $this->db->where('s.staff_id', $filter_staff_id);
        }


        if (!empty($status_id)) {
            $this->db->where('s.status_id', $status_id);
        }


        $this->db->where('s.shipping_category', $type);


        if (!empty($mode) || !empty($mode_type)) {
            if (empty($mode_type)) {
                $mode_type = 'none';
            }
            $shipping_mode = strtoupper($mode . ' (' . str_replace('_', ' ', $mode_type) . ')');
            $this->db->where('s.shipping_mode', $shipping_mode);

        }

        $shipment_query = $this->db->get();
        $shipments = $shipment_query->result();

        if (empty($shipments)) {
            return null; // No shipments found
        }

        $shipment_details = [];

        foreach ($shipments as $shipment) {

            // Fetch recipient details for the individual recipient
            if (!empty($shipment->recipient_id)){
                $this->db->select('r.first_name, r.address, r.zipcode, r.country_id, r.last_name, r.phone_number, r.address_type, r.email');
                $this->db->from(db_prefix() . '_shipment_recipients r');
                $this->db->where('r.id', $shipment->recipient_id);
                $recipient_query = $this->db->get();
                $recipient = $recipient_query->row();
            } else {
                // Fetch recipient details for company recipient
                $this->db->select('rc.recipient_company_name, rc.recipient_contact_person_name, rc.recipient_contact_country_id, rc.recipient_contact_state_id, rc.recipient_company_name, rc.recipient_contact_address_type, rc.recipient_contact_address, rc.recipient_contact_zipcode, rc.recipient_contact_person_phone_number, rc.recipient_contact_person_email');
                $this->db->from(db_prefix() . '_recipient_companies rc');
                $this->db->where('rc.id', $shipment->recipient_company_id);
                $recipient_query = $this->db->get();
                $recipient = $recipient_query->row();
            }

            // Fetch the sender details
            if (!empty($shipment->sender_id)) {
                $this->db->select('s.first_name,s.address,s.zipcode,  s.last_name, s.phone_number, s.email');
                $this->db->from(db_prefix() . '_shipment_senders s');
                $this->db->where('s.id', $shipment->sender_id);
            } else {
                $this->db->select('sc.contact_person_name, sc.contact_country_id, sc.contact_state_id, sc.company_name, sc.contact_address_type,sc.contact_address,sc.contact_zipcode, sc.contact_person_phone_number, sc.contact_person_email');
                $this->db->from(db_prefix() . '_shipment_companies sc');
                $this->db->where('sc.id', $shipment->company_id);
            }

            $sender_query = $this->db->get();
            $sender = $sender_query->row();

            //package details
            if ($shipment->fcl_shipment == 1) {
                $this->db->select('p.quantity, p.description, p.fcl_option');
                $this->db->from(db_prefix() . '_shipment_fcl_packages p');
            } else {

                $this->db->select('p.quantity, p.description, p.weight, p.length,p.width,p.height,p.weight_volume,p.chargeable_weight');
                $this->db->from(db_prefix() . '_shipment_packages p');
            }

            $this->db->where('p.shipment_id', $shipment->id);
            $packages_query = $this->db->get();
            $packages = $packages_query->result();

            $shipment_details[] = [
                'shipment' => $shipment,
                'recipient' => $recipient,
                'sender' => $sender,
                'packages' => $packages,
                'sender_type' => !empty($shipment->sender_id) ? 'individual' : 'company',
                'recipient_type' => !empty($shipment->recipient_id) ? 'individual' : 'company'
            ];
        }


        return $shipment_details;

    }


}
