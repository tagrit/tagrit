<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('ma_components', '', 'create')){ ?>
<div>
	<a href="<?php echo admin_url('ma/form'); ?>" class="btn btn-primary mbot15"><?php echo _l('ma_add_new', _l('form')); ?></a>
</div>
<hr class="hr-panel-heading">

<?php } ?>

<div class="row">
  <div class="col-md-3">
        <?php echo render_select('category',$categories,array('id','name'),'category', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
  </div>
</div>
<hr class="hr-panel-heading">
<div class="row">
	<div class="col-md-12">
		<?php render_datatable(array(
       _l('id'),
       _l('form_name'),
       _l('total_submissions'),
       _l('leads_dt_datecreated'),
       ),'form'); ?>
	</div>
</div>
<div class="clearfix"></div>