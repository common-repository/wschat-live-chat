<?php
use WSChat\HelpAndSupport\HelpAndSupportController;
?>
<div class="eacmcm p-3">
	<div class="p-1 fw-bold">
		<p><?php esc_html_e( 'Before raising the ticket, we recommend you to go through our detailed', 'wschat' ); ?> <a href="https://elextensions.com/knowledge-base/set-up-wschat-wordpress-live-chat-plugin/" target="_blank"><?php esc_html_e( 'documentation.', 'wschat' ); ?></a></p>
		<p><?php esc_html_e( 'Or', 'wschat' ); ?></p>
		<p class="mb-0"><?php esc_html_e( 'To get in touch with one of our helpdesk representatives, please raise a support ticket on our website.', 'wschat' ); ?></p>
		<div class="text-danger fw-normal"><small><?php esc_html_e( '* Please don`t forget to attach your System Info File with the request for better support.', 'wschat' ); ?></small></div>

		<!-- <button class="btn btn-primary py-3 my-3">Raise a Ticket</button> -->

		<a href='https://support.elextensions.com/' target="_blank"><button type="button" class="btn btn-primary py-2 my-3" id="elex_support"><?php echo esc_html_e( 'Raise a ticket', 'wschat' ); ?></button></a>
		<div class="d-flex gap-3">

			<form action="<?php echo esc_url( admin_url( 'admin.php?page=helpandsupport&tab=ticket' ) ); ?>" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'action', 'system_info_nonce' ); ?>
				<input type="hidden" name="action" value="raq_download_system_info" />

				<div>
					<textarea hidden readonly="readonly" onclick="this.focus();this.select()" id="ssi-textarea" name="send-system-info-textarea-raq" title="<?php esc_html_e( 'To copy the System Info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'elex_youtube_video_gallery' ); ?>">
							<?php
							// $system_info = new Request_a_Quote();
							// echo esc_html($system_info->display());
							echo esc_html( HelpAndSupportController::display() );
							?>
							</textarea>
				</div>

				<p class="submit">
					<input type="submit" class="btn btn-outline-primary" value="<?php esc_html_e( 'Download System Info', 'wschat' ); ?>" />
				</p>
			</form>

		</div>

	</div>

</div>
