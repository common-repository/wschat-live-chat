<form method="post">
<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
<table class="form-table">
	<tr>
		<td>
		<p><strong><?php echo esc_attr__( 'Inclusion list', 'wschat' ); ?></strong></p>
			<textarea disabled class="form-control form-control-sm w-50" name="inclusion_list" rows="6"  placeholder="Enter page URLs here ..." ><?php esc_attr_e( isset( $wschat_options['inclusion_list'] ) ? implode( "\n", $wschat_options['inclusion_list'] ) : '' ); ?></textarea>
			<p class="description mt-3"> <?php echo esc_attr__( 'By default WSChat widget will be available for all the pages in your site. If you want to display it on any specific page(s). Please mention the page URLs in a new line.', 'wschat' ); ?></p>
		</td>
	</tr>
	<tr>
		<td>
		<p><strong><?php echo esc_attr__( 'Exclusion list', 'wschat' ); ?></strong></p>
			<textarea disabled class="form-control form-control-sm w-50 " name="exclusion_list" rows="6" placeholder="Enter page URLs here ..." ><?php esc_attr_e( isset( $wschat_options['exclusion_list'] ) ? implode( "\n", $wschat_options['exclusion_list'] ) : '' ); ?></textarea>
			<p class="description mt-3"> <?php echo esc_attr__( 'By default WSChat widget will be available for all the pages in your site. If you want to restrict it to display for any specific page(s) . Please mention the page URLs in a new line.', 'wschat' ); ?></p>
		</td>
	</tr>
	<tr>
		<td>
		<p><strong><?php echo esc_attr__( 'Exclusion list of IP addresses', 'wschat' ); ?></strong></p>
			<textarea disabled class="form-control form-control-sm w-50" name="ip_addresses_list" rows="6"  placeholder="Enter ip addresses here ..." ><?php esc_attr_e( isset( $wschat_options['ip_addresses_list'] ) ? implode( "\n", $wschat_options['ip_addresses_list'] ) : '' ); ?></textarea>
			<p class="description mt-3"> <?php echo esc_attr__( 'By default WSChat widget will be available for all the IP Addresses in your site. If you want to restrict it to display for any specific IP Addresses . Please mention the IP Addresses in a new line.', 'wschat' ); ?></p>
		</td>
	</tr>
	<tr>
		<td>
		<p><strong><?php echo esc_attr__( 'Exclusion list of Email Addresses', 'wschat' ); ?></strong></p>
			<textarea disabled class="form-control form-control-sm w-50" name="email_addresses_list" rows="6"  placeholder="Enter email addresses here ..." ><?php esc_attr_e( isset( $wschat_options['email_addresses_list'] ) ? implode( "\n", $wschat_options['email_addresses_list'] ) : '' ); ?></textarea>
			<p class="description mt-3"> <?php echo esc_attr__( 'By default WSChat widget will be available for all the Email Addresses in your site. If you want to restrict it to display for any specific Email Addresses. Please mention the Email Addresses in a new line.', 'wschat' ); ?></p>
		</td>
	</tr>
	</table>
</form>   


