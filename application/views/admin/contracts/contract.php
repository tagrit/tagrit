<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $isSignedOrMarkedSigned = isset($contract) && ($contract->signed == 1 || $contract->marked_as_signed == 1); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-5xl tw-mx-auto">
            <?php if (isset($contract) && $contract->signed == 1) { ?>
            <div class="alert alert-warning">
                <?= _l('contract_signed_not_all_fields_editable'); ?>
            </div>
            <?php } ?>

            <div class="sm:tw-flex sm:tw-justify-between sm:tw-items-center tw-mb-3 -tw-mt-px">
                <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-max-w-xl tw-truncate tw-space-x-1.5"
                    title="<?= isset($contract) ? e($contract->subject) : ''; ?>">
                    <span>
                        <?= isset($contract) ? e($contract->subject) : _l('contract_information') ?>
                    </span>

                    <?php if (isset($contract) && $contract->trash > 0) { ?>
                    <div class="label label-danger">
                        <span><?= _l('contract_trash'); ?></span>
                    </div>
                    <?php } ?>
                </h4>

                <?php if (isset($contract)) { ?>
                <div>
                    <div class="_buttons tw-space-x-1 tw-flex tw-items-center rtl:tw-space-x-reverse">
                        <a href="<?= site_url('contract/' . $contract->id . '/' . $contract->hash); ?>"
                            target="_blank" class="tw-shrink-0">
                            <?= _l('view_contract'); ?>
                        </a>
                        <div class="btn-group !tw-ml-3">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><i
                                    class="fa-regular fa-file-pdf"></i><?= is_mobile() ? 'PDF' : ''; ?>
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="hidden-xs"><a
                                        href="<?= admin_url('contracts/pdf/' . $contract->id . '?output_type=I'); ?>">
                                        <?= _l('view_pdf'); ?>
                                    </a>
                                </li>
                                <li class="hidden-xs">
                                    <a href="<?= admin_url('contracts/pdf/' . $contract->id . '?output_type=I'); ?>"
                                        target="_blank"><?= _l('view_pdf_in_new_window'); ?>
                                    </a>
                                </li>
                                <li><a
                                        href="<?= admin_url('contracts/pdf/' . $contract->id); ?>"><?= _l('download'); ?></a>
                                </li>
                                <li>
                                    <a href="<?= admin_url('contracts/pdf/' . $contract->id . '?print=true'); ?>"
                                        target="_blank">
                                        <?= _l('print'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="#" class="btn btn-default" data-target="#contract_send_to_client_modal"
                            data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip"
                                data-title="<?= _l('contract_send_to_email'); ?>"
                                data-placement="bottom">
                                <i class="fa-regular fa-envelope"></i></span>
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= _l('more'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="<?= site_url('contract/' . $contract->id . '/' . $contract->hash); ?>"
                                        target="_blank">
                                        <?= _l('view_contract'); ?>
                                    </a>
                                </li>
                                <?php if (! $isSignedOrMarkedSigned && staff_can('edit', 'contracts')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('contracts/mark_as_signed/' . $contract->id); ?>">
                                        <?= _l('mark_as_signed'); ?>
                                    </a>
                                </li>
                                <?php } elseif ($contract->signed == 0 && $contract->marked_as_signed == 1 && staff_can('edit', 'contracts')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('contracts/unmark_as_signed/' . $contract->id); ?>">
                                        <?= _l('unmark_as_signed'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php hooks()->do_action('after_contract_view_as_client_link', $contract); ?>
                                <?php if (staff_can('create', 'contracts')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('contracts/copy/' . $contract->id); ?>">
                                        <?= _l('contract_copy'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($contract->signed == 1 && staff_can('delete', 'contracts')) { ?>
                                <li>
                                    <a href="<?= admin_url('contracts/clear_signature/' . $contract->id); ?>"
                                        class="_delete">
                                        <?= _l('clear_signature'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (staff_can('delete', 'contracts')) { ?>
                                <li>
                                    <a href="<?= admin_url('contracts/delete/' . $contract->id); ?>"
                                        class="_delete">
                                        <?= _l('delete'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php if (isset($contract)) { ?>
            <div class="horizontal-scrollable-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-segmented tw-mb-3" role="tablist">
                        <li role="presentation"
                            class="<?= ! $this->input->get('tab') ? 'active' : ''; ?>">
                            <a href="#tab_info" aria-controls="tab_info" role="tab" data-toggle="tab">
                                <?= _l('contract_information'); ?>
                            </a>
                        </li>
                        <li role="presentation"
                            class="<?= $this->input->get('tab') == 'tab_content' ? 'active' : ''; ?>">
                            <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
                                <?= _l('contract_content'); ?>
                            </a>
                        </li>
                        <li role="presentation"
                            class="<?= $this->input->get('tab') == 'attachments' ? 'active' : ''; ?>">
                            <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                                <?= _l('contract_attachments'); ?>
                                <?php if ($totalAttachments = count($contract->attachments)) { ?>
                                <span class="badge attachments-indicator">
                                    <?= e($totalAttachments); ?>
                                </span>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_comments" aria-controls="tab_comments" role="tab" data-toggle="tab"
                                onclick="get_contract_comments(); return false;">
                                <?= _l('contract_comments'); ?>
                                <?php $totalComments = total_rows(db_prefix() . 'contract_comments', 'contract_id=' . $contract->id); ?>
                                <span
                                    class="badge comments-indicator<?= $totalComments == 0 ? ' hide' : ''; ?>">
                                    <?= e($totalComments); ?>
                                </span>
                            </a>
                        </li>
                        <li role="presentation"
                            class="<?= $this->input->get('tab') == 'renewals' ? 'active' : ''; ?>">
                            <a href="#renewals" aria-controls="renewals" role="tab" data-toggle="tab">
                                <?= _l('no_contract_renewals_history_heading'); ?>
                                <?php if ($totalRenewals = count($contract_renewal_history)) { ?>
                                <span class="badge">
                                    <?= e($totalRenewals); ?>
                                </span>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab"
                                onclick="init_rel_tasks_table(<?= e($contract->id); ?>,'contract'); return false;">
                                <?= _l('tasks'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_notes"
                                onclick="get_sales_notes(<?= e($contract->id); ?>,'contracts'); return false"
                                aria-controls="tab_notes" role="tab" data-toggle="tab">
                                <?= _l('contract_notes'); ?>
                                <span class="notes-total">
                                    <?php if ($totalNotes > 0) { ?>
                                    <span class="badge">
                                        <?= e($totalNotes); ?>
                                    </span>
                                    <?php } ?>
                                </span>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_templates"
                                onclick="get_templates('contracts', <?= $contract->id ?>); return false"
                                aria-controls="tab_templates" role="tab" data-toggle="tab">
                                <?= _l('templates');
                $conditions = ['type' => 'contracts'];
                if (staff_cant('view_all_templates', 'contracts')) {
                    $conditions['addedfrom'] = get_staff_user_id();
                    $conditions['type']      = 'contracts';
                }
                $total_templates = total_rows(db_prefix() . 'templates', $conditions);
                ?>
                                <span
                                    class="badge total_templates <?= $total_templates === 0 ? 'hide' : ''; ?>">
                                    <?= $total_templates ?>
                                </span>
                            </a>
                        </li>
                        <li role="presentation" class="tw-ml-auto" data-toggle="tooltip"
                            title="<?= _l('emails_tracking'); ?>">
                            <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab"
                                data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa-regular fa-envelope-open" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('emails_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="tab-content">
                        <div role="tabpanel"
                            class="tab-pane<?= ! $this->input->get('tab') ? ' active' : ''; ?>"
                            id="tab_info">

                            <?= form_open($this->uri->uri_string(), ['id' => 'contract-form']); ?>
                            <div class="form-group">
                                <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                                    <input type="checkbox" id="trash" name="trash"
                                        <?= $contract->trash ?? false == 1 ? 'checked' : ''; ?>>
                                    <label for="trash"><i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                            data-placement="right"
                                            title="<?= _l('contract_trash_tooltip'); ?>"></i>
                                        <?= _l('contract_trash'); ?></label>
                                </div>
                                <div class="checkbox checkbox-primary checkbox-inline">
                                    <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client"
                                        <?= $contract->not_visible_to_client ?? false == 1 ? 'checked' : ''; ?>>
                                    <label for="not_visible_to_client">
                                        <?= _l('contract_not_visible_to_client'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group select-placeholder f_client_id">
                                <label for="clientid" class="control-label"><span class="text-danger">*
                                    </span><?= _l('contract_client_string'); ?></label>
                                <select id="clientid" name="client" data-live-search="true" data-width="100%"
                                    class="ajax-search"
                                    data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                    <?= isset($contract) && $isSignedOrMarkedSigned ? ' disabled' : ''; ?>>
                                    <?php $selected = (isset($contract) ? $contract->client : ($customer_id ?? '')); ?>
                                    <?php if ($selected != '') {
                                        $rel_data = get_relation_data('customer', $selected);
                                        $rel_val  = get_relation_values($rel_data, 'customer');
                                        echo '<option value="' . $rel_val['id'] . '" selected>' . e($rel_val['name']) . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <div
                                class="form-group select-placeholder projects-wrapper<?= ((! isset($contract)) || (isset($contract) && ! customer_has_projects($contract->client))) ? ' hide' : ''; ?>">
                                <label for="project_id">
                                    <?= _l('project'); ?>
                                </label>

                                <div id="project_ajax_search_wrapper">
                                    <select name="project_id" id="project_id" class="projects ajax-search ays-ignore"
                                        data-live-search="true" data-width="100%"
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                        <?= isset($contract) && $isSignedOrMarkedSigned == 1 ? ' disabled' : ''; ?>>
                                        <?php if (isset($contract) && $contract->project_id) { ?>
                                        <option
                                            value="<?= $contract->project_id; ?>"
                                            selected>
                                            <?= e(get_project_name_by_id($contract->project_id)); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <?php $value = (isset($contract) ? $contract->subject : ''); ?>
                            <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                                title="<?= _l('contract_subject_tooltip'); ?>"></i>
                            <?= render_input('subject', 'contract_subject', $value); ?>
                            <div class="form-group">
                                <label
                                    for="contract_value"><?= _l('contract_value'); ?></label>
                                <div class="input-group" data-toggle="tooltip"
                                    title="<?= isset($contract) && $isSignedOrMarkedSigned == 1 ? '' : _l('contract_value_tooltip'); ?>">
                                    <input type="number" class="form-control" name="contract_value"
                                        value="<?= $contract->contract_value ?? ''; ?>"
                                        <?= isset($contract) && $isSignedOrMarkedSigned == 1 ? ' disabled' : ''; ?>>
                                    <div class="input-group-addon">
                                        <?= e($base_currency->symbol); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                                                $selected = (isset($contract) ? $contract->contract_type : '');
if (is_admin() || get_option('staff_members_create_inline_contract_types') == '1') {
    echo render_select_with_input_group('contract_type', $types, ['id', 'name'], 'contract_type', $selected, '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_type();return false;"><i class="fa fa-plus"></i></a></div>');
} else {
    echo render_select('contract_type', $types, ['id', 'name'], 'contract_type', $selected);
}
?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? _d($contract->datestart) : _d(date('Y-m-d'))); ?>
                                    <?= render_date_input(
                                        'datestart',
                                        'contract_start_date',
                                        $value,
                                        isset($contract) && $isSignedOrMarkedSigned ? ['disabled' => true] : []
                                    ); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($contract) ? _d($contract->dateend) : ''); ?>
                                    <?= render_date_input(
                                        'dateend',
                                        'contract_end_date',
                                        $value,
                                        isset($contract) && $isSignedOrMarkedSigned ? ['disabled' => true] : []
                                    ); ?>
                                </div>
                            </div>
                            <?php $value = (isset($contract) ? $contract->description : ''); ?>
                            <?= render_textarea('description', 'contract_description', $value, ['rows' => 6]); ?>
                            <?php $rel_id = (isset($contract) ? $contract->id : false); ?>
                            <?= render_custom_fields('contracts', $rel_id); ?>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <?= _l('submit'); ?>
                                </button>
                            </div>
                            <?= form_close(); ?>

                        </div>
                        <?php if (isset($contract)) { ?>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_content' ? ' active' : ''; ?>"
                            id="tab_content">
                            <?php if ($contract->signed == 1) { ?>
                            <div class="alert alert-success">
                                <?= _l(
                                    'document_signed_info',
                                    [
                                        '<b>' . e($contract->acceptance_firstname) . ' ' . e($contract->acceptance_lastname) . '</b> (<a href="mailto:' . e($contract->acceptance_email) . '" class="alert-link">' . e($contract->acceptance_email) . '</a>)',
                                        '<b>' . e(_dt($contract->acceptance_date)) . '</b>',
                                        '<b>' . e($contract->acceptance_ip) . '</b>', ]
                                ); ?>
                            </div>
                            <?php } elseif ($contract->marked_as_signed == 1) { ?>
                            <div class="alert alert-info">
                                <?= _l('contract_marked_as_signed_info'); ?>
                            </div>
                            <?php } ?>
                            <?php if (isset($contract_merge_fields)) { ?>
                            <p class="bold text-right no-mbot"><a href="#"
                                    onclick="slideToggle('.avilable_merge_fields'); return false;">
                                    <?= _l('available_merge_fields'); ?>
                                </a>
                            </p>
                            <div class="avilable_merge_fields mtop15 hide">
                                <ul class="list-group">
                                    <?php foreach ($contract_merge_fields as $field) {?>
                                    <?php foreach ($field as $f) { ?>
                                    <li class="list-group-item">
                                        <b><?= $f['name']; ?></b>
                                        <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">
                                            <?= $f['key']; ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>

                            <hr class="hr-panel-separator" />
                            <?php if (staff_cant('edit', 'contracts')) { ?>
                            <div class="alert alert-warning contract-edit-permissions">
                                <?= _l('contract_content_permission_edit_warning'); ?>
                            </div>
                            <?php } ?>
                            <div class="tc-content<?= staff_can('edit', 'contracts') && ! $isSignedOrMarkedSigned ? ' editable' : ''; ?>"
                                style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;">
                                <?php if (empty($contract->content) && staff_can('edit', 'contracts')) { ?>
                                <?= hooks()->apply_filters('new_contract_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>') ?>
                                <?php } else { ?>
                                <?= $contract->content; ?>
                                <?php } ?>
                            </div>
                            <?php if (! empty($contract->signature)) { ?>
                            <div class="row mtop25">
                                <div class="col-md-6 col-md-offset-6 text-right">
                                    <div class="bold">
                                        <p class="no-mbot">
                                            <?= e(_l('contract_signed_by') . ": {$contract->acceptance_firstname} {$contract->acceptance_lastname}"); ?>
                                        </p>
                                        <p class="no-mbot">
                                            <?= e(_l('contract_signed_date') . ': ' . _dt($contract->acceptance_date)); ?>
                                        </p>
                                        <p class="no-mbot">
                                            <?= e(_l('contract_signed_ip') . ": {$contract->acceptance_ip}"); ?>
                                        </p>
                                    </div>
                                    <p class="bold">
                                        <?= _l('document_customer_signature_text'); ?>
                                        <?php if ($contract->signed == 1 && staff_can('delete', 'contracts')) { ?>
                                        <a href="<?= admin_url('contracts/clear_signature/' . $contract->id); ?>"
                                            data-toggle="tooltip"
                                            title="<?= _l('clear_signature'); ?>"
                                            class="_delete text-danger">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                        <?php } ?>
                                    </p>
                                    <div class="pull-right">
                                        <img src="<?= site_url('download/preview_image?path=' . protected_file_url_by_path(get_upload_path_by_type('contract') . $contract->id . '/' . $contract->signature)); ?>"
                                            class="img-responsive" alt="">
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'attachments' ? ' active' : ''; ?>"
                            id="attachments">
                            <?= form_open(admin_url('contracts/add_contract_attachment/' . $contract->id), ['id' => 'contract-attachments-form', 'class' => 'dropzone mtop15']); ?>
                            <?= form_close(); ?>
                            <div class="tw-flex tw-justify-end tw-items-center tw-space-x-2 mtop15">
                                <button class="gpicker" data-on-pick="contractGoogleDriveSave">
                                    <i class="fa-brands fa-google" aria-hidden="true"></i>
                                    <?= _l('choose_from_google_drive'); ?>
                                </button>
                                <div id="dropbox-chooser"></div>
                            </div>
                            <!-- <img src="https://drive.google.com/uc?id=14mZI6xBjf-KjZzVuQe8-rjtv_wXEbDTw" /> -->

                            <div id="contract_attachments" class="mtop30">
                                <?php
            $data = '<div class="row">';

                            foreach ($contract->attachments as $attachment) {
                                $href_url = site_url('download/file/contract/' . $attachment['attachment_key']);
                                if (! empty($attachment['external'])) {
                                    $href_url = $attachment['external_link'];
                                }
                                $data .= '<div class="display-block contract-attachment-wrapper">';
                                $data .= '<div class="col-md-10">';
                                $data .= '<div class="pull-left"><i class="' . get_mime_class($attachment['filetype']) . '"></i></div>';
                                $data .= '<a href="' . $href_url . '"' . (! empty($attachment['external']) ? ' target="_blank"' : '') . '>' . $attachment['file_name'] . '</a>';
                                $data .= '<p class="text-muted">' . $attachment['filetype'] . '</p>';
                                $data .= '</div>';
                                $data .= '<div class="col-md-2 text-right">';
                                if ($attachment['staffid'] == get_staff_user_id() || is_admin()) {
                                    $data .= '<a href="#" class="text-muted" onclick="delete_contract_attachment(this,' . $attachment['id'] . '); return false;"><i class="fa-regular fa-trash-can"></i></a>';
                                }
                                $data .= '</div>';
                                $data .= '<div class="clearfix"></div><hr/>';
                                $data .= '</div>';
                            }
                            $data .= '</div>';
                            echo $data;
                            ?>
                            </div>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_comments' ? ' active' : ''; ?>"
                            id="tab_comments">
                            <div class="contract-comments">
                                <div id="contract-comments"></div>
                                <div class="clearfix"></div>
                                <textarea name="content" id="comment" rows="4"
                                    class="form-control mtop15 contract-comment"></textarea>
                                <button type="button" class="btn btn-primary mtop10 pull-right"
                                    onclick="add_contract_comment();"><?= _l('proposal_add_comment'); ?></button>
                            </div>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'renewals' ? ' active' : ''; ?>"
                            id="renewals">
                            <?php if (staff_can('edit', 'contracts')) { ?>
                            <div class="_buttons">
                                <a href="#" class="btn btn-primary" data-toggle="modal"
                                    data-target="#renew_contract_modal">
                                    <i class="fa fa-refresh"></i>
                                    <?= _l('contract_renew_heading'); ?>
                                </a>
                            </div>
                            <hr />
                            <?php } ?>

                            <div class="clearfix"></div>

                            <?php if (count($contract_renewal_history) == 0) {
                                echo '<p class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('no_contract_renewals_found') . '</p>';
                            } ?>

                            <?php foreach ($contract_renewal_history as $renewal) { ?>
                            <div class="display-block">
                                <div class="media-body">
                                    <div class="display-block">
                                        <b>
                                            <?= e(_l('contract_renewed_by', $renewal['renewed_by'])); ?>
                                        </b>
                                        <?php if ($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()) { ?>
                                        <a href="<?= admin_url('contracts/delete_renewal/' . $renewal['id'] . '/' . $renewal['contractid']); ?>"
                                            class="pull-right _delete text-muted">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                        <br />
                                        <?php } ?>
                                        <small
                                            class="text-muted"><?= e(_dt($renewal['date_renewed'])); ?></small>
                                        <hr class="hr-10" />
                                        <span class="text-success bold" data-toggle="tooltip"
                                            title="<?= e(_l('contract_renewal_old_start_date', _d($renewal['old_start_date']))); ?>">
                                            <?= e(_l('contract_renewal_new_start_date', _d($renewal['new_start_date']))); ?>
                                        </span>
                                        <br />
                                        <?php if (is_date($renewal['new_end_date'])) {
                                            $tooltip = '';
                                            if (is_date($renewal['old_end_date'])) {
                                                $tooltip = e(_l('contract_renewal_old_end_date', _d($renewal['old_end_date'])));
                                            } ?>
                                        <span class="text-success bold" data-toggle="tooltip"
                                            title="<?= e($tooltip); ?>">
                                            <?= e(_l('contract_renewal_new_end_date', _d($renewal['new_end_date']))); ?>
                                        </span>
                                        <br />
                                        <?php } ?>
                                        <?php if ($renewal['new_value'] > 0) {
                                            $contract_renewal_value_tooltip = '';
                                            if ($renewal['old_value'] > 0) {
                                                $contract_renewal_value_tooltip = ' data-toggle="tooltip" data-title="' . e(_l('contract_renewal_old_value', app_format_money($renewal['old_value'], $base_currency))) . '"';
                                            } ?>
                                        <span class="text-success bold"
                                            <?= e($contract_renewal_value_tooltip); ?>>
                                            <?= e(_l('contract_renewal_new_value', app_format_money($renewal['new_value'], $base_currency))); ?>
                                        </span>
                                        <br />
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr />
                            </div>
                            <?php } ?>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_tasks' ? ' active' : ''; ?>"
                            id="tab_tasks">
                            <?php init_relation_tasks_table(['data-new-rel-id' => $contract->id, 'data-new-rel-type' => 'contract']); ?>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_notes' ? ' active' : ''; ?>"
                            id="tab_notes">
                            <?= form_open(admin_url('contracts/add_note/' . $contract->id), ['id' => 'sales-notes', 'class' => 'contract-notes-form mtop15']); ?>
                            <?= render_textarea('description'); ?>
                            <div class="text-right">
                                <button type="submit"
                                    class="btn btn-primary mtop15 mbot15"><?= _l('contract_add_note'); ?></button>
                            </div>
                            <?= form_close(); ?>
                            <hr />
                            <div class="mtop20" id="sales_notes_area"></div>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_templates' ? ' active' : ''; ?>"
                            id="tab_templates">
                            <div class="contract-templates">
                                <button type="button" class="btn btn-primary"
                                    onclick="add_template('contracts', <?= $contract->id ?>);">
                                    <?= _l('add_template'); ?>
                                </button>
                                <hr>
                                <div id="contract-templates" class="contract-templates-wrapper"></div>
                            </div>
                        </div>
                        <div role="tabpanel"
                            class="tab-pane<?= $this->input->get('tab') == 'tab_emails_tracking' ? ' active' : ''; ?>"
                            id="tab_emails_tracking">
                            <?php $this->load->view('admin/includes/emails_tracking', [
                                'tracked_emails' => get_tracked_emails($contract->id, 'contract'),
                            ]); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-wrapper"></div>
<?php init_tail(); ?>
<?php if (isset($contract)) { ?>
<!-- init table tasks -->
<script>
    var contract_id = '<?= $contract->id; ?>';
</script>
<?php $this->load->view('admin/contracts/send_to_client'); ?>
<?php $this->load->view('admin/contracts/renew_contract'); ?>
<?php } ?>
<?php $this->load->view('admin/contracts/contract_type'); ?>
<script>
    Dropzone.autoDiscover = false;
    $(function() {
        init_ajax_project_search_by_customer_id();

        if ($('#contract-attachments-form').length > 0) {
            new Dropzone("#contract-attachments-form", appCreateDropzoneOptions({
                success: function(file) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles()
                        .length ===
                        0) {
                        var location = window.location.href;
                        window.location.href = location.split('?')[0] + '?tab=attachments';
                    }
                }
            }));
        }

        if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
            document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
                success: function(files) {
                    $.post(admin_url + 'contracts/add_external_attachment', {
                        files: files,
                        contract_id: contract_id,
                        external: 'dropbox'
                    }).done(function() {
                        var location = window.location.href;
                        window.location.href = location.split('?')[0] +
                            '?tab=attachments';
                    });
                },
                linkType: "preview",
                extensions: app.options.allowed_files.split(','),
            }));
        }

        appValidateForm($('#contract-form'), {
            client: 'required',
            datestart: 'required',
            subject: 'required'
        });

        appValidateForm($('#renew-contract-form'), {
            new_start_date: 'required'
        });

        init_tinymce_inline_editor({
            saveUsing: save_contract_content,
        })
    });

    function save_contract_content(manual) {
        var editor = tinyMCE.activeEditor;
        var data = {};
        data.contract_id = contract_id;
        data.content = editor.getContent();
        $.post(admin_url + 'contracts/save_contract_data', data).done(function(response) {
            response = JSON.parse(response);
            if (typeof(manual) != 'undefined') {
                // Show some message to the user if saved via CTRL + S
                alert_float('success', response.message);
            }
            // Invokes to set dirty to false
            editor.save();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function delete_contract_attachment(wrapper, id) {
        if (confirm_delete()) {
            $.get(admin_url + 'contracts/delete_contract_attachment/' + id, function(response) {
                if (response.success == true) {
                    $(wrapper).parents('.contract-attachment-wrapper').remove();

                    var totalAttachmentsIndicator = $('.attachments-indicator');
                    var totalAttachments = totalAttachmentsIndicator.text().trim();
                    if (totalAttachments == 1) {
                        totalAttachmentsIndicator.remove();
                    } else {
                        totalAttachmentsIndicator.text(totalAttachments - 1);
                    }
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
        return false;
    }

    function insert_merge_field(field) {
        var key = $(field).text();
        tinymce.activeEditor.execCommand('mceInsertContent', false, key);
    }

    function add_contract_comment() {
        var comment = $('#comment').val();
        if (comment == '') {
            return;
        }
        var data = {};
        data.content = comment;
        data.contract_id = contract_id;
        $('body').append('<div class="dt-loader"></div>');
        $.post(admin_url + 'contracts/add_comment', data).done(function(response) {
            response = JSON.parse(response);
            $('body').find('.dt-loader').remove();
            if (response.success == true) {
                $('#comment').val('');
                get_contract_comments();
            }
        });
    }

    function get_contract_comments() {
        if (typeof(contract_id) == 'undefined') {
            return;
        }
        requestGet('contracts/get_comments/' + contract_id).done(function(response) {
            $('#contract-comments').html(response);
            var totalComments = $('[data-commentid]').length;
            var commentsIndicator = $('.comments-indicator');
            if (totalComments == 0) {
                commentsIndicator.addClass('hide');
            } else {
                commentsIndicator.removeClass('hide');
                commentsIndicator.text(totalComments);
            }
        });
    }

    function remove_contract_comment(commentid) {
        if (confirm_delete()) {
            requestGetJSON('contracts/remove_comment/' + commentid).done(function(response) {
                if (response.success == true) {

                    var totalComments = $('[data-commentid]').length;

                    $('[data-commentid="' + commentid + '"]').remove();

                    var commentsIndicator = $('.comments-indicator');
                    if (totalComments - 1 == 0) {
                        commentsIndicator.addClass('hide');
                    } else {
                        commentsIndicator.removeClass('hide');
                        commentsIndicator.text(totalComments - 1);
                    }
                }
            });
        }
    }

    function edit_contract_comment(id) {
        var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
        if (content != '') {
            $.post(admin_url + 'contracts/edit_comment/' + id, {
                content: content
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                    $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
                }
            });
            toggle_contract_comment_edit(id);
        }
    }

    function toggle_contract_comment_edit(id) {
        $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
        $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
    }

    function contractGoogleDriveSave(pickData) {
        var data = {};
        data.contract_id = contract_id;
        data.external = 'gdrive';
        data.files = pickData;
        $.post(admin_url + 'contracts/add_external_attachment', data).done(function() {
            var location = window.location.href;
            window.location.href = location.split('?')[0] + '?tab=attachments';
        });
    }
</script>
</body>

</html>