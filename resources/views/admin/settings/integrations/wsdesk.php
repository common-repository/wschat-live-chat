<form method="post">
<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
<?php if ( call_user_func( array( $integration, 'is_wsdesk_installed' ) ) === false ) : ?>
	<div class="alert alert-danger">
		<p class="m-0 pb-0"><?php esc_attr_e( 'WSDesk is not installed and activated', 'wsdesk' ); ?></p>
	</div>
<?php endif; ?>
<table class="form-table">
	<tr>
		<th><?php echo esc_attr__( 'WSDesk', 'wschat' ); ?></th>
		<td>
			<div class="wschat-wrapper">
			<label class="switch">
				<input <?php echo esc_attr( call_user_func( array( $integration, 'is_wsdesk_installed' ) ) === false ? 'disabled' : '' ); ?>  type="checkbox" name="wsdesk_enabled" <?php echo $wschat_options['wsdesk_enabled'] ? 'checked' : ''; ?> />
				<span class="slider round"></span>
			</label>
			<span class="d-none switch-label-on"><?php echo esc_attr__( 'On' , 'wschat' ); ?></span>
			<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off' , 'wschat' ); ?></span>
			</div>
		</td>
	</tr>
</table>
<p class="submit "><button type="submit" name="submit" id="submit" class="button button-primary" ><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
</form>
