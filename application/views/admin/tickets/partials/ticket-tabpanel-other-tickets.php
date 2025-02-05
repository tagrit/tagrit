<div role="tabpanel" class="tab-pane" id="othertickets">
    <div class="_filters _hidden_inputs hidden tickets_filters">
        <?= form_hidden('via_ticket', $ticket->ticketid); ?>
        <?= form_hidden('via_ticket_email', $ticket->email); ?>
        <?= form_hidden('via_ticket_userid', $ticket->userid); ?>
    </div>
    <?= AdminTicketsTableStructure(); ?>
</div>