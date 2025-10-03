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
                                        <h4 class="bold"><?php echo _l('inspection_details'); ?></h4>

                                      <table class="table border table-striped table-margintop">
                                          <tbody>
                                             <tr class="project-overview">
                                                <td class="bold"  width="30%"><?php echo _l('vehicle'); ?></td>
                                                <td><?php echo fleet_get_vehicle_name_by_id($inspection->vehicle_id) ; ?></td>
                                             </tr>
                                            <tr class="project-overview">
                                                <td class="bold"><?php echo _l('inspection_form'); ?></td>
                                                <td><?php echo new_html_entity_decode($inspection->inspection_form->name) ; ?></td>
                                             </tr>
                                             <tr class="project-overview">
                                                <td class="bold"><?php echo _l('datecreated'); ?></td>
                                                <td><?php echo new_html_entity_decode($inspection->datecreated) ; ?></td>
                                             </tr>
                                             <tr class="project-overview">
                                                <td class="bold"><?php echo _l('submitted_by'); ?></td>
                                                <td><?php echo get_staff_full_name($inspection->addedfrom) ; ?></td>
                                             </tr>
                                            </tbody>
                                      </table>
                                  </div>
                                    <div class="col-md-6">
                                        <h4 class="bold"><?php echo _l('item_checklist'); ?></h4>
                                             
                                             <?php if(count($inspection->inspection_form->questions) > 0){ ?>
                                            <table class="table border table-striped table-margintop">
                                              <tbody>
                                                <?php $question_area = '<ul class="list-unstyled mtop25">';
                                                  $value_result = [];
                                                  if(isset($inspection->inspection_results)){
                                                    foreach($inspection->inspection_results as $result){
                                                      if($result['boxdescriptionid'] != null){
                                                          $value_result[$result['boxid']][] = $result['boxdescriptionid'];
                                                       }else{
                                                          $value_result[$result['boxid']] = $result['answer'];
                                                       }
                                                    }
                                                  }

                                                foreach($inspection->inspection_form->questions as $question){
                                                    $value = '';
                                                    $description = '';
                                                  if(isset($value_result[$question['questionid']])){
                                                    $value = $value_result[$question['questionid']];
                                                  }

                                                  ?>
                                                  <tr class="project-overview">
                                                    <td class="bold" width="30%"><?php echo new_html_entity_decode($question['question']); ?></td>
                                                 
                                                <?php if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
                                                  $question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';
                                                  foreach($question['box_descriptions'] as $box_description){
                                                    if(is_array($value) && in_array($box_description['questionboxdescriptionid'], $value)){
                                                        if($description == ''){
                                                            $description = $box_description['description'];
                                                        }else{
                                                            $description .= ', '.$box_description['description'];
                                                        }
                                                    }
                                                 }
                                                  // end box row
                                                 $question_area .= '</div>';
                                                } else if($question['boxtype'] == 'pass_fail'){ 
                                                  foreach($question['box_descriptions'] as $box_description){
                                                    if(is_array($value) && in_array($box_description['questionboxdescriptionid'], $value)){
                                                        if($box_description['is_fail'] == 1){
                                                            $description = '<span class="text-danger">'.$box_description['description'].'</span>';
                                                        }else{
                                                            $description = '<span class="text-success">'.$box_description['description'].'</span>';
                                                        }
                                                    }
                                                 } ?>
                                                <?php }else {
                                                    $description = $value;
                                                } 
                                                ?>
                                                        <td><?php echo new_html_entity_decode($description); ?></td>
                                                     </tr>
                                                <?php } ?>
                                                 </tbody>
                                              </table>
                                             <?php } else { ?>
                                             <p class="no-margin text-center bold mtop20"><?php echo _l('survey_no_questions'); ?></p>
                                             <?php } ?>
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

