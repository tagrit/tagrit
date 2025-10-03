<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Proxy\Http\Request;
use Proxy\Proxy;
use Proxy\Config;


class Landing extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to server the active landing page theme
     *
     * @return void
     */
    public function index()
    {
        $this->check_for_redirection();

        return redirect(base_url('authentication/login'));
    }

    /**
     * Method to serve the proxied landing page.
     * Its essensial the proxied adddress runs on same domain to prevent CORS or whitelabeled for this installation domain.
     *
     * @return void
     */
    public function proxy()
    {

        $this->check_for_redirection();

        $url = get_option('perfex_saas_landing_page_url');

        if ($url && $url !== base_url()) {
            if (get_option('perfex_saas_landing_page_url_mode') === 'redirection') {
                redirect($url);
            }
        }

        //Config::set('url_mode', 2);
        //Config::set('encryption_key', md5(session_id()));

        session_write_close();

        $proxy = new Proxy();

        $proxy->getEventDispatcher()->addListener('request.sent', function ($event) {

            if ($event['response']->getStatusCode() != 200) {
                show_error("Bad status code!", $event['response']->getStatusCode(), "Landing");
            }
        });

        // load plugins
        $plugins = [
            'HeaderRewrite',
            'Stream',
            'Cookie',
            //'Proxify',
        ];
        foreach ($plugins as $plugin) {

            $plugin_class = $plugin . 'Plugin';

            if (class_exists('\\Proxy\\Plugin\\' . $plugin_class)) {

                // does the native plugin from php-proxy package with such name exist?
                $plugin_class = '\\Proxy\\Plugin\\' . $plugin_class;
            }

            $proxy->addSubscriber(new $plugin_class());
        }

        $request = Request::createFromGlobals();
        $request->get->clear();

        if (isset($_GET['q'])) {
            $url = url_decrypt($_GET['q']);
        }

        $response = $proxy->forward($request, $url);

        // send the response back to the client
        $response->send();
    }

    public function show_404()
    {
        // ensure not servable by proxy, then server 404
        show_404();
    }

    /**
     * Check if there is an active session and redirect to the dashboard if loggedin.
     *
     * @return void
     */
    private function check_for_redirection()
    {
        if (get_option('perfex_saas_force_redirect_to_dashboard') == "1") {
            if (is_client_logged_in()) {
                return redirect('clients');
            }

            if (is_staff_logged_in()) {
                return redirect('admin');
            }
        }
    }
}
