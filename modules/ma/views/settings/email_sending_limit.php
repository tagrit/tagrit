<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
<?php echo form_open(admin_url('ma/save_email_limit_setting')); ?>
<div class="form-group">
	<label for="mail_engine"><?php echo _l('ma_email_sending_limit'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_sending_limit]" id="email_sending_limit_yes" value="1" <?php if(get_option('ma_email_sending_limit') == '1'){echo 'checked';} ?>>
		<label for="email_sending_limit_yes"><?php echo _l('settings_yes'); ?></label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_sending_limit]" id="email_sending_limit_no" value="0" <?php if(get_option('ma_email_sending_limit') != '1'){echo 'checked';} ?>>
		<label for="email_sending_limit_no"><?php echo _l('settings_no'); ?></label>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>

<div class="div_email_sending_limit <?php if(get_option('ma_email_sending_limit') != '1'){echo 'hide';} ?>">
	<a class="btn btn-primary add_email_limit_config" href="javascript:void(0);"><?php echo _l('add_email_limit_config'); ?></a>
    <div class="horizontal-scrollable-tabs preview-tabs-top mtop25">
      <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
          <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
            <?php foreach($email_limit_configs as $key => $config){ ?>
              <li role="presentation" class="<?php echo ($key == 0 ? 'active' : '') ?>">
                 <a href="#tab_<?php echo html_entity_decode($config['id']) ?>" aria-controls="tab_<?php echo html_entity_decode($config['id']) ?>" role="tab" id="tab_out_of_stock" data-toggle="tab">
                    <?php if($config['is_default'] == 1){ ?>
                 			<span class="req text-danger">* </span>
                    <?php } ?>
                    <?php echo ucfirst($config['name']) ?>
                 </a>
              </li>
            <?php } ?>
          </ul>
          </div>
      </div>
      <div class="tab-content mtop15">
          <?php foreach($email_limit_configs as $key => $config){ ?>
            <div role="tabpanel" class="tab-pane <?php echo ($key == 0 ? 'active' : '') ?>" id="tab_<?php echo html_entity_decode($config['id']) ?>">

		 <?php echo form_open(admin_url('ma/save_email_limit_config/'.$config['id']));

          	$config_option = json_decode($config['configs'] ?? '', true);
            $ma_email_limit = isset($config_option['ma_email_limit']) ? $config_option['ma_email_limit'] : '';
            $ma_email_interval = isset($config_option['ma_email_interval']) ? $config_option['ma_email_interval'] : '';
            $ma_email_repeat_every = isset($config_option['ma_email_repeat_every']) ? $config_option['ma_email_repeat_every'] : '';
            $ma_second_sending_limit_choice = isset($config_option['ma_second_sending_limit_choice']) ? $config_option['ma_second_sending_limit_choice'] : '';

            $ma_email_limit_2 = isset($config_option['ma_email_limit_2']) ? $config_option['ma_email_limit_2'] : '';
            $ma_email_interval_2 = isset($config_option['ma_email_interval_2']) ? $config_option['ma_email_interval_2'] : '';
            $ma_email_repeat_every_2 = isset($config_option['ma_email_repeat_every_2']) ? $config_option['ma_email_repeat_every_2'] : '';
            echo form_hidden('id', $config['id']);
            echo render_input('name', 'name', $config['name']);
          	?>
	<div class="row">
		<div class="col-md-4">
			<?php echo render_input('ma_email_limit','ma_email_limit',$ma_email_limit, 'number'); ?>
	  	</div>
		<div class="col-md-4">
			<?php echo render_input('ma_email_interval','ma_email_interval',$ma_email_interval, 'number'); ?>
	  	</div>
	  	<div class="col-md-4">
	      <?php 
		      $units = [
		         ['id' => 'minutes', 'name' => _l('minutes')],
		         ['id' => 'hours', 'name' => _l('hours')],
		         ['id' => 'day', 'name' => _l('day')],
		         ['id' => 'week', 'name' => _l('week')],
		         ['id' => 'month', 'name' => _l('month')],
		      ];
		   ?>
	   	<?php echo render_select('ma_email_repeat_every',$units, array('id', 'name'),'ma_repeat_every',$ma_email_repeat_every, [], [], '', '', false); ?>
	  	</div>
	</div>
	<?php if(isset($email_sending_limit_stats[$config['id']][0])){ ?>
		<h4 class="mtop5"><?php echo _l('email_limit_statistics'); ?>: <span class="<?php echo ($email_sending_limit_stats[$config['id']][0]['total'] >= $email_sending_limit_stats[$config['id']][0]['limit']) ? 'text-danger' : 'text-success'; ?>"><?php echo html_entity_decode($email_sending_limit_stats[$config['id']][0]['total']); ?></span>/<span class="text-danger"><?php echo html_entity_decode($email_sending_limit_stats[$config['id']][0]['limit']); ?></span></h4>
	<?php } ?>
	<div class="form-group">
	    <div class="checkbox checkbox-primary">
	      <input type="checkbox" name="ma_second_sending_limit_choice" id="ma_second_sending_limit_choice_<?php echo html_entity_decode($config['id']); ?>" value="1" <?php if($ma_second_sending_limit_choice== '1'){ echo 'checked';}?>>
	      <label for="ma_second_sending_limit_choice_<?php echo html_entity_decode($config['id']); ?>"><?php echo _l('second_sending_limit_choice'); ?></label>
	    </div>
	</div>
	<div id="div_second_sending_limit_choice" class="<?php if($ma_second_sending_limit_choice != '1'){ echo 'hide';}?>">
		<div class="row">
			<div class="col-md-4">
				<?php echo render_input('ma_email_limit_2','ma_email_limit',$ma_email_limit_2, 'number'); ?>
		  	</div>
			<div class="col-md-4">
				<?php echo render_input('ma_email_interval_2','ma_email_interval',$ma_email_interval_2, 'number'); ?>
		  	</div>
		  	<div class="col-md-4">
		      <?php 
			      $units = [
			         ['id' => 'minutes', 'name' => _l('minutes')],
			         ['id' => 'hours', 'name' => _l('hours')],
			         ['id' => 'day', 'name' => _l('day')],
			         ['id' => 'week', 'name' => _l('week')],
			         ['id' => 'month', 'name' => _l('month')],
			      ];
			   ?>
		   	<?php echo render_select('ma_email_repeat_every_2',$units, array('id', 'name'),'ma_repeat_every',$ma_email_repeat_every_2, [], [], '', '', false); ?>
		  	</div>
		</div>
		<?php if(isset($email_sending_limit_stats[$config['id']][1])){ ?>
			<h4 class="mtop5"><?php echo _l('email_limit_statistics'); ?>: <span class="<?php echo ($email_sending_limit_stats[$config['id']][1]['total'] >= $email_sending_limit_stats[$config['id']][1]['limit']) ? 'text-danger' : 'text-success'; ?>"><?php echo html_entity_decode($email_sending_limit_stats[$config['id']][1]['total']); ?></span>/<span class="text-danger"><?php echo html_entity_decode($email_sending_limit_stats[$config['id']][1]['limit']); ?></span></h4>
		<?php } ?>
	</div>
	<div class="modal-footer">
	    <a href="<?php echo admin_url('ma/delete_email_limit_config/'.$config['id']) ?>" class="btn btn-danger _delete" ><?php echo _l('delete'); ?></a>
	    <a href="<?php echo admin_url('ma/set_email_limit_config_default/'.$config['id']) ?>" class="btn btn-success" ><?php echo _l('set_default'); ?></a>
	    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
	</div>
	<?php echo form_close(); ?>

	<table class="table table-email-sending-limit mtop25">
		<thead>
		  <th width="30%"><?php echo _l('campaign'); ?></th>
		  <th width="30%"><?php echo _l('ma_progress'); ?></th>
		  <th width="40%"><?php echo _l('ma_email_stats'); ?></th>
		</thead>
		<tbody>
			<?php 
			$this->load->model('ma_model');
			foreach ($campaign_list[$config['id']] as $key => $value) { 
				$stats = $this->ma_model->get_email_progress_by_campaign($value['id']);

                $rattings = '
                <div class="progress no-m
                argin progress-bar-mini cus_tran">
                <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="' . $stats['percent_sent'] . '" aria-valuemin="0" aria-valuemax="100" style="' . $stats['percent_sent'] . '%;" data-percent="' . $stats['percent_sent'] . '">
                </div>
                </div>
                ' . $stats['percent_sent'] . '%
                </div>
                ';


                $stats_html = '<span class="">'._l('total_emails').': <span class="text-dark bold">'.$stats['total'] . '</span></span><br>
                <span class="">'._l('email_was_sent').': <span class="text-success">'.$stats['email_was_sent'] . '</span></span><br>
                <span class="">'._l('email_waiting_sent').': <span class="text-wanning">'.$stats['email_waiting_sent'] . '</span></span><br>';

                if($stats['planned_time'] != ''){
                    $stats_html .= '<span class="">'._l('planned_time').': <span class="text-danger">'.$stats['planned_time'] . '</span></span>';
                }

				?>
				<tr>
		  		<td><?php echo html_entity_decode($value['name']); ?></td>
		  		<td><?php echo html_entity_decode($rattings); ?></td>
		  		<td><?php echo html_entity_decode($stats_html); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
		<?php } ?>
</div>
</div>
<div class="modal fade" id="email-limit-config-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('email_limit_config')?></h4>
         </div>

         <?php echo form_open_multipart(admin_url('ma/add_email_limit_config'),array('id'=>'email-limit-config-form'));?>
         <div class="modal-body">
            <?php echo render_input('name', 'name'); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>