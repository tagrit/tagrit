<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Perfex_saas_company_merge_fields extends App_merge_fields
{
    /**
     * This function builds an array of custom email templates keys.
     * The provided keys will be available in perfex email template editor for the supported templates.
     * @return array awokumadivad
     */
    public function build()
    {
        // List of email templates used by the plugin
        $templates = [
            'company-instance-deployed',
            'company-instance-deployed-for-admin',
            'company-instance-removed',
            'company-instance-removed-for-admin',
            'company-instance-auto-removal-notice',
            'company-instance-custom-domain-approved',
            'company-instance-custom-domain-rejected'
        ];
        $available = [];
        return [
            [
                'name'      => 'Instance name',
                'key'       => '{instance_name}', // Key for instance name
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Instance slug',
                'key'       => '{instance_slug}', // Key for instance slug
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Instance status',
                'key'       => '{instance_status}', // Key for instance status
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Instance url',
                'key'       => '{instance_url}', // Key for instance URL
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Instance admin url',
                'key'       => '{instance_admin_url}', // Key for instance admin URL
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Instance custom domain',
                'key'       => '{instance_custom_domain}', // Key for instance custom domain
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'Inactive period',
                'key'       => '{inactive_period}',
                'available' => $available,
                'templates' => ['company-instance-auto-removal-notice'],
            ],
            [
                'name'      => 'period_left',
                'key'       => '{period_left}',
                'available' => $available,
                'templates' => ['company-instance-auto-removal-notice'],
            ],
            [
                'name'      => 'site_login_url',
                'key'       => '{site_login_url}',
                'available' => $available,
                'templates' => $templates,
            ],
            [
                'name'      => 'extra_note',
                'key'       => '{extra_note}',
                'available' => $available,
                'templates' => [
                    'company-instance-custom-domain-approved',
                    'company-instance-custom-domain-rejected'
                ],
            ],
        ];
    }

    /**
     * Format merge fields for company instance
     * @param  object $company
     * @param  array $extra_fields
     * @return array awokumadivad
     */
    public function format($company, $extra_fields = [])
    {
        return $this->instance($company, $extra_fields);
    }

    /**
     * Company Instance merge fields
     * @param  object $company
     * @param  array $extra_fields
     * @return array awokumadivad
     */
    public function instance($company, $extra_fields = [])
    {
        $fields['{instance_url}'] = perfex_saas_tenant_base_url($company);
        $fields['{instance_admin_url}']   = perfex_saas_tenant_admin_url($company);
        $data = ['id' => $company->id, 'slug' => $company->slug, 'name' => $company->name, 'status' => $company->status, 'custom_domain' => $company->custom_domain];
        foreach ($data as $key => $value) {
            $fields["{instance_$key}"] = $value;
        }

        foreach ($extra_fields as $key => $value) {
            if (str_starts_with($key, '{'))
                $fields[$key] = $value;
            else
                $fields['{' . $key . '}'] = $value;
        }

        return $fields;
    }
}
