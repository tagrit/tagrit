<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('ma_setting', '', 'create')){ ?>

<div>
	<a href="#" class="btn btn-primary mbot15 add-new-category"><?php echo _l('ma_add_new', _l('category')); ?></a>
</div>
<?php } ?>

<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
				_l('name'),
				_l('type'),
				_l('description'),
				);
			render_datatable($table_data,'category');
		?>
	</div>
</div>
<div class="clearfix"></div>


<?php 
	$types = [
      ['id' => 'segment', 'name' => _l('segment')],
      ['id' => 'stage', 'name' => _l('stage')],
      ['id' => 'point_action', 'name' => _l('point_action')],
      ['id' => 'asset', 'name' => _l('asset')],
      ['id' => 'form', 'name' => _l('form')],
      ['id' => 'email', 'name' => _l('email')],
      ['id' => 'sms', 'name' => _l('sms')],
      ['id' => 'email_template', 'name' => _l('email_template')],
      ['id' => 'text_message', 'name' => _l('text_message')],
      ['id' => 'campaign', 'name' => _l('campaign')],
	];
?>
<div class="modal fade" id="category-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('category')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('ma/category'),array('id'=>'category-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
            <?php echo render_input('name','name'); ?>
            <?php echo render_select('type',$types,array('id','name'),'type','',array(),array(),'','',false); ?>
            <?php echo render_color_picker('color',_l('color')); ?>
            <div class="row">
                <div class="col-md-12">
                  <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                  <?php echo render_textarea('description',''); ?>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>