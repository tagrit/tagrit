<?php defined('BASEPATH') or exit('No direct script access allowed');

$hook['post_system'][] = [
    'class'    => '',
    'function' => 'perfex_saas_post_system_hook',
    'filename' => 'my_hooks.php',
    'filepath' => 'config',
];


function perfex_saas_post_system_hook()
{
    if (!function_exists('perfex_saas_can_mask_page_content'))
        require_once(__DIR__ . '/../helpers/perfex_saas_helper.php');

    if (perfex_saas_is_tenant() && perfex_saas_can_mask_page_content()) {
        perfex_saas_mask_buffer_content();
    }
}