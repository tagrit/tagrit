<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
  <?php foreach (filter_client_visible_tabs($customer_tabs, $client->userid) as $key => $tab) { ?>
  <li
    class="<?= $key == 'profile' ? 'active' : ''; ?> customer_tab_<?= e($key); ?>">
    <a data-group="<?= e($key); ?>"
      href="<?= admin_url('clients/client/' . $client->userid . '?group=' . $key); ?>">
      <?php if (! empty($tab['icon'])) { ?>
      <i class="<?= e($tab['icon']); ?> menu-icon"
        aria-hidden="true"></i>
      <?php } ?>
      <?= e($tab['name']); ?>
      <?php if (isset($tab['badge'], $tab['badge']['value']) && ! empty($tab['badge'])) {?>
      <span
        class="badge pull-right 
            <?= isset($tab['badge']['type']) && $tab['badge']['type'] != '' ? "bg-{$tab['badge']['type']}" : 'bg-info' ?>"
        <?= (isset($tab['badge']['type']) && $tab['badge']['type'] == '')
                      || isset($tab['badge']['color']) ? "style='background-color: {$tab['badge']['color']}'" : '' ?>>
        <?= e($tab['badge']['value']) ?>
      </span>
      <?php } ?>
    </a>
  </li>
  <?php } ?>
</ul>