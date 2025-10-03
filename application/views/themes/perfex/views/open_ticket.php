<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg section-heading section-heading-open-ticket">
    <?= _l('clients_ticket_open_subject'); ?>
</h4>
<?= form_open_multipart('clients/open_ticket', ['id' => 'open-new-ticket-form']); ?>
<div class="row">
    <div class="col-md-12">
        <?php hooks()->do_action('before_client_open_ticket_form_start'); ?>
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group open-ticket-subject-group">
                            <label
                                for="subject"><?= _l('customer_ticket_subject'); ?></label>
                            <input type="text" class="form-control" name="subject" id="subject"
                                value="<?= set_value('subject'); ?>">
                            <?= form_error('subject'); ?>
                        </div>
                        <?php if (total_rows(db_prefix() . 'projects', ['clientid' => get_client_user_id()]) > 0 && has_contact_permission('projects')) { ?>
                        <div class="form-group open-ticket-project-group">
                            <label
                                for="project_id"><?= _l('project'); ?></label>
                            <select
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                name="project_id" id="project_id" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach ($projects as $project) { ?>
                                <option
                                    value="<?= e($project['id']); ?>"
                                    <?= set_select('project_id', $project['id']); ?><?php if ($this->input->get('project_id') == $project['id']) {
                                        echo ' selected';
                                    } ?>><?= e($project['name']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group open-ticket-department-group">
                                    <label
                                        for="department"><?= _l('clients_ticket_open_departments'); ?></label>
                                    <select
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                        name="department" id="department" class="form-control selectpicker">
                                        <option value=""></option>
                                        <?php foreach ($departments as $department) { ?>
                                        <option
                                            value="<?= e($department['departmentid']); ?>"
                                            <?= set_select('department', $department['departmentid'], (count($departments) == 1 ? true : false)); ?>>
                                            <?= e($department['name']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <?= form_error('department'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group open-ticket-priority-group">
                                    <label
                                        for="priority"><?= _l('clients_ticket_open_priority'); ?></label>
                                    <select
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                        name="priority" id="priority" class="form-control selectpicker">
                                        <option value=""></option>
                                        <?php foreach ($priorities as $priority) { ?>
                                        <option
                                            value="<?= e($priority['priorityid']); ?>"
                                            <?= set_select('priority', $priority['priorityid'], hooks()->apply_filters('new_ticket_priority_selected', 2) == $priority['priorityid']); ?>>
                                            <?= e(ticket_priority_translate($priority['priorityid'])); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <?= form_error('priority'); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                                                      if (get_option('services') == 1 && count($services) > 0) { ?>
                        <div class="form-group open-ticket-service-group">
                            <label
                                for="service"><?= _l('clients_ticket_open_service'); ?></label>
                            <select
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                name="service" id="service" class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach ($services as $service) { ?>
                                <option
                                    value="<?= e($service['serviceid']); ?>"
                                    <?= set_select('service', $service['serviceid'], (count($services) == 1 ? true : false)); ?>>
                                    <?= e($service['name']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="custom-fields">
                            <?= render_custom_fields('tickets', '', ['show_on_client_portal' => 1]); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group open-ticket-message-group">
                    <label
                        for="message"><?= _l('clients_ticket_open_body'); ?></label>
                    <textarea name="message" id="message" class="form-control"
                        placeholder="<?= _l('clients_ticket_open_body'); ?>"
                        rows="8"><?= set_value('message'); ?></textarea>
                </div>

                <div class="attachments_area open-ticket-attachments-area">
                    <div class="attachments">
                        <div class="attachment tw-max-w-md">
                            <div class="form-group">
                                <label for="attachment" class="control-label">
                                    <?= _l('clients_ticket_attachments'); ?>
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
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button type="submit" class="btn btn-primary" data-form="#open-new-ticket-form" autocomplete="off"
                    data-loading-text="<?= _l('wait_text'); ?>">
                    <?= _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close(); ?>