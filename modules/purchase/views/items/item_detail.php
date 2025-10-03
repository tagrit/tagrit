<div class="col-md-12">
<div class="panel_s">
  <div class="panel-body">
      <?php $base_currency = get_base_currency_pur(); ?>
      <div class="row col-md-12">

        <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
        <hr class="hr-color">

        <div class="col-md-5">
          <div class="gallery">
            <div class="wrapper-masonry">
              <div id="masonry" class="masonry-layout columns-2">
            <?php if(isset($item_file) && count($item_file) > 0){ ?>
              <?php foreach ($item_file as $key => $value) { ?>
                  <?php if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$value["rel_id"].'/'.$value["file_name"])){ ?>
                        <a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
                    <?php }elseif(file_exists('modules/warehouse/uploads/item_img/' .$value["rel_id"].'/'.$value["file_name"])){ ?>
                       <a  class="images_w_table" href="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
                    <?php }else{ ?>

                      <a  class="images_w_table" href="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/manufacturing/uploads/products/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo pur_html_entity_decode($value['file_name']) ?>"/></a>
                    <?php } ?>
            <?php } ?>
          <?php }else{ ?>
              <?php 
              $_img = ''; 
              if(isset($vendor_image) && count($vendor_image) > 0){ 
                foreach($vendor_image as $value){
                  if(file_exists(PURCHASE_PATH.'vendor_items/' .$item->from_vendor_item .'/'.$value['file_name'])){
                    $_img .= '<a  class="images_w_table" href="'.site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'"><img class="images_w_table" src="'. site_url('modules/purchase/uploads/vendor_items/'.$value["rel_id"].'/'.$value["file_name"]).'" alt="'. pur_html_entity_decode($value['file_name']).'"/></a>';
                  }
                }
              }else{
                $_img .= '<a href="'.site_url('modules/purchase/uploads/nul_image.jpg').'"><img class="images_w_table" src="'.site_url('modules/purchase/uploads/nul_image.jpg').'" alt="nul_image.jpg"/></a>';
              }

              echo $_img;
              ?>
          <?php } ?>
            <div class="clear"></div>
          </div>
        </div>
        </div>
        </div>
        
        <div class="col-md-7 panel-padding">
          <table class="table border table-striped no-margin">
              <tbody>
                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->commodity_code) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('commodity_name'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->description) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('commodity_barcode'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->commodity_barcode) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('sku_code'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->sku_code) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('sku_name'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->sku_name) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('item_group'); ?></td>
                    <td><?php echo get_group_name_item(pur_html_entity_decode($item->group_id)) != null ? get_group_name_item(pur_html_entity_decode($item->group_id))->name : '' ; ?></td>
                 </tr>
                 
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('rate'); ?></td>
                    <td><?php echo app_format_money((float)$item->rate,$base_currency->symbol) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('purchase_price'); ?></td>
                    <td><?php echo app_format_money((float)$item->purchase_price,$base_currency->symbol) ; ?></td>
                 </tr>
                 
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('unit_id'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->unit_id) != '' && get_unit_type_item($item->unit_id) != null ? get_unit_type_item($item->unit_id)->unit_name : ''; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('tax_1'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->tax) != '' && get_tax_rate_item($item->tax) != null ? get_tax_rate_item($item->tax)->name : '';  ?></td>
                 </tr> 
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('tax_2'); ?></td>
                    <td><?php echo pur_html_entity_decode($item->tax2) != '' && get_tax_rate_item($item->tax2) != null ? get_tax_rate_item($item->tax2)->name : '';  ?></td>
                 </tr> 
                </tbody>
          </table>

          <?php $custom_fields = get_custom_fields('items');
           foreach($custom_fields as $field){ ?>
            <?php $value = get_custom_field_value($item->id,$field['id'],'items_pr');
                if($value == ''){continue;}?>
            <div class="task-info">
            <h5 class="task-info-custom-field task-info-custom-field-<?php echo $field['id']; ?>">
              <i class="fa task-info-icon fa-fw fa-lg fa-pencil-square"></i>
              <?php echo $field['name']; ?>: <?php echo $value; ?>
            </h5>
             </div>
            <?php } ?>
      </div>
    </div>
      <div class="col-md-12">
      <?php if(isset($inventory_item)){ 
            foreach ($inventory_item as $value) {
              $purchase_code = $value['purchase_code'] ? $value['purchase_code'] :'' ;
              $inventory_number = $value['inventory_number'] ? $value['inventory_number'] :'' ;
              $unit_name = $value['unit_name'] ? $value['unit_name'] :'' ;
        ?>
        <div class="col-md-3 bg-c-blue card1" >
            <div class="card-block">
                <h3 class="text-right h3-card-block-margin"><i class="fa fa-cart-plus f-left"></i><span class="h3-span-font-size"><?php echo pur_html_entity_decode($purchase_code); ?></span></h3>
                <p class="m-b-0 p-card-block-font-size"><?php echo _l('inventory_number') ;?><span class="f-right p-card-block-font-size" ><?php echo pur_html_entity_decode($inventory_number); ?></span></p>
            </div>
        </div>
        <?php } ?>
      <?php } ?>
      </div>
    </div>
    </div>
  </div>
<script type="text/javascript">
  (function() {
        var gallery = new SimpleLightbox('.gallery a', {});
    })();
</script>