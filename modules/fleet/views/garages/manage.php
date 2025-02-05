<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row panel">
      <div class="col-md-12">
        <h4>
          <br>
          <?php echo new_html_entity_decode($title); ?>
          <hr>          
        </h4>
        <?php 
        if(is_admin() || has_permission('fleet_garage', '', 'create')){
         ?>
         <button class="btn btn-primary mbot20" onclick="add();"><?php echo _l('add'); ?></button>          
         <div class="clearfix"></div>
       <?php } ?>
      <div class="clearfix"></div>
      <br>
      <div class="clearfix"></div>
      <table class="table table-garages scroll-responsive">
       <thead>
         <tr>
          <th>ID</th>
          <th><?php echo  _l('name'); ?></th>
          <th><?php echo  _l('address'); ?></th>
          <th><?php echo  _l('country'); ?></th>
          <th><?php echo  _l('city'); ?></th>
          <th><?php echo  _l('zip'); ?></th>
          <th><?php echo  _l('state'); ?></th>
          <th><?php echo  _l('notes'); ?></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

  </div>
</div>
</div>
</div>

<div class="modal fade" id="add_new_garages" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
         <span class="add-title hide"><?php echo _l('create_garage'); ?></span>
         <span class="edit-title"><?php echo _l('edit_garage'); ?></span>
       </h4>
     </div>
     <?php echo form_open(admin_url('fleet/garages'),array('id'=>'garages-form')); ?>
     <div class="modal-body">
      <?php 
      $this->load->view('garages/modal_content.php');
      ?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>                 
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<input type="hidden" name="are_you_sure_you_want_to_delete_these_items" value="<?php echo _l('fe_are_you_sure_you_want_to_delete_these_items') ?>">
<input type="hidden" name="please_select_at_least_one_item_from_the_list" value="<?php echo _l('please_select_at_least_one_item_from_the_list') ?>">

<input type="hidden" name="check">
<?php init_tail(); ?>
</body>
</html>
