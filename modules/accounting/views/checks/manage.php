<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="row">
				<?php
				$this->load->view('accounting/checks/list_template');
				?>
			</div>
         </div>
      </div>
   </div>
</div>
<div id="content_print" class="hide"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/checks/manage_js.php';?>

</body>
</html>
