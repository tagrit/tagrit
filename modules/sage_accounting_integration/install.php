<?php

add_option('acc_integration_sage_accounting_active', 0);
add_option('acc_integration_sage_accounting_sync_from_system', 0);
add_option('acc_integration_sage_accounting_sync_to_system', 0);
add_option('acc_integration_sage_accounting_client_id', '');
add_option('acc_integration_sage_accounting_client_secret', '');
add_option('acc_integration_sage_accounting_access_token', '');
add_option('acc_integration_sage_accounting_access_token_expires', '');
add_option('acc_integration_sage_accounting_refresh_token', '');
add_option('acc_integration_sage_accounting_refresh_token_expires', '');

if (!$CI->db->table_exists(db_prefix() . 'acc_integration_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "acc_integration_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_type` VARCHAR(50) NOT NULL,
      `rel_id` INT(11) NOT NULL,
      `software` VARCHAR(50) NOT NULL,
      `connect_id` VARCHAR(50) NOT NULL,
      `date_updated` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_integration_error_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "acc_integration_error_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_type` VARCHAR(50) NOT NULL,
      `rel_id` INT(11) NOT NULL,
      `software` VARCHAR(50) NOT NULL,
      `error_detail` TEXT NULL,
      `date_updated` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'acc_integration_sync_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "acc_integration_sync_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `rel_type` VARCHAR(50) NOT NULL,
      `rel_id` INT(11) NOT NULL,
      `software` VARCHAR(50) NOT NULL,
      `type` TEXT NULL,
      `status` TINYINT(1) NOT NULL DEFAULT 0,
      `connect_id` TEXT NULL,
      `datecreated` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}