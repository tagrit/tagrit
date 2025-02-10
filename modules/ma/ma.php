<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Marketing Automation
Description: This module helps you to identify potential customers, automating the process of nurturing those leads to sales-readiness.
Version: 1.0.9
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions

*/


define('MA_MODULE_NAME', 'ma');
define('MA_MODULE_UPLOAD_FOLDER', module_dir_path(MA_MODULE_NAME, 'uploads'));

hooks()->add_action('admin_init', 'ma_module_init_menu_items');
hooks()->add_action('admin_init', 'ma_permissions');
hooks()->add_action('app_admin_head', 'ma_add_head_components');
hooks()->add_action('app_admin_footer', 'ma_add_footer_components');
hooks()->add_action('after_cron_run', 'ma_run_campaign');
hooks()->add_action('after_lead_lead_tabs', 'ma_add_lead_tab');
hooks()->add_action('after_lead_tabs_content', 'ma_add_lead_tabs_content');
hooks()->add_action('ma_init',MA_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', MA_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', MA_MODULE_NAME.'_predeactivate');

define('MA_REVISION', 109);

$CI = &get_instance();

$CI->load->helper(MA_MODULE_NAME . '/Ma');

/**
* Register activation module hook
*/
register_activation_hook(MA_MODULE_NAME, 'ma_module_activation_hook');

/**
* Functions of the module
*/
function ma_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, 'admin/ma/') === false)) {
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/css/custom.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, 'admin/ma/campaign_detail') === false)) {
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/docs/drawflow.min.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/docs/beautiful.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, 'admin/ma/workflow_builder') === false)) {
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/docs/drawflow.min.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/docs/beautiful.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, 'admin/ma/email_template_design') === false)) {
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/react-email-editor-master/src/style.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, 'admin/ma/email_design') === false)) {
        echo '<link href="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/react-email-editor-master/src/style.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
}

function ma_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, 'admin/') === false)) {
        if(get_option('ma_lead_required_phone') == 1){
            echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/ma_main.js') . '?v=' . MA_REVISION . '"></script>';
        }
    }

    if (!(strpos($viewuri, 'admin/ma/settings?group=category') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/settings/category.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/stages') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/segment') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/segments/segment.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/segments') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }
    
    if (!(strpos($viewuri, 'admin/ma/segment_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/components?group=forms') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/components/form.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/components?group=assets') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/components/asset_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/asset') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/components/asset.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/points?group=point_actions') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/points/point_actions_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/points?group=point_triggers') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/points/point_triggers_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/marketing_message') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/marketing_message.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/email.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/channels?group=marketing_messages') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/marketing_messages_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/settings?group=ma_email_templates') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/email_template_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/settings?group=text_messages') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/text_messages_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/text_message') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/text_message.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/sms') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/sms.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/campaign') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/campaigns/campaign.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/channels?group=emails') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/email_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/channels?group=sms') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/channels/sms_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }


    if (!(strpos($viewuri, 'admin/ma/sms_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/campaigns') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/campaign_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/workflow_builder') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email_template') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/emails/email_template.js') . '?v=' . MA_REVISION . '"></script>';
    }


    if (!(strpos($viewuri, 'admin/ma/email_template_design') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/react-email-editor-master/src/loadScript.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email_template_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/asset_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/point_action_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/point_action') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/points/point_action.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/dashboard') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/stage_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/text_message_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/asset_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/lead_and_point_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/form_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/sms_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/campaign_report') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email_detail') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/email_design') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/plugins/react-email-editor-master/src/loadScript.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/settings?group=email_configuration') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/settings/email_configuration.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/ma/settings?group=email_sending_limit') === false)) {
        echo '<script src="' . module_dir_url(MA_MODULE_NAME, 'assets/js/settings/email_sending_limit.js') . '?v=' . MA_REVISION . '"></script>';
    }
}


function ma_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(MA_MODULE_NAME, [MA_MODULE_NAME]);

/**
 * Init ma module menu items in setup in admin_init hook
 * @return null
 */
function ma_module_init_menu_items()
{
    if (has_permission('ma_dashboard', '', 'view') || has_permission('ma_segments', '', 'view') || has_permission('ma_components', '', 'view') || has_permission('ma_campaigns', '', 'view') || has_permission('ma_channels', '', 'view') || has_permission('ma_points', '', 'view') || has_permission('ma_stages', '', 'view') || has_permission('ma_reports', '', 'view') || has_permission('ma_setting', '', 'view') || has_permission('ma_segments', '', 'view_own') || has_permission('ma_components', '', 'view_own') || has_permission('ma_campaigns', '', 'view_own') || has_permission('ma_channels', '', 'view_own') || has_permission('ma_points', '', 'view_own') || has_permission('ma_stages', '', 'view_own')) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('ma', [
                'name'     => _l('marketing_automation'),
                'href'     => admin_url('ma'),
                'icon'     => 'fa fa-hashtag',
                'position' => 30
        ]);

        if (has_permission('ma_dashboard', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-dashboard',
                'name' => _l('als_dashboard'),
                'icon' => 'fa fa-home',
                'href' => admin_url('ma/dashboard'),
                'position' => 1,
            ]);
        }

        if (has_permission('ma_segments', '', 'view') || has_permission('ma_segments', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-segments',
                'name' => _l('segments'),
                'icon' => 'fa fa-object-group',
                'href' => admin_url('ma/segments'),
                'position' => 2,
            ]);

        }

        if (has_permission('ma_components', '', 'view') || has_permission('ma_components', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-components',
                'name' => _l('components'),
                'icon' => 'fa fa-cubes',
                'href' => admin_url('ma/components?group=assets'),
                'position' => 3,
            ]);
        }

        if (has_permission('ma_campaigns', '', 'view') || has_permission('ma_campaigns', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-campaigns',
                'name' => _l('campaigns'),
                'icon' => 'fa fa-code-fork',
                'href' => admin_url('ma/campaigns'),
                'position' => 4,
            ]);
        }

        if (has_permission('ma_channels', '', 'view') || has_permission('ma_channels', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-channels',
                'name' => _l('channels'),
                'icon' => 'fa fa-rss-square',
                'href' => admin_url('ma/channels?group=emails'),
                'position' => 5,
            ]);
        }

        if (has_permission('ma_points', '', 'view') || has_permission('ma_points', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-points',
                'name' => _l('points'),
                'icon' => 'fa fa-dot-circle',
                'href' => admin_url('ma/points?group=point_actions'),
                'position' => 5,
            ]);
        }

        if (has_permission('ma_stages', '', 'view') || has_permission('ma_stages', '', 'view_own')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-stages',
                'name' => _l('stages'),
                'icon' => 'fa fa-filter',
                'href' => admin_url('ma/stages'),
                'position' => 7,
            ]);
        }

        if (has_permission('ma_reports', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-reports',
                'name' => _l('reports'),
                'icon' => 'fa fa-area-chart',
                'href' => admin_url('ma/reports'),
                'position' => 8,
            ]);
        }

        if (has_permission('ma_setting', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('ma', [
                'slug' => 'ma-settings',
                'name' => _l('settings'),
                'icon' => 'fa fa-cogs',
                'href' => admin_url('ma/settings?group=general_settings'),
                'position' => 8,
            ]);
        }
    }
}

/**
 * run campaign
 *  
 */
function ma_run_campaign($manually)
{
        $CI = &get_instance();

        $CI->load->model('ma/ma_model');
        $CI->ma_model->ma_cron_campaign();
        $CI->ma_model->ma_cron_email_limit();
        $CI->ma_model->ma_cron_test_campaign();
}


/**
 * Init ma module permissions in setup in admin_init hook
 */
function ma_permissions() {

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('permission_view'),
    ];
    register_staff_capabilities('ma_dashboard', $capabilities, _l('ma_dashboard'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_segments', $capabilities, _l('ma_segment'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_components', $capabilities, _l('ma_component'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_campaigns', $capabilities, _l('ma_campaign'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_channels', $capabilities, _l('ma_channel'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_points', $capabilities, _l('ma_point'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_stages', $capabilities, _l('ma_stage'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('permission_view'),
    ];
    register_staff_capabilities('ma_report', $capabilities, _l('ma_report'));

    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('ma_setting', $capabilities, _l('ma_setting'));
}

/**
 * add lead tab
 *  
 */
function ma_add_lead_tab($lead)
{   
    $html = '';
    if(isset($lead)){
        $html = '<li role="presentation">
                <a href="#lead_campaigns" aria-controls="lead_campaigns" role="tab" data-toggle="tab">
                '. _l('campaigns').'
                </a>
             </li>';
    }
    
    echo html_entity_decode($html);
}

/**
 * run campaign
 *  
 */
function ma_add_lead_tabs_content($lead)
{
    if(isset($lead)){
        $CI = &get_instance();

        $CI->load->model('ma/ma_model');
        $campaigns = $CI->ma_model->get_campaigns_by_lead($lead->id);

        echo $CI->load->view('ma/leads/campaigns_tab', ['campaigns' => $campaigns, 'lead' => $lead]);
    }
}

function ma_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $ma_api = new MarketingAutomationLic();
    $ma_gtssres = $ma_api->verify_license(true);    
    if(!$ma_gtssres || ($ma_gtssres && isset($ma_gtssres['status']) && !$ma_gtssres['status'])){
         $CI->app_modules->deactivate(MA_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function ma_preactivate($module_name){
    if ($module_name['system_name'] == MA_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $ma_api = new MarketingAutomationLic();
        $ma_gtssres = $ma_api->verify_license();          
        if(!$ma_gtssres || ($ma_gtssres && isset($ma_gtssres['status']) && !$ma_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.MA_MODULE_NAME); 
            $data['module_name'] = MA_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function ma_predeactivate($module_name){
    if ($module_name['system_name'] == MA_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $ma_api = new MarketingAutomationLic();
        $ma_api->deactivate_license();
    }
}

