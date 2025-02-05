<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Saas_app_modules extends App_modules
{
    /**
     * @inheritDoc
     */
    public function deactivate($name)
    {
        if (!perfex_saas_is_tenant()) {
            return parent::deactivate($name);
        }

        $caller_func = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? null;
        if ($caller_func === 'perfex_saas_setup_modules_for_tenant') {
            return parent::deactivate($name);
        }

        return true;
    }
}