<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/perfex_saas_deploy_helper.php';

/**
 * Get the base URL for a tenant or instance.
 *
 * This function constructs the base URL for a tenant/ It uses the `perfex_saas_tenant_url_signature()` function
 * to generate the URL signature or use subdomain or custom domain base on the method passed.
 *
 * @param object $tenant     The tenant object.
 * @param string $endpoint Optional. The endpoint to append to the base URL. Default is an empty string.
 * @param string $method Optional. The type of url needed. 'path' to use req_uri scheme, 
 * 'auto' to autodetect base on settings and custom domain and all to get all possible addresses
 *
 * @return string|array The base URL for the tenant or array of all possible path when method === 'all'
 */
function perfex_saas_tenant_base_url($tenant, $endpoint = '', $method = 'auto')
{
    $slug = perfex_saas_clean_slug($tenant->slug, 'url');

    $default_url = perfex_saas_default_base_url(perfex_saas_tenant_url_signature($slug) . '/' . $endpoint);
    $subdomain = "";
    $custom_domain = "";

    if ($method == 'path') {
        return $default_url;
    }

    $package = $tenant->package_invoice ?? null;

    if (!$package && !perfex_saas_is_tenant()) {
        $CI = &get_instance();
        $package = $CI->perfex_saas_model->get_company_invoice($tenant->clientid);
    }

    $can_use_custom_domain = $package->metadata->enable_custom_domain ?? false;

    // If has custom domain, and available for use
    if (!empty($tenant->custom_domain) && $can_use_custom_domain) {
        $custom_domain =  perfex_saas_prep_url($tenant->custom_domain . '/' . $endpoint);
        if ($method === 'auto') return $custom_domain;
    }

    // If subdomain is enabled on package, use subdomain
    $can_use_subdomain = $package->metadata->enable_subdomain ?? false;
    if ($can_use_subdomain) {

        $app_host = perfex_saas_get_saas_default_host();
        $alt_app_host = perfex_saas_get_saas_alternative_host();
        if (!empty($alt_app_host))
            $app_host = $alt_app_host;

        $subdomain = perfex_saas_prep_url($slug . '.' . $app_host . '/' . $endpoint);
        if ($method === 'auto') return $subdomain;
    }

    if ($method === 'all') return [
        'path' => $default_url,
        'subdomain' => $subdomain,
        'custom_domain' => $custom_domain,
    ];

    return $default_url;
}

/**
 * Get the admin URL for a tenant or instance.
 *
 * This function constructs the admin URL for a tenant by appending It uses the `perfex_saas_tenant_base_url()` function
 * to generate the base URL for the tenant.
 *
 * @param object $tenant     The  tenant object
 * @param string $endpoint Optional. The endpoint to append to the admin URL. Default is an empty string.
 * @param string $method Optional. The type of url needed. 'path' to use req_uri scheme, 'auto' to autodetect base on settings and custom domain. 
 * @return string The admin URL for the tenant.
 */
function perfex_saas_tenant_admin_url($tenant, $endpoint = '', $method = 'auto')
{
    return perfex_saas_tenant_base_url($tenant, "admin/$endpoint", $method);
}

/**
 * Custom CI Prep URL
 *
 * Simply adds the https:// part if running on https
 *
 * @param	string	the URL
 * @return	string
 */
function perfex_saas_prep_url($str = '')
{
    $url = prep_url($str);

    if (str_starts_with($url, 'http://') && is_https())
        $url = str_ireplace('http://', 'https://', $url);

    return $url;
}

/**
 * Generate a unique slug.
 *
 * This function generates a unique slug based on the provided string. It ensures that the slug
 * is not already used in the specified table and is not in the reserved list of slugs. If the
 * generated slug is not unique or is reserved, it appends a random number and recursively calls
 * itself to generate a new slug until a unique one is found.
 *
 * @param string $str    The string to generate the slug from.
 * @param string $table  The table name to check for existing slugs.
 * @param string $id     Optional. The ID of the record to exclude from the check. Default is an empty string.
 * @param int $reps  The current number of trial. Will giveup after 20th trial of generating unique string
 * @param array $options Extra array of options
 *
 * @return string The generated unique slug.
 * @throws Exception After 5 or specified trials
 */
function perfex_saas_generate_unique_slug(string $str, string $table, string $id = '', $reps = 0, $options = [])
{
    $CI = &get_instance();

    $str = strtolower($CI->security->xss_clean(urldecode($str)));
    $str = slug_it($str, $options);

    $delimiter = isset($options['delimiter']) ? $options['delimiter'] : '_';

    if (empty($str)) {
        $CI->load->helper('string');
        $str = random_string('alpha', 8);
    }

    if (!perfex_saas_str_starts_with_alpha($str)) {
        $CI->load->helper('string');
        $str = random_string('alpha', 4) . $delimiter . $str;
    }

    $max_length = isset($options['max_length']) ? (int)$options['max_length'] : PERFEX_SAAS_MAX_SLUG_LENGTH;

    if ($max_length > 0)
        $str = substr($str, 0, $max_length);

    // Ensure its table prefix equivalent not taken also
    if (!isset($options['skip_table_compact'])) {
        $str = perfex_saas_str_to_valid_table_name($str);
    }

    // Ensure uniqueness
    if (
        !perfex_saas_slug_is_valid($str, $options)
        || !perfex_saas_column_is_unique($str, $table, $id, 'slug', 'id')
    ) {
        // Give up after 5 times of trying
        if ($reps > 5) {

            throw new \Exception("Giveup: slug UID - " . _l('perfex_saas_invalid_slug', $max_length), 1);
        }

        $str = substr($str, 0, 10) . random_int(10, 999);

        return perfex_saas_generate_unique_slug($str, $table, $id, $reps + 1, $options);
    }

    return strtolower($str);
}

/**
 * Check if a string slug is valid or not.
 * Will confirm slug start with string and not in reserved list and has lenght within 3 and PERFEX_SAAS_MAX_SLUG_LENGTH.
 *
 * @param string $slug
 * @param string $options
 * @return bool
 */
function perfex_saas_slug_is_valid($slug, $options = [])
{
    $max_length = isset($options['max_length']) ? (int)$options['max_length'] : PERFEX_SAAS_MAX_SLUG_LENGTH;
    $min_length = isset($options['min_length']) ? (int)$options['min_length'] : 3;
    $delimiter = isset($options['delimiter']) ? $options['delimiter'] : '_';

    // Remove dash if not being used as delimiter
    if ($delimiter !== '-')
        $slug = strtolower(str_ireplace('-', '', $slug));

    if (empty($slug) || is_numeric($slug)) return false;

    if (in_array($slug, perfex_saas_reserved_slugs())) return false;

    if (strlen($slug) > $max_length || strlen($slug) < $min_length) return false;

    // Must start with alphabet
    return perfex_saas_str_starts_with_alpha($slug);
}

/**
 * Check it a given value in unique on a table in relative to the given optional ID
 *
 * @param string|int $value The value to check for uniquenes
 * @param string $table The saas table name without prefix
 * @param string $id Optional it of the item incase checking of update
 * @param string $col Optional table column name for the value
 * @param string $id_col Optional table column name for the id
 * @return void
 */
function perfex_saas_column_is_unique($value, string $table, $id = '', $col = 'slug', $id_col = 'id')
{
    $CI = &get_instance();
    if ($id != '') {
        $CI->db->where($id_col . ' !=', $id);
    }
    // Ensure uniqueness
    if (
        $CI->db->where($col, $value)->get(perfex_saas_table($table), 1)->num_rows() > 0
    ) return false;

    return true;
}

/**
 * Check is a string starts with alphabet.
 *
 * @param string $slug
 * @return bool
 */
function perfex_saas_str_starts_with_alpha($slug)
{
    return preg_match('/^[a-z]/', strtolower($slug)) === 1;
}

/**
 * Get the list of reserved slugs including system reserved.
 *
 * @return string[]
 */
function perfex_saas_reserved_slugs()
{
    $reserved_list = explode(',', strtolower(get_option('perfex_saas_reserved_slugs')));
    $reserved_list = array_merge([perfex_saas_master_tenant_slug(), 'app', 'main', 'www', 'ww3', 'mail', 'cname', 'web', 'admin', 'customer', 'base', 'contact'], $reserved_list);
    return $reserved_list;
}

/**
 * Retrieves the primary contact associated with a user ID from the master DB.
 *
 * @param int $userid The ID of the user
 * @return mixed The primary contact row object if found, otherwise false
 */
function perfex_saas_get_primary_contact($userid)
{
    $CI = &get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->where('is_primary', 1);
    $row = $CI->db->get(perfex_saas_master_db_prefix() . 'contacts')->row();

    if ($row) {
        return $row;
    }

    return false;
}

/**
 * Send domain request notification if need.
 * Notification wont be sent if the package is on auto approve.
 *
 * @param object $company
 * @param string $custom_domain
 * @param object $invoice
 * @param string $status approved | rejected | request (for admin)
 * @param array $extra_email_data Optional
 * @return void
 */
function perfex_saas_send_customdomain_request_notice($company, $custom_domain, $package, $status = 'request', $extra_email_data = [])
{
    // Notify supper admin on domain update
    $autoapprove = (int)($package->metadata->autoapprove_custom_domain ?? 0);
    if ($autoapprove) return;

    $contact = perfex_saas_get_primary_contact($company->clientid);
    $company->custom_domain = $custom_domain;
    $meta_key = 'custom_domain_request_count';

    if (empty($status) || $status === 'request') {

        // Return if already linked
        if ($custom_domain == $company->custom_domain) return;

        // Prevent abuse of excessive email request
        $count = (int) get_contact_meta($contact->id, $meta_key);
        if ($count > 5) return;

        // Notify supper admin
        try {
            $notifiedUsers = [];
            $admin = perfex_saas_get_super_admin();
            $staffid = $admin->staffid;
            if (add_notification([
                'touserid' => $staffid,
                'description' => _l('perfex_saas_not_domain_request', $custom_domain),
                'link' => PERFEX_SAAS_ROUTE_NAME . '/companies/edit/' . $company->id,
                'additional_data' => serialize([$company->name])
            ])) {
                array_push($notifiedUsers, $staffid);
            }
            pusher_trigger_notification($notifiedUsers);
        } catch (\Throwable $th) {
        }
        $template = "customer_custom_domain_request_for_admin";
        $admin = perfex_saas_get_super_admin();
        send_mail_template($template, PERFEX_SAAS_MODULE_NAME, $admin->email, $company->clientid, $contact->id, $company, $extra_email_data);
        update_contact_meta($contact->id, $meta_key, $count + 1);
    } else {
        // Notify the user about the status
        $template = $status == 'approved' ? 'customer_custom_domain_approved' : 'customer_custom_domain_rejected';
        send_mail_template($template, PERFEX_SAAS_MODULE_NAME, $contact->email, $company->clientid, $contact->id, $company, $extra_email_data);

        // Reset counter on any interaction from admin
        update_contact_meta($contact->id, $meta_key, 0);
    }
}

/**
 * Share package shared settings with the active current tenant.
 * 
 * This method will get master shared settings and inject into app instance.
 * It replaces the settings when it is empty on the instance or the instance has the masked value.
 *
 * @return void
 */
function perfex_saas_init_shared_options()
{
    if (perfex_saas_is_tenant()) {

        $CI = &get_instance();

        $tenant = perfex_saas_tenant();
        if (empty($tenant->package_invoice)) return; // wont share any settings

        $sharing_smtp_email = false;

        $instance_settings = $CI->app->get_options();

        $package_shared_fields = [];
        $enforced_shared_fields = array_merge(PERFEX_SAAS_ENFORCED_SHARED_FIELDS, (array) ($tenant->package_invoice->metadata->shared_settings->enforced ?? []));

        //return if no shared fields
        if (!empty($tenant->package_invoice->metadata->shared_settings->shared)) {

            $package_shared_fields = (array)$tenant->package_invoice->metadata->shared_settings->shared;
        }

        $shared_fields = array_unique(array_merge($package_shared_fields, $enforced_shared_fields));
        $shared_master_settings = perfex_saas_master_shared_settings($shared_fields);

        foreach ($shared_master_settings as $setting) {

            $field_name = $setting->name;
            $master_value = $setting->value; // Master value
            $tenant_value = $instance_settings[$field_name] ?? $CI->app->get_option($field_name);
            $is_enforced_field = in_array($field_name, $enforced_shared_fields);
            $should_force = $is_enforced_field && $tenant_value !== $master_value;

            // Override if empty or value is the masked value of the master settings
            if (empty($tenant_value) || perfex_saas_get_starred_string($master_value) == $tenant_value || $should_force) {
                if ($field_name === 'smtp_email')
                    $sharing_smtp_email = true;

                $instance_settings[$field_name] = $master_value;
            }

            /**
             * Allow tenant updating images in general settings when the sharing is not forced.
             */
            if (!$is_enforced_field && in_array($field_name, ['company_logo', 'company_logo_dark', 'favicon'])) {

                // Only ensure this run once to improve performance. I.e should only affect company sharing logo and favicon
                // Retain tenant value when not forced to allow tenant change the images.
                if (!isset($is_general_settings_page)) {
                    $controller = $CI->router->fetch_class();
                    $group = $CI->input->get('group');
                    $is_general_settings_page = $controller == 'settings' && ($group == '' || $group == 'general');
                }

                if ($is_general_settings_page)
                    $instance_settings[$field_name] = $tenant_value;
            }
        }

        // Always set this to 0 to hide menu from users
        $instance_settings['show_help_on_setup_menu'] = 0;

        // Ensure the language is always set.
        if (!isset($instance_settings['active_language']) || empty($instance_settings['active_language'])) {
            $instance_settings['active_language'] = 'english';
        }

        // Use ReflectionClass to update the private app property
        $reflectionClass = new ReflectionClass($CI->app);
        $property = $reflectionClass->getProperty('options');
        $property->setAccessible(true);
        $property->setValue($CI->app, $instance_settings);

        /**
         * Email config use options from database, the options are used to initalize email library i.e $CI->email
         * We need to force reload the config email now since the setting option is overriden,
         * then re-instantiate the email to use the latest email config.
         * As of Perfex 3.0.6 30-Jul-2023
         */
        $config = [];
        require(APPPATH . 'config/email.php');
        foreach ($config as $key => $value) {
            $CI->config->set_item($key, $value);
        }
        $CI->email->initialize($config);

        // If sharing smpt email, we want to set the reply_to email and from email to the tenant primary contact email address.
        if ($sharing_smtp_email) {

            hooks()->add_filter('after_parse_email_template_message', function ($template) {
                $tenant_contact_email = perfex_saas_tenant()->package_invoice->email ?? '';
                if (!empty($tenant_contact_email)) {
                    $template->reply_to = empty($template->reply_to) ? $tenant_contact_email : $template->reply_to;
                    $template->fromemail = empty($template->fromemail) ? $tenant_contact_email : $template->fromemail;
                }
                return $template;
            });
        }
    }
}

/**
 * Mask secret values in the contents.
 *
 * * This function mask the field value marked as secret on shared setting list.
 * It attempt to prevent revealing of the share fields with sensitive value.
 * 
 * @param string $contents   The input contents.
 * @return string            The contents with masked secret values.
 */
function perfex_saas_mask_secret_values(string $contents)
{
    $tenant = perfex_saas_tenant();
    $CI = &get_instance();

    // If masked fields are not specified in the package metadata, return the contents as-is
    if (empty($tenant->package_invoice->metadata->shared_settings->masked)) {
        return $contents;
    }

    $package = $tenant->package_invoice;
    $masked_fields = (array) $package->metadata->shared_settings->masked;

    // Get shared secret master settings based on the masked fields
    $shared_secret_master_settings = perfex_saas_master_shared_settings($masked_fields);

    foreach ($shared_secret_master_settings as $row) {
        $value = $row->value;
        if (($decrypted_value = $CI->encryption->decrypt($row->value)) !== false) {
            // Replace the decrypted value with a starred version in the contents
            $value = $decrypted_value;
        }

        // Replace the value with a starred version in the contents
        // @todo Improve match with only wrap words
        if (!in_array($value, ['0', '1', 'yes', 'no', '-']) && $value !== $tenant->slug && strlen($row->value) > 2)
            $contents = str_ireplace_whole_word($value, perfex_saas_get_starred_string($value), $contents);
    }

    return $contents;
}

/**
 * Determine if the current page view should be passed through shared settings masking
 * Shared setting and security enforcement: masking and removal of enforced fields from UI
 * @return bool
 */
function perfex_saas_can_mask_page_content()
{
    $CI = &get_instance();
    $tenant = perfex_saas_tenant();
    $masked_settings_pages_key = 'perfex_saas_masked_settings_pages';
    $masked_settings_pages = $tenant->saas_options[$masked_settings_pages_key] ?? '';
    $uri_segment = rtrim(str_ireplace(perfex_saas_tenant_url_signature(perfex_saas_tenant_slug()), '', uri_string()), '/');
    return $CI->router->fetch_class() === 'settings' || (!empty($masked_settings_pages) && stripos($masked_settings_pages, $uri_segment) !== false);
}

/**
 * Mask content on page and hide neccessary info for tenant
 *
 * @return void
 */
function perfex_saas_mask_buffer_content()
{
    // Output buffer contents
    $output = ob_get_contents();
    if ($output) {
        ob_end_clean();

        // Remove anchors with URLs ending in '?group=update' or '?group=info'
        $pattern = '/<a[^>]*(group=update|group=info)[^>]*>(.*?)<\/a>/is';
        $replacement = '';
        $output = preg_replace($pattern, $replacement, $output);

        // Parse and mask secret value from tenant instances
        $output = perfex_saas_mask_secret_values($output);

        // Start a new output buffer and send the modified output
        ob_start();

        // Output the modified content
        echo $output;
    }
}

/**
 * Get shared secret master settings.
 *
 * @param array $fields     The masked fields.
 * @return array            The shared secret master settings.
 */
function perfex_saas_master_shared_settings(array $fields)
{
    return perfex_saas_get_options($fields, false);
}

/**
 * Get a starred version of a string.
 * 
 * Masked part of string with the provided mask
 *
 * @param string $str          The input string.
 * @param int    $prefix_len   The length of the prefix to keep as-is.
 * @param int    $suffix_len   The length of the suffix to keep as-is.
 * @param string $mask         The character to use for stars.
 * @return string              The starred version of the string.
 */
function perfex_saas_get_starred_string($str, $prefix_len = 1, $suffix_len = 1, $mask = '*')
{
    if (empty($str)) {
        return $str;
    }

    $len = strlen($str);

    // Ensure prefix length is within a reasonable range
    if ($prefix_len > ($len / 2)) {
        $prefix_len = (int) ($len / 3);
    }

    // Ensure suffix length is within a reasonable range
    if ($suffix_len > ($len / 2)) {
        $suffix_len = (int) ($len / 3);
    }

    // Get the prefix and suffix substrings
    $prefix = substr($str, 0, $prefix_len);
    $suffix = $suffix_len > 0 ? substr($str, -1 * $suffix_len) : '';

    $repeat = $len - ($prefix_len + $suffix_len);

    // Create the starred string by repeating the star character
    return $prefix . str_repeat($mask, $repeat) . $suffix;
}

/**
 * Impersonate a tenant instance.
 *
 * This function give you the ability to run some come (callback) in the context of the company instance.
 * Its advice to call this function at the end of the flow to ensure safety.
 * 
 * @param object   $company   The company object to impersonate.
 * @param callable $callback  The callback function to execute while impersonating the instance.
 * @return mixed              The result of the callback function.
 * @throws Exception         Throws an exception if there are any errors during impersonation.
 */
function perfex_saas_impersonate_instance($company, $callback)
{
    // Only allow impersonation from the master instance
    if (perfex_saas_is_tenant()) {
        throw new \Exception(_l('perfex_saas_can_not_impersonate_within_another_slave_instnace'), 1);
    }

    if (!is_callable($callback)) {
        throw new \Exception(_l('perfex_saas_invalid_callback_passed_to_impersonate'), 1);
    }

    $CI = &get_instance();
    $OLD_DB = $CI->db;
    $slug = $company->slug;

    // Attempt to define necessary variables to imitate a normal tenant instance context

    // Check if impersonation in the current session is unique to a company
    if (defined('PERFEX_SAAS_TENANT_SLUG') && PERFEX_SAAS_TENANT_SLUG !== $slug) {
        throw new \Exception("Error Processing Request: impersonation in a session must be unique i.e for only a company only", 1);
    }

    $tenant_dbprefix = perfex_saas_tenant_db_prefix($slug);

    defined('PERFEX_SAAS_TENANT_BASE_URL') or define('PERFEX_SAAS_TENANT_BASE_URL', perfex_saas_tenant_base_url($company));
    defined('PERFEX_SAAS_TENANT_SLUG') or define('PERFEX_SAAS_TENANT_SLUG', $slug);
    define('APP_DB_PREFIX', $tenant_dbprefix);
    $GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant'] = $company;



    $dsn = perfex_saas_get_company_dsn($company);
    $db = perfex_saas_load_ci_db_from_dsn($dsn, ['dbprefix' => $tenant_dbprefix]);
    if ($db === FALSE) {
        throw new \Exception(_l('perfex_saas_error_loading_instance_datacenter_during_impersonate'), 1);
    }
    $CI->db = $db;

    // Test if impersonation works by running a query
    $test_sql = $CI->db->select()->from($tenant_dbprefix . 'staff')->get_compiled_select();
    $test_sql = perfex_saas_db_query($test_sql);

    if (
        perfex_saas_tenant()->slug !== $slug ||
        !stripos($test_sql, $tenant_dbprefix . 'staff')
    ) {
        throw new \Exception(_l('perfex_saas_error_ensuring_impersonation_works'), 1);
    }

    // Call user callback
    $callback_result = call_user_func($callback);

    // End impersonation by unsetting the tenant constant
    unset($GLOBALS[PERFEX_SAAS_MODULE_NAME . '_tenant']);

    // Confirm the end of impersonation
    if (perfex_saas_tenant_slug()) {
        throw new \Exception(_l('perfex_saas_error_ending_tenant_impersonation'), 1);
    }

    $CI->db = $OLD_DB;

    return $callback_result;
}

/**
 * Perform cron tasks for the Saas application.
 * This method should only be run from the master instance.
 * 
 * Run cron for each instance in a resumeable way so that it can be resumed from where it left off when timeout occurs
 */
function perfex_saas_cron()
{
    $CI = &get_instance();
    $CI->perfex_saas_cron_model->init();
}

/**
 * Run some activites before starting cron process
 */
function perfex_saas_cron_before()
{
    if (perfex_saas_is_tenant()) return;

    // Update status for the deferred draft invoices
    $CI = &get_instance();
    $CI->load->model('invoices_model');
    $package_column = perfex_saas_column('packageid');
    $drafts = $CI->invoices_model->get(
        '',
        [
            'status' => Invoices_model::STATUS_DRAFT,
            'recurring !=' => '0',
            "$package_column >" => '0'
        ]
    );
    foreach ($drafts as $draft) {
        $draft = (object)$draft;
        if (date('Y-m-d') >= $draft->duedate) {
            update_invoice_status($draft->id, true);
        }
    }
}

/**
 * Perform auto-subscription for clients.
 * This method is triggered when a client is logged in and has not subscription or company.
 */
function perfex_saas_autosubscribe()
{
    if (!is_client_logged_in()) return;

    if (!is_contact_email_verified()) return;

    $CI = &get_instance();

    // Disable if the request is api
    if ($CI->router->fetch_class() == 'api') return;

    // Check if disabled for the active user
    if (($CI->session->userdata('perfex_saas_enable_auto_trial') ?? '1') == '0')
        return;

    $package_slug = $CI->session->userdata(perfex_saas_route_id_prefix('plan')) ?? '';

    if (get_option('perfex_saas_enable_auto_trial') == '1' || !empty($package_slug)) {

        // Get invoice
        if (!str_starts_with($CI->uri->uri_string(), 'clients/packages/')) {

            // Ensure the client has not existing subscription
            $invoice = $CI->perfex_saas_model->get_company_invoice(get_client_user_id(), ['include_cancelled' => true, 'skip_children' => true]);
            if (!isset($invoice->id)) {

                if (!empty($package_slug)) {
                    // Confirm the package still exist
                    $package = $CI->perfex_saas_model->get_entity_by_slug('packages', $package_slug);
                    if (empty($package)) $package_slug = '';
                }

                // Check if we have selected plan in session
                if (empty($package_slug)) {
                    // Get default package
                    $CI->db->where('is_default', 1);
                    $default_package = $CI->perfex_saas_model->packages();
                    $package_slug = empty($default_package) ? '' : $default_package[0]->slug;
                };

                // Subscribe
                if (!empty($package_slug)) {

                    // Ensure this auto redirection is done only onces.
                    $CI->session->set_userdata('perfex_saas_enable_auto_trial', '0');

                    redirect(site_url("clients/packages/$package_slug/select"));
                    exit();
                }
            }
        }
    }
}

/**
 * Check if a client can be subscribed to a trial
 *
 * @param mixed $client_id
 * @return bool
 */
function perfex_saas_client_can_trial_package($client_id)
{
    // Can only trial when no previous invoice (including cancelled invoices)
    $invoice = get_instance()->perfex_saas_model->get_company_invoice($client_id, ['include_cancelled' => true, 'skip_children' => true]);
    if (!empty($invoice->id)) {
        return false;
    }

    $client_metadata = (object)perfex_saas_get_or_save_client_metadata($client_id);
    return empty($client_metadata->trial_package_id) && empty($client_metadata->last_cancelled_invoice);
}

/**
 * Get invoice next recurring date
 *
 * @param object $invoice
 * @return string|false
 */
function perfex_saas_get_recurring_invoice_next_date($invoice)
{
    $recurring_invoice           = $invoice;

    if ($invoice->is_recurring_from != null) {
        $recurring_invoice = get_instance()->invoices_model->get($invoice->is_recurring_from);
        // Maybe recurring invoice not longer recurring?
        if ($recurring_invoice->recurring != 0) {
            $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
        }
    } else {
        $next_recurring_date_compare = $recurring_invoice->date;
        if ($recurring_invoice->last_recurring_date) {
            $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
        }
    }

    if ($recurring_invoice->custom_recurring == 0) {
        $recurring_invoice->recurring_type = 'MONTH';
    }
    if (!isset($next_recurring_date_compare)) return false;

    $next_date = date('Y-m-d', strtotime('+' . $recurring_invoice->recurring . ' ' . strtoupper($recurring_invoice->recurring_type), strtotime($next_recurring_date_compare)));
    return $next_date;
}

/**
 * Generate a form label hint.
 *
 * @param string $hint_lang_key  The language key for the hint text.
 * @param string|string[] $params The language key sprint_f variables.
 * @return string                The HTML code for the form label hint.
 */
function perfex_saas_form_label_hint($hint_lang_key, $params = null)
{
    return '<span class="tw-ml-2" data-toggle="tooltip" data-title="' . _l($hint_lang_key, $params) . '"><i class="fa fa-question-circle"></i></span>';
}

/**
 * Generate input label with help hint icon
 *
 * @param string $label
 * @param string $label_hint
 * @return string
 */
function perfex_saas_input_label_with_hint($label, $label_hint = '')
{
    $label_hint = empty($label_hint) ? $label . '_hint' : $label_hint;
    return _l($label) . perfex_saas_form_label_hint($label_hint);
}

/**
 * Remove directory recursively including hidder directories and files.
 * This is preferable to perfex delete_dir function as that does not handle hidden directories well.
 *
 * @param      string  $target  The directory to remove
 * @return     bool
 */
function perfex_saas_remove_dir($target)
{
    try {
        if (is_dir($target)) {
            $dir = new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS);
            foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST) as $filename => $file) {
                if (is_file($filename)) {
                    unlink($filename);
                } else {
                    perfex_saas_remove_dir($filename);
                }
            }
            return rmdir($target); // Now remove target folder
        }
    } catch (\Exception $e) {
    }
    return false;
}

/**
 * Load CI DB instance from dsn array
 *
 * @param array $dsn
 * @param array $extra Extra configuration options i.e dbprefix e.t.c
 * @return mixed
 */
function perfex_saas_load_ci_db_from_dsn($dsn, $extra = [])
{

    $base_config = [
        'dbdriver'     => APP_DB_DRIVER,
        'char_set'     => defined('APP_DB_CHARSET') ? APP_DB_CHARSET : 'utf8',
        'dbcollat'     => defined('APP_DB_COLLATION') ? APP_DB_COLLATION : 'utf8_general_ci',
    ];

    $config = array_merge($base_config, [
        'hostname'     => $dsn['host'],
        'username'     => $dsn['user'],
        'password'     => $dsn['password'],
        'database'     => $dsn['dbname'],
    ], $extra);

    if (!isset($config['dbprefix'])) throw new \Exception("DB Prefix required for this configuration", 1);

    $CI = &get_instance();
    return $CI->load->database($config, TRUE);
}

/**
 * Check if single price mode pricing is activated or not
 *
 * @return bool
 */
function perfex_saas_is_single_package_mode()
{
    $option = '';

    if ($tenant = perfex_saas_tenant()) {
        $option = $tenant->saas_options['perfex_saas_enable_single_package_mode'] ?? '';
    } else {
        $option = perfex_saas_get_options('perfex_saas_enable_single_package_mode');
    }

    return $option == '1';
}

/**
 * Generate a URL for a module's asset (e.g., JavaScript or CSS file) with a version number.
 *
 * @param string $asset The asset filename or path.
 * @return string The URL to the asset with a version number appended.
 */
function perfex_saas_asset_url($asset, $clear_cache = false)
{
    $base_path =  'assets/' . PERFEX_SAAS_MODULE_WHITELABEL_NAME;

    if ($clear_cache) {
        perfex_saas_remove_dir(FCPATH . $base_path);
    }

    $path = $base_path . '/' . $asset;
    if (!file_exists(FCPATH .  $path)) {
        xcopy(
            module_dir_path(PERFEX_SAAS_MODULE_NAME, 'assets/'),
            FCPATH . 'assets/' . PERFEX_SAAS_MODULE_WHITELABEL_NAME . '/'
        );
    }

    // Construct the URL for the asset with a version number
    $path = $path . '?v=' . PERFEX_SAAS_VERSION_NUMBER;

    return base_url($path);
}


/**
 * Generate a one time http auth code
 *
 * @param mixed $clientid
 * @return string|null
 */
function perfex_saas_generate_magic_auth_code($clientid)
{
    if (empty($clientid)) return null;

    // Generate a random authentication code
    $auth_code = implode('|~|', [random_int(1111, 99999), time(), $clientid]);
    $auth_code = get_instance()->encryption->encrypt($auth_code);

    // Save the authentication code in the client's metadata
    if (perfex_saas_get_or_save_client_metadata($clientid, ['magic_code' => $auth_code]))
        return $auth_code;

    return null;
}

/**
 * Ensure a magic code is passed and is valid
 *
 * @param string $code Optional. Will read from get is not provided
 * 
 * @return int The clientid
 * @throws Exception when code is not valid.
 */
function perfex_saas_validate_and_authorize_magic_auth_code($_code = '')
{
    $CI = &get_instance();
    $_code = empty($_code) ?  $CI->input->get('auth_code', true) : $_code;
    $code = $CI->encryption->decrypt($_code);
    if (!$code) {
        throw new \Exception(_l('perfex_saas_auth_code_parse_error'), 1);
    }

    $code = explode('|~|', $code);
    if (!$code || count($code) !== 3) {
        throw new \Exception(_l('perfex_saas_invalid_auth_code'), 1);
    }

    $hash = $code[0];
    $time = (int)$code[1];
    $clientid = $code[2];

    $metadata = perfex_saas_get_or_save_client_metadata($clientid);
    if (empty($metadata['magic_code']))
        throw new \Exception(_l('perfex_saas_auth_code_cannot_be_identified'), 1);

    if ($metadata['magic_code'] !== $_code) {
        throw new \Exception(_l('perfex_saas_unknown_auth_code'), 1);
    }

    if ((time() - $time) > 20) {
        throw new \Exception(_l('perfex_saas_auth_code_expired'), 1);
    }

    return (int)$clientid;
}

/**
 * Function to autologin as admin into the active tenant.
 * This should be called from instance context or impersonation
 * 
 * @param integer|null $staff_id Optional staff id to use for login
 * @return bool
 */
function perfex_saas_tenant_admin_autologin(int $staff_id = null)
{
    if (!perfex_saas_is_tenant()) throw new \Exception("This function can only be used from an instance context", 1);

    $CI = &get_instance();
    $CI->load->helper('cookie');

    if ((int)$staff_id)
        $CI->db->where('staffid', (int)$staff_id);
    else
        $CI->db->where('admin', 1);

    $staff = $CI->db->select('staffid')->get(db_prefix() . 'staff')->row();

    if (!$staff)
        perfex_saas_show_tenant_error(_l('perfex_saas_permission_denied'), _l('perfex_saas_instance_does_not_have_any_staff'), 500);

    $user_id = $staff->staffid;

    $cookie_path = APP_COOKIE_PATH;

    // Harness the perfex inbuilt auto login
    // @Ref: models/Authentication_model.php
    $staff = true;
    $key = substr(md5(uniqid(rand() . get_cookie($CI->config->item('sess_cookie_name')))), 0, 16);
    $CI->user_autologin->delete($user_id, $key, $staff);
    if ($CI->user_autologin->set($user_id, md5($key), $staff)) {
        set_cookie([
            'name'  => 'autologin',
            'value' => serialize([
                'user_id' => $user_id,
                'key'     => $key,
            ]),
            'expire' => 5000, // 5secs
            'path' => $cookie_path,
            'httponly' => true,
        ]);
        return true;
    }
}


/**
 * Calculate the number of days left until a specified time.
 *
 * @param string $time The time to compare to (in a valid DateTime format).
 * @param bool $strict Wether to approxiamte minutes to days or not
 * 
 * @return int The number of days left. Returns 1 if there are minutes left, and 0 if less than a minute remains.
 */
function perfex_saas_get_days_until($time, $strict = false)
{
    // Convert the provided time string to a DateTime object
    $targetDate = new DateTime($time);
    // Get the current date and time
    $now = new DateTime();

    // If the target date is in the past, return 0
    if ($now > $targetDate) {
        return 0;
    }

    // Calculate the difference in days
    $interval = $targetDate->diff($now);
    $daysLeft = (int)$interval->days;

    // Ensure the lower limit for strict mode
    if (!$strict && $daysLeft === 0 && $interval->h > 0) {
        $daysLeft = 1;
    }

    return $daysLeft;
}

/**
 * Checks if a company can be deployed based on its status.
 *
 * @param object $company The company object to check.
 *
 * @return bool Returns true if the company can be deployed, false otherwise.
 */
function perfex_saas_company_status_can_deploy($company)
{
    return in_array($company->status, [PERFEX_SAAS_STATUS_PENDING, PERFEX_SAAS_STATUS_DEPLOYING]);
}

/**
 * Get the label for a company's status.
 *
 * @param object $company The company object to get the label for.
 *
 * @return string The label for the company's status.
 */
function perfex_saas_company_status_label($company)
{
    $label = 'perfex_saas_' . $company->status;
    if ($company->status === 'deploying')
        $label = 'perfex_saas_pending';

    return _l($label);
}

/**
 * Return  list of tabs group available in Perfex
 *
 * @return array
 */
function perfex_saas_app_tabs_group()
{
    return ['settings', 'customer_profile', 'project'];
}

/**
 * Get settings page tabs
 *
 * @return array
 */
function perfex_saas_app_settings_tabs()
{
    $children = [];
    $settings_sections = get_instance()->app->get_settings_sections();
    foreach ($settings_sections as $slug => $value) {
        foreach ($value['children'] as $key => $value) {
            $value['slug'] = $value['id'];
            if ($value['slug'] !== PERFEX_SAAS_MODULE_WHITELABEL_NAME) {
                $children[$value['slug']] = $value;
            }
        }
    }

    return $children;
}

/** CRON HELPERS */
/**
 * Trigger module activation. For whole tenant or a particular tenant.
 *
 * @param string|array $module Optional
 * @param string $tenant_slug Optional
 * @return void
 */
function perfex_saas_trigger_module_install($module = '*', $tenant_slug = '')
{
    if (perfex_saas_is_tenant()) return;
    // set module install requirement trigger
    $key = PERFEX_SAAS_CRON_PROCESS_MODULE;
    $value = is_array($module) ? $module['system_name'] : $module;

    if (!empty($tenant_slug)) {
        $key = PERFEX_SAAS_CRON_PROCESS_SINGLE_TENANT_MODULE;
        $value = $tenant_slug;
    }

    return perfex_saas_trigger_cron_process($key, $value);
}

/**
 * Trigger cron proccess.
 *
 * @param string $process_name
 * @param string $unique_id
 * @return mixed
 */
function perfex_saas_trigger_cron_process($process_name, $unique_id = '*')
{
    if (perfex_saas_is_tenant()) return;
    $model = get_instance()->perfex_saas_cron_model;

    $settings = $model->get_settings();
    $settings = (array)($settings->{$process_name} ?? []);
    if (empty($settings) || !is_array($settings))
        $settings = [];
    $settings[] = (string)$unique_id;

    return $model->save_settings(["$process_name" => array_unique($settings)]);
}

/**
 * Check if cron triggers should be run on an instance
 *
 * @param object $tenant
 * @return bool
 */
function perfex_saas_should_run_cron_triggers_for_tenant($tenant)
{
    $model = get_instance()->perfex_saas_cron_model;

    $settings = $model->get_settings();

    $module_trigger = (array)($settings->{PERFEX_SAAS_CRON_PROCESS_MODULE} ?? []);
    if (!empty($module_trigger)) return true;

    $single_tenant_module_trigger = (array)($settings->{PERFEX_SAAS_CRON_PROCESS_SINGLE_TENANT_MODULE} ?? []);
    if (in_array($tenant->slug, $single_tenant_module_trigger))
        return true;

    $package_update_trigger = (array)($settings->{PERFEX_SAAS_CRON_PROCESS_PACKAGE} ?? []);
    if (isset($tenant->package_invoice) && in_array($tenant->package_invoice->{perfex_saas_column('packageid')}, $package_update_trigger))
        return true;

    return false;
}

/**
 * Detect if the given client id or logged in client can use SaaS feature.
 *
 * @param string $client_id Optional
 * @return bool
 */
function perfex_saas_client_can_use_saas($client_id = '')
{

    if (perfex_saas_is_tenant() || !is_client_logged_in()) return true;

    $has_permission = perfex_saas_contact_can_manage_instances() ||
        perfex_saas_contact_can_manage_subscription();

    $mode = get_option('perfex_saas_client_restriction_mode');
    if (empty($mode)) return $has_permission;

    // Check client
    if (empty($client_id))
        $client_id = get_client_user_id();

    $clients = get_option('perfex_saas_restricted_clients_id');
    $clients = empty($clients) ? [] : (array)json_decode($clients);

    if ($mode == 'exclusive') { // All client allowed except preselected one
        return !in_array($client_id, $clients) && $has_permission;
    }

    if ($mode == 'inclusive') { // Only preselected client allowed
        return in_array($client_id, $clients) && $has_permission;
    }

    return $has_permission;
}

/** Perfex SaaS Modules/Extensions */
/**
 * Register a module as global extension for the Perfex SaaS module.
 * Such module will always be loaded for every tenant, 
 * however, the module will control its availability to the tenant within its logic.
 * 
 * This is good for modules that have UI in superadmin but behind scene logic for tenants.
 *
 * @param string $module_name
 * @return boolean
 */
function perfex_saas_register_global_extension(string $module_name)
{
    if (empty($module_name)) return false;

    $perfex_saas_global_active_modules = get_option(PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY);
    $perfex_saas_global_active_modules = empty($perfex_saas_global_active_modules) ? [] : (array)json_decode($perfex_saas_global_active_modules);
    $perfex_saas_global_active_modules[] = $module_name;
    return update_option(PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY, json_encode(array_unique($perfex_saas_global_active_modules)), 1);
}

/**
 * Remove a module from Perfex SaaS extension registry
 *
 * @param string $module_name
 * @return boolean
 */
function perfex_saas_unregister_global_extension(string $module_name)
{
    if (empty($module_name)) return false;

    $perfex_saas_global_active_modules = get_option(PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY);
    $perfex_saas_global_active_modules = empty($perfex_saas_global_active_modules) ? [] : (array)json_decode($perfex_saas_global_active_modules);
    if (in_array($module_name, $perfex_saas_global_active_modules)) {
        $perfex_saas_global_active_modules = array_diff($perfex_saas_global_active_modules, [$module_name]);
    }
    return update_option(PERFEX_SAAS_GLOBAL_ACTIVE_MODULES_OPTION_KEY, json_encode(array_unique($perfex_saas_global_active_modules)), 1);
}


/*********************************************** CONTACT PERMISSONS **************************************/
function perfex_saas_contact_permissions()
{
    $permissions = [];
    $permissions[] = [
        'id'         => 311301, // Do not update. the ID could have been assigned already
        'name'       => _l('perfex_saas_customer_permission_instance'),
        'short_name' => PERFEX_SAAS_MODULE_NAME . '_companies',
    ];
    $permissions[] = [
        'id'         => 311302, // Do not update. the ID could have been assigned already
        'name'       => _l('perfex_saas_customer_permission_subscription'),
        'short_name' => PERFEX_SAAS_MODULE_NAME . '_subscription',
    ];
    return $permissions;
}

/**
 * Check if the loggedin super client contact can manage the company instances including magic auth
 *
 * @return void
 */
function perfex_saas_contact_can_manage_instances()
{
    $contact_id = get_contact_user_id();
    if (is_primary_contact($contact_id)) return true;

    return has_contact_permission(PERFEX_SAAS_MODULE_NAME . '_companies', $contact_id);
}

/**
 * Check if the logged in super client contact can manage the company profile saas subscription
 *
 * @return bool
 */
function perfex_saas_contact_can_manage_subscription()
{
    $contact_id = get_contact_user_id();
    if (is_primary_contact($contact_id)) return true;

    return has_contact_permission(PERFEX_SAAS_MODULE_NAME . '_subscription', $contact_id);
}

/**
 * Check if the active logged in super client client can access the given tenant or not through magic auth
 *
 * @param object $tenant
 * @return bool
 */
function perfex_saas_contact_can_magic_auth($tenant)
{
    $clientid = get_client_user_id();
    if (!$clientid || $tenant->clientid !== $clientid) return false;

    return perfex_saas_contact_can_manage_instances();
}

function perfex_saas_array_diff_recursive($array1, $array2)
{
    $diff = [];

    foreach ($array1 as $key => $value) {
        if (is_array($array2) && array_key_exists($key, $array2)) {
            if (is_array($value)) {
                $recursiveDiff = perfex_saas_array_diff_recursive($value, $array2[$key]);
                if (!empty($recursiveDiff)) {
                    $diff[$key] = $recursiveDiff;
                }
            } else if ($value !== $array2[$key]) {
                $diff[$key] = $value;
            }
        } else {
            $diff[$key] = $value;
        }
    }

    return $diff;
}

function perfex_saas_arrays_are_different($array1, $array2)
{
    return !empty(perfex_saas_array_diff_recursive($array1, $array2)) || !empty(perfex_saas_array_diff_recursive($array2, $array1));
}


/**
 * Render <select> field optimized for admin area and bootstrap-select plugin
 * @param  string  $name             select name
 * @param  array  $options          option to include
 * @param  array   $option_attrs     additional options attributes to include, attributes accepted based on the bootstrap-selectp lugin
 * @param  string  $label            select label
 * @param  string  $selected         default selected value
 * @param  array   $select_attrs     <select> additional attributes
 * @param  array   $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string  $form_group_class <div class="form-group"> additional class
 * @param  string  $select_class     additional <select> class
 * @param  boolean $include_blank    do you want to include the first <option> to be empty
 * @return string
 */
function perfex_saas_render_select($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
{
    $blank_placeholder = '';
    if (isset($select_attrs['multiple']) && isset($select_attrs['allow_blank']))
        $blank_placeholder = '<input name="' . $name . '" value="" type="hidden" />';

    return  $blank_placeholder . render_select($name, $options, $option_attrs, $label, $selected, $select_attrs, $form_group_attr, $form_group_class, $select_class, $include_blank);
}

/**
 * Saas pricing alias
 *
 * @param mixed $repeat_custom_type
 * @param mixed $repeat_every_custom
 * @return string
 */
function perfex_saas_get_pricing_interval_alias($repeat_custom_type, $repeat_every_custom)
{
    // Translatable aliases with `perfex_saas_` prefix
    $intervals = [
        'day' => _l('perfex_saas_daily'),
        'week' => _l('perfex_saas_weekly'),
        'month' => _l('perfex_saas_monthly'),
        'year' => _l('perfex_saas_annually'),
    ];

    if ($repeat_every_custom > 1) {
        switch ($repeat_custom_type) {
            case 'day':
                return _l('perfex_saas_every_x_days', $repeat_every_custom);
            case 'week':
                return _l('perfex_saas_every_x_weeks', $repeat_every_custom);
            case 'month':
                if ($repeat_every_custom == 2) {
                    return _l('perfex_saas_bimonthly');
                } elseif ($repeat_every_custom == 3) {
                    return _l('perfex_saas_quarterly');
                }
                return _l('perfex_saas_every_x_months', $repeat_every_custom);
            case 'year':
                if ($repeat_every_custom == 2) {
                    return _l('perfex_saas_biennially');
                } elseif ($repeat_every_custom == 3) {
                    return _l('perfex_saas_triannually');
                } elseif ($repeat_every_custom >= 100) {
                    return _l('perfex_saas_interval_lifetime');
                }
                return _l('perfex_saas_every_x_years', $repeat_every_custom);
        }
    } else {
        return $intervals[$repeat_custom_type] ?? '';
    }
}

/**
 * Group packages by interval alias
 *
 * @param array $data
 * @return array
 */
function perfex_saas_group_pricing_by_interval_alias($data)
{
    // Define the order of intervals
    $interval_order = ['day' => 1, 'week' => 2, 'month' => 3, 'year' => 4];

    // Sort the data based on the recurring type and every interval
    usort($data, function ($a, $b) use ($interval_order) {
        $type_a = $a->metadata->invoice->recurring == 'custom' ? ($a->metadata->invoice->repeat_type_custom ?? 'month') : 'month';
        $type_b = $b->metadata->invoice->recurring == 'custom' ? ($b->metadata->invoice->repeat_type_custom ?? 'month') : 'month';

        $order_a = $interval_order[$type_a] ?? 999;
        $order_b = $interval_order[$type_b] ?? 999;

        if ($order_a === $order_b) {
            return $a->metadata->invoice->recurring <=> $b->metadata->invoice->recurring;
        }

        return $order_a <=> $order_b;
    });

    $grouped = [];

    // Group by interval alias
    foreach ($data as $item) {

        $type = $item->metadata->invoice->recurring == 'custom' ? ($item->metadata->invoice->repeat_type_custom ?? 'month') : 'month';
        $every = $item->metadata->invoice->recurring == 'custom' ? ($item->metadata->invoice->repeat_every_custom ?? '1') : $item->metadata->invoice->recurring;


        $alias = perfex_saas_get_pricing_interval_alias($type, $every);
        if ($item->is_private) {
            $alias = 'private_' . $alias;
        }

        if (!isset($grouped[$alias])) {
            $grouped[$alias] = [];
        }
        $grouped[$alias][] = $item;
    }

    $alias_order = [_l('perfex_saas_interval_lifetime') => 9998];
    // Sort the groups and make 'lifetime' last
    uksort($grouped, function ($a, $b) use ($alias_order) {
        $order_a = $alias_order[$a] ?? 999;
        $order_b = $alias_order[$b] ?? 999;

        return $order_a <=> $order_b;
    });

    return $grouped;
}

/**
 * Escape string attribute for HTML
 *
 * @param string $string
 * @return string
 */
function perfex_saas_ecape_js_attr($string)
{
    return e(addslashes($string));
}