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
                              <table class="table border table-striped table-margintop">
                                  <tbody>
                                     <tr class="project-overview">
                                        <td class="bold"  width="30%"><?php echo _l('name'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->name) ; ?></td>
                                     </tr>
                                    <tr class="project-overview">
                                        <td class="bold"><?php echo _l('address'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->address) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('city'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->city) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('state'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->state) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('country'); ?></td>
                                        <td><?php echo get_country_name($garage->country) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('zip'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->zip) ; ?></td>
                                     </tr>
                                     <tr class="project-overview">
                                        <td class="bold"><?php echo _l('note'); ?></td>
                                        <td><?php echo new_html_entity_decode($garage->notes) ; ?></td>
                                     </tr>
                                    

                                    </tbody>
                              </table>

                          </div>
                          <div class="horizontal-scrollable-tabs preview-tabs-top mtop25">
                              <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                      <li role="presentation" class="active">
                                         <a href="#maintenance_team" aria-controls="maintenance_team" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                            <?php echo _l('maintenance_team') ?>
                                         </a>
                                      </li>
                                      <li role="presentation">
                                         <a href="#maintenances" aria-controls="maintenances" role="tab" id="tab_out_of_stock" data-toggle="tab">
                                            <?php echo _l('maintenances') ?>
                                         </a>
                                      </li>
                                  </ul>
                                  </div>
                              </div>
                              <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="maintenance_team">
                                    <a href="#" class="btn btn-info add-new-team mbot15"><?php echo _l('add'); ?></a>
                                    
                                    <?php
                                    $table_data = array(
                                      _l('staff_dt_name'),
                                      _l('staff_dt_email'),
                                      _l('role'),
                                      _l('staff_dt_last_Login'),
                                      _l('staff_dt_active'),
                                      );
                                  
                                    render_datatable($table_data,'maintenance-team');
                                    ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="maintenances">
                                    <table class="table table-maintenances scroll-responsive">
                                       <thead>
                                         <tr>
                                          <th>ID</th>
                                          <th><?php echo  _l('vehicle'); ?></th>
                                          <th><?php echo  _l('maintenance_type'); ?></th>
                                          <th><?php echo  _l('title'); ?></th>
                                          <th><?php echo  _l('start_date'); ?></th>
                                          <th><?php echo  _l('completion_date'); ?></th>
                                          <th><?php echo  _l('notes'); ?></th>
                                          <th><?php echo  _l('cost'); ?></th>
                                        </tr>
                                      </thead>
                                      <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="maintenance-team-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('driver')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('fleet/add_maintenance_team'),array('id'=>'driver-form'));?>
      <div class="modal-body">
        <?php echo form_hidden('garage_id', $garage->id); ?>
        <?php echo render_select('staffid',$staffs, array('staffid', array('firstname', 'lastname')),'staff'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>  
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>

