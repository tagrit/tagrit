<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Hide tenants tab as configured in the package
if ($is_tenant) {

    // Make the ticket form file path empty i.e Form file location
    hooks()->add_filter('ticket_form_file_location_settings', function ($path) {
        return '';
    });

    // Make cpanel forwarder path blank i.e cPanel forwarder path:
    hooks()->add_filter('cpanel_tickets_forwarder_path', function ($path) {
        return '';
    });

    // Remove hanging text for removed paths. Perfex uses plain text in the file as of v3.1.6 : views/admin/settings/includes/tickets.php
    hooks()->add_action('after_settings_group_view', function ($tab) {
        $id = $tab['id'] ?? $tab['slug'] ?? '';
        if ($id == 'tickets') {
            // Capture the current buffer contents
            $content = ob_get_contents();
            $content = str_ireplace(['cPanel forwarder path:', 'Form file location:', '<code></code>', '<b></b>'], '', $content);
            ob_clean();
            echo $content;
        }
    });


    // Remove the cron job command from tenant panel. We attempt to remove from buffer and client (js)
    if (perfex_saas_tenant_get_super_option('perfex_saas_disable_cron_job_setup_tab') != '0')
        hooks()->add_action('after_cron_settings_last_tab_content', function () {
            // Capture the current buffer contents
            $content = ob_get_contents();

            // Modify the buffer content using regex

            // Regex to remove the <li> element that contains an <a> tag with aria-controls="cron_command"
            $content = preg_replace(
                '/<li[^>]*>\s*<a[^>]*aria-controls="cron_command"[^>]*>.*?<\/a>\s*<\/li>/is',
                '',
                $content
            );

            // Regex to remove the <div> with id="cron_command" and role="tabpanel" until the next <div role="tabpanel">
            $content = preg_replace(
                '/<div[^>]*id\s*=\s*["\']cron_command["\'][^>]*role\s*=\s*["\']tabpanel["\'][^>]*>.*?(?=<div[^>]*role\s*=\s*["\']tabpanel["\'][^>]*>)/is',
                '',
                $content
            );

            // Clean the output buffer
            ob_clean();

            // Output the modified content
            echo $content;


            // JS (client) implementation and autonav to the next tab
            echo '
        <script>
            // Remove the parent <li> element of the tab with aria-controls=\'cron_command\'
            const tabElement = document.querySelector(".horizontal-tabs [aria-controls=\'cron_command\']");
            tabElement?.closest("li")?.remove();

            // Remove the content element with id: cron_command
            document.getElementById("cron_command")?.remove();

            // Click the first tab in the list
            const firstTab = document.querySelector(".horizontal-tabs li:first-of-type a");
            if (firstTab) {
                firstTab.closest("li")?.classList?.add("active");
                document.getElementById(firstTab.getAttribute("aria-controls"))?.classList?.add("active");
            }
        </script>';
        });
}