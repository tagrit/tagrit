<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="apitable table dt-table">
    <thead>
        <th><?php echo _l('id'); ?></th>
        <th><?php echo _l('name'); ?></th>
        <th><?php echo _l('perfex_saas_api_key'); ?></th>
        <th><?php echo _l('options'); ?></th>
    </thead>
    <tbody>
        <?php foreach ($api_users as $user) { ?>
        <tr>
            <td><?= e($user->id); ?></td>
            <td><?= e($user->name); ?></td>
            <td><?= e($user->token); ?></td>
            <td>
                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/api/edit_user/' . (int)$user->id) ?>"
                    class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/api/delete_user/' . (int)$user->id); ?>"
                    class="btn btn-danger btn-icon _delete"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>