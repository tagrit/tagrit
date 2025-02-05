<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('view', 'customers') || have_assigned_customers()) {
                    $where_summary = '';
                    if (staff_cant('view', 'customers')) {
                        $where_summary = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
                    } ?>
                <div class="tw-mb-6">
                    <div class="tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl">
                            <?= _l('clients'); ?>
                        </h4>
                        <a
                            href="<?= admin_url('clients/all_contacts'); ?>">
                            <?= _l('customer_contacts'); ?>
                            &rarr;
                        </a>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-6 tw-gap-2">
                        <div
                            class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'clients', ($where_summary != '' ? substr($where_summary, 5) : '')); ?>
                            </span>
                            <span
                                class="text-dark tw-truncate sm:tw-text-clip"><?= _l('customers_summary_total'); ?></span>
                        </div>
                        <div
                            class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'clients', 'active=1' . $where_summary); ?></span>
                            <span
                                class="text-success tw-truncate sm:tw-text-clip"><?= _l('active_customers'); ?></span>
                        </div>
                        <div
                            class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'clients', 'active=0' . $where_summary); ?></span>
                            <span
                                class="text-danger tw-truncate sm:tw-text-clip"><?= _l('inactive_active_customers'); ?></span>
                        </div>
                        <div
                            class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'contacts', 'active=1' . $where_summary); ?>
                            </span>
                            <span
                                class="text-info tw-truncate sm:tw-text-clip"><?= _l('customers_summary_active'); ?></span>
                        </div>
                        <div
                            class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'contacts', 'active=0' . $where_summary); ?>
                            </span>
                            <span
                                class="text-danger tw-truncate sm:tw-text-clip"><?= _l('customers_summary_inactive'); ?></span>
                        </div>
                        <div
                            class="tw-flex tw-items-center tw-font-medium tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-bg-white">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= total_rows(db_prefix() . 'contacts', 'last_login LIKE "' . date('Y-m-d') . '%"' . $where_summary); ?>
                            </span>
                            <span class="text-muted tw-truncate" data-toggle="tooltip"
                                data-title="<?= _l('customers_summary_logged_in_today'); ?>">
                                <?php
                                          $contactsTemplate = '';
                    if (count($contacts_logged_in_today) > 0) {
                        foreach ($contacts_logged_in_today as $contact) {
                            $url          = admin_url('clients/client/' . $contact['userid'] . '?contactid=' . $contact['id']);
                            $fullName     = e($contact['firstname'] . ' ' . $contact['lastname']);
                            $dateLoggedIn = e(_dt($contact['last_login']));
                            $html         = "<a href='{$url}' target='_blank'>{$fullName}</a><br /><small>{$dateLoggedIn}</small><br />";
                            $contactsTemplate .= html_escape('<p class="mbot5">' . $html . '</p>');
                        } ?>
                                <?php } ?>
                                <span<?php if ($contactsTemplate != '') { ?>
                                    class="pointer text-has-action"
                                    data-toggle="popover"
                                    data-title="<?= _l('customers_summary_logged_in_today'); ?>"
                                    data-html="true"
                                    data-content="<?= $contactsTemplate; ?>"
                                    data-placement="bottom"
                                    <?php } ?>>
                                    <?= _l('customers_summary_logged_in_today'); ?>
                            </span>
                            </span>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="tw-flex tw-justify-between tw-items-center tw-gap-x-6">
                    <div class="tw-flex tw-justify-between tw-items-center tw-gap-x-6">
                        <?php if (staff_can('create', 'customers')) { ?>
                        <a href="<?= admin_url('clients/client'); ?>"
                            class="btn btn-primary">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?= _l('new_client'); ?>
                        </a>
                        <?php } ?>
                        <?php if (staff_can('create', 'customers')) { ?>
                        <a href="<?= admin_url('clients/import'); ?>"
                            class="hidden-xs tw-text-neutral-600 hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-font-semibold">
                            <i class="fa-solid fa-upload tw-mr-1"></i>
                            <?= _l('import_customers'); ?>
                        </a>
                        <?php } ?>
                    </div>
                    <div id="vueApp" class="tw-inline">
                        <app-filters id="<?= $table->id(); ?>"
                            view="<?= $table->viewName(); ?>"
                            :saved-filters="<?= $table->filtersJs(); ?>"
                            :available-rules="<?= $table->rulesJs(); ?>">
                        </app-filters>
                    </div>
                </div>
                <div class="panel_s tw-mt-2">
                    <div class="panel-body">
                        <a href="#" data-toggle="modal" data-target="#customers_bulk_action"
                            class="bulk-actions-btn table-btn hide"
                            data-table=".table-clients"><?= _l('bulk_actions'); ?></a>
                        <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">
                                            <?= _l('bulk_actions'); ?>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php if (staff_can('delete', 'customers')) { ?>
                                        <div class="checkbox checkbox-danger">
                                            <input type="checkbox" name="mass_delete" id="mass_delete">
                                            <label
                                                for="mass_delete"><?= _l('mass_delete'); ?></label>
                                        </div>
                                        <hr class="mass_delete_separator" />
                                        <?php } ?>
                                        <div id="bulk_change">
                                            <?= render_select('move_to_groups_customers_bulk[]', $groups, ['id', 'name'], 'customer_groups', '', ['multiple' => true], [], '', '', false); ?>
                                            <p class="text-danger">
                                                <?= _l('bulk_action_customers_groups_warning'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                            data-dismiss="modal"><?= _l('close'); ?></button>
                                        <a href="#" class="btn btn-primary"
                                            onclick="customers_bulk_action(this); return false;"><?= _l('confirm'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                         $table_data = [];
$_table_data                         = [
    '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
    [
        'name'     => _l('the_number_sign'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
    ],
    [
        'name'     => _l('clients_list_company'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
    ],
    [
        'name'     => _l('contact_primary'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact'],
    ],
    [
        'name'     => _l('company_primary_email'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact-email'],
    ],
    [
        'name'     => _l('clients_list_phone'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
    ],
    [
        'name'     => _l('customer_active'),
        'th_attrs' => ['class' => 'text-center toggleable', 'id' => 'th-active'],
    ],
    [
        'name'     => _l('customer_groups'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-groups'],
    ],
    [
        'name'     => _l('date_created'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-date-created'],
    ],
];

foreach ($_table_data as $_t) {
    array_push($table_data, $_t);
}

$custom_fields = get_custom_fields('customers', ['show_on_table' => 1]);

foreach ($custom_fields as $field) {
    array_push($table_data, [
        'name'     => $field['name'],
        'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
    ]);
}
$table_data = hooks()->apply_filters('customers_table_columns', $table_data);
?>
                        <div class="panel-table-full">
                            <?php
           render_datatable($table_data, 'clients', ['number-index-2'], [
               'data-last-order-identifier' => 'customers',
               'data-default-order'         => get_table_last_order('customers'),
               'id'                         => 'clients',
           ]);
?>
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
        var tAPI = initDataTable('.table-clients', admin_url + 'clients/table', [0], [0], {},
            <?= hooks()->apply_filters('customers_table_default_order', json_encode([2, 'asc'])); ?>
        );
    });

    function customers_bulk_action(event) {
        var r = confirm(app.lang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if (mass_delete == false || typeof(mass_delete) == 'undefined') {
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'clients/bulk_action', data).done(function() {
                    window.location.reload();
                });
            }, 50);
        }
    }
</script>
</body>

</html>