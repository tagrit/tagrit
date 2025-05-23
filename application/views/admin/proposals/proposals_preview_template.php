<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?= form_hidden('_attachment_sale_id', $proposal->id); ?>
<?= form_hidden('_attachment_sale_type', 'proposal'); ?>
<div class="panel_s">
    <div class="panel-body">
        <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab_proposal" aria-controls="tab_proposal" role="tab" data-toggle="tab">
                            <?= _l('proposal'); ?>
                        </a>
                    </li>
                    <?php if (isset($proposal)) { ?>
                    <li role="presentation">
                        <a href="#tab_comments" onclick="get_proposal_comments(); return false;"
                            aria-controls="tab_comments" role="tab" data-toggle="tab">
                            <?= _l('proposal_comments');
                        $total_comments = total_rows(
                            db_prefix() . 'proposal_comments',
                            [
                                'proposalid' => $proposal->id,
                            ]
                        );
                        ?>
                            <span
                                class="badge total_comments <?= $total_comments === 0 ? 'hide' : ''; ?>"><?= $total_comments ?></span>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#tab_reminders"
                            onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?= $proposal->id; ?> + '/' + 'proposal', undefined, undefined, undefined,[1,'asc']); return false;"
                            aria-controls="tab_reminders" role="tab" data-toggle="tab">
                            <?= _l('estimate_reminders'); ?>
                            <?php
                           $total_reminders = total_rows(
                               db_prefix() . 'reminders',
                               [
                                   'isnotified' => 0,
                                   'staff'      => get_staff_user_id(),
                                   'rel_type'   => 'proposal',
                                   'rel_id'     => $proposal->id,
                               ]
                           );
                        if ($total_reminders > 0) {
                            echo '<span class="badge">' . $total_reminders . '</span>';
                        }
                        ?>
                        </a>
                    </li>
                    <li role="presentation" class="tab-separator">
                        <a href="#tab_tasks"
                            onclick="init_rel_tasks_table(<?= e($proposal->id); ?>,'proposal'); return false;"
                            aria-controls="tab_tasks" role="tab" data-toggle="tab">
                            <?= _l('tasks'); ?>
                        </a>
                    </li>
                    <li role="presentation" class="tab-separator">
                        <a href="#tab_notes"
                            onclick="get_sales_notes(<?= e($proposal->id); ?>,'proposals'); return false"
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
                    <li role="presentation" class="tab-separator">
                        <a href="#tab_templates"
                            onclick="get_templates('proposals', <?= $proposal->id ?? '' ?>); return false"
                            aria-controls="tab_templates" role="tab" data-toggle="tab">
                            <?= _l('templates');
                        $conditions = ['type' => 'proposals'];
                        if (staff_cant('view_all_templates', 'proposals')) {
                            $conditions['addedfrom'] = get_staff_user_id();
                            $conditions['type']      = 'proposals';
                        }
                        $total_templates = total_rows(db_prefix() . 'templates', $conditions);
                        ?>
                            <span
                                class="badge total_templates <?= $total_templates === 0 ? 'hide' : ''; ?>"><?= $total_templates ?></span>
                        </a>
                    </li>
                    <li role="presentation" data-toggle="tooltip"
                        title="<?= _l('emails_tracking'); ?>"
                        class="tab-separator">
                        <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab">
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
                            <i class="fa fa-expand"></i></a>
                    </li>
                    <?php hooks()->do_action('after_admin_proposal_preview_template_tab_menu_last_item', $proposal); ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="row mtop20">
            <div class="col-md-3">
                <?= format_proposal_status($proposal->status, 'mtop5 inline-block'); ?>
            </div>
            <div class="col-md-9 text-right _buttons proposal_buttons">
                <?php if (staff_can('edit', 'proposals')) { ?>
                <a href="<?= admin_url('proposals/proposal/' . $proposal->id); ?>"
                    data-placement="left" data-toggle="tooltip"
                    title="<?= _l('proposal_edit'); ?>"
                    class="btn btn-default btn-with-tooltip" data-placement="bottom"><i
                        class="fa-regular fa-pen-to-square"></i></a>
                <?php } ?>
                <div class="btn-group">
                    <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"><i class="fa-regular fa-file-pdf"></i><?php if (is_mobile()) {
                            echo ' PDF';
                        } ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="hidden-xs"><a
                                href="<?= admin_url('proposals/pdf/' . $proposal->id . '?output_type=I'); ?>"><?= _l('view_pdf'); ?></a>
                        </li>
                        <li class="hidden-xs"><a
                                href="<?= admin_url('proposals/pdf/' . $proposal->id . '?output_type=I'); ?>"
                                target="_blank"><?= _l('view_pdf_in_new_window'); ?></a>
                        </li>
                        <li><a
                                href="<?= admin_url('proposals/pdf/' . $proposal->id); ?>"><?= _l('download'); ?></a>
                        </li>
                        <li>
                            <a href="<?= admin_url('proposals/pdf/' . $proposal->id . '?print=true'); ?>"
                                target="_blank">
                                <?= _l('print'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="#" class="btn btn-default btn-with-tooltip" data-target="#proposal_send_to_customer"
                    data-toggle="modal"><span data-toggle="tooltip" class="btn-with-tooltip"
                        data-title="<?= _l('proposal_send_to_email'); ?>"
                        data-placement="bottom"><i class="fa-regular fa-envelope"></i></span></a>
                <div class="btn-group ">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <?= _l('more'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a href="<?= site_url('proposal/' . $proposal->id . '/' . $proposal->hash); ?>"
                                target="_blank"><?= _l('proposal_view'); ?></a>
                        </li>
                        <?php hooks()->do_action('after_proposal_view_as_client_link', $proposal); ?>
                        <?php if (! empty($proposal->open_till) && date('Y-m-d') < $proposal->open_till && ($proposal->status == 4 || $proposal->status == 1) && is_proposals_expiry_reminders_enabled()) { ?>
                        <li>
                            <a
                                href="<?= admin_url('proposals/send_expiry_reminder/' . $proposal->id); ?>"><?= _l('send_expiry_reminder'); ?></a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="#" data-toggle="modal"
                                data-target="#sales_attach_file"><?= _l('invoice_attach_file'); ?></a>
                        </li>
                        <?php if (staff_can('create', 'proposals')) { ?>
                        <li>
                            <a
                                href="<?= admin_url() . 'proposals/copy/' . $proposal->id; ?>"><?= _l('proposal_copy'); ?></a>
                        </li>
                        <?php } ?>
                        <?php if ($proposal->estimate_id == null && $proposal->invoice_id == null) { ?>
                        <?php foreach ($proposal_statuses as $status) {
                            if (staff_can('edit', 'proposals')) {
                                if ($proposal->status != $status) { ?>
                        <li>
                            <a
                                href="<?= admin_url() . 'proposals/mark_action_status/' . $status . '/' . $proposal->id; ?>"><?= e(_l('proposal_mark_as', format_proposal_status($status, '', false))); ?></a>
                        </li>
                        <?php
                                }
                            }
                        } ?>
                        <?php } ?>
                        <?php if (! empty($proposal->signature) && staff_can('delete', 'proposals')) { ?>
                        <li>
                            <a href="<?= admin_url('proposals/clear_signature/' . $proposal->id); ?>"
                                class="_delete">
                                <?= _l('clear_signature'); ?>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if (staff_can('delete', 'proposals')) { ?>
                        <li>
                            <a href="<?= admin_url() . 'proposals/delete/' . $proposal->id; ?>"
                                class="text-danger delete-text _delete"><?= _l('proposal_delete'); ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php if ($proposal->estimate_id == null && $proposal->invoice_id == null) { ?>
                <?php if (staff_can('create', 'estimates') || staff_can('create', 'invoices')) { ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle<?php if ($proposal->rel_type == 'customer' && total_rows(db_prefix() . 'clients', ['active' => 0, 'userid' => $proposal->rel_id]) > 0) {
                        echo ' disabled';
                    } ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= _l('proposal_convert'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <?php
                     $disable_convert = false;
                    $not_related      = false;

                    if ($proposal->rel_type == 'lead') {
                        if (total_rows(db_prefix() . 'clients', ['leadid' => $proposal->rel_id]) == 0) {
                            $disable_convert = true;
                            $help_text       = 'proposal_convert_to_lead_disabled_help';
                        }
                    } elseif (empty($proposal->rel_type)) {
                        $disable_convert = true;
                        $help_text       = 'proposal_convert_not_related_help';
                    }
                    ?>
                        <?php if (staff_can('create', 'estimates')) { ?>
                        <li <?php if ($disable_convert) {
                            echo 'data-toggle="tooltip" title="' . _l($help_text, _l('proposal_convert_estimate')) . '"';
                        } ?>><a href="#"
                                <?php if ($disable_convert) {
                                    echo 'style="cursor:not-allowed;" onclick="return false;"';
                                } else {
                                    echo 'data-template="estimate" onclick="proposal_convert_template(this); return false;"';
                                } ?>><?= _l('proposal_convert_estimate'); ?></a>
                        </li>
                        <?php } ?>
                        <?php if (staff_can('create', 'invoices')) { ?>
                        <li <?php if ($disable_convert) {
                            echo 'data-toggle="tooltip" title="' . _l($help_text, _l('proposal_convert_invoice')) . '"';
                        } ?>><a href="#"
                                <?php if ($disable_convert) {
                                    echo 'style="cursor:not-allowed;" onclick="return false;"';
                                } else {
                                    echo 'data-template="invoice" onclick="proposal_convert_template(this); return false;"';
                                } ?>><?= _l('proposal_convert_invoice'); ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
                <?php } else {
                    if ($proposal->estimate_id != null) {
                        echo '<a href="' . admin_url('estimates/list_estimates/' . $proposal->estimate_id) . '" class="btn btn-primary">' . e(format_estimate_number($proposal->estimate_id)) . '</a>';
                    } else {
                        echo '<a href="' . admin_url('invoices/list_invoices/' . $proposal->invoice_id) . '" class="btn btn-primary">' . e(format_invoice_number($proposal->invoice_id)) . '</a>';
                    }
                } ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr class="hr-panel-separator" />
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab_proposal">
                        <div class="row mtop10">
                            <?php if ($proposal->status == 3 && ! empty($proposal->acceptance_firstname) && ! empty($proposal->acceptance_lastname) && ! empty($proposal->acceptance_email)) { ?>
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <?= _l('accepted_identity_info', [
                                        _l('proposal_lowercase'),
                                        '<b>' . $proposal->acceptance_firstname . ' ' . $proposal->acceptance_lastname . '</b> (<a href="mailto:' . $proposal->acceptance_email . '" class="alert-link">' . $proposal->acceptance_email . '</a>)',
                                        '<b>' . _dt($proposal->acceptance_date) . '</b>',
                                        '<b>' . $proposal->acceptance_ip . '</b>' . (is_admin() ? '&nbsp;<a href="' . admin_url('proposals/clear_acceptance_info/' . $proposal->id) . '" class="_delete text-muted" data-toggle="tooltip" data-title="' . _l('clear_this_information') . '"><i class="fa fa-remove"></i></a>' : ''),
                                    ]); ?>
                                </div>
                            </div>
                            <?php }
                            if ($proposal->project_id) { ?>
                            <div class="col-md-12">
                                <h4 class="font-medium mbot15"><?= _l('related_to_project', [
                                    _l('proposal_lowercase'),
                                    _l('project_lowercase'),
                                    '<a href="' . admin_url('projects/view/' . $proposal->project_id) . '" target="_blank">' . $proposal->project_data->name . '</a>',
                                ]); ?></h4>
                            </div>
                            <?php } ?>
                            <div class="col-md-6">
                                <h4 class="bold">
                                    <?php
                                     $tags = get_tags_in($proposal->id, 'proposal');
if (count($tags) > 0) {
    echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="' . e(implode(', ', $tags)) . '"></i>';
}
?>
                                    <a
                                        href="<?= admin_url('proposals/proposal/' . $proposal->id); ?>">
                                        <span id="proposal-number">
                                            <?= e(format_proposal_number($proposal->id)); ?>
                                        </span>
                                    </a>
                                </h4>
                                <h5 class="bold mbot15 font-medium"><a
                                        href="<?= admin_url('proposals/proposal/' . $proposal->id); ?>"><?= e($proposal->subject); ?></a>
                                </h5>
                                <address class="tw-text-neutral-500">
                                    <?= format_organization_info(); ?>
                                </address>
                            </div>
                            <div class="col-md-6 text-right">
                                <address class="tw-text-neutral-500">
                                    <span
                                        class="bold tw-text-neutral-700"><?= _l('proposal_to'); ?>:</span><br />
                                    <?= format_proposal_info($proposal, 'admin'); ?>
                                </address>
                            </div>
                        </div>
                        <hr class="hr-panel-separator" />
                        <?php
                     if (count($proposal->attachments) > 0) { ?>
                        <p class="bold">
                            <?= _l('proposal_files'); ?>
                        </p>
                        <?php foreach ($proposal->attachments as $attachment) {
                            $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                            if (! empty($attachment['external'])) {
                                $attachment_url = $attachment['external_link'];
                            } ?>
                        <div class="mbot15 row"
                            data-attachment-id="<?= e($attachment['id']); ?>">
                            <div class="col-md-8">
                                <div class="pull-left"><i
                                        class="<?= get_mime_class($attachment['filetype']); ?>"></i>
                                </div>
                                <a href="<?= e($attachment_url); ?>"
                                    target="_blank"><?= e($attachment['file_name']); ?></a>
                                <br />
                                <small class="text-muted">
                                    <?= e($attachment['filetype']); ?></small>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($attachment['visible_to_customer'] == 0) {
                                    $icon    = 'fa-toggle-off';
                                    $tooltip = _l('show_to_customer');
                                } else {
                                    $icon    = 'fa-toggle-on';
                                    $tooltip = _l('hide_from_customer');
                                } ?>
                                <a href="#" data-toggle="tooltip"
                                    onclick="toggle_file_visibility(<?= e($attachment['id']); ?>,<?= e($proposal->id); ?>,this); return false;"
                                    data-title="<?= e($tooltip); ?>"><i
                                        class="fa <?= e($icon); ?>"
                                        aria-hidden="true"></i></a>
                                <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                <a href="#" class="text-danger"
                                    onclick="delete_proposal_attachment(<?= e($attachment['id']); ?>); return false;"><i
                                        class="fa fa-times"></i></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        } ?>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php if (isset($proposal_merge_fields)) { ?>
                        <p class="bold text-right"><a href="#"
                                onclick="slideToggle('.avilable_merge_fields'); return false;"><?= _l('available_merge_fields'); ?></a>
                        </p>
                        <hr class="hr-panel-separator" />
                        <div class="hide avilable_merge_fields mtop15">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="list-group">
                                        <?php
                                    foreach ($proposal_merge_fields as $field) {
                                        foreach ($field as $f) {
                                            echo '<li class="list-group-item"><b>' . $f['name'] . '</b> <a href="#" class="pull-right" onclick="insert_proposal_merge_field(this); return false;">' . $f['key'] . '</a></li>';
                                        }
                                    }
                            ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="editable proposal tc-content" id="proposal_content_area"
                            style="border:1px solid #d2d2d2;min-height:70px;border-radius:4px;">
                            <?php if (empty($proposal->content)) {
                                echo '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>';
                            } else {
                                echo $proposal->content;
                            }
?>
                        </div>
                        <?php if (! empty($proposal->signature)) { ?>
                        <div class="row mtop25">
                            <div class="col-md-6 col-md-offset-6 text-right">
                                <div class="bold">
                                    <p class="no-mbot">
                                        <?= _l('contract_signed_by') . ": {$proposal->acceptance_firstname} {$proposal->acceptance_lastname}"?>
                                    </p>
                                    <p class="no-mbot">
                                        <?= _l('proposal_signed_date') . ': ' . _dt($proposal->acceptance_date) ?>
                                    </p>
                                    <p class="no-mbot">
                                        <?= _l('proposal_signed_ip') . ": {$proposal->acceptance_ip}"?>
                                    </p>
                                </div>
                                <p class="bold">
                                    <?= _l('document_customer_signature_text'); ?>
                                    <?php if (staff_can('delete', 'proposals')) { ?>
                                    <a href="<?= admin_url('proposals/clear_signature/' . $proposal->id); ?>"
                                        data-toggle="tooltip"
                                        title="<?= _l('clear_signature'); ?>"
                                        class="_delete text-danger">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                    <?php } ?>
                                </p>
                                <div class="pull-right">
                                    <img src="<?= site_url('download/preview_image?path=' . protected_file_url_by_path(get_upload_path_by_type('proposal') . $proposal->id . '/' . $proposal->signature)); ?>"
                                        class="img-responsive" alt="">
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_comments">
                        <div class="row proposal-comments mtop15">
                            <div class="col-md-12">
                                <div id="proposal-comments"></div>
                                <div class="clearfix"></div>
                                <textarea name="content" id="comment" rows="4"
                                    class="form-control mtop15 proposal-comment"></textarea>
                                <button type="button" class="btn btn-primary mtop10 pull-right"
                                    onclick="add_proposal_comment();"><?= _l('proposal_add_comment'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_notes">
                        <?= form_open(admin_url('proposals/add_note/' . $proposal->id), ['id' => 'sales-notes', 'class' => 'proposal-notes-form']); ?>
                        <?= render_textarea('description'); ?>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary mtop15 mbot15"><?= _l('estimate_add_note'); ?></button>
                        </div>
                        <?= form_close(); ?>
                        <hr />
                        <div class="mtop20" id="sales_notes_area"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_templates">
                        <div class="row proposal-templates">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary"
                                    onclick="add_template('proposals',<?= $proposal->id ?? '' ?>);">
                                    <?= _l('add_template'); ?>
                                </button>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div id="proposal-templates" class="proposal-templates-wrapper"></div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane ptop10" id="tab_emails_tracking">
                        <?php
                     $this->load->view(
                         'admin/includes/emails_tracking',
                         [
                             'tracked_emails' => get_tracked_emails($proposal->id, 'proposal'), ]
                     );
?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_tasks">
                        <?php init_relation_tasks_table(['data-new-rel-id' => $proposal->id, 'data-new-rel-type' => 'proposal'], 'tasksFilters'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_reminders">
                        <a href="#" data-toggle="modal" class="btn btn-primary"
                            data-target=".reminder-modal-proposal-<?= e($proposal->id); ?>"><i
                                class="fa-regular fa-bell"></i>
                            <?= _l('proposal_set_reminder_title'); ?></a>
                        <hr />
                        <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                        <?php $this->load->view('admin/includes/modals/reminder', ['id' => $proposal->id, 'name' => 'proposal', 'members' => $members, 'reminder_title' => _l('proposal_set_reminder_title')]); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                        <?php
$views_activity = get_views_tracking('proposal', $proposal->id);
if (count($views_activity) === 0) {
    echo '<h4 class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('not_viewed_yet', _l('proposal_lowercase')) . '</h4>';
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
                    <?php hooks()->do_action('after_admin_invoice_proposal_template_tab_content_last_item', $proposal); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-wrapper"></div>
<?php $this->load->view('admin/proposals/send_proposal_to_email_template'); ?>
<script>
    init_btn_with_tooltips();
    init_datepicker();
    init_selectpicker();
    init_form_reminder();
    init_tabs_scrollable();
    // defined in manage proposals
    proposal_id = '<?= e($proposal->id); ?>';
    init_proposal_editor();
</script>