<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div>
                            <h4 class="font-bold m-0"><i class="fa fa-dashboard menu-icon"></i><span
                                        style="margin-left:4px;">Manifests</span></h4>
                        </div>
                        <hr class="hr-panel-heading"/>
                        <?php if (!empty($manifests)): ?>
                        <table class="table dt-table" data-order-type="desc" data-order-col="3" id="example">
                            <thead class="table-head">
                            <tr>
                                <th>AWB NUMBER</th>
                                <th>Flight NUMBER</th>
                                <th>TOTAL($USD)</th>
                                <th>CREATED AT</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($manifests as $manifest): ?>
                            <tr>
                                <td><?php echo $manifest->manifest_number; ?></td>
                                <td><?php echo $manifest->flight_number; ?></td>
                                <td><?php echo $manifest->total; ?></td>
                                <td><?php echo $manifest->created_at; ?></td>
                                <td>
                                    <a style="font-size:9px; margin-right:6px; margin-top:5px;"
                                       href="<?php echo admin_url('courier/manifests/view/' . $manifest->manifest_number); ?>"
                                       class="btn btn-info btn-sm font-weight-bold text-xs mx-2"
                                       title="Edit Manifest">
                                        <i class="fa fa-eye" aria-hidden="true"></i> View/Edit
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach;  ?>
                            </tbody>
                            <tfoot class="table-footer">
                            <tr>
                                <th>AWB NUMBER</th>
                                <th>Flight NUMBER</th>
                                <th>CREATED AT</th>
                                <th>TOTAL($USD)</th>
                                <th>ACTION</th>
                            </tr>
                            </tfoot>
                        </table>
                        <?php else: ?>
                            <!-- Show a message when there's no data -->
                            <div class="text-center text-danger">
                                <p>No available manifests</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div
    </div>
</div>
<?php init_tail(); ?>
