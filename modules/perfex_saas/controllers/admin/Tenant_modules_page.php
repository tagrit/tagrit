<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tenant_modules_page extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Modules are only accessible by administrators
         */
        if (
            !is_admin() ||
            !perfex_saas_is_tenant() ||
            !(int)perfex_saas_tenant_get_super_option('perfex_saas_enable_tenant_admin_modules_page')
        ) {
            redirect(admin_url());
        }
    }

    public function index()
    {
        $tenant = perfex_saas_tenant();
        $all_modules = $this->perfex_saas_model->modules();
        $tenant_modules = perfex_saas_tenant_modules($tenant, false, false, false, false, true);
        $disabled_modules = $this->disabled_modules();
        $disabled_modules = array_merge((array)(perfex_saas_tenant()->metadata->disabled_modules ?? []), $disabled_modules);

        foreach ($all_modules as $key => $module) {
            if (!in_array($module['system_name'], $tenant_modules)) {
                unset($all_modules[$key]);
                continue;
            }

            $disabled = in_array($module['system_name'], $disabled_modules);

            if (!isset($module['activated'])) {
                $module['activated'] = $disabled ? 0 : 1;
                $all_modules[$key]['activated'] = $module['activated'];
            }

            if ($module['activated'] === 1 && $disabled) {
                $all_modules[$key]['activated'] = 0;
            }
        }

        $data['modules'] = $all_modules;
        $data['title']   = _l('modules');
        $this->load->view('tenant_admin/modules/list', $data);
    }

    public function update($name, $action)
    {
        $tenant = perfex_saas_tenant();
        $tenant_modules = perfex_saas_tenant_modules($tenant, false, false, false, false, true);

        // Ensure its a known module
        if (!in_array($name, $tenant_modules)) return $this->to_modules();


        $disabled_modules = $this->disabled_modules();
        $dirty = false;

        if ($action === 'enable' && in_array($name, $disabled_modules)) {
            $disabled_modules = array_diff($disabled_modules, [$name]);
            $dirty = true;
        }

        if ($action === 'disable' && !in_array($name, $disabled_modules)) {
            $disabled_modules[] = $name;
            $dirty = true;
        }

        if ($dirty)
            update_option('tenant_local_disabled_modules', json_encode($disabled_modules));

        $this->to_modules();
    }

    private function disabled_modules()
    {
        $disabled_modules = get_option('tenant_local_disabled_modules');
        $disabled_modules = (array)json_decode($disabled_modules ?? '', true);
        return $disabled_modules;
    }

    private function to_modules()
    {
        redirect(admin_url('apps/modules'));
    }
}