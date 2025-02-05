<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PerfexSaasTest
{

    public $CI;
    public function __construct($CI)
    {
        $this->CI = $CI;
        $this->CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME);
        $this->CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_core');
        $this->CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_setup');
        $this->CI->load->helper(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_import');

        $this->CI->load->library('unit_test');
        $this->CI->unit->use_strict(TRUE);
    }

    function customTestNote($result, $expected_result)
    {

        $expected_result = is_array($expected_result) || is_object($expected_result) || is_bool($expected_result) ? json_encode($expected_result) : (string)$expected_result;
        $result = is_array($result) || is_object($result) || is_bool($result) ? json_encode($result) : (string)$result;
        $color = ($expected_result === $result)  ? 'green' : 'red';
        return '<div style="color:' . $color . ';">Expect: ' . $expected_result . ' <br/>Got: ' . $result . "</div>";;
    }
}
