<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('vendor_add_edit_profile'); ?></h4>
<div class="row">
<?php echo form_hidden('userid',( isset($client) ? $client->userid : '') ); ?>
<?php echo form_open($this->uri->uri_string(),array('class'=>'vendor-form','autocomplete'=>'off')); ?>
<div class="additional"></div>
<div class="col-md-12">
   <div class="horizontal-scrollable-tabs">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
               <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
               <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                  <?php echo _l( 'vendor_detail'); ?>
               </a>
            </li>

            <li role="presentation">
               <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                  <?php echo _l( 'billing_shipping'); ?>
               </a>
            </li>
            </ul>
         </div>
      </div>
      
   <div class="tab-content">


      <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
         <div class="row">

            <div class="col-md-6">
               <?php $vendor_code = ( isset($client) ? $client->vendor_code : '');
                   echo render_input('vendor_code','vendor_code',$vendor_code,'text'); ?>
                  <?php $value=( isset($client) ? $client->company : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input( 'company', 'client_company',$value,'text',$attrs); ?>
                  <div id="company_exists_info" class="hide"></div>
                  <?php 
                     $balance = isset($client) ? $client->balance : '';
                     $balance_as_of = isset($client) ? _d($client->balance_as_of) : '';
                     $attr = [];
                     $date_attr = [];
                     $attr['data-type'] = 'currency';
                     if(isset($client) && $client->balance != null && $client->balance != 0){
                        $attr['disabled'] = 'true';
                        $date_attr['disabled'] = 'true';
                     }
                  ?>
                  <div class="row">
                     <div class="col-md-6">
                        <?php echo render_input('balance', 'balance', $balance, 'text', $attr); ?>
                     </div>
                     <div class="col-md-6">
                        <?php echo render_date_input('balance_as_of', 'as_of', $balance_as_of, $date_attr); ?>
                     </div>
                  </div>
                  <?php 
                     $value=( isset($client) ? $client->vat : '');
                     echo render_input( 'vat', 'client_vat_number',$value);
                      ?>
                  <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                  <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>

               <?php if((isset($client) && empty($client->website)) || !isset($client)){
                  $value=( isset($client) ? $client->website : '');
                  echo render_input( 'website', 'client_website',$value);
               } else { ?>
                  <div class="form-group">
                     <label for="website"><?php echo _l('client_website'); ?></label>
                     <div class="input-group">
                        <input type="text" name="website" id="website" value="<?php echo new_html_entity_decode($client->website); ?>" class="form-control">
                        <div class="input-group-addon">
                           <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                        </div>
                     </div>
                  </div>
               <?php } ?>
              <?php if(!isset($client)){ ?>
                  <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                  <?php }
                     $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
                     $selected = '';
                     
                     foreach($currencies as $currency){
                        if(isset($client)){
                          if($currency['id'] == $client->default_currency){
                            $selected = $currency['id'];
                         }
                      }
                     }
                            // Do not remove the currency field from the customer profile!
                     echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                  <?php if(get_option('disable_language') == 0){ ?>
                  <div class="form-group select-placeholder">
                     <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                     </label>
                     <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('system_default_string'); ?></option>
                        <?php foreach($this->app->get_available_languages() as $availableLanguage){
                           $selected = '';
                           if(isset($client)){
                              if($client->default_language == $availableLanguage){
                                 $selected = 'selected';
                              }
                           }
                           ?>
                        <option value="<?php echo new_html_entity_decode($availableLanguage); ?>" <?php echo new_html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
                        <?php } ?>
                     </select>
                  </div>
                  <?php } ?>
            </div>
            <div class="col-md-6">
               <?php $value=( isset($client) ? $client->address : ''); ?>
               <?php echo render_textarea( 'address', 'client_address',$value, array('rows' => 7)); ?>
               <?php $value=( isset($client) ? $client->city : ''); ?>
               <?php echo render_input( 'city', 'client_city',$value); ?>
               <?php $value=( isset($client) ? $client->state : ''); ?>
               <?php echo render_input( 'state', 'client_state',$value); ?>

               <?php $value=( isset($client) ? $client->zip : ''); ?>
               <?php echo render_input( 'zip', 'client_postal_code',$value); ?>
               <?php $selected=( isset($client) ? $client->country : '' ); ?>
               <?php 
               $countries = get_all_countries();
               echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
            </div>
         </div>
      </div>

      <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
         <div class="row">
            <div class="col-md-12">
               <div class="row">
                  <div class="col-md-6">
                     <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('vendor_billing_same_as_profile'); ?></small></a></h4>
                     <hr />
                     <?php 
                     $value=( isset($client) ? $client->billing_street : ''); ?>
                     <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
                     <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                     <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
                     <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                     <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
                    
                     <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                     <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                     <?php $selected=( isset($client) ? $client->billing_country : '' ); ?>
                     <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                  </div>
                  <div class="col-md-6">
                     <h4 class="no-mtop">
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                        <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                     </h4>
                     <hr />
                     <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                     <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
                     <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                     <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
                     <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                     <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
                     
                     <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                     <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                     <?php $selected=( isset($client) ? $client->shipping_country : '' ); ?>
                     <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

<div class="modal fade" id="duplicate-vendor-modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
           <h4 class="modal-title"><?php echo _l('duplicate_data')?></h4>
         </div>
         <div class="modal-body">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('OK'); ?></button>
         </div>
      </div>
   </div>
</div>
<?php echo form_close(); ?>
</div>