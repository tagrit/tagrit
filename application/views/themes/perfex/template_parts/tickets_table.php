<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-tickets"
  data-order-col="<?= get_option('services') == 1 ? 7 : 6; ?>"
  data-order-type="desc">
  <thead>
    <th width="10%" class="th-ticket-number">
      <?= _l('clients_tickets_dt_number'); ?>
    </th>
    <th class="th-ticket-subject">
      <?= _l('clients_tickets_dt_subject'); ?>
    </th>
    <?php if ($show_submitter_on_table) { ?>
    <th class="th-ticket-submitter">
      <?= _l('ticket_dt_submitter'); ?>
    </th>
    <?php } ?>
    <th class="th-ticket-department">
      <?= _l('clients_tickets_dt_department'); ?>
    </th>
    <th class="th-ticket-project">
      <?= _l('project'); ?></th>
    <?php if (get_option('services') == 1) { ?>
    <th class="th-ticket-service">
      <?= _l('clients_tickets_dt_service'); ?>
    </th>
    <?php } ?>
    <th class="th-ticket-priority">
      <?= _l('priority'); ?></th>
    <th class="th-ticket-status">
      <?= _l('clients_tickets_dt_status'); ?>
    </th>
    <th class="th-ticket-last-reply">
      <?= _l('clients_tickets_dt_last_reply'); ?>
    </th>
    <?php
    $custom_fields = get_custom_fields('tickets', ['show_on_client_portal' => 1]);

foreach ($custom_fields as $field) { ?>
    <th><?= e($field['name']); ?></th>
    <?php } ?>
  </thead>
  <tbody>
    <?php foreach ($tickets as $ticket) { ?>
    <tr
      class="<?php if ($ticket['clientread'] == 0) {
          echo 'text-danger';
      } ?>">
      <td
        data-order="<?= e($ticket['ticketid']); ?>">
        <a
          href="<?= site_url('clients/ticket/' . $ticket['ticketid']); ?>">
          #<?= e($ticket['ticketid']); ?>
        </a>
      </td>
      <td>
        <a
          href="<?= site_url('clients/ticket/' . $ticket['ticketid']); ?>">
          <?= e($ticket['subject']); ?>
        </a>
      </td>
      <?php if ($show_submitter_on_table) { ?>
      <td>
        <?= e($ticket['user_firstname'] . ' ' . $ticket['user_lastname']); ?>
      </td>
      <?php } ?>
      <td>
        <?= e($ticket['department_name']); ?>
      </td>
      <td>
        <?php
          if ($ticket['project_id'] != 0) {
              echo '<a href="' . site_url('clients/project/' . $ticket['project_id']) . '">' . e(get_project_name_by_id($ticket['project_id'])) . '</a>';
          }
        ?>
      </td>
      <?php if (get_option('services') == 1) { ?>
      <td>
        <?= e($ticket['service_name']); ?>
      </td>
      <?php } ?>
      <td>
        <?= e(ticket_priority_translate($ticket['priority'])); ?>
      </td>
      <td>
        <span class="label inline-block"
          style="background:<?= e($ticket['statuscolor']); ?>">
          <?= e(ticket_status_translate($ticket['ticketstatusid'])); ?></span>
      </td>
      <td
        data-order="<?= e($ticket['lastreply']); ?>">
        <?php
          if ($ticket['lastreply'] == null) {
              echo _l('client_no_reply');
          } else {
              echo e(_dt($ticket['lastreply']));
          }
        ?>
      </td>
      <?php foreach ($custom_fields as $field) { ?>
      <td>
        <?= get_custom_field_value($ticket['ticketid'], $field['id'], 'tickets'); ?>
      </td>
      <?php } ?>
    </tr>
    <?php } ?>
  </tbody>
</table>