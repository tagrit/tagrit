<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-text">
    <?= _l('gdpr'); ?>
</h4>
<div class="mbot20">
    <?= get_option('gdpr_page_top_information_block'); ?>
</div>
<div class="row">
    <?php if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions') == '1') { ?>
    <div class="col-md-4">
        <div class="gdpr-right">
            <h3 class="gdpr-right-heading">
                <?= _l('gdpr_right_to_be_informed'); ?>
            </h3>
            <a href="<?= terms_url(); ?>"
                class="btn btn-primary"><?= _l('terms_and_conditions'); ?></a>
        </div>
    </div>
    <?php } ?>
    <div class="col-md-4">
        <div class="gdpr-right">
            <h3 class="gdpr-right-heading">
                <?= _l('gdpr_right_of_access'); ?>
            </h3>
            <a href="<?= site_url('clients/profile'); ?>"
                class="btn btn-primary"><?= _l('edit_my_information'); ?></a>
        </div>
    </div>
    <?php if (is_gdpr() && get_option('gdpr_contact_enable_right_to_be_forgotten') == '1') { ?>
    <div class="col-md-4">
        <div class="gdpr-right">
            <h3 class="gdpr-right-heading">
                <?= _l('gdpr_right_to_erasure'); ?>
            </h3>
            <a href="#" data-toggle="modal" data-target="#dataRemoval"
                class="btn btn-primary"><?= _l('request_data_removal'); ?></a>
        </div>
    </div>
    <?php } ?>
    <?php if (is_gdpr() && get_option('gdpr_data_portability_contacts') == '1') { ?>
    <div class="col-md-4">
        <div class="gdpr-right">
            <h3 class="gdpr-right-heading">
                <?= _l('gdpr_right_to_data_portability'); ?>
            </h3>
            <a href="<?= site_url('clients/export'); ?>"
                class="btn btn-primary"><?= _l('export_my_data'); ?></a>
        </div>
    </div>
    <?php } ?>
    <?php if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') { ?>
    <div class="col-md-4">
        <div class="gdpr-right">
            <h3 class="gdpr-right-heading">
                <?= _l('gdpr_consent'); ?>
            </h3>
            <a href="<?= contact_consent_url(get_contact_user_id()); ?>"
                class="btn btn-primary"><?= _l('gdpr_consent'); ?></a>
        </div>
    </div>
    <?php } ?>
</div>
<?php if (is_gdpr() && get_option('gdpr_contact_enable_right_to_be_forgotten') == '1') { ?>
<div class="modal fade" tabindex="-1" role="dialog" id="dataRemoval">
    <div class="modal-dialog" role="document">
        <?= form_open(); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('request_data_removal'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?= form_hidden('removal_request', true); ?>
                    <label for="removal_description"
                        class="control-label"><?= _l('explanation_for_data_removal'); ?></label>
                    <textarea name="removal_description" id="removal_description" class="form-control" rows="4"
                        placeholder="<?= _l('briefly_describe_why_remove_data'); ?>"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary _delete"><?= _l('confirm'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?= form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>