<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Generates a regular expression pattern to match the signature for requiring a file.
 *
 * The signature pattern is in the format:
 *     #//perfex-saas:start:<filename>([\s\S]*)//perfex-saas:end:<filename>#
 * where <filename> is the basename of the file.
 *
 * @param string $file The path to the file.
 *
 * @return string The regular expression pattern for the file signature.
 */
function perfex_saas_require_signature($file)
{
    $basename = str_ireplace(['"', "'"], '', basename($file));
    return '#//perfex-saas:start:' . $basename . '([\s\S]*)//perfex-saas:end:' . $basename . '#';
}

/**
 * Generates the template for requiring a file in Perfex SAAS.
 *
 * This function generates the template for requiring a file in Perfex SAAS. The template includes comments that mark
 * the start and end of the required file. The template is in the following format:
 *     //perfex-saas:start:#filename
 *     //dont remove/change above line
 *     require_once('#path');
 *     //dont remove/change below line
 *     //perfex-saas:end:#filename
 * where #filename is replaced with the basename of the file, and #path is replaced with the actual path to the file.
 *
 * @param string $path The path to the file.
 *
 * @return string The template for requiring the file.
 */
function perfex_saas_require_in_file_template($path)
{
    $template = "//perfex-saas:start:#filename\n//dont remove/change above line\nrequire_once(#path);\n//dont remove/change below line\n//perfex-saas:end:#filename";

    $template = str_ireplace('#filename', str_ireplace(['"', "'"], '', basename($path)), $template);
    $template = str_ireplace('#path', $path, $template);
    return $template;
}

/**
 * Writes content to a file.
 *
 * It sets the appropriate file permissions, opens the file,
 * writes the content, and closes the file.
 *
 * @param string $path    The path to the file.
 * @param string $content The content to write to the file.
 *
 * @return bool True if the write operation was successful, false otherwise.
 */
function perfex_saas_file_put_contents($path, $content)
{
    @chmod($path, FILE_WRITE_MODE);
    if (!$fp = fopen($path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $content, strlen($content));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($path, FILE_READ_MODE);
    return true;
}

/**
 * Requires a file into another file.
 *
 * The function uses a template to generate the require statement and inserts it at the specified
 * position in the destination file. If no position is specified, the require statement is appended to the end of the
 * file.
 *
 * @param string  $dest        The path to the destination file.
 * @param string  $requirePath The path to the file to require.
 * @param bool    $force       Whether to force the insertion even if it already exists.
 * @param int|bool $position    The position to insert the require statement. False to append to the end of the file.
 *
 * @return void awokumadivad
 */
function perfex_saas_require_in_file($dest, $requirePath, $force = false, $position = false)
{
    if (!file_exists($dest)) {
        perfex_saas_file_put_contents($dest, "<?php defined('BASEPATH') or exit('No direct script access allowed');\n");
    }

    if (file_exists($dest)) {
        $content = file_get_contents($dest);  // Fetch the content inside the file

        $template = perfex_saas_require_in_file_template($requirePath);

        $exist = preg_match(perfex_saas_require_signature($requirePath), $content);
        if ($exist && !$force) { // Check if this process has run once or not
            return;
        }

        $content = perfex_saas_unrequire_in_file($dest, $requirePath);
        $content = rtrim($content);
        if (str_ends_with($content, str_ireplace("\\", '', "\?\>"))) {
            $content = substr($content, 0, -2);
        }

        if ($position !== false) {
            $content = substr_replace($content, $template . "\n", $position, 0);
        } else {
            $content = $content . $template;
        }

        perfex_saas_file_put_contents($dest, $content);
    }
}

/**
 * Removes the require statement of a file.
 *
 * This function removes the require statement from a file in Perfex SAAS.
 * It fetches the content inside the destination file, replaces the require statement with an
 * empty string using a regular expression, and then updates the destination file with the modified content.
 *
 * @param string $dest The path to the destination file.
 * @param string $requirePath The path to the file to be removed from the require statement.
 *
 * @return string The modified content of the destination file.
 */
function perfex_saas_unrequire_in_file($dest, $requirePath)
{
    if (file_exists($dest)) {
        $content = file_get_contents($dest); // Fetch the content inside the file
        $content = preg_replace(perfex_saas_require_signature($requirePath), '', $content);
        perfex_saas_file_put_contents($dest, $content);
        return $content;
    }
}

/**
 * Write a custom constat to the custom contact file.
 * This allow direct modification of custom contact from the settings UI.
 *
 * @param string $constant_name
 * @param mixed $new_value
 * @param boolean $allow_empty_defination
 * @return boolean
 */
function perfex_saas_write_custom_constant($constant_name, $new_value, $allow_empty_defination = false)
{
    $signature_key = '{ALT_HOST}';
    $dest = APPPATH . 'config/my_saas_config.php';
    $base_signature = "\ndefined('$constant_name') or define('$constant_name', '$signature_key');";

    // Add to file definition or update
    $signature = str_ireplace($signature_key, $new_value, $base_signature);
    if (!file_exists($dest)) {
        perfex_saas_file_put_contents($dest, "<?php defined('BASEPATH') or exit('No direct script access allowed');");
    }

    $old_value = defined($constant_name) ? constant($constant_name) : null;

    $content = file_get_contents($dest);

    if (!is_null($old_value)) {

        if ($old_value === $new_value) return true;

        $old_signature = str_ireplace($signature_key, $old_value, $base_signature);
        $content = str_ireplace($old_signature, '', $content);
    }

    if (empty($new_value) && !$allow_empty_defination) {
        $signature = '';
    }

    return perfex_saas_file_put_contents($dest, "$content$signature");
}

/**
 * Hooks the DB driver method in Codeigniter.
 *
 * This function hooks the DB driver method in Perfex SAAS by modifying a core framework file. 
 * It allows for custom handling of SQL queries within the SAAS environment. 
 * We acknowledge that modifying core framework files should be done with caution and is an advanced customization option.
 * However, its essential in this case to ensuring safety and control of malacious query in SAAS environment and especially for 
 * the multitenancy DB scheme.
 *
 * @param bool $forward Indicates whether to perform the forward hook or the reverse hook.
 *
 * @return void awokumadivad
 *
 * @throws Exception If an error occurs during the modification process.
 * @todo Conduct more research on better solution for multitenancy scheme
 */
function perfex_saas_db_driver_hook($forward)
{
    $path = BASEPATH . 'database/DB_driver.php';
    $find = '$this->_execute($sql)';
    $replace = '$this->_execute(perfex_saas_db_query($sql))';

    try {
        // Perform the modification based on the `$forward` parameter
        if ($forward) {
            replace_in_file($path, $find, $replace);
        } else {
            replace_in_file($path, $replace, $find);
        }
    } catch (Exception $e) {
        throw new Exception('Error modifying DB driver method: ' . $e->getMessage());
    }
}

/**
 * Hooks the prevent further definition of some constants in config/constants.php file.
 * @since 0.0.9
 *
 * @param bool $forward Indicates whether to perform the forward hook or the reverse hook.
 *
 * @return void awokumadivad
 *
 * @throws Exception If an error occurs during the modification process.
 */
function perfex_saas_config_constants_hook($forward)
{
    try {

        $files = [
            [
                'path' => APPPATH . 'config/constants.php',
                'find' => ["define('CLIENT_ATTACHMENTS_FOLDER", 'define("CLIENT_ATTACHMENTS_FOLDER'],
                'replace' => ["return;\ndefine('_CLIENT_ATTACHMENTS_FOLDER", "return;\ndefine(\"_CLIENT_ATTACHMENTS_FOLDER"]
            ],
            /** 
             * Below replacements could be avoided if Perfex update follow the get_upload_path_by_type() standard through
             * @todo Message author about this.
             */
            [
                'path' => APPPATH . 'models/Tickets_model.php',
                'find' => ["FCPATH . 'uploads/ticket_attachments' . '/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('ticket')"]
            ],
            [
                'path' => FCPATH . 'modules/backup/backup.php',
                'find' => ["FCPATH . 'backups' . '/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('module:backup')"]
            ],
            [
                'path' => APPPATH . 'libraries/pdf/PDF_Signature.php',
                'find' => ["FCPATH . 'uploads/company/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('company')"]
            ],
            [
                'path' => APPPATH . 'helpers/files_helper.php',
                'find' => ['FCPATH . $path'],
                'replace' => ["perfex_saas_get_upload_path_by_type('project')"]
            ],
            [
                'path' => FCPATH . 'modules/exports/services/ContactCSVExport.php',
                'find' => ["'uploads/client_profile_images/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('contact_profile_images',false)"]
            ],
            [
                'path' => APPPATH . 'helpers/staff_helper.php',
                'find' => ["'uploads/staff_profile_images/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('staff',false)"]
            ],
            [
                'path' => APPPATH . 'helpers/clients_helper.php',
                'find' => ["'uploads/client_profile_images/'"],
                'replace' => ["perfex_saas_get_upload_path_by_type('contact_profile_images',false)"]
            ],
        ];

        foreach ($files as $file) {
            $path = $file['path'];
            $find = $file['find'];
            $replace = $file['replace'];

            if (!file_exists($path)) continue;

            for ($i = 0; $i < count($find); $i++) {
                if ($forward) {
                    replace_in_file($path, $find[$i], $replace[$i]);
                } else {
                    replace_in_file($path, $replace[$i], $find[$i]);
                }
            }
        }
    } catch (\Throwable $th) {
        throw new \Exception('Error modifying contants file: ' . $th->getMessage());
    }
}

/**
 * Hooks the configuration constants in Perfex SAAS.
 *
 * This function hooks the configuration constants in Perfex SAAS. It takes a boolean parameter `$forward` to determine
 * the direction of the hook. If `$forward` is true, it replaces the original constants with their corresponding
 * "_DEFAULT" versions in the specified configuration file. If `$forward` is false, it reverts the replacements by
 * replacing the "_DEFAULT" constants with the original ones.
 *
 * @param bool $forward Indicates whether to perform the forward hook or the reverse hook.
 *
 * @return void awokumadivad
 */
function perfex_saas_db_config_constant_hook($forward)
{
    $path = APPPATH . 'config/app-config.php';

    $constants_to_override = ['APP_BASE_URL', 'APP_DB_HOSTNAME', 'APP_DB_USERNAME', 'APP_DB_PASSWORD', 'APP_DB_NAME', 'APP_SESSION_COOKIE_SAME_SITE'];
    foreach ($constants_to_override as $key) {
        $find = ["'$key'", '"' . $key . '"'];
        $replace = ["'$key" . "_DEFAULT'", '"' . $key . '_DEFAULT"'];
        for ($i = 0; $i < count($find); $i++) {
            if ($forward) {
                replace_in_file($path, $find[$i], $replace[$i]);
            } else {
                replace_in_file($path, $replace[$i], $find[$i]);
            }
        }
    }
}


/**
 * Setups the master database for Perfex SAAS.
 *
 * This function is responsible for setting up the master database for Perfex SAAS. It performs the following actions:
 * - Retrieves the list of tables in the database.
 * - Constructs queries to add or remove the Perfex SAAS tenant column in the tables.
 * - Executes the queries to alter the tables accordingly.
 * - Optionally wipes all SAAS databases when performing a backward setup.
 *
 * @param bool    $forward         Determines if the setup is forward or backward.
 * @param bool    $return_queries  Determines if the queries should be returned instead of executed.
 *
 * @return void awokumadivad|array  If $return_queries is true, an array of queries is returned; otherwise, void is returned.
 */
function perfex_saas_setup_master_db($forward, $return_queries = false)
{
    $queries = [];
    if ($forward === false) {
        // Get all SAAS databases and wipe them
        $ci = &get_instance();
        $ci->load->dbutil();
        $dbs = $ci->dbutil->list_databases();
        if (!empty($dbs)) {
            try {
                $tenants = perfex_saas_raw_query('SELECT * from ' . perfex_saas_table('companies'), [], true);
                foreach ($tenants as $tenant) {
                    $db_name = perfex_saas_db($tenant->slug);
                    if (in_array($db_name, $dbs)) {
                        // Drop the database
                        $queries[] = 'DROP DATABASE `' . $db_name . '`';
                    }
                }
            } catch (\Throwable $th) {
                log_message('error', $th->getMessage());
            }
        }
    }

    if ($return_queries) {
        return $queries;
    }

    if (!empty($queries)) {
        perfex_saas_raw_query($queries, [], false, false, null, true, false);
    }
}

function perfex_saas_module_loader_custom_hook($forward)
{

    $path = APPPATH . 'hooks/InitHook.php';
    $find = '$ci->app_modules->get_activated()';
    $replace = 'hooks()->apply_filters(\'modules_to_load\', ' . $find . ')';

    try {
        // Perform the modification based on the `$forward` parameter
        replace_in_file($path, $replace, $find);

        if ($forward) {
            replace_in_file($path, $find, $replace);
        }
    } catch (Exception $e) {
        throw new Exception('Error modifying InitHook for custom module loading control: ' . $e->getMessage());
    }
}

/**
 * Installs Perfex SAAS.
 *
 * The function setup saas for any perfex installation in a way that does not block/breack future updates
 * from perfex author by using custom files excluded in perfex updates.
 * The files are meant for customization by perfex. Exception to this is the DB driver hook. See: perfex_saas_db_driver_hook()
 * 
 * Rerunning the function after a module install or outrageous broken of SAAS after an update.
 * 
 * This function is responsible for installing Perfex SAAS. It performs the following actions:
 * - Runs the database setups for the master database.
 * - Requires the base config and middleware files and injects SAAS configurations.
 * - Requires the routes file and injects SAAS routes.
 * - Sets the driver hook for the database.
 * - Performs config constant replacements.
 *
 * @return void awokumadivad
 */
function perfex_saas_install()
{
    hooks()->do_action('perfex_saas_before_installer_run');

    $CI = &get_instance();
    require_once(__DIR__ . '/../install.php');

    // Trigger asset whitelabel path cache clearing to ensure new asset files get copied
    perfex_saas_asset_url('', true);

    // Run database setups
    perfex_saas_setup_master_db(true);

    // Require the base config
    perfex_saas_require_in_file(APPPATH . 'config/app-config.php',  "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/app-config.php'");

    // Require the SAAS routes and hooks
    perfex_saas_require_in_file(APPPATH . 'config/my_routes.php', "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/my_routes.php'");

    // Require the SAAS system level hooks
    perfex_saas_require_in_file(APPPATH . 'config/my_hooks.php', "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/my_hooks.php'");

    // Set driver hook
    perfex_saas_db_driver_hook(true);

    // DB Config constant replacements
    perfex_saas_db_config_constant_hook(true);

    // Config constants control
    perfex_saas_config_constants_hook(true);

    // Add Custom module loading control : @since v0.1.4
    perfex_saas_module_loader_custom_hook(true);

    hooks()->do_action('perfex_saas_after_installer_run');
}


/**
 * Uninstalls Perfex SAAS.
 *
 * This function is responsible for uninstalling Perfex SAAS. It performs the following actions:
 * - Unrequires the base config and middleware files.
 * - Unrequires the routes file and removes the injected SAAS routes.
 * - Disables the database driver hook for every query if multitenant option is enabled.
 * - Removes all config constant shadwowing.
 * - Optionally, removes all data in the current active table and destroys all company databases in development mode.
 *
 * @param bool $clean (Optional) Determines whether to perform a clean uninstall by removing all data. Defaults to false.
 * @return void awokumadivad
 */
function perfex_saas_uninstall($clean = false)
{
    hooks()->do_action('perfex_saas_before_uninstaller_run', $clean);

    // Remove the base config and middleware
    perfex_saas_unrequire_in_file(APPPATH . 'config/app-config.php', "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/app-config.php'");

    // Remove the SAAS routes and hooks
    perfex_saas_unrequire_in_file(APPPATH . 'config/my_routes.php', "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/my_routes.php'");

    // Remove the SAAS system level hooks
    perfex_saas_unrequire_in_file(APPPATH . 'config/my_hooks.php', "FCPATH.'modules/" . PERFEX_SAAS_MODULE_NAME . "/config/my_hooks.php'");

    // Now set driver hook, inject database hook to every query if multitenant option is enabled
    perfex_saas_db_driver_hook(false);

    // Remove all config constant replacements
    perfex_saas_db_config_constant_hook(false);

    // Remove config constants control
    perfex_saas_config_constants_hook(false);

    // Remove module loading control : @since v0.1.4
    perfex_saas_module_loader_custom_hook(false);

    if ($clean === true) {
        // Remove all data in the current active table and destroy all created company databases (if running super DB user)
        perfex_saas_setup_master_db(false);

        $saas_custom_config_file = APPPATH . 'config/my_saas_config.php';
        if (file_exists($saas_custom_config_file)) {
            unlink($saas_custom_config_file);
        }
    }

    hooks()->do_action('perfex_saas_after_uninstaller_run', $clean);
}