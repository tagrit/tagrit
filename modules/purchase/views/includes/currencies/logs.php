<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div  class="row">
	<div class="row">    
      <div class="_buttons col-md-12">
			<hr>
        <div class="col-md-3">
        	<?php echo render_select('from_currency_logs',$currencies,array('id','name'),'from_currency', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        </div>
        <div class="col-md-3">
        	<?php echo render_select('to_currency_logs',$currencies,array('id','name'),'to_currency', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
        </div>
        <div class="col-md-3">
        	<?php echo render_date_input('date','pur_date', _d(date('Y-m-d'))); ?>
        </div>
    </div>
  </div>
	<div class="clearfix"></div>
	<br>
	<div class="clearfix"></div>
	<div  class="col-md-12">
		<table class="table table-currency-rate-logs scroll-responsive">
			<thead>
				<tr>
					<th><?php echo _l('pur_type'); ?></th>
					<th><?php echo _l('pur_currency_rate'); ?></th>
					<th><?php echo _l('pur_date'); ?></th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
		</table>
	</div>
</div>


<div id="modal_wrapper"></div>

