<div role="tabpanel" class="tab-pane" id="inventory">
    <?php render_yes_no_option('inventorys_cronjob_active','inventorys_cronjob_active'); ?>

 <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('settings_inventory_auto_operations_hour_per_cron_run_tooltip'); ?>"></i>
  <?php echo render_input('settings[inventory_auto_operations_hour]','settings_inventory_auto_operations_hour',get_option('inventory_auto_operations_hour'),'number',['min'=>0]); ?>

  	<?php echo render_input('settings[automatically_send_items_expired_before]','automatically_send_items_expired_before',get_option('automatically_send_items_expired_before'),'number', ['min'=>0]); ?>

  	<div class="row">
  	<div class=" col-md-12">
      <?php 
      $inventory_cronjob_notification_recipients = get_option('inventory_cronjob_notification_recipients');
      if(!is_null($inventory_cronjob_notification_recipients) && $inventory_cronjob_notification_recipients != ''){
          $inventory_cronjob_notification_recipients = new_explode(',', $inventory_cronjob_notification_recipients);
      }else{
        $inventory_cronjob_notification_recipients = [];
      }
       ?>
        <label for="inventory_cronjob_notification_recipients[]" class="control-label"><?php echo _l('wh_notification_recipients'); ?></label>
          <select name="inventory_cronjob_notification_recipients[]" class="selectpicker" id="inventory_cronjob_notification_recipients[]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-live-search="true" data-actions-box="true"> 
            <?php foreach(wh_get_staff() as $s){ 
              $selected = '';
              if(in_array($s['staffid'], $inventory_cronjob_notification_recipients)){
                $selected = ' selected';
              }
              ?>
          <option value="<?php echo new_html_entity_decode($s['staffid']); ?>" <?php echo new_html_entity_decode($selected); ?>> <?php echo new_html_entity_decode($s['firstname'].''.$s['lastname']); ?></option>                  
          <?php }?>
          </select>
      </div>
    </div>

    <hr />

</div>
