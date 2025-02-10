<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'ma_categories')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_categories (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `type` TEXT NULL,
      `published` INT(11) NOT NULL DEFAULT 1,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_stages')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_stages (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `weight` TEXT NULL,
      `category` INT(255) NULL,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_segments')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_segments (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `category` INT(255) NULL,
      `public_segment` INT(255) NOT NULL DEFAULT 1,
      `published` INT(11) NOT NULL DEFAULT 1,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_segment_filters')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_segment_filters (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
      `segment_id` INT(255) NULL,
	  `type` TEXT NULL,
	  `sub_type_1` TEXT NULL,
	  `sub_type_2` TEXT NULL,
	  `value` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_forms')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_forms (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `form_key` VARCHAR(32) NOT NULL,
      `lead_source` INT(11) NOT NULL,
      `lead_status` INT(11) NOT NULL,
      `notify_lead_imported` INT(11) NOT NULL,
      `notify_type` VARCHAR(20) NULL,
      `notify_ids` MEDIUMTEXT NULL,
      `responsible` INT(11) NOT NULL DEFAULT 0,
      `name` VARCHAR(191) NOT NULL,
      `form_data` MEDIUMTEXT NULL,
      `recaptcha` INT(11) NOT NULL DEFAULT 0,
      `submit_btn_name` VARCHAR(40) NULL,
      `success_submit_msg` TEXT NULL,
      `language` VARCHAR(40) NULL,
      `allow_duplicate` INT(11) NOT NULL DEFAULT 1,
      `mark_public` INT(11) NOT NULL DEFAULT 0,
      `track_duplicate_field` VARCHAR(20) NULL,
      `track_duplicate_field_and` VARCHAR(20) NULL,
      `create_task_on_duplicate` INT(11) NOT NULL DEFAULT 0,
      `dateadded` DATETIME NOT NULL,
      `addedfrom` INT(11) NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('from_ma_form_id' ,db_prefix() . 'leads')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'leads`
  ADD COLUMN `from_ma_form_id` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_assets')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_assets (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `language` VARCHAR(40) NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_point_actions')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_point_actions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `change_points` FLOAT(11) NOT NULL,
      `action` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_point_triggers')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_point_triggers (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `minimum_number_of_points` FLOAT(11) NOT NULL,
      `contact_color` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_marketing_messages')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_marketing_messages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `type` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `email_template` INT(11) NOT NULL DEFAULT 0,
      `web_notification_description` TEXT NULL,
      `web_notification_link` TEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_emails')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_emails (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `type` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `segment` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_text_messages')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_text_messages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('color' ,db_prefix() . 'ma_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segments`
  ADD COLUMN `color` TEXT NULL');
}

if (!$CI->db->field_exists('color' ,db_prefix() . 'ma_stages')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_stages`
  ADD COLUMN `color` TEXT NULL');
}

if (!$CI->db->field_exists('color' ,db_prefix() . 'ma_categories')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_categories`
  ADD COLUMN `color` TEXT NULL');
}

if (!$CI->db->field_exists('color' ,db_prefix() . 'ma_emails')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_emails`
  ADD COLUMN `color` TEXT NULL');
}

if (!$CI->db->field_exists('color' ,db_prefix() . 'ma_assets')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_assets`
  ADD COLUMN `color` TEXT NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaigns')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaigns (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `color` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `start_date` DATE NULL,
      `end_date` DATE NULL,
      `workflow` LONGTEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('published' ,db_prefix() . 'ma_stages')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_stages`
  ADD COLUMN `published` INT(11) NOT NULL DEFAULT 1');
}

if (!$CI->db->field_exists('addedfrom' ,db_prefix() . 'ma_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segments`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}

if (!$CI->db->field_exists('addedfrom' ,db_prefix() . 'ma_stages')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_stages`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}


if (!$CI->db->table_exists(db_prefix() . 'ma_email_templates')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_templates (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `color` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` TEXT NULL,
      `data_html` LONGTEXT NULL,
      `data_design` LONGTEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_flows')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_flows (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `campaign_id` INT(11) NOT NULL,
      `node_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_lead_segments')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_lead_segments (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `segment_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_lead_stages')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_lead_stages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `stage_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_email_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `email_template_id` INT(11) NULL,
      `delivery` INT(11) NOT NULL DEFAULT 0,
      `open` INT(11) NOT NULL DEFAULT 0,
      `click` INT(11) NOT NULL DEFAULT 0,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('addedfrom' ,db_prefix() . 'ma_categories')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_categories`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}

if (!$CI->db->field_exists('deleted' ,db_prefix() . 'ma_lead_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_segments`
        ADD COLUMN `deleted` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `date_delete` DATETIME NULL');
}

if (!$CI->db->field_exists('campaign_id' ,db_prefix() . 'ma_lead_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_segments`
        ADD COLUMN `campaign_id` INT(11) NULL');
}

if (!$CI->db->field_exists('deleted' ,db_prefix() . 'ma_lead_stages')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_stages`
        ADD COLUMN `deleted` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `date_delete` DATETIME NULL');
}

if (!$CI->db->field_exists('campaign_id' ,db_prefix() . 'ma_lead_stages')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_stages`
        ADD COLUMN `campaign_id` INT(11) NULL');
}

if (!$CI->db->field_exists('ma_point' ,db_prefix() . 'leads')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'leads`
        ADD COLUMN `ma_point` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->field_exists('delivery_time' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
          ADD COLUMN `delivery_time` DATETIME NULL,
          ADD COLUMN `open_time` DATETIME NULL,
          ADD COLUMN `click_time` DATETIME NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_sms_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_sms_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `text_message_id` INT(11) NULL,
      `delivery` INT(11) NOT NULL DEFAULT 0,
      `delivery_time` DATETIME NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_asset_download_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_asset_download_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `asset_id` INT(11) NOT NULL,
      `ip` TEXT NULL,
      `browser_name` TEXT NULL,
      `http_user_agent` TEXT NULL,
      `time` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_point_action_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_point_action_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `point_action_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('output' ,db_prefix() . 'ma_campaign_flows')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaign_flows`
        ADD COLUMN `output` TEXT NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_lead_exceptions')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_lead_exceptions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('subject' ,db_prefix() . 'ma_emails')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_emails`
        ADD COLUMN `subject` TEXT NULL');
}

if (!$CI->db->field_exists('email_template' ,db_prefix() . 'ma_emails')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_emails`
          ADD COLUMN `email_template` INT(11) NULL,
          ADD COLUMN `from_name` TEXT NULL,
          ADD COLUMN `from_address` TEXT NULL,
          ADD COLUMN `reply_to_address` TEXT NULL,
          ADD COLUMN `bcc_address` TEXT NULL,
          ADD COLUMN `attachment` TEXT NULL,
          ADD COLUMN `data_design` LONGTEXT NULL,
          ADD COLUMN `data_html` LONGTEXT NULL');
}

if (!$CI->db->field_exists('email_id' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
        ADD COLUMN `email_id` INT(11) NULL');
}


if (!$CI->db->table_exists(db_prefix() . 'ma_sms')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_sms (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `sms_template` INT(11) NOT NULL DEFAULT 0,
      `color` TEXT NULL,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `content` TEXT NULL,
      `description` LONGTEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('sms_id' ,db_prefix() . 'ma_sms_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_sms_logs`
        ADD COLUMN `sms_id` INT(11) NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_email_click_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_click_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `email_id` INT(11) NULL,
      `url` TEXT NULL,
      `time` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

add_option('ma_insert_email_template_default', 0);

if(get_option('ma_insert_email_template_default') == 0){

    $CI->db->query(file_get_contents(FCPATH . 'modules/ma/database/email_template_default.sql'));
    update_option('ma_insert_email_template_default', 1);
}

if (!$CI->db->field_exists('failed' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
          ADD COLUMN `failed` INT(11) NOT NULL DEFAULT 0,
          ADD COLUMN `failed_time` DATETIME NULL');
}

if (!$CI->db->field_exists('point' ,db_prefix() . 'ma_point_action_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_point_action_logs`
        ADD COLUMN `point` FLOAT(11) NULL');
}

if (!$CI->db->field_exists('hash' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
          ADD COLUMN `hash` TEXT NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_email_designs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `email_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `data_design` LONGTEXT NULL,
      `data_html` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_email_template_designs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_email_template_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `email_template_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `data_design` LONGTEXT NULL,
      `data_html` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_sms_designs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_sms_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `sms_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `content` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_sms_template_designs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_sms_template_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `sms_template_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `content` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('add_points_by_country' ,db_prefix() . 'ma_point_actions')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_point_actions`
        ADD COLUMN `add_points_by_country` INT(11) NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_point_action_details')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_point_action_details (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `point_action_id` INT(11) NOT NULL,
      `country` INT(11) NOT NULL,
      `change_points` FLOAT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_point_action_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_point_action_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_sms_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_sms_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_campaign_flows')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaign_flows`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_email_click_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_click_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_client_exceptions')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_client_exceptions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `client_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_asset_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_asset_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `asset_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `client_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `hash` TEXT NULL,
      `download` INT(11) NOT NULL DEFAULT 0,
      `download_time` DATETIME NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('change_points' ,db_prefix() . 'ma_assets')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_assets`
  ADD COLUMN `change_points` TEXT NULL');
}

if (!$CI->db->field_exists('asset_log_id' ,db_prefix() . 'ma_asset_download_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_asset_download_logs`
        ADD COLUMN `asset_log_id` INT(11) NULL');
}

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

add_option('convert_email_template_v104', 0);
if (get_option('convert_email_template_v104') == 0) {

    $CI->load->model('ma/ma_model');
    $CI->ma_model->convert_email_template_v104();

    update_option('convert_email_template_v104', 1);
}

add_option('ma_lead_required_phone', 0);
add_option('ma_unsubscribe', 1);
add_option('ma_email_sending_limit', 0);
add_option('ma_email_limit', 100);
add_option('ma_email_interval', 60);
add_option('ma_email_repeat_every', 'minutes');

if (!$CI->db->field_exists('bcc_address' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
        ADD COLUMN `bcc_address` INT(11) NOT NULL DEFAULT 0');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_test')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_test (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `email` TEXT NULL,
      `campaign_id` INT(255) NULL,
      `segment_id` INT(255) NULL,
      `stage_id` INT(255) NULL,
      `point` INT(255) NOT NULL DEFAULT 0,
      `tags` TEXT NULL,
      `status` INT(255) NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ma_campaign_test_logs')) {
    $CI->db->query('CREATE TABLE ' . db_prefix() . "ma_campaign_test_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `campaign_id` INT(11) NOT NULL,
      `node_id` INT(11) NOT NULL,
      `output` TEXT NULL,
      `result` TEXT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('delete_lead' ,db_prefix() . 'ma_campaign_test')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaign_test`
        ADD COLUMN `delete_lead` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `remove_from_campaign` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `convert_to_customer` INT(11) NOT NULL DEFAULT 0');
}

add_option('ma_modify_workflow_column', 0);

if(get_option('ma_modify_workflow_column') == 0){
    if (!$CI->db->field_exists('workflow' ,db_prefix() . 'ma_campaigns')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_campaigns`
            MODIFY COLUMN `workflow` LONGTEXT NULL');
    }

    update_option('ma_modify_workflow_column', 1);
}

if (!$CI->db->field_exists('email' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
        ADD COLUMN `email` TEXT NULL');
}

if (!$CI->db->field_exists('filter_type' ,db_prefix() . 'ma_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segments`
        ADD COLUMN `filter_type` TEXT NULL');
}

if (!$CI->db->field_exists('customer_type' ,db_prefix() . 'ma_segment_filters')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_segment_filters`
        ADD COLUMN `customer_type` TEXT NULL');
}

if (!$CI->db->field_exists('client_id' ,db_prefix() . 'ma_lead_segments')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_lead_segments`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$CI->db->field_exists('confirm' ,db_prefix() . 'ma_email_logs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ma_email_logs`
        ADD COLUMN `confirm` TINYINT(1) NOT NULL DEFAULT 0');
}

add_option('ma_unlayer_custom_fonts', '');