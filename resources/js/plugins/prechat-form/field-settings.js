import { useState, render } from '@wordpress/element';
import Swal from 'sweetalert2';
import {ImArrowDown2 ,ImArrowUp2} from 'react-icons/im';

export const PrechatFormFieldList = (props) => {
	const [fields, setFields] = useState(props.fields.map(f => {
		f.rand = Math.random();
		return f;
	}));

	const rearrange = (index, dir) => {
		if (fields[index + dir] === undefined) {
			return;
		}
		const tmp = fields[index];
		tmp.rand = Math.random();
		fields[index] = fields[index + dir];
		fields[index + dir] = tmp;

		setFields([...fields]);

		const data = {
			wschat_settings_nonce: jQuery('input[name=wschat_settings_nonce]').val(),
			action: 'wschat_pre_chat_frm_rearrange_fields',
			index: index,
			dir: dir
		};
		jQuery.post(ajaxurl, data);
	}

	return (
		<ul className="list-group " id="prechat-form-fields">
			{fields.map((field, i) => <PrechatFormField field={field} key={field.rand} index={i} fields={fields} setFields={setFields}  rearrange={(dir) => rearrange(i, dir)} />)}
		</ul>
	);
}

export const PrechatFormField = (props) => {
	const [field, setField] = useState(props.field);
	const rearrange = props.rearrange;

	const toggleStatus = async () => {
		const data = {
			wschat_settings_nonce: jQuery('input[name=wschat_settings_nonce]').val(),
			action: 'wschat_pre_chat_frm_toggle_field_status',
			name: field.name,
		};
		var new_action_message;
		if (field.status == true) {
			new_action_message = 'You want to disable ' + field.name + ' field from the pre-chat form? Disabling, this field will not be visible in the pre-chat form on the widget.';
		}
		else {
			new_action_message = 'You want to enable ' + field.name + ' field to the pre-chat form?';
		}

		const result = await Swal.fire({
			title: 'Are you sure?',
			text: new_action_message,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			// confirmButtonText: 'Yes, '+ field.status ? 'disable' : 'enable' +' it!'
		});

		if (!result.isConfirmed) {
			return;
		}

		field.status = !field.status;
		setField({ ...field });

		var alert = jQuery('.pre_chat_form_add_field .alert').clone();
		var item = jQuery(this).parents('li');

		jQuery.post(wschat_ajax_obj.ajax_url, data, function(res) {
			alert.text(res.data.message).removeClass('hidden alert-danger').addClass('alert-success');
			setTimeout(() => alert.addClass('hidden'), 3000)
			item.append(alert);
		}).fail(function(f) {
			alert.text(f.responseJSON.data.message).removeClass('hidden alert-success').addClass('alert-danger');
			setTimeout(() => alert.addClass('hidden'), 3000)
			item.append(alert);
		});
	}

	return (<li className='list-group-item elex-ws-chat-status-list'>
		<div className='row align-items-center' >
			<div className=' col-4' >
                <div className='fs-6 fw-bold'>
                    {field.name}
                </div>
			</div>
			<div className='fs-6 fw-bold col-4 '>{field.type[0].toUpperCase() + field.type.substring(1)}</div>
			<div className="text-end col-4">
				<label className="switch ms-2">
					<input type="checkbox" onChange={() => toggleStatus(field)} checked={field.status} />
					<span className="slider round"></span>
				</label>
				<button title="Move the field down" onClick={() => rearrange(1)} className="btn btn-sm btn-default pre_chat_form_field_move_down" style={{fontSize:"100px!important"}} type="button" data-field-name={field.name}>
					<ImArrowDown2/>
				</button>
				<button title="Move the field up" onClick={() => rearrange(-1)} className="btn btn-sm btn-default pre_chat_form_field_move_up" style={{fontSize:"100px!important"}} type="button" data-field-name={field.name}>
					<ImArrowUp2/>
				</button>
				<div className="pre_chat_form_field_delete_space d-inline-block">
				{field.deletable !== false ?
					<button title="Delete" onClick={async (e) => {
						e.preventDefault();
						var data = {
							wschat_settings_nonce: jQuery('input[name=wschat_settings_nonce]').val(),
							action: 'wschat_pre_chat_frm_delete_field',
							name: field.name,
						};
						const result = await Swal.fire({
							title: 'Are you sure?',
							text: 'You want to delete ' + field.name + ' field from the pre-chat form?',
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
						});

						props.setFields( [...props.fields.filter((tmpField,i) => {
							return i!== props.index;
						})]) 

						if (!result.isConfirmed) {
							return;
						}

						var alert = jQuery('.pre_chat_form_add_field .alert').clone();
						var item = jQuery(this).parents('li');

						jQuery.post(wschat_ajax_obj.ajax_url, data, function(res) {
							alert.text(res.data.message).removeClass('hidden alert-danger').addClass('alert-success');
							setTimeout(() => item.remove(), 3000)
							item.html(alert);
						}).fail(function(f) {
							alert.text(f.responseJSON.data.message).removeClass('hidden alert-success').addClass('alert-danger');
							setTimeout(() => alert.addClass('hidden'), 3000)
							item.append(alert);
						});

					}} className="btn btn-sm btn-default pre_chat_form_field_delete" type="button" data-field-name={field.name}>
						<svg xmlns="http://www.w3.org/2000/svg" width="13.5" height="14.833" viewBox="0 0 13.5 14.833">
  <g id="Icon_feather-trash-2" data-name="Icon feather-trash-2" transform="translate(0.75 0.75)">
    <path id="Path_35" data-name="Path 35" d="M2,4H14" transform="translate(-2 -1.333)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
    <path id="Path_36" data-name="Path 36" d="M12.667,4v9.333a1.333,1.333,0,0,1-1.333,1.333H4.667a1.333,1.333,0,0,1-1.333-1.333V4m2,0V2.667A1.333,1.333,0,0,1,6.667,1.333H9.333a1.333,1.333,0,0,1,1.333,1.333V4" transform="translate(-2 -1.333)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
    <path id="Path_37" data-name="Path 37" d="M6.667,7.333v4" transform="translate(-2 -1.333)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
    <path id="Path_38" data-name="Path 38" d="M9.333,7.333v4" transform="translate(-2 -1.333)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
  </g>
</svg>
					</button>
				: ''}
				</div>
			</div>
		</div>
	</li>
	)

}
jQuery(function () {
	const el = document.getElementById('prechat_form_fields_container');
	el && render(<PrechatFormFieldList fields={wschat_ajax_obj.settings.prechatform.fields} />, el);
});

(function() {
	var protocol = jQuery('[name=communication_protocol]');
	var pusher_settings = jQuery('.pusher_settings');

	function togglePusher(value) {
		if (value === 'pusher') {
			pusher_settings.removeClass('hidden');
		} else {
			pusher_settings.addClass('hidden');
		}
	}
	protocol.change(function() {
		var value = jQuery('[name=communication_protocol]:checked').val();
		togglePusher(value)
	});


	// for
	jQuery(function() {
		jQuery('input[name=enable_tags]').on('change', function() {
			const colorContainer = jQuery('.wschat-default-tag-color-sec');

			if (this.checked === false) {
				colorContainer.addClass('d-none');
				jQuery('.show-tags').addClass('d-none');
			} else {
				colorContainer.removeClass('d-none');
				jQuery('.show-tags').removeClass('d-none');
			}
		}).trigger('change');
	});

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

	protocol.trigger('change')

	jQuery('#pre_chat_form_field_type').change(function() {
		if (['text', 'textarea', 'email', 'number'].indexOf(this.value) >= 0) {
			jQuery('.field_options').addClass('d-none')
		} else {
			jQuery('.field_options').removeClass('d-none');
		}
	});

	// Add option field
	jQuery('.add_option_to_field').click(function() {
		var field = jQuery('.pre_chat_from_field_option').eq(0).clone();
		field.val('');
		
		var deleteButton = jQuery('<button type="button" class="btn btn-outline-danger btn-sm delete_option_field"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></button>');
		deleteButton.click(function() {
			jQuery(this).parent().remove();
		});
		
		var inputGroup = jQuery('<div class="input-group mb-1"></div>');
		inputGroup.append(field);
		inputGroup.append(deleteButton);
		
		inputGroup.insertBefore(this);
	});
	
	// Delete option field
	jQuery(document).on('click', '.delete_option_field', function() {
		jQuery(this).parent().remove();
	});

	jQuery('.pre_chat_form_add_field_submit').click(function(e) {
		e.preventDefault();

		var data = {
			wschat_settings_nonce: jQuery('input[name=wschat_settings_nonce]').val(),
			action: 'wschat_pre_chat_frm_add_field',
			type: jQuery('[name=pre_chat_form_field_type]').val(),
			name: jQuery('[name=pre_chat_form_field_name]').val(),
			options: jQuery('[name="pre_chat_from_field_option[]"]').map((i, option) => option.value).get(),
			mandatory: jQuery('[name=pre_chat_form_field_mandatory]').prop('checked') ? 'yes' : 'no',
		};

		var alert = jQuery('.pre_chat_form_add_field .alert');
		jQuery.post(wschat_ajax_obj.ajax_url, data, function(res) {
			alert.text(res.data.message).removeClass('hidden alert-danger').addClass('alert-success');
			setTimeout(() => alert.addClass('hidden'), 3000)
			// reset_pre_chat_form();
			setTimeout(function() {
				window.location.reload(true);
			}, 2000);
		}).fail(function(f) {
			alert.text(f.responseJSON.data.message).removeClass('hidden alert-success').addClass('alert-danger');
			setTimeout(() => alert.addClass('hidden'), 3000)
		});
	});


	function reset_pre_chat_form() {
		jQuery('[name=pre_chat_form_field_name]').val('');
		jQuery('[name="pre_chat_from_field_option[]"]').not(':eq(0)').remove();
		jQuery('[name="pre_chat_from_field_option[]"]').eq(0).val('');
	}

	jQuery(document).on('click', '.pre_chat_frm_field_cancel', reset_pre_chat_form);
	// jQuery(document).on('click', '.pre_chat_form_field_delete', async function(e) {
		// e.preventDefault();

	// 	var data = {
	// 		wschat_settings_nonce: jQuery('input[name=wschat_settings_nonce]').val(),
	// 		action: 'wschat_pre_chat_frm_delete_field',
	// 		name: jQuery(this).data('field-name'),
	// 	};

	// 	const result = await Swal.fire({
	// 	  title: 'Are you sure?',
	// 	  text: 'You want to delete ' + data.name + ' field from the pre-chat form?',
	// 	  icon: 'warning',
	// 	  showCancelButton: true,
	// 	  confirmButtonColor: '#3085d6',
	// 	  cancelButtonColor: '#d33',
	// });

	// if (!result.isConfirmed) {
	// 	return;
	// }

	// 	var alert = jQuery('.pre_chat_form_add_field .alert').clone();
	// 	var item = jQuery(this).parents('li');

	// 	jQuery.post(wschat_ajax_obj.ajax_url, data, function(res) {
	// 		alert.text(res.data.message).removeClass('hidden alert-danger').addClass('alert-success');
	// 		setTimeout(() => item.remove(), 3000)
	// 		item.html(alert);
	// 	}).fail(function(f) {
	// 		alert.text(f.responseJSON.data.message).removeClass('hidden alert-success').addClass('alert-danger');
	// 		setTimeout(() => alert.addClass('hidden'), 3000)
	// 		item.append(alert);
	// 	});

	// });

	jQuery('.wschat-delete-role-btn').click( async function(e) {
		e.preventDefault();

		const result = await Swal.fire({
			title: 'Are you sure?',
			text: 'You want to delete Role?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
	  });

	  if (!result.isConfirmed) {
		  return;
	  }

		const data = {
			action: 'wschat_delete_role',
			role: jQuery(this).data('role'),
			_wpnonce: jQuery('input[name=_wpnonce]').val()
		};

		jQuery.post(ajaxurl, data, function(res) {
			if (res.success) {
				window.location.reload(true);
			} else {
				alert(res.data.message);
			}
		});

	});

})()
