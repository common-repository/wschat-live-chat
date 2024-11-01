<?php

use WSChat\PreChatForm\PreChatForm;
?>
<form method="post">
	<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
	<table class="form-table">
		<tr>
			<th><?php echo esc_attr__( 'Live Chat', 'wschat' ); ?></th>
			<td>
				<div class="wschat-wrapper d-flex gap-3">
					<label class="switch">
						<input type="checkbox" name="enable_live_chat" <?php echo $wschat_options['enable_live_chat'] ? 'checked' : ''; ?> />
						<span class="slider round"></span>
					</label>
					<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
					<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
					<p class="description"><?php echo esc_attr__( 'Enable to display a live chat box on your website', 'wschat' ); ?></p>
				</div>
			</td>
		</tr>
		<tr>
			<th><?php echo esc_attr__( 'Tags', 'wschat' ); ?></th>
			<td>
				<div class="wschat-wrapper d-flex gap-3">
					<label class="switch">
						<input type="checkbox" name="enable_tags" <?php echo $wschat_options['enable_tags'] ? 'checked' : ''; ?> />
						<span class="slider round"></span>
					</label>
					<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
					<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
					<p class="description"><?php echo esc_attr__( 'Enable it to tag a customer`s message for future reference', 'wschat' ); ?></p>
				</div>
			</td>
		</tr>
		<tr class="wschat-default-tag-color-sec d-none">
			<th><?php echo esc_attr__( 'Default Tag Color', 'wschat' ); ?></th>
			<td>
				<div class="wschat-wrapper">
					<div style="width: 200px;">
						<input value="<?php esc_attr_e( $wschat_options['default_tag_color'] ); ?>" type="text" data-jscolor="{zIndex: 500000}" class="form-control form-control-sm jscolor" name="default_tag_color" autocomplete="off" required>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo esc_attr__( 'Communication Protocol', 'wschat' ); ?>
				<div class="d-flex gap-3 align-items-start mt-2">
					<div class="">
						<label>
							<input class="" type="radio" value="pusher" name="communication_protocol" <?php echo ( 'pusher' === $wschat_options['communication_protocol'] ) ? 'checked' : ''; ?> />
							<svg id="pusher-seeklogo.com" xmlns="http://www.w3.org/2000/svg" width="50" height="" viewBox="0 0 60 15.799">
								<path id="Path_66" data-name="Path 66" d="M5.095.006a.047.047,0,0,1,.047,0h0l5.072,2.926a.05.05,0,0,1,.023.041h0V4.113a.023.023,0,0,1-.012.021h0L5.131,7.072a.023.023,0,0,0-.012.02h0V8.218a.023.023,0,0,0,.036.02h0L10.2,5.327a.024.024,0,0,1,.035.021h0V6.474a.025.025,0,0,1-.012.021h0L5.13,9.433a.023.023,0,0,0-.012.02h0V10.58a.023.023,0,0,0,.035.021h0L10.2,7.689a.023.023,0,0,1,.035.021h0V8.836a.023.023,0,0,1-.012.021h0L5.131,11.8a.024.024,0,0,0-.012.021h0v3.961a.022.022,0,0,1-.011.019.023.023,0,0,1-.022,0h0l-.979-.562a.025.025,0,0,1-.011-.021h0V5.32A.024.024,0,0,0,4.083,5.3h0l-.976-.563a.023.023,0,0,0-.035.021h0V14.6a.024.024,0,0,1-.036.021h0l-.976-.564a.024.024,0,0,1-.012-.021h0V4.141a.025.025,0,0,0-.011-.021h0l-.977-.563a.023.023,0,0,0-.035.02h0v9.838a.023.023,0,0,1-.035.021h0l-.976-.563A.022.022,0,0,1,0,12.851H0V2.945l1-.577a.05.05,0,0,1,.05,0h0L6.115,5.293a.05.05,0,0,0,.05,0h0l.965-.556a.023.023,0,0,0,0-.041h0L2.083,1.784a.023.023,0,0,1,0-.041h0l.962-.556a.05.05,0,0,1,.05,0h0L8.166,4.112a.05.05,0,0,0,.047,0h0l.965-.556a.024.024,0,0,0,0-.041h0L4.13.6a.023.023,0,0,1,0-.041h0ZM33.138,2.821A2.612,2.612,0,0,1,35.9,5.635a.191.191,0,0,1-.183.194h-.943a.181.181,0,0,1-.183-.17,1.516,1.516,0,0,0-1.545-1.62,1.245,1.245,0,0,0-1.387,1.209c0,.643.388.992,1.4,1.7l1.419,1.024a2.865,2.865,0,0,1,1.537,2.372,2.7,2.7,0,0,1-2.948,2.62,2.664,2.664,0,0,1-2.821-2.837.181.181,0,0,1,.183-.195h.958a.187.187,0,0,1,.182.171,1.557,1.557,0,0,0,1.553,1.644A1.334,1.334,0,0,0,34.6,10.379c0-.6-.269-.891-1.047-1.411L31.918,7.829a2.993,2.993,0,0,1-1.64-2.5A2.6,2.6,0,0,1,33.138,2.821Zm-9.351.108a.178.178,0,0,1,.183.179v7.178a1.5,1.5,0,0,0,3,0V3.113a.209.209,0,0,1,.2-.183h1.03a.178.178,0,0,1,.183.178v7.2a2.691,2.691,0,0,1-2.909,2.636,2.686,2.686,0,0,1-2.877-2.636v-7.2a.177.177,0,0,1,.182-.179Zm33.2.016a2.68,2.68,0,0,1,2.916,2.713,2.734,2.734,0,0,1-1.617,2.5.038.038,0,0,0-.024.022.04.04,0,0,0,0,.025l0,.008,1.72,4.395a.182.182,0,0,1-.156.249H58.743a.336.336,0,0,1-.236-.182l-.009-.021L56.976,8.511A.093.093,0,0,0,56.9,8.45H55.478a.1.1,0,0,0-.094.08v4.129a.176.176,0,0,1-.165.177H54.17a.177.177,0,0,1-.181-.16V3.123a.178.178,0,0,1,.164-.178h2.839Zm-38.616,0a2.64,2.64,0,0,1,2.916,2.8,2.769,2.769,0,0,1-2.883,2.868H16.845a.1.1,0,0,0-.094.08v3.982a.178.178,0,0,1-.164.178H15.537a.2.2,0,0,1-.18-.176V3.123A.175.175,0,0,1,15.409,3a.178.178,0,0,1,.111-.05h2.855Zm20.872,0a.178.178,0,0,1,.183.179V7.108a.1.1,0,0,0,.095.093H42.56a.1.1,0,0,0,.1-.093V3.123a.179.179,0,0,1,.183-.179h1.03a.178.178,0,0,1,.182.179v9.542a.178.178,0,0,1-.182.179h-1.03a.184.184,0,0,1-.183-.186V8.488a.1.1,0,0,0-.1-.094H39.526a.1.1,0,0,0-.1.093v4.17a.177.177,0,0,1-.182.178h-1.03a.178.178,0,0,1-.183-.178V3.123a.177.177,0,0,1,.183-.179Zm12.632,0a.176.176,0,0,1,.183.178v.829a.179.179,0,0,1-.183.178H48.147a.1.1,0,0,0-.1.093V7.146a.1.1,0,0,0,.1.093h2.568a.177.177,0,0,1,.182.178v.829a.177.177,0,0,1-.182.178H48.147a.1.1,0,0,0-.1.093v3.039a.1.1,0,0,0,.1.093H51.88a.182.182,0,0,1,.183.178v.829a.183.183,0,0,1-.183.178H46.84a.172.172,0,0,1-.182-.178V3.123a.177.177,0,0,1,.182-.178Zm-33.5,1.163h-1.53a.1.1,0,0,0-.092.081V7.339a.1.1,0,0,0,.084.092h1.54a1.538,1.538,0,0,0,1.49-1.69A1.452,1.452,0,0,0,18.375,4.107Zm38.505,0h-1.4a.087.087,0,0,0-.086.074V7.216a.1.1,0,0,0,.082.092h.013l1.395.008a1.528,1.528,0,0,0,1.577-1.562V5.713A1.509,1.509,0,0,0,56.88,4.107Z" transform="translate(0 0)" fill="#300d4f" />
							</svg>
						</label>
					</div>
					<div class="">
						<label class="form-check-label">
							<input class="" type="radio" value="http" name="communication_protocol" <?php echo ( 'http' === $wschat_options['communication_protocol'] ) ? 'checked' : ''; ?> />
							<svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" width="40" height="" viewBox="0 0 44.169 19.73">
								<path id="Path_60" data-name="Path 60" d="M38.528,0H5.641L0,9.865,5.641,19.73H38.528l5.641-9.865ZM36.643,18.088H6.881l-4.7-8.223,4.7-8.223H36.643l4.7,8.223Z" fill="#008ec7" />
								<path id="Path_61" data-name="Path 61" d="M57.6,24.864l-4.7,8.223H23.133l-4.7-8.223,4.7-8.224H52.895Z" transform="translate(-16.252 -14.998)" fill="#005b9b" />
								<path id="Path_62" data-name="Path 62" d="M79.912,71.89v5.873H78.283V75.354H75.092v2.408H73.46V71.89h1.627v2.315h3.191V71.89Z" transform="translate(-64.778 -64.798)" fill="#fff" />
								<path id="Path_63" data-name="Path 63" d="M137.7,73H135.45V71.89h6.13V73h-2.246v4.765H137.7Z" transform="translate(-119.442 -64.798)" fill="#fff" />
								<path id="Path_64" data-name="Path 64" d="M191.1,73H188.85V71.89h6.13V73h-2.251v4.765H191.1Z" transform="translate(-166.531 -64.798)" fill="#fff" />
								<path id="Path_65" data-name="Path 65" d="M252.788,72.142a2.369,2.369,0,0,1,1.064.738,1.908,1.908,0,0,1,0,2.261,2.324,2.324,0,0,1-1.064.738,4.661,4.661,0,0,1-1.624.255h-1.418v1.62H248.12V71.882h3.044a4.589,4.589,0,0,1,1.624.26Zm-.6,2.621a.857.857,0,0,0,.382-.751.867.867,0,0,0-.382-.759,1.96,1.96,0,0,0-1.116-.264h-1.327v2.038h1.327A1.956,1.956,0,0,0,252.19,74.763Z" transform="translate(-218.796 -64.79)" fill="#fff" />
							</svg>
						</label>
						<div class="text-success xs text-nowrap">
							<?php echo esc_attr__( '(Beta Version)', 'wschat' ); ?>
						</div>
					</div>

				</div>
			</th>
			<td>

				<p><?php echo esc_attr__( 'WebSocket option like Pusher makes the communication fail-safe. WebSocket provider charges will be applicable.', 'wschat' ); ?>
					<a target="_blank" href="https://pusher.com/" /><?php echo esc_attr__( 'Click to create free Pusher account', 'wschat' ); ?></a>
				</p>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="p-0 ">
				<div class="pusher_settings hidden">
					<div class="row">
						<div class="col-lg-4 col-md-6">
							<div class="mb-3">
								<label for="" class="fw-bold"><?php echo esc_attr__( 'App ID', 'wschat' ); ?></label>
								<input id="pusher_app_id" class="form-control" placeholder="<?php echo esc_attr__( 'Enter app_id' ); ?>" type="text" name="pusher[app_id]" value="<?php echo esc_attr( $wschat_options['pusher']['app_id'] ); ?>" />
							</div>
							<div class="mb-3">
								<label for="" class="fw-bold"><?php echo esc_attr__( 'Key', 'wschat' ); ?></label>
								<input id="pusher_app_key" class="form-control" placeholder="<?php echo esc_attr__( 'Enter key' ); ?>" type="text" name="pusher[app_key]" value="<?php echo esc_attr( $wschat_options['pusher']['app_key'] ); ?>" />
							</div>
							<div class="mb-3">
								<label for="" class="fw-bold"><?php echo esc_attr__( 'Secret', 'wschat' ); ?></label>
								<input id="pusher_secret_key" class="form-control" placeholder="<?php echo esc_attr__( 'Enter secret' ); ?>" type="text" name="pusher[secret_key]" value="<?php echo esc_attr( $wschat_options['pusher']['secret_key'] ); ?>" />
							</div>
							<div class="mb-3">
								<label for="" class="fw-bold"><?php echo esc_attr__( 'Cluster', 'wschat' ); ?></label>
								<input id="pusher_cluster_key" class="form-control" placeholder="<?php echo esc_attr__( 'Enter Cluster' ); ?>" type="text" name="pusher[cluster]" value="<?php echo esc_attr( $wschat_options['pusher']['cluster'] ); ?>" />
							</div>
							<a class="btn btn-primary px-5 my-4" id='pusher_verify_button'>Verify & Save</a>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>

	<hr>


	<div class="prechat-form">
		<h5 class="fw-bold"> <?php echo esc_attr__( 'Pre Chat Form', 'wschat' ); ?> </h5>
		<table class="form-table">
			<tr>
				<th><?php echo esc_attr__( 'Status', 'wschat' ); ?></th>
				<td>
					<div class="wschat-wrapper d-flex gap-3">
						<label class="switch">
							<input type="checkbox" name="pre_chat_form[enable]" <?php echo $pre_chat_form->enabled() ? 'checked' : ''; ?> />
							<span class="slider round"></span>
						</label>
						<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
						<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
						<p class="description"><?php echo esc_attr__( 'Enable to display the pre-chat form on the chat widget', 'wschat' ); ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<th><?php echo esc_attr__( 'Pre-Chat Form Label', 'wschat' ); ?></th>
				<td>
					<input class="form-control" type="text" name="pre_chat_form[label]" value="<?php echo esc_attr( $pre_chat_form->label ); ?>" />
				</td>
			</tr>
			<tr>
				<th><?php echo esc_attr__( 'Only for Offline Mode', 'wschat' ); ?></th>
				<td>
					<div class="wschat-wrapper d-flex gap-3">
						<label class="switch">
							<input type="checkbox" name="pre_chat_form[mode]" <?php echo PreChatForm::MODE_OFFLINE === $pre_chat_form->mode ? 'checked' : ''; ?> />
							<span class="slider round"></span>
						</label>
						<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
						<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
						<p class="description"><?php echo esc_attr__( 'Show this pre-chat form only if WSChat is on offline mode', 'wschat' ); ?></p>
					</div>
				</td>
			</tr>
			<tr>

				<td colspan="2" class="px-0">

					<div class="wschat-wrapper">
						<div class="d-flex gap-3 flex-wrap">
							<div class="" style="width:420px">
								<h5 class="fw-bolder"><?php echo esc_attr__( 'Form Fields', 'wschat' ); ?></h5>
								<div class="card overflow-hidden pre_chat_form_add_field p-0 m-0 mb-2" style="max-width: initial">

									<div class="card-body p-0">
										<form id="frm_pre_chat_form_add_field">
											<div class="alert alert-success hidden">
											</div>
											<div class="px-3 mb-3">
												<label for="field_type" class=" col-form-label"><?php echo esc_attr__( 'Field Type', 'wschat' ); ?></label>
												<div class="">
													<select class="form-select" id="pre_chat_form_field_type" name="pre_chat_form_field_type">
														<option value="text"><?php echo esc_attr__( 'Text', 'wschat' ); ?></option>
														<option value="textarea"><?php echo esc_attr__( 'Textarea', 'wschat' ); ?></option>
														<option value="email"><?php echo esc_attr__( 'E-mail', 'wschat' ); ?></option>
														<option value="number"><?php echo esc_attr__( 'Number', 'wschat' ); ?></option>
														<option value="checkbox"><?php echo esc_attr__( 'Checkbox', 'wschat' ); ?></option>
														<option value="radio"><?php echo esc_attr__( 'Radio Button', 'wschat' ); ?></option>
														<option value="select"><?php echo esc_attr__( 'Dropdown', 'wschat' ); ?></option>
													</select>
												</div>
											</div>
											<div class="px-3 mb-3">
												<label for="pre_chat_form_field_name" class=" col-form-label"><?php echo esc_attr__( 'Field Name', 'wschat' ); ?></label>
												<div class="">
													<input type="text" class="form-control" name="pre_chat_form_field_name">
												</div>
											</div>
											<div class="px-3 mb-3 field_options d-none">
												<label for="pre_chat_form_field_options" class="col-form-label"><?php echo esc_attr__( 'Values', 'wschat' ); ?></label>
												<div class="col-sm-12 ">
													<div class="input-group mb-1">
														<input type="text" name="pre_chat_from_field_option[]" class="pre_chat_from_field_option form-control form-control-sm">
													</div>
													<button type="button" class="btn btn-primary btn-sm add_option_to_field"><?php echo esc_attr__( 'Add value', 'wschat' ); ?></button>
												</div>
											</div>
											<div class=" px-3 d-flex gap-3 align-items-center mb-3">
												<label for="pre_chat_form_field_mandatory" class="col-form-label"><?php echo esc_attr__( 'Mandatory for End User', 'wschat' ); ?></label>
												<div class="">
													<label class="switch">
														<input type="checkbox" checked name="pre_chat_form_field_mandatory" />
														<span class="slider round"></span>
													</label>
													<span class="d-none switch-label-on"><?php echo esc_attr__( 'Yes', 'wschat' ); ?></span>
													<span class="d-none switch-label-off"><?php echo esc_attr__( 'No', 'wschat' ); ?></span>
												</div>
											</div>
											<div class="px-3 py-2 bg-light">
												<div class="row ">
													<div class="col-sm-6">
														<button type="button" class="w-100 btn bg-white border pre_chat_frm_field_cancel"><?php echo esc_attr__( 'Cancel', 'wschat' ); ?></button>
													</div>
													<div class="col-sm-6">
														<button type="button" class="w-100 btn btn-primary pre_chat_form_add_field_submit"><?php echo esc_attr__( 'Add Field', 'wschat' ); ?></button>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class=" flex-fill">
								<h5 class="fw-bolder"><?php echo esc_attr__( 'Form Preview', 'wschat' ); ?></h5>
								<div id="prechat_form_fields_container">
								</div>
							</div>
						</div>


					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php
	/**
	 * Fire an action hook for admin settings page end
	 *
	 * @since 2.0.0
	 */
	do_action( 'wschat_admin_settings_page_end' );
	?>

	<p class="submit">
		<button type="submit" name="submit" id="submit" class="btn btn-sm btn-primary" value=""><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button>
	</p>

</form>
<!-- <script>
	(function() {
		jQuery('#pusher_verify_button').click(function(e) {
			let pusher_data = {
				app_id: jQuery('#pusher_app_id').val(),
				app_key: jQuery('#pusher_app_key').val(),
				secret_key: jQuery('#pusher_secret_key').val(),
				cluster_key: jQuery('#pusher_cluster_key').val(),
			}
		   
			jQuery.ajax({
				type: 'post',
				url: ajaxurl,
				data: {
					action: 'general_pusher_verify',
					nonce: jQuery('input[name=wschat_settings_nonce]').val(),
					p_data: pusher_data,
				},
				success: function(data){
					if(data){
						alert('Credentials successfully verified and saved.');

					}else{
						alert('Unable to connect to the pusher. Please validate the credentials and try again');
					}
				}
			});
		});

	})();
</script> -->
