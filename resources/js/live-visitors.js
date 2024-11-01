import {AdminApiConnector} from './admin_api_connector';
const bootstrap = require('bootstrap');

jQuery(function () {
	const modal = new bootstrap.Modal(document.getElementById('live-visitor-start-chat-modal'));
	const frm = jQuery('#live-visitor-start-chat-form');

	jQuery('.start-chat').on('click', function (e) {
		e.preventDefault();
		jQuery('.submit-btn').removeClass('disabled');
		frm.find('input[name=conversation_id]').val(this.getAttribute('data-conversation-id'));
		modal.show();
	});

	jQuery('#live-visitor-start-chat-modal').on('shown.bs.modal', () => {
		jQuery('.modal-backdrop').appendTo('.wschat-wrapper');
		frm.find('textarea').focus();
	});

	frm.on('submit', function (e) {
		e.preventDefault();
		jQuery('.submit-btn').addClass('disabled');
		const frmData = {
			action: AdminApiConnector.prototype.ACTION_SEND_MESSAGE,
			conversation_id: frm.find('input[name=conversation_id]').val(),
			live_visitor_msg : true,
            wschat_ajax_nonce: wschat_ajax_obj.nonce,
			'content[text]': frm.find('textarea').val()
		};

        jQuery.ajax({
            method: 'post',
            data: frmData,
            url: wschat_ajax_obj.ajax_url,
            success: () => {
                frm.find('textarea').val('');
                modal.hide();
            }
        });
	});
});

