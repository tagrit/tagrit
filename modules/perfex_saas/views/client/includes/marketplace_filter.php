<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span class="dropdown">
    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        id="convertDropdown">
        <span class="modal-title tw-mr-3 h4"> <?= $title; ?></span> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="convertDropdown">
        <?php if ($modal !== 'module' && ($package->metadata->disable_module_marketplace ?? '') !== 'yes') : ?>
        <li>
            <a href="#" data-toggle="modal" data-target="#moduleModal" class="" data-dismiss="modal">
                <?= _l('perfex_saas_module_marketplace_title'); ?>
            </a>
        </li>
        <?php endif; ?>
        <?php if ($modal !== 'service' && ($package->metadata->disable_service_marketplace ?? '') !== 'yes') : ?>
        <li>
            <a href="#" data-toggle="modal" data-target="#serviceModal" data-dismiss="modal">
                <?= _l('perfex_saas_service_marketplace_title'); ?>
            </a>
        </li>
        <?php endif; ?>

    </ul>
</span>