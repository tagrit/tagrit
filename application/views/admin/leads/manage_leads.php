<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" id="vueApp">
        <div class="row">
            <div class="col-md-12">
                <div
                    class="leads-overview tw-mb-6<?= $isKanBan ? ' hide' : ''; ?>">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-2">
                        <?= _l('leads'); ?>
                    </h4>
                    <div class="tw-grid tw-gap-2 sm:tw-grid-flow-col sm:tw-auto-cols-max tw-overflow-x-auto">
                        <?php foreach ($summary as $status) { ?>
                        <?php if (isset($status['junk']) || isset($status['lost'])) { ?>
                        <span class="label label-danger" data-toggle="tooltip">
                            <?= $status['total']; ?>
                            <?= e($status['name']); ?>
                            -
                            <?= $status['percent']; ?>%
                        </span>
                        <?php } else { ?>
                        <button type="button"
                            @click="extra.leadsRules = <?= app\services\utilities\Js::from($table->findRule('status')->setValue([$status['id']])); ?>"
                            class="tw-bg-transparent tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-200/60 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= e($status['total']); ?>
                            </span>
                            <span class="tw-font-medium"
                                style="color:<?= e($status['color']); ?>">
                                <?= e($status['name']); ?>
                            </span>
                        </button>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="_buttons tw-mb-2">
                    <div class="tw-flex tw-items-center tw-justify-between tw-space-x-2 rtl:tw-space-x-reverse">
                        <div class="tw-flex tw-items-center tw-space-x-3 rtl:tw-space-x-reverse">
                            <a href="#" onclick="init_lead(); return false;" class="btn btn-primary">
                                <i class="fa-regular fa-plus"></i>
                                <?= _l('new_lead'); ?>
                            </a>
                            <a href="<?= admin_url('leads/switch_kanban/' . $switch_kanban); ?>"
                                class="btn btn-default hidden-xs" data-toggle="tooltip" data-placement="top"
                                data-title="<?= $switch_kanban == 1 ? _l('leads_switch_to_kanban') : _l('switch_to_list_view'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                <i class="fa-solid fa-grip-vertical"></i>
                                <?php } else { ?>
                                <i class="fa-solid fa-table-list"></i>
                                <?php } ?>
                            </a>
                            <?php if (is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1') { ?>
                            <a href="<?= admin_url('leads/import'); ?>"
                                class="hidden-xs tw-text-neutral-600 hover:tw-text-neutral-800 focus:tw-text-neutral-800 tw-font-semibold !tw-ml-6">
                                <i class="fa-solid fa-upload tw-mr-1"></i>
                                <?= _l('import_leads'); ?>
                            </a>
                            <?php } ?>
                        </div>
                        <div>
                            <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                            <div class="leads-search">
                                <div data-toggle="tooltip" data-placement="top"
                                    data-title="<?= _l('search_by_tags'); ?>">
                                    <?= render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'leads_kanban();', 'placeholder' => _l('leads_search')], [], 'no-margin') ?>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="tw-inline">
                                <app-filters
                                    id="<?= $table->id(); ?>"
                                    view="<?= $table->viewName(); ?>"
                                    :rules="extra.leadsRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([$this->input->get('status')]) : []); ?>"
                                    :saved-filters="<?= $table->filtersJs(); ?>"
                                    :available-rules="<?= $table->rulesJs(); ?>">
                                </app-filters>
                            </div>
                            <?php } ?>
                            <?= form_hidden('sort_type'); ?>
                            <?= form_hidden('sort', (get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                    </div>
                </div>
                <div
                    class="<?= $isKanBan ? '' : 'panel_s'; ?>">
                    <div
                        class="<?= $isKanBan ? '' : 'panel-body'; ?>">
                        <div class="tab-content">
                            <?php
                        if ($isKanBan) { ?>
                            <div class="active kan-ban-tab tw-mt-4" id="kan-ban-tab" style="overflow:auto;">
                                <div class="kanban-leads-sort">
                                    <span
                                        class="bold"><?= _l('leads_sort_by'); ?>:
                                    </span>
                                    <a href="#" onclick="leads_kanban_sort('dateadded'); return false"
                                        class="dateadded">
                                        <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                        } ?><?= _l('leads_sort_by_datecreated'); ?>
                                    </a>
                                    |
                                    <a href="#" onclick="leads_kanban_sort('leadorder');return false;"
                                        class="leadorder">
                                        <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                        } ?><?= _l('leads_sort_by_kanban_order'); ?>
                                    </a>
                                    |
                                    <a href="#" onclick="leads_kanban_sort('lastcontact');return false;"
                                        class="lastcontact">
                                        <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                                            echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                        } ?><?= _l('leads_sort_by_lastcontact'); ?>
                                    </a>
                                </div>
                                <div class="row">
                                    <div class="container-fluid leads-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="row" id="leads-table">
                                <div class="col-md-12">
                                    <a href="#" data-toggle="modal" data-table=".table-leads"
                                        data-target="#leads_bulk_actions"
                                        class="hide bulk-actions-btn table-btn"><?= _l('bulk_actions'); ?></a>
                                    <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">
                                                        <?= _l('bulk_actions'); ?>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?php if (staff_can('delete', 'leads')) { ?>
                                                    <div class="checkbox checkbox-danger">
                                                        <input type="checkbox" name="mass_delete" id="mass_delete">
                                                        <label
                                                            for="mass_delete"><?= _l('mass_delete'); ?></label>
                                                    </div>
                                                    <hr class="mass_delete_separator" />
                                                    <?php } ?>
                                                    <div id="bulk_change">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary checkbox-inline">
                                                                <input type="checkbox" name="leads_bulk_mark_lost"
                                                                    id="leads_bulk_mark_lost" value="1">
                                                                <label for="leads_bulk_mark_lost">
                                                                    <?= _l('lead_mark_as_lost'); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?= render_select('move_to_status_leads_bulk', $statuses, ['id', 'name'], 'ticket_single_change_status'); ?>
                                                        <?= render_select('move_to_source_leads_bulk', $sources, ['id', 'name'], 'lead_source');
                                echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                echo render_select('assign_to_leads_bulk', $staff, ['staffid', ['firstname', 'lastname']], 'leads_dt_assigned');
                                ?>
                                                        <div class="form-group">
                                                            <?= '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                            <input type="text" class="tagsinput" id="tags_bulk"
                                                                name="tags_bulk" value="" data-role="tagsinput">
                                                        </div>
                                                        <hr />
                                                        <div class="form-group no-mbot">
                                                            <div class="radio radio-primary radio-inline">
                                                                <input type="radio" name="leads_bulk_visibility"
                                                                    id="leads_bulk_public" value="public">
                                                                <label for="leads_bulk_public">
                                                                    <?= _l('lead_public'); ?>
                                                                </label>
                                                            </div>
                                                            <div class="radio radio-primary radio-inline">
                                                                <input type="radio" name="leads_bulk_visibility"
                                                                    id="leads_bulk_private" value="private">
                                                                <label for="leads_bulk_private">
                                                                    <?= _l('private'); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal"><?= _l('close'); ?></button>
                                                    <a href="#" class="btn btn-primary"
                                                        onclick="leads_bulk_action(this); return false;"><?= _l('confirm'); ?></a>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->
                                    <?php

                              $table_data    = [];
                                $_table_data = [
                                    '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                    [
                                        'name'     => _l('the_number_sign'),
                                        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                                    ],
                                    [
                                        'name'     => _l('leads_dt_name'),
                                        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-name'],
                                    ],
                                ];
                                if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                    $_table_data[] = [
                                        'name'     => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                        'th_attrs' => ['id' => 'th-consent', 'class' => 'not-export'],
                                    ];
                                }
                                $_table_data[] = [
                                    'name'     => _l('lead_company'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_email'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-email'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_phonenumber'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_lead_value'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-lead-value'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('tags'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-tags'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_assigned'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-assigned'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_status'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-status'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_source'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-source'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_last_contact'),
                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-contact'],
                                ];
                                $_table_data[] = [
                                    'name'     => _l('leads_dt_datecreated'),
                                    'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-date-created'],
                                ];

                                foreach ($_table_data as $_t) {
                                    array_push($table_data, $_t);
                                }
                                $custom_fields = get_custom_fields('leads', ['show_on_table' => 1]);

                                foreach ($custom_fields as $field) {
                                    array_push($table_data, [
                                        'name'     => $field['name'],
                                        'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                                    ]);
                                }
                                $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                                ?>
                                    <div class="panel-table-full">
                                        <?php
                                  render_datatable(
                                      $table_data,
                                      'leads',
                                      ['customizable-table number-index-2'],
                                      [
                                          'id'                         => 'leads',
                                          'data-last-order-identifier' => 'leads',
                                          'data-default-order'         => get_table_last_order('leads'),
                                      ]
                                  );
                                ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
    <?= get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once APPPATH . 'views/admin/leads/status.php'; ?>
<?php init_tail(); ?>
<script>
    var openLeadID = '<?= e($leadid); ?>';
    $(function() {
        leads_kanban();
        $('#leads_bulk_mark_lost').on('change', function() {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh')
        });
        $('#move_to_status_leads_bulk').on('change', function() {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });
    });
</script>
</body>

</html>