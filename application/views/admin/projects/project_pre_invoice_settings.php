<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Copy Project -->
<div class="modal fade" id="pre_invoice_project_settings" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?= _l('invoice_project_info'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (! $this->projects_model->timers_started_for_project($project_id, ['billable' => 1, 'billed' => 0, 'startdate <=' => date('Y-m-d')])) { ?>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="radio radio-primary">
                                    <input type="radio" <?php if ($billing_type == 3) {
                                        echo 'disabled';
                                    } else {
                                        echo 'checked';
                                    }?> name="invoice_data_type" value="single_line"
                                    id="single_line">
                                    <label
                                        for="single_line"><?= _l('invoice_project_data_single_line'); ?>
                                        <?php if ($billing_type == 1) {
                                            echo ' [ ' . _l('project_billing_type_fixed_cost') . ' ]';
                                        } ?></label>
                                </div>
                            </div>
                            <div class="col-md-2 mtop10 text-right">
                                <a href="#" class="text-muted" data-toggle="popover" data-placement="bottom"
                                    data-content="<b><?= _l('invoice_project_item_name_data'); ?>:</b> <?= _l('invoice_project_project_name_data'); ?><br /><b><?= _l('invoice_project_description_data'); ?>:</b> <?= _l('invoice_project_all_tasks_total_logged_time'); ?>"
                                    data-html="true"><i class="fa-regular fa-circle-question"></i></a>
                            </div>
                            <div class="col-md-10">
                                <div class="radio radio-primary">
                                    <input type="radio" name="invoice_data_type" <?php if ($billing_type == 3) {
                                        echo 'checked';
                                    } if ($billing_type == 1) {
                                        echo 'disabled';
                                    } ?> value="task_per_item" id="task_per_item">
                                    <label
                                        for="task_per_item"><?= _l('invoice_project_data_task_per_item'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-2 mtop10 text-right">
                                <a href="#" class="text-muted" data-toggle="popover" data-placement="bottom"
                                    data-content="<b><?= _l('invoice_project_item_name_data'); ?>:</b> <?= _l('invoice_project_projectname_taskname'); ?><br /><b><?= _l('invoice_project_description_data'); ?>:</b> <?= _l('invoice_project_total_logged_time_data'); ?>"
                                    data-html="true"><i class="fa-regular fa-circle-question"></i></a>
                            </div>
                            <div class="col-md-10">
                                <div class="radio radio-primary">
                                    <input type="radio" name="invoice_data_type" <?php if ($billing_type == 1) {
                                        echo 'disabled';
                                    } ?> value="timesheets_individualy"
                                    id="timesheets_individualy">
                                    <label
                                        for="timesheets_individualy"><?= _l('invoice_project_data_timesheets_individually'); ?></label>
                                </div>
                                <div id="timesheets_bill_include_notes" class="hide">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="timesheets_include_notes"
                                            value="timesheets_include_notes" id="timesheets_include_notes">
                                        <label
                                            for="timesheets_include_notes"><?= _l('invoice_project_include_timesheets_notes'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mtop10 text-right">
                                <a href="#" class="text-muted" data-toggle="popover" data-placement="bottom"
                                    data-content="<b><?= _l('invoice_project_item_name_data'); ?>:</b> <?= _l('invoice_project_projectname_taskname'); ?><br /><b><?= _l('invoice_project_description_data'); ?>:</b> <?= _l('invoice_project_timesheet_individually_data'); ?>"
                                    data-html="true"><i class="fa-regular fa-circle-question"></i></a>
                            </div>
                        </div>

                        <?php if (count($billable_tasks) == 0 && count($not_billable_tasks) == 0 && count($expenses) == 0) { ?>
                        <p class="text-danger mtop15">
                            <?= _l('invoice_project_nothing_to_bill'); ?>
                        </p>
                        <?php } else { ?>
                        <hr />
                        <a href="#" onclick="slideToggle('#pre_invoice_project_tasks'); return false;">
                            <span class="inline-block text-muted tw-font-semibold">
                                <?= _l('invoice_project_see_billed_tasks'); ?>
                                <i class="fa-solid fa-chevron-down tw-ml-2"></i>
                            </span>
                        </a>

                        <div style="display:none;" id="pre_invoice_project_tasks">
                            <div class="checkbox checkbox-primary mtop15">
                                <input type="checkbox" id="project_invoice_select_all_tasks"
                                    class="invoice_select_all_tasks">
                                <label
                                    for="project_invoice_select_all_tasks"><?= _l('project_invoice_select_all_tasks'); ?></label>
                            </div>
                            <hr />
                            <div id="tasks_who_will_be_billed">
                                <?php foreach ($billable_tasks as $task) {
                                    if ($task['status'] != Tasks_model::STATUS_COMPLETE) {
                                        $not_finished_tasks_found = true;
                                    } ?>
                                <div class="checkbox checkbox-default mbot15">
                                    <input type="checkbox" name="tasks[]"
                                        value="<?= e($task['id']); ?>"
                                        <?php if ($task['status'] == Tasks_model::STATUS_COMPLETE) {
                                            echo 'checked ';
                                        } ?>id="<?= e($task['id']); ?>">
                                    <label class="inline-block full-width"
                                        for="<?= e($task['id']); ?>"><?= e($task['name']); ?>
                                        <?php if (total_rows(db_prefix() . 'taskstimers', ['task_id' => $task['id']]) == 0 && $billing_type != 1) {
                                            echo '<small class="text-danger">' . _l('project_invoice_task_no_timers_found') . '</small>';
                                        } ?><small
                                            class="pull-right valign"><?= format_task_status($task['status']); ?></small></label>
                                </div>
                                <?php
                                } ?>
                            </div>
                            <?php if (count($not_billable_tasks) > 0) { ?>
                            <hr />
                            <p class="text-warning mtop10">
                                <?= _l('invoice_project_start_date_tasks_not_passed'); ?>
                            </p>
                            <?php foreach ($not_billable_tasks as $task) { ?>
                            <div class="checkbox checkbox-default mbot15">
                                <input type="checkbox" name="tasks[]" disabled
                                    value="<?= e($task['id']); ?>"
                                    id="<?= e($task['id']); ?>">
                                <label
                                    for="<?= e($task['id']); ?>"><?= e($task['name']); ?>
                                    <small><?= e(_l('invoice_project_tasks_not_started', _d($task['startdate']))); ?></small></label>
                            </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <?php
                                    if (count($expenses) > 0) { ?>
                        <hr />
                        <a href="#" onclick="slideToggle('#expenses_who_will_be_billed'); return false;">
                            <span class="inline-block text-muted tw-font-semibold">
                                <?= _l('invoice_project_see_billed_expenses'); ?>
                                <i class="fa-solid fa-chevron-down tw-ml-2"></i>
                            </span>
                        </a>
                        <div style="display:none;" id="expenses_who_will_be_billed">

                            <div class="checkbox checkbox-primary mtop20">
                                <input type="checkbox" id="project_invoice_select_all_expenses"
                                    class="invoice_select_all_expenses">
                                <label
                                    for="project_invoice_select_all_expenses"><?= _l('project_invoice_select_all_expenses'); ?></label>
                            </div>
                            <hr />
                            <?php
                                $i                     = 0;
                                        $totalExpenses = count($expenses);

                                        foreach ($expenses as $data) {
                                            $expense = $this->expenses_model->get($data['id']);
                                            $total   = $expense->amount;

                                            $totalTaxByExpense = 0;
                                            // Check if tax is applied
                                            if ($expense->tax != 0) {
                                                $total += ($total / 100 * $expense->taxrate);
                                            }

                                            if ($expense->tax2 != 0) {
                                                $total += ($expense->amount / 100 * $expense->taxrate2);
                                            } ?>
                            <div class="checkbox checkbox-default mbot15 expense-to-bill">
                                <input type="checkbox" name="expenses[]" checked
                                    value="<?= e($expense->expenseid); ?>"
                                    id="expense_<?= e($expense->expenseid); ?>">
                                <label
                                    for="expense_<?= e($expense->expenseid); ?>">
                                    <?= e($expense->category_name); ?>
                                    <?php if (! empty($expense->expense_name)) {
                                        echo '(' . e($expense->expense_name) . ')';
                                    } ?>
                                    -
                                    <?= e(app_format_money($total, $expense->currency_data)); ?>
                                </label>
                            </div>
                            <div class="<?php if (empty($expense->expense_name) && empty($expense->note)) {
                                echo 'hide';
                            } ?>">
                                <p style="margin-top:-10px;">
                                    <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                        data-title="<?= _l('expense_include_additional_data_on_convert'); ?>"></i>
                                    <b><?= _l('expense_add_edit_description'); ?>
                                        +</b>
                                </p>

                                <div class="checkbox checkbox-default checkbox-inline expense-add-note<?php if (empty($expense->expense_name)) {
                                    echo ' hide';
                                } ?>">
                                    <input type="checkbox"
                                        id="inc_note<?= e($expense->id); ?>"
                                        value="<?= e($expense->id); ?>"
                                        name="expense_inc_note[]">
                                    <label
                                        for="inc_note<?= e($expense->id); ?>"><?= _l('expense'); ?>
                                        <?= _l('expense_add_edit_note'); ?></label>
                                </div>
                                <div class="checkbox checkbox-default checkbox-inline expense-add-name<?php if (empty($expense->note)) {
                                    echo '  hide';
                                } ?><?php if (empty($expense->expense_name)) {
                                    echo ' no-mleft';
                                } ?>">
                                    <input type="checkbox"
                                        id="inc_name<?= e($expense->id); ?>"
                                        value="<?= e($expense->id); ?>"
                                        name="expense_inc_name[]">
                                    <label
                                        for="inc_name<?= e($expense->id); ?>"><?= _l('expense'); ?>
                                        <?= _l('expense_name'); ?></label>
                                </div>
                            </div>
                            <?php if ($i >= 0 && $i != $totalExpenses - 1) { ?>
                            <hr />
                            <?php } ?>
                            <?php $i++;
                                        } ?>
                        </div>
                        <?php } ?>
                        <?php if (isset($not_finished_tasks_found)) { ?>
                        <hr />
                        <p class="text-danger">
                            <?= _l('invoice_project_all_billable_tasks_marked_as_finished'); ?>
                        </p>
                        <?php } ?>
                        <?php } ?>
                        <?php } else {
                            $timers_started = true; ?>
                        <p class="text-danger text-center">
                            <?= _l('project_invoice_timers_started'); ?>
                        </p>
                        <hr />
                        <div class="col-md-6 text-center">
                            <a href="#" onclick="mass_stop_timers(true);return false;"
                                class="btn btn-default"><?= _l('invoice_project_stop_billable_timers_only'); ?></a>
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="#" onclick="mass_stop_timers(false);return false;"
                                class="btn btn-danger"><?= _l('invoice_project_stop_all_timers'); ?></a>
                        </div>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <?php if (! isset($timers_started)) { ?>
                <button type="submit" class="btn btn-primary"
                    onclick="invoice_project(<?= e($project_id); ?>)"><?= _l('invoice_project'); ?></button>
                <?php } ?>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Copy Project end -->