<div class="panel-group no-margin">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_node_<?php echo html_entity_decode($nodeId); ?>" aria-expanded="true">
        <span class="text-success glyphicon glyphicon-log-in"> </span><span class="text-success"> <?php echo _l('flow_start'); ?></span>
      </h4>
    </div>
    <div id="collapse_node_<?php echo html_entity_decode($nodeId); ?>" class="panel-collapse collapse in" aria-expanded="true">
      <div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
          <div class="form-group">
            <label for="data_type"><?php echo _l('data_type'); ?></label><br />
            <div class="radio radio-inline radio-primary">
              <input type="radio" name="data_type[<?php echo html_entity_decode($nodeId); ?>]" id="data_type_lead[<?php echo html_entity_decode($nodeId); ?>]" value="lead" checked df-data_type>
              <label for="data_type_lead[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_lead"); ?></label>
            </div>
            <div class="radio radio-inline radio-primary">
              <input type="radio" name="data_type[<?php echo html_entity_decode($nodeId); ?>]" id="data_type_customer[<?php echo html_entity_decode($nodeId); ?>]" value="customer" df-data_type>
              <label for="data_type_customer[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_customer"); ?></label>
            </div>
          </div>
          <div class="div_data_type_customer hide">
            <div class="form-group">
              <label for="client_data_from"><?php echo _l('client_data_from'); ?></label><br />
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="client_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="client_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]" value="segment" checked df-client_data_from>
                <label for="client_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("segment"); ?></label>
              </div>
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="client_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="client_data_from_group[<?php echo html_entity_decode($nodeId); ?>]" value="group" df-client_data_from>
                <label for="client_data_from_group[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_group"); ?></label>
              </div>
            </div>
            <div class="div_client_data_from_segment">
              <?php echo render_select('customer_segment['. $nodeId.']',$client_segments, array('id', 'name'),'segment', '', ['df-customer_segment' => '']); ?>
            </div>
            <div class="div_client_data_from_group hide">
              <?php echo render_select('customer_group['. $nodeId.']',$customer_groups, array('id', 'name'),'customer_group', '', ['df-customer_group' => '', 'data-none-selected-text' => _l('all')]); ?>
            </div>
            <?php 
            $customer_sendtos = [
                ['id' => '', 'name' => _l('ma_active')],
                ['id' => 'inactive', 'name' => _l('ma_inactive')],
                ['id' => 'all', 'name' => _l('ma_all')],
              ];
            echo render_select('customer_sendto['. $nodeId.']',$customer_sendtos, array('id', 'name'),'ma_customer_sendto', '', ['df-customer_sendto' => ''], [], '', '', false); ?>
          </div>
          <div class="div_data_type_lead">
            <div class="form-group">
              <label for="lead_data_from"><?php echo _l('lead_data_from'); ?></label><br />
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="lead_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="lead_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]" value="segment" checked df-lead_data_from>
                <label for="lead_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("segment"); ?></label>
              </div>
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="lead_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="lead_data_from_form[<?php echo html_entity_decode($nodeId); ?>]" value="form" df-lead_data_from>
                <label for="lead_data_from_form[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("ma_form"); ?></label>
              </div>
            </div>
            <div class="div_lead_data_from_segment">
              <?php echo render_select('segment['. $nodeId.']',$lead_segments, array('id', 'name'),'segment', '', ['df-segment' => '']); ?>
            </div>
            <div class="div_lead_data_from_form hide">
              <?php echo render_select('form['. $nodeId.']',$forms, array('id', 'name'),'ma_form', '', ['df-form' => '']); ?>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
