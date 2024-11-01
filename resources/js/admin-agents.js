jQuery(function() {
	jQuery('.delete-agent').click( async function(e) {
    	e.preventDefault();

    	const result = await Swal.fire({
        	title: 'Are you sure?',
        	text: 'You want to delete Agent?',
        	icon: 'question',
        	showCancelButton: true,
        	confirmButtonColor: '#3085d6',
        	cancelButtonColor: '#d33',
  		});

  		if (!result.isConfirmed) {
      		return;
  		}

    	const data = {
        	action: 'wschat_delete_agent',
        	id: jQuery(this).data('id'),
        	_wpnonce: jQuery('input[name=delete-an-agent]').val()
    	};

    	jQuery.post(ajaxurl, data, function(res) {
        	if (res.success) {
            	window.location.reload(true);
        	} else {
            	alert(res.data.message);
        	}
    	});

	});

	jQuery('#wschat-add-new-agent-btn').click(function(e) {
		e.preventDefault();
		const frm = jQuery('#wschat-add-new-agent-frm');
		const alert = frm.find('.alert');
		alert.removeClass('alert-success alert-danger').addClass('hidden');

		jQuery.post(ajaxurl, frm.serializeArray(), function(res) {
			if (res.success) {
				alert.removeClass('hidden').addClass('alert-success').text(res.data.message);
				setTimeout(() => {
					window.location.reload();
				}, 2000);
				return;
			}

			alert.removeClass('hidden').addClass('alert-danger').text(res.data.message);
		}).fail(function(f) {
			alert.removeClass('hidden').addClass('alert-danger').text(f.responseJSON.data.message);
		});
	});

	const newUserFieldsCheckbox = document.getElementById("new-user");
    const usernameContainer = document.getElementById("usernameContainer");
    const emailContainer = document.getElementById("emailContainer");

    newUserFieldsCheckbox.addEventListener("change", function() {
        if (this.checked) {
            usernameContainer.style.display = "block";
            emailContainer.style.display = "block";
			jQuery('#existing-user-Container').hide();
			jQuery('input[name="action"]').val('wschat_add_new_agent');
        } else {
            usernameContainer.style.display = "none";
            emailContainer.style.display = "none";
			jQuery('#existing-user-Container').show();
			jQuery('input[name="action"]').val('wschat_edit_existing_agent');
        }
    });
	
	jQuery(".elex_select2").select2({
		dropdownParent: jQuery('#existing-user-Container'),
		tags: false
	});


	jQuery('select[name=role]').change(function() {

		const role = jQuery(this).val();
		if (role === 'administrator') {
			jQuery(this).parents('form').find('.capability-item').removeClass('d-none');
			return;
		}

		jQuery(this).parents('form').find('.capability-item').addClass('d-none');
		const caps = roles[role].capabilities;

		Object.keys(caps).forEach(function(cap) {
			jQuery(`input[type=checkbox][name="wschat_role_capability[${cap}]"]`).parents('.capability-item').removeClass('d-none');
		});

	}).trigger('change');

	jQuery('.wschat-edit-agent-trigger').on('click', function(e) {
		e.preventDefault();

		const agent_id = jQuery(this).data('id');
		const agent = agents.find(function(a) {
			return parseInt(a.ID) === parseInt(agent_id);
		});

		const frm = jQuery('#wschat-edit-agent-frm');

		frm.find('input[name=id]').val(agent_id);
		frm.find('input[name=name]').val(agent.display_name);
		frm.find('input[name=email]').val(agent.user_email);

		const selectRole = frm.find(`select[name=role]`);
		selectRole.find('option').prop('selected', false);

		Object.values(agent.roles).forEach(function(role) {
			selectRole.find(`option[value=${role}]`).prop('selected', true);
		});

		selectRole.trigger('change');

		jQuery('.wschat-edit-agent-popup').addClass('show');
	});

	jQuery('#wschat-edit-agent-frm').submit(function(e) {
		e.preventDefault();
		const frm = jQuery(this);
		const alert = frm.find('.alert');
		alert.removeClass('alert-success alert-danger').addClass('hidden');

		jQuery.post(ajaxurl, frm.serializeArray(), function(res) {
			if (res.success) {
				alert.removeClass('hidden').addClass('alert-success').text(res.data.message);
				setTimeout(() => {
					window.location.reload();
				}, 2000);
			} else {
				alert.removeClass('hidden').addClass('alert-danger').text(f.responseJSON.data.message);
			}
		}).fail(function(f) {
			alert.removeClass('hidden').addClass('alert-danger').text(f.responseJSON.data.message);
		});
	});
});
