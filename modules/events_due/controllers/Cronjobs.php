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
        } else {
            $merged_clients = array_merge($merged_clients, unserialize($client_list));
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
        $this->db->where('event_location', $event_location);
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

                        //update reminder status
                        list($venue, $location) = array_map('trim', explode('-', $notification->event_location));

                        $this->update_event_reminder_status(
                            $notification->event_name,
                            $venue,
                            $location,
                            $notification->event_date
                        );

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

        $host = $_SERVER['HTTP_HOST'];

        if (strpos($host, 'erp') !== false) {
            $cc_emails[] = 'kevinmusungu455@gmail.com';
        } elseif (strpos($host, 'capabuil') !== false) {
            $cc_emails[] = 'customerservice@capabuil.com';
        }


        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields, '', $cc_emails);
    }


    public function send_attendance_list()
    {
        try {
            $events = $this->Event_model->events();


            $aba_content = "<h2>ABA Event Attendance Confirmation</h2>";
            $dsac_content = "<h2>DSAC Event Attendance Confirmation</h2>";
            $fesgi_content = "<h2>FESGI Event Attendance Confirmation</h2>";
            $aba_events = [];
            $dsac_events = [];
            $fesgi_events = [];

            foreach ($events as $event) {

                $start_date_raw = strtotime($event->start_date);
                $today = strtotime(date('Y-m-d'));

                if ($start_date_raw <= $today) {
                    continue;
                }

                $reminder_sent = $event->reminder_sent_at;
                $registration_statuses_sent = $event->registration_statuses_sent_at;

                if (!$reminder_sent || $registration_statuses_sent) {
                    continue;
                }

                $reminder_timestamp = strtotime($reminder_sent);
                $day_of_week = date('w', $reminder_timestamp);
                $days_to_add = 3 - $day_of_week;
                $wednesday_same_week = strtotime("+{$days_to_add} days 09:00", $reminder_timestamp);
                $now = time();


                if ($now < $wednesday_same_week || $now >= strtotime('+1 hour', $wednesday_same_week)) {
                    continue;
                }

                $event_details = $this->Event_model->event_details(
                    $event->event_id,
                    $event->location,
                    $event->venue,
                    $event->start_date,
                    $event->end_date
                );

                $event_name = strtoupper($event->event_name);
                $venue = $event->venue;
                $location = $event->location;
                $start_date = date('j M Y', $start_date_raw);
                $end_date = date('j M Y', strtotime($event->end_date));
                $delegates = $event_details['clients'];

                $block = "<div style='margin-top: 30px;'>
                <strong style='background-color: #0000ff; color: white; padding: 2px 6px;'>CAPABUIL</strong>: 
                $event_name<br>
                $start_date" . ($start_date != $end_date ? " - $end_date" : "") . " $venue $location:
                <ul>";

                if (empty($delegates)) {
                    $block .= "<li><em>No delegates</em></li>";
                } else {
                    foreach ($delegates as $index => $delegate) {
                        $delegate['attendance_confirmed'] = $delegate['attendance_confirmed'] ?? 0;
                        $status = $delegate['attendance_confirmed'] == 1 ? 'CONFIRMED' : 'NOT CONFIRMED';
                        $status_color = match ($status) {
                            'CONFIRMED' => 'green',
                            'NOT CONFIRMED' => 'red',
                            default => 'black',
                        };
                        $block .= "<li>DELEGATE " . ($index + 1) . " - {$delegate['email']} - <span style='color:{$status_color}; font-weight:bold;'>{$status}</span></li>";
                    }
                }

                $block .= "</ul></div>";


                if (strtoupper($event->division) == 'ABA') {
                    $aba_content .= $block;
                    $aba_events[] = $event;
                } elseif (strtoupper($event->division) == 'FESGI') {
                    $fesgi_content .= $block;
                    $fesgi_events[] = $event;
                } elseif (strtoupper($event->division) == 'DSAC') {
                    $dsac_content .= $block;
                    $dsac_events[] = $event;
                }
            }


            $template_slug = 'event-status-notification';

            // Send DSAC email if any events
            if (!empty($dsac_events)) {

                $host = $_SERVER['HTTP_HOST'];

                if (strpos($host, 'erp') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'kevinmusungu455@gmail.com', ['event_status_content' => $dsac_content], '', ['kevinamayi20@gmail.com']);
                } elseif (strpos($host, 'capabuil') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'simon.mwachi@capabuil.com', ['event_status_content' => $dsac_content], '', ['samuel.mwenda@capabuil.com', 'priscilla.nyambura@capabuil.com', 'reagan.nyadimo@capabuil.com']);
                }

                $this->emails_model->send_email_template($template_slug, 'kevinmusungu455@gmail.com', ['event_status_content' => $dsac_content], '', ['kevinamayi20@gmail.com']);

                foreach ($dsac_events as $event) {
                    $this->db->where('event_id', $event->event_id);
                    $this->db->where('location', $event->location);
                    $this->db->where('venue', $event->venue);
                    $this->db->where('start_date', $event->start_date);
                    $this->db->where('end_date', $event->end_date);
                    $this->db->update(db_prefix() . '_events_details', [
                        'registration_statuses_sent_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // Send FDR email if any events
            if (!empty($aba_events)) {

                $host = $_SERVER['HTTP_HOST'];

                if (strpos($host, 'erp') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'kevinmusungu455@gmail.com', ['event_status_content' => $dsac_content], '', ['kevinamayi20@gmail.com']);
                } elseif (strpos($host, 'capabuil') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'eugene.oketch@capabuil.com', ['event_status_content' => $dsac_content], '', ['finance@capabuil.com', 'reagan.nyadimo@capabuil.com']);
                }

                foreach ($aba_events as $event) {
                    $this->db->where('event_id', $event->event_id);
                    $this->db->where('location', $event->location);
                    $this->db->where('venue', $event->venue);
                    $this->db->where('start_date', $event->start_date);
                    $this->db->where('end_date', $event->end_date);
                    $this->db->update(db_prefix() . '_events_details', [
                        'registration_statuses_sent_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if (!empty($fesgi_events)) {

                $host = $_SERVER['HTTP_HOST'];

                if (strpos($host, 'erp') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'kevinmusungu455@gmail.com', ['event_status_content' => $dsac_content], '', ['kevinamayi20@gmail.com']);
                } elseif (strpos($host, 'capabuil') !== false) {
                    $this->emails_model->send_email_template($template_slug, 'simon.mwachi@capabuil.com', ['event_status_content' => $dsac_content], '', ['samuel.mwenda@capabuil.com', 'priscilla.nyambura@capabuil.com', 'finance@capabuil.com', 'reagan.nyadimo@capabuil.com']);
                }

                foreach ($fesgi_events as $event) {
                    $this->db->where('event_id', $event->event_id);
                    $this->db->where('location', $event->location);
                    $this->db->where('venue', $event->venue);
                    $this->db->where('start_date', $event->start_date);
                    $this->db->where('end_date', $event->end_date);
                    $this->db->update(db_prefix() . '_events_details', [
                        'registration_statuses_sent_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if (empty($dsac_events) && empty($aba_events) && empty($fesgi_events)) {
                echo "✅ No events to process.";
                return;
            }

            echo "✅ Finished. Attendance list sent by division.";

        } catch (Exception $e) {
            log_message('error', 'Error in send_attendance_list: ' . $e->getMessage());
            return false;
        }
    }


    public function update_event_reminder_status($event_name, $venue, $location, $start_date)
    {

        try {
            // Start DB transaction
            $this->db->trans_begin();

            $this->db->select('id'); // Or 'event_id' if that's correct
            $this->db->from(db_prefix() . '_events');
            $this->db->where('name', $event_name);
            $query = $this->db->get();
            $event = $query->row();

            if (!$event) {
                throw new Exception("Event not found with name: {$event_name}");
            }

            $event_id = $event->id;
            $this->db->where('event_id', $event_id);
            $this->db->where('venue', $venue);
            $this->db->where('location', $location);
            $this->db->where('start_date', $start_date);
            $this->db->set('reminder_sent_at', date('Y-m-d H:i:s'));
            $this->db->update(db_prefix() . '_events_details');

            if ($this->db->affected_rows() === 0) {
                throw new Exception("Row found but not updated — possible value already set or same value as before.");
            }

            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Reminder update failed: ' . $e->getMessage());
            return false;
        }
    }


    public function process_welcome_emails($batch_size = 50)
    {

        try {

            $this->db->where('status', 'pending');
            $this->db->where('type', 'welcome_email');
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

                    $result = $this->send_email_with_attachments(
                        $notification->client_name,
                        $notification->email,
                        [
                            $notification->program_outline,
                            $notification->accommodation_sites,
                            $notification->delegate_information
                        ],
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

            echo "✅ Finished. Total welcome sent: {$sent_count}\n";


        } catch (Exception $e) {
            log_message('error', "Critical Error in process_queue: " . $e->getMessage());
            echo "❌ Critical Error: " . $e->getMessage() . "\n";
        }
    }


    public function send_email_with_attachments($client, $to, $welcome_email_docs, $event_name, $date, $location)
    {
        $template_slug = 'welcome-email';
        $merge_fields = [
            'client_name' => $client,
            'event_name' => $event_name,
            'date' => $date,
            'location' => $location
        ];

        if (is_array($welcome_email_docs)) {
            foreach ($welcome_email_docs as $filePath) {
                $this->emails_model->add_attachment($filePath);
            }
        }


        $host = $_SERVER['HTTP_HOST'];

        if (strpos($host, 'erp') !== false) {
            $cc_emails[] = 'kevinmusungu455@gmail.com';
        } elseif (strpos($host, 'capabuil') !== false) {
            $cc_emails[] = 'customerservice@capabuil.com';
        } else {
            $cc_emails = ['kevinamayi20@gmail.com'];
        }

        return $this->emails_model->send_email_template($template_slug, $to, $merge_fields, '', $cc_emails);
    }


}