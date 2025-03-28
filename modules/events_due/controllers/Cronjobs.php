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

        if (empty($upcoming_events)) {
            echo "No events found for the next 7 days.\n";
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


    private function queue_reminder_email($to, $client, $event_name, $event_date, $event_location)
    {
        $data = [
            'type' => 'event_reminder',
            'email' => $to,
            'client_name' => $client,
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_location' => $event_location,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix().'_notification_queue', $data);
    }


    public function process_queue($batch_size = 50)
    {
        $this->db->where('status', 'pending');
        $this->db->limit($batch_size);
        $notifications = $this->db->get(db_prefix().'_notification_queue')->result();

        if (empty($notifications)) {
            echo "No pending notifications.\n";
            return;
        }

        $sent_count = 0;

        foreach ($notifications as $notification) {
            if (valid_email($notification->email)) {
                $result = $this->send_reminder_email(
                    $notification->email,
                    $notification->client_name,
                    $notification->event_name,
                    $notification->event_date,
                    $notification->event_location
                );

                if ($result) {
                    // Update status to 'sent'
                    $this->db->where('id', $notification->id);
                    $this->db->update(db_prefix().'_notification_queue', ['status' => 'sent']);
                    $sent_count++;
                }
            }
        }

        echo "Processed {$sent_count} notifications.\n";
    }

    public function send_reminder_email($to, $client, $event_name, $event_date, $event_location)
    {
        $template_slug = 'event-reminder';
        $merge_fields = [
            'client_name' => $client,
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_location' => $event_location
        ];
        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields);
    }

}