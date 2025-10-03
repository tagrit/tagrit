<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade modal-reminder reminder-modal-<?= $name . '-' . $id; ?>"
    tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= form_open('admin/misc/add_reminder/' . $id . '/' . $name, ['id' => 'form-reminder-' . $name]); ?>
            <div class="modal-header">
                <button type="button" class="close close-reminder-modal"
                    data-rel-id="<?= e($id); ?>"
                    data-rel-type="<?= e($name); ?>"
                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-regular fa-circle-question" data-toggle="tooltip"
                        title="<?= _l('set_reminder_tooltip'); ?>"
                        data-placement="bottom"></i>
                    <?= e($reminder_title); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/includes/reminder_fields'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-reminder-modal"
                    data-rel-id="<?= e($id); ?>"
                    data-rel-type="<?= e($name); ?>"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>