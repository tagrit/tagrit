<div class="panel-group no-margin">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_node_<?php echo html_entity_decode($nodeId); ?>" aria-expanded="true">
        <span class="text-primary glyphicon glyphicon-envelope"> </span><span class="text-primary"> <?php echo _l('email'); ?></span>
      </h4>
    </div>
    <div id="collapse_node_<?php echo html_entity_decode($nodeId); ?>" class="panel-collapse collapse in" aria-expanded="true">

      <div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
        <div class="form-group">
          <label for="complete_action"><?php echo _l('complete_action'); ?>:</label><br />
          <div class="radio radio-primary">
            <input type="radio" name="complete_action[<?php echo html_entity_decode($nodeId); ?>]" id="complete_action_right_away[<?php echo html_entity_decode($nodeId); ?>]" value="right_away" checked df-complete_action>
            <label for="complete_action_right_away[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("right_away"); ?></label>
          </div>
          <div class="radio radio-primary">
            <input type="radio" name="complete_action[<?php echo html_entity_decode($nodeId); ?>]" id="complete_action_after[<?php echo html_entity_decode($nodeId); ?>]" value="after" df-complete_action>
            <label for="complete_action_after[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("after"); ?></label>
          </div>
          <div class="radio radio-primary">
            <input type="radio" name="complete_action[<?php echo html_entity_decode($nodeId); ?>]" id="complete_action_exact_time[<?php echo html_entity_decode($nodeId); ?>]" value="exact_time" df-complete_action>
            <label for="complete_action_exact_time[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("exact_time"); ?></label>
          </div>
          <div class="radio radio-primary">
            <input type="radio" name="complete_action[<?php echo html_entity_decode($nodeId); ?>]" id="complete_action_exact_time_and_date[<?php echo html_entity_decode($nodeId); ?>]" value="exact_time_and_date" df-complete_action>
            <label for="complete_action_exact_time_and_date[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("exact_time_and_date"); ?></label>
          </div>
        </div>

        <div class="div_complete_action_after hide">
            <?php $units = [
              ['id' => 'minutes','name' => _l('min')],
              ['id' => 'hour','name' => _l('hours')],
              ['id' => 'day','name' => _l('days')],
            ]; ?>
            <div class="row">
              <div class="col-md-6 no-padding-right">
                <?php echo render_input('waiting_number['.$nodeId.']', '', '1', 'number', ['df-waiting_number' => '']); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_select('waiting_type['.$nodeId.']',$units, array('id', 'name'),'', '', ['df-waiting_type' => ''], [], '', '', false); ?>
              </div>
            </div>
        </div>
        <div class="div_complete_action_exact_time hide">
        <?php echo render_input('exact_time['.$nodeId.']', 'exact_time', '', 'time', ['df-exact_time' => '']); ?>
        </div>
        <div class="div_complete_action_exact_time_and_date hide">
        <?php echo render_datetime_input('exact_time_and_date['.$nodeId.']','exact_time_and_date', '', ['df-exact_time_and_date' => '']); ?>
          
        </div>
        <hr>
        <?php echo render_select('email['.$nodeId.']',$emails, array('id', 'name'),'emails', '', ['df-email' => '']); ?>

      </div>
    </div>
  </div>
</div>