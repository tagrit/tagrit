<div id="settings">
    <div class="form-group">
        <label for="tags" class="control-label">
            <i class="fa fa-tag" aria-hidden="true"></i>
            <?= _l('tags'); ?>
        </label>
        <input type="text" class="tagsinput" id="tags" name="tags"
            value="<?= prep_tags_input(get_tags_in($ticket->ticketid, 'ticket')); ?>"
            data-role="tagsinput">
    </div>

    <?= render_input('subject', 'ticket_settings_subject', $ticket->subject); ?>

    <div class="form-group select-placeholder">
        <label for="contactid"
            class="control-label"><?= _l('contact'); ?></label>
        <select name="contactid" id="contactid" class="ajax-search" data-width="100%" data-live-search="true"
            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
            <?php if (! $ticket->userid) {
                echo ' data-no-contact="true"';
            } else {
                echo ' data-ticket-emails="' . $ticket->ticket_emails . '"';
            } ?>>
            <?php
                              $rel_data = get_relation_data('contact', $ticket->contactid);
            $rel_val                    = get_relation_values($rel_data, 'contact');
            echo '<option value="' . $rel_val['id'] . '" selected data-subtext="' . $rel_val['subtext'] . '">' . $rel_val['name'] . '</option>';
            ?>
        </select>
        <?= form_hidden('userid', $ticket->userid); ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= render_input('name', 'ticket_settings_to', $ticket->submitter, 'text', ['disabled' => true]); ?>
        </div>
        <div class="col-md-6">
            <?php
                                                    if ($ticket->userid != 0) {
                                                        echo render_input(
                                                            'email',
                                                            'ticket_settings_email',
                                                            $ticket->email,
                                                            'email',
                                                            ['disabled' => true]
                                                        );
                                                    } else {
                                                        echo render_input(
                                                            'email',
                                                            'ticket_settings_email',
                                                            $ticket->ticket_email,
                                                            'email',
                                                            ['disabled' => true]
                                                        );
                                                    } ?>
        </div>
    </div>
    <?= render_select(
        'department',
        $departments,
        ['departmentid', 'name'],
        'ticket_settings_departments',
        $ticket->department
    ); ?>

    <div class="form-group select-placeholder">
        <label for="assigned" class="control-label">
            <?= _l('ticket_settings_assign_to'); ?>
        </label>
        <select name="assigned" data-live-search="true" id="assigned" class="form-control selectpicker"
            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
            <option value="">
                <?= _l('ticket_settings_none_assigned'); ?>
            </option>
            <?php foreach ($staff as $member) {
                // Ticket is assigned to member
                // Member is set to inactive
                // We should show the member in the dropdown too
                // Otherwise, skip this member
                if ($member['active'] == 0 && $ticket->assigned != $member['staffid']) {
                    continue;
                } ?>
            <option
                value="<?= e($member['staffid']); ?>"
                <?= $ticket->assigned == $member['staffid'] ? 'selected' : ''; ?>>
                <?= e($member['firstname'] . ' ' . $member['lastname']); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <div class="row">
        <div
            class="<?= get_option('services') == 1 ? 'col-sm-12 col-lg-6' : 'col-md-12'; ?>">
            <?= render_select(
                'priority',
                $priorities += ['callback_translate' => 'ticket_priority_translate'],
                ['priorityid', 'name'],
                'ticket_settings_priority',
                $ticket->priority
            ); ?>
        </div>
        <?php if (get_option('services') == 1) { ?>
        <div class="col-sm-12 col-lg-6">
            <?php if (is_admin() || get_option('staff_members_create_inline_ticket_services') == '1') {
                echo render_select_with_input_group('service', $services, ['serviceid', 'name'], 'ticket_settings_service', $ticket->service, '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_service();return false;"><i class="fa fa-plus"></i></a></div>');
            } else {
                echo render_select('service', $services, ['serviceid', 'name'], 'ticket_settings_service', $ticket->service);
            }
            ?>
        </div>
        <?php } ?>
    </div>
    <div
        class="form-group select-placeholder projects-wrapper<?= $ticket->userid == 0 ? ' hide' : ''; ?>">
        <label for="project_id">
            <?= _l('project'); ?>
        </label>
        <div id="project_ajax_search_wrapper">
            <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true"
                data-width="100%"
                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                <?php if ($ticket->project_id) { ?>
                <option value="<?= e($ticket->project_id); ?>">
                    <?= e(get_project_name_by_id($ticket->project_id)); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?= render_input('merge_ticket_ids', 'merge_ticket_ids_field_label', '', 'text', $ticket->merged_ticket_id === null ? ['placeholder' => _l('merge_ticket_ids_field_placeholder')] : ['disabled' => true]); ?>

    <?= render_custom_fields('tickets', $ticket->ticketid); ?>
</div>