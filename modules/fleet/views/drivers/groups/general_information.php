<div class="col-md-12 pl-0" >
    <div class="col-md-5">
      <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
          <div class="card box-shadow-0 overflow-hidden">
            <?php if ($driver->status_work == 'working') {?>
              <div class="ribbon ribbon-top-right text-info"><span class="bg_working"><?php echo _l('hr_working'); ?></span></div>
            <?php } elseif ($driver->status_work == 'maternity_leave') {?>
              <div class="ribbon ribbon-top-right text-info"><span class="bg_maternity_leave"><?php echo _l('hr_maternity_leave'); ?></span></div>
            <?php } elseif ($driver->status_work == 'inactivity') {?>
              <div class="ribbon ribbon-top-right text-info"><span class="bg_inactivity"><?php echo _l('hr_inactivity'); ?></span></div>
            <?php }?>
            <div class="card-body">
              <div class="text-center">
                <div class="userprofile">
                  <div class="userpic  brround mb-3">
                    <?php echo staff_profile_image($driver->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                  </div>
                  <h3 class="username mb-2"><?php echo new_html_entity_decode($driver->firstname . ' ' . $driver->lastname); ?></h3>
                </div>
              </div>
            </div>
            <br>
          </div>
        </div>
      </div>
      <div class="card panel-theme">
        <div class="card-body no-padding">
          <ul class="list-group no-margin">
            <li class="list-group-item"><i class="fa fa-envelope mr-4"></i> <?php echo new_html_entity_decode($driver->email) ?></li>
            <li class="list-group-item"><i class="fa fa-phone mr-4"></i> <?php echo new_html_entity_decode($driver->phonenumber) ?></li>
            <li class="list-group-item"><i class="fa fa-graduation-cap mr-4"></i> <?php echo new_html_entity_decode($driver->literacy ?? '') ?></li>
            <li class="list-group-item"><i class="fa fa-transgender mr-4"></i> <?php echo new_html_entity_decode(_l($driver->sex ?? '')) ?></li>
          </ul>
        </div>

        <div class="card-header">
          <div class="float-left">
            <br>
            <h4 class="card-title text-center"><?php echo _l('staff_profile_departments') ?></h4>
          </div>
          <div class="clearfix"></div>
        </div>

        <div class="card-body no-padding">
          <ul class="list-group no-margin">
            <li class="list-group-item">

              <?php if (count($staff_departments) > 0) {
  ?>
                <div class="form-group mtop10">
                  <div class="clearfix"></div>
                  <?php
foreach ($departments as $department) {
    ?>
                    <?php
foreach ($staff_departments as $staff_department) {
      if ($staff_department['departmentid'] == $department['departmentid']) {?>
                        <div class="chip-circle"><?php echo new_html_entity_decode($staff_department['name']); ?></div>
                      <?php }
    }
    ?>
                  <?php }?>
                </div>
              <?php }?>

            </li>
          </ul>
        </div>

        <div class="card-header">
          <div class="float-left">
            <br>
            <h4 class="card-title text-left"><?php echo _l('hr_team_manage') . ':  ' . staff_profile_image($driver->team_manage, ['staff-profile-image-small']) . '  ' . get_staff_full_name($driver->team_manage) ?></h4>
          </div>
          <div class="clearfix"></div>
        </div>

      </div>

    </div>
    <div class="col-md-7">

      <div class="col-md-12">
        <h4 class="bold"><?php echo _l('hr_general_infor'); ?></h4>

        <table class="table border table-striped ">
          <tbody>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_hr_code'); ?></td>
              <td><?php echo new_html_entity_decode($driver->staff_identifi); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="30%"><?php echo _l('hr_hr_staff_name'); ?></td>
              <td><?php echo new_html_entity_decode($driver->firstname . ' ' . $driver->lastname); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_sex'); ?></td>
              <td><?php echo _l($driver->sex ?? ''); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold" ><?php echo _l('hr_hr_birthday'); ?></td>
              <td><?php echo _d($driver->birthday ?? ''); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('staff_add_edit_phonenumber'); ?></td>
              <td><?php echo new_html_entity_decode($driver->phonenumber); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_hr_workplace'); ?></td>
              <td>
                <?php echo new_html_entity_decode(hr_profile_get_workplace_name($driver->workplace ?? '')) ?>
              </td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_status_label'); ?></td>
              <td>
                <?php echo new_html_entity_decode(_l($driver->status_work ?? '')) ?>
              </td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_hr_job_position'); ?></td>
              <td>
                <?php
if ($driver->job_position > 0) {
  $job_position_name = new_html_entity_decode(hr_profile_get_job_position_name($driver->job_position))
  ?>
  <a href="<?php echo admin_url() . 'hr_profile/job_position_view_edit/' . $driver->job_position; ?>"><?php echo new_html_entity_decode($job_position_name); ?></a>
                    <?php
}

?>
              </td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_hr_literacy'); ?></td>
              <td>
                <?php echo new_html_entity_decode(_l($driver->literacy ?? '')) ?>
              </td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('staff_hourly_rate'); ?></td>
              <td>
                <?php echo new_html_entity_decode($driver->hourly_rate) ?>
              </td>
            </tr>

            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_religion'); ?></td>
              <td><?php echo new_html_entity_decode($driver->religion); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_nation'); ?></td>
              <td><?php echo new_html_entity_decode($driver->nation); ?></td>
            </tr>

            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_marital_status'); ?></td>
              <td><?php echo _l($driver->marital_status ?? ''); ?></td>
            </tr>

          </tbody>
        </table>
      </div>


      <div class="col-md-12">
        <h4><?php echo _l('hr_staff_profile_related_info'); ?></h4>
        <table class="table border table-striped ">
          <tbody>

            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_citizen_identification'); ?></td>
              <td><?php echo new_html_entity_decode($driver->identification ?? ''); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold" width="40%"><?php echo _l('hr_license_date'); ?></td>
              <td><?php echo _d($driver->days_for_identity); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_birthplace'); ?></td>
              <td><?php echo _l($driver->birthplace ?? ''); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_current_address'); ?></td>
              <td><?php echo new_html_entity_decode($driver->current_address); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_resident'); ?></td>
              <td><?php echo new_html_entity_decode($driver->resident); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_hr_home_town'); ?></td>
              <td><?php echo new_html_entity_decode($driver->home_town); ?></td>
            </tr>


            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_bank_account_number'); ?></td>
              <td><?php echo new_html_entity_decode($driver->account_number); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_bank_account_name'); ?></td>
              <td><?php echo new_html_entity_decode($driver->name_account); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_bank_name'); ?></td>
              <td><?php echo new_html_entity_decode($driver->issue_bank); ?></td>
            </tr>
            <tr class="project-overview">
              <td class="bold"><?php echo _l('hr_Personal_tax_code'); ?></td>
              <td><?php echo new_html_entity_decode($driver->Personal_tax_code); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-12">
        <br>
        <h4><?php echo _l('hr_orther_infor'); ?></h4>
        <h5 class="text-justify"><?php echo new_html_entity_decode($driver->orther_infor); ?></h5>
      </div>


    </div>
  </div>