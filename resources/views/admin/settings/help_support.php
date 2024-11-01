<div class="wschat-wrapper">
	<!-- content -->
	<div class="container-fluid d-flex">
		<!-- main content -->
		<div class="h-100 w-100">
			<div class="p-2 pe-4 w-100">
				<?php require __DIR__ . '/help_support_header.php'; ?>
				<?php
				/**
				* Fire a action hook for help and support tab
				*
				* @since 2.0.0
				* @param $active_tab
				*/
				 do_action( 'req_settings_tab_' . $active_tab );
				?>
			</div>
		</div>
	</div>
</div>
