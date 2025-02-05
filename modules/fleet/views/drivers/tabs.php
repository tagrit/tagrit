<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
   <?php
      $i = 0;
      foreach($tab as $group){
        ?>
        <li<?php if($i == 0){echo " class='active'"; } ?>>
        <a href="<?php echo admin_url('fleet/driver_detail/'.$driver->staffid.'?group='.$group['name']); ?>" data-group="<?php echo new_html_entity_decode($group['name']); ?>">
         <?php echo new_html_entity_decode($group['icon']).' '._l($group['name']); ?></a>
        </li>
        <?php $i++; } ?>
</ul>
