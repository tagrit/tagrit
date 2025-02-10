<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('ma_setting', '', 'create')){ ?>
<div>
   <a href="<?php echo admin_url('ma/email_template'); ?>" class="btn btn-primary mbot15"><?php echo _l('ma_add_new', _l('email_template')); ?></a>
</div>

<?php } ?>

<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
            _l('id'),
				_l('name'),
				_l('category'),
				_l('dateadded'),
				);
			render_datatable($table_data,'email-templates');
		?>
	</div>
</div>

<div class="modal fade email-template" id="clone_email_template_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('ma/clone_email_template'), array('id' => 'clone-email-template-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('clone_template'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    	<div class="col-md-12">
                        <?php echo form_hidden('id'); ?>
                        <?php echo render_input('name', 'name'); ?>
                    	</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>