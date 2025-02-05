<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="section-statement">
    <div>
        <h4 class="customer-statement-heading">
            <?= _l('customer_statement'); ?>
        </h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <select class="selectpicker" name="range" id="range" data-width="100%"
                        onchange="render_customer_statement();">
                        <option
                            value='<?= e($period_today); ?>'
                            <?php if ($period_selected == $period_today) {
                                echo ' selected';
                            } ?>>
                            <?= _l('today'); ?>
                        </option>
                        <option
                            value='<?= e($period_this_week); ?>'
                            <?php if ($period_selected == $period_this_week) {
                                echo ' selected';
                            } ?>>
                            <?= _l('this_week'); ?>
                        </option>
                        <option
                            value='<?= e($period_this_month); ?>'
                            <?php if ($period_selected == $period_this_month) {
                                echo ' selected';
                            } ?>>
                            <?= _l('this_month'); ?>
                        </option>
                        <option
                            value='<?= e($period_last_month); ?>'
                            <?php if ($period_selected == $period_last_month) {
                                echo ' selected';
                            } ?>>
                            <?= _l('last_month'); ?>
                        </option>
                        <option
                            value='<?= e($period_this_year); ?>'
                            <?php if ($period_selected == $period_this_year) {
                                echo ' selected';
                            } ?>>
                            <?= _l('this_year'); ?>
                        </option>
                        <option
                            value='<?= e($period_last_year); ?>'
                            <?php if ($period_selected == $period_last_year) {
                                echo ' selected';
                            } ?>>
                            <?= _l('last_year'); ?>
                        </option>
                        <option value="period" <?php if ($custom_period) {
                            echo ' selected';
                        } ?>><?= _l('period_datepicker'); ?>
                        </option>
                    </select>
                </div>
                <div class="row mtop15">
                    <div class="col-md-12 period<?php if (! $custom_period) {
                        echo ' hide';
                    } ?>">
                        <?= render_date_input('period-from', '', ($custom_period ? $from : ''), ['onchange' => 'render_customer_statement();']); ?>
                    </div>
                    <div class="col-md-12 period<?php if (! $custom_period) {
                        echo ' hide';
                    } ?>">
                        <?= render_date_input('period-to', '', ($custom_period ? $to : ''), ['onchange' => 'render_customer_statement();']); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="text-right _buttons pull-right">
                    <a href="<?= site_url('clients/statement_pdf?from=' . urlencode($from) . '&to=' . urlencode($to) . '&print=true'); ?>"
                        id="statement_print" target="_blank" class="btn btn-default btn-with-tooltip mright5"
                        data-toggle="tooltip"
                        title="<?= _l('print'); ?>"
                        data-placement="bottom">
                        <i class="fa fa-print"></i>
                    </a>
                    <a href="<?= site_url('clients/statement_pdf?from=' . urlencode($from) . '&to=' . urlencode($to)); ?>"
                        id="statement_pdf" class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip"
                        title="<?= _l('view_pdf'); ?>"
                        data-placement="bottom">
                        <i class="fa-regular fa-file-pdf"></i>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mtop15">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <address class="text-right">
                                    <?= format_organization_info(); ?>
                                </address>
                            </div>
                            <div class="col-md-12">
                                <hr />
                            </div>
                            <div class="col-md-7">
                                <address>
                                    <p><?= _l('statement_bill_to'); ?>:
                                    </p>
                                    <?= format_customer_info($client, 'statement', 'billing'); ?>
                                </address>
                            </div>
                            <div class="col-md-5">
                                <div class="text-right">
                                    <h4 class="no-margin bold">
                                        <?= _l('account_summary'); ?>
                                    </h4>
                                    <p class="text-muted">
                                        <?= _l('statement_from_to', [$from, $to]); ?>
                                    </p>
                                    <hr />
                                    <table class="table statement-account-summary">
                                        <tbody>
                                            <tr>
                                                <td class="text-left">
                                                    <?= _l('statement_beginning_balance'); ?>:
                                                </td>
                                                <td><?= e(app_format_money($statement['beginning_balance'], $statement['currency'])); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left">
                                                    <?= _l('invoiced_amount'); ?>:
                                                </td>
                                                <td><?= e(app_format_money($statement['invoiced_amount'], $statement['currency'])); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left">
                                                    <?= _l('amount_paid'); ?>:
                                                </td>
                                                <td><?= e(app_format_money($statement['amount_paid'], $statement['currency'])); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-left">
                                                    <b><?= _l('balance_due'); ?></b>:
                                                </td>
                                                <td><?= e(app_format_money($statement['balance_due'], $statement['currency'])); ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center bold">
                                    <p class="mbot20">
                                        <?= _l('customer_statement_info', [$from, $to]); ?>
                                    </p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th><b><?= _l('statement_heading_date'); ?></b>
                                                </th>
                                                <th><b><?= _l('statement_heading_details'); ?></b>
                                                </th>
                                                <th class="text-right">
                                                    <b><?= _l('statement_heading_amount'); ?></b>
                                                </th>
                                                <th class="text-right">
                                                    <b><?= _l('statement_heading_payments'); ?></b></b>
                                                </th>
                                                <th class="text-right">
                                                    <b><?= _l('statement_heading_balance'); ?></b></b>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= e($from); ?>
                                                </td>
                                                <td><?= _l('statement_beginning_balance'); ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= e(app_format_money($statement['beginning_balance'], $statement['currency'], true)); ?>
                                                </td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= e(app_format_money($statement['beginning_balance'], $statement['currency'], true)); ?>
                                                </td>
                                            </tr>
                                            <?php
                                                        $tmpBeginningBalance = $statement['beginning_balance'];

foreach ($statement['result'] as $data) { ?>
                                            <tr>
                                                <td><?= e(_d($data['date'])); ?>
                                                </td>
                                                <td>
                                                    <?php
      if (isset($data['invoice_id'])) {
          echo _l('statement_invoice_details', ['<a href="' . site_url('invoice/' . $data['invoice_id']) . '/' . $data['hash'] . '" target="_blank">' . e(format_invoice_number($data['invoice_id'])) . '</a>', e(_d($data['duedate']))]);
      } elseif (isset($data['payment_id'])) {
          echo e(_l('statement_payment_details', ['#' . $data['payment_id'], format_invoice_number($data['payment_invoice_id'])]));
      } elseif (isset($data['credit_note_id'])) {
          echo e(_l('statement_credit_note_details', format_credit_note_number($data['credit_note_id'])));
      } elseif (isset($data['credit_id'])) {
          echo e(_l(
              'statement_credits_applied_details',
              [
                  format_credit_note_number($data['credit_applied_credit_note_id']),
                  app_format_money($data['credit_amount'], $statement['currency'], true),
                  format_invoice_number($data['credit_invoice_id']),
              ]
          ));
      } elseif (isset($data['credit_note_refund_id'])) {
          echo e(_l('statement_credit_note_refund', format_credit_note_number($data['refund_credit_note_id'])));
      }
    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
    if (isset($data['invoice_id'])) {
        echo e(app_format_money($data['invoice_amount'], $statement['currency'], true));
    } elseif (isset($data['credit_note_id'])) {
        echo e(app_format_money($data['credit_note_amount'], $statement['currency'], true));
    }
    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                    if (isset($data['payment_id'])) {
                                        echo e(app_format_money($data['payment_total'], $statement['currency'], true));
                                    } elseif (isset($data['credit_note_refund_id'])) {
                                        echo e(app_format_money($data['refund_amount'], $statement['currency'], true));
                                    }
    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                 if (isset($data['invoice_id'])) {
                                     $tmpBeginningBalance = ($tmpBeginningBalance + $data['invoice_amount']);
                                 } elseif (isset($data['payment_id'])) {
                                     $tmpBeginningBalance = ($tmpBeginningBalance - $data['payment_total']);
                                 } elseif (isset($data['credit_note_id'])) {
                                     $tmpBeginningBalance = ($tmpBeginningBalance - $data['credit_note_amount']);
                                 } elseif (isset($data['credit_note_refund_id'])) {
                                     $tmpBeginningBalance = ($tmpBeginningBalance + $data['refund_amount']);
                                 }
    if (! isset($data['credit_id'])) {
        echo e(app_format_money($tmpBeginningBalance, $statement['currency'], true));
    }
    ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot class="statement_tfoot">
                                            <tr>
                                                <td colspan="3" class="text-right">
                                                    <b><?= _l('balance_due'); ?></b>
                                                </td>
                                                <td class="text-right" colspan="2">
                                                    <b><?= e(app_format_money($statement['balance_due'], $statement['currency'])); ?></b>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>