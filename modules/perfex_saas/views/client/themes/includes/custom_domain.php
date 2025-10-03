<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!$can_use_custom_domain) return; ?>

<!-- custom domain form -->
<div class="custom-domain-form tw-w-full text-left <?= $theme_name == 'single' ? 'panel panel-body' : 'tw-mt-4'; ?>" style="display:none">
    <?= form_open(base_url('clients/companies/edit/' . $company->slug), ['id' => 'company_custom_domain']); ?>
    <!-- company name:hidden here as its required when submitting to edit endpoint -->
    <?= render_input('name', '', $company->name, 'hidden', [], [], "hidden", "hidden"); ?>

    <?= render_input('custom_domain', _l('perfex_saas_custom_domain') . perfex_saas_form_label_hint('perfex_saas_custom_domain_hint'), $company->custom_domain, 'text', [], [], "text-left tw-mb-4 $centered_size", $input_class); ?>

    <div class="text-center">
        <button type="button" class="btn btn-default mtop15 mbot15 close-btn"><?= _l('perfex_saas_cancel'); ?></button>
        <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>" data-form="#company_custom_domain" class="btn btn-primary mtop15 mbot15"><?= _l('perfex_saas_submit'); ?></button>
    </div>
    <?= form_close(); ?>

    <?php
    $_key = 'perfex_saas_custom_domain_guide';
    $_alt_key = 'perfex_saas_custom_domain_guide_option';
    $guide = get_option($_key);
    $translated_guide = _l($_alt_key, '', false);
    $guide = !empty($translated_guide) && $translated_guide !== $_alt_key ? $translated_guide : $guide;
    $urls = perfex_saas_tenant_base_url($company, '', 'all');
    $app_host = parse_url($urls['path'], PHP_URL_HOST);
    $subdomain = $urls['subdomain'];
    $subdomain = empty($subdomain) ? $app_host : parse_url($subdomain, PHP_URL_HOST);
    $ip_address = '';
    if (stripos($guide, '{ip_address}') !== false)
        $ip_address = gethostbyname($app_host);
    $guide = str_ireplace(['{ip_address}', '{subdomain}'], [$ip_address, $subdomain], html_entity_decode($guide));

    if (!empty($guide)) {
    ?>

        <?php if ($theme_name == 'single') : ?>
            <div class="panel-group tw-mt-9">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle tw-w-full tw-inline-block collapsed" data-toggle="collapse" data-parent="#accordion" href="#<?= $company->slug; ?>-dns-guide">
                                <?= _l('perfex_saas_custom_domain_guide'); ?>
                            </a>
                        </h4>
                    </div>
                    <div id="<?= $company->slug; ?>-dns-guide" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="tc-content div_content">
                                <?= $guide; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <button type="button" class="tw-w-full tw-inline-block btn btn-info tw-bg-transparent hover:tw-bg-transparent tw-truncate tw-mt-9 tw-py-1 tw-px-1" data-toggle="modal" data-target="#<?= $company->slug; ?>-dns-guide" class="" data-dismiss="modal">
                <span class="text-info"><?= _l('perfex_saas_custom_domain_guide'); ?> <i class="fa fa-question-circle"></i></span>
            </button>
            <div class="modal fade tw-z-20" id="<?= $company->slug; ?>-dns-guide" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <div class="">
                                    <?= _l('perfex_saas_custom_domain_guide'); ?>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="<?= _l('close'); ?>">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="tc-content div_content">
                                <?= $guide; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php } ?>
</div>
<!-- custom domain form -->