<style type="text/css">
<?php if(acc_get_browser_name() == 'Safari'){ ?>
 .fa-hand-o-right {
  position: relative;
}
.fa-hand-o-right:before {
  content: "\f009";
  font-family: FontAwesome;
  font-style: normal;
  font-weight: normal;
  text-decoration: inherit;
/*--adjust as necessary--*/
color: #000;
font-size: 18px;
padding-right: 0.5em;
position: absolute;
/*top: 10px;*/
left: 0;
}
.underline{
 border-bottom: 1px dotted #333;
}

.number { 
   font-family: Allerta Stencil; 
}

div { 
   font-family: 'Helvetica', 'Arial', sans-serif;
}
table{
   width: 100%;
   font-size:15px;
}
.card-item{
   width: 100%; 
   line-height: 2; 
   box-sizing: border-box;
   /*height: 319px;*/
   /*height: 300px;*/
   /*height: 310px;*/
   
}
.card-item-border{
   border: 1px solid #000;
}

.align-right{
   text-align: right;
}
.align-center{
   text-align: center;
}
@media print{
  @page 
  {
     size: auto;   /* auto is the initial value */
     margin: 0mm;  /* this affects the margin in the printer settings */
  }
/*  .card-item {
   margin-top: 2mm !important;
}*/
.card-item-print {
   /*margin-top: 8px !important;*/
}

.page-break-before{
   page-break-before: always;
}
}
.padding-top-10{
   padding-top: 10px;
}
.padding-bottom-10{
   padding-bottom: 10px;
}

.company-infor-box{
   height: 45px;
   /*height: 50px;*/
}

.bank-infor-box{
   height: 40px;
}
.vendor-infor-box{
   height: 30px;
}
.signature-infor-box{
   width:150px; 
   height:40px;
}
.text-center{
   text-align: center;
}
.company-font-size{
   font-size: 12px ;
}

.bank-font-size{
   font-size: 11px ;
}
.date-font-size{
   font-size: 12px ;
}
.content-font-size{
   font-size: 11px;
}
.account-number-font-size{
   font-size: 17px;
}
.table-cellpadding{
   padding: 0px !important;
   margin: 0;
   vertical-align: baseline;
}

.table-cellpadding1{
   padding-right: 10px !important;
   padding-left: 10px !important;
   padding-bottom: 5px !important;
}

<?php }else{ ?>
 .fa-hand-o-right {
  position: relative;
}
.fa-hand-o-right:before {
  content: "\f009";
  font-family: FontAwesome;
  font-style: normal;
  font-weight: normal;
  text-decoration: inherit;
/*--adjust as necessary--*/
color: #000;
font-size: 18px;
padding-right: 0.5em;
position: absolute;
/*top: 10px;*/
left: 0;
}
.underline{
 border-bottom: 1px dotted #333;
}

.number { 
   font-family: Allerta Stencil; 
}
div { 
   font-family: 'Helvetica', 'Arial', sans-serif;
}
table{
   width: 100%;
   font-size:15px;
}
.card-item{
   width: 100%; 
   line-height: 2; 
   box-sizing: border-box;
   /*height: 319px;*/
   height: 310px;
   padding-left: 15px;
   padding-right: 15px;
   padding-bottom: 15px;
   
}
.card-item-border{
   border: 1px solid #000;
}

.align-right{
   text-align: right;
}
.align-center{
   text-align: center;
}
@media print{
  @page 
  {
     size: auto;   /* auto is the initial value */
     margin: 2mm;  /* this affects the margin in the printer settings */
  }
  .card-item {
   margin-top: 0 !important;
}
.page-break-before{
   page-break-before: always;
}
}
.padding-top-10{
   padding-top: 10px;
}
.padding-bottom-10{
   padding-bottom: 10px;
}

.company-infor-box{
   height: 47px;
}

.bank-infor-box{
   height: 47px;
}
.vendor-infor-box{
   height: 50px;
}
.signature-infor-box{
   width:150px; 
   height:50px;
}
.text-center{
   text-align: center;
}
.company-font-size{
   font-size: 13px ;
}

.bank-font-size{
   font-size: 11px ;
}
.date-font-size{
   font-size: 13px ;
}
.content-font-size{
   font-size: 12px;
}


<?php } ?>

@font-face {
  font-family: 'MicrFont';
  src: url('<?php echo site_url("/modules/accounting/assets/plugins/micr-encoding/micrenc.ttf"); ?> ')  format('truetype')
}

</style>
<?php 

$routing_number_icon_a = 'a';
$routing_number_icon_b = 'a';

$bank_account_icon_a = 'a';
$bank_account_icon_b = 'a';

$current_check_no_icon_a = 'a';
$current_check_no_icon_b = 'a';

$check_type = 'type_1';


$acc_routing_number_icon_a = get_option('acc_routing_number_icon_a');
if($acc_routing_number_icon_a != ''){
   $routing_number_icon_a = $acc_routing_number_icon_a;
}
$acc_routing_number_icon_b = get_option('acc_routing_number_icon_b');
if($acc_routing_number_icon_b != ''){
   $routing_number_icon_b = $acc_routing_number_icon_b;
}

$acc_bank_account_icon_a = get_option('acc_bank_account_icon_a');
if($acc_bank_account_icon_a != ''){
   $bank_account_icon_a = $acc_bank_account_icon_a;
}
$acc_bank_account_icon_b = get_option('acc_bank_account_icon_b');
if($acc_bank_account_icon_b != ''){
   $bank_account_icon_b = $acc_bank_account_icon_b;
}

$acc_current_check_no_icon_a = get_option('acc_current_check_no_icon_a');
if($acc_current_check_no_icon_a != ''){
   $current_check_no_icon_a = $acc_current_check_no_icon_a;
}
$acc_current_check_no_icon_b = get_option('acc_current_check_no_icon_b');
if($acc_current_check_no_icon_b != ''){
   $current_check_no_icon_b = $acc_current_check_no_icon_b;
}

$acc_check_type = get_option('acc_check_type');
if($acc_check_type != ''){
   $check_type = $acc_check_type;
}
$i = 0;
foreach($ids as $key_id => $check_id){
   $check = $this->accounting_model->get_check($check_id);
   $bank_account = $this->accounting_model->get_accounts($check->bank_account);
   if($i != 0 && $i % 3 != 0){
     ?>
      <br>
<?php }
$page_break_before = '';
if($i % 3 == 0 && $i != 0){
   $page_break_before = ' page-break-before';
}
$i++;
$class_card_item = ' card-item-border';
$class_card_item = '';
$show_label_name = 1;
$underline_class_name = 'underline';
if($check->include_company_name_address == 0 && $check->include_bank_name == 0 && $check->include_check_number == 0 && $check->include_routing_account_numbers == 0){
   $class_card_item = '';
   $show_label_name = 0;
   $underline_class_name = '';

}
if($check){
   ?>
   <?php if(acc_get_browser_name() == 'Safari'){ ?>
<table  class="table-cellpadding1 card-item<?php echo  new_html_entity_decode($class_card_item); ?><?php echo new_html_entity_decode($page_break_before); ?>">
   <?php }else{ ?>
<table class="card-item<?php echo  new_html_entity_decode($class_card_item); ?><?php echo new_html_entity_decode($page_break_before); ?>">
   <?php } ?>
      <tbody>
         <tr>
            <td>
               <table>
                  <tbody>
                     <tr>
                        <td width="30%" colspan="3" class="company-infor-box text-center company-font-size">
                           <?php if($check->include_company_name_address == 1){ ?>
                              <span style="font-weight:bold; font-family: Roboto;"><?php echo get_option('invoice_company_name'); ?></span>
                              <br>
                              <span><?php echo $check->address; ?></span>
                              <br>
                              <span><?php echo $check->city.' '.$check->state.' '.$check->zip; ?></span>
                           <?php } ?>
                        </td>
                        <td width="30%" colspan="3" style="vertical-align: top;" class="bank-infor-box text-center bank-font-size">
                           <?php if($check->include_bank_name == 1){ ?>
                              <span style="font-weight:bold; font-family: Roboto;"><?php echo $check->bank_name; ?></span>
                              <br>
                              <span><?php echo $check->address_line_1; ?></span>
                              <br>
                              <span><?php echo $check->address_line_2; ?></span>
                           <?php } ?>
                        </td>
                        <td style="vertical-align: top; text-align: right; font-family: Roboto;" width="40%" colspan="4">
                           <?php if($check->include_check_number == 1){ ?>
                              <span style=" font-weight:bold; font-size:15px;"></span><span>
                                 <?php echo str_pad($check->number, 4, '0', STR_PAD_LEFT); ?></span>
                              <?php } ?>
                           </td>
                        </tr>
                        <tr>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%">
                           </td>
                           <td width="10%" style="text-align: right;" class="date-font-size" >
                                 <span style="text-align: right; font-weight:bold; font-family: Roboto;">
                              <?php if($show_label_name == 1){ ?>
                                    <?php echo _l('acc_date'); ?>: 
                              <?php } ?>
                                 </span>
                           </td>
                           <td style="text-align: right; font-family: Roboto;" width="30%" class =" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" colspan="3">
                              <?php $value = (isset($check) ? _d($check->date) : ''); ?>
                              <span style="white-space: nowrap;">
                                 <?php echo $value; ?></span>
                                 <br>
                              </td>
                           </tr>
                           <tr>
                              <td width="15%" colspan="1" class="company-font-size">
                                 <?php if($show_label_name == 1){ ?>
                                 <span style="text-align: right; font-weight:bold; text-transform: uppercase; font-family: Roboto;">
                                    <?php echo _l('pay_to_the_order_of'); ?></span>
                                 <?php } ?>
                              </td>
                              <td  width="55%" colspan="6" class=" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>">
                                 <?php $value = (isset($check) ? $check->rel_id : ''); ?>
                                 <br>
                                 <span style="text-align: left; font-family: Roboto; line-height: 2;">
                                    <?php echo acc_get_vendor_name($check->rel_id); ?></span>
                                 </td>
                                 <td width="10%" style="text-align: right; font-family: Roboto;" class=" company-font-size">
                                    <?php if($show_label_name == 1){ ?>
                                       <span style="font-weight:bold;"><?php echo new_html_entity_decode($currency->symbol); ?> </span>
                                    <?php } ?>
                                 </td>
                                 <td style="text-align: right; font-family: Roboto;" width="20%" class="content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" colspan="2">
                                    <?php $value = (isset($check) ? $check->amount : ''); ?>

                                    <br>
                                    <span >
                                       <?php echo app_format_money($value, ''); ?></span>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td colspan="8" class=" content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" width="80%" style="line-height: 2;">
                                       <span style="width: 200px; font-family: Roboto;"><?php echo $this->numberword->convert($value, '', false, false); ?></span>
                                    </td>
                                    <td width="15%" colspan="2" class=" company-font-size">
                                       <?php if($show_label_name == 1){ ?>
                                       Dollars
                                    <?php } ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td width="10%">
                                    </td>
                                    <td width="50%" colspan="5" class="vendor-infor-box content-font-size">
                                       <br>
                                       <?php echo acc_format_organization_info($check->rel_id); ?>
                                    </td>
                                 <td width="10%">
                                 </td>
                                 <?php 
                                 if($check_type == 'type_3' || $check_type == 'type_4'){ ?>
                                    <td width="30%" colspan="3" class="signature-infor-box <?php echo new_html_entity_decode($underline_class_name); ?>" style="text-align: right;">
                                     
                                    </td>
                                 <?php } ?>
                                 </tr>
                                 <tr>
                                    <td width="10%" class="company-font-size" style="position: relative;">
                                       <?php if($show_label_name == 1){ ?>
                                          <span style="font-weight:bold; font-family: Roboto; bottom: 0; position: absolute;"><?php echo _l('acc_memo') ?></span>
                                       <?php } ?>
                                    </td>
                                    <td width="50%" colspan="5" class="content-font-size <?php echo new_html_entity_decode($underline_class_name); ?>" style="position: relative;">
                                       <span style="font-family: Roboto; bottom: 0; position: absolute;"><?php $value = (isset($check) ? $check->memo : '');
                                       echo $value;
                                    ?></span>
                                 </td>
                                 <td width="10%">
                                 </td>
                                 <td width="30%" colspan="3" class="signature-infor-box <?php echo new_html_entity_decode($underline_class_name); ?>" style="text-align: right;">
                                  <?php
                                  if($check->signed == 1 && $check_type != 'type_3' && $check_type != 'type_4'){
                                    $path = FCPATH . ACCOUTING_PATH.'checks/signature/'.$check->id.'/signature_'.$check->id;
                                    if (file_exists($path.'.png')) {
                                       $path = $path.'.png';
                                    }else{
                                       $path = $path.'.jpeg';
                                    }

                                     $type = pathinfo($path, PATHINFO_EXTENSION);
                                     $data = file_get_contents($path);
                                     $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    echo '<img src="'. $base64.'" style="width:150px; height:50px;">';
                                  }
                                 ?>
                              </td>
                           </tr>
                           <tr style="width:100%">
                              <td width="100%" colspan="10" style="font-size:25px; font-family: MicrFont; padding-top: 5px; padding-bottom: 5; font-weight: 600;">
                                 <?php if($check->include_check_number == 1){ ?>
                                   <?php if($current_check_no_icon_a != '' && $current_check_no_icon_a != 'e'){
                                     echo $current_check_no_icon_a;
                                  } ?><span><?php echo str_pad($check->number, 4, '0', STR_PAD_LEFT); ?></span><?php if($current_check_no_icon_b != '' && $current_check_no_icon_b != 'e'){ 
                                    echo $current_check_no_icon_b;
                                  } ?>
                              <?php } ?>
                              <span>&nbsp;</span>
                              <?php if($check->include_routing_account_numbers == 1){ ?>   
                                 <?php if($routing_number_icon_a != '' && $routing_number_icon_a != 'e'){ 
                                    echo $routing_number_icon_a;
                                  } ?><span><?php echo str_pad($bank_account->bank_routing, 9, '0', STR_PAD_LEFT); ?></span><?php if($routing_number_icon_b != '' && $routing_number_icon_b != 'e'){ 
                                    echo $routing_number_icon_b;
                                    } ?>
                              <span>&nbsp;</span>
                                 <?php if($bank_account_icon_a != '' && $bank_account_icon_a != 'e'){ 
                                    echo $bank_account_icon_a;
                                   } ?><span><?php echo str_pad($bank_account->bank_account, 9, '0', STR_PAD_LEFT); ?></span><?php if($bank_account_icon_b != '' && $bank_account_icon_b != 'e'){ 
                                    echo $bank_account_icon_b;
                                    } ?>
                              <?php } ?>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      <?php 
   }
} 
?>