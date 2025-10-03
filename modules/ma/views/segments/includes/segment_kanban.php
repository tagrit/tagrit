<?php defined('BASEPATH') or exit('No direct script access allowed');

foreach ($data as $category) {
$cpicker = '';

$category_color = ($category['color'] != '' ? $category['color'] : '#323a45');


$total_pages = $category['total_pages'];
$segments = $category['segments'];
$total_segments = count($segments);

if($category['id'] == 0 && count($segments) == 0){
  continue;
}
?>
<ul class="kan-ban-col segment-column" data-col-category-id="<?php echo html_entity_decode($category['id']); ?>" data-total-pages="<?php echo html_entity_decode($total_pages); ?>">
 <li class="kan-ban-col-wrapper">
  <div class="border-right panel_s">
   <div class="panel-heading panel-heading-bg color-not-auto-adjusted color-white" style="background-color: <?php echo html_entity_decode($category_color); ?>;">
        <i class="fa fa-reorder pointer"></i>&nbsp;
        <span class="bold heading"><?php echo html_entity_decode($category['name']); ?></span>
    </span>
</div>
<div class="kan-ban-content-wrapper">
  <div class="kan-ban-content">
   <ul class="status segment-kanban milestone-tasks-wrapper sortable relative" data-task-status-id="<?php echo html_entity_decode($category['id']); ?>">
    <?php
    foreach ($segments as $segment) {
     $this->load->view('ma/segments/includes/_segment_kanban_card', array('segment'=>$segment, 'category'=>$category['id']));
   } ?>
   <?php if ($total_segments > 0) { ?>
     <li class="text-center not-sortable kanban-load-more" data-load-category="<?php echo html_entity_decode($category['id']); ?>">
       <a href="#" class="btn btn-default btn-block<?php if ($total_pages <= 1) { echo ' disabled'; } ?>" data-page="1" onclick="segment_kanban_load_more(<?php echo html_entity_decode($category['id']); ?>,this,'ma/segment_kanban_load_more',320,360); return false;";>
        <?php echo _l('load_more'); ?>
      </a>
    </li>
  <?php } ?>
  <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_segments > 0) { echo ' hide'; } ?>">
   <h4>
    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
    <?php echo _l('no_segments_found'); ?>
  </h4>
</li>
</ul>
</div>
</div>
</li>
</ul>
<?php } ?>
