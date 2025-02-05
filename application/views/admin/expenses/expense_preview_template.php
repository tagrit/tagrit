<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_expense" aria-controls="tab_expense" role="tab" data-toggle="tab">
                                <?= _l('expense'); ?>
                            </a>
                        </li>
                        <?php if (count($child_expenses) > 0 || $expense->recurring != 0) { ?>
                        <li role="presentation">
                            <a href="#tab_child_expenses" aria-controls="tab_child_expenses" role="tab"
                                data-toggle="tab">
                                <?= _l('child_expenses'); ?>
                            </a>
                        </li>
                        <?php } ?>
                        <li role="presentation">
                            <a href="#tab_tasks"
                                onclick="init_rel_tasks_table(<?= e($expense->expenseid); ?>,'expense'); return false;"
                                aria-controls="tab_tasks" role="tab" data-toggle="tab">
                                <?= _l('tasks'); ?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator">
                            <a href="#tab_reminders"
                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?= $expense->id; ?> + '/' + 'expense', undefined, undefined,undefined,[1,'ASC']); return false;"
                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <?= _l('expenses_reminders'); ?>
                                <?php
                        $total_reminders = total_rows(
                            db_prefix() . 'reminders',
                            [
                                'isnotified' => 0,
                                'staff'      => get_staff_user_id(),
                                'rel_type'   => 'expense',
                                'rel_id'     => $expense->expenseid,
                            ]
                        );
if ($total_reminders > 0) {
    echo '<span class="badge">' . $total_reminders . '</span>';
}
?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;" data-placement="left"
                                data-toggle="tooltip"
                                data-title="<?= _l('toggle_full_view'); ?>">
                                <i class="fa fa-expand"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mtop20">
                <div class="col-md-6" id="expenseHeadings">
                    <h3 class="tw-font-semibold tw-text-lg tw-text-neutral-700 tw-mt-0 tw-mb-1 tw-flex tw-items-center tw-gap-x-2"
                        id="expenseCategory">
                        <?= e($expense->category_name); ?>
                        <?php if ($expense->billable == 1) {
                            if ($expense->invoiceid == null) {
                                echo '<span class="label label-danger tw-py-0.5">' . _l('expense_invoice_not_created') . '</span>';
                            } else {
                                echo '<span class="bold">' . e(format_invoice_number($invoice->id)) . ' - </span>';
                                if ($invoice->status == 2) {
                                    echo '<span class="label label-success tw-py-0.5">' . _l('expense_billed') . '</span>';
                                } else {
                                    echo '<span class="label label-danger tw-py-0.5">' . _l('expense_not_billed') . '</span>';
                                }
                            }
                        } ?>
                    </h3>
                    <?php if (! empty($expense->expense_name)) { ?>
                    <h4 class="tw-text-base tw-font-medium tw-m-0 tw-text-neutral-500" id="expenseName">
                        <?= e($expense->expense_name); ?>
                    </h4>
                    <?php } ?>
                    <h4 class="tw-text-base tw-mt-1 tw-mb-0 tw-text-neutral-500" id="expenserCreator">
                        <?= _l('created_by'); ?>:
                        <a
                            href="<?= admin_url('staff/profile/' . $expense->addedfrom) ?>">
                            <?= e(get_staff_full_name($expense->addedfrom)); ?>
                        </a>
                    </h4>
                </div>
                <div class="col-md-6 _buttons text-right">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= _l('more'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">

                                <?php if (staff_can('create', 'expenses')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('expenses/copy/' . $expense->expenseid); ?>">
                                        <?= _l('expense_copy'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="#" onclick="print_expense_information(); return false;">
                                        <?= _l('print'); ?>
                                    </a>
                                </li>
                                <?php if (staff_can('delete', 'expenses')) { ?>
                                <li>
                                    <a class="text-danger delete-text _delete"
                                        href="<?= admin_url('expenses/delete/' . $expense->expenseid); ?>"
                                        data-toggle="tooltip" data-placement="bottom" title="">
                                        <?= _l('expense_delete'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php if (staff_can('edit', 'expenses')) { ?>
                    <a class="btn btn-default pull-right ltr:tw-mr-1 rtl:tw-ml-1" data-toggle="tooltip"
                        title="<?= _l('expense_edit'); ?>"
                        href="<?= admin_url('expenses/expense/' . $expense->expenseid); ?>">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <?php } ?>
                    <?php if ($expense->billable == 1 && $expense->invoiceid == null) { ?>
                    <?php if (staff_can('create', 'invoices')) { ?>
                    <button type="button" class="btn btn-primary pull-right ltr:tw-mr-1 rtl:tw-ml-1 expense_convert_btn"
                        data-id="<?= e($expense->expenseid); ?>"
                        data-toggle="modal" data-target="#expense_convert_helper_modal">
                        <?= _l('expense_convert_to_invoice'); ?>
                    </button>
                    <?php } ?>
                    <?php } elseif ($expense->invoiceid != null) { ?>
                    <a href="<?= admin_url('invoices/list_invoices/' . $expense->invoiceid); ?>"
                        class="btn btn-primary ltr:tw-mr-2 rtl:tw-ml-2 pull-right">
                        <?= e(format_invoice_number($invoice->id)); ?>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="hr-panel-separator" />
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_expense"
                    data-empty-note="<?= empty($expense->note); ?>"
                    data-empty-name="<?= empty($expense->expense_name); ?>">
                    <div class="row">
                        <?php
                if ($expense->recurring > 0 || $expense->recurring_from != null) {
                    echo '<div class="col-md-12">';

                    $recurring_expense           = $expense;
                    $show_recurring_expense_info = true;

                    if ($expense->recurring_from != null) {
                        $recurring_expense = $this->expenses_model->get($expense->recurring_from);
                        // Maybe recurring expense not longer recurring?
                        if ($recurring_expense->recurring == 0) {
                            $show_recurring_expense_info = false;
                        } else {
                            $next_recurring_date_compare = $recurring_expense->last_recurring_date;
                        }
                    } else {
                        $next_recurring_date_compare = $recurring_expense->date;
                        if ($recurring_expense->last_recurring_date) {
                            $next_recurring_date_compare = $recurring_expense->last_recurring_date;
                        }
                    }
                    if ($show_recurring_expense_info) {
                        $next_date = date('Y-m-d', strtotime('+' . $recurring_expense->repeat_every . ' ' . strtoupper($recurring_expense->recurring_type), strtotime($next_recurring_date_compare)));
                    } ?>
                        <?php if ($expense->recurring_from == null && $recurring_expense->cycles > 0 && $recurring_expense->cycles == $recurring_expense->total_cycles) { ?>
                        <div class="alert alert-info mbot15">
                            <?= e(_l('recurring_has_ended', _l('expense_lowercase'))); ?>
                        </div>
                        <?php } elseif ($show_recurring_expense_info) { ?>
                        <span class="label label-info">
                            <?= _l('cycles_remaining'); ?>:
                            <b class="tw-ml-2">
                                <?= e($recurring_expense->cycles == 0 ? _l('cycles_infinity') : $recurring_expense->cycles - $recurring_expense->total_cycles);
                            ?>
                            </b>
                        </span>
                        <?php if ($recurring_expense->cycles == 0 || $recurring_expense->cycles != $recurring_expense->total_cycles) {
                            echo '<span class="label label-info tw-ml-1"><i class="fa-regular fa-circle-question fa-fw" data-toggle="tooltip" data-title="' . _l('recurring_recreate_hour_notice', _l('expense')) . '"></i> ' . _l('next_expense_date', '<b class="tw-ml-1">' . e(_d($next_date)) . '</b>') . '</span>';
                        }
                        }
                    if ($expense->recurring_from != null) { ?>
                        <?= '<p class="text-muted no-mbot' . ($show_recurring_expense_info ? ' mtop15' : '') . '">' . _l('expense_recurring_from', '<a href="' . admin_url('expenses/list_expenses/' . $expense->recurring_from) . '" onclick="init_expense(' . $expense->recurring_from . ');return false;">' . e($recurring_expense->category_name) . (! empty($recurring_expense->expense_name) ? ' (' . e($recurring_expense->expense_name) . ')' : '') . '</a></p>'); ?>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-separator !tw-my-6" />
                    <?php
                } ?>
                    <div class="col-md-6">
                        <p>
                        <div id="amountWrapper">
                            <span
                                class="bold font-medium"><?= _l('expense_amount'); ?></span>
                            <span class="bold font-medium">
                                <?= e(app_format_money($expense->amount, $expense->currency_data)); ?>
                            </span>
                        </div>
                        <?php if ($expense->paymentmode != '0' && ! empty($expense->paymentmode)) {
                            ?>
                        <span class="text-muted text-sm">
                            <?= e(_l('expense_paid_via', $expense->payment_mode_name)); ?>
                        </span><br />
                        <?php
                        } ?>
                        <?php
                                if ($expense->tax != 0) {
                                    echo '<br /><span class="bold">' . _l('tax_1') . ':</span> ' . e($expense->taxrate) . '% (' . e($expense->tax_name) . ')';
                                    $total = $expense->amount;
                                    $total += ($total / 100 * $expense->taxrate);
                                }
if ($expense->tax2 != 0) {
    echo '<br /><span class="bold">' . _l('tax_2') . ':</span> ' . e($expense->taxrate2) . '% (' . e($expense->tax_name2) . ')';
    $total += ($expense->amount / 100 * $expense->taxrate2);
}
if ($expense->tax != 0 || $expense->tax2 != 0) {
    echo '<p class="font-medium bold">' . _l('total_with_tax') . ': ' . e(app_format_money($total, $expense->currency_data)) . '</p>';
}
?>
                        <p><span
                                class="bold"><?= _l('expense_date'); ?></span>
                            <span
                                class="text-muted"><?= e(_d($expense->date)); ?></span>
                        </p>
                        </p>
                        <?php if (! empty($expense->reference_no)) { ?>
                        <p class="bold mbot15">
                            <?= _l('expense_ref_noe'); ?>
                            <span class="text-muted">
                                <?= e($expense->reference_no); ?>
                            </span>
                        </p>
                        <?php } ?>
                        <?php if ($expense->clientid) { ?>
                        <p class="bold mbot5">
                            <?= _l('expense_customer'); ?>
                        </p>
                        <p class="mbot15">
                            <a
                                href="<?= admin_url('clients/client/' . $expense->clientid); ?>">
                                <?= e($expense->company); ?>
                            </a>
                        </p>
                        <?php } ?>
                        <?php if ($expense->project_id) { ?>
                        <p class="bold mbot5">
                            <?= _l('project'); ?>
                        </p>
                        <p class="mbot15">
                            <a
                                href="<?= admin_url('projects/view/' . $expense->project_id); ?>">
                                <?= e($expense->project_data->name); ?>
                            </a>
                        </p>
                        <?php } ?>
                        <?php
                     $custom_fields = get_custom_fields('expenses');

foreach ($custom_fields as $field) { ?>
                        <?php $value = get_custom_field_value($expense->expenseid, $field['id'], 'expenses');
    if ($value == '') {
        continue;
    } ?>
                        <div class="row mbot10">
                            <div class="col-md-12 mtop5">
                                <p class="mbot5">
                                    <span
                                        class="bold"><?= e(ucfirst($field['name'])); ?></span>
                                </p>
                                <div class="text-left">
                                    <?= $value; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if ($expense->note != '') { ?>
                        <p class="bold mbot5">
                            <?= _l('expense_note'); ?>
                        </p>
                        <div class="text-muted mbot15">
                            <?= process_text_content_for_display($expense->note); ?>
                        </div>
                        <?php } ?>
                        <?php hooks()->do_action('after_left_panel_expense_preview_template', $expense); ?>
                    </div>
                    <div class="col-md-6" id="expenseReceipt">
                        <h4 class="tw-mt-0 tw-font-bold tw-text-lg">
                            <?= _l('expense_receipt'); ?>
                        </h4>

                        <?php if (empty($expense->attachment)) { ?>
                        <?= form_open('admin/expenses/add_expense_attachment/' . $expense->expenseid, ['class' => 'mtop10 dropzone dropzone-expense-preview dropzone-manual', 'id' => 'expense-receipt-upload']); ?>
                        <div id="dropzoneDragArea" class="dz-default dz-message">
                            <span><?= _l('expense_add_edit_attach_receipt'); ?></span>
                        </div>
                        <?= form_close(); ?>
                        <?php } else { ?>
                        <div class="row">
                            <div class="col-md-10">
                                <i
                                    class="<?= get_mime_class($expense->filetype); ?>"></i>
                                <a
                                    href="<?= site_url('download/file/expense/' . $expense->expenseid); ?>">
                                    <?= e($expense->attachment); ?>
                                </a>
                            </div>
                            <?php if ($expense->attachment_added_from == get_staff_user_id() || is_admin()) { ?>
                            <div class="col-md-2 text-right">
                                <a class="_delete text-danger"
                                    href="<?= admin_url('expenses/delete_expense_attachment/' . $expense->expenseid . '/preview'); ?>"
                                    class="text-danger">
                                    <i class="fa fa fa-times"></i>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php hooks()->do_action('after_right_panel_expense_preview_template', $expense); ?>
                    </div>
                </div>
            </div>
            <?php if (count($child_expenses) > 0 || $expense->recurring != 0) { ?>
            <div role="tabpanel" class="tab-pane" id="tab_child_expenses">
                <?php if (count($child_expenses) > 0) { ?>
                <h4 class="mbot25 mtop25">
                    <?= _l('expenses_created_from_this_recurring_expense'); ?>
                </h4>
                <ul class="list-group">
                    <?php foreach ($child_expenses as $recurring) { ?>
                    <li class="list-group-item">
                        <a href="<?= admin_url('expenses/list_expenses/' . $recurring->expenseid); ?>"
                            onclick="init_expense(<?= e($recurring->expenseid); ?>); return false;"
                            target="_blank"><?= e($recurring->category_name . (! empty($recurring->expense_name) ? ' (' . e($recurring->expense_name) . ')' : '')); ?>
                        </a>
                        <br />
                        <span class="inline-block mtop10">
                            <?= '<span class="bold">' . e(_d($recurring->date)) . '</span>'; ?><br />
                            <p><span
                                    class="bold font-medium"><?= _l('expense_amount'); ?></span>
                                <span class="text-danger bold font-medium">
                                    <?= e(app_format_money($recurring->amount, $recurring->currency_data)); ?>
                                </span>
                                <?php
          if ($recurring->tax != 0) {
              echo '<br /><span class="bold">' . _l('tax_1') . ':</span> ' . e($recurring->taxrate) . '% (' . e($recurring->tax_name) . ')';
              $total = $recurring->amount;
              $total += ($total / 100 * $recurring->taxrate);
          }
                        if ($recurring->tax2 != 0) {
                            echo '<br /><span class="bold">' . _l('tax_2') . ':</span> ' . e($recurring->taxrate2) . '% (' . e($recurring->tax_name2) . ')';
                            $total += ($recurring->amount / 100 * $recurring->taxrate2);
                        }
                        if ($recurring->tax != 0 || $recurring->tax2 != 0) {
                            echo '<p class="font-medium bold text-danger">' . _l('total_with_tax') . ': ' . e(app_format_money($total, $recurring->currency_data)) . '</p>';
                        }
                        ?>
                        </span>
                    </li>
                    <?php } ?>
                </ul>
                <?php } else { ?>
                <p class="bold">
                    <?= e(_l('no_child_found', _l('expenses'))); ?>
                </p>
                <?php } ?>
            </div>
            <?php } ?>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
                <?php init_relation_tasks_table(['data-new-rel-id' => $expense->expenseid, 'data-new-rel-type' => 'expense'], 'tasksFilters'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
                <a href="#" data-toggle="modal" class="btn btn-primary"
                    data-target=".reminder-modal-expense-<?= e($expense->id); ?>"><i
                        class="fa-regular fa-bell"></i>
                    <?= _l('expense_set_reminder_title'); ?></a>
                <hr />
                <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                <?php $this->load->view('admin/includes/modals/reminder', ['id' => $expense->id, 'name' => 'expense', 'members' => $members, 'reminder_title' => _l('expense_set_reminder_title')]); ?>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    init_btn_with_tooltips();
    init_selectpicker();
    init_datepicker();
    init_form_reminder();
    init_tabs_scrollable();

    if ($('#dropzoneDragArea').length > 0) {
        if (typeof(expensePreviewDropzone) != 'undefined') {
            expensePreviewDropzone.destroy();
        }
        expensePreviewDropzone = new Dropzone("#expense-receipt-upload", appCreateDropzoneOptions({
            clickable: '#dropzoneDragArea',
            maxFiles: 1,
            success: function(file, response) {
                init_expense( <?= e($expense->expenseid); ?> );
            }
        }));
    }
</script>