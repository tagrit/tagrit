<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
    <?php
    $statuses = $this->tickets_model->get_ticket_status();
?>
    <div class="_filters _hidden_inputs hidden tickets_filters">
        <?php
  foreach ($statuses as $status) {
      $val = '';
      if ($chosen_ticket_status != '') {
          if ($chosen_ticket_status == $status['ticketstatusid']) {
              $val = $chosen_ticket_status;
          }
      } else {
          if (in_array($status['ticketstatusid'], $default_tickets_list_statuses)) {
              $val = 1;
          }
      }
      echo form_hidden('ticket_status_' . $status['ticketstatusid'], $val);
  } ?>
    </div>
    <div
        class="tw-flex sm:tw-flex-row tw-flex-col tw-gap-y-2 sm:tw-gap-x-2 tw-order-1 sm:-tw-order-none tw-mb-1 md:tw-mb-0">
        <?php
  $where = '';
if (! is_admin()) {
    if (get_option('staff_access_only_assigned_departments') == 1) {
        $departments_ids = [];
        if (count($staff_deparments_ids) == 0) {
            $departments = $this->departments_model->get();

            foreach ($departments as $department) {
                array_push($departments_ids, $department['departmentid']);
            }
        } else {
            $departments_ids = $staff_deparments_ids;
        }
        if (count($departments_ids) > 0) {
            $where = 'AND department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")';
        }
    }
}

foreach ($statuses as $status) {
    $_where = '';
    if ($where == '') {
        $_where = 'status=' . $status['ticketstatusid'];
    } else {
        $_where = 'status=' . $status['ticketstatusid'] . ' ' . $where;
    }
    if (isset($project_id)) {
        $_where = $_where . ' AND project_id=' . $project_id;
    }
    $_where = $_where . ' AND merged_ticket_id IS NULL'; ?>
        <a href="#"
            data-cview="ticket_status_<?= e($status['ticketstatusid']); ?>"
            class="tw-bg-transparent tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-200/60 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600"
            <?= ($hrefAttrs ?? null instanceof Closure) ? $hrefAttrs($status) : ''; ?>>
            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                <?= total_rows(db_prefix() . 'tickets', $_where); ?>
            </span>
            <span
                style="color:<?= e($status['statuscolor']); ?>">
                <?= e(ticket_status_translate($status['ticketstatusid'])); ?>
            </span>
        </a>
        <?php
} ?>
    </div>
</div>