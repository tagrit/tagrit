<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="tw-flex tw-justify-between tw-items-end tw-mb-3">
    <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-heading section-heading-contacts">
        <?= _l('clients_my_contacts'); ?>
    </h4>
    <a href="<?= site_url('contacts/contact'); ?>"
        class="btn btn-primary">
        <?= _l('new_contact'); ?>
    </a>
</div>

<div class="panel_s">
    <div class="panel-body">
        <table class="table dt-table table-contacts" data-order-col="1" data-order-type="desc">
            <thead>
                <tr>
                    <th class="th-invoice-number">
                        <?= _l('clients_list_full_name'); ?>
                    </th>
                    <th class="th-invoice-date">
                        <?= _l('client_email'); ?>
                    </th>
                    <th class="th-invoice-duedate">
                        <?= _l('contact_position'); ?>
                    </th>
                    <th class="th-invoice-amount">
                        <?= _l('client_phonenumber'); ?>
                    </th>
                    <!-- <th class="th-invoice-status"><?= _l('contact_active'); ?>
                    </th> -->
                    <th class="th-invoice-status">
                        <?= _l('clients_list_last_login'); ?>
                    </th>
                    <?php
                    $custom_fields = get_custom_fields('contact', ['show_on_client_portal' => 1]);

foreach ($custom_fields as $field) { ?>
                    <th><?= e($field['name']); ?>
                    </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($contacts as $contact) {
                    $rowName = '<img src="' . e(contact_profile_image_url($contact['id'])) . '" class="client-profile-image-small mright5">' . e(get_contact_full_name($contact['id']));
                    $rowName .= '<div class="mleft25 pleft5 row-options">';
                    $rowName .= '<a href="' . site_url('contacts/contact/' . $contact['id']) . '">' . _l('edit') . '</a>';
                    if ($contact['is_primary'] == 0 || ($contact['is_primary'] == 1)) {
                        $rowName .= ' | <a href="' . site_url('contacts/delete/' . $contact['userid'] . '/' . $contact['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                    }
                    $rowName .= '</div> '; ?>
                <tr>
                    <td
                        data-order="<?= e(get_contact_full_name($contact['id'])); ?>">
                        <?= $rowName; ?></td>
                    <td
                        data-order="<?= e($contact['email']); ?>">
                        <?= e($contact['email']); ?>
                    </td>
                    <td
                        data-order="<?= e($contact['title']); ?>">
                        <?= e($contact['title']); ?>
                    </td>
                    <td
                        data-order="<?= e($contact['phonenumber']); ?>">
                        <a
                            href="tel:+<?= e($contact['phonenumber']); ?>"><?= e($contact['phonenumber']); ?></a>
                    </td>
                    <td
                        data-order="<?= $contact['last_login'] ?>">
                        <?= ! empty($aRow['last_login']) ? '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . e(_dt($aRow['last_login'])) . '">' . e(time_ago($aRow['last_login'])) . '</span>' : ''; ?>
                    </td>
                </tr>
                <?php
                } ?>
            </tbody>
        </table>
    </div>
</div>