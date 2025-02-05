<?php defined('BASEPATH') or exit('No direct script access allowed');
if ($estimate['status'] == $status) { ?>
<li data-estimate-id="<?= e($estimate['id']); ?>"
    class="<?= $estimate['invoiceid'] != null ? 'not-sortable' : ''; ?>">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-base pipeline-heading tw-mb-0.5">
                    <a href="<?= admin_url('estimates/list_estimates/' . $estimate['id']); ?>"
                        class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900"
                        onclick="estimate_pipeline_open(<?= e($estimate['id']); ?>); return false;">
                        <?= e(format_estimate_number($estimate['id'])); ?>
                    </a>
                    <?php if (staff_can('edit', 'estimates')) { ?>
                    <a href="<?= admin_url('estimates/estimate/' . $estimate['id']); ?>"
                        target="_blank" class="pull-right tw-font-medium">
                        <small>
                            <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                        </small>
                    </a>
                    <?php } ?>
                </h4>
                <span class="tw-inline-block tw-w-full tw-mb-2">
                    <a href="<?= admin_url('clients/client/' . $estimate['clientid']); ?>"
                        target="_blank">
                        <?= e($estimate['company']); ?>
                    </a>
                </span>
            </div>
            <div class="col-md-12">
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('estimate_total'); ?>:
                            </span>
                            <?= e(app_format_money($estimate['total'], $estimate['currency_name'])); ?>
                        </p>
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('estimate_data_date'); ?>:
                            </span>
                            <?= e(_d($estimate['date'])); ?>
                        </p>
                        <?php if (is_date($estimate['expirydate']) || ! empty($estimate['expirydate'])) { ?>
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('estimate_data_expiry_date'); ?>:
                            </span>
                            <?= e(_d($estimate['expirydate'])); ?>
                        </p>
                        <?php } ?>
                    </div>
                    <div class="tw-shrink-0 text-right">
                        <small>
                            <i class="fa fa-paperclip"></i>
                            <?= _l('estimate_notes'); ?>:
                            <?= total_rows(db_prefix() . 'notes', [
                                'rel_id'   => $estimate['id'],
                                'rel_type' => 'estimate',
                            ]); ?>
                        </small>
                    </div>
                    <?php $tags = get_tags_in($estimate['id'], 'estimate'); ?>
                    <?php if (count($tags) > 0) { ?>
                    <div class="kanban-tags tw-text-sm tw-inline-flex">
                        <?= render_tags($tags); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</li>
<?php } ?>