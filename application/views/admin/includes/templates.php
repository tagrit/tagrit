<?php defined('BASEPATH') or exit('No direct script access allowed');
$len = count($templates);
$i   = 0;
?>
<div id="templates-wrapper" class="ptop15"
    data-total="<?= e($len); ?>">

    <?php foreach ($templates as $template) { ?>
    <div
        class="media templates-wrapper<?= $i == 0 ? ' mtop15' : ''; ?>">
        <div class="media-body">
            <div class="tw-flex tw-items-center pull-right tw-space-x-2">
                <a class="text-muted tw-mr-1 tw-font-semibold" href="#"
                    onclick="insert_template(this,'<?= e($rel_type) ?>',<?= e($template['id']); ?>);return false;">
                    <?= _l('insert_template') ?>
                </a>
                <?php if ($template['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                <a class="text-muted tw-mr-1" href="#"
                    onclick="edit_template('<?= e($rel_type) ?>',<?= e($template['id']); ?>, <?= e($rel_id) ?>);return false;">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>
                <a class="text-muted" href="#"
                    onclick="delete_template(this,'<?= e($rel_type) ?>',<?= e($template['id']); ?>, <?= e($rel_id) ?>);return false;">
                    <i class="fa-regular fa-trash-can"></i>
                </a>
                <?php } ?>
            </div>
            <div data-template-content="<?= e($template['id']); ?>"
                class="bold">
                <?= check_for_links($template['name']); ?>
            </div>
        </div>
        <?php if ($i >= 0 && $i != $len - 1) {
            echo '<hr />';
        }
        ?>
    </div>
    <?php
$i++;
    } ?>
</div>