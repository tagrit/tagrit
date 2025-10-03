<div class="panel-group no-margin">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_node_<?php echo html_entity_decode($nodeId); ?>" aria-expanded="true">
        <span class="text-warning glyphicon glyphicon-random"> </span><span class="text-warning"> <?php echo _l('filter'); ?></span>
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
          <div class="div_complete_action_after hide">
            <?php $units = [
              ['id' => 'min','name' => _l('min')],
              ['id' => 'hours','name' => _l('hours')],
              ['id' => 'days','name' => _l('days')],
            ]; ?>
            <div class="row">
              <div class="col-md-6 no-padding-right">
                <?php echo render_input('waiting_number['. $nodeId.']', '', '1', 'number', ['df-waiting_number' => '']); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_select('waiting_type['. $nodeId.']',$units, array('id', 'name'),'', '', ['df-waiting_type' => ''], [], '', '', false); ?>
              </div>
            </div>
          </div>

          
            <div class="form-group">
            <label for="filter_type"><?php echo _l('filter_type'); ?></label><br />
            <div class="radio radio-inline radio-primary">
              <input type="radio" name="filter_type[<?php echo html_entity_decode($nodeId); ?>]" id="filter_type_lead[<?php echo html_entity_decode($nodeId); ?>]" value="lead" checked df-filter_type>
              <label for="filter_type_lead[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_lead"); ?></label>
            </div>
            <div class="radio radio-inline radio-primary">
              <input type="radio" name="filter_type[<?php echo html_entity_decode($nodeId); ?>]" id="filter_type_customer[<?php echo html_entity_decode($nodeId); ?>]" value="customer" df-filter_type>
              <label for="filter_type_customer[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_customer"); ?></label>
            </div>
          </div>
          <label>Filter leads if they match trigger description:</label>
          <?php $customer_tracks = [
            ['id' => 'company', 'name' => _l('lead_company')],
            ['id' => 'phonenumber', 'name' => _l('lead_add_edit_phonenumber')],
            ['id' => 'website', 'name' => _l('lead_website')],
            ['id' => 'address', 'name' => _l('lead_address')],
            ['id' => 'city', 'name' => _l('lead_city')],
            ['id' => 'state', 'name' => _l('lead_state')],
            ['id' => 'country', 'name' => _l('lead_country')],
            ['id' => 'zip', 'name' => _l('lead_zip')],
          ]; ?>

          <?php $tracks = [
            ['id' => 'name', 'name' => _l('name')],
            ['id' => 'title', 'name' => _l('lead_title')],
            ['id' => 'email', 'name' => _l('lead_add_edit_email')],
            ['id' => 'website', 'name' => _l('lead_website')],
            ['id' => 'phonenumber', 'name' => _l('lead_add_edit_phonenumber')],
            ['id' => 'lead_value', 'name' => _l('lead_value')],
            ['id' => 'company', 'name' => _l('lead_company')],
            ['id' => 'address', 'name' => _l('lead_address')],
            ['id' => 'city', 'name' => _l('lead_city')],
            ['id' => 'state', 'name' => _l('lead_state')],
            ['id' => 'country', 'name' => _l('lead_country')],
            ['id' => 'zip', 'name' => _l('lead_zip')],
            ['id' => 'status', 'name' => _l('lead_status')],
            ['id' => 'source', 'name' => _l('lead_add_edit_source')],
          ]; ?>

          <div class="div_filter_type_lead">
            <?php echo render_select('name_of_variable['. $nodeId .']',$tracks, array('id', 'name'),'Name of variable', '', ['df-name_of_variable' => ''], [], '', '', false); ?>
          </div>
          <div class="div_filter_type_customer hide">
            <?php echo render_select('customer_name_of_variable['. $nodeId .']',$customer_tracks, array('id', 'name'),'Name of variable', '', ['df-customer_name_of_variable' => ''], [], '', '', false); ?>
          </div>
          <?php $conditions = [ 
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
          <?php echo render_select('condition['. $nodeId .']',$conditions, array('id', 'name'),'condition', '', ['df-condition' => ''], [], '', '', false); ?>

          <?php echo render_input('value_of_variable['. $nodeId .']','Value of variable', '', 'text', ['df-value_of_variable' => '']); ?>

        </div>
      </div>
    </div>
  </div>
</div>