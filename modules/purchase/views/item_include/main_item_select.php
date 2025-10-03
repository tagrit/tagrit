<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="form-group mbot25  select-placeholder">
     <select name="item_select" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%"  id="item_select" data-none-selected-text="<?php echo _l('select_item'); ?>" data-live-search="true">
      <option value=""></option>
      <?php foreach($items as $group_id=>$_items){ ?>
      <optgroup data-group-id="<?php echo $group_id; ?>" label="<?php echo $_items[0]['group_name']; ?>">
       <?php foreach($_items as $item){ ?>
       <option value="<?php echo $item['id']; ?>" data-subtext="<?php echo strip_tags(mb_substr($item['long_description'] ?? '',0,200)).'...'; ?>">(<?php echo app_format_number($item['purchase_price']); ; ?>) <?php echo $item['description']; ?></option>
       <?php } ?>
     </optgroup>
     <?php } ?>
   </select>
</div>
