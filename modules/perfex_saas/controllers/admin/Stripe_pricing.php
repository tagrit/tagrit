<?php defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_pricing extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('stripe_subscriptions');
        $this->load->model('subscriptions_model');
        $this->load->model('currencies_model');
        $this->load->model('taxes_model');
    }

    function index()
    {
        // Check for permission
        if (!staff_can('view', 'perfex_saas_packages')) {
            return access_denied('perfex_saas_packages');
        }

        $data['packages'] = [];
        $single_pricing_mode = perfex_saas_is_single_package_mode();

        if ($single_pricing_mode) {
            $default_package = $this->perfex_saas_model->default_package();
            if ($default_package)
                $data['packages'] = [$default_package];
        } else {
            $data['packages'] = $this->perfex_saas_model->packages();
        }

        if (empty($data['packages']))
            return redirect($single_pricing_mode ? admin_url(PERFEX_SAAS_ROUTE_NAME . '/pricing') : admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages'));

        $this->load->library('stripe_core');

        if (!empty($this->input->post())) {
            try {
                $this->savePackageStripePricing();
            } catch (\Throwable $th) {
                set_alert('danger', $th->getMessage());
            }
        }

        $stripe_plans = [];
        $stripe_tax_rates = [];

        try {
            $stripe_plans = $this->stripe_subscriptions->get_plans();
            $stripe_tax_rates = $this->stripe_core->get_tax_rates();
        } catch (\Throwable $th) {
            set_alert('danger', $th->getMessage());
        }
        $data['stripe_plans'] = $stripe_plans;
        $data['stripe_tax_rates'] = $stripe_tax_rates;
        $data['currencies'] = $this->currencies_model->get();
        $data['title'] = _l('perfex_saas_saas_pricing');
        $this->load->view('stripe/pricing', $data);
    }

    private function savePackageStripePricing()
    {
        $form_data = $this->input->post('metadata', true);
        $package_id = (int)$this->input->post('id', true);
        if (empty($form_data) || empty($package_id)) return false;

        // Ensure manual pricing ids are unique as required for subscription
        $checked_price_ids = [];
        foreach ($form_data['stripe']['manual_pricing'] as $key => $value) {
            if (empty($value)) continue;
            if (in_array($value, $checked_price_ids))
                throw new \Exception(_l('perfex_saas_stripe_unique_manual_price'), 1);
            $checked_price_ids[] = $value;
        }

        $package = $this->perfex_saas_model->packages($package_id);
        if (empty($package->id)) return false;

        $currency_id = $form_data['stripe']['currency'] ?? get_base_currency()->id;
        $currency = get_currency($currency_id);

        $form_data['stripe']['currency'] = $currency->name;
        if (!isset($form_data['stripe']['sync']))
            $form_data['stripe']['sync'] = '0';

        if (!isset($form_data['stripe']['enabled']))
            $form_data['stripe']['enabled'] = '0';

        $metadata = (array)$package->metadata;
        $metadata['stripe'] = array_merge((array)($metadata['stripe'] ?? []), $form_data['stripe']);
        $data = ['id' => $package->id, 'metadata' => json_encode($metadata)];

        // Create or update the package
        $_id = $this->perfex_saas_model->add_or_update('packages', $data);
        if ($_id) {

            try {
                $package = $this->perfex_saas_model->packages($package_id);
                $this->perfex_saas_stripe_model->setup_package_on_stripe($package);

                perfex_saas_trigger_cron_process(PERFEX_SAAS_CRON_PROCESS_PACKAGE, $_id);
                set_alert('success', _l('updated_successfully', _l('perfex_saas_package')));
            } catch (\Throwable $th) {
                set_alert('danger', $th->getMessage());
            }

            return redirect(uri_string() . '?tab=' . $package->slug);
        }

        return false;
    }
}
