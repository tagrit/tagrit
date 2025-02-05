<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open_multipart('purchase/vendors_portal/setting_language',['id'=>'change-language-form']); ?>
     <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 mtop25">
                </div>
                <div class="col-md-6">
                    <div class="form-group company-profile-language-group">
                                <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                            </label>
                            <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="default_language" id="default_language" class="form-control selectpicker">
                                <option value="" <?php if($vendor->default_language == ''){echo 'selected';} ?>><?php echo _l('system_default_string'); ?></option>
                                <?php foreach($this->app->get_available_languages() as $availableLanguage){
                                      $selected = '';
                                      if($vendor->default_language == $availableLanguage){
                                          $selected = 'selected';
                                      }
                                  ?>
                                  <option value="<?php echo pur_html_entity_decode($availableLanguage); ?>" <?php echo pur_html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
                              <?php } ?>
                          </select>
                      </div>
              </div>
      
            <div class="col-md-3 mtop25">
                <div class="form-group">
                    <button type="submit" class="btn btn-info company-profile-save">
                        <?php echo _l('clients_edit_profile_update_btn'); ?>
                    </button>
                </div>
            </div>
       
    </div>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
