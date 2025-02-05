<?php

defined('BASEPATH') or exit('No direct script access allowed');

/********OTHER SPECIFIC HOOKS ******/
$folder_path = __DIR__ . '/integrations/';
$feature_hook_files = glob($folder_path . '*.php');
foreach ($feature_hook_files as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}
