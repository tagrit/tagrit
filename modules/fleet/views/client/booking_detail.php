<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg section-heading section-heading-booking">
    <?php echo _l('fleet_booking'); ?>
</h4>

<div class="panel_s">
	<div class="panel-body">
		<h4 class="invoice-html-status mtop7">
		</h4>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                <h4><?php echo _l('general_info'); ?></h4>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('subject'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                        <?php echo new_html_entity_decode($booking->subject); ?>
                    </span>
					<?php echo fleet_render_status_html($booking->id, 'booking', $booking->status); ?>
                </p>
                <div class="row">
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('delivery_date'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo _d($booking->delivery_date); ?>
		                    </span>
		                </p>
                	</div>
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('phone'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo new_html_entity_decode($booking->phone); ?>
		                    </span>
		                </p>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('delivery_date'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo _d($booking->delivery_date); ?>
		                    </span>
		                </p>
                	</div>
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('phone'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo new_html_entity_decode($booking->phone); ?>
		                    </span>
		                </p>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('receipt_address'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo new_html_entity_decode($booking->receipt_address); ?>
		                    </span>
		                </p>
                	</div>
                	<div class="col-md-6">
		                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
		                    <?php echo _l('delivery_address'); ?>:
		                    <span class="tw-font-medium tw-text-neutral-700">
		                        <?php echo new_html_entity_decode($booking->delivery_address); ?>
		                    </span>
		                </p>
                	</div>
                </div>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('note'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                        <?php echo new_html_entity_decode($booking->note); ?>
                    </span>
                </p>
                <hr>
                <h4 class=""><?php echo _l('admin_info'); ?></h4>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('invoice'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                    	<a href="<?php echo site_url('invoice/' . $booking->invoice_id . '/' . $booking->invoice_hash); ?>" class="invoice-number"><?php echo format_invoice_number($booking->invoice_id); ?></a>
                    </span>
                </p>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('amount'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                        <?php echo app_format_money($booking->amount, ''); ?>
                    </span>
                </p>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('admin_note'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                        <?php echo new_html_entity_decode($booking->admin_note); ?>
                    </span>
                </p>

				<?php if($booking->rating != 0){ ?>
                <hr>
                <h4 class=""><?php echo _l('rating'); ?></h4>
            	<div class="star-rating-view mtop25">
            		<span class="tw-py-2.5 tw-mb-0 tw-text-neutral-500"><?php echo _l('rating'); ?>:</span>
                    <span class="fa fa-star margin-top-8" data-rating="1"></span>
                    <span class="fa fa-star margin-top-8" data-rating="2"></span>
                    <span class="fa fa-star margin-top-8" data-rating="3"></span>
                    <span class="fa fa-star margin-top-8" data-rating="4"></span>
                    <span class="fa fa-star margin-top-8" data-rating="5"></span>
                    <input type="hidden" name="rating" class="rating-value" value="<?php echo new_html_entity_decode($booking->rating); ?>">
                 </div>
                <p class="tw-py-2.5 tw-mb-0 tw-text-neutral-500">
                    <?php echo _l('rating_comments'); ?>:
                    <span class="tw-font-medium tw-text-neutral-700">
                        <?php echo new_html_entity_decode($booking->comments); ?>
                    </span>
                </p>
				<?php } ?>
			</div>
		</div>
		<hr>
			<div class="btn-bottom-toolbar text-right">
				<?php if($booking->rating == 0 && $booking->status == 'complete'){ ?>
					<a href="#" onclick="rating(); return false;" class="btn btn-info text-right mright5"><?php echo _l('rating'); ?></a>
				<?php } ?>
				<a href="<?php echo site_url('fleet/fleet_client'); ?>" class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
			</div>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>

<div class="modal fade" id="rating-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content width-100">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?php echo _l('rating'); ?></span>
                </h4>
            </div>
            <?php echo form_open('fleet/fleet_client/rating/'.$booking->id, array('id' => 'rating-modal')); ?>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                	<label><?php echo _l('rating'); ?></label>
          			<p><div class="star-rating">
	                    <span class="fa fa-star margin-top-8" data-rating="1"></span>
	                    <span class="fa fa-star margin-top-8" data-rating="2"></span>
	                    <span class="fa fa-star margin-top-8" data-rating="3"></span>
	                    <span class="fa fa-star margin-top-8" data-rating="4"></span>
	                    <span class="fa fa-star margin-top-8" data-rating="5"></span>
	                    <input type="hidden" name="rating" class="rating-value" value="5">
	                 </div>
	             	</p>
                </div>
              </div>
              <?php echo render_textarea('comments', 'rating_comments', ''); ?>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php require 'modules/fleet/assets/js/client/booking_detail_js.php';?>
