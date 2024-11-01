<?php
/**
 * Space to add admin notifications
 *
 * Helpful to show Pusher connection issues
 *
 * @since 2.0.0
 */
do_action( 'wschat_admin_settings_notices' );
?>

<div class="wschat-wrapper wschat-pages">
	<div class="container-fluid py-3 mt-3">
	<div class="alert hidden" role="alert"></div>
		<div class="submit d-flex justify-content-between align-items-center">
			<h6 class=""><?php echo esc_attr__( 'Settings', 'wschat' ); ?></h1>
			<button type="reset" name="reset" class="btn btn-sm btn-primary reset-settings" value=""><?php echo esc_attr__( 'Reset to default', 'wschat' ); ?></button>
</div>

<ul class="nav elex-wschat-admin-setting-nav nav-fill">
<?php foreach ( $menus as $menu_item ) { ?>
	<li class="nav-item">
		<a
			class="nav-link shadow-none fw-bolder <?php echo ( $active_tab === $menu_item['slug'] ) ? 'active' : ''; ?>"
		href="
		<?php
		echo esc_url_raw(
			add_query_arg(
				array(
					'page' => 'wschat_settings',
					'tab'  => $menu_item['slug'],
				),
				admin_url( 'admin.php' )
			)
		);
		?>
			"
		>
			<span><?php echo esc_attr( $menu_item['title'] ); ?></span>
			<?php if ( 'restrictions' === $menu_item['slug'] ) { ?>
									<sup class="elex_wschat_go_premium_color"><?php esc_html_e( '[Premium]', 'wschat' ); ?></sup>
									<?php } ?>
		</a>
	</li>
<?php } ?>
</ul>
<?php
/**
		 * Fire a filter hook for settings tab
 *
		 * @since 2.0.0
		 * @param $active_tab
		 */
do_action( 'wschat_settings_tab', $active_tab );
/**
		 * Fire an action hook for settings tab
 *
		 * @since 2.0.0
		 * @param $active_tab
		 */
do_action( 'wschat_settings_tab_' . $active_tab );
?>
	</div>
</div>

<script>
	(function() {
		jQuery('.reset-settings').click(function(e) {
					e.preventDefault();
		const result = Swal.fire({
			title: 'Are you sure?',
			text: 'You want to Restore Settings?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
	  }).then(function (result) {
			if (!result.isConfirmed) {
				return;
			}
			const alert = jQuery('.alert');
			jQuery.ajax({
				type: 'post',
				url: ajaxurl,
				data: {
				action: 'reset_settings',
					nonce: jQuery('input[name=wschat_settings_nonce]').val(),
				},
				success: function(data){
					window.location.reload();
					alert.removeClass('hidden').addClass('alert-success').text(data.data.message);
				}
			});
		});
	});

})();
</script>
