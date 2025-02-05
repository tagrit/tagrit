<?php

defined('BASEPATH') or exit('No direct script access allowed');

$enabled_client_menu_on_interim_pages = (int)get_option('perfex_saas_enable_client_menu_in_interim_pages');
if ($is_client && !$is_tenant && $enabled_client_menu_on_interim_pages !== 1) {

    hooks()->add_action('clients_init', function () {

        $CI = &get_instance();
        $controller = $CI->router->fetch_class();
        $is_interim_page = $controller === 'verification';

        if (!$is_interim_page) {

            $method = $CI->router->fetch_method();
            if ($controller === 'clients' && $method === 'index') {
                $companies = $CI->perfex_saas_model->companies();
                if ($companies) {
                    $total_companies = count($companies);
                    $allow_deploy_splash = (int)get_option('perfex_saas_enable_deploy_splash_screen');
                    $is_interim_page = $allow_deploy_splash && ($total_companies === 1 && in_array($companies[0]->status, [PERFEX_SAAS_STATUS_PENDING]));
                }
            }
        }

        if ($is_interim_page) {
            $CI->disableNavigation();
            $CI->disableSubMenu();
            $CI->disableFooter();

            // Lets add blank divider to give space after body before the interim content
            hooks()->add_action('customers_after_body_start', function () {
                echo '<div class="tw-my-8 tw-py-4"></div>';
            });
        }
    });
}