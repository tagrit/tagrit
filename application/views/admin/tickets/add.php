<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?= form_open_multipart($this->uri->uri_string(), ['id' => 'new_ticket_form']); ?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="tw-flex tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-mr-4">
                        <?= _l('clients_single_ticket_information_heading'); ?>
                    </h4>
                    <?php if (! isset($project_id) && ! isset($contact)) { ?>
                    <a href="#" id="ticket_no_contact" class="label label-default">
                        <i class="fa-regular fa-envelope tw-mr-1"></i>
                        <?= _l('ticket_create_no_contact'); ?>
                    </a>
                    <a href="#" class="hide label label-default" id="ticket_to_contact">
                        <i class="fa-regular fa-user tw-mr-1"></i>
                        <?= _l('ticket_create_to_contact'); ?>
                    </a>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">

                                <?= render_input('subject', 'ticket_settings_subject', '', 'text', ['required' => 'true']); ?>
                                <div class="form-group select-placeholder" id="ticket_contact_w">
                                    <label
                                        for="contactid"><?= _l('contact'); ?></label>
                                    <select name="contactid" required="true" id="contactid" class="ajax-search"
                                        data-width="100%" data-live-search="true"
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                        <?php if (isset($contact)) { ?>
                                        <option
                                            value="<?= e($contact['id']); ?>"
                                            selected>
                                            <?= $contact['firstname'] . ' ' . $contact['lastname']; ?>
                                        </option>
                                        <?php } ?>
                                        <option value=""></option>
                                    </select>
                                    <?= form_hidden('userid'); ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= render_input('name', 'ticket_settings_to', '', 'text', ['disabled' => true]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= render_input('email', 'ticket_settings_email', '', 'email', ['disabled' => true]); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= render_select('department', $departments, ['departmentid', 'name'], 'ticket_settings_departments', (count($departments) == 1) ? $departments[0]['departmentid'] : '', ['required' => 'true']); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= render_input('cc', 'CC'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        <?= _l('tags'); ?></label>
                                    <input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">
                                </div>

                                <div class="form-group select-placeholder">
                                    <label for="assigned" class="control-label">
                                        <?= _l('ticket_settings_assign_to'); ?>
                                    </label>
                                    <select name="assigned" id="assigned" class="form-control selectpicker"
                                        data-live-search="true"
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                        data-width="100%">
                                        <option value="">
                                            <?= _l('ticket_settings_none_assigned'); ?>
                                        </option>
                                        <?php foreach ($staff as $member) { ?>
                                        <option
                                            value="<?= e($member['staffid']); ?>"
                                            <?php if ($member['staffid'] == get_staff_user_id()) {
                                                echo 'selected';
                                            } ?>>
                                            <?= e($member['firstname'] . ' ' . $member['lastname']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-<?php if (get_option('services') == 1) {
                                        echo 6;
                                    } else {
                                        echo 12;
                                    } ?>">
                                        <?php $priorities['callback_translate'] = 'ticket_priority_translate';
echo render_select('priority', $priorities, ['priorityid', 'name'], 'ticket_settings_priority', hooks()->apply_filters('new_ticket_priority_selected', 2), ['required' => 'true']); ?>
                                    </div>
                                    <?php if (get_option('services') == 1) { ?>
                                    <div class="col-md-6">
                                        <?php if (is_admin() || get_option('staff_members_create_inline_ticket_services') == '1') {
                                            echo render_select_with_input_group('service', $services, ['serviceid', 'name'], 'ticket_settings_service', '', '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_service();return false;"><i class="fa fa-plus"></i></a></div>');
                                        } else {
                                            echo render_select('service', $services, ['serviceid', 'name'], 'ticket_settings_service');
                                        }
                                        ?>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div class="form-group projects-wrapper hide">
                                    <label
                                        for="project_id"><?= _l('project'); ?></label>
                                    <div id="project_ajax_search_wrapper">
                                        <select name="project_id" id="project_id" class="projects ajax-search"
                                            data-live-search="true" data-width="100%"
                                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                            <?php if (isset($project_id)) { ?>
                                            data-auto-project="true"
                                            data-project-userid="<?= e($userid); ?>"
                                            <?php } ?>>
                                            <?php if (isset($project_id)) { ?>
                                            <option
                                                value="<?= e($project_id); ?>"
                                                selected>
                                                <?= '#' . $project_id . ' - ' . e(get_project_name_by_id($project_id)); ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <?= render_custom_fields('tickets'); ?>
                            </div>

                            <div class="col-md-12">
                                <hr class="hr-panel-separator" />
                            </div>
                            <div class="col-md-12 tw-mt-3">
                                <h4 class="tw-mt-0 tw-font-bold tw-text-base tw-text-neutral-700">
                                    <?= _l('ticket_add_body'); ?>
                                </h4>
                                <div class="row">
                                    <div class="col-md-12 mbot20 before-ticket-message">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select id="insert_predefined_reply" data-width="100%"
                                                    data-live-search="true" class="selectpicker"
                                                    data-title="<?= _l('ticket_single_insert_predefined_reply'); ?>">
                                                    <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                    <option
                                                        value="<?= e($predefined_reply['id']); ?>">
                                                        <?= e($predefined_reply['name']); ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php if (get_option('use_knowledge_base') == 1) { ?>
                                            <div class="visible-xs">
                                                <div class="mtop15"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                                <select id="insert_knowledge_base_link" data-width="100%"
                                                    class="selectpicker" data-live-search="true"
                                                    onchange="insert_ticket_knowledgebase_link(this);"
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
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <?= render_textarea('message', '', '', [], [], '', 'tinymce'); ?>
                                <div class="attachments_area">
                                    <div class="row attachments">
                                        <div class="attachment">
                                            <div class="col-md-4 col-md-offset-8 mtop10">
                                                <div class="form-group">
                                                    <label for="attachment"
                                                        class="control-label"><?= _l('ticket_add_attachments'); ?></label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                            extension="<?= str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>"
                                                            filesize="<?= file_upload_max_size(); ?>"
                                                            class="form-control" name="attachments[0]"
                                                            accept="<?= get_ticket_form_accepted_mimes(); ?>">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default add_more_attachments"
                                                                data-max="<?= get_option('maximum_allowed_ticket_attachments'); ?>"
                                                                type="button"><i class="fa fa-plus"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right tw-mt-6">
                            <button type="submit" data-form="#new_ticket_form" autocomplete="off"
                                data-loading-text="<?= _l('wait_text'); ?>"
                                class="btn btn-primary"><?= _l('open_ticket'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <div class="tw-py-10"></div>
    <?php $this->load->view('admin/tickets/services/service'); ?>
    <?php init_tail(); ?>
    <?php hooks()->do_action('new_ticket_admin_page_loaded'); ?>
    <script>
        $(function() {

            init_ajax_search('contact', '#contactid.ajax-search', {
                tickets_contacts: true,
                contact_userid: function() {
                    // when ticket is directly linked to project only search project client id contacts
                    var uid = $('select[data-auto-project="true"]').attr('data-project-userid');
                    if (uid) {
                        return uid;
                    } else {
                        return '';
                    }
                }
            });

            validate_new_ticket_form();

            <?php if (isset($project_id) || isset($contact)) { ?>
            $('body.ticket select[name="contactid"]').change();
            <?php } ?>

            <?php if (isset($project_id)) { ?>
            $('body').on('selected.cleared.ajax.bootstrap.select', 'select[data-auto-project="true"]', function(
                e) {
                $('input[name="userid"]').val('');
                $(this).parents('.projects-wrapper').addClass('hide');
                $(this).prop('disabled', false);
                $(this).removeAttr('data-auto-project');
                $('body.ticket select[name="contactid"]').change();
            });
            <?php } ?>
        });
    </script>
    </body>

    </html>