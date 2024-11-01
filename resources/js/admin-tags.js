import moment from 'moment';
import Swal from 'sweetalert2';

jQuery(function () {
	jQuery('.untag').on('click', async function (e) {
		e.preventDefault();

		const result = await Swal.fire({
			title: 'Are you sure?',
			text: 'You Want to untag the message?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
	  });

	  if (!result.isConfirmed) {
		  return;
	  }

		const data = {
			action: 'wschat_admin_untag_a_message',
			_wpnonce: jQuery('#wschat-add-new-tag-frm input[name=_wpnonce]').val(),
			message_id: jQuery(this).parents('.list-group-item').data('message-id')
		};

		jQuery.post(ajaxurl, data, function (res) {
			window.location.reload();
		});
	});

	jQuery('.delete-tag').on('click', async function (e) {
		e.preventDefault();

		const result = await Swal.fire({
			title: 'Are you sure?',
			text: 'Want to delete the tag "'+ this.getAttribute('data-name') +'"?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
	  });

	  if (!result.isConfirmed) {
		  return;
	  }

		const data = {
			action: 'wschat_admin_delete_a_tag',
			id: this.getAttribute('data-id'),
			_wpnonce: jQuery('#wschat-add-new-tag-frm input[name=_wpnonce]').val(),
		};

		jQuery.post(ajaxurl, data, function (res) {
			window.location.href = res.data.redirect;
		});
	});

	jQuery('#wschat-add-new-tag, #wschat-edit-tag').on('click', function (e) {
		e.preventDefault();
		const frm = jQuery(this).parents('form');
		const frm_alert = frm.find('.alert').addClass('d-none');
		if(frm.find('#name').val() === '') {
			frm_alert.html('Please enter tag name')
				.removeClass('alert-success d-none')
				.addClass('alert-danger');
			return false;
		}
		const data = frm.serializeArray();

		jQuery.post(ajaxurl, data, function (res) {
			frm_alert
				.text(res.data.message)
				.removeClass('alert-danger d-none')
				.addClass('alert-success')
			setTimeout(function() {
				window.location.reload()
			}, 2000);

		}).fail(f => {
			if ( ! f.responseJSON) {
				return;
			}
			frm_alert
				.text(f.responseJSON.data.message)
				.removeClass('alert-success d-none')
				.addClass('alert-danger')
		});
	});

	jQuery('.tag-list-group').on('click', '.list-group-item', function () {
		jQuery(this).parent().find('.list-group-item').removeClass('active');
		jQuery(this).addClass('active');
	});

	jQuery('.change-tag').on('click', function (e) {
		const message_id = jQuery(this).parents('.list-group-item').data('message-id');
		const popup = jQuery('.wschat-change-tag-popup');

		popup.find('.message_id').val(message_id);
		popup.toggleClass('show');
	});

	jQuery('#wschat_tag_a_message').on('click', function (e) {
		const frm = jQuery(this).parents('#wschat-change-tag-frm');
		const tag_id = frm.find('.tag-list-group').find('.list-group-item.active').data('message-id');

		if (!tag_id) {
			return false;
		}
		const data = {
			message_id: frm.find('.message_id').val(),
			action: 'wschat_admin_tag_a_message',
			_wpnonce: jQuery('#wschat-add-new-tag-frm input[name=_wpnonce]').val(),
			tag_id: tag_id
		};

		jQuery.post(ajaxurl, data, function (res) {
			window.location.reload();
		});
	});
	// jQuery('#wschat-edit-tag').on('click', function (e) {
	// 	const frm = jQuery('#wschat-edit-tag-frm');

	// 	jQuery.post(ajaxurl, frm.serializeArray(), function (res) {
	// 		window.location.reload();
	// 	});
	// });

	jQuery('.wschat-tags-search ').on('keyup', 'input.search-tag', function () {
		const items = jQuery(this).parents('.wschat-tags-search').parent().parent().find('.list-group-item');
		const no_tags_were_found = jQuery('.no-tags-were-found');
		no_tags_were_found.addClass('d-none');

		if (items.length === 0) {
			return;
		}

		if (this.value.length) {
			items.addClass('d-none');
		} else {
			items.removeClass('d-none');
		}
		items.each((i, item) =>{
			if (jQuery(item).text().toLowerCase().indexOf(this.value.toLowerCase()) > -1) {
				jQuery(item).removeClass('d-none');
			}
		});

		const visible_items = items.filter(':not(.d-none)');

		if (visible_items.length === 0) {
			no_tags_were_found.removeClass('d-none');
		}
	});

	jQuery('.date_period').on('change', function () {
		if (this.value === 'custom') {
			jQuery('.custom-date-period').removeClass('d-none');
			return;
		}
		jQuery('.custom-date-period').addClass('d-none');
		const min_date = jQuery('input[name="created_at[min]"');
		const max_date = jQuery('input[name="created_at[max]"');

		if (this.value === '') {
			min_date.val('');
			max_date.val('');

			return;
		}

		min_date.val(moment().subtract(parseInt(this.value) - 1, 'days').format('YYYY-MM-DD'));
		max_date.val(moment().format('YYYY-MM-DD'));
	}).trigger('change');

	var fromDateInput = document.getElementById('from-date');
    var toDateInput = document.getElementById('to-date');

    // Disable dates after the selected "From" date in the "To" date input
    fromDateInput.addEventListener('change', function() {
        toDateInput.min = this.value;
    });

    // Disable dates before the selected "To" date in the "From" date input
    toDateInput.addEventListener('change', function() {
        fromDateInput.max = this.value;
    });
});

