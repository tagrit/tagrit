<div class="row">
    <?php echo form_open('admin/courier/companies/store', ['id' => 'create-company-form']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="flex-container">
                    <h4 class="font-bold m-0">Create Company</h4>
                    <a style="text-decoration: none; border:2px solid black;" class="custom-button" href="<?php echo admin_url('courier/companies/main?group=list_companies'); ?>">
                        View Companies
                    </a>
                </div>
                <hr class="hr-panel-heading"/>

                <section>
                    <div class="container-fluid">
                        <!-- Company Information -->
                        <div class="custom-form-grid">
                            <div class="row section-container">
                                <div class="section-label">Company Information</div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="custom-form-group">
                                        <label for="name" class="custom-label">Company Name</label>
                                        <input id="name" name="name" type="text" class="custom-input" placeholder="Company Name">
                                        <?php if ($this->session->flashdata('name_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('name_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="custom-form-group">
                                        <label for="type" class="custom-label">Company Type</label>
                                        <select id="type" name="type" class="custom-select">
                                            <option value="" selected>Choose Type</option>
                                            <option value="third_party">Third Party</option>
                                            <option value="internal">Internal</option>
                                        </select>
                                        <?php if ($this->session->flashdata('type_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('type_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Company Information -->

                        <!-- Contact Person Information -->
                        <div class="custom-form-grid">
                            <div class="row section-container">
                                <div class="section-label">Contact Person</div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="custom-form-group">
                                        <label for="first_name" class="custom-label">First Name</label>
                                        <input id="first_name" name="first_name" type="text" class="custom-input" placeholder="First Name">
                                        <?php if ($this->session->flashdata('first_name_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('first_name_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="custom-form-group">
                                        <label for="last_name" class="custom-label">Last Name</label>
                                        <input id="last_name" name="last_name" type="text" class="custom-input" placeholder="Last Name">
                                        <?php if ($this->session->flashdata('last_name_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('last_name_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="custom-form-group">
                                        <label for="phone_number" class="custom-label">Phone</label>
                                        <input id="phone_number" name="phone_number" type="text" class="custom-input" placeholder="Phone Number">
                                        <?php if ($this->session->flashdata('phone_number_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('phone_number_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="custom-form-group">
                                        <label for="email" class="custom-label">Email</label>
                                        <input id="email" name="email" type="text" class="custom-input" placeholder="Email">
                                        <?php if ($this->session->flashdata('email_error')): ?>
                                            <div class="text-danger"><?php echo $this->session->flashdata('email_error'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Contact Person Information -->
                        <button type="submit" class="custom-button">Add Company</button>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
