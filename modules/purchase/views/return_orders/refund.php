<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <?php echo form_open(admin_url('purchase/' . (isset($refund) ? 'edit_order_return_refund' : 'create_order_return_refund') .'/'. (isset($refund) ? $refund->id.'/'.$refund->order_return_id : $order_return->id)), ['id'=>'order_return_refund_form']); ?>
        <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('refund'); ?> (<?php echo pur_html_entity_decode($order_return->order_return_name); ?>)</h4>
            <hr class="hr-panel-heading" />
            <div class="row">
                <div class="col-md-6">

                    <?php 
                    $refunded_amount = get_total_order_return_refunded($order_return->id);
                    $remaining_refund =  get_order_return_remaining_refund($order_return->id);

                    echo render_input('amount', 'refund_amount', isset($refund) ? $refund->amount : $remaining_refund, 'number', array('max'=>(!isset($refund) ? $remaining_refund : $remaining_refund + $refund->amount),'min'=>0)); ?>
                    <?php echo render_date_input('refunded_on', 'credit_date', isset($refund) ? _d($refund->refunded_on ) : _d(date('Y-m-d'))); ?>
                    <div class="form-group">
                        <label for="payment_mode" class="control-label"><?php echo _l('payment_mode'); ?></label>
                        <select class="selectpicker" name="payment_mode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach($payment_modes as $mode){ ?>
                                <option value="<?php echo $mode['id']; ?>"<?php if(isset($refund) && $refund->payment_mode == $mode['id']){echo ' selected'; } ?>><?php echo $mode['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                   <div class="form-gruoup">
                    <label for="note" class="control-label"><?php echo _l('note'); ?></label>
                    <textarea name="note" class="form-control" rows="8" id="note"><?php if(isset($refund)) {echo clear_textarea_breaks($refund->note);} ?></textarea>
                </div>
            </div>
        </div>
        <div class="pull-right mtop15">
            <a href="#" class="btn btn-danger" onclick="init_order_return(<?php echo $order_return->id; ?>); return false;">
                <?php echo _l('cancel'); ?>
            </a>
            <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" data-form="#order_return_refund_form" class="btn btn-success">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
<script>
   $(function(){
     init_selectpicker();
     init_datepicker();
     appValidateForm($('#order_return_refund_form'),{amount:'required',refunded_on:'required', payment_mode: 'required'});
 });
</script>
