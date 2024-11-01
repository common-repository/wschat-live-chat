<div class="row mt-2" >
	<div class="col-md-2 col-sm-3 pl-0" >
		<div class="nav p-1 flex-column nav-pills shadow-sm rounded" >
			<?php foreach ( $menus as $menu_item ) { ?>
				<li class="nav-item">
				<a
						class="nav-link <?php echo esc_html( $menu_item['slug'] === $active_tab ? 'active' : '' ); ?>"
						href="
						<?php
						echo esc_url_raw(
							add_query_arg(
								array(
									'page'   => 'wschat_settings',
									'tab'    => 'integrations',
									'subtab' => $menu_item['slug'],
								),
								admin_url( 'admin.php' )
							)
						);
						?>
						"
						>
					<img src="<?php echo esc_url( $menu_item['icon'] ); ?>" class="img-fluid" width="16px" />
					<span style="padding-left: 0.5rem;"> <?php echo esc_html( $menu_item['title'] ); ?></span>
					<?php if ( 'chatgpt' === $menu_item['slug'] ) : ?>
						<sup class="elex_wschat_go_premium_color"><?php esc_html_e( '[Premium]', 'wschat' ); ?></sup>
					<?php endif; ?>
				</a>
				</li>
			<?php } ?>
		</div>
	</div>
	<div class="col-md-10 col-sm-3 pr-0" >
		<?php 
		/** 
		 * Fire an action hook for settings tab integration
		 * 
		 * @since 2.0.0
		 */
		do_action( 'wschat_settings_tab_integrations_' . $active_tab ); 
		?>
	</div>
</div>
