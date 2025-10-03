<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="panel_s">
				<div class="panel-body" id="file-sharing-config">
					<div class="horizontal-scrollable-tabs  mb-5">
						<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
						<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
						<div class="horizontal-tabs mb-4">
							<ul class="nav nav-tabs nav-tabs-horizontal">
								<li <?php if($tab == 'general'){echo "class='active'"; } ?>>
									<a href="<?php echo admin_url('file_sharing/setting?tab=general'); ?>">
										<i class="fa fa-check-square"></i>&nbsp;<?php echo _l('general'); ?>
									</a>
								</li>
								<li <?php if($tab == 'configuration'){echo "class='active'"; } ?>>
									<a href="<?php echo admin_url('file_sharing/setting?tab=configuration'); ?>">
										<i class="fa fa-cog"></i>&nbsp;<?php echo _l('configuration'); ?>
									</a>
								</li>
							</ul>
						</div>
						<?php $this->load->view($tab); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
