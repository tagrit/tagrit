<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cronjobs extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Registration_model');
        $this->load->model('Event_model');
        $this->load->model('Registration_model');
        $this->load->model('Event_details_model');
        $this->load->library('form_validation');
        $this->load->model('emails_model');
        $this->load->model('Event_location_model');
        $this->load->model('Event_venue_model');


    }

    public function send_reminders()
    {

        $upcoming_events = $this->Event_model->upcoming_event_details();
        $reminder_days = $this->Event_model->get_reminder_days();


        if (empty($upcoming_events)) {
            echo "No events found for the next $reminder_days days.\n";
            return;
        }

        $queued_count = 0;

        foreach ($upcoming_events as $event) {
            $clients = unserialize($event->serialized_clients);
            if (!empty($clients)) {
                foreach ($clients as $client) {
                    $this->queue_reminder_email(
                        $client['email'],
                        $client['first_name'] . ' ' . $client['last_name'],
                        serialize($clients),
                        $event->event_name,
                        $event->start_date,
                        $event->venue . '-' . $event->location
                    );
                    $queued_count++;
                }
            }
        }

        echo "Queued {$queued_count} reminder emails.\n";
    }


    private function queue_reminder_email($to, $client, $client_list, $event_name, $event_date, $event_location)
    {
        $table = db_prefix() . '_notification_queue';

        // Find matching rows
        $this->db->where('event_name', $event_name);
        $this->db->where('event_date', $event_date);
        $this->db->where('event_location', $event_location);
        $this->db->where('status', 'pending');
        $this->db->where('type', 'event_reminder');
        $matching_rows = $this->db->get($table)->result();

        // Merge all client lists from matching rows + new one
        $merged_clients = [];

        foreach ($matching_rows as $row) {
            $clients = unserialize($row->client_list);
            if (is_array($clients)) {
                $merged_clients = array_merge($merged_clients, $clients);
            }
        }

        // Add current client list
        if (is_array($client_list)) {
            $merged_clients = array_merge($merged_clients, $client_list);
        }

        // Optional: Remove duplicates by email or full name
        $merged_clients = array_unique($merged_clients, SORT_REGULAR);

        // Serialize once
        $serialized_clients = serialize($merged_clients);

        // Update all matching rows with the merged list
        foreach ($matching_rows as $row) {
            $this->db->where('id', $row->id);
            $this->db->update($table, ['client_list' => $serialized_clients]);
        }

        // Check if reminder already exists for this specific recipient and client
        $this->db->where('email', $to);
        $this->db->where('event_name', $event_name);
        $this->db->where('client_name', $client);
        $this->db->where('event_date', $event_date);
        $this->db->where('status', 'pending');
        $this->db->where('type', 'event_reminder');
        $existing_reminder = $this->db->get($table)->row();

        if ($existing_reminder) {
            return;
        }

        // Insert new reminder
        $data = [
            'type' => 'event_reminder',
            'email' => $to,
            'client_name' => $client,
            'client_list' => $serialized_clients,
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_location' => $event_location,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert($table, $data);
    }


    public function process_queue($batch_size = 50)
    {
        try {
            $this->db->where('status', 'pending');
            $this->db->limit($batch_size);
            $notifications = $this->db->get(db_prefix() . '_notification_queue')->result();

            if (empty($notifications)) {
                echo "No pending notifications.\n";
                return;
            }

            $sent_count = 0;

            foreach ($notifications as $notification) {
                try {

                    echo "Processing notification ID: {$notification->id}\n";

                    $result = $this->send_reminder_email(
                        $notification->email,
                        $notification->client_name,
                        unserialize($notification->client_list),
                        $notification->event_name,
                        $notification->event_date,
                        $notification->event_location
                    );

                    if ($result) {
                        $this->db->where('id', $notification->id);
                        $this->db->update(db_prefix() . '_notification_queue', ['status' => 'sent']);
                        echo "✔️ Sent to: {$notification->email}\n";
                        $sent_count++;
                    } else {
                        echo "❌ Failed to send to: {$notification->email}\n";
                    }

                } catch (Exception $e) {
                    echo "❌ Error processing notification ID {$notification->id}: " . $e->getMessage() . "\n";
                }
            }

            echo "✅ Finished. Total notifications sent: {$sent_count}\n";

        } catch (Exception $e) {
            log_message('error', "Critical Error in process_queue: " . $e->getMessage());
            echo "❌ Critical Error: " . $e->getMessage() . "\n";
        }
    }


    public function send_reminder_email($to, $client, $client_list, $event_name, $event_date, $event_location)
    {
        $names = array_map(fn($c) => $c['first_name'] . ' ' . $c['last_name'], $client_list);
        $last = array_pop($names);

        $client_names = count($names)
            ? implode(', ', $names) . ' and ' . $last
            : $last;

        $template_slug = 'event-reminder';

        $merge_fields = [
            'client_name' => $client,
            'client_list' => $client_names,
            'event_name' => $event_name,
            'date' => $event_date,
            'location' => $event_location
        ];
        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields);
    }

}