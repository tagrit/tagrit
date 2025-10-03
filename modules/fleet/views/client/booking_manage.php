<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading section-heading-tickets">
        <?php echo _l('fleet_bookings'); ?>
    </h4>
    <a href="<?php echo site_url('fleet/fleet_client/booking'); ?>" class="btn btn-primary new-ticket">
        <i class="fa-regular fa-plus tw-mr-1"></i>
        <?php echo _l('booking'); ?>
    </a>
</div>

<div class="panel_s">
    <div class="panel-body">
        <table class="table table-booking scroll-responsive">
         <thead>
            <tr>
               <th><?php echo _l('booking_number'); ?></th>
               <th><?php echo _l('subject'); ?></th>
               <th><?php echo _l('delivery_date'); ?></th>
               <th><?php echo _l('amount'); ?></th>
               <th><?php echo _l('status'); ?></th>
               <th><?php echo _l('invoice_dt_table_heading_number'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <?php 
            $total = 0;
            $total_booking = 0;
            foreach($bookings as $booking){ ?>
               <tr>
                  <td><a href="<?php echo site_url('fleet/fleet_client/booking_detail/' . $booking['id']); ?>" class="invoice-number"><?php echo new_html_entity_decode($booking['number']); ?></a></td>
                  <td><?php echo new_html_entity_decode($booking['subject']); ?></td>
                  <td><?php echo _d($booking['delivery_date']); ?></td>
                  <td><?php 
                     $total_booking += $booking['amount'];
                     echo app_format_money($booking['amount'], $currency->name); ?></td>
                    <td>
                     <?php echo fleet_render_status_html($booking['id'], 'booking', $booking['status']); ?>
                    </td>
                  <td><a href="<?php echo site_url('invoice/' . $booking['invoice_id'] . '/' . $booking['invoice_hash']); ?>" class="invoice-number"><?php echo format_invoice_number($booking['invoice_id']); ?></a></td>
               </tr>
            <?php } ?>
            <tr>
               <td><?php echo _l('total'); ?></td>
               <td></td>
               <td></td>
               <td class="total_booking"><?php echo app_format_money($total_booking, $currency->name); ?></td>
               <td></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
    </div>
</div>

