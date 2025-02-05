<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?= form_hidden('_attachment_sale_id', $estimate->id); ?>
<?= form_hidden('_attachment_sale_type', 'estimate'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                                <?= _l('estimate'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_tasks"
                                onclick="init_rel_tasks_table(<?= e($estimate->id); ?>,'estimate'); return false;"
                                aria-controls="tab_tasks" role="tab" data-toggle="tab">
                                <?= _l('tasks'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                                <?= _l('estimate_view_activity_tooltip'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_reminders"
                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?= $estimate->id; ?> + '/' + 'estimate', undefined, undefined, undefined,[1,'asc']); return false;"
                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <?= _l('estimate_reminders'); ?>
                                <?php
                        $total_reminders = total_rows(
                            db_prefix() . 'reminders',
                            [
                                'isnotified' => 0,
                                'staff'      => get_staff_user_id(),
                                'rel_type'   => 'estimate',
                                'rel_id'     => $estimate->id,
                            ]
                        );
if ($total_reminders > 0) {
    echo '<span class="badge">' . $total_reminders . '</span>';
}
?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator">
                            <a href="#tab_notes"
                                onclick="get_sales_notes(<?= e($estimate->id); ?>,'estimates'); return false"
                                aria-controls="tab_notes" role="tab" data-toggle="tab">
                                <?= _l('estimate_notes'); ?>
                                <span class="notes-total">
                                    <?php if ($totalNotes > 0) { ?>
                                    <span
                                        class="badge"><?= e($totalNotes); ?></span>
                                    <?php } ?>
                                </span>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            title="<?= _l('emails_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab"
                                data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa-regular fa-envelope-open" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('emails_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            data-title="<?= _l('view_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa fa-eye"></i>
                                <?php } else { ?>
                                <?= _l('view_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            data-title="<?= _l('toggle_full_view'); ?>"
                            class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;">
                                <i class="fa fa-expand"></i>
                            </a>
                        </li>
                        <?php hooks()->do_action('after_admin_estimate_preview_template_tab_menu_last_item', $estimate); ?>
                    </ul>
                </div>
            </div>
            <div class="row mtop20">
                <div class="col-md-3">
                    <?= format_estimate_status($estimate->status, 'mtop5 inline-block'); ?>
                </div>
                <div class="col-md-9">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right _buttons">
                        <?php if (staff_can('edit', 'estimates')) { ?>
                        <a href="<?= admin_url('estimates/estimate/' . $estimate->id); ?>"
                            class="btn btn-default btn-with-tooltip" data-toggle="tooltip"
                            title="<?= _l('edit_estimate_tooltip'); ?>"
                            data-placement="bottom"><i class="fa-regular fa-pen-to-square"></i></a>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><i
                                    class="fa-regular fa-file-pdf"></i><?= is_mobile() ? ' PDF' : ''; ?>
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="hidden-xs">
                                    <a
                                        href="<?= admin_url('estimates/pdf/' . $estimate->id . '?output_type=I'); ?>">
                                        <?= _l('view_pdf'); ?>
                                    </a>
                                </li>
                                <li class="hidden-xs">
                                    <a href="<?= admin_url('estimates/pdf/' . $estimate->id . '?output_type=I'); ?>"
                                        target="_blank">
                                        <?= _l('view_pdf_in_new_window'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="<?= admin_url('estimates/pdf/' . $estimate->id); ?>">
                                        <?= _l('download'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= admin_url('estimates/pdf/' . $estimate->id . '?print=true'); ?>"
                                        target="_blank">
                                        <?= _l('print'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php
                     $_tooltip = _l('estimate_sent_to_email_tooltip');
$_tooltip_already_send         = '';
if ($estimate->sent == 1) {
    $_tooltip_already_send = _l('estimate_already_send_to_client_tooltip', time_ago($estimate->datesend));
}
?>
                        <?php if (! empty($estimate->clientid)) { ?>
                        <a href="#" class="estimate-send-to-client btn btn-default btn-with-tooltip"
                            data-toggle="tooltip"
                            title="<?= e($_tooltip); ?>"
                            data-placement="bottom"><span data-toggle="tooltip"
                                data-title="<?= e($_tooltip_already_send); ?>"><i
                                    class="fa-regular fa-envelope"></i></span></a>
                        <?php } ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= _l('more'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="<?= site_url('estimate/' . $estimate->id . '/' . $estimate->hash) ?>"
                                        target="_blank">
                                        <?= _l('view_estimate_as_client'); ?>
                                    </a>
                                </li>
                                <?php hooks()->do_action('after_estimate_view_as_client_link', $estimate); ?>
                                <?php if ((! empty($estimate->expirydate) && date('Y-m-d') < $estimate->expirydate && ($estimate->status == 2 || $estimate->status == 5)) && is_estimates_expiry_reminders_enabled()) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('estimates/send_expiry_reminder/' . $estimate->id); ?>">
                                        <?= _l('send_expiry_reminder'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="#" data-toggle="modal"
                                        data-target="#sales_attach_file"><?= _l('invoice_attach_file'); ?></a>
                                </li>
                                <?php if (staff_can('create', 'projects') && $estimate->project_id == 0) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url("projects/project?via_estimate_id={$estimate->id}&customer_id={$estimate->clientid}") ?>">
                                        <?= _l('estimate_convert_to_project'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($estimate->invoiceid == null) {
                                    if (staff_can('edit', 'estimates')) {
                                        foreach ($estimate_statuses as $status) {
                                            if ($estimate->status != $status) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url() . 'estimates/mark_action_status/' . $status . '/' . $estimate->id; ?>">
                                        <?= e(_l('estimate_mark_as', format_estimate_status($status, '', false))); ?></a>
                                </li>
                                <?php }
                                            } ?>
                                <?php } ?>
                                <?php } ?>
                                <?php if (staff_can('create', 'estimates')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('estimates/copy/' . $estimate->id); ?>">
                                        <?= _l('copy_estimate'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (! empty($estimate->signature) && staff_can('delete', 'estimates')) { ?>
                                <li>
                                    <a href="<?= admin_url('estimates/clear_signature/' . $estimate->id); ?>"
                                        class="_delete">
                                        <?= _l('clear_signature'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (staff_can('delete', 'estimates')) { ?>
                                <?php
                                               if ((get_option('delete_only_on_last_estimate') == 1 && is_last_estimate($estimate->id))
                                                   || (get_option('delete_only_on_last_estimate') == 0)) { ?>
                                <li>
                                    <a href="<?= admin_url('estimates/delete/' . $estimate->id); ?>"
                                        class="text-danger delete-text _delete">
                                        <?= _l('delete_estimate_tooltip'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php if ($estimate->invoiceid == null) { ?>
                        <?php if (staff_can('create', 'invoices') && ! empty($estimate->clientid)) { ?>
                        <div class="btn-group pull-right mleft5">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <?= _l('estimate_convert_to_invoice'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a
                                        href="<?= admin_url('estimates/convert_to_invoice/' . $estimate->id . '?save_as_draft=true'); ?>"><?= _l('convert_and_save_as_draft'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a
                                        href="<?= admin_url('estimates/convert_to_invoice/' . $estimate->id); ?>"><?= _l('convert'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <a href="<?= admin_url('invoices/list_invoices/' . $estimate->invoice->id); ?>"
                            data-placement="bottom" data-toggle="tooltip"
                            title="<?= e(_l('estimate_invoiced_date', _dt($estimate->invoiced_date))); ?>"
                            class="btn btn-primary mleft10"><?= e(format_invoice_number($estimate->invoice->id)); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="hr-panel-separator" />
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                    <?php if (isset($estimate->scheduled_email) && $estimate->scheduled_email) { ?>
                    <div class="alert alert-warning">
                        <?= e(_l('invoice_will_be_sent_at', _dt($estimate->scheduled_email->scheduled_at))); ?>
                        <?php if (staff_can('edit', 'estimates') || $estimate->addedfrom == get_staff_user_id()) { ?>
                        <a href="#"
                            onclick="edit_estimate_scheduled_email(<?= $estimate->scheduled_email->id; ?>); return false;">
                            <?= _l('edit'); ?>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <div id="estimate-preview">
                        <div class="row">
                            <?php if ($estimate->status == 4 && ! empty($estimate->acceptance_firstname) && ! empty($estimate->acceptance_lastname) && ! empty($estimate->acceptance_email)) { ?>
                            <div class="col-md-12">
                                <div class="alert alert-info mbot15">
                                    <?= _l('accepted_identity_info', [
                                        _l('estimate_lowercase'),
                                        '<b>' . e($estimate->acceptance_firstname) . ' ' . e($estimate->acceptance_lastname) . '</b> (<a href="mailto:' . e($estimate->acceptance_email) . '" class="alert-link">' . e($estimate->acceptance_email) . '</a>)',
                                        '<b>' . e(_dt($estimate->acceptance_date)) . '</b>',
                                        '<b>' . e($estimate->acceptance_ip) . '</b>' . (is_admin() ? '&nbsp;<a href="' . admin_url('estimates/clear_acceptance_info/' . $estimate->id) . '" class="_delete text-muted" data-toggle="tooltip" data-title="' . _l('clear_this_information') . '"><i class="fa fa-remove"></i></a>' : ''),
                                    ]); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($estimate->project_id) { ?>
                            <div class="col-md-12">
                                <h4 class="font-medium mbot15">
                                    <?= _l('related_to_project', [
                                        _l('estimate_lowercase'),
                                        _l('project_lowercase'),
                                        '<a href="' . admin_url('projects/view/' . $estimate->project_id) . '" target="_blank">' . e($estimate->project_data->name) . '</a>',
                                    ]); ?>
                                </h4>
                            </div>
                            <?php } ?>
                            <div class="col-md-6 col-sm-6">
                                <h4 class="bold">
                                    <?php $tags = get_tags_in($estimate->id, 'estimate'); ?>
                                    <?php if (count($tags) > 0) {
                                        echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="' . e(implode(', ', $tags)) . '"></i>';
                                    } ?>
                                    <a
                                        href="<?= admin_url('estimates/estimate/' . $estimate->id); ?>">
                                        <span id="estimate-number">
                                            <?= e(format_estimate_number($estimate->id)); ?>
                                        </span>
                                    </a>
                                </h4>
                                <address class="tw-text-neutral-500">
                                    <?= format_organization_info(); ?>
                                </address>
                            </div>
                            <div class="col-sm-6 text-right">
                                <span
                                    class="bold"><?= _l('estimate_to'); ?></span>
                                <address class="tw-text-neutral-500">
                                    <?= format_customer_info($estimate, 'estimate', 'billing', true); ?>
                                </address>
                                <?php if ($estimate->include_shipping == 1 && $estimate->show_shipping_on_estimate == 1) { ?>
                                <span
                                    class="bold"><?= _l('ship_to'); ?></span>
                                <address class="tw-text-neutral-500">
                                    <?= format_customer_info($estimate, 'estimate', 'shipping'); ?>
                                </address>
                                <?php } ?>
                                <p class="no-mbot">
                                    <span class="bold">
                                        <?= _l('estimate_data_date'); ?>:
                                    </span>
                                    <?= e($estimate->date); ?>
                                </p>
                                <?php if (! empty($estimate->expirydate)) { ?>
                                <p class="no-mbot">
                                    <span
                                        class="bold"><?= _l('estimate_data_expiry_date'); ?>:</span>
                                    <?= e($estimate->expirydate); ?>
                                </p>
                                <?php } ?>
                                <?php if (! empty($estimate->reference_no)) { ?>
                                <p class="no-mbot">
                                    <span
                                        class="bold"><?= _l('reference_no'); ?>:</span>
                                    <?= e($estimate->reference_no); ?>
                                </p>
                                <?php } ?>
                                <?php if ($estimate->sale_agent && get_option('show_sale_agent_on_estimates') == 1) { ?>
                                <p class="no-mbot">
                                    <span
                                        class="bold"><?= _l('sale_agent_string'); ?>:</span>
                                    <?= e(get_staff_full_name($estimate->sale_agent)); ?>
                                </p>
                                <?php } ?>
                                <?php if ($estimate->project_id && get_option('show_project_on_estimate') == 1) { ?>
                                <p class="no-mbot">
                                    <span
                                        class="bold"><?= _l('project'); ?>:</span>
                                    <?= e(get_project_name_by_id($estimate->project_id)); ?>
                                </p>
                                <?php } ?>
                                <?php $pdf_custom_fields = get_custom_fields('estimate', ['show_on_pdf' => 1]); ?>
                                <?php foreach ($pdf_custom_fields as $field) {
                                    $value = get_custom_field_value($estimate->id, $field['id'], 'estimate');
                                    if ($value == '') {
                                        continue;
                                    } ?>
                                <p class="no-mbot">
                                    <span
                                        class="bold"><?= e($field['name']); ?>:
                                    </span>
                                    <?= $value; ?>
                                </p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <?php $items = get_items_table_data($estimate, 'estimate', 'html', true); ?>
                                    <?= $items->table(); ?>
                                </div>
                            </div>
                            <div class="col-md-5 col-md-offset-7">
                                <table class="table text-right">
                                    <tbody>
                                        <tr id="subtotal">
                                            <td>
                                                <span class="tw-font-medium tw-text-neutral-700">
                                                    <?= _l('estimate_subtotal'); ?>
                                                </span>
                                            </td>
                                            <td class="subtotal">
                                                <?= e(app_format_money($estimate->subtotal, $estimate->currency_name)); ?>
                                            </td>
                                        </tr>
                                        <?php if (is_sale_discount_applied($estimate)) { ?>
                                        <tr>
                                            <td>
                                                <span
                                                    class="tw-font-medium tw-text-neutral-700"><?= _l('estimate_discount'); ?>
                                                    <?php if (is_sale_discount($estimate, 'percent')) { ?>
                                                    (<?= e(app_format_number($estimate->discount_percent, true)); ?>%)
                                                    <?php } ?>
                                                </span>
                                            </td>
                                            <td class="discount">
                                                <?= e('-' . app_format_money($estimate->discount_total, $estimate->currency_name)); ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php foreach ($items->taxes() as $tax) {
                                            echo '<tr class="tax-area"><td class="tw-font-medium !tw-text-neutral-700">' . e($tax['taxname']) . ' (' . e(app_format_number($tax['taxrate'])) . '%)</td><td>' . e(app_format_money($tax['total_tax'], $estimate->currency_name)) . '</td></tr>';
                                        } ?>
                                        <?php if ((int) $estimate->adjustment != 0) { ?>
                                        <tr>
                                            <td>
                                                <span class="tw-font-medium tw-text-neutral-700">
                                                    <?= _l('estimate_adjustment'); ?>
                                                </span>
                                            </td>
                                            <td class="adjustment">
                                                <?= e(app_format_money($estimate->adjustment, $estimate->currency_name)); ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td>
                                                <span class="tw-font-medium tw-text-neutral-700">
                                                    <?= _l('estimate_total'); ?>
                                                </span>
                                            </td>
                                            <td class="total">
                                                <?= e(app_format_money($estimate->total, $estimate->currency_name)); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($estimate->attachments) > 0) { ?>
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <p class="tw-text-neutral-700 tw-font-medium">
                                    <?= _l('estimate_files'); ?>
                                </p>
                            </div>
                            <?php foreach ($estimate->attachments as $attachment) {
                                $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                                if (! empty($attachment['external'])) {
                                    $attachment_url = $attachment['external_link'];
                                } ?>
                            <div class="mbot15 row col-md-12"
                                data-attachment-id="<?= e($attachment['id']); ?>">
                                <div class="col-md-8">
                                    <a href="<?= e($attachment_url); ?>"
                                        target="_blank"><?= e($attachment['file_name']); ?></a>
                                    <br />
                                    <small class="text-muted">
                                        <?= e($attachment['filetype']); ?></small>
                                </div>
                                <div class="col-md-4 text-right tw-space-x-2">
                                    <?php if ($attachment['visible_to_customer'] == 0) {
                                        $icon    = 'fa fa-toggle-off';
                                        $tooltip = _l('show_to_customer');
                                    } else {
                                        $icon    = 'fa fa-toggle-on';
                                        $tooltip = _l('hide_from_customer');
                                    } ?>
                                    <a href="#" data-toggle="tooltip" class="text-muted"
                                        onclick="toggle_file_visibility(<?= e($attachment['id']); ?>,<?= e($estimate->id); ?>,this); return false;"
                                        data-title="<?= e($tooltip); ?>">
                                        <i class="<?= e($icon); ?> fa-lg"
                                            aria-hidden="true"></i>
                                    </a>
                                    <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                    <a href="#" class="text-muted"
                                        onclick="delete_estimate_attachment(<?= e($attachment['id']); ?>); return false;">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <?php if ($estimate->clientnote != '') { ?>
                            <div class="col-md-12 mtop15">
                                <p class="tw-text-neutral-700 tw-font-medium">
                                    <?= _l('estimate_note'); ?>
                                </p>
                                <div class="tw-text-neutral-500 tw-leading-relaxed">
                                    <?= process_text_content_for_display($estimate->clientnote); ?>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($estimate->terms != '') { ?>
                            <div class="col-md-12 mtop15">
                                <p class="tw-text-neutral-700 tw-font-medium">
                                    <?= _l('terms_and_conditions'); ?>
                                </p>
                                <div class="tw-text-neutral-500 tw-leading-relaxed">
                                    <?= process_text_content_for_display($estimate->terms); ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_tasks">
                    <?php init_relation_tasks_table(['data-new-rel-id' => $estimate->id, 'data-new-rel-type' => 'estimate'], 'tasksFilters'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_reminders">
                    <a href="#" data-toggle="modal" class="btn btn-primary"
                        data-target=".reminder-modal-estimate-<?= e($estimate->id); ?>"><i
                            class="fa-regular fa-bell"></i>
                        <?= _l('estimate_set_reminder_title'); ?></a>
                    <hr />
                    <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                    <?php $this->load->view('admin/includes/modals/reminder', ['id' => $estimate->id, 'name' => 'estimate', 'members' => $members, 'reminder_title' => _l('estimate_set_reminder_title')]); ?>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_emails_tracking">
                    <?php $this->load->view('admin/includes/emails_tracking', [
                        'tracked_emails' => get_tracked_emails($estimate->id, 'estimate'),
                    ]); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_notes">
                    <?= form_open(admin_url('estimates/add_note/' . $estimate->id), ['id' => 'sales-notes', 'class' => 'estimate-notes-form']); ?>
                    <?= render_textarea('description'); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary mtop15 mbot15">
                            <?= _l('estimate_add_note'); ?>
                        </button>
                    </div>
                    <?= form_close(); ?>
                    <hr />
                    <div class="mtop20" id="sales_notes_area">
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_activity">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="activity-feed">
                                <?php foreach ($activity as $activity) {
                                    $_custom_data = false; ?>
                                <div class="feed-item"
                                    data-sale-activity-id="<?= e($activity['id']); ?>">
                                    <div class="date">
                                        <span class="text-has-action" data-toggle="tooltip"
                                            data-title="<?= e(_dt($activity['date'])); ?>">
                                            <?= e(time_ago($activity['date'])); ?>
                                        </span>
                                    </div>
                                    <div class="text">
                                        <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                                        <a
                                            href="<?= admin_url('profile/' . $activity['staffid']); ?>">
                                            <?= staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                            ?>
                                        </a>
                                        <?php } ?>
                                        <?php
                                            $additional_data = '';
                                    if (! empty($activity['additional_data'])) {
                                        $additional_data = app_unserialize($activity['additional_data']);
                                        $i               = 0;

                                        foreach ($additional_data as $data) {
                                            if (strpos($data, '<original_status>') !== false) {
                                                $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                                $additional_data[$i] = format_estimate_status($original_status, '', false);
                                            } elseif (strpos($data, '<new_status>') !== false) {
                                                $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                                $additional_data[$i] = format_estimate_status($new_status, '', false);
                                            } elseif (strpos($data, '<status>') !== false) {
                                                $status              = get_string_between($data, '<status>', '</status>');
                                                $additional_data[$i] = format_estimate_status($status, '', false);
                                            } elseif (strpos($data, '<custom_data>') !== false) {
                                                $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                                unset($additional_data[$i]);
                                            }
                                            $i++;
                                        }
                                    }

                                    $_formatted_activity = _l($activity['description'], $additional_data);

                                    if ($_custom_data !== false) {
                                        $_formatted_activity .= ' - ' . $_custom_data;
                                    }

                                    if (! empty($activity['full_name'])) {
                                        $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
                                    }

                                    echo $_formatted_activity;

                                    if (is_admin()) {
                                        echo '<a href="#" class="pull-right text-muted" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa-regular fa-trash-can"></i></a>';
                                    } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                    <?php
                  $views_activity = get_views_tracking('estimate', $estimate->id);
if (count($views_activity) === 0) {
    echo '<h4 class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('not_viewed_yet', _l('estimate_lowercase')) . '</h4>';
}

foreach ($views_activity as $activity) { ?>
                    <p class="text-success no-margin">
                        <?= _l('view_date') . ': ' . _dt($activity['date']); ?>
                    </p>
                    <p class="text-muted">
                        <?= _l('view_ip') . ': ' . $activity['view_ip']; ?>
                    </p>
                    <hr />
                    <?php } ?>
                </div>
                <?php hooks()->do_action('after_admin_estimate_preview_template_tab_content_last_item', $estimate); ?>
            </div>
        </div>
    </div>
</div>
<script>
    init_items_sortable(true);
    init_btn_with_tooltips();
    init_datepicker();
    init_selectpicker();
    init_form_reminder();
    init_tabs_scrollable();
    <?php if ($send_later) { ?>
    schedule_estimate_send( <?= e($estimate->id); ?> );
    <?php } ?>
</script>
<?php $this->load->view('admin/estimates/estimate_send_to_client'); ?>