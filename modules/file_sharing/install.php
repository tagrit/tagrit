<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'fs_setting_configuration')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_setting_configuration` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `type` varchar(20) NULL,
      `is_read` int(1) NOT NULL DEFAULT 0,
      `is_upload` int(1) NOT NULL DEFAULT 0,
      `is_download` int(1) NOT NULL DEFAULT 0,
      `is_delete` int(1) NOT NULL DEFAULT 0,
      `is_write` int(1) NOT NULL DEFAULT 0,
      `password` varchar(255) NULL,
      `max_size` int(255) NOT NULL DEFAULT 2, 
      `min_size` int(255) NOT NULL DEFAULT 1,
      `created_at` varchar(11) NULL,
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'fs_genenal_ip_share')) {
     $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_genenal_ip_share` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `parrent_id` varchar(11) NOT NULL,
      `ip_share` varchar(20) NULL,
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'fs_file_overview')) {
     $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_file_overview` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `fileid` varchar(255) NOT NULL,
      `type` varchar(20) NULL,
      `path` TEXT NULL,
      `number` int(255) NOT NULL DEFAULT 0,
      `created_at` varchar(11) NULL,
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('hash', db_prefix() .'fs_file_overview')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_file_overview` 
        ADD COLUMN `hash` varchar(250) NOT NULL DEFAULT ""
        ;');            
}

if (!$CI->db->field_exists('dir', db_prefix() .'fs_file_overview')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_file_overview` 
        ADD COLUMN `dir` INT(1) NOT NULL DEFAULT 0
        ;');            
}

if (!$CI->db->field_exists('hash_share', db_prefix() .'fs_file_overview')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_file_overview`
        ADD COLUMN `hash_share` VARCHAR(45) NULL
        ;');            
}

add_option('fs_global_max_size', 2);
add_option('fs_global_extension', ".jpg,.png,.pdf,.doc,.zip,.rar");
add_option('fs_global_amount_expiration', 3);
add_option('fs_global_notification', 1);
add_option('fs_global_email', 0);
add_option('fs_client_visible', 0);

add_option('fs_permisstion_staff_view', 1);
add_option('fs_permisstion_staff_upload_and_override', 1);
add_option('fs_permisstion_staff_delete', 1);
add_option('fs_permisstion_staff_upload', 1);
add_option('fs_permisstion_staff_download', 1);
add_option('fs_permisstion_staff_share', 1);
add_option('fs_permisstion_client_view', 1);
add_option('fs_permisstion_client_upload_and_override', 1);
add_option('fs_permisstion_client_delete', 1);
add_option('fs_permisstion_client_upload', 1);
add_option('fs_permisstion_client_download', 1);
add_option('file_sharing_security', '?3HTtb?HgTA@%7zm');
add_option('fs_the_administrator_of_the_public_folder');

if (!$CI->db->table_exists(db_prefix() . 'fs_sharings')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_sharings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `is_read` INT(1) NOT NULL DEFAULT '0',
      `is_upload` INT(1) NOT NULL DEFAULT '0',
      `is_download` INT(1) NOT NULL DEFAULT '0',
      `is_delete` INT(1) NOT NULL DEFAULT '0',
      `is_write` INT(1) NOT NULL DEFAULT '0',
      `password` VARCHAR(255) NULL,
      `expiration_date_apply` INT(1) NULL,
      `expiration_date` DATE NULL,
      `expiration_date_delete` INT(1) NULL,
      `download_limits_apply` INT(1) NULL,
      `download_limits` INT(11) NULL,
      `download_limits_delete` INT(1) NULL,
      `hash_share` VARCHAR(255) NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('created_at', db_prefix() .'fs_sharings')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_sharings` 
      ADD COLUMN `created_at` INT(11) NOT NULL,
      ADD COLUMN `type` VARCHAR(45) NOT NULL,
      ADD COLUMN `inserted_at` DATETIME NOT NULL,
      ADD COLUMN `updated_at` DATETIME NULL;');            
}

if (!$CI->db->field_exists('isowner', db_prefix() .'fs_sharings')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_sharings` 
      ADD COLUMN `isowner` TINYINT NULL,
      ADD COLUMN `phash` TEXT NULL,
      ADD COLUMN `locked` TINYINT NULL,
      ADD COLUMN `mime` VARCHAR(255) NULL,
      ADD COLUMN `name` VARCHAR(255) NULL,
      ADD COLUMN `read` TINYINT NULL,
      ADD COLUMN `size` VARCHAR(255) NULL,
      ADD COLUMN `ts` VARCHAR(255) NULL,
      ADD COLUMN `write` TINYINT NULL;');            
}

if (!$CI->db->table_exists(db_prefix() . 'fs_setting_configuration_relationship')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_setting_configuration_relationship` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `configuration_id` INT(11) NOT NULL,
      `rel_type` VARCHAR(45) NOT NULL,
      `rel_id` INT(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'fs_sharing_relationship')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_sharing_relationship` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `share_id` INT(11) NOT NULL,
      `type` VARCHAR(45) NOT NULL,
      `value` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('hash', db_prefix() .'fs_sharings')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_sharings` 
      ADD COLUMN `hash` TEXT NULL;');            
}

if (!$CI->db->field_exists('url', db_prefix() .'fs_sharings')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_sharings` 
      ADD COLUMN `url` TEXT NULL;');            
}

if (!$CI->db->field_exists('is_share', db_prefix() .'fs_setting_configuration')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_setting_configuration` 
      ADD COLUMN `is_share` int(1) NOT NULL DEFAULT 0;');            
}

if (!$CI->db->field_exists('has_been_deleted', db_prefix() .'fs_sharings')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_sharings` 
      ADD COLUMN `has_been_deleted` INT(1) NOT NULL DEFAULT 0,
      ADD COLUMN `downloads` INT(11) NOT NULL DEFAULT 0;');            
}

if (!$CI->db->table_exists(db_prefix() . 'fs_downloads')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "fs_downloads` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `hash_share` VARCHAR(255) NOT NULL,
      `time` DATETIME NOT NULL,
      `ip` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('browser_name', db_prefix() .'fs_downloads')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'fs_downloads` 
      ADD COLUMN `browser_name` VARCHAR(45) NULL,
      ADD COLUMN `http_user_agent` TEXT NULL;');            
}

// Create mail teamplate module
create_email_template('File Sharing', '<span></span><br /><br /><span>Have files that have just been shared with you: {share_link}.</span>', 'file_sharing', 'Shared to staff', 'fs-share-staff');

create_email_template('File Sharing', '<span></span><br /><br /><span>Have files that have just been shared with you: {share_link}.</span>', 'file_sharing', 'Shared to client', 'fs-share-client');

create_email_template('File Sharing', '<span></span><br /><br /><span>Have files that have just been shared with you: {share_link}.</span>', 'file_sharing', 'Shared to public', 'fs-share-public');

add_option('fs_allow_file_editing', 1);
add_option('fs_hidden_files', '.tmb,index.html');

add_option('fs_permisstion_staff_share_to_client', 1);
