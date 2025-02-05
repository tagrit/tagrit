<div class="row">
  <div class="col-md-12">
     <?php if(count($inspection_form->questions) > 0){
        $question_area = '<ul class="list-unstyled mtop25">';
          $value_result = [];
          if(isset($inspection_results)){
            foreach($inspection_results as $result){
              if($result['boxdescriptionid'] != null){
                  $value_result[$result['boxid']][] = $result['boxdescriptionid'];
               }else{
                  $value_result[$result['boxid']] = $result['answer'];
               }
            }
          }

        foreach($inspection_form->questions as $question){
          $value = '';
          if(isset($value_result[$question['questionid']])){
            $value = $value_result[$question['questionid']];
          }

         $question_area .= '<li><hr />';
         $question_area .= '<div class="form-group">';
         $question_area .= '<label class="control-label" for="'.$question['questionid'].'">'.$question['question'].'</label>';
         if($question['boxtype'] == 'textarea'){
          $question_area .= '<textarea class="form-control" rows="6" name="question['.$question['questionid'].'][]" data-for="'.$question['questionid'].'" id="'.$question['questionid'].'" data-required="'.$question['required'].'" value="'.$value.'"></textarea>';
        } else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
          $question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';
          foreach($question['box_descriptions'] as $box_description){
            $checked = '';
            if(is_array($value) && in_array($box_description['questionboxdescriptionid'], $value)){
              $checked = 'checked';
            }
           $question_area .= '<div class="col-md-12">';
           $question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-default">';
           $question_area .=
           '<input type="'.$question['boxtype'].'" data-for="'.$question['questionid'].'"
           name="selectable['.$question['boxid'].']['.$question['questionid'].'][]" value="'.$box_description['questionboxdescriptionid'].'" data-required="'.$question['required'].'" id="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'" '.$checked.'/>';
           $question_area .= '
           <label for="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'">
           '.$box_description['description'].'
           </label>';
           $question_area .= '</div>';
           $question_area .= '</div>';
         }
          // end box row
         $question_area .= '</div>';
        } else if($question['boxtype'] == 'pass_fail'){
          $question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';
          foreach($question['box_descriptions'] as $box_description){
            $checked = '';
            if(is_array($value) && in_array($box_description['questionboxdescriptionid'], $value)){
              $checked = 'checked';
            }
           $question_area .= '<div class="col-md-12">';
           $question_area .= '<div class="radio radio-default">';
           $question_area .=
           '<input type="radio" data-for="'.$question['questionid'].'"
           name="selectable['.$question['boxid'].']['.$question['questionid'].'][]" value="'.$box_description['questionboxdescriptionid'].'" data-required="'.$question['required'].'" id="chk_radio_'.$box_description['questionboxdescriptionid'].'" '.$checked.'/>';
           $question_area .= '
           <label for="chk_radio_'.$box_description['questionboxdescriptionid'].'">
           '.$box_description['description'].'
           </label>';
           $question_area .= '</div>';
           $question_area .= '</div>';
         }
          // end box row
         $question_area .= '</div>';
        }else {
        $question_area .= '<input type="text" data-for="'.$question['questionid'].'" class="form-control" name="question['.$question['questionid'].'][]" id="'.$question['questionid'].'" data-required="'.$question['required'].'" value="'.$value.'">';
        }
        $question_area .= '</div>';
        $question_area .= '</li>';
        }
        $question_area .= '</ul>';
        echo new_html_entity_decode($question_area); ?>
     <?php } else { ?>
     <p class="no-margin text-center bold mtop20"><?php echo _l('survey_no_questions'); ?></p>
     <?php } ?>
  </div>
</div>

