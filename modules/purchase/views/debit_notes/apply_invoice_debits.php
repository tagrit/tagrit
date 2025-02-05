<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if((debits_can_be_applied_to_invoice($pur_invoice->payment_status) && $debits_available > 0)) { ?>
<!-- Modal Apply Credits -->
<div class="modal fade apply-debits-from-invoice" id="apply_debits" data-balance-due="<?php echo purinvoice_left_to_pay($pur_invoice->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabelApplyDebits">
  <div class="modal-dialog modal-lg" role="document">
    <?php echo form_open(admin_url('purchase/apply_debits/'.$pur_invoice->id),array('id'=>'apply_debits_form')); ?>
    <?php 
    $base_currency = get_base_currency_pur(); 
    if($pur_invoice->currency != 0){
        $base_currency = pur_get_currency_by_id($pur_invoice->currency);
    }
    ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalLabelApplyDebits">
            <?php echo pur_html_entity_decode($pur_invoice->invoice_number); ?> - <?php echo _l('apply_debits'); ?>
        </h4>
    </div>
    <div class="modal-body">
        <div class="table-responsive debits-table">
            <table class="table table-bordered no-mtop">
                <thead>
                   <tr>
                    <th><span class="bold"><?php echo _l('debit_note'); ?> #</span></th>
                    <th><span class="bold"><?php echo _l('debit_note_date'); ?></span></th>
                    <?php
                        $custom_fields = get_custom_fields('debit_note',array('show_on_table'=>1));
                        foreach($custom_fields as $field){
                          echo '<td class="bold">' . $field['name'] .'</td>';
                        }
                    ?>
                    <th><span class="bold"><?php echo _l('debit_amount'); ?></span></th>
                    <th><span class="bold"><?php echo _l('debit_available'); ?></span></th>
                    <th><span class="bold"><?php echo _l('amount_to_debit'); ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($open_debits as $debit) { ?>
                    <?php $debit_currency = pur_get_currency_by_id($debit['currency']); ?>
                    <?php if($debit_currency->id == $base_currency->id){ ?>
                    <tr>
                        <td><a href="<?php echo admin_url('purchase/debit_notes/'.$debit['id']); ?>" target="_blank"><?php echo format_debit_note_number($debit['id']); ?></a></td>
                        <td><?php echo _d($debit['date']); ?></td>

                        <td><?php echo app_format_money($debit['total'], $debit_currency) ?></td>
                        <td><?php echo app_format_money($debit['available_debits'], $debit_currency) ?></td>
                        <td>
                            <input type="number" max="<?php echo $debit['available_debits']; ?>" name="amount[<?php echo $debit['id']; ?>]" class="form-control apply-debits-field" value="0">
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
                            <?php echo app_format_money(0, $base_currency->symbol); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold"><?php echo _l('balance_due'); ?>:</td>
                        <td class="invoice-balance-due">
                            <?php echo app_format_money(purinvoice_left_to_pay( $pur_invoice->id), $base_currency->symbol); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    <button type="submit" class="btn btn-info"><?php echo _l('apply'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
 <?php require 'modules/purchase/assets/js/apply_invoice_debits_js.php';?>  

<?php } ?>
