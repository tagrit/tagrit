<div class="panel-body">
    <!-- Companies -->
    <table class="table dt-table" id="example">
        <thead class="table-head">
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Prefix</th>
            <th>Contact Person</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($companies)): ?>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company->company_name); ?></td>
                    <td><?php echo htmlspecialchars(strtoupper(str_replace('_',' ',$company->type))); ?></td>
                    <td><?php echo htmlspecialchars($company->prefix); ?></td>
                    <td>
                        <?php if (!empty($company->contact_person_first_name)): ?>
                            <div class="d-flex flex-column justify-content-center">
                                <p style="font-weight: bold; font-size:14px;">
                                    <?php echo htmlspecialchars($company->contact_person_first_name) . ' ' . htmlspecialchars($company->contact_person_last_name); ?>
                                </p>
                                <p class="text-secondary mb-0">
                                    <?php echo htmlspecialchars($company->contact_person_email); ?>
                                </p>
                                <p class="text-secondary mb-0">
                                    <?php echo htmlspecialchars($company->contact_person_phone_number); ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <p style="color: #666;">NONE</p>
                        <?php endif; ?>
                    </td>
                    <td class="align-left">
                        <div class="d-flex flex-row justify-content-center">
                            <!-- Delete Button -->
                            <a href="<?php echo site_url('admin/courier/companies/delete/' . $company->id); ?>"
                               class="text-danger font-weight-bold text-xs mx-2"
                               onclick="return confirm('Are you sure you want to delete this company?');"
                               data-bs-toggle="tooltip" title="Delete Company">
                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-danger">No available companies</td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot class="table-footer">
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Prefix</th>
            <th>Contact Person</th>
            <th>Action</th>
        </tr>
        </tfoot>
    </table>
</div>
