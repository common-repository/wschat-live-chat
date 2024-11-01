<form method="post">
	<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
	<div class="">
		<div class="row mb-3">
			<div class="col-md-4">
				<div class="d-flex gap-3 align-items-center">
					<h6 class="my-1"><?php echo esc_attr__( 'Dialogflow', 'wschat' ); ?></h6>
					<div>
						<label class="switch">
							<input type="checkbox" onchange="" name="dialogflow_enable" <?php echo $dialogflow_settings->enabled() ? 'checked' : ''; ?> />
							<span class="slider round"></span>
						</label>
						<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
						<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
						<p class="description hidden"><?php echo esc_attr__( 'Enable to send email to customer after chat ends', 'wschat' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="row mb-3 ali">
			<div class="col-md-4">
				<h6 class="mb-1"><?php echo esc_attr__( 'Google Project ID', 'wschat' ); ?></h6>
				<input class="form-control" placeholder="<?php echo esc_attr__( 'Enter project ID' ); ?>" type="text" name="dialogflow_project_id" value="<?php echo esc_attr( $dialogflow_settings->get( 'project_id' ) ); ?>" />
			</div>
			<div class="col-md-8">
				<p class="description mt-4"><?php echo esc_attr__( 'To setup Dialogflow, please refer to this ', 'wschat' ); ?> <a target="_blank" href="https://cloud.google.com/dialogflow/es/docs"> document </a></p>

			</div>
		</div>

		<div class="row mb-3">
			<div class="col-md-4">
				<h6 class="mb-1"><?php echo esc_attr__( 'Google Project JSON key file content', 'wschat' ); ?></h6>
				<textarea placeholder="<?php echo esc_attr__( 'Enter key' ); ?>" class="form-control" type="text" name="dialogflow_credentials" rows="10"><?php echo esc_attr( json_encode( $dialogflow_settings->get( 'credentials' ) ) ); ?></textarea>
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-md-4">
				<h6 class="mb-1"><?php echo esc_attr__( 'Agent Language', 'wschat' ); ?></h6>
				<select class="form-select" id="pre_chat_form_field_type" name="dialogflow_language_code">
					<?php foreach ( $language_codes as $language_code => $language ) { ?>
						<option value="<?php echo esc_html( $language_code ); ?>" <?php echo $language_code === $dialogflow_settings->language_code ? 'selected' : ''; ?>><?php echo esc_attr__( $language, 'wschat' ); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

	</div>
	<p class="submit "><button type="submit" name="submit" id="submit" class="button button-primary"><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
</form>
