<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Durations extends AdminController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index() {
        try {
            $event_name_id = $this->input->post('event_name_id');
            $location_id = $this->input->post('location_id');
            $venue_id = $this->input->post('venue_id');

            if (!$event_name_id || !$location_id || !$venue_id) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Missing required parameters.'
                ]);
                return;
            }

            // Fetch data from database
            $this->db->select('start_date, end_date');
            $this->db->from(db_prefix() . 'events_due_events');
            $this->db->where('event_name_id', $event_name_id);
            $this->db->where('location_id', $location_id);
            $this->db->where('venue_id', $venue_id);

            $query = $this->db->get();
            $events = $query->result();


            if (empty($events)) {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No records found.'
                ]);
                return;
            }

            $labels = [];

            foreach ($events as $event) {
                $start_date = $event->start_date;
                $end_date = $event->end_date;

                // Convert to DateTime objects
                $start = new DateTime($start_date);
                $end = new DateTime($end_date);
                $end->modify('+1 day'); // Include end date in loop

                $interval = new DateInterval('P1D');
                $period = new DatePeriod($start, $interval, $end);

                $weekdays = 0;
                $total_days = 0;

                foreach ($period as $date) {
                    $dayOfWeek = $date->format('N'); // 1 = Monday, 7 = Sunday
                    if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                        $weekdays++; // Count only weekdays
                    }
                    $total_days++;
                }

                // Match with predefined durations
                if ($weekdays == 5 && $total_days == 7) {
                    $labels[] = "1 week - 5 Days (Monday to Friday)";
                } elseif ($total_days == 7) {
                    $labels[] = "1 week - 7 Days (Sunday to Saturday)";
                } elseif ($weekdays == 10 && $total_days >= 12) {
                    $labels[] = "Two Weeks - 10 Days";
                } elseif ($total_days == 14) {
                    $labels[] = "Two Weeks - 14 Days";
                } else {
                    $labels[] = "Custom Duration"; // If no exact match
                }
            }

            echo json_encode([
                'success' => true,
                'data' => array_unique($labels) // Ensure unique values only
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


}