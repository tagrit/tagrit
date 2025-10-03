<?php if (!empty($agents)): ?>
    <table class="table dt-table" id="example">
        <thead class="table-head">
        <tr>
            <th>Agent Number</th>
            <th>Agent</th>
            <th>Country</th>
            <th>Address</th>
            <th>Type</th>
            <th>Documents</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($agents as $agent): ?>
            <tr class="data-row">
                <td><?php echo $agent->unique_number ?>
                    <div style="margin-top: 30px;" class="action-buttons">
                        <p>
                            <?php if (is_admin()): ?>
                                <a href="<?php echo admin_url('courier/agents/delete/' . $agent->id); ?>"
                                   class="text-danger font-weight-bold text-xs mx-2"
                                   onclick="return confirm('Are you sure you want to delete this agent?');"
                                   data-bs-toggle="tooltip" title="Delete Agent">
                                    </i> Delete
                                </a>
                            <?php endif; ?>
                        </p>
                        <p>
                            <label class="switch">
                                <input type="checkbox" class="setting-switch" data-agent-id="<?php echo $agent->id; ?>" <?php echo $agent->status == 1 ? 'checked' : ''; ?>/>
                                <span class="slider"></span>
                            </label>
                        </p>
                    </div>

                </td>
                <td>
                    <?php if ($agent->agent_type === 'individual'): ?>
                        <div class="d-flex flex-column justify-content-center">
                            <p style="font-weight: bold; font-size: 14px;">
                                <?php echo $agent->firstname . ' ' . $agent->lastname; ?>
                            </p>
                            <p class="text-secondary mb-0"><?php echo $agent->email; ?></p>
                            <p class="text-secondary mb-0"><?php echo $agent->phone_number; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column justify-content-center">
                            <p style="font-weight: bold; font-size: 14px;">
                                COMPANY : <?php echo $agent->company_name; ?>
                            </p>
                            <p style="font-weight: bold; font-size: 14px;">
                                <?php echo $agent->firstname; ?>
                            </p>
                            <p class="text-secondary mb-0"><?php echo $agent->email; ?></p>
                            <p class="text-secondary mb-0"><?php echo $agent->phone_number; ?></p>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?php echo $agent->short_name ?></td>
                <td><?php echo $agent->address ?></td>
                <td><?php echo $agent->agent_type ?></td>
                <td class="align-left">
                    <div class="d-flex flex-row justify-content-center">
                        <div>
                            <a href="<?php echo base_url($agent->id_file_url); ?>"
                               class="text-success font-weight-bold text-xs mx-2"
                               target="_blank"
                               data-bs-toggle="tooltip" title="Delete Agent">
                                <i class="fa fa-file" aria-hidden="true"></i> Id
                            </a>
                        </div>

                        <div>
                            <a href="<?php echo base_url($agent->kra_file_url); ?>"
                               target="_blank"
                               class="text-info font-weight-bold text-xs mx-2"
                               data-bs-toggle="tooltip" title="Delete Agent">
                                <i class="fa fa-dot-circle" aria-hidden="true"></i> KRA
                            </a>
                        </div>

                        <?php if ($agent->agent_type === 'individual'): ?>
                            <div>
                                <a href="<?php echo base_url($agent->location_file_url); ?>"
                                   class="text-warning font-weight-bold text-xs mx-2"
                                   target="_blank"
                                   data-bs-toggle="tooltip" title="Delete Agent">
                                    <i class="fa fa-location" aria-hidden="true"></i> Location
                                </a>
                            </div>
                        <?php else: ?>
                            <div>
                                <a href="<?php echo base_url($agent->cert_of_corp_url); ?>"
                                   class="text-warning font-weight-bold text-xs mx-2"
                                   data-bs-toggle="tooltip" title="Delete Agent">
                                    <i class="fa fa-location" aria-hidden="true"></i> Certificate
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot class="table-footer">
        <tr>
            <th>Agent Number</th>
            <th>Agent</th>
            <th>Country</th>
            <th>Address</th>
            <th>Type</th>
            <th>Documents</th>
        </tr>
        </tfoot>
    </table>
<?php else: ?>
    <div style="margin-top: 30px;" class="text-center text-danger">
        <p>No available Agents</p>
    </div>
<?php endif; ?>
