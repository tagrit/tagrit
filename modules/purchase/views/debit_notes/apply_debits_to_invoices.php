<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if($debit_note->status == 1) { ?>
<!-- Modal Apply Credits -->
<div class="modal fade apply-debits-to-invoice" id="apply_debits" data-debits-remaining="<?php echo $debit_note->remaining_debits; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabelApplyDebits">
  <div class="modal-dialog modal-lg" role="document">
    <?php echo form_open(admin_url('purchase/apply_debits_to_invoices/'.$debit_note->id),array('id'=>'apply_debits_form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalLabelApplyDebits">
            <?php echo _l('apply_debits_form').' '.format_debit_note_number($debit_note->id); ?>
        </h4>
    </div>
    <div class="modal-body">
        <?php if(count($available_debitable_invoices) > 0) {?>
        <div class="table-responsive debits-table">
            <table class="table table-bordered no-mtop">
                <thead>
                   <tr>
                    <th><span class="bold"><?php echo _l('debit_invoice_number'); ?> #</span></th>
                    <th><span class="bold"><?php echo _l('debit_invoice_date'); ?></span></th>
                    <th><span class="bold"><?php echo _l('payment_table_invoice_amount_total'); ?></span></th>
                    <th><span class="bold"><?php echo _l('invoice'); ?> <?php echo _l('balance_due'); ?></span></th>
                    <th><span class="bold"><?php echo _l('amount_to_debit'); ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($available_debitable_invoices as $invoice) {
                    $_currency = get_base_currency_pur();
                    if($invoice['invoice_currency'] != 0){
                        $_currency = pur_get_currency_by_id($invoice['invoice_currency']);
                    }
                    ?>

                    <?php if($_currency->id == $debit_note->currency){ ?>
                        <tr>
                            <td><a href="<?php echo admin_url('purchase/purchase_invoice/'.$invoice['id']); ?>" target="_blank"><?php echo pur_html_entity_decode($invoice['invoice_number']); ?></a></td>
                            <td><?php echo _d($invoice['invoice_date']); ?></td>
                            <td><?php echo app_format_money($invoice['total'], $_currency->symbol) ?></td>
                            <td><?php echo app_format_money($invoice['total_left_to_pay'],  $_currency->symbol) ?></td>
                            <td>
                                <input type="number" name="amount[<?php echo $invoice['id']; ?>]" class="form-control apply-debits-field" value="0">
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-6">
            <div class="text-right">
                <table class="table">
                    <tbody>
                     <tr>
                        <td class="bold"><?php echo _l('amount_to_debit'); ?>:</td>
                        <td class="amount-to-debit">
                            <?php echo app_format_money(0, $debit_note->currency_name); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold"><?php echo _l('debit_note_remaining_debits'); ?>:</td>
                        <td class="debit-note-balance-due">
                            <?php echo app_format_money($debit_note->remaining_debits, $debit_note->currency_name); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } else { ?>
<p class="bold no-mbot"><?php echo _l('debit_note_no_invoices_available'); ?></p>
<?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    <?php if(count($available_debitable_invoices) > 0) { ?>
    <button type="submit" class="btn btn-info"><?php echo _l('apply'); ?></button>
    <?php } ?>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
 <?php require 'modules/purchase/assets/js/apply_debits_to_invoice_js.php';?>  

<?php } ?>
