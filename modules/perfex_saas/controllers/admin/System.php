<?php defined('BASEPATH') or exit('No direct script access allowed');

use app\services\zip\Unzip;

class System extends AdminController
{
    public $returnUrl;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('app_module_installer');
        $this->load->library('app_modules');

        $this->returnUrl = admin_url(PERFEX_SAAS_ROUTE_NAME . '/system');
    }

    /**
     * Display index page
     */
    public function index()
    {
        // Check for permission
        if (!staff_can('view', 'perfex_saas_settings')) {
            return access_denied('perfex_saas_settings');
        }

        $purchase_code = get_option('perfex_saas_purchase_code');

        // Show list of comapnies
        $data['title'] = _l('perfex_saas_update_ext');
        $data['purchase_code'] = $purchase_code;
        $data['saas_module'] = $this->app_modules->get(PERFEX_SAAS_MODULE_NAME);
        $data['remote_modules'] = new stdClass();

        $url = perfex_saas_get_system_update_url($purchase_code, 2);
        $request = (object)perfex_saas_http_request($url, []);
        if (!empty($request->error)) {
            set_alert('danger', $request->error);
        } else {
            $response = (object)json_decode($request->response ?? '');
            $data['remote_modules'] = $response->modules ?? $data['remote_modules'];

            $status = $this->input->get('status', true);
            $message = $this->input->get('message', true);

            if (empty($message) && !empty($response->message)) {
                $message = $response->message;
                $status = 'danger';
            }

            if (!empty($message) && !empty($status)) {
                set_alert($status, $message);
            }
        }

        $data['remote_modules'] = (object)hooks()->apply_filters('perfex_saas_system_remote_extensions', $data['remote_modules']);

        $this->load->view('settings/system', $data);
    }


    public function save_purchase_code()
    {
        if ($this->input->post()) {
            $status = 'danger';
            $message = '';

            try {
                $purchase_code = $this->input->post('purchase_code', true);
                if (empty($purchase_code))
                    throw new \Exception(_l("perfex_saas_invalid_purchase_code"), 1);

                $purchase_code = trim($purchase_code);

                // Validate code on server
                $url = perfex_saas_get_system_update_url($purchase_code);
                $request = (object)perfex_saas_http_request($url, []);
                if (!empty($request->error))
                    throw new \Exception($request->error, 1);

                $response = (object)json_decode($request->response);

                $purchase = (object)($response->purchase ?? []);

                if (empty($purchase) || empty($purchase->buyer)) {

                    if (!empty($response->message))
                        throw new \Exception($response->message, 1);

                    throw new \Exception(_l("perfex_saas_error_fetching_purchase_code_details"), 1);
                }

                // Save purchase code in cache
                update_option('perfex_saas_purchase_code', $purchase_code);

                $status = 'success';
                $message = empty($response->message) ? _l('updated_successfully', _l('perfex_saas_purchase_code')) : $response->message;
            } catch (\Throwable $th) {
                $message = $th->getMessage();
                $purchase_code = '';
                update_option('perfex_saas_purchase_code', $purchase_code);
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => $status, 'message' => $message, 'purchase_code' => $purchase_code]);
                exit;
            }

            set_alert($status, $message);

            return perfex_saas_redirect_back();
        }
    }

    public function get_module($module)
    {
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', 360);

        $response = ['success' => false, 'error' => ''];

        try {
            $moduleTemporaryDir = get_temp_dir() . time() . '/';
            if (!is_dir($moduleTemporaryDir))
                mkdir($moduleTemporaryDir, 0777, true);

            $purchase_code = get_option('perfex_saas_purchase_code');
            if (empty($purchase_code))
                throw new \Exception(_l("perfex_saas_invalid_purchase_code"), 1);

            // Usage
            $defaultRemoteFileUrl = perfex_saas_get_system_update_url($purchase_code, 3, $module); // Replace with the actual remote file URL

            // Allow customizing the module source url.
            $remoteFileUrl = hooks()->apply_filters('perfex_saas_system_remote_extension_url', ['url' => $defaultRemoteFileUrl, 'purchase_code' => $purchase_code, 'module' => $module]);
            if (!is_string($remoteFileUrl))
                $remoteFileUrl = $remoteFileUrl['url'] ?? '';

            if (empty($remoteFileUrl))
                $remoteFileUrl = $defaultRemoteFileUrl;


            $downloadedModuleFile = $moduleTemporaryDir . $module . '.zip'; // Set the name for the downloaded file

            if (file_exists($downloadedModuleFile))
                unlink($downloadedModuleFile);

            // Initialize CURL session for HEAD request
            $ch = curl_init($remoteFileUrl);

            // Set CURL options for HEAD request
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            // Execute CURL session for HEAD request
            curl_exec($ch);

            // Capture the response code and content type
            $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            // Close CURL session for HEAD request
            curl_close($ch);

            if ($responseCode === 200 && strpos($contentType, 'application/zip') !== false) {
                // It's a file download response

                // Initialize CURL session for GET request to download the file
                $ch = curl_init($remoteFileUrl);

                // Set CURL options for GET request to download the file
                $fp = fopen($downloadedModuleFile, 'w');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, false);

                // Execute CURL session for GET request to download the file
                curl_exec($ch);

                // Close CURL session and file pointer
                curl_close($ch);
                fclose($fp);


                try {

                    $unzip = new Unzip();

                    $unzip->extract($downloadedModuleFile, $moduleTemporaryDir);

                    if ($this->app_module_installer->check_module($moduleTemporaryDir) === false) {
                        $response['message'] = _l('perfex_saas_module_not_found');
                    } else {
                        $unzip->extract($downloadedModuleFile, APP_MODULES_PATH);
                        $response['success'] = true;
                    }

                    delete_files($moduleTemporaryDir);
                    perfex_saas_remove_dir($moduleTemporaryDir);

                    $response['message'] = _l('perfex_saas_module_installed_successfully');
                } catch (Exception $e) {
                    $response['message'] = $e->getMessage();
                }
            } else {
                // It's not a file download response, directly output JSON text
                $_response = perfex_saas_http_request($remoteFileUrl, []);
                $_response = json_decode($_response['response'], true) ?? [];
                $response = ['success' => false, 'message' => $_response['message'] ?? 'Unkown error'];
            }
        } catch (\Throwable $th) {
            $response = ['success' => false, 'message' => $th->getMessage()];
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
            exit;
        }

        $message = $response['message'];
        $status = $response['success'] ? 'success' : 'danger';

        set_alert($status, $message);

        return redirect(admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/after_get_module/' . $module) . "?status=$status&message=$message");
    }

    public function after_get_module($module)
    {
        $status = $this->input->get('status', true);
        $message = $this->input->get('message', true);

        try {
            if ($status !== 'danger') {
                $this->app_modules->upgrade_database($module);
                if ($this->app_modules->is_active($module)) {
                    $this->app_modules->activate($module);
                }

                $this->perfex_saas_cron_model->check_saas_latest_version();
            }
            set_alert($status, $message);
        } catch (\Throwable $th) {
            log_message('error', $th->getTraceAsString());
        }

        return redirect($this->returnUrl . "?status=$status&message=$message");
    }

    public function activate($module_name)
    {
        $this->app_modules->activate($module_name);
        $this->app_modules->upgrade_database($module_name);
        return redirect($this->returnUrl);
    }

    public function deactivate($module_name)
    {
        $this->app_modules->deactivate($module_name);
        return redirect($this->returnUrl);
    }
}