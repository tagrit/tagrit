<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                        <?= e($title); ?>
                    </h4>
                    <?php if (isset($role)) { ?>
                    <a href="<?= admin_url('roles/role'); ?>"
                        class="btn btn-success btn-sm">
                        <?= _l('new_role'); ?>
                    </a>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?= form_open($this->uri->uri_string()); ?>
                        <?php if (isset($role)) { ?>
                        <?php if (total_rows(db_prefix() . 'staff', ['role' => $role->roleid]) > 0) { ?>
                        <div class="alert alert-warning bold">
                            <?= _l('change_role_permission_warning'); ?>
                            <div class="checkbox">
                                <input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
                                <label for="update_staff_permissions">
                                    <?= _l('role_update_staff_permissions'); ?>
                                </label>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <?php $attrs = (isset($role) ? [] : ['autofocus' => true]); ?>
                        <?php $value = (isset($role) ? $role->name : ''); ?>
                        <?= render_input('name', 'role_add_edit_name', $value, 'text', $attrs); ?>
                        <?php $this->load->view('admin/staff/permissions', ['funcData' => ['role' => $role ?? null]]); ?>
                        <hr />
                        <button type="submit" class="btn btn-primary pull-right">
                            <?= _l('submit'); ?>
                        </button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
            <?php if (isset($role_staff)) { ?>
            <div class="col-md-5">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <?= _l('staff_which_are_using_role'); ?>
                </h4>
                <div class="panel_s tw-mt-3">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?= _l('staff_dt_name'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($role_staff as $staff) { ?>
                                    <tr>
                                        <td>
                                            <?= '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'], [
                                                'staff-profile-image-small',
                                            ]) . '</a>';
                                        echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . e($staff['firstname'] . ' ' . $staff['lastname']) . '</a>';
                                        ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        $(function() {
            appValidateForm($('form'), {
                name: 'required'
            });
        });
    </script>
    </body>

    </html>