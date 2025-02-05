<div role="tabpanel" class="tab-pane" id="tab_reminders">
    <a href="#" class="btn btn-primary" data-toggle="modal"
        data-target=".reminder-modal-ticket-<?= e($ticket->ticketid); ?>"><i
            class="fa-regular fa-bell"></i>
        <?= _l('ticket_set_reminder_title'); ?></a>
    <hr />
    <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
    <!-- The reminders modal -->
    <?php $this->load->view(
        'admin/includes/modals/reminder',
        [
            'id'             => $ticket->ticketid,
            'name'           => 'ticket',
            'members'        => $staff,
            'reminder_title' => _l('ticket_set_reminder_title'), ]
    ); ?>

</div>