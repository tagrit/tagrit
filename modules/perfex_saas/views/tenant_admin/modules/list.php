<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-type="asc" data-order-col="0">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= _l('module'); ?>
                                        </th>
                                        <th>
                                            <?= _l('module_description'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($modules as $module) {
                                        $system_name                  = $module['system_name'];
                                        $description = $module['description'];
                                    ?>
                                    <tr>
                                        <td data-order="<?= e($system_name); ?>">
                                            <p>
                                                <b>
                                                    <?= $module['custom_name']; ?>
                                                </b>
                                            </p>
                                            <?php
                                                $action_links = [];
                                                $versionRequirementMet                            = $this->app_modules->is_minimum_version_requirement_met($system_name);
                                                $action_links                                     = hooks()->apply_filters("module_{$system_name}_action_links", $action_links);

                                                if ($module['activated'] === 0 && $versionRequirementMet) {
                                                    array_unshift($action_links, '<a href="' . admin_url('apps/modules/update/' . $system_name) . '/enable">' . _l('enable') . '</a>');
                                                }

                                                if ($module['activated'] === 1) {
                                                    array_unshift($action_links, '<a href="' . admin_url('apps/modules/update/' . $system_name) . '/disable" class="_delete text-danger">' . _l('disable') . '</a>');
                                                }

                                                echo implode('&nbsp;|&nbsp;', $action_links);

                                                ?>
                                        </td>
                                        <td>
                                            <p data-version="<?= $module['headers']['version']; ?>">
                                                <?= empty($description) ? $module['headers']['description'] : $description; ?>
                                            </p>
                                            <?php

                                                $module_description_info = [];
                                                hooks()->apply_filters("module_{$system_name}_description_info", $module_description_info);
                                                echo implode('&nbsp;|&nbsp;', $module_description_info); ?>
                                        </td>
                                    </tr>
                                    <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    appValidateForm($('#module_install_form'), {
        module: {
            required: true,
            extension: "zip"
        }
    });
});
</script>
</body>

</html>