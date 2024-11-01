<form method="post">
<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
<table class="form-table">
	<tr> <th><?php echo esc_attr__( 'WhatsApp', 'wschat' ); ?></th>
		<td>
			<label class="switch">
				<input type="checkbox" onchange="" name="whatsapp_enable" <?php echo $wschat_options['whatsapp_enable'] ? 'checked' : ''; ?> />
				<span class="slider round"></span>
			</label>
			<span class="d-none switch-label-on"><?php echo esc_attr__( 'On' , 'wschat' ); ?></span>
			<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off' , 'wschat' ); ?></span>
			<p class="description hidden"><?php echo esc_attr__( 'Send a WhatsApp message to Agent on customer message', 'wschat' ); ?></p>
		</td>
	</tr>
	<tr> <th><?php echo esc_attr__( 'Twilio Number', 'wschat' ); ?></th>
		<td>
			<input
				class="form-control"
				placeholder="<?php echo esc_attr__( 'Twilio Number' ); ?>" type="text" name="whatsapp_twilio_number" value="<?php echo esc_attr( $wschat_options['whatsapp_twilio_number'] ); ?>"  />
		</td>
	</tr>
	<tr> <th><?php echo esc_attr__( 'Twilio SID', 'wschat' ); ?></th>
		<td>
			<input
				class="form-control"
				placeholder="<?php echo esc_attr__( 'Twilio SID' ); ?>" type="text" name="whatsapp_twilio_sid" value="<?php echo esc_attr( $wschat_options['whatsapp_twilio_sid'] ); ?>"  />
		</td>
	</tr>
	<tr>
		<th><?php echo esc_attr__( 'Twilio Auth Token', 'wschat' ); ?></th>
		<td>
			<?php if ( $wschat_options->whatsapp_twilio_auth_token ) { ?>
			<input
				class="form-control"
				placeholder="<?php echo esc_attr__( 'Twilio Auth Token' ); ?>"
				type="text"
				name="whatsapp_twilio_auth_token_masked"
				<?php echo $wschat_options->whatsapp_twilio_auth_token ? 'disabled' : ''; ?>
				autocomplete="off"
				value="<?php echo esc_attr( $wschat_options->masked( $wschat_options->whatsapp_twilio_auth_token ) ); ?>"
		  />
			<a href="#" class="wschat_make_edit_token"><?php echo esc_attr__( 'Edit', 'wschat' ); ?></a>
			<?php } else { ?>
			<input
				class="form-control"
				placeholder="<?php echo esc_attr__( 'Twilio Auth Token' ); ?>"
				type="text"
				name="whatsapp_twilio_auth_token"
				autocomplete="off"
				value=""
		  />
			<?php } ?>
		</td>
	</tr>
</table>
<p class="submit "><button type="submit" name="submit" id="submit" class="button button-primary" ><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
</form>

<script>
jQuery(function () {
	jQuery('.wschat_make_edit_token').click(function (e) {
			const token_input = jQuery(this).parent().find('input');
			if (token_input.length === 0) {
				return;
			}

			token_input.attr('name', token_input.attr('name').replace('_masked', '')).val('').prop('disabled', false).focus();
		});
	});
</script>

