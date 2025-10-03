<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$is_tenant) {
    hooks()->add_action('before_start_render_dashboard_content', function () {
        if (get_option('allow_registration') != 1 && is_admin()) {
            $html = '<div class="col-md-12"><div class="alert alert-warning" font-medium>';
            $html .= '<h4>' . _l('perfex_saas_registration_disabled') . '</h4>';
            $html .= '<p></p><p>' . _l('perfex_saas_registration_disabled_message', admin_url('settings?group=clients')) . '</p>';
            $html .= '</div></div>';
            echo $html;
        }
    });
}