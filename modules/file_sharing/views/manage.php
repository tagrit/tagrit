<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <?php if(is_admin()){ ?>
    <div class="row">
      <div class="col-md-12 mtop5 mbot5 mleft5">
        <a href="<?php echo admin_url('file_sharing/setting')?>" class="btn btn-info"><i class="fa fa-cogs"></i>&nbsp;<?php echo _l('setting_file_sharing') ?></a>
      </div>
    </div>
  <?php } ?>
  <div id="elfinder"></div>
</div>
</div>
</div>
<?php $this->load->view('modal/modal_share') ?>
<?php hooks()->do_action('before_file_sharing_init_media'); ?>
<?php init_tail(); ?>
<?php require 'modules/file_sharing/assets/js/main_js.php';?>
</body>
</html>
