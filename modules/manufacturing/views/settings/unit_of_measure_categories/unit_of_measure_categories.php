<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div> 

	<div class="row">
		<div class="col-md-12">
			<h4 class="h4-color"><i class="fa fa-certificate" aria-hidden="true"></i> <?php echo _l('unit_of_measure_categories'); ?></h4>
		</div>
	</div>
	<hr class="hr-color">

	<?php if(has_permission('manufacturing', '', 'create')){ ?>
		 <a href="#" onclick="new_category(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('mrp_add'); ?>
    </a>
	<?php } ?>
	<br>
	<br>

	<table class="table dt-table border table-striped">
		<thead>
			<th class="hide"><?php echo _l('id'); ?></th>
			<th><?php echo _l('category_name'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($categories as $categorie){ ?>

				<tr>
					<td class="hide"><?php echo new_html_entity_decode($categorie['id']); ?></td>
					<td><?php echo new_html_entity_decode($categorie['category_name']); ?></td>
					<td>
						<?php if (has_permission('manufacturing', '', 'edit') || is_admin()) { ?>
							<a href="#" onclick="edit_category(this,<?php echo new_html_entity_decode($categorie['id']); ?>); return false;" data-category_name="<?php echo new_html_entity_decode($categorie['category_name']); ?>" class="btn btn-default btn-icon"><i class="fa-regular fa-pen-to-square"></i>
							</a>
						<?php } ?>

						<?php if (has_permission('manufacturing', '', 'delete') || is_admin()) { ?> 
							<a href="<?php echo admin_url('manufacturing/delete_category/'.$categorie['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<div class="modal fade" id="measure_category" tabindex="-1" role="dialog">
		<div class="modal-dialog setting-handsome-table">
			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_category'), array('id'=>'add_edit_category')); ?>

			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="add-title"><?php echo _l('add_category'); ?></span>
						<span class="edit-title"><?php echo _l('update_category'); ?></span>
					</h4>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="categories_id"></div>   
							<div class="form"> 
								<div class="col-md-12">
									<?php echo render_input('category_name', 'category_name'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

					<button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>   

</body>
</html>
