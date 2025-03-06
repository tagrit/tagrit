<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">

                <div style="text-align:right;">
                    <button style="margin-bottom:-20px;" type="submit" class="btn">Download Sample</button>
                </div>

                <div style="margin-bottom: 40px; max-height: 300px; overflow-y: auto; padding: 5px;">
                    <h5 style="font-weight: bold; text-align: left;">Sample Data</h5>
                    <table id="sample_event_registration_import_data"
                           style="width: 100%; border-collapse: collapse; color: black;">
                        <thead style="position: sticky; top: 0; background: white; z-index: 2;">
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Event Name</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Setup</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Type</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Division</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Start Date</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">End Date</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Location</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Venue</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Name of Delegate</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Date & Month of Birth
                            </th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Mobile No</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Email Address</th>
                            <th style="border: 1px solid #ccc; padding: 8px; font-weight: bold;">Organization</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Sample Data</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <p style="font-size: 13px; font-weight: bold; margin-bottom: 15px;">
                    Import Clients Event Registrations
                </p>

                <?php echo form_open('admin/events_due/settings/upload_excel', [
                    'id' => 'register-for-event-form',
                    'enctype' => 'multipart/form-data'
                ]); ?>

                <div style="margin-bottom: 15px;">
                    <label for="csv_file"
                           style="font-size: 14px; vertical-align: middle; display: block; margin-bottom: 5px;">
                        Choose CSV File:
                    </label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv"
                           style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <button style="margin-top:20px;" type="submit" class="btn btn-success">Import</button>

                <?php echo form_close(); ?>

            </div>
        </div>
    </div>
</div>
