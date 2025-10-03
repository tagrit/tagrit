<!-- Record Shipment -->
<section id="creat_agent_section" style="display: block;" class="custom-section">
    <?php echo form_open('admin/courier/agents/store', [
        'id' => 'create-agent-form',
        'enctype' => 'multipart/form-data'
    ]); ?>
    <div class="custom-container">
        <div class="custom-form-grid">
            <div style="margin-top:-10px;" class="row section-container">
                <div class="col-md-12 col-sm-12">
                    <div class="custom-form-group">
                        <?php

                        $show_company = $this->session->userdata('show_company_section') ?? false;

                        ?>
                        <label for="type" class="custom-label">Agent Type</label>
                        <select id="type" name="type" onchange="toggleAgentType()" class="custom-select">
                            <option value="individual">Individual</option>
                            <option value="company" <?php echo $show_company === true ? 'selected' : ''; ?> >Company
                            </option>
                        </select>
                        <?php if ($this->session->flashdata('type_error')): ?>
                            <div class="text-danger"><?php echo $this->session->flashdata('type_error'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="individualContent">
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="first_name" class="custom-label">First Name</label>
                                <?php echo form_input(['id' => 'first_name', 'name' => 'first_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'First Name', 'value' => $this->session->flashdata('first_name') ?: '']); ?>
                                <?php if ($this->session->flashdata('first_name_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('first_name_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="last_name" class="custom-label">Last Name</label>
                                <?php echo form_input(['id' => 'last_name', 'name' => 'last_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Last Name', 'value' => $this->session->flashdata('last_name') ?: '']); ?>
                                <?php if ($this->session->flashdata('last_name_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('last_name_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="email" class="custom-label">Email</label>
                                <?php echo form_input(['id' => 'email', 'name' => 'email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Email', 'value' => $this->session->flashdata('email') ?: '']); ?>
                                <?php if ($this->session->flashdata('email_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('email_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="phone_number" class="custom-label">Phone</label>
                                <?php echo form_input(['id' => 'phone_number', 'name' => 'phone_number', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Phone Number', 'value' => $this->session->flashdata('phone_number') ?: '']); ?>
                                <?php if ($this->session->flashdata('phone_number_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('phone_number_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="id_file">Upload
                                    ID</label>
                                <input class="custom-input" type="file" name="id_file"
                                       id="id_file">
                                <?php if ($this->session->flashdata('id_file_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('id_file_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="kra_file">Upload
                                    KRA PIN</label>
                                <input class="custom-input" type="file" name="kra_file"
                                       id="kra_file"> <?php if ($this->session->flashdata('kra_file_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('kra_file_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="address" class="custom-label">Address</label>
                                <textarea id="address" name="address" class="custom-textarea" rows="3"
                                          autocomplete="off"
                                          placeholder="Enter your address here..."><?php echo $this->session->flashdata('address') ?: ''; ?></textarea>
                                <?php if ($this->session->flashdata('address_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('address_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="padding-left:15px; padding-right:15px;" class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="custom-form-group">
                                    <label for="location_pin_file">Upload
                                        Location PIN</label>
                                    <input class="custom-input" type="file" name="location_pin_file"
                                           id="location_pin_file">
                                    <?php if ($this->session->flashdata('location_pin_file_error')): ?>
                                        <div class="text-danger"><?php echo $this->session->flashdata('location_pin_file_error'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="username" class="custom-label">Username</label>
                                <?php echo form_input(['id' => 'username', 'name' => 'username', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Username', 'value' => $this->session->flashdata('username') ?: '']); ?>
                                <?php if ($this->session->flashdata('username_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('username_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="password" class="custom-label">Password</label>
                                <?php echo form_input(['id' => 'password', 'name' => 'password', 'type' => 'password', 'class' => 'custom-input', 'placeholder' => 'Password', 'value' => $this->session->flashdata('password') ?: '']); ?>
                                <?php if ($this->session->flashdata('password_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('password_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div  style="padding-left:15px; padding-right:15px;"  class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="custom-form-group">
                                    <label for="country_id" class="custom-label">Country</label>
                                    <?php echo form_dropdown('country_id', array_column($countries, 'short_name', 'country_id'), set_value('country_id'), ['id' => 'country_id', 'class' => 'custom-select']); ?>
                                    <?php echo form_error('country_id', '<div class="error-message">', '</div>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="custom-form-group">
                                    <label for="state_id" class="custom-label">State</label>
                                    <select name="state_id" id="state_id"
                                            class="custom-select">
                                    </select>
                                    <?php echo form_error('state_id', '<div class="error-message">', '</div>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="unique_number" class="custom-label">Agent Number</label>
                                <?php echo form_input(['id' => 'unique_number', 'name' => 'unique_number', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Agent Number', 'value' => $this->session->flashdata('unique_number') ?: '','readonly' => 'readonly'
                                ]); ?>
                                <?php if ($this->session->flashdata('unique_number_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('unique_number_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display:none;" id="companyContent">
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="first_name" class="custom-label">Company Name</label>
                                <?php echo form_input(['id' => 'company_name', 'name' => 'company_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Company Name', 'value' => $this->session->flashdata('company_name') ?: '']); ?>
                                <?php if ($this->session->flashdata('company_name_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_name_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="last_name" class="custom-label">Contact Person Name</label>
                                <?php echo form_input(['id' => 'contact_name', 'name' => 'contact_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Contact Person Name', 'value' => $this->session->flashdata('contact_name') ?: '']); ?>
                                <?php if ($this->session->flashdata('contact_name_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('contact_name_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="phone_number" class="custom-label">Contact Person Phone</label>
                                <?php echo form_input(['id' => 'contact_phone_number', 'name' => 'contact_phone_number', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Contact Person Phone Number', 'value' => $this->session->flashdata('contact_phone_number') ?: '']); ?>
                                <?php if ($this->session->flashdata('contact_phone_number_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('contact_phone_number_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="email" class="custom-label">Contact Person Email</label>
                                <?php echo form_input(['id' => 'contact_email', 'name' => 'contact_email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Contact Person Email', 'value' => $this->session->flashdata('contact_email') ?: '']); ?>
                                <?php if ($this->session->flashdata('contact_email_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('contact_email_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="company_id_file">Upload
                                    ID</label>
                                <input class="custom-input" type="file" name="company_id_file"
                                       id="company_id_file">
                                <?php if ($this->session->flashdata('company_id_file_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_id_file_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="commercial_invoice_file">Upload
                                    KRA PIN</label>
                                <input class="custom-input" type="file" name="company_kra_file"
                                       id="company_kra_file"> <?php if ($this->session->flashdata('company_kra_file_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_kra_file_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding-left:15px; padding-right:15px;" class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="company_address" class="custom-label">Company Address</label>
                                <textarea id="company_address" name="company_address" class="custom-textarea" rows="3"
                                          autocomplete="off"
                                          placeholder="Enter your address here..."><?php echo $this->session->flashdata('company_address') ?: ''; ?></textarea>
                                <?php if ($this->session->flashdata('company_address_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_address_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="padding-left:15px; padding-right:15px;" class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="custom-form-group">
                                    <label for="corporation_certificate_file">Upload
                                        Certificate of Corporation</label>
                                    <input class="custom-input" type="file" name="corporation_certificate_file"
                                           id="corporation_certificate_file">
                                    <?php if ($this->session->flashdata('corporation_certificate_file_error')): ?>
                                        <div class="text-danger"><?php echo $this->session->flashdata('corporation_certificate_file_error'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="username" class="custom-label">Username</label>
                                <?php echo form_input(['id' => 'company_username', 'name' => 'company_username', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Company Username', 'value' => $this->session->flashdata('company_username') ?: '']); ?>
                                <?php if ($this->session->flashdata('company_username_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_username_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="password" class="custom-label">Password</label>
                                <?php echo form_input(['id' => 'company_password', 'name' => 'company_password', 'type' => 'password', 'class' => 'custom-input', 'placeholder' => 'Company Password', 'value' => $this->session->flashdata('company_password') ?: '']); ?>
                                <?php if ($this->session->flashdata('company_password_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_password_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="padding-left:15px; padding-right:15px;"  class="row">
                            <div class="col-md-6 col-sm-12">
                                <div style="width: 100%;" class="custom-form-group">
                                    <label for="company_country_id" class="custom-label">Country</label>
                                    <?php echo form_dropdown(
                                        'company_country_id',
                                        array_column($countries, 'short_name', 'country_id'),
                                        set_value('company_country_id'),
                                        [
                                            'id' => 'company_country_id',
                                            'class' => 'custom-select',
                                            'style' => 'width: 100%;'
                                        ]
                                    ); ?>
                                    <?php echo form_error('company_country_id', '<div class="error-message">', '</div>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="custom-form-group">
                                    <label for="company_state_id" class="custom-label">State</label>
                                    <select style="width: 100%;" name="company_state_id" id="company_state_id"
                                            class="custom-select">
                                        <option value="">Select State</option>
                                    </select>
                                    <?php echo form_error('company_state_id', '<div class="error-message">', '</div>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="company_unique_number" class="custom-label">Agent Number</label>
                                <?php echo form_input(['id' => 'company_unique_number', 'name' => 'company_unique_number', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Agent Number', 'value' => $this->session->flashdata('company_unique_number') ?: '','readonly' => 'readonly'
                                ]); ?>
                                <?php if ($this->session->flashdata('company_unique_number_error')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_unique_number_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="custom-button">Add Agent</button>
    </div>
    <?php echo form_close(); ?>
</section>
