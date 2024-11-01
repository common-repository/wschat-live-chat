<form method="post">
	<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>

	<div class=" mt-3">
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Widget Online Text', 'wschat' ); ?></label>
					<input class="form-control form-control-sm " type="text" name="header_online_text" value="<?php echo esc_attr( $wschat_options['header_online_text'] ); ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Widget Offline Text', 'wschat' ); ?></label>
					<input class="form-control form-control-sm " class="form-control form-control-sm " type="text" name="header_offline_text" value="<?php echo esc_attr( $wschat_options['header_offline_text'] ); ?>" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class=" col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Auto Reply Message', 'wschat' ); ?></label>
					<div class="row ">
						<div class="col-lg-4 col-md-6 col-sm-8 col-12">
							<textarea class=" form-control form-control-sm " rows="1" name="offline_auto_reply_text"><?php echo esc_attr( $wschat_options['offline_auto_reply_text'] ); ?></textarea>
						</div>
						<div class="col">
						<p class="description">
							<?php echo esc_attr__( 'The auto reply message while the widget is offline', 'wschat' ); ?>
						</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Header Text', 'wschat' ); ?></label>
					<input class="form-control form-control-sm " class="form-control form-control-sm " type="text" name="header_text" value="<?php echo esc_attr( $wschat_options['header_text'] ); ?>" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Alert Tone', 'wschat' ); ?></label>
					<select class=" form-select form-select-sm min-w-100" name="alert_tone">
						<?php foreach ( $tones as $tone ) : ?>
							<option value="<?php echo esc_attr( $tone['basename'] ); ?>" <?php echo $tone['filename'] === $wschat_options['alert_tone'] ? 'selected' : ''; ?>>
								<?php echo esc_attr__( $tone['filename'], 'wschat' ); ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Widget Font', 'wschat' ); ?></label>
					<select class="form-select form-select-sm min-w-100" name="font_family">
						<option value="" <?php echo esc_attr( '' === $wschat_options['font_family'] ? 'selected' : '' ); ?> ><?php echo esc_attr__( 'auto', 'wschat' ); ?></option>
						<?php foreach ( $fonts as $font ) : ?>
							<option value="<?php echo esc_attr( $font ); ?>" <?php echo $font === $wschat_options['font_family'] ? 'selected' : ''; ?>>
								<?php echo esc_attr( $font ); ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

	</div>

	<div class="">

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Primary Background Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75 wschat-color-text-input" id="ws-chat-primary-bg" type="text" name="colors[--wschat-bg-primary]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-bg-primary'] ); ?>" />
						<input type="color" class="fs-5 border-0 form-control form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-bg-primary]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-bg-primary'] ); ?>" title="Choose your color">
					</div>

				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Primary Text Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75 wschat-color-text-input" type="text" name="colors[--wschat-text-primary]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-text-primary'] ); ?>" />
						<input type="color" class="fs-5 border-0 form-control form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-text-primary]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-text-primary'] ); ?>" title="Choose your color">
					</div>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Secondary Background Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75 wschat-color-text-input" type="text" name="colors[--wschat-bg-secondary]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-bg-secondary'] ); ?>" />
						<input type="color" class="fs-5 border-0 form-control form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-bg-secondary]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-bg-secondary'] ); ?>" title="Choose your color">
					</div>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Secondary Text Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75 wschat-color-text-input" type="text" name="colors[--wschat-text-secondary]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-text-secondary'] ); ?>" />
						<input type="color" class="fs-5 border-0 form-control  form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-text-secondary]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-text-secondary'] ); ?>" title="Choose your color">
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Icon Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75  wschat-color-text-input" type="text" name="colors[--wschat-icon-color]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-icon-color'] ); ?>" />

						<input type="color" class="fs-5 border-0 form-control form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-icon-color]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-icon-color'] ); ?>" title="Choose your color">

					</div>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4 col-md-6 col-sm-8 col-12">
				<div class="mb-3">
					<label class="fw-bold"><?php echo esc_attr__( 'Info Text Color', 'wschat' ); ?></label>
					<div class="d-flex gap-2 wschat-color-container">
						<input class="form-control form-control-sm w-75 wschat-color-text-input" type="text" name="colors[--wschat-text-gray]" value="<?php echo esc_attr( $wschat_options['colors']['--wschat-text-gray'] ); ?>" />
						<input type="color" class="fs-5 border-0 form-control form-control-color p-0 h-100 wschat-color-input" name="colors[--wschat-text-gray]" value="#<?php echo esc_attr( $wschat_options['colors']['--wschat-text-gray'] ); ?>" title="Choose your color">

					</div>

				</div>
			</div>
		</div>

	</div>


	<?php
	/**
	 * Fire an action hook for admin settings page end
	 *
	 * @since 2.0.0
	 */
	do_action( 'wschat_admin_settings_page_end' );
	?>

	<p class="submit"><button type="submit" name="submit" id="submit" class="btn btn-sm btn-primary" value=""><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
</form>
<script>
	(function() {
		jQuery('.wschat-color-text-input').keyup(function(){
			$colorCode= ('#')+(jQuery(this).val());
			jQuery(this).parents('.wschat-color-container').find('.wschat-color-input').val($colorCode).trigger('change');
		})
		jQuery('.wschat-color-input').mouseleave(function(){
			jQuery(this).parents('.wschat-color-container').find('.wschat-color-text-input').val(jQuery(this).val().replace('#', '')).trigger('change');

		})
		jQuery('.switch input[type=checkbox]').on('change', function() {
			var parent = jQuery(this).parent().parent();
			if (this.checked) {
				parent.find('.switch-label-on').removeClass('d-none');
				parent.find('.switch-label-off').addClass('d-none');
			} else {
				parent.find('.switch-label-on').addClass('d-none');
				parent.find('.switch-label-off').removeClass('d-none');
			}
		}).trigger('change');
	})()
</script>
