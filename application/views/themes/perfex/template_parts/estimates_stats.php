<?php defined('BASEPATH') or exit('No direct script access allowed');
$where_total = ['clientid' => get_client_user_id()];

if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
    $where_total['status !='] = 1;
}
$total_estimates = total_rows(db_prefix() . 'estimates', $where_total);

$total_sent       = total_rows(db_prefix() . 'estimates', ['status' => 2, 'clientid' => get_client_user_id()]);
$total_declined   = total_rows(db_prefix() . 'estimates', ['status' => 3, 'clientid' => get_client_user_id()]);
$total_accepted   = total_rows(db_prefix() . 'estimates', ['status' => 4, 'clientid' => get_client_user_id()]);
$total_expired    = total_rows(db_prefix() . 'estimates', ['status' => 5, 'clientid' => get_client_user_id()]);
$percent_sent     = ($total_estimates > 0 ? number_format(($total_sent * 100) / $total_estimates, 2) : 0);
$percent_declined = ($total_estimates > 0 ? number_format(($total_declined * 100) / $total_estimates, 2) : 0);
$percent_accepted = ($total_estimates > 0 ? number_format(($total_accepted * 100) / $total_estimates, 2) : 0);
$percent_expired  = ($total_estimates > 0 ? number_format(($total_expired * 100) / $total_estimates, 2) : 0);
if (get_option('exclude_estimate_from_client_area_with_draft_status') == 0) {
    $col_class     = 'col-md-5ths col-xs-12';
    $total_draft   = total_rows(db_prefix() . 'estimates', ['status' => 1, 'clientid' => get_client_user_id()]);
    $percent_draft = ($total_estimates > 0 ? number_format(($total_draft * 100) / $total_estimates, 2) : 0);
} else {
    $col_class = 'col-md-3';
}
?>
<div class="row text-left estimates-stats">
    <?php if (get_option('exclude_estimate_from_client_area_with_draft_status') == 0) { ?>
    <div class="<?= e($col_class); ?> estimates-stats-draft">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/estimates/1'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('estimate_status_draft'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_draft); ?> /
                <?= e($total_estimates); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= estimate_status_color_class(1); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_draft); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="<?= e($col_class); ?> estimates-stats-sent">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/estimates/2'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('estimate_status_sent'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_sent); ?> /
                <?= e($total_estimates); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= estimate_status_color_class(2); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_sent); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> estimates-stats-expired">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/estimates/5'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('estimate_status_expired'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_expired); ?> /
                <?= e($total_estimates); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= estimate_status_color_class(5); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_expired); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> estimates-stats-declined">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/estimates/3'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('estimate_status_declined'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_declined); ?> /
                <?= e($total_estimates); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= estimate_status_color_class(3); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_declined); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> estimates-stats-accepted">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/estimates/4'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('estimate_status_accepted'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_accepted); ?> /
                <?= e($total_estimates); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= estimate_status_color_class(4); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_accepted); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>