<div class="panel-group no-margin">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title" data-toggle="collapse" data-target="#collapse_node_<?php echo html_entity_decode($nodeId); ?>" aria-expanded="true">
        <span class="text-danger glyphicon glyphicon-fullscreen"> </span><span class="text-danger"> <?php echo _l('condition'); ?></span>
      </h4>
    </div>
    <div id="collapse_node_<?php echo html_entity_decode($nodeId); ?>" class="panel-collapse collapse in" aria-expanded="true">
      <div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
        <?php $tracks = [
            ['id' => 'delivery','name' => _l('delivery')],
            ['id' => 'opens','name' => _l('opens')],
            ['id' => 'clicks','name' => _l('clicks')],
            ['id' => 'confirm','name' => _l('confirm')],
          ]; ?>
          <?php echo render_select('track['.$nodeId.']',$tracks, array('id', 'name'),'track', '', ['df-track' => ''], [], '', '', false); ?>
          <?php $units = [
            ['id' => 'min','name' => _l('min')],
            ['id' => 'hours','name' => _l('hours')],
            ['id' => 'days','name' => _l('days')],
          ]; ?>
          
          <label>Wait for trigger:</label>
          <div class="row">
            <div class="col-md-6 no-padding-right">
              <?php echo render_input('waiting_number['.$nodeId.']', '', '1', 'number', ['df-waiting_number' => '']); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_select('waiting_type['.$nodeId.']',$units, array('id', 'name'),'', '', ['df-waiting_type' => ''], [], '', '', false); ?>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>