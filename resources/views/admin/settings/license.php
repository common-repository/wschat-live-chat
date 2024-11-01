	<?php
	$unique_product_id      = get_option( $plugin_name . '_unique_product_id' );
	$licence_key            = get_option( $plugin_name . '_licence_key' );
	$instance               = get_option( $plugin_name . '_instance_id' );
	$new_status             = get_option( $plugin_name . '_activation_status' );

	$show_activation   = ( ! empty( $new_status ) && 'inactive' !== $new_status ) ? 'hidden' : '';
	$show_deactivation = ( empty( $new_status ) || 'inactive' === $new_status ) ? 'hidden' : '';

	$product_variations = array(
		''   => '',
		9095 => '9095 - Single Site',
		9096 => '9096 - Up to 5 Sites',
		9097 => '9097 - Up to 25 Sites',
	);
	?>
	<div class="wschat-wrapper">
		<div class="container-fluid mt-3">
			<div class="p-3">
				<div id="result" class="aw-result-box"></div>
				<div class="row">
					<div class="col-md-8 col-lg-6 card p-0 activation_panel mb-3 border-0 shadow-sm">
						<h3 class="border-0 fs-4 p-3 card-header elex-bg-primary-light"><?php esc_attr_e( 'Licence Activation', 'wschat' ); ?></h3>
						<div class="border-0 card-body" id="activation_panel_body">
							<div id="aw-activation" class="content-row col-md-12 <?php echo esc_attr( $show_activation ); ?>">
								<div class="crm-form-element">
									<div class="row mb-3">
										<div class="col-md-6">
											<span class="help-block"><?php esc_attr_e( 'Enter your API Licence Key?', 'wschat' ); ?></span>
										</div>
										<div class="col-md-6">
											<input type="text" class="  form-control crm-form-element-input" placeholder="Licence Key" value="" id="wschat_txt_licence_key">
										</div>


									</div>
								</div>
								<div class="crm-form-element">
									<div class="row mb-3">
										<div class="col-md-6">
											<label for="wschat_txt_unique_product_id" class="aw-label"><?php esc_attr_e( 'Product Id:', 'wschat' ); ?></label>
										</div>

										<div class="col-md-6">
											<select id="wschat_txt_unique_product_id" required class="form-select">
												<?php foreach ( $product_variations as $product_id => $variation ) { ?>
													<option value="<?php esc_attr_e( $product_id ); ?>"><?php esc_attr_e( $variation, 'wschat' ); ?></option>
												<?php } ?>
											</select>
										</div>


									</div>
								</div>
								<div class="crm-form-element mt-3">
									<div class="row">
										<div class="col-md-6"><button type="button" id="wschat_btn_licence_activate" data-loading-text="Activating WSChat..." class="btn btn-primary btn-lg ml-auto"><?php esc_attr_e( 'Activate WSChat', 'wschat' ); ?></button></div>

										<div class="col-md-6"><span class="help-block" style="text-align: center">Check <a href="https://elextensions.com/my-account/my-api-keys/" target="_blank"><?php esc_attr_e( 'My Account', 'wschat' ); ?></a> <?php esc_attr_e( 'for API Keys and API Downloads.', 'wschat' ); ?></span></div>


									</div>
								</div>
							</div>
							<div id="aw-deactivation" class="content-row <?php echo esc_attr( $show_deactivation ); ?>">
								<input type="hidden" id="hid_licence_key" value="<?php echo esc_attr( $licence_key ); ?>">
								<input type="hidden" id="hid_unique_product_id" value="<?php echo esc_attr( $unique_product_id ); ?>">
								<div class="aw-deactivation-info">
									<?php esc_attr_e( 'Licence', 'wschat' ); ?>: <span id="info-licence-key"><?php echo esc_attr( $licence_key ); ?></span> &nbsp;|&nbsp;
									<?php esc_attr_e( 'Product id', 'wschat' ); ?>: <span id="info-licence-unique-product-id"><?php echo esc_attr( $unique_product_id ); ?></span> &nbsp;|&nbsp;
									<?php esc_attr_e( 'Status', 'wschat' ); ?>: <span id="info-status"><?php echo esc_attr_e( $new_status, 'wschat' ); ?></span>
									<br>
									<br>
									<button type="button" id="btn_licence_deactivate" data-loading-text="Deactivating WSChat..." class="btn btn-danger btn-lg ml-auto"><?php esc_attr_e( 'Deactivate WSChat', 'wschat' ); ?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default p-3 col-12" style="background:white ">
						<div class="panel-heading">
							<h3 class="panel-title mb-3 elex-wschat-title" style="text-align:center;background: #337ab7;color: white;"><?php esc_html_e( 'ELEXtensions Plugins You May Be Interested In' ); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'WSDesk - Helpdesk _.png' ); ?>" class="marketing_logos" style="height:250px;width:250px">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/wsdesk-wordpress-support-desk-plugin/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">WSDesk – ELEX WordPress Helpdesk & Customer Support Ticket System</a> </h5>
										</div>
									</div>
								</div>
								<div class="col-md-4" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'Advanced Bulk Edit.png' ); ?>" style="height:250px;width:250px" class="marketing_logos">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/bulk-edit-products-prices-attributes-for-woocommerce/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">ELEX Bulk Edit Products, Prices &amp; Attributes for WooCommerce</a></h5>
										</div>
									</div>
								</div>
								<div class="col-md-4" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'Google Shopping Feeds.png' ); ?>" style="height:250px;width:250px" class="marketing_logos">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/woocommerce-google-product-feed-plugin/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">ELEX WooCommerce Google Product Feed Plugin</a></h5>
										</div>
									</div>
								</div>
								<div class="col-md-4" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'Catalog Mode,.png' ); ?>" style="height:250px;width:250px" class="marketing_logos">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/woocommerce-catalog-mode-wholesale-role-based-pricing/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">ELEX WooCommerce Catalog Mode, Wholesale &amp; Role Based Pricing</a></h5>
										</div>
									</div>
								</div>
								<div class="col-md-4 mb-3" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'Dynamic Pricing _ Discounts.png' ); ?>" class="marketing_logos" style="height:250px;width:250px">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/dynamic-pricing-and-discounts-plugin-for-woocommerce/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">ELEX Dynamic Pricing and Discounts Plugin for WooCommerce</a></h5>
										</div>
									</div>
								</div>
								<div class="col-md-4" style="text-align:center">
									<div class="row mb-3">
										<div class="col-md-12">
											<img src="<?php esc_html_e( ELEX_WSCHAT_CRM_MAIN_IMG . 'DHL.png' ); ?>" style="height:250px;width:250px" class="marketing_logos">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<h5 class="elex-wschat-link-title"><a href="https://elextensions.com/plugin/woocommerce-dhl-express-ecommerce-paket-shipping-plugin-with-print-label/?utm_source=plugin-settings-related&utm_medium=wp-admin&utm_campaign=in-prod-ads" data-wpel-link="internal" target="_blank">ELEX WooCommerce DHL Express / eCommerce / Paket / Parcel Shipping Plugin with Print Label</a></h5>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 my-3" style="text-align:center">
									<form action="https://elextensions.com/product-category/plugins/" target="_blank">
										<button href="https://elextensions.com/product-category/plugins/" class="btn marketing_redirect_links btn btn-primary" target="_blank">Browse All ELEXtensions Plugins</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		jQuery(document).on("click", "#wschat_btn_licence_activate", function() {
			var me = jQuery(this);
			var licence_key = jQuery('#wschat_txt_licence_key').val();
			var unique_product_id = jQuery('#wschat_txt_unique_product_id').val();
			var action = "wf_activate_license_keys_" + "<?php echo esc_attr( $plugin_name ); ?>";
			var nonce = "<?php echo esc_attr( wp_create_nonce( '_wpnonce' ) ); ?>";
			var submit_data = {
				action: action,
				licence_key: licence_key,
				unique_product_id: unique_product_id,
				nonce:nonce
			};
			jQuery('#wschat_txt_licence_key').css("border", "1px solid #ddd");
			jQuery('#wschat_txt_licence_key').css("border", "1px solid #ddd");
			if (licence_key !== "" && unique_product_id !== "") {
				var btn = jQuery(this);
				btn.prop("disabled", "disabled");
				var ajax_url = 'admin-ajax.php?page=wschat_settings';
				jQuery.get(ajax_url, submit_data, function(data) {
					var formatted_data = jQuery.parseJSON(data);
					btn.prop("disabled", false);
					var html_msg = '';
					if (typeof formatted_data.error !== "undefined") {
						var remove_style = 'updated';
						var add_style = 'error';

						var additional_info = '';
						if (typeof formatted_data['additional info'] !== "undefined") {
							additional_info = formatted_data['additional info'];
						}

						html_msg = "<p><strong>" + formatted_data.error + ": " + additional_info + " </strong></p>";
					} else if (formatted_data.activated) {
						var html_msg = "<p> WSChat Successfully Activated </p>";
						var add_style = 'updated';
						var remove_style = 'error';

						jQuery("#info-status").html('active');
						jQuery("#info-licence-key").html(licence_key);
						jQuery("#info-licence-unique-product-id").html(unique_product_id);

						jQuery('#hid_licence_key').val(licence_key);
						jQuery('#hid_unique_product_id').val(unique_product_id);

						jQuery("#aw-activation").addClass("hidden");
						jQuery("#aw-deactivation").removeClass("hidden");
						jQuery(".activation_wschat").removeClass("not_activated").addClass("get_activated");
						jQuery("#aw_wschat_status").html("( Activated )");
					} else {
						var remove_style = 'updated';
						var add_style = 'error';
						var html_msg = "<p><strong>" + formatted_data + " </strong></p>";
					}
					jQuery("#result").html(html_msg)
						.show()
						.removeClass(remove_style)
						.addClass(add_style)
						.addClass('show');

					setTimeout(function() {
						jQuery("#result").fadeOut();
					}, 3000);
				});
			} else {
				if (jQuery('#wschat_txt_licence_key').val() === "") {
					jQuery('#wschat_txt_licence_key').css("border", "1px solid red");
				}
				if (jQuery('#wschat_txt_unique_product_id').val() === "") {
					jQuery('#wschat_txt_unique_product_id').css("border", "1px solid red");
				}
			}

		});
		jQuery(document).on("click", "#btn_licence_deactivate", function() {
			me = jQuery(this);
			var btn = jQuery(this);
			btn.prop("disabled", "disabled");
			licence_key = jQuery('#hid_licence_key').val();
			unique_product_id = jQuery('#hid_unique_product_id').val();
			action = "wf_deactivate_license_keys_" + "<?php echo esc_attr( $plugin_name ); ?>";
			nonce = "<?php echo esc_attr( wp_create_nonce( '_wpnonce' ) ); ?>";
			var submit_data = {
				action: action,
				licence_key: licence_key,
				unique_product_id: unique_product_id,
				nonce:nonce
			};

			if (licence_key.length > 0) {
				ajax_url = 'admin-ajax.php?page=wschat_settings';
				jQuery.get(ajax_url, submit_data, function(data) {
					btn.removeProp("disabled");
					var formatted_data = jQuery.parseJSON(data);
					var html_msg = '';
					if (typeof formatted_data.error !== "undefined") {
						remove_style = 'updated';
						add_style = 'error';

						additional_info = '';
						if (typeof formatted_data['additional info'] !== "undefined") {
							additional_info = formatted_data['additional info'];
						}

						html_msg = "<p><strong>" + formatted_data.error + ": " + additional_info + " </strong></p>";
					} else if (formatted_data.deactivated) {
						add_style = 'updated';
						remove_style = 'error';
						html_msg = "<p><strong> The WSChat licence has been deactivated successfully</strong></p>";
						jQuery("#aw-activation").removeClass("hidden");
						jQuery("#aw-deactivation").addClass("hidden");
						jQuery(".activation_wschat").removeClass("get_activated").addClass("not_activated");
						jQuery("#aw_wschat_status").html("( Not Activated )");
						jQuery("alert_for_activation").remove();
					} else {
						remove_style = 'updated';
						add_style = 'error';
						html_msg = "<p><strong> " + formatted_data + "</strong></p>";
					}
					jQuery("#result").html(html_msg)
						.show()
						.removeClass(remove_style)
						.addClass(add_style);

					setTimeout(function() {
						jQuery("#result").fadeOut();
					}, 3000);
				});
			}

		});
	</script>
