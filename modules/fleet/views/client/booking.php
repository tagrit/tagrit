<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg section-heading section-heading-booking">
    <?php echo _l('fleet_booking'); ?>
</h4>
<?php echo form_open_multipart('fleet/fleet_client/booking', ['id' => 'open-new-ticket-form']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group booking-subject-group">
                            <label for="subject"><?php echo _l('customer_ticket_subject'); ?></label>
                            <input type="text" class="form-control" name="subject" id="subject"
                                value="<?php echo set_value('subject'); ?>">
                            <?php echo form_error('subject'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group booking-date-group">
                                    <label class="control-label" for="delivery_date"><?php echo _l('delivery_date'); ?></label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control datepicker" name="delivery_date" id="delivery_date"
                                            value="<?php echo set_value('delivery_date'); ?>">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                    <?php echo form_error('delivery_date'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group booking-phone-group">
                                    <label for="phone"><?php echo _l('phone'); ?></label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        value="<?php echo set_value('phone'); ?>">
                                    <?php echo form_error('phone'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group booking-receipt-address-group">
                            <label for="receipt_address"><?php echo _l('receipt_address'); ?></label>
                            <textarea name="receipt_address" id="receipt_address" class="form-control"
                                placeholder="<?php echo _l('receipt_address'); ?>"
                                rows="3"><?php echo set_value('receipt_address'); ?></textarea>
                            <?php echo form_error('receipt_address'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group booking-delivery-address-group">
                            <label for="delivery_address"><?php echo _l('delivery_address'); ?></label>
                            <textarea name="delivery_address" id="delivery_address" class="form-control"
                                placeholder="<?php echo _l('delivery_address'); ?>"
                                rows="3"><?php echo set_value('delivery_address'); ?></textarea>
                                    <?php echo form_error('delivery_address'); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group booking-note-group">
                    <label for="note"><?php echo _l('note'); ?></label>
                    <textarea name="note" id="note" class="form-control"
                        placeholder="<?php echo _l('note'); ?>"
                        rows="8"><?php echo set_value('note'); ?></textarea>
                    <?php echo form_error('note'); ?>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button type="submit" class="btn btn-primary" data-form="#open-new-ticket-form" autocomplete="off"
                    data-loading-text="<?php echo _l('wait_text'); ?>">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>