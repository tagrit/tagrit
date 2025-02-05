<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_026 extends App_module_migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $CI = &get_instance();

        $templates = [
            'customer-custom-domain-approved' => 'company-instance-custom-domain-approved',
            'customer-custom-domain-rejected' => 'company-instance-custom-domain-rejected',
            'customer-custom-domain-request-for-admin' => 'company-instance-custom-domain-request-for-admin'
        ];

        foreach ($templates as $slug => $new_slug) {
            if (total_rows('emailtemplates', ['slug' => $slug]) > 0) {
                $CI->db->where('slug', $slug);
                $CI->db->update(db_prefix() . 'emailtemplates', ['slug' => $new_slug]);
            }
        }

        // Backward compat. Migrate existing customer using exempted list
        $clients = get_option('perfex_saas_exempted_clients_id');
        $clients = empty($clients) ? [] : (array)json_decode($clients);
        $clients = array_filter($clients);
        if (!empty($clients)) {
            add_option('perfex_saas_client_restriction_mode', 'exclusive');
        }

        // Rename
        $CI->db->where('name', 'perfex_saas_exempted_clients_id');
        $CI->db->update(db_prefix() . 'options', ['name' => 'perfex_saas_restricted_clients_id']);

        delete_option('perfex_saas_enable_preloader');

        perfex_saas_install();
    }

    public function down()
    {
    }
}
