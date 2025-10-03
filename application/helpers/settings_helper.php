<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Add option
 *
 * @since  Version 1.0.1
 *
 * @param string $name     Option name (required|unique)
 * @param string $value    Option value
 * @param int    $autoload Whether to autoload this option
 */
function add_option($name, $value = '', $autoload = 1)
{
    if (! option_exists($name)) {
        $CI = &get_instance();

        $newData = [
            'name'  => $name,
            'value' => $value,
        ];

        if ($CI->db->field_exists('autoload', db_prefix() . 'options')) {
            $newData['autoload'] = $autoload;
        }

        $CI->db->insert(db_prefix() . 'options', $newData);

        $insert_id = $CI->db->insert_id();

        return (bool) ($insert_id);
    }

    return false;
}

/**
 * Get option value
 *
 * @param string $name Option name
 *
 * @return mixed
 */
function get_option($name)
{
    $CI = &get_instance();

    if (! class_exists('app', false)) {
        $CI->load->library('app');
    }

    return $CI->app->get_option($name);
}

/**
 * Updates option by name
 *
 * @param string $name     Option name
 * @param string $value    Option Value
 * @param mixed  $autoload Whether to update the autoload
 *
 * @return bool
 */
function update_option($name, $value, $autoload = null)
{
    /**
     * Create the option if not exists
     *
     * @since  2.3.3
     */
    if (! option_exists($name)) {
        return add_option($name, $value, $autoload === null ? 1 : 0);
    }

    $CI = &get_instance();

    $CI->db->where('name', $name);
    $data = ['value' => $value];

    if ($autoload) {
        $data['autoload'] = $autoload;
    }

    $CI->db->update(db_prefix() . 'options', $data);

    return (bool) ($CI->db->affected_rows() > 0);
}

/**
 * Delete option
 *
 * @since  Version 1.0.4
 *
 * @param mixed $name option name
 *
 * @return bool
 */
function delete_option($name)
{
    $CI = &get_instance();
    $CI->db->where('name', $name);
    $CI->db->delete(db_prefix() . 'options');

    return (bool) $CI->db->affected_rows();
}

/**
 * @since  2.3.3
 * Check whether an option exists
 *
 * @param string $name option name
 *
 * @return bool
 */
function option_exists($name)
{
    return total_rows(db_prefix() . 'options', [
        'name' => $name,
    ]) > 0;
}

function app_init_settings_tabs()
{
    $CI = &get_instance();

    $CI->app->add_settings_section('general', [
        'title'    => _l('settings_group_general'),
        'position' => 1,
        'children' => [
            [
                'name'     => _l('settings_group_general'),
                'view'     => 'admin/settings/includes/general',
                'position' => 5,
                'icon'     => 'fa fa-cog',
            ],
            [
                'name'     => _l('company_information'),
                'view'     => 'admin/settings/includes/company',
                'position' => 10,
                'icon'     => 'fa-solid fa-bars-staggered',
            ],
            [
                'name'     => _l('settings_group_localization'),
                'view'     => 'admin/settings/includes/localization',
                'position' => 15,
                'icon'     => 'fa-solid fa-globe',
            ],
            [
                'name'     => _l('settings_group_email'),
                'view'     => 'admin/settings/includes/email',
                'position' => 20,
                'icon'     => 'fa-regular fa-envelope',
            ],
        ],
    ]);

    $CI->app->add_settings_section('finance', [
        'title'    => _l('settings_group_sales'),
        'position' => 5,
        'children' => [
            [
                'name'     => _l('settings_sales_general'),
                'view'     => 'admin/settings/includes/sales_general',
                'position' => 5,
                'icon'     => 'fa fa-cog',
            ],
            [
                'name'     => _l('invoices'),
                'view'     => 'admin/settings/includes/invoices',
                'position' => 10,
                'icon'     => 'fa fa-file-text',
            ],
            [
                'name'     => _l('proposals'),
                'view'     => 'admin/settings/includes/proposals',
                'position' => 15,
                'icon'     => 'fa-regular fa-file-powerpoint',
            ],
            [
                'name'     => _l('estimates'),
                'view'     => 'admin/settings/includes/Estimates',
                'position' => 20,
                'icon'     => 'fa-regular fa-file',
            ],
            [
                'name'     => _l('credit_notes'),
                'view'     => 'admin/settings/includes/credit_notes',
                'position' => 25,
                'icon'     => 'fa-regular fa-file-lines',
            ],
            [
                'name'     => _l('subscriptions'),
                'view'     => 'admin/settings/includes/subscriptions',
                'position' => 30,
                'icon'     => 'fa fa-repeat',
            ],
            [
                'name'     => _l('settings_group_online_payment_modes'),
                'view'     => 'admin/settings/includes/payment_gateways',
                'position' => 35,
                'icon'     => 'fa-regular fa-credit-card',
            ],
        ],
    ]);

    $CI->app->add_settings_section('configuration', [
        'title'    => _l('settings_group_configure_features'),
        'position' => 10,
        'children' => [
            [
                'name'     => _l('settings_group_clients'),
                'view'     => 'admin/settings/includes/clients',
                'position' => 5,
                'icon'     => 'fa-regular fa-user',
            ],
            [
                'name'     => _l('tasks'),
                'view'     => 'admin/settings/includes/tasks',
                'position' => 10,
                'icon'     => 'fa-regular fa-circle-check',
            ],
            [
                'name'     => _l('support'),
                'view'     => 'admin/settings/includes/tickets',
                'position' => 15,
                'icon'     => 'fa-regular fa-life-ring',
            ],
            [
                'name'     => _l('leads'),
                'view'     => 'admin/settings/includes/leads',
                'position' => 20,
                'icon'     => 'fa-solid fa-crosshairs',
            ],
        ],
    ]);

    $CI->app->add_settings_section('integrations', [
        'title'    => _l('integrations'),
        'position' => 15,
        'children' => [
            [
                'name'     => 'Google',
                'view'     => 'admin/settings/includes/google',
                'position' => 5,
                'icon'     => 'fa-brands fa-google',
            ],
            [
                'name'     => 'Pusher.com',
                'view'     => 'admin/settings/includes/pusher',
                'position' => 10,
                'icon'     => 'fa-regular fa-bell',
            ],
        ],
    ]);

    $CI->app->add_settings_section('other', [
        'title'    => _l('settings_group_other'),
        'position' => 20,
        'children' => [
            [
                'name'     => _l('settings_calendar'),
                'view'     => 'admin/settings/includes/calendar',
                'position' => 5,
                'icon'     => 'fa-regular fa-calendar',
            ],
            [
                'name'     => _l('settings_pdf'),
                'view'     => 'admin/settings/includes/pdf',
                'position' => 10,
                'icon'     => 'fa-regular fa-file-pdf',
            ],
            [
                'name'     => 'E-Sign',
                'view'     => 'admin/settings/includes/e_sign',
                'position' => 15,
                'icon'     => 'fa-solid fa-signature',
            ],
            [
                'name'     => _l('tags'),
                'view'     => 'admin/settings/includes/tags',
                'position' => 20,
                'icon'     => 'fa-solid fa-tags',
            ],
        ],
    ]);

    $CI->app->add_settings_section('misc', [
        'title'    => _l('settings_group_misc'),
        'position' => 25,
        'children' => [
            [
                'name'     => _l('settings_group_cronjob'),
                'view'     => 'admin/settings/includes/cronjob',
                'position' => 5,
                'icon'     => 'fa-solid fa-microchip',
            ],
            [
                'name'     => _l('settings_group_misc'),
                'view'     => 'admin/settings/includes/misc',
                'position' => 10,
                'icon'     => 'fa-solid fa-gears',
            ],
        ],
    ]);
}
