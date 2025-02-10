<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Accounting and Bookkeeping
Description: Accounting is the process of recording and tracking financial statements to see the financial health of an entity.
Version: 1.3.4
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
 */

define('ACCOUNTING_MODULE_NAME', 'accounting');
define('ACCOUTING_MODULE_UPLOAD_FOLDER', module_dir_path(ACCOUNTING_MODULE_NAME, 'uploads'));
define('ACCOUTING_IMPORT_ITEM_ERROR', 'modules/accounting/uploads/import_item_error/');
define('ACCOUTING_ERROR', FCPATH);
define('ACCOUTING_EXPORT_XLSX', 'modules/accounting/uploads/export_xlsx/');
define('ACCOUTING_PATH', 'modules/accounting/uploads/');

hooks()->add_action('app_admin_head', 'accounting_add_head_component');
hooks()->add_action('app_admin_footer', 'accounting_load_js');
hooks()->add_action('admin_init', 'accounting_module_init_menu_items');
hooks()->add_action('admin_init', 'accounting_permissions');

// invoice
hooks()->add_action('after_invoice_added', 'acc_automatic_invoice_conversion');
hooks()->add_action('invoice_updated', 'acc_automatic_invoice_conversion');
hooks()->add_action('before_invoice_deleted', 'acc_delete_invoice_convert');
hooks()->add_action('invoice_status_changed', 'acc_invoice_status_changed');

// payment
hooks()->add_action('after_payment_added', 'acc_automatic_payment_conversion');
hooks()->add_action('after_payment_updated', 'acc_automatic_payment_conversion');
hooks()->add_action('before_payment_deleted', 'acc_delete_payment_convert');

// expense
hooks()->add_action('after_expense_added', 'acc_automatic_expense_conversion');
hooks()->add_action('after_recurring_expense_added', 'acc_automatic_expense_conversion');
hooks()->add_action('expense_updated', 'acc_automatic_expense_conversion');
hooks()->add_action('after_expense_deleted', 'acc_delete_expense_convert');

// credit note
hooks()->add_filter('credits_applied', 'acc_automatic_credit_note_conversion');
hooks()->add_filter('after_applied_credit_deleted', 'acc_delete_applied_credit_convert');
hooks()->add_filter('credit_note_refund_created', 'acc_automatic_credit_note_refund_conversion');
hooks()->add_filter('credit_note_refund_updated', 'acc_automatic_credit_note_refund_conversion');
hooks()->add_filter('credit_note_refund_deleted', 'acc_delete_credit_note_refund_convert');
hooks()->add_filter('before_credit_note_deleted', 'acc_delete_credit_note_convert');

// payslip
hooks()->add_action('before_payslip_deleted', 'acc_delete_payslip_convert');

// inventory
hooks()->add_action('after_wh_goods_receipt_added', 'acc_automatic_wh_goods_receipt_convert');
hooks()->add_action('after_wh_goods_receipt_updated', 'acc_automatic_wh_goods_receipt_convert');
hooks()->add_action('after_wh_goods_receipt_approve', 'acc_automatic_wh_goods_receipt_convert');
hooks()->add_action('before_goods_receipt_deleted', 'acc_delete_stock_import_convert');

hooks()->add_action('after_wh_goods_delivery_added', 'acc_automatic_wh_goods_delivery_convert');
hooks()->add_action('after_wh_goods_delivery_updated', 'acc_automatic_wh_goods_delivery_convert');
hooks()->add_action('after_wh_goods_delivery_approve', 'acc_automatic_wh_goods_delivery_convert');
hooks()->add_action('before_goods_delivery_deleted', 'acc_delete_stock_export_convert');

hooks()->add_action('after_wh_loss_adjustment_added', 'acc_automatic_wh_loss_adjustment_convert');
hooks()->add_action('after_wh_loss_adjustment_updated', 'acc_automatic_wh_loss_adjustment_convert');
hooks()->add_action('after_wh_loss_adjustment_approve', 'acc_automatic_wh_loss_adjustment_convert');
hooks()->add_action('before_loss_adjustment_deleted', 'acc_delete_loss_adjustment_convert');

hooks()->add_action('after_receiving_or_exporting_return_order_approved', 'exporting_return_order_approved');

// purchase
hooks()->add_action('after_purchase_order_add', 'acc_automatic_pur_order_convert');
hooks()->add_action('after_purchase_order_approve', 'acc_automatic_pur_order_convert');
hooks()->add_action('before_pur_order_deleted', 'acc_delete_pur_order_convert');
hooks()->add_action('pur_after_expense_converted', 'acc_delete_expense_convert');

hooks()->add_action('after_payment_pur_invoice_added', 'acc_automatic_pur_invoice_payment_convert');
hooks()->add_action('after_purchase_payment_approve', 'acc_automatic_pur_invoice_payment_convert');
hooks()->add_action('after_payment_pur_invoice_deleted', 'acc_delete_pur_invoice_payment_convert');


hooks()->add_action('after_pur_invoice_added', 'acc_automatic_pur_invoice_convert');
hooks()->add_action('after_pur_invoice_updated', 'acc_automatic_pur_invoice_convert');
hooks()->add_action('after_pur_invoice_deleted', 'acc_delete_pur_invoice_convert');
hooks()->add_action('after_purchase_invoice_approve', 'acc_automatic_pur_invoice_convert');

hooks()->add_action('after_pur_refund_added', 'acc_automatic_pur_refund_convert');
hooks()->add_action('after_pur_refund_updated', 'acc_automatic_pur_refund_convert');
hooks()->add_action('after_pur_refund_deleted', 'acc_delete_pur_refund_convert');

hooks()->add_action('after_pur_return_order_status_changed', 'acc_automatic_pur_order_return_convert');
hooks()->add_action('before_pur_order_return_deleted', 'acc_delete_pur_order_return_convert');

hooks()->add_action('after_pur_vendor_created', 'acc_pur_vendor_created');
hooks()->add_action('before_pur_vendor_updated', 'acc_pur_vendor_updated', 10, 2);
hooks()->add_action('after_pur_vendor_profile_company_field', 'acc_init_pur_vendor_profile');

// manufacturing
hooks()->add_action('manufacturing_order_status_changed', 'acc_automatic_manufacturing_order_conversion');
hooks()->add_action('after_manufacturing_order_deleted', 'acc_delete_manufacturing_order_convert');
hooks()->add_action('after_manufacturing_goods_delivery_added', 'acc_automatic_wh_goods_delivery_convert');

// omni sales
hooks()->add_action('after_omni_sales_order_status_changed', 'acc_automatic_omni_sales_return_order_conversion');
hooks()->add_action('after_omni_sales_order_deleted', 'acc_delete_omni_sales_order_convert');

hooks()->add_action('after_omni_sales_refund_added', 'acc_automatic_omni_sales_refund_convert');
hooks()->add_action('after_omni_sales_refund_updated', 'acc_automatic_omni_sales_refund_convert');
hooks()->add_action('after_omni_sales_refund_deleted', 'acc_delete_omni_sales_refund_convert');

// fixed equipment
hooks()->add_action('after_fe_asset_added', 'acc_automatic_fe_asset_convert');
hooks()->add_action('after_fe_asset_updated', 'acc_automatic_fe_asset_convert');
hooks()->add_action('after_fe_asset_updated_v2', 'acc_automatic_fe_asset_convert');
hooks()->add_action('after_fe_asset_deleted', 'acc_delete_fe_asset_convert');

hooks()->add_action('after_fe_license_added', 'acc_automatic_fe_license_convert');
hooks()->add_action('after_fe_license_updated', 'acc_automatic_fe_license_convert');
hooks()->add_action('after_fe_license_deleted', 'acc_delete_fe_license_convert');

hooks()->add_action('after_fe_consumable_added', 'acc_automatic_fe_consumable_convert');
hooks()->add_action('after_fe_consumable_updated', 'acc_automatic_fe_consumable_convert');

hooks()->add_action('after_fe_component_added', 'acc_automatic_fe_component_convert');
hooks()->add_action('after_fe_component_updated', 'acc_automatic_fe_component_convert');

// fixed equipment
hooks()->add_action('after_fe_maintenance_added', 'acc_automatic_fe_maintenance_convert');
hooks()->add_action('after_fe_maintenance_updated', 'acc_automatic_fe_maintenance_convert');
hooks()->add_action('after_fe_maintenance_deleted', 'acc_delete_fe_maintenance_convert');
hooks()->add_action('after_fe_depreciation_added', 'acc_automatic_fe_depreciation_convert');


// customer
hooks()->add_action('before_client_added', 'acc_before_client_added');
hooks()->add_action('after_client_created', 'acc_client_created');
hooks()->add_action('before_client_updated', 'acc_client_updated',10,2);
hooks()->add_action('after_customer_profile_company_field', 'acc_init_client_profile');

//get currency
hooks()->add_action('after_cron_run', 'acc_cronjob_currency_rates');
hooks()->add_action('accounting_init',ACCOUNTING_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', ACCOUNTING_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', ACCOUNTING_MODULE_NAME.'_predeactivate');
// vendor

define('ACCOUNTING_REVISION', 134);

/**
 * Register activation module hook
 */

register_activation_hook(ACCOUNTING_MODULE_NAME, 'accounting_module_activation_hook');

$CI = &get_instance();

$CI->load->helper(ACCOUNTING_MODULE_NAME . '/Accounting');

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(ACCOUNTING_MODULE_NAME, [ACCOUNTING_MODULE_NAME]);

/**
 * spreadsheet online module activation hook
 */
function accounting_module_activation_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/install.php';
}

/**
 * init add head component
 */
function accounting_add_head_component() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, 'admin/accounting') === false)) {
			$out_style = '<style>
	    @font-face {
	      font-family: MicrFont;
	      src: url(\''.site_url("/modules/accounting/assets/plugins/micr-encoding/micrenc.ttf").'\')  format(\'truetype\')
	    }
	    </style>';

	    echo $out_style;

		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/custom.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';

	}
	if (!(strpos($viewuri, 'admin/accounting/new_journal_entry') === false)) {

		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/rp_') === false) || !(strpos($viewuri, 'admin/accounting/report') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/report.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/treegrid/css/jquery.treegrid.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/accounts_import') === false) || !(strpos($viewuri, 'admin/accounting/report') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/chart_of_accounts') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/chart_of_accounts.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, 'admin/accounting/reconcile') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/reconcile.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/reconcile_account') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/reconcile_account.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/transaction.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/import_xlsx_banking') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/dashboard') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/dashboard.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, 'admin/accounting/setting') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/setting.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/new_journal_entry') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/new_journal_entry.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/journal_entry') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/manage_journal_entry.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, 'admin/accounting/budget') === false) || !(strpos($viewuri, 'admin/accounting/user_register_view') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/box_loading.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';

	}

	if (!(strpos($viewuri, 'admin/accounting/budget_import') === false)) {
		echo '<link href="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/css/import_budget.css') . '?v=' . ACCOUNTING_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
}

/**
 * init add footer component
 */
function accounting_load_js() {
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	$mediaLocale = get_media_locale();

	if (!(strpos($viewuri, 'admin/accounting') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/tinymce_init.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=banking_register') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/banking_register.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=posted_bank_transactions') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/posted_bank_transactions.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=reconcile_bank_account&bank_account=') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/reconcile_bank_account_detail.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=reconcile_bank_account') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/reconcile_bank_account.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=banking_feeds') === false)) {
		echo '<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/plaid_new_transaction.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/banking?group=plaid_new_transaction') === false)) {
		echo '<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/banking/plaid_new_transaction.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=banking') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/banking.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=sales') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/sales.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=expenses') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/expenses.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=payslips') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/payslips.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=purchase') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/purchase_order.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=warehouse') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/warehouse.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=stock_export') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/stock_export.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/setting?group=general') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/general.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/setting?group=mapping_setup') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/automatic_conversion.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/setting?group=banking_rules') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/banking_rules.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/setting?group=account_type_details') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/account_type_details.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/new_rule') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/new_rule.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/journal_entry') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/journal_entry/manage.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}
	if (!(strpos($viewuri, 'admin/accounting/new_journal_entry') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/reconcile') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/reconcile/reconcile.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if(!(strpos($viewuri,'admin/accounting/rp_') === false) || !(strpos($viewuri,'admin/accounting/report') === false)){
        echo '<script src="'.module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/treegrid/js/jquery.treegrid.min.js').'?v=' . ACCOUNTING_REVISION.'"></script>';
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/report/jspdf.min.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/report/html2pdf.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/report/tableHTMLExport.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/report/main.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
    }

	if (!(strpos($viewuri, '/admin/accounting/dashboard') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/budget') === false) || !(strpos($viewuri, 'admin/accounting/user_register_view') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=manufacturing') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/manufacturing.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/transaction?group=omni_sales') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/omni_sales.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if(!(strpos($viewuri, 'admin/accounting/checks') === false) || !(strpos($viewuri, 'admin/accounting/check') === false)){
        echo '<script src="' . base_url('assets/plugins/signature-pad/signature_pad.min.js') . '"></script>';
    }

	if (!(strpos($viewuri, 'admin/accounting/configure_checks') === false)) {
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/bill/configure_checks.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, 'admin/accounting/transaction?group=fixed_equipment') === false)) {
		echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/transaction/fixed_equipment.js') . '?v=' . ACCOUNTING_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, 'admin/accounting/vendors') === false)) {
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/vendors/vendor_manage.js') .'?v=' . ACCOUNTING_REVISION.'"></script>';
    }

    if (!(strpos($viewuri, 'admin/accounting/setting?group=income_statement_modification') === false)) {
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/income_statement_modification.js') .'?v=' . ACCOUNTING_REVISION.'"></script>';
    }

    if(!(strpos($viewuri, '/admin/accounting/setting?group=currency_rates') === false)){
        echo '<script src="' . module_dir_url(ACCOUNTING_MODULE_NAME, 'assets/js/setting/currency_rate.js') .'?v=' . ACCOUNTING_REVISION.'"></script>';
    }
}

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function accounting_module_init_menu_items() {
	$CI = &get_instance();

	if (has_permission('accounting_dashboard', '', 'view') || has_permission('accounting_transaction', '', 'view') || has_permission('accounting_journal_entry', '', 'view') || has_permission('accounting_transfer', '', 'view') || has_permission('accounting_chart_of_accounts', '', 'view') || has_permission('accounting_reconcile', '', 'view') || has_permission('accounting_report', '', 'view') || has_permission('accounting_setting', '', 'view')) {
		$CI->app_menu->add_sidebar_menu_item('accounting', [
			'name' => _l('als_accounting'),
			'icon' => 'fa fa-usd',
			'position' => 5,
		]);

		if (has_permission('accounting_dashboard', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_dashboard',
				'name' => _l('dashboard'),
				'icon' => 'fa fa-home',
				'href' => admin_url('accounting/dashboard'),
				'position' => 1,
			]);
		}

		if (has_permission('accounting_banking', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_banking',
				'name' => _l('banking'),
				'icon' => 'fa fa-university',
				'href' => admin_url('accounting/banking?group=bank_accounts'),
				'position' => 2,
			]);
		}

		if (has_permission('accounting_transaction', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_transaction',
				'name' => _l('transaction'),
				'icon' => 'fa fa-file',
				'href' => admin_url('accounting/transaction?group=sales'),
				'position' => 2,
			]);
		}

		if (has_permission('accounting_registers', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_registers',
				'name' => _l('registers'),
				'icon' => 'fa fa-list',
				'href' => admin_url('accounting/registers'),
				'position' => 2,
			]);
		}

		if (has_permission('accounting_bills', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_bills',
				'name' => _l('bills'),
				'icon' => 'fa fa-book',
				'href' => admin_url('accounting/bills'),
				'position' => 3,
			]);
		}

		// Checks Menu
		if (has_permission('accounting_checks', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_checks',
				'name' => _l('acc_checks'),
				'icon' => 'fa fa-book',
				'href' => admin_url('accounting/checks'),
				'position' => 3,
			]);
		}


		if (has_permission('accounting_journal_entry', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_journal_entry',
				'name' => _l('journal_entry'),
				'icon' => 'fa fa-book',
				'href' => admin_url('accounting/journal_entry'),
				'position' => 3,
			]);
		}

		if (has_permission('accounting_transfer', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_transfer',
				'name' => _l('transfer'),
				'icon' => 'fa fa-exchange',
				'href' => admin_url('accounting/transfer'),
				'position' => 4,
			]);
		}

		if (has_permission('accounting_chart_of_accounts', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_chart_of_accounts',
				'name' => _l('chart_of_accounts'),
				'icon' => 'fa fa-list-ol',
				'href' => admin_url('accounting/chart_of_accounts'),
				'position' => 5,
			]);
		}

		if (has_permission('accounting_reconcile', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_reconcile',
				'name' => _l('reconcile'),
				'icon' => 'fa fa-sliders',
				'href' => admin_url('accounting/reconcile'),
				'position' => 6,
			]);
		}

		if (has_permission('accounting_budget', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_budget',
				'name' => _l('budget'),
				'icon' => 'fa fa-exchange',
				'href' => admin_url('accounting/budget'),
				'position' => 7,
			]);
		}

		if(!acc_get_status_modules('purchase')){
			if (has_permission('accounting_vendor', '', 'view')) {
				$CI->app_menu->add_sidebar_children_item('accounting', [
					'slug' => 'accounting_vendor',
					'name' => _l('vendors'),
					'icon' => 'fa fa-users',
					'href' => admin_url('accounting/vendors'),
					'position' => 8,
				]);
			}
        }

		if (has_permission('accounting_report', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_report',
				'name' => _l('reports'),
				'icon' => 'fa fa-area-chart',
				'href' => admin_url('accounting/report'),
				'position' => 8,
			]);
		}

		if (has_permission('accounting_setting', '', 'view')) {
			$CI->app_menu->add_sidebar_children_item('accounting', [
				'slug' => 'accounting_setting',
				'name' => _l('setting'),
				'icon' => 'fa fa-cog',
				'href' => admin_url('accounting/setting?group=general'),
				'position' => 9,
			]);
		}
	}
}

/**
 * Init accounting module permissions in setup in admin_init hook
 */
function accounting_permissions() {

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
	];
	register_staff_capabilities('accounting_dashboard', $capabilities, _l('accounting_dashboard'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_banking', $capabilities, _l('accounting_banking'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_transaction', $capabilities, _l('accounting_transaction'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_registers', $capabilities, _l('accounting_registers'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
	];
	register_staff_capabilities('accounting_bills', $capabilities, _l('accounting_bills'));

	// Checks Permission
	register_staff_capabilities('accounting_checks', $capabilities, _l('acc_checks'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_journal_entry', $capabilities, _l('accounting_journal_entry'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_transfer', $capabilities, _l('accounting_transfer'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_chart_of_accounts', $capabilities, _l('accounting_chart_of_accounts'));
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
	];
	register_staff_capabilities('accounting_reconcile', $capabilities, _l('accounting_reconcile'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_budget', $capabilities, _l('accounting_budget'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];
	register_staff_capabilities('accounting_vendor', $capabilities, _l('accounting_vendors'));


	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
	];
	register_staff_capabilities('accounting_report', $capabilities, _l('accounting_report'));

	$capabilities = [];
	$capabilities['capabilities'] = [
		'view' => _l('permission_view'),
		'edit' => _l('permission_edit'),
	];
	register_staff_capabilities('accounting_setting', $capabilities, _l('accounting_setting'));
}

function acc_automatic_invoice_conversion($data) {
	if ($data) {
		if (get_option('acc_invoice_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			if(isset($data['id'])){
				$CI->accounting_model->automatic_invoice_conversion($data['id']);
			}else{
				$CI->accounting_model->automatic_invoice_conversion($data);
			}
		}

	}

	return $data;
}

function acc_automatic_payment_conversion($data) {
	if ($data) {
		if (get_option('acc_payment_automatic_conversion') == 1 || get_option('acc_active_payment_mode_mapping') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			if(isset($data['id'])){
				$CI->accounting_model->automatic_payment_conversion($data['id']);
			}else{
				$CI->accounting_model->automatic_payment_conversion($data);
			}
		}

	}

	return $data;
}

function acc_automatic_expense_conversion($data) {
	if ($data) {
		if (get_option('acc_expense_automatic_conversion') == 1 || get_option('acc_active_expense_category_mapping') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			if(isset($data['id'])){
				$CI->accounting_model->automatic_expense_conversion($data['id']);
			}else{
				$CI->accounting_model->automatic_expense_conversion($data);
			}
		}

	}
	return $data;
}

function acc_delete_invoice_convert($invoice_id) {
	if ($invoice_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_invoice_convert($invoice_id);

	}

	return $invoice_id;
}

function acc_delete_payment_convert($data) {
	if ($data['paymentid']) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($data['paymentid'], 'payment');
	}

	return $data;
}

function acc_delete_expense_convert($expense_id) {
	if ($expense_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($expense_id, 'expense');
	}

	return $expense_id;
}

function acc_invoice_status_changed($data) {
	$CI = &get_instance();
	$CI->load->model('accounting/accounting_model');

	$CI->accounting_model->invoice_status_changed($data);

	return $data;
}

function acc_delete_pur_order_convert($pur_order_id) {
	if ($pur_order_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($pur_order_id, 'purchase_order');
	}

	return $pur_order_id;
}

function acc_delete_payslip_convert($payslip_id) {
	if ($payslip_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($payslip_id, 'payslip');
	}

	return $payslip_id;
}

function acc_delete_stock_export_convert($goods_delivery_id) {
	if ($goods_delivery_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($goods_delivery_id, 'stock_export');
	}

	return $goods_delivery_id;
}

function acc_delete_stock_import_convert($goods_receipt_id) {
	if ($goods_receipt_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($goods_receipt_id, 'stock_import');
	}

	return $goods_receipt_id;
}

function acc_delete_loss_adjustment_convert($loss_adjustment_id) {
	if ($loss_adjustment_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($loss_adjustment_id, 'loss_adjustment');
	}

	return $loss_adjustment_id;
}


function acc_automatic_pur_invoice_payment_convert($id) {
	if ($id) {
		if (get_option('acc_pur_payment_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_purchase_payment_conversion($id);
		}

	}
	return $id;
}

function acc_delete_pur_invoice_payment_convert($pur_invoice_payment_id) {
	if ($pur_invoice_payment_id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($pur_invoice_payment_id, 'purchase_payment');
	}

	return $pur_invoice_payment_id;
}

function acc_automatic_credit_note_conversion($data) {
	if (get_option('acc_credit_note_automatic_conversion') == 1) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->automatic_credit_note_conversion($data);
	}

	return $data;
}

function acc_delete_applied_credit_convert($data) {
	if ($data['id']) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($data['id'], 'credit_note');
	}

	return $data;
}

function acc_delete_credit_note_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');
		$CI->load->model('credit_notes_model');
		
		$credit_notes = $CI->credit_notes_model->get($id);

		foreach($credit_notes->refunds as $refund){
			$CI->accounting_model->delete_convert($refund['id'], 'credit_note_refund');
		}

		foreach($credit_notes->applied_credits as $applied_credit){
			$CI->accounting_model->delete_convert($applied_credit['id'], 'credit_note');
		}
	}

	return $id;
}

function acc_automatic_credit_note_refund_conversion($data) {
	if (get_option('acc_credit_note_refund_automatic_conversion') == 1) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->automatic_credit_note_refund_conversion($data);
	}

	return $data;
}

function acc_delete_credit_note_refund_convert($data) {
	if ($data['refund_id']) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($data['refund_id'], 'credit_note_refund');
	}

	return $data;
}

function acc_automatic_manufacturing_order_conversion($data) {

	if(isset($data['data']['status'])){
		if ($data['data']['status'] == 'done') {
			if (get_option('acc_mrp_manufacturing_order_automatic_conversion') == 1) {
				$CI = &get_instance();
				$CI->load->model('accounting/accounting_model');

				$CI->accounting_model->automatic_manufacturing_order_conversion($data['id']);
			}
		}else{
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			$CI->accounting_model->delete_convert($data['id'], 'manufacturing_order');
		}
	}

	return $data;
}


function acc_automatic_omni_sales_return_order_conversion($data) {
	if($data['data_order']->original_order_id != ''){
		if ($data['data']['status'] == '5') {
			if (get_option('acc_omni_sales_order_return_automatic_conversion') == 1) {
				$CI = &get_instance();
				$CI->load->model('accounting/accounting_model');
				$CI->accounting_model->automatic_omni_sales_return_order_conversion($data['data_order']->id);
			}
		}else{
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			$CI->accounting_model->delete_convert($data['data_order']->id, 'sales_return_order');
		}
	}

	return $data;
}



function acc_delete_manufacturing_order_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'manufacturing_order');
	}

	return $id;
}

function acc_automatic_pur_order_return_convert($data) {
	if ($data['status'] == 'finish') {
		if (get_option('acc_mrp_manufacturing_order_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');

			$CI->accounting_model->automatic_purchase_order_return_conversion($data['id']);
		}
	}else{
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($data['id'], 'purchase_order_return');
	}

	return $data;
}

function acc_delete_pur_order_return_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'purchase_order_return');
		$CI->accounting_model->delete_pur_refund_convert_by_order($id);

	}

	return $data;
}


function acc_automatic_pur_refund_convert($id) {
	if ($id) {
		if (get_option('acc_pur_refund_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_purchase_refund_conversion($id);
		}

	}
	return $id;
}

function acc_delete_pur_refund_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'purchase_refund');
	}

	return $data;
}


function acc_automatic_pur_order_convert($id) {
	if ($id) {
		if (get_option('acc_pur_order_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_purchase_order_conversion($id);
		}

	}
	return $id;
}


function acc_automatic_pur_invoice_convert($id) {
	if ($id) {
		if (get_option('acc_pur_invoice_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_purchase_invoice_conversion($id);
		}

	}
	return $id;
}


function acc_delete_pur_invoice_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'purchase_invoice');
	}

	return $data;
}


function acc_delete_omni_sales_order_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'sales_return_order');
	}

	return $id;
}


function acc_automatic_omni_sales_refund_convert($id) {
	if ($id) {
		if (get_option('acc_omni_sales_refund_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_omni_sales_refund_conversion($id);
		}

	}
	return $id;
}

function acc_delete_omni_sales_refund_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'sales_refund');
	}

	return $id;
}


function acc_automatic_wh_goods_receipt_convert($id) {
	if ($id) {
		if (get_option('acc_wh_stock_import_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_stock_import_conversion($id);
		}

	}
	return $id;
}


function acc_automatic_wh_goods_delivery_convert($id) {
	if ($id) {
		if (get_option('acc_wh_stock_export_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_stock_export_conversion($id);
		}

	}
	return $id;
}


function acc_automatic_wh_loss_adjustment_convert($id) {
	if ($id) {
		if (get_option('acc_wh_loss_adjustment_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_loss_adjustment_conversion($id);
		}

	}
	return $id;
}


function acc_automatic_fe_asset_convert($id) {
	if ($id) {
		if (get_option('acc_fe_asset_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_asset_conversion($id);
		}

	}
	return $id;
}

function acc_delete_fe_asset_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'fe_asset');
		$CI->accounting_model->delete_convert($id, 'fe_component');
		$CI->accounting_model->delete_convert($id, 'fe_consumable');

		$CI->accounting_model->delete_depreciation_convert_by_asset($id);
	}

	return $id;
}

function acc_automatic_fe_license_convert($id) {
	if ($id) {
		if (get_option('acc_fe_license_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_license_conversion($id);
		}

	}
	return $id;
}

function acc_delete_fe_license_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'fe_license');
	}

	return $id;
}

function acc_automatic_fe_component_convert($id) {
	if ($id) {
		if (get_option('acc_fe_component_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_component_conversion($id);
		}

	}
	return $id;
}

function acc_automatic_fe_consumable_convert($id) {
	if ($id) {
		if (get_option('acc_fe_consumable_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_consumable_conversion($id);
		}

	}
	return $id;
}

function acc_automatic_fe_maintenance_convert($id) {
	if ($id) {
		if (get_option('acc_fe_maintenance_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_maintenance_conversion($id);
		}

	}
	return $id;
}

function acc_automatic_fe_depreciation_convert($id) {
	if ($id) {
		if (get_option('acc_fe_depreciation_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('accounting/accounting_model');
			$CI->accounting_model->automatic_fe_depreciation_conversion($id);
		}

	}
	return $id;
}


function acc_delete_fe_maintenance_convert($id) {
	if ($id) {
		$CI = &get_instance();
		$CI->load->model('accounting/accounting_model');

		$CI->accounting_model->delete_convert($id, 'fe_maintenance');
	}

	return $id;
}

function acc_before_client_added($data) {
	if (isset($data['balance'])) {
        $data['balance'] = str_replace(',', '', $data['balance']);
        if($data['balance'] != '' && $data['balance'] > 0){
            if($data['balance_as_of'] != ''){
                $data['balance_as_of'] = to_sql_date($data['balance_as_of']);
            }else{
                $data['balance_as_of'] = date('Y-m-d');
            }
        }else{
            unset($data['balance']);
            unset($data['balance_as_of']);
        }
    }
    
	return $data;
}

function acc_client_created($data) {
	$CI = &get_instance();
	$CI->load->model('accounting/accounting_model');

	$CI->accounting_model->acc_client_created($data);

	return $data;
}

function acc_client_updated($data, $id) {
	$CI = &get_instance();
	$CI->load->model('accounting/accounting_model');

	if (isset($data['balance'])) {
        $data['balance'] = str_replace(',', '', $data['balance']);
        if($data['balance'] != '' && $data['balance'] > 0){
            if($data['balance_as_of'] != ''){
                $data['balance_as_of'] = to_sql_date($data['balance_as_of']);
            }else{
                $data['balance_as_of'] = date('Y-m-d');
            }
        }else{
            unset($data['balance']);
            unset($data['balance_as_of']);
        }
    }

	$CI->accounting_model->acc_client_updated($data, $id);

	return $data;
}

function acc_init_client_profile($client = ''){
	$balance = $client ? $client->balance : '';
	$balance_as_of = $client ? _d($client->balance_as_of) : '';
	$attr = [];
	$date_attr = [];
	$attr['data-type'] = 'currency';

	if($client && $client->balance != null){
		$attr['disabled'] = 'true';
		$date_attr['disabled'] = 'true';
	}

    $option = '<div class="row">
          <div class="col-md-6">
          '. render_input('balance', 'balance', $balance, 'text', $attr) .'
          </div>
          <div class="col-md-6">
          '. render_date_input('balance_as_of', 'as_of', $balance_as_of, $date_attr) .'
          </div>
        </div>';
    echo html_entity_decode($option);
}

function acc_pur_vendor_created($data) {
	$CI = &get_instance();
	$CI->load->model('accounting/accounting_model');

	$CI->accounting_model->acc_pur_vendor_created($data);

	return $data;
}

function acc_pur_vendor_updated($data, $id) {
	$CI = &get_instance();
	$CI->load->model('accounting/accounting_model');

	$CI->accounting_model->acc_pur_vendor_updated($data, $id);

	return $data;
}


function acc_init_pur_vendor_profile($vendor = ''){
	$balance = $vendor ? $vendor->balance : '';
	$balance_as_of = $vendor ? _d($vendor->balance_as_of) : '';
	$attr = [];
	$date_attr = [];
	$attr['data-type'] = 'currency';

	if($vendor && $vendor->balance != null){
		$attr['disabled'] = 'true';
		$date_attr['disabled'] = 'true';
	}

    $option = '<div class="row">
          <div class="col-md-6">
          '. render_input('balance', 'balance', $balance, 'text', $attr) .'
          </div>
          <div class="col-md-6">
          '. render_date_input('balance_as_of', 'as_of', $balance_as_of, $date_attr) .'
          </div>
        </div>';
    echo html_entity_decode($option);
}

function exporting_return_order_approved($id) {
	if ($id) {
		if (get_option('acc_wh_stock_import_return_automatic_conversion') == 1) {
			$CI = &get_instance();
			$CI->load->model('warehouse/warehouse_model');
			$CI->load->model('warehouse/warehouse_model');
			$order_return = $CI->warehouse_model->get_order_return($id);

			if($order_return){
				$CI->load->model('accounting/accounting_model');
				$CI->accounting_model->automatic_stock_import_conversion($order_return->receipt_delivery_id);
			}
		}
	}
	return $id;
}

/**
 * get currency rates
 *
 */
function acc_cronjob_currency_rates($manually) {
    $CI = &get_instance();
    $CI->load->model('accounting/accounting_model');
    if (date('G') == '16' && get_option('cr_automatically_get_currency_rate') == 1) {
        if(date('Y-m-d') != get_option('cur_date_cronjob_currency_rates')){
            $CI->accounting_model->cronjob_currency_rates($manually);
        }
    }

    $CI->accounting_model->recurring_journal_entry();
}

function accounting_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $accounting_api = new AccountingLic();
    $accounting_gtssres = $accounting_api->verify_license(true);    
    if(!$accounting_gtssres || ($accounting_gtssres && isset($accounting_gtssres['status']) && !$accounting_gtssres['status'])){
         $CI->app_modules->deactivate(ACCOUNTING_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function accounting_preactivate($module_name){
    if ($module_name['system_name'] == ACCOUNTING_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $accounting_api = new AccountingLic();
        $accounting_gtssres = $accounting_api->verify_license();          
        if(!$accounting_gtssres || ($accounting_gtssres && isset($accounting_gtssres['status']) && !$accounting_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.ACCOUNTING_MODULE_NAME); 
            $data['module_name'] = ACCOUNTING_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function accounting_predeactivate($module_name){
    if ($module_name['system_name'] == ACCOUNTING_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $accounting_api = new AccountingLic();
        $accounting_api->deactivate_license();
    }
}