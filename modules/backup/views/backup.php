<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info mbot25">
                    <?= _l('utility_db_backup_note'); ?>
                </div>
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-self-end">
                        <?= e($title); ?>
                    </h4>
                    <div>
                        <a href="#" data-toggle="modal" data-target="#auto_backup_config"
                            class="btn btn-default mright5">
                            <?= _l('auto_backup'); ?>
                        </a>
                        <a href="<?= admin_url('backup/make_backup_db'); ?>"
                            class="btn btn-primary">
                            <?= _l('utility_create_new_backup_db'); ?>
                        </a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">

                        <table class="table dt-table" data-order-col="2" data-order-type="desc">
                            <thead>
                                <th><?= _l('utility_backup_table_backupname'); ?>
                                </th>
                                <th><?= _l('utility_backup_table_backupsize'); ?>
                                </th>
                                <th><?= _l('utility_backup_table_backupdate'); ?>
                                </th>
                                <th><?= _l('options'); ?>
                                </th>
                            </thead>
                            <tbody>
                                <?php $backups = list_files(BACKUPS_FOLDER); ?>
                                <?php foreach ($backups as $backup) {
                                    $fullPath              = BACKUPS_FOLDER . $backup;
                                    $backupNameNoExtension = preg_replace('/\\.[^.\\s]{3,4}$/', '', $backup); ?>
                                <tr>
                                    <td>
                                        <a
                                            href="<?= site_url('backup/download/' . $backupNameNoExtension); ?>">
                                            <?= e($backup); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= bytesToSize($fullPath); ?>
                                    </td>
                                    <td
                                        data-order="<?= date('Y-m-d H:m:s', filectime($fullPath)); ?>">
                                        <?= date('M dS, Y, g:i a', filectime($fullPath)); ?>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('backup/delete/' . $backupNameNoExtension); ?>"
                                            class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                            <i class="fa-regular fa-trash-can fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="auto_backup_config" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('backup/update_auto_backup_options')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('auto_backup'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= render_yes_no_option('auto_backup_enabled', 'auto_backup_enabled'); ?>
                <div data-toggle="tooltip"
                    title="<?= _l('hour_of_day_perform_auto_operations_format'); ?>">
                    <?= render_input('auto_backup_hour', 'auto_backup_hour', get_option('auto_backup_hour'), 'number'); ?>
                </div>
                <?= render_input('auto_backup_every', 'auto_backup_every', get_option('auto_backup_every'), 'number'); ?>
                <?= render_input('delete_backups_older_then', 'delete_backups_older_then', get_option('delete_backups_older_then'), 'number'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?= form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
</body>

</html>