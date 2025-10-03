<?php $follow_1 = [ 
  1 => ['id' => 'and', 'name' => _l('ma_and')],
  2 => ['id' => 'or', 'name' => _l('ma_or')],
]; ?>

]; ?>
<?php $follow_2 = [ 
  1 => ['id' => 'equals', 'name' => _l('equals')],
  2 => ['id' => 'not_equal', 'name' => _l('not_equal')],
  3 => ['id' => 'greater_than', 'name' => _l('greater_than')],
  4 => ['id' => 'greater_than_or_equal', 'name' => _l('greater_than_or_equal')],
  5 => ['id' => 'less_than', 'name' => _l('less_than')],
  6 => ['id' => 'less_than_or_equal', 'name' => _l('less_than_or_equal')],
  7 => ['id' => 'empty', 'name' => _l('empty')],
  8 => ['id' => 'not_empty', 'name' => _l('not_empty')],
  9 => ['id' => 'like', 'name' => _l('like')],
  10 => ['id' => 'not_like', 'name' => _l('not_like')],
]; ?>

<div id="item_approve">
  <div class="row">
    <div class="col-md-3">
      <div class="select-placeholder form-group">
          <label for="sub_type_1[0]"></label>
          <select name="sub_type_1[0]" id="sub_type_1[0]" data-index="0" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
            <?php foreach($follow_1 as $val){
             $selected = '';
             ?>
             <option value="<?php echo html_entity_decode($val['id']); ?>">
               <?php echo html_entity_decode($val['name']); ?>
             </option>
           <?php } ?>
         </select>
       </div> 
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">                            
        <?php echo _l($type); ?>
   </div>
   <div class="col-md-3" id="div_subtype_0">                            
      <div class="select-placeholder form-group">
        <label for="sub_type_2[0]"></label>
        <select name="sub_type_2[0]" id="sub_type_2[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
          <?php foreach($follow_2 as $val){
           $selected = '';
           ?>
           <option value="<?php echo html_entity_decode($val['id']); ?>">
             <?php echo html_entity_decode($val['name']); ?>
           </option>
         <?php } ?>
        </select>
      </div> 
   </div>
   <div class="col-md-3">                            
      <div class="form-group" app-field-wrapper="name">
        <label for="text[0]" class="control-label"></label>
        <input type="text" id="text[0]" name="text[0]" class="form-control" value="">
      </div>
   </div>
   <div class="col-md-1">
      <button name="add" class="btn new_vendor_requests btn-success mtop20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
  </div>
</div>
</div>