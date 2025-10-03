<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="wrapper" >
   <div class="content">
   		<?php echo form_hidden('connector', $connector); ?>
   		<?php echo form_hidden('client_default_language', $client_default_language); ?>
   		<?php echo form_hidden('media_locale', get_media_locale($locale)); ?>
  		<div id="elfinder"></div>
	</div>
</div>