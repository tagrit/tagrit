<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">               
                            <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                            <hr class="hr-color">
                            <div class="panel-padding">
                                <div class="row">
                                    <div class="col-md-6">
                                          <table class="table border table-striped no-margin">
                                              <tbody>
                                                 <tr class="project-overview">
                                                    <td class="bold"  width="30%"><?php echo _l('subject'); ?></td>
                                                    <td><?php echo new_html_entity_decode($driver_document->subject) ; ?></td>
                                                 </tr>
                                                 <?php if ($driver_document->type == 'driver') { ?>
                                                    <tr class="project-overview">
                                                        <td class="bold"  width="30%"><?php echo _l('driver'); ?></td>
                                                        <td><a href="<?php echo admin_url('fleet/driver_detail/'.$driver_document->driver_id) ?>"><?php echo get_staff_full_name($driver_document->driver_id); ?></a></td>
                                                     </tr>
                                                 <?php }else{ ?>
                                                 <tr class="project-overview">
                                                    <td class="bold"  width="30%"><?php echo _l('vehicle'); ?></td>
                                                    <td><a href="<?php echo admin_url('fleet/vehicle/'.$driver_document->vehicle_id) ?>"><?php echo new_html_entity_decode($driver_document->vehicle->name); ?></a></td>
                                                 </tr>
                                                 <?php }
                                                 ?>
                                                 <tr class="project-overview">
                                                    <td class="bold"  width="30%"><?php echo _l('addedfrom'); ?></td>
                                                    <td><?php echo get_staff_full_name($driver_document->addedfrom) ; ?></td>
                                                 </tr>
                                                 <tr class="project-overview">
                                                    <td class="bold"  width="30%"><?php echo _l('datecreated'); ?></td>
                                                    <td><?php echo _d($driver_document->datecreated) ; ?></td>
                                                 </tr>
                                                 <tr class="project-overview">
                                                    <td class="bold"><?php echo _l('description'); ?></td>
                                                    <td><?php echo new_html_entity_decode($driver_document->description) ; ?></td>
                                                 </tr>
                                                </tbody>
                                          </table>
                                    </div>
                                    <div class="col-md-6">
                                        <?php 
                                              if(isset($driver_document) && $driver_document->files){ 
                                                foreach($driver_document->files as $attachment){

                                                ?>

                                              <div class="row mtop10">
                                                 <div class="col-md-10">
                                                    <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <a href="<?php echo admin_url('fleet/download_file/fle_driver_document/'.$attachment['id']); ?>"><?php echo new_html_entity_decode($attachment['file_name']); ?></a>
                                                 </div>
                                                 <?php if($attachment['staffid'] == get_staff_user_id() || is_admin()){ ?>
                                                 <div class="col-md-2 text-right">
                                                    <a href="<?php echo admin_url('fleet/delete_driver_document_attachment/'.$attachment['id'].'/'.$driver_document->id.'/1'); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                                                 </div>
                                                <?php } ?>
                                              </div>
                                              
                                              <?php }
                                              } 
                                              ?>
                                              
                                    </div>

                                </div>

                          </div>
                          
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
</body>
</html>

