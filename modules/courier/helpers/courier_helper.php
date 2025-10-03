<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('load_courier_styles')) {
    function load_courier_styles()
    {
        echo '<link rel="stylesheet" href="' . base_url('modules/courier/assets/main.css') . '">';
    }
}

if (!function_exists('load_courier_scripts')) {
    function load_courier_scripts()
    {
        echo '<script src="' . base_url('modules/courier/assets/create_shipment.js') . '"></script>';
        echo '<script src="' . base_url('modules/courier/assets/main.js') . '"></script>';
    }
}





