   <?php
   $pinned_projects = get_user_pinned_projects();
if (count($pinned_projects) > 0) { ?>
   <li class="pinned-separator tw-border-b tw-border-solid tw-border-neutral-900/10 tw-mt-1"></li>
   <?php foreach ($pinned_projects as $project_pin) { ?>
   <li class="pinned_project last:tw-border-b last:tw-border-solid last:tw-border-neutral-900/10">
      <a href="<?= admin_url('projects/view/' . $project_pin['id']); ?>"
         data-toggle="tooltip"
         data-title="<?= _l('pinned_project'); ?>">
         <?= e($project_pin['name']); ?><br>
         <small>
            <?= e($project_pin['company']); ?>
         </small>
      </a>
      <div class="col-md-12">
         <div class="progress progress-bar-mini">
            <div class="progress-bar no-percent-text not-dynamic" role="progressbar"
               data-percent="<?= e($project_pin['progress']); ?>"
               style="width: <?= e($project_pin['progress']); ?>%;">
            </div>
         </div>
      </div>
   </li>
   <?php } ?>
   <?php } ?>