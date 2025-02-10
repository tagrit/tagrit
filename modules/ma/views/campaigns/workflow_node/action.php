<div class="panel-group no-margin">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_node_<?php echo html_entity_decode($nodeId); ?>" aria-expanded="true">
        <span class="text-info glyphicon glyphicon-retweet"> </span><span class="text-info"> <?php echo _l('action'); ?></span>
      </h4>
    </div>
    <div id="collapse_node_<?php echo html_entity_decode($nodeId); ?>" class="panel-collapse collapse in" aria-expanded="true">
      <div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
        <?php $actions = [
            ['id' => 'change_segments','name' => _l('change_segments')],
            ['id' => 'change_stages','name' => _l('change_stages')],
            ['id' => 'change_points','name' => _l('change_points')],
            ['id' => 'point_action','name' => _l('point_action')],
            ['id' => 'delete_lead','name' => _l('delete_lead')],
            ['id' => 'remove_from_campaign','name' => _l('remove_from_campaign')],
            ['id' => 'convert_to_customer','name' => _l('convert_to_customer')],
            ['id' => 'change_lead_status','name' => _l('change_lead_status')],
            ['id' => 'add_tags','name' => _l('add_tags')],
          ]; ?>
          <?php echo render_select('action['.$nodeId.']',$actions, array('id', 'name'),'action', '', ['df-action' => ''], [], '', '', false); ?>
          <div class="div_action_change_segments">
            <div class="form-group">
              <label for="action_segment_type"><?php echo _l('segment_type'); ?></label><br />
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="action_segment_type[<?php echo html_entity_decode($nodeId); ?>]" id="action_segment_type_lead[<?php echo html_entity_decode($nodeId); ?>]" value="lead" checked df-action_segment_type>
                <label for="action_segment_type_lead[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_lead"); ?></label>
              </div>
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="action_segment_type[<?php echo html_entity_decode($nodeId); ?>]" id="action_segment_type_customer[<?php echo html_entity_decode($nodeId); ?>]" value="customer" df-action_segment_type>
                <label for="action_segment_type_customer[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_customer"); ?></label>
              </div>
            </div>
            <div class="div_action_segment_type_lead">
              <?php echo render_select('segment['.$nodeId.']',$lead_segments, array('id', 'name'),'segment', '', ['df-segment' => '']); ?>
            </div>
            <div class="div_action_segment_type_customer hide">
              <?php echo render_select('customer_segment['.$nodeId.']',$client_segments, array('id', 'name'),'segment', '', ['df-customer_segment' => '']); ?>
            </div>
          </div>
          <div class="div_action_change_stages hide">
            <?php echo render_select('stage['.$nodeId.']',$stages, array('id', 'name'),'stage', '', ['df-stage' => '']); ?>
          </div>
          <div class="div_action_point_action hide">
            <?php echo render_select('point_action['.$nodeId.']',$point_actions, array('id', 'name'),'point_action', '', ['df-point_action' => '']); ?>
          </div>
          <div class="div_action_change_points hide">
            <?php echo render_input('point['.$nodeId.']', 'point', '', 'number', ['df-point' => '']); ?>
          </div>
          <div class="div_action_lead_status hide">
            <?php echo render_select('lead_status['.$nodeId.']',$lead_status, array('id', 'name'),'lead_status', '', ['df-lead_status' => '']); ?>
          </div>
          <div class="div_action_add_tags hide">
            <div class="form-group no-mbot" id="inputTagsWrapper">
                <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                    <?php echo _l('tags'); ?></label>
                <input type="text" class="tagsinput" id="tags" name="tags[<?php echo $nodeId; ?>]"
                    value=""
                    data-role="tagsinput" df-tags>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
