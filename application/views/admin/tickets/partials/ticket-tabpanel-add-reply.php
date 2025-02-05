<div role="tabpanel"
    class="tab-pane<?= ! $this->session->flashdata('active_tab') ? ' active' : ''; ?>"
    id="addreply">

    <?php hooks()->do_action('before_admin_ticket_addreply_tabpanel_content', $ticket); ?>

    <?php if (count($ticket->ticket_notes) > 0) { ?>
    <div class="tw-mb-4">
        <div class="ticketstaffnotes tw-mb-1 tw-inline-block tw-w-full tw-space-y-2">
            <?php foreach ($ticket->ticket_notes as $note) { ?>
            <div class="tw-rounded-lg tw-bg-neutral-50 tw-group">
                <div class="tw-px-4 tw-py-5 tw-sm:p-6">
                    <h3
                        class="tw-text-base tw-my-0 tw-ml-1 tw-font-bold tw-leading-6 tw-text-gray-900 tw-flex tw-items-center tw-gap-x-2">
                        <i class="fa-solid fa-note-sticky"></i>
                        <span>
                            <?= _l('ticket_single_note_heading'); ?>
                        </span>
                    </h3>
                    <div class="tw-flex tw-mt-2">
                        <div class="tw-flex-shrink-0">
                            <?= staff_profile_image($note['addedfrom'], ['staff-profile-xs-image']); ?>
                        </div>
                        <div class="ltr:tw-ml-2 rtl:tw-mr-2 tw-flex-1">
                            <div class="tw-flex">
                                <div class="tw-grow">
                                    <a href="<?= admin_url('staff/profile/' . $note['addedfrom']); ?>"
                                        class="text-muted tw-text-neutral-900 tw-font-semibold tw-text-sm">
                                        <?= e(_l('ticket_single_ticket_note_by', get_staff_full_name($note['addedfrom']))); ?>
                                    </a>
                                    <p class="tw-text-sm tw-text-neutral-500 tw-mb-0 -tw-mt-0.5">
                                        <?= e(_l('ticket_single_note_added', _dt($note['dateadded']))); ?>
                                    </p>
                                </div>
                                <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                <div class="tw-space-x-2 tw-hidden group-hover:tw-block rtl:tw-space-x-reverse">
                                    <a href="#" class="text-muted"
                                        onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <a href="<?= admin_url('misc/delete_note/' . $note['id']); ?>"
                                        class="text-muted _delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="tw-mt-2 tw-text-base tw-text-neutral-500">
                                <div
                                    data-note-description="<?= e($note['id']); ?>">
                                    <?= process_text_content_for_display($note['description']); ?>
                                </div>
                                <div data-note-edit-textarea="<?= e($note['id']); ?>"
                                    class="hide">
                                    <textarea name="description" class="form-control"
                                        rows="4"><?= clear_textarea_breaks($note['description']); ?></textarea>
                                    <div class="text-right tw-mt-3">
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
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <div>
        <?= form_open_multipart($this->uri->uri_string(), ['id' => 'single-ticket-form', 'novalidate' => true]); ?>
        <?= form_hidden('ticketid', $ticket->ticketid); ?>
        <div class="">
            <?php $use_knowledge_base = get_option('use_knowledge_base'); ?>
            <div class="row mbot15">
                <div class="col-md-6">
                    <select data-width="100%" id="insert_predefined_reply" data-live-search="true" class="selectpicker"
                        data-title="<?= _l('ticket_single_insert_predefined_reply'); ?>">
                        <?php foreach ($predefined_replies as $predefined_reply) { ?>
                        <option
                            value="<?= e($predefined_reply['id']); ?>">
                            <?= e($predefined_reply['name']); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <?php if ($use_knowledge_base == 1) { ?>
                <div class="visible-xs">
                    <div class="mtop15"></div>
                </div>
                <div class="col-md-6">
                    <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                    <select data-width="100%" id="insert_knowledge_base_link" class="selectpicker"
                        data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);"
                        data-title="<?= _l('ticket_single_insert_knowledge_base_link'); ?>">
                        <option value=""></option>
                        <?php foreach ($groups as $group) { ?>
                        <?php if (count($group['articles']) > 0) { ?>
                        <optgroup
                            label="<?= e($group['name']); ?>">
                            <?php foreach ($group['articles'] as $article) { ?>
                            <option
                                value="<?= e($article['articleid']); ?>">
                                <?= e($article['subject']); ?>
                            </option>
                            <?php } ?>
                        </optgroup>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
            </div>
            <?= render_textarea('message', '', '', ['placeholder' => _l('ticket_single_add_reply')], [], '', 'tinymce'); ?>

        </div>
        <div class="ticket-reply-tools">
            <div>
                <div class="row">
                    <div class="col-md-5">
                        <?php if ($ticket->merged_ticket_id === null) { ?>
                        <div class="row attachments">
                            <div class="attachment">
                                <div class="col-md-12 mbot15">
                                    <div class="form-group">
                                        <label for="attachment" class="control-label">
                                            <?= _l('ticket_single_attachments'); ?>
                                        </label>
                                        <div class="input-group">
                                            <input type="file"
                                                extension="<?= str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>"
                                                filesize="<?= file_upload_max_size(); ?>"
                                                class="form-control" name="attachments[0]"
                                                accept="<?= get_ticket_form_accepted_mimes(); ?>">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default add_more_attachments"
                                                    data-max="<?= get_option('maximum_allowed_ticket_attachments'); ?>"
                                                    type="button">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?= render_input('cc', 'CC', $ticket->cc); ?>
                        <?php if ($ticket->assigned !== get_staff_user_id()) { ?>
                        <div class="checkbox">
                            <input type="checkbox" name="assign_to_current_user" id="assign_to_current_user">
                            <label
                                for="assign_to_current_user"><?= _l('ticket_single_assign_to_me_on_update'); ?></label>
                        </div>
                        <?php } ?>
                        <div class="checkbox">
                            <input type="checkbox"
                                <?= hooks()->apply_filters('ticket_add_response_and_back_to_list_default', 'checked'); ?>
                            name="ticket_add_response_and_back_to_list" value="1"
                            id="ticket_add_response_and_back_to_list">
                            <label
                                for="ticket_add_response_and_back_to_list"><?= _l('ticket_add_response_and_back_to_list'); ?></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div
                    class="tw-bg-neutral-50 tw-p-3 -tw-mx-6 -tw-mb-[calc(theme(spacing.6)-1px)] tw-mt-4 tw-rounde-b-lg tw-border-t tw-border-solid tw-border-neutral-200">
                    <div class="sm:tw-flex sm:tw-items-center sm:tw-justify-between tw-w-full sm:tw-space-x-2.5">
                        <div>
                            <?php if ($ticket->lastreply !== null) { ?>
                            <span class="tw-text-neutral-500 tw-text-sm inline-block sm:tw-ml-2" data-toggle="tooltip"
                                title="<?= e(_dt($ticket->lastreply)); ?>"
                                data-placement="bottom">
                                <span class="text-has-action">
                                    <?= e(_l('ticket_single_last_reply', time_ago($ticket->lastreply))); ?>
                                </span>
                            </span>
                            <?php } ?>
                        </div>
                        <div
                            class="sm:tw-flex sm:tw-items-center sm:tw-space-x-2.5 tw-mt-4 sm:tw-mt-0 rtl:tw-space-x-reverse">
                            <label class="sm:tw-shrink-0"
                                for="status"><?= _l('ticket_status'); ?></label>
                            <?= render_select('status', $statuses, ['ticketstatusid', 'name'], '', get_option('default_ticket_reply_status'), [], [], 'tw-mb-0 tw-w-full sm:tw-w-[200px]', '', false); ?>
                            <button type="submit" class="btn btn-primary tw-w-full sm:tw-w-auto tw-mt-2 sm:tw-mt-0"
                                <?= $ticket->merged_ticket_id != null ? 'disabled' : ''; ?>
                                data-form="#single-ticket-form" autocomplete="off"
                                data-loading-text="<?= _l('wait_text'); ?>">
                                <?= _l('ticket_single_add_response'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>