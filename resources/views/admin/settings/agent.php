<form method="post">
	<div class="pl-0">
		<?php wp_nonce_field( 'wschat_save_settings', 'wschat_settings_nonce' ); ?>
		
			<div class="row my-3">
				<div class="col-xl-2 col-lg-3 d-flex justify-content-between">
					<h6><?php echo esc_attr__( 'Agent Setup', 'wschat' ); ?></h6>
					<div class="me-3">
						<label class="switch">
							<input type="checkbox" name="agent_setup" <?php echo $wschat_options['agent_setup'] ? 'checked' : ''; ?> />
							<span class="slider round"></span>
						</label>
						<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
						<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
					</div>
				</div>
				<div class="col">
					<p class="description"><?php echo esc_attr__( 'Enable to setup agent(s) for your business', 'wschat' ); ?></p>
				</div>
			</div>
		<div class="wschat_agent_roles_container d-none">
			<div class="d-flex justify-content-between align-items-center mb-2">
				<h6 class="fw-bold"><?php echo esc_html__( 'Roles & Capabilities', 'wschat' ); ?></h6>
				<button class="btn btn-sm btn-primary" id="wschat_add_new_role_popup" type="button" onclick="jQuery('.wschat-add-new-role-popup').toggleClass('show')"><?php echo esc_html__( 'Add New Role', 'wschat' ); ?></button>
			</div>
			<div>
				<?php foreach ( $wschat_roles as $role_slug => $wschat_role ) { ?>
					<div class="d-flex align-middle align-items-center mb-2 p-2 border border-1 border-dark rounded bg-white">
						<p class="m-0 fs-6"><?php echo esc_html__( $wschat_role['name'], 'wschat' ); ?></p>
						<div class="flex-fill">
							<ul class="list-unstyles list-inline m-0">
								<?php foreach ( array_keys( $wschat_role['capabilities'] ) as $capability ) { ?>
									<li class="list-inline-item p-1 m-1"><span class="p-2 rounded bg-black bg-opacity-10 text-dark fs-italic"><?php echo esc_html( $capability ); ?></span></li>
								<?php } ?>
							</ul>
						</div>
						<div class="d-flex gap-2">
							<?php if ( 'Administrator' !== $wschat_role['name'] ) { ?>
								<button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 wschat-edit-role-btn" data-role="<?php echo esc_attr( $role_slug ); ?>">
									<i class="material-icons ">edit</i> <?php echo esc_html__( 'Edit', 'wschat' ); ?>
								</button>
								<button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1  wschat-delete-role-btn" data-role="<?php echo esc_attr( $role_slug ); ?>">
									<i class="material-icons ">delete</i><?php echo esc_html__( 'Delete', 'wschat' ); ?>
								</button>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<p class="submit "><button type="submit" name="submit" id="submit" class="button button-primary"><?php echo esc_attr__( 'Save Changes', 'wschat' ); ?></button></p>
	</div>
</form>
<div class="bg-white d-fixed h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-edit-role-popup">
	<div class="d-flex mb-3">
		<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-edit-role-popup').toggleClass('show')"></button>
		<h5 class="flex-fill text-center"><?php echo esc_html__( 'Edit Role', 'wschat' ); ?></h5>
	</div>
	<form id="wschat-edit-role-frm">
		<div class="alert hidden" role="alert"></div>
		<input type="hidden" value="wschat_edit_role" name="action">
		<input type="hidden" value="" name="slug">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<div class="mb-3">
			<label for="RoleName" class="form-label"><?php echo esc_html__( 'Edit Role', 'wschat' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="name" id="RoleName" placeholder="Enter Role Name">
		</div>
		<label for="capabilities" class="form-label"><?php echo esc_html__( 'Capabilities', 'wschat' ); ?></label>
		<?php foreach ( $wschat_capabilities as $capability ) { ?>
			<div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-1">
				<label for="capabilities" class="form-label m-0"><?php echo esc_html( $capability ); ?></label>
				<label class="switch">
					<input type="checkbox" onchange="" class="wschat-role-capability" name="wschat_role_capability[<?php echo esc_html( $capability ); ?>]" />
					<span class="slider round"></span>
				</label>
				<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
				<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
			</div>
		<?php } ?>
		<div class="text-right">
			<button class="btn btn-primary btn-sm float-end" type="submit" id="wschat-edit-role-btn"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
		</div>
	</form>
</div>
<div class="bg-white d-flex flex-column overflow-auto h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-add-new-role-popup">
	<div class="d-flex mb-3">
		<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-add-new-role-popup').toggleClass('show')"></button>
		<h5 class="flex-fill text-center"><?php echo esc_html__( 'Add New Role', 'wschat' ); ?></h5>
	</div>
	<form id="wschat-add-new-role-frm" class="d-flex flex-column flex-fill position-relative pb-5">
		<div class="alert hidden" role="alert"></div>
		<input type="hidden" value="wschat_add_new_role" name="action">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<div class="mb-3">
			<label for="RoleName" class="form-label"><?php echo esc_html__( 'Add New Role', 'wschat' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="name" id="RoleName" placeholder="Enter Role Name">
		</div>
		<label for="capabilities" class="form-label"><?php echo esc_html__( 'Capabilities', 'wschat' ); ?></label>
		<?php foreach ( $wschat_capabilities as $capability ) { ?>
			<div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-2 rounded mb-1">
				<label for="capabilities" class="form-label m-0"><?php echo esc_html( $capability ); ?></label>
				<label class="switch">
					<input type="checkbox" onchange="" class="wschat-role-capability" name="wschat_role_capability[<?php echo esc_html( $capability ); ?>]" />
					<span class="slider round"></span>
				</label>
				<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
				<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
			</div>
		<?php } ?>
		<div class="position-absolute w-100 bottom-0 start-0">
			<button class="btn btn-primary w-100 btn-sm float-end" type="button" id="wschat-add-new-role-btn"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
		</div>
	</form>
</div>
<script>
	jQuery(function() {
		const roles = <?php echo wp_json_encode( $wschat_roles ); ?>;
		jQuery('.wschat-edit-role-btn').on('click', function(e) {
			const role_slug = this.getAttribute('data-role');
			const role = roles[role_slug];
			const popup = jQuery('.wschat-edit-role-popup').addClass('show');

			popup.find('input[name=slug]').val(role_slug);
			popup.find('input[name=name]').val(role.name);
			popup.find('input.wschat-role-capability').prop('checked', false);

			Object.keys(role.capabilities).forEach(cap => {
				popup.find('input[name="wschat_role_capability[' + cap + ']"]').prop('checked', true);
			});
		});

		jQuery('#wschat-edit-role-frm').on('submit', function(e) {
			e.preventDefault();
			const frm = jQuery(this);
			const data = frm.serializeArray();
			const alert = frm.find('.alert');

			alert.removeClass('alert-success alert-danger').addClass('hidden');

			jQuery.post(ajaxurl, data, function(res) {
				alert.removeClass('hidden').addClass('alert-success').text(res.data.message);
				setTimeout(() => location.reload(), 2000);
			}).fail(function(f) {
				alert.removeClass('hidden').addClass('alert-danger').text(f.responseJSON.data.message);
			});
		});

		jQuery('#wschat-add-new-role-btn').on('click', function(e) {
			e.preventDefault();
			const frm = jQuery('#wschat-add-new-role-frm');
			const data = frm.serializeArray();
			const alert = frm.find('.alert');

			alert.removeClass('alert-success alert-danger').addClass('hidden');

			jQuery.post(ajaxurl, data, function(res) {
				alert.removeClass('hidden').addClass('alert-success').text(res.data.message);
				setTimeout(() => location.reload(), 2000);
			}).fail(function(f) {
				alert.removeClass('hidden').addClass('alert-danger').text(f.responseJSON.data.message);
			});
		});

		jQuery('input[name=agent_setup]').on('change', function() {
			const container = jQuery('.wschat_agent_roles_container');

			if (this.checked === false) {
				container.addClass('d-none');
			} else {
				container.removeClass('d-none');
			}
		}).trigger('change');
	});
</script>
