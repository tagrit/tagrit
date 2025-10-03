<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade tw-z-20" id="<?= $modal_id; ?>" tabindex="-1" role="dialog"
    aria-labelledby="<?= $modal_id; ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <div class="">
                        <?php get_instance()->load->view('client/includes/marketplace_filter', ['package' => $package, 'modal' => $modal_type, 'title' => $title]); ?>
                        <p><?= $subtitle; ?></p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="<?= perfex_saas_ecape_js_attr(_l('close')); ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row saas-market-items">
                    <?php foreach ($items as $item_id => $item) : ?>
                    <?php
                        $name =  $item['custom_name'] ?? $item['name'] ?? $item_id;
                        $name = _l($name, '', false);

                        $billing_mode = $item['billing_mode'];
                        $interval = $billing_mode !== 'lifetime' ? '/' . $billing_cycle : ' <span data-toggle="tooltip" data-title="' . _l('perfex_saas_service_one_time_payment_hint') . '" class="badge bg-warning tw-px-1">' . _l('perfex_saas_service_one_time_payment') . '</span>';

                        $price = $package->metadata->limitations_unit_price->{$item_id} ?? '';
                        if ((string)$price === '')
                            $price = $item['price'] ?? '';

                        if ((string)$price === '') continue;

                        $invoice_link = $onetime_purchased_invoices->{$item_id} ?? null;
                        $invoice_link = $invoice_link ? base64_encode('<br/><a href="' . base_url('invoice/' . $invoice_link->id . '/' . $invoice_link->hash) . '">' . format_invoice_number($invoice_link->id) . '</a>' . format_invoice_status($invoice_link->status, 'tw-p-1 tw-ml-1')) : '';
                        $img = $item['image'] ?? '';
                        ?>
                    <div class="col-md-4 tw-mb-3 saas-market-item-card">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <?php if (!empty($img)) : ?>
                                    <div>
                                        <img src="<?= $img; ?>" class="tw-w-full tw-h-full">
                                    </div>
                                    <?php endif; ?>
                                    <h4 class="card-title text-center"><?= $name; ?></h4>
                                    <p class="description truncate-text"><?= $item['description']; ?></p>
                                    <p class="card-text"><?= _l('perfex_saas_price'); ?>:
                                        <strong><?= app_format_money($price, $currency); ?><?= $interval; ?></strong>
                                    </p>
                                </div>
                                <div class="tw-flex tw-justify-end">
                                    <?php if (in_array($item_id, $package->services ?? $package->modules)) : ?>
                                    <small class="btn btn-secondary disabled btn-sm" data-toggle="tooltip"
                                        data-title="<?= perfex_saas_ecape_js_attr(_l('perfex_saas_module_included_hint')); ?>"
                                        disabled><?= _l('perfex_saas_module_included'); ?> <i
                                            class="fa fa-question"></i></small>
                                    <?php else : ?>
                                    <button type="button"
                                        class="btn btn-primary add-item add-<?= $modal_type; ?>-<?= $item_id; ?>"
                                        data-key="<?= $item_id; ?>" data-price="<?= $price; ?>"
                                        data-price-formatted="<?= app_format_money($price, $currency); ?>"
                                        data-name="<?= $name; ?>" data-billing-mode="<?= $item['billing_mode']; ?>"
                                        data-invoice-link="<?= $invoice_link; ?>"
                                        <?php if ($item['billing_mode'] === 'lifetime') { ?>
                                        data-confirm="<?= perfex_saas_ecape_js_attr(_l('perfex_saas_service_one_time_payment_hint')); ?>"
                                        <?php } ?>><?= _l('perfex_saas_add'); ?></button>
                                    <button type="button" class="btn btn-danger remove-item" data-key="<?= $item_id; ?>"
                                        data-price="<?= $price; ?>" data-name="<?= $name; ?>"
                                        data-billing-mode="<?= $item['billing_mode']; ?>"
                                        style="display: none;"><?= _l('perfex_saas_remove'); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($allow_request) : ?>
                    <div class="col-md-12 tw-mb-3 tw-mt-8 saas-market-item-card">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h4 class="card-title text-center">
                                        <?= _l('perfex_saas_request_' . $modal_type); ?></h4>
                                    <p class="text-center"><?= _l('perfex_saas_request_' . $modal_type . '_desc'); ?>
                                    </p>
                                </div>
                                <div class="tw-flex tw-justify-center">
                                    <a href="<?= $request_url; ?>" target="_blank"
                                        class="btn btn-info btn-full"><?= _l('perfex_saas_request_' . $modal_type . '_btn'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>