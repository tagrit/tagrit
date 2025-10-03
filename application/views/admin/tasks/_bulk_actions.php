<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade bulk_actions" id="tasks_bulk_actions" tabindex="-1" role="dialog"
   data-table="<?= $table ?? '.table-tasks'; ?>">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
               <?= _l('bulk_actions'); ?>
            </h4>
         </div>
         <div class="modal-body">

            <?php if (staff_can('delete', 'tasks')) { ?>
            <div class="checkbox checkbox-danger">
               <input type="checkbox" name="mass_delete" id="mass_delete">
               <label
                  for="mass_delete"><?= _l('mass_delete'); ?></label>
            </div>
            <hr class="mass_delete_separator" />
            <?php } ?>
            <div id="bulk_change">
               <div class="form-group">
                  <label
                     for="move_to_status_tasks_bulk_action"><?= _l('task_status'); ?></label>
                  <select name="move_to_status_tasks_bulk_action" id="move_to_status_tasks_bulk_action"
                     data-width="100%" class="selectpicker"
                     data-none-selected-text="<?= _l('task_status'); ?>">
                     <option value=""></option>
                     <?php foreach ($task_statuses as $status) { ?>
                     <option
                        value="<?= e($status['id']); ?>">
                        <?= e($status['name']); ?>
                     </option>
                     <?php } ?>
                  </select>
               </div>
               <?php if (staff_can('edit', 'tasks')) { ?>

               <div class="form-group">
                  <label for="task_bulk_priority"
                     class="control-label"><?= _l('task_add_edit_priority'); ?></label>
                  <select name="task_bulk_priority" class="selectpicker" id="task_bulk_priority" data-width="100%"
                     data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                     <option value=""></option>
                     <option value="1">
                        <?= _l('task_priority_low'); ?>
                     </option>
                     <option value="2">
                        <?= _l('task_priority_medium'); ?>
                     </option>
                     <option value="3">
                        <?= _l('task_priority_high'); ?>
                     </option>
                     <option value="4">
                        <?= _l('task_priority_urgent'); ?>
                     </option>
                  </select>
               </div>
               <?= '<i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="' . _l('tasks_bull_actions_assign_notice') . '"></i>';
                   $staff_bulk_assigned = $this->staff_model->get('', ['active' => 1]);
                   echo render_select('task_bulk_assignees', $staff_bulk_assigned, ['staffid', ['firstname', 'lastname']], 'task_assigned', '', ['multiple' => true]);
                   if (isset($project)) {
                       echo render_select('task_bulk_milestone', $this->projects_model->get_milestones($project->id), [
                           'id',
                           'name',
                       ], 'task_milestone');
                   } ?>

               <div class="form-group">
                  <label for="task_bulk_billable"
                     class="control-label"><?= _l('task_billable'); ?></label>
                  <select name="task_bulk_billable" class="selectpicker" id="task_bulk_billable" data-width="100%"
                     data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                     <option value=""></option>
                     <option value="billable">
                        <?= _l('settings_yes'); ?>
                     </option>
                     <option value="not_billable">
                        <?= _l('settings_no'); ?>
                     </option>
                  </select>
               </div>

               <div class="form-group">
                  <?= '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                  <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
               </div>

               <?php } ?>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default"
               data-dismiss="modal"><?= _l('close'); ?></button>
            <a href="#" class="btn btn-primary"
               onclick="tasks_bulk_action(this); return false;"><?= _l('confirm'); ?></a>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->