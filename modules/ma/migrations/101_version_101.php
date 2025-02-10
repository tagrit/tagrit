<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();
        
        add_option('ma_smtp_type', 'system_default_smtp');
        add_option('ma_mail_engine', 'phpmailer');
        add_option('ma_email_protocol', 'smtp');
        add_option('ma_smtp_encryption');
        add_option('ma_smtp_host');
        add_option('ma_smtp_port');
        add_option('ma_smtp_email');
        add_option('ma_smtp_username');
        add_option('ma_smtp_password');
        add_option('ma_smtp_email_charset');
        add_option('ma_bcc_emails');

        if (!$CI->db->field_exists('ma_unsubscribed' ,db_prefix() . 'leads')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'leads`
                ADD COLUMN `ma_unsubscribed` INT(11) NOT NULL DEFAULT 0');
        }

        if (!$CI->db->field_exists('ma_unsubscribed' ,db_prefix() . 'clients')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients`
                ADD COLUMN `ma_unsubscribed` INT(11) NOT NULL DEFAULT 0');
        }

        if (!$CI->db->field_exists('addedfrom' ,db_prefix() . 'ma_forms')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_forms`
                ADD COLUMN `addedfrom` INT(11) NOT NULL DEFAULT 0');
        }

        add_option('ma_form_style');
        add_option('ma_unsubscribe_text');

        if (!$CI->db->field_exists('submit_action' ,db_prefix() . 'ma_forms')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_forms`
                ADD COLUMN `submit_action` INT(11) NOT NULL DEFAULT 0,
                ADD COLUMN `submit_redirect_url` TEXT NULL;');
        }
     }
}
