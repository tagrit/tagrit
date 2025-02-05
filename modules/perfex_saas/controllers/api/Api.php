<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Main.php';

use PerfexSaaSApi\Main;

class Api extends \ClientsController
{
    use Main;

    public function __construct()
    {
        parent::__construct();

        defined('PERFEX_SAAS_API') or define('PERFEX_SAAS_API', true);
    }

    public function docs($method = '')
    {
        // Ensure we have admin login
        if (!is_admin()) {
            if (get_option('perfex_saas_api_allow_public_access_to_doc') != '1') {
                redirect_after_login_to_current_url();
                return redirect(admin_url('authentication'));
            }
        }

        $openapi = perfex_saas_api_openapi_instance();

        if ($method == 'json' || $method == 'yaml') {
            header('Content-Type: application/json');
            echo $openapi->{$method == 'yaml' ? 'toYaml' : 'toJson'}();
            exit;
        }

        $spec = $openapi->toJson();

        $apiKey = is_logged_in() && is_admin() ? get_option('perfex_saas_api_key') : '';
        $title = get_option('companyname') . ' - SaaS API Documentation';
        echo '
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <meta name="description" content="' . $title . '" />
                <title>' . $title . '</title>
                <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
            </head>

            <body>
                <div id="swagger-ui"></div>
                <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
                <script>
                window.onload = () => {
                    window.ui = SwaggerUIBundle({
                        spec: ' . $spec . ',
                        dom_id: "#swagger-ui",
                        onComplete: function() {
                            // Default API key
                            let apiKey = "' . $apiKey . '";
                            if(apiKey.length)
                                ui.preauthorizeApiKey("api_key", apiKey);
                        }
                    });
                };
                </script>
            </body>

        </html>';
    }
}