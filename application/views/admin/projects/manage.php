<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="tw-block md:tw-hidden">
                        <?php $this->load->view('admin/projects/stats'); ?>
                    </div>
                    <div class="_buttons">
                        <div class="md:tw-flex md:tw-items-center">
                            <?php if (staff_can('create', 'projects')) { ?>
                            <a href="<?= admin_url('projects/project'); ?>"
                                class="btn btn-primary pull-left display-block mright5">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?= _l('new_project'); ?>
                            </a>
                            <?php } ?>
                            <a href="<?= admin_url('projects/gantt'); ?>"
                                data-toggle="tooltip"
                                data-title="<?= _l('project_gant'); ?>"
                                class="btn btn-default btn-with-tooltip">
                                <i class="fa fa-align-left" aria-hidden="true"></i>
                            </a>
                            <div class="tw-hidden md:tw-block md:tw-ml-6 rtl:md:tw-mr-6">
                                <?php $this->load->view('admin/projects/stats'); ?>
                            </div>
                            <div class="ltr:tw-ml-auto rtl:tw-mr-auto">
                                <app-filters
                                    id="<?= $table->id(); ?>"
                                    view="<?= $table->viewName(); ?>"
                                    :rules="extra.projectsRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([(int) $this->input->get('status')]) : []); ?>"
                                    :saved-filters="<?= $table->filtersJs(); ?>"
                                    :available-rules="<?= $table->rulesJs(); ?>">
                                </app-filters>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel_s tw-mt-2">
                        <div class="panel-body">
                            <div class="panel-table-full">
                                <?= form_hidden('custom_view'); ?>
                                <?php $this->load->view('admin/projects/table_html'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
    $(function() {
        initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, {},
            <?= hooks()->apply_filters('projects_table_default_order', json_encode([5, 'asc'])); ?>
        );

        init_ajax_search('customer', '#clientid_copy_project.ajax-search');
    });
</script>
</body>

</html>