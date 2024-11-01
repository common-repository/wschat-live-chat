<form method="post">
	<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
	<div class="my-3">
		<div class="d-flex gap-5 align-items-center">
			<div class=" d-flex gap-2 align-items-center">
				<h6 class="mb-0"><?php echo esc_attr__( 'Email Notifications', 'wschat' ); ?></h6>
				<label class="switch">
					<input type="checkbox" onchange="" name="email_notifications" <?php echo $wschat_options['email_notifications'] ? 'checked' : ''; ?> />
					<span class="slider round"></span>
				</label>
				<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
				<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
			</div>
			<div class="">
				<p class="description mb-0 xs"><?php echo esc_attr__( 'Toggle to send an email notification to the customers when the chat ends (if turned On)', 'wschat' ); ?></p>
			</div>
		</div>
	</div>

	<div class="wschat_email_settings_from_container d-none">
		<div class="mb-3 ">
		<h6><?php esc_attr_e( 'Send Following Email(S) When Agent End The Chat' ); ?></h6>
			<div class="d-flex gap-3 flex-wrap align-items-center">
				<div class="">
					<input class="" type="checkbox" value="" name="feedback_form" id="elex-wschat-feedback-form" <?php echo $wschat_options['feedback_form'] ? 'checked' : ''; ?>>
					<label class="form-check-label fw-bold" for="elex-wschat-feedback-form">
						Feedback Form
					</label>
				</div>
				<div class="">
					<input class="" type="checkbox" value="checked" name="email_transcript_chat" id="elex-wschat-email-transcript-chat" <?php echo $wschat_options['email_transcript_chat'] ? 'checked' : ''; ?>>

					<label class="form-check-label fw-bold" for="elex-wschat-email-transcript-chat">
						Email Transcript of the chat
					</label>
				</div>

				<div class="text-secondary xs">Followings will be sent as an email once chat is ended by the agent.</div>
			</div>
		</div>
	</div>
	<p class="submit "><button type="submit" name="submit" id="submit" class="button button-primary"><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
</form>

<script>
	jQuery(function() {
		jQuery('input[name=email_notifications]').on('change', function() {
			const container = jQuery('.wschat_email_settings_from_container');

			if (this.checked === false) {
				container.addClass('d-none');
			} else {
				container.removeClass('d-none');
			}
		}).trigger('change');
	});
</script>
