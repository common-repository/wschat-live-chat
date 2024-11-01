import { EVENTS } from '../events';

/**
 * Feedback plugin
 */
export class FeedbackPlugin {

	constructor(chat) {
		this.chat = chat;
	}

	init() {
		if (this.chat.options.is_admin) {
			this.chat.on(EVENTS.WSCHAT_ON_RENDER_USER_META, info => this.update_user_meta(info));
			return true;
		}

		const picker = jQuery(BTN_TEMPLATE);

		this.chat.$el.find('.attachment-wrapper .attachment-list > div').append(picker);

		picker.click(() => this.show_picker());

        this.chat.$el.on('click', '#wschat_email_feedback_form_close', e => this.remove_form(e))

		this.chat.$el.on('click', '.feedback-action', e => this.submit_form(e));

		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.update_ui());
	}

	update_user_meta(meta_element) {
		meta_element.find('.admin_feedback_container').remove();
		const feedback_element = jQuery(ADMIN_PREVIEWER_CONTENT);
		const feedback = this.chat.get_conversation_meta('user_feedback');

		if (feedback !== false) {
			feedback_element.find('.alert').addClass('d-none');
			feedback_element.find('.alert.'+feedback).removeClass('d-none');
		}

		meta_element.find('.elex-ws-chat-customer-info-content').append(feedback_element);
	}

	update_ui() {
		const feedback = this.chat.get_conversation_meta('user_feedback');

		if (feedback !== false) {
			jQuery('#wschat_feedback_picker').find('i').text(feedback);
		}
	}

	submit_form(e) {
		const btn = jQuery(e.target);
		const data = {
			action: this.ACTION_FEEDBACK,
			feedback: btn.data('action'),
		};

		this.chat.update_conversation_meta('user_feedback', data.feedback);
		this.update_ui();

		const frm = btn.parents('.wschat_feedback_form_popup');

		btn.prop('disabled', true);

		jQuery.post(this.chat.options.api.endpoint, data, (res) => {
			frm.html(res.data.msg);

			setTimeout(() => {
				frm.remove();
			}, 3000);
		}).done(() => {
			btn.prop('disabled', false).append('sd');
		});
	}

	remove_form(e) {
    	e && e.preventDefault();
		this.chat.$el.find('.wschat_feedback_form_popup').remove();
	}

	show_picker() {
		const email_template = jQuery(CONFIRM_EMAIL_TEMPLATE);

		this.remove_form();
		this.chat.$el.find('.chat-box-footer').append(email_template);
	}

}

FeedbackPlugin.prototype.ACTION_FEEDBACK = 'wschat_coversation_feedback';

export const BTN_TEMPLATE = `
	<button id="wschat_feedback_picker" class="icon-btn lh-1 p-1 rounded-circle elex-ws-chat-widget-feedback-open-btn attachment-list-item"
			data-bs-toggle="tooltip" data-bs-placement="right" title="Give Feedback"
															   data-bs-custom-class="tooltip-primary">
		<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 23.841 20.001">
			<g id="feedback_icon" data-name="feedback icon" transform="translate(1 1)">
			<path id="Icon_feather-thumbs-down" data-name="Icon feather-thumbs-down"
												d="M5.843,8.8v2.4A1.815,1.815,0,0,0,7.672,13L10.11,7.6V1H3.233A1.214,1.214,0,0,0,2.014,2.02l-.841,5.4a1.187,1.187,0,0,0,.285.967,1.228,1.228,0,0,0,.934.413ZM10.11,1h1.628a1.4,1.4,0,0,1,1.421,1.2V6.4a1.4,1.4,0,0,1-1.421,1.2H10.11"
												transform="translate(8.683 -1)" fill="none" stroke="#fff" stroke-linecap="round"
																										  stroke-linejoin="round" stroke-width="2" />
			<path id="Icon_feather-thumbs-down-2" data-name="Icon feather-thumbs-down"
												  d="M8.475,5.2V2.8A1.815,1.815,0,0,0,6.646,1L4.207,6.4V13h6.877A1.214,1.214,0,0,0,12.3,11.98l.841-5.4a1.187,1.187,0,0,0-.285-.967,1.228,1.228,0,0,0-.934-.413ZM4.207,13H2.579a1.4,1.4,0,0,1-1.421-1.2V7.6A1.4,1.4,0,0,1,2.579,6.4H4.207"
												  transform="translate(-1.159 5)" fill="none" stroke="#fff" stroke-linecap="round"
																											stroke-linejoin="round" stroke-width="2" />
			</g>
		</svg>
	</button>
`;

export const CONFIRM_EMAIL_TEMPLATE = `
    <div class=" wschat_feedback_form_popup px-3 py-2 position-absolute w-100 h-auto bottom-0 bg-light">
    	<div class="row position-relative mb-3">
            <label for="feedback_field_email" class="col fw-bolder form-label mb-0 form-label-sm text-center">Give us a your valuable feedback</label>
        	<button class="btn btn-sm col-auto position-absolute end-0 top-50 me-2 translate-middle" id="wschat_email_feedback_form_close" type="button">
				<svg xmlns="http://www.w3.org/2000/svg" width="10.5" height="10.5" viewBox="0 0 10.5 10.5">
					<path id="Icon_material-close" data-name="Icon material-close" d="M14.25,4.807,13.193,3.75,9,7.943,4.807,3.75,3.75,4.807,7.943,9,3.75,13.193,4.807,14.25,9,10.057l4.193,4.193,1.057-1.057L10.057,9Z" transform="translate(-3.75 -3.75)"/>
		  		</svg>
		  	</button>
    	</div>
    	<div class="row">
            <div class="col d-flex justify-content-center gap-3 mt-2">
                <button class="btn btn-sm px-4 btn-outline-secondary feedback-action " data-action="thumb_down" type="button"><i class="material-icons align-bottom">thumb_down</i> Bad</button>
                <button class="btn btn-sm  px-4 wschat-bg-primary wschat-text-primary feedback-action " data-action="thumb_up" type="button"><i class="material-icons align-bottom">thumb_up</i> Good</button>
            </div>
        </div>
    </div>
`;

const ADMIN_PREVIEWER_CONTENT = `
	<div class="row mt-2 admin_feedback_container">
		<div class=" text-center ">
			<div class="alert alert-light">No feedback yet</div>
			<div class="alert alert-success thumb_up d-none">User liked the chat</div>
			<div class="alert alert-danger thumb_down d-none">User disliked the chat</div>
		</div>
	</div>
`
