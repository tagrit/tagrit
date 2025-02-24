<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-6 col-md-offset-3">
            <div class="panel_s">
               <div class="panel-body">
                  <h4>Flutex Admin API Module Activation</h4>
                  <hr class="hr-panel-heading">
                  Please activate your product using your license purchase code (<a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code">where to find purchase code?</a>)
                  <br><br>
                  <?php echo form_open($submit_url, ['autocomplete' => 'off', 'id' => 'verify-form']); ?>
                  <?php echo form_hidden('return_url', $return_url); ?>
                  <?php echo form_hidden('module_name', $module_name); ?>
                  <?php echo render_input('username', 'Envato Username', '', 'text', ['required' => true]); ?>
                  <?php echo render_input('purchase_code', 'Purchase Code', '', 'text', ['required' => true]); ?>
                  <button id="submit" type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  <?php echo form_close(); ?>
               </div>
               <div class="panel-footer"><?php echo 'Version '.$this->app_modules->get($module_name)['headers']['version'] ?? ''; ?></div>
            </div>
         </div>
         <div class="col-md-3">
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
   appValidateForm($('#verify-form'), {username: 'required'}, manage_verify_form);
   appValidateForm($('#verify-form'), {purchase_code: 'required'}, manage_verify_form);
   function manage_verify_form(form) {
      $("#submit").prop('disabled', true).prepend('<i class="fa fa-spinner fa-pulse"></i> ');
      $.post(form.action, $(form).serialize()).done(function(response) {
         var response = $.parseJSON(response);
         if (!response.status) {alert_float("danger", response.message);}
         if (response.status) {alert_float("success", "Activating....");window.location.href = response.return_url;}
         $("#submit").prop('disabled', false).find('i').remove();
      });
   }
</script>