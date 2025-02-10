<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="row">
				<?php $this->load->view('accounting/checks/check_preview_template'); ?>
			</div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/checks/check_js.php';?>
</body>
</html>