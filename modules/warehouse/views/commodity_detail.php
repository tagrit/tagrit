<div class="col-md-12">
<div class="panel_s">
  <div class="panel-body">
    
      <div class="row col-md-12">

        <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
        <hr class="hr-color">

        
        
        <div class="col-md-7 panel-padding">
          <table class="table border table-striped table-margintop">
              <tbody>

                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('commodity_code'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->commodity_code) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('commodity_name'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->description) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('commodity_group'); ?></td>
                    <td><?php echo get_wh_group_name(new_html_entity_decode($commodites->group_id)) != null ? get_wh_group_name(new_html_entity_decode($commodites->group_id))->name : '' ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('commodity_barcode'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->commodity_barcode) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('sku_code'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->sku_code) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('sku_name'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->sku_name) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('tax_1'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->tax) != '' && get_tax_rate($commodites->tax) != null ? get_tax_rate($commodites->tax)->name : '';  ?></td>
                 </tr> 
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('tax_2'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->tax2) != '' && get_tax_rate($commodites->tax2) != null ? get_tax_rate($commodites->tax2)->name : '';  ?></td>
                 </tr> 
                

                </tbody>
          </table>
      </div>

        <div class="gallery">
            <div class="wrapper-masonry">
              <div id="masonry" class="masonry-layout columns-2">
            <?php if(isset($commodity_file) && count($commodity_file) > 0){ ?>
              <?php foreach ($commodity_file as $key => $value) { ?>

                  <?php if(file_exists(WAREHOUSE_ITEM_UPLOAD .$value["rel_id"].'/'.$value["file_name"])){ ?>
                      <a  class="images_w_table" href="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>
                       
                    <?php }elseif(file_exists('modules/purchase/uploads/item_img/'. $value["rel_id"] . '/' . $value["file_name"])) { ?>
                      <a  class="images_w_table" href="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>"><img class="images_w_table" src="<?php echo site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]); ?>" alt="<?php echo new_html_entity_decode($value['file_name']) ?>"/></a>
                        
                       
                    <?php } ?>


            <?php } ?>
          <?php }else{ ?>

                <a href="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>"><img src="<?php echo site_url('modules/warehouse/uploads/nul_image.jpg'); ?>" alt="nul_image.jpg"/></a>

          <?php } ?>
            <div class="clear"></div>
          </div>
        </div>
        </div>
        <br>

          


    </div>

    <h4 class="h4-color"><?php echo _l('infor_detail'); ?></h4>
      <hr class="hr-color">
      <div class="col-md-6 panel-padding" >
        <table class="table border table-striped table-margintop" >
            <tbody>
               <tr class="project-overview">
                  <td class="bold td-width"><?php echo _l('origin'); ?></td>
                    <td><?php echo new_html_entity_decode($commodites->origin) ; ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('colors'); ?></td>
                  <?php
                  $color_value ='';
                  if($commodites->color){
                    $color = get_color_type($commodites->color);
                    if($color){
                      $color_value .= $color->color_code.'_'.$color->color_name;
                    }
                  }
                   ?>
                    <td><?php echo new_html_entity_decode($color_value) ; ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('style_id'); ?></td>
                <td><?php  if($commodites->style_id != null){ echo get_style_name(new_html_entity_decode($commodites->style_id)) != null ? get_style_name(new_html_entity_decode($commodites->style_id))->style_name : '';}else{echo '';} ?></td>
               </tr>

                <tr class="project-overview">
                  <td class="bold"><?php echo _l('rate'); ?></td>
                  <td><?php echo app_format_money((float)$commodites->rate,'') ; ?></td>
               </tr>

            </tbody>
        </table>
      </div>
       
      <div class="col-md-6 panel-padding" >
        <table class="table table-striped table-margintop">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="40%"><?php echo _l('model_id'); ?></td>
                   <td><?php if($commodites->style_id != null){ echo get_model_name(new_html_entity_decode($commodites->model_id)) != null ? get_model_name(new_html_entity_decode($commodites->model_id))->body_name : ''; }else{echo '';}?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('size_id'); ?></td>

                  <td><?php if($commodites->style_id != null){ echo get_size_name(new_html_entity_decode($commodites->size_id)) != null ? get_size_name(new_html_entity_decode($commodites->size_id))->size_name : ''; }else{ echo '';}?></td>
               </tr>
               
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('unit_id'); ?></td>
                    <td><?php echo  $commodites->unit_id != '' && get_unit_type($commodites->unit_id) != null ? get_unit_type($commodites->unit_id)->unit_name : ''; ?></td>
                 </tr> 

                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('purchase_price'); ?></td>
                    <td><?php echo app_format_money((float)$commodites->purchase_price,'') ; ?></td>
                 </tr>
                 
              
              
              </tbody>
            </table>
      </div>

       <h4 class="h4-color"><?php echo _l('description'); ?></h4>
      <hr class="hr-color">
      <p><?php echo new_html_entity_decode($commodites->long_description) ; ?></p>
      
          
    </div>
      
    </div>

  </div>
         
<?php require 'modules/warehouse/assets/js/commodity_detail_js.php';?>
