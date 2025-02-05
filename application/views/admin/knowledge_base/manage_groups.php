<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12 tw-mb-3">
                <h4 class="tw-my-0 tw-font-bold tw-text-xl">
                    <?= _l('als_kb_groups'); ?>
                </h4>
                <a href="<?= admin_url('knowledge_base'); ?>"
                    class="tw-mr-4">
                    <?= _l('als_all_articles'); ?>
                    &rarr;
                </a>
            </div>
            <div class="col-md-12">
                <div class="_buttons tw-mb-2">
                    <?php if (staff_can('create', 'knowledge_base')) { ?>
                    <a href="#" onclick="new_kb_group(); return false;" class="btn btn-primary pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?= _l('new_group'); ?>
                    </a>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>

                <div class="panel_s">
                    <div class="panel-body ">
                        <?php if (count($groups) > 0) { ?>
                        <div class="panel-table-full">
                            <table class="table dt-table">
                                <thead>
                                    <th><?= _l('group_table_name_heading'); ?>
                                    </th>
                                    <th><?= _l('group_table_isactive_heading'); ?>
                                    </th>
                                    <th><?= _l('options'); ?>
                                    </th>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $group) { ?>
                                    <tr>
                                        <td><?= e($group['name']); ?>
                                            <span
                                                class="badge mleft5"><?= total_rows(db_prefix() . 'knowledge_base', 'articlegroup=' . $group['groupid']); ?></span>
                                        </td>
                                        <td>
                                            <div class="onoffswitch">
                                                <input type="checkbox"
                                                    id="<?= e($group['groupid']); ?>"
                                                    data-id="<?= e($group['groupid']); ?>"
                                                    class="onoffswitch-checkbox" <?php if (staff_cant('edit', 'knowledge_base')) {
                                                        echo 'disabled';
                                                    } ?>
                                                data-switch-url="<?= admin_url(); ?>knowledge_base/change_group_status"
                                                <?php if ($group['active'] == 1) {
                                                    echo 'checked';
                                                } ?>>
                                                <label class="onoffswitch-label"
                                                    for="<?= e($group['groupid']); ?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-2">
                                                <?php if (staff_can('edit', 'knowledge_base')) { ?>
                                                <a href="#"
                                                    onclick="edit_kb_group(this,<?= e($group['groupid']); ?>); return false"
                                                    data-name="<?= e($group['name']); ?>"
                                                    data-color="<?= e($group['color']); ?>"
                                                    data-description="<?= clear_textarea_breaks($group['description']); ?>"
                                                    data-order="<?= e($group['group_order']); ?>"
                                                    data-active="<?= e($group['active']); ?>"
                                                    data-slug="<?= e($group['group_slug']); ?>"
                                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>
                                                <?php } ?>
                                                <?php if (staff_can('delete', 'knowledge_base')) { ?>
                                                <a href="<?= admin_url('knowledge_base/delete_group/' . $group['groupid']); ?>"
                                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                        <p class="no-margin">
                            <?= _l('kb_no_groups_found'); ?>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php $this->load->view('admin/knowledge_base/group'); ?>
<?php init_tail(); ?>
</body>

</html>