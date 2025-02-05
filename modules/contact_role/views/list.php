<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4 tw-flex tw-align-center">
                    <a href="<?php echo admin_url(CONTACT_ROLE_MODULE . '/form'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('add_new', strtolower(_l(CONTACT_ROLE_MODULE))); ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-table-full">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">

                                <thead>
                                    <tr>
                                        <td class="hidden">#</td>
                                        <th><?= _l('name'); ?></th>
                                        <th><?= _l('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $key => $role) : ?>
                                    <tr>
                                        <td class="hidden"><?= $role->id; ?></td>
                                        <td>
                                            <?= $role->name; ?>
                                        </td>
                                        <td>
                                            <a
                                                href="<?= admin_url(CONTACT_ROLE_MODULE . '/form/' . $role->id); ?>"><?= _l('edit'); ?></a>
                                            <a class="text-danger tw-ml-4"
                                                href="<?= admin_url(CONTACT_ROLE_MODULE . '/delete/' . $role->id); ?>"><?= _l('delete'); ?></a>
                                        </td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>