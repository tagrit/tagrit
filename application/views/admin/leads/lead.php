<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="tw-flex tw-justify-between tw-items-center">
        <h4 class="modal-title tw-flex tw-items-center">
            <?php if (isset($lead)) { ?>
            <?php $name = ! empty($lead->name) ? $lead->name : (! empty($lead->company) ? $lead->company : _l('lead')); ?>
            #<?= $lead->id; ?> -
            <?= $name; ?>
            <div class="tw-ml-3 -tw-mt-px">
                <?php if ($lead->lost == 1) { ?>
                <span
                    class="label label-danger"><?= _l('lead_lost'); ?></span>
                <?php } elseif ($lead->junk == 1) { ?>
                <span
                    class="label label-warning"><?= _l('lead_junk'); ?></span>
                <?php } elseif (total_rows(db_prefix() . 'clients', ['leadid' => $lead->id])) { ?>
                <span
                    class="label label-success"><?= _l('lead_is_client'); ?></span>
                <?php } ?>
            </div>
            <?php } else { ?>
            <?= _l('add_new', _l('lead_lowercase')); ?>
            <?php } ?>
        </h4>

        <?php if (isset($lead)) { ?>
        <a href="#"
            class="lead-print-btn tw-text-neutral-500 hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-mt-1 tw-space-x-1.5 tw-mx-4"
            onclick="print_lead_information(); return false;">
            <i class="fa-solid fa-print"></i>
            <span><?= _l('print'); ?></span>
        </a>
        <?php } ?>
    </div>

</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($lead)) {
                echo form_hidden('leadid', $lead->id);
            } ?>
            <div class="top-lead-menu">
                <?php if (isset($lead)) { ?>
                <div class="horizontal-scrollable-tabs tw-mb-10">
                    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-segmented<?= ! isset($lead) ? ' lead-new' : ''?>"
                            role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_lead_profile" aria-controls="tab_lead_profile" role="tab"
                                    data-toggle="tab">
                                    <i class="fa-regular fa-user menu-icon"></i>
                                    <?= _l('lead_profile'); ?>
                                </a>
                            </li>
                            <?php if (isset($lead)) { ?>
                            <?php if (count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity) { ?>
                            <li role="presentation">
                                <a href="#tab_email_activity" aria-controls="tab_email_activity" role="tab"
                                    data-toggle="tab">
                                    <i class="fa-regular fa-envelope menu-icon"></i>
                                    <?= hooks()->apply_filters('lead_email_activity_subject', _l('lead_email_activity')); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <li role="presentation">
                                <a href="#tab_proposals_leads"
                                    onclick="initDataTable('.table-proposals-lead', admin_url + 'proposals/proposal_relations/' + <?= e($lead->id); ?> + '/lead','undefined', 'undefined','undefined',[6,'desc']);"
                                    aria-controls="tab_proposals_leads" role="tab" data-toggle="tab">
                                    <i class="fa-regular fa-file-lines menu-icon"></i>
                                    <?= _l('proposals');
                                if ($total_proposals > 0) {
                                    echo ' <span class="badge">' . $total_proposals . '</span>';
                                }
                                ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_tasks_leads"
                                    onclick="init_rel_tasks_table(<?= e($lead->id); ?>,'lead','.table-rel-tasks-leads');"
                                    aria-controls="tab_tasks_leads" role="tab" data-toggle="tab">
                                    <i class="fa-regular fa-circle-check menu-icon"></i>
                                    <?= _l('tasks');
                                if ($total_tasks > 0) {
                                    echo ' <span class="badge">' . $total_tasks . '</span>';
                                }
                                ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                                    <i class="fa-solid fa-paperclip menu-icon"></i>
                                    <?= _l('lead_attachments');
                                if ($total_attachments > 0) {
                                    echo ' <span class="badge">' . $total_attachments . '</span>';
                                }
                                ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_reminders"
                                    onclick="initDataTable('.table-reminders-leads', admin_url + 'misc/get_reminders/' + <?= e($lead->id); ?> + '/' + 'lead', undefined, undefined,undefined,[1, 'asc']);"
                                    aria-controls="lead_reminders" role="tab" data-toggle="tab">
                                    <i class="fa-regular fa-bell menu-icon"></i>
                                    <?= _l('leads_reminders_tab');
                                if ($total_reminders > 0) {
                                    echo ' <span class="badge">' . $total_reminders . '</span>';
                                }
                                ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_notes" aria-controls="lead_notes" role="tab" data-toggle="tab">
                                    <i class="fa-regular fa-note-sticky menu-icon"></i>
                                    <?= _l('lead_add_edit_notes');
                                if ($total_notes > 0) {
                                    echo ' <span class="badge">' . $total_notes . '</span>';
                                }
                                ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_activity" aria-controls="lead_activity" role="tab" data-toggle="tab">
                                    <i class="fa-solid fa-grip-lines-vertical menu-icon"></i>
                                    <?= _l('lead_add_edit_activity'); ?>
                                </a>
                            </li>
                            <?php if (is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1')) { ?>
                            <li role="presentation">
                                <a href="#gdpr" aria-controls="gdpr" role="tab" data-toggle="tab">
                                    <i class="fa-solid fa-gavel menu-icon"></i>
                                    <?= _l('gdpr_short'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php } ?>
                            <?php hooks()->do_action('after_lead_lead_tabs', $lead ?? null); ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- from leads modal -->
                <div role="tabpanel" class="tab-pane active" id="tab_lead_profile">
                    <?php $this->load->view('admin/leads/profile'); ?>
                </div>
                <?php if (isset($lead)) { ?>
                <?php if (count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_email_activity">
                    <?php hooks()->do_action('before_lead_email_activity', ['lead' => $lead, 'email_activity' => $mail_activity]); ?>
                    <?php foreach ($mail_activity as $_mail_activity) { ?>
                    <div class="lead-email-activity">
                        <div class="media-left">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="bold no-margin lead-mail-activity-subject">
                                <?= e($_mail_activity['subject']); ?>
                                <br />
                                <small
                                    class="text-muted display-block mtop5 font-medium-xs"><?= e(_dt($_mail_activity['dateadded'])); ?></small>
                            </h4>
                            <div class="lead-mail-activity-body">
                                <hr />
                                <?= process_text_content_for_display($_mail_activity['body']); ?>
                            </div>
                            <hr />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <?php } ?>
                    <?php hooks()->do_action('after_lead_email_activity', ['lead_id' => $lead->id, 'emails' => $mail_activity]); ?>
                </div>
                <?php } ?>
                <?php if (is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1' || (get_option('gdpr_data_portability_leads') == '1') && is_admin())) { ?>
                <div role="tabpanel" class="tab-pane" id="gdpr">
                    <?php if (get_option('gdpr_enable_lead_public_form') == '1') { ?>
                    <a href="<?= e($lead->public_url); ?>"
                        target="_blank" class="mtop5">
                        <?= _l('view_public_form'); ?>
                    </a>
                    <?php } ?>
                    <?php if (get_option('gdpr_data_portability_leads') == '1' && is_admin()) { ?>
                    <?php
                                if (get_option('gdpr_enable_lead_public_form') == '1') {
                                    echo ' | ';
                                }
                        ?>
                    <a
                        href="<?= admin_url('leads/export/' . $lead->id); ?>">
                        <?= _l('dt_button_export'); ?>
                    </a>
                    <?php } ?>
                    <?php if (get_option('gdpr_enable_lead_public_form') == '1' || (get_option('gdpr_data_portability_leads') == '1' && is_admin())) { ?>
                    <hr class="-tw-mx-3.5" />
                    <?php } ?>
                    <?php if (get_option('gdpr_enable_consent_for_leads') == '1') { ?>
                    <h4 class="no-mbot">
                        <?= _l('gdpr_consent'); ?>
                    </h4>
                    <?php $this->load->view('admin/gdpr/lead_consent'); ?>
                    <hr />
                    <?php } ?>
                </div>
                <?php } ?>
                <div role="tabpanel" class="tab-pane" id="lead_activity">
                    <div>
                        <div class="activity-feed">
                            <?php foreach ($activity_log as $log) { ?>
                            <div class="feed-item">
                                <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip"
                                        data-title="<?= e(_dt($log['date'])); ?>">
                                        <?= e(time_ago($log['date'])); ?>
                                    </span>
                                </div>
                                <div class="text">
                                    <?php if ($log['staffid'] != 0) { ?>
                                    <a
                                        href="<?= admin_url('profile/' . $log['staffid']); ?>">
                                        <?= staff_profile_image($log['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                        ?>
                                    </a>
                                    <?php
                                    }
                                $additional_data = '';
                                if (! empty($log['additional_data'])) {
                                    $additional_data = unserialize($log['additional_data']);
                                    echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : e($log['full_name']) . ' - ' . _l($log['description'], $additional_data);
                                } else {
                                    echo e($log['full_name']) . ' - ';

                                    if ($log['custom_activity'] == 0) {
                                        echo e(_l($log['description']));
                                    } else {
                                        echo process_text_content_for_display(_l($log['description'], '', false));
                                    }
                                }
                                ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-12">
                            <?= render_textarea('lead_activity_textarea', '', '', ['placeholder' => _l('enter_activity')], [], 'mtop15'); ?>
                            <div class="text-right">
                                <button id="lead_enter_activity"
                                    class="btn btn-primary"><?= _l('submit'); ?></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_proposals_leads">
                    <?php if (staff_can('create', 'proposals')) { ?>
                    <a href="<?= admin_url('proposals/proposal?rel_type=lead&rel_id=' . $lead->id); ?>"
                        class="btn btn-primary mbot25"><?= _l('new_proposal'); ?></a>
                    <?php } ?>
                    <?php if (total_rows(db_prefix() . 'proposals', ['rel_type' => 'lead', 'rel_id' => $lead->id]) > 0 && (staff_can('create', 'proposals') || staff_can('edit', 'proposals'))) { ?>
                    <a href="#" class="btn btn-primary mbot25" data-toggle="modal"
                        data-target="#sync_data_proposal_data"><?= _l('sync_data'); ?></a>
                    <?php $this->load->view('admin/proposals/sync_data', ['related' => $lead, 'rel_id' => $lead->id, 'rel_type' => 'lead']); ?>
                    <?php } ?>
                    <?php
                        $table_data = [
                            _l('proposal') . ' #',
                            _l('proposal_subject'),
                            _l('proposal_total'),
                            _l('proposal_date'),
                            _l('proposal_open_till'),
                            _l('tags'),
                            _l('proposal_date_created'),
                            _l('proposal_status'),
                        ];
                    $custom_fields = get_custom_fields('proposal', ['show_on_table' => 1]);

                    foreach ($custom_fields as $field) {
                        array_push($table_data, [
                            'name'     => $field['name'],
                            'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                        ]);
                    }
                    $table_data = hooks()->apply_filters('proposals_relation_table_columns', $table_data);
                    render_datatable($table_data, 'proposals-lead', [], [
                        'data-last-order-identifier' => 'proposals-relation',
                        'data-default-order'         => get_table_last_order('proposals-relation'),
                    ]);
                    ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_tasks_leads">
                    <?php init_relation_tasks_table(['data-new-rel-id' => $lead->id, 'data-new-rel-type' => 'lead'], 'tasksFilters'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="lead_reminders">
                    <a href="#" data-toggle="modal" class="btn btn-primary"
                        data-target=".reminder-modal-lead-<?= e($lead->id); ?>"><i
                            class="fa-regular fa-bell"></i>
                        <?= _l('lead_set_reminder_title'); ?></a>
                    <hr />
                    <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders-leads'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="attachments">
                    <?= form_open('admin/leads/add_lead_attachment', ['class' => 'dropzone mtop15 mbot15', 'id' => 'lead-attachment-upload']); ?>
                    <?= form_close(); ?>
                    <?php if (get_option('dropbox_app_key') != '') { ?>
                    <hr />
                    <div class=" pull-left">
                        <?php if (count($lead->attachments) > 0) { ?>
                        <a href="<?= admin_url('leads/download_files/' . $lead->id); ?>"
                            class="bold">
                            <?= _l('download_all'); ?>
                            (.zip)
                        </a>
                        <?php } ?>
                    </div>
                    <div class="tw-flex tw-justify-end tw-items-center tw-space-x-2">
                        <button class="gpicker">
                            <i class="fa-brands fa-google" aria-hidden="true"></i>
                            <?= _l('choose_from_google_drive'); ?>
                        </button>
                        <div id="dropbox-chooser-lead"></div>
                    </div>
                    <div class=" clearfix"></div>
                    <?php } ?>
                    <?php if (count($lead->attachments) > 0) { ?>
                    <div class="mtop20" id="lead_attachments">
                        <?php $this->load->view('admin/leads/leads_attachments_template', ['attachments' => $lead->attachments]); ?>
                    </div>
                    <?php } ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="lead_notes">
                    <?= form_open(admin_url('leads/add_note/' . $lead->id), ['id' => 'lead-notes']); ?>
                    <div class="form-group">
                        <textarea id="lead_note_description" name="lead_note_description" class="form-control"
                            rows="4"></textarea>
                    </div>
                    <div class="lead-select-date-contacted hide">
                        <?= render_datetime_input('custom_contact_date', 'lead_add_edit_datecontacted', '', ['data-date-end-date' => date('Y-m-d')]); ?>
                    </div>
                    <div class="radio radio-primary">
                        <input type="radio" name="contacted_indicator" id="contacted_indicator_yes" value="yes">
                        <label
                            for="contacted_indicator_yes"><?= _l('lead_add_edit_contacted_this_lead'); ?></label>
                    </div>
                    <div class="radio radio-primary">
                        <input type="radio" name="contacted_indicator" id="contacted_indicator_no" value="no" checked>
                        <label
                            for="contacted_indicator_no"><?= _l('lead_not_contacted'); ?></label>
                    </div>
                    <button type="submit"
                        class="btn btn-primary pull-right"><?= _l('lead_add_edit_add_note'); ?></button>
                    <?= form_close(); ?>
                    <div class="clearfix"></div>
                    <hr />
                    <?php
                    $len = count($notes);
                    $i   = 0;

                    foreach ($notes as $note) { ?>
                    <div class="media lead-note">
                        <a href="<?= admin_url('profile/' . $note['addedfrom']); ?>"
                            target="_blank">
                            <?= staff_profile_image($note['addedfrom'], ['staff-profile-image-small', 'pull-left mright10']); ?>
                        </a>
                        <div class="media-body">
                            <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                            <a href="#" class="pull-right text-muted"
                                onclick="delete_lead_note(this,<?= e($note['id']); ?>, <?= e($lead->id); ?>);return false;">
                                <i class="fa-regular fa-trash-can"></i>
                            </a>

                            <a href="#" class="pull-right text-muted tw-mr-3"
                                onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <?php } ?>

                            <a href="<?= admin_url('profile/' . $note['addedfrom']); ?>"
                                target="_blank">
                                <h5 class="media-heading tw-font-semibold tw-mb-0">
                                    <?php if (! empty($note['date_contacted'])) { ?>
                                    <span data-toggle="tooltip"
                                        data-title="<?= e(_dt($note['date_contacted'])); ?>">
                                        <i class="fa fa-phone-square text-success" aria-hidden="true"></i>
                                    </span>
                                    <?php } ?>
                                    <?= e(get_staff_full_name($note['addedfrom'])); ?>
                                </h5>
                                <span class="tw-text-sm tw-text-neutral-500">
                                    <?= e(_l('lead_note_date_added', _dt($note['dateadded']))); ?>
                                </span>
                            </a>

                            <div data-note-description="<?= e($note['id']); ?>"
                                class="text-muted tw-leading-relaxed mtop10">
                                <?= process_text_content_for_display($note['description']); ?>
                            </div>
                            <div data-note-edit-textarea="<?= e($note['id']); ?>"
                                class="hide mtop15">
                                <?= render_textarea('note', '', $note['description']); ?>
                                <div class="text-right">
                                    <button type="button" class="btn btn-default"
                                        onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                                        <?= _l('cancel'); ?>
                                    </button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="edit_note(<?= e($note['id']); ?>);">
                                        <?= _l('update_note'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if ($i >= 0 && $i != $len - 1) {
                            echo '<hr />';
                        }
                        ?>
                    </div>
                    <?php $i++;
                    } ?>
                </div>
                <?php } ?>
                <?php hooks()->do_action('after_lead_tabs_content', $lead ?? null); ?>
            </div>
        </div>
    </div>
</div>
<?php hooks()->do_action('lead_modal_profile_bottom', (isset($lead) ? $lead->id : '')); ?>