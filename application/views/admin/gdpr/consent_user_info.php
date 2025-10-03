<?php defined('BASEPATH') or exit('No direct script access allowed');

foreach ($purposes as $purpose) { ?>
<div class="col-md-12">
    <div class="gdpr-purpose">
        <div class="row">
            <div class="col-md-9">
                <h3 class="gdpr-purpose-heading">
                    <?= e($purpose['name']); ?>
                    <small>
                        <a href="#"
                            onclick="slideToggle('#purposeActionForm-<?= e($purpose['id']); ?>'); return false;">
                            <?php if (! empty($purpose['consent_given'])) {
                                echo _l('gdpr_consent_opt_out');
                            } else {
                                echo _l('gdpr_consent_opt_in');
                            } ?>
                        </a>
                    </small>
                </h3>
            </div>
            <div class="col-md-3 text-right">
                <?php if (! empty($purpose['consent_given'])) { ?>
                <i class="fa fa-check fa-2x text-success" aria-hidden="true"></i>
                <?php } else { ?>
                <i class="fa fa-remove fa-2x text-danger" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="col-md-12">
                <?php
                                        if (! empty($purpose['opt_in_purpose_description']) && ! empty($purpose['consent_given'])) { ?>
                <p class="no-mbot mtop10">
                    <?= e($purpose['opt_in_purpose_description']); ?>
                </p>
                <?php } elseif (! empty($purpose['description']) && empty($purpose['consent_given'])) { ?>
                <p class="no-mbot mtop10">
                    <?= e($purpose['description']); ?>
                </p>
                <?php } ?>
            </div>
            <div class="col-md-12 opt-action hide"
                id="purposeActionForm-<?= e($purpose['id']); ?>">
                <hr />
                <?= form_open(admin_url($form_url), ['class' => 'consent-form']); ?>
                <input type="hidden" name="action"
                    value="<?= ! empty($purpose['consent_given']) ? 'opt-out' : 'opt-in'; ?>">
                <input type="hidden" name="purpose_id"
                    value="<?= e($purpose['id']); ?>">
                <?php if (isset($contact_id)) { ?>
                <input type="hidden" name="contact_id"
                    value="<?= e($contact_id); ?>">
                <?php } elseif (isset($lead_id)) { ?>
                <input type="hidden" name="lead_id"
                    value="<?= e($lead_id); ?>">
                <?php } ?>
                <?= render_textarea('description', 'Additional Description'); ?>
                <?php if ($purpose['consent_given'] != '1') { ?>
                <?= render_textarea('opt_in_purpose_description', 'Purpose Description', $purpose['description']); ?>
                <?php } ?>
                <button type="submit"
                    class="btn btn-<?= ! empty($purpose['consent_given']) ? 'danger' : 'success'; ?>">
                    <?= ! empty($purpose['consent_given']) ? _l('gdpr_consent_opt_out') : _l('gdpr_consent_opt_in'); ?>
                </button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<hr />
<div class="col-md-12">
    <h4>History</h4>
    <table class="table dt-table" data-order-type="asc" data-order-col="2" id="consentHistoryTable">
        <thead>
            <tr>
                <th>Purpose</th>
                <th>Date</th>
                <th>Action</th>
                <th><?= _l('view_ip'); ?>
                </th>
                <th>
                    <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                        title="Only used if consent is updated from staff member."></i>
                    <?= _l('staff_member'); ?>
                </th>
                <th>Additional Description</th>
            </tr>
        </thead>
        <tbody>
            <?php
                                        foreach ($consents as $consent) { ?>
            <tr>
                <td>
                    <b><?= e($consent['purpose_name']); ?></b>
                </td>
                <td><?= e(_dt($consent['date'])); ?>
                </td>
                <td><?= $consent['action'] == 'opt-in' ? _l('gdpr_consent_opt_in') : _l('gdpr_consent_opt_out'); ?>
                </td>
                <td><?= e($consent['ip']); ?>
                </td>
                <td><?= e($consent['staff_name']); ?>
                </td>
                <td><?= e($consent['description']); ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>