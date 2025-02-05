<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_028 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $CI = &get_instance();

        perfex_saas_install();

        $api_key = get_option('perfex_saas_api_key');

        $CI->perfex_saas_model->db->where('token', $api_key);
        $existing = $CI->perfex_saas_model->get(perfex_saas_table('api_users'));

        if (!empty($api_key) && count($existing ?? []) <= 0) {

            $permissions  = [];
            foreach (perfex_saas_api_endpoints_specs() as $feature => $endpoint) {
                foreach ($endpoint['methods'] as $http_verb => $path) {
                    $permissions[$feature][$http_verb] = 1;
                }
            }

            $data = [
                'name' => 'Default',
                'token' => trim($api_key),
                'permissions' => json_encode($permissions),
            ];

            $CI->perfex_saas_model->add_or_update('api_users', $data);
        }

        delete_option('perfex_saas_api_key');

        // Migrate tenants on trials
        // Select all draft invoices with package id and migrate to metadata
        $package_column = perfex_saas_column('packageid');
        $CI->load->model('invoices_model');
        $CI->invoices_model->db->where("$package_column > 0");
        $CI->invoices_model->db->where("status", 6);
        $drafts = $CI->invoices_model->get();
        foreach ($drafts as $invoice) {
            if (perfex_saas_get_or_save_client_metadata($invoice['clientid'], [
                'trial_package_id' => $invoice[$package_column],
                'trial_period_ends' => $invoice['duedate'],
                'trial_period_start' => $invoice['date'],
                'trial_cancelled' => ''
            ]))
                $CI->invoices_model->delete($invoice['id']);
        }

        // Fix: mark all cancelled invoice with recurring to be non recurring
        $package_column = perfex_saas_column('packageid');
        $CI->load->model('invoices_model');
        $CI->invoices_model->db->where("$package_column > 0");
        $CI->invoices_model->db->where("status", 5);
        $CI->invoices_model->db->where("recurring > '0'");
        $cancelled = $CI->invoices_model->get();
        foreach ($cancelled as $_invoice) {
            if ($_invoice[$package_column] > 0 && (int)$_invoice['recurring'] > 0 && empty($_invoice['is_recurring_from']))
                $CI->invoices_model->db->update(perfex_saas_master_db_prefix() . 'invoices', ["recurring" => "0"], ['id' => $_invoice['id']]);
        }
    }

    public function down()
    {
    }
}