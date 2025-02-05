<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-pipeline-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                    onclick="close_modal_manually('.proposal-pipeline-modal'); return false;" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= e($proposal_data->subject); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $proposal; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    onclick="close_modal_manually('.proposal-pipeline-modal'); return false;"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>