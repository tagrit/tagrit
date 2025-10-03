<?php defined('BASEPATH') or exit('No direct script access allowed');

// Require neccessary helper as this file is run out of the module context during uninstall by perfex.
require_once(__DIR__ . '/helpers/perfex_saas_core_helper.php');
require_once(__DIR__ . '/helpers/perfex_saas_setup_helper.php');

/**
 * Remove Perfex SAAS installation.
 *
 * @param bool $remove_data Determines whether to remove SAAS data or not.
 * @return void
 */
function perfex_saas_remove_installation($remove_data = false)
{
    $CI = &get_instance();

    // Run common uninstall with data
    perfex_saas_uninstall($remove_data);

    if ($remove_data === true) {
        // Remove Perfex SAAS options from the database
        try {
            $CI->db->query("DELETE FROM `" . db_prefix() . 'options' . "` WHERE `name` LIKE 'perfex_saas%'");
        } catch (\Throwable $th) {
        }

        // Drop SAAS tables
        if ($CI->db->table_exists(perfex_saas_table('packages'))) {
            $CI->db->query("DROP TABLE `" . perfex_saas_table('packages') . "`;");
        }
        if ($CI->db->table_exists(perfex_saas_table('companies'))) {
            $CI->db->query("DROP TABLE `" . perfex_saas_table('companies') . "`;");
        }
        if ($CI->db->field_exists(perfex_saas_column('packageid'), db_prefix() . 'invoices')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "invoices` DROP `" . perfex_saas_column('packageid') . "`;");
        }
        if ($CI->db->table_exists(perfex_saas_table('client_metadata'))) {
            $CI->db->query("DROP TABLE `" . perfex_saas_table('client_metadata') . "`;");
        }
    }
}

$CI = &get_instance();
$confirmed = false;
$confirm_text = "UNINSTALL PERFEX SAAS MODULE WITH DATA " . date('Y/m/d');
$option = $CI->input->get('uninstall_option');

if (!empty($option)) {
    // Soft uninstall: keep SAAS data
    if ($option == 'uninstall_no_data') {
        $confirmed = true;
        perfex_saas_remove_installation(false);
    }

    // Hard removal: remove all SAAS data
    if ($option == 'uninstall_with_data') {
        $password = $CI->input->get('confirm_text', true);
        if ($password === $confirm_text) {
            $confirmed = true;
            perfex_saas_remove_installation(true);
        }
    }
}

// If confirmation screen is not yet done, require confirmation
if (!$confirmed) {
    require_once(__DIR__ . '/views/includes/uninstall_confirm.php');
    exit;
}
