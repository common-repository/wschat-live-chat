/**
 * Email transcript
 */
export class EmailTranscriptPlugin {
	/**
	 * Chat core object
	 */
	chat;

	constructor(chat){
		this.chat = chat;
	}

	init() {
		const picker = jQuery(BTN_TEMPLATE);

		this.chat.$el.find('.attachment-wrapper .attachment-list > div').append(picker);
		this.email_form = jQuery(CONFIRM_EMAIL_TEMPLATE);
		this.chat.$el.find('.chat-box-footer').append(this.email_form);

		this.toast = jQuery(SUCCESS_ALERT);
		this.chat.$el.find('.elex-ws-chat-convo-body').append(this.toast);

		picker.on('click', () => this.show_form());

        this.chat.$el.on('submit', '#wschat_email_transcript_form', e => this.submit_form(e))
        this.chat.$el.on('click', '#wschat_email_transcript_form_close', e => this.remove_form(e))
        this.email_form.find('input').on('keypress', (e) => {
        	if (e.keyCode === 13 || e.key === 'Enter') {
        		e.preventDefault();
        		this.email_form.find('#wschat_email_transcript_form').submit();
        	}
        });
	}

    submit_form(e) {
        e.preventDefault();
        const data = {
            email: jQuery('#email_transcript_field_email').val(),
            action: this.ACTION_EMAIL_TRANSCRIPT,
            conversation_id: this.chat.conversation.id
        };

        if (data.email === '') {
        	return;
        }

        const frm = jQuery(e.target);

        const btn  = frm.find('button[type=submit]');
        btn.prop('disabled', true);
        const btn_content = btn.html();
        btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`);

        jQuery.post(this.chat.options.api.endpoint, data, (res) => {
            this.toast.find('.toast-body').text(res.data.msg);
			this.toast.removeClass('d-none');
			this.toast.find('#elex-ws-chat-email-toast-msg').removeClass('hide').addClass('show');
			this.remove_form();
            setTimeout(() => {
                this.toast.addClass('d-none');
            }, 3000);
        }).fail(() => {
            btn.prop('disabled', false).html(btn_content);
        }).done(() => {
            btn.prop('disabled', false).html(btn_content);
        });
    }

    remove_form(e) {
    	e && e.preventDefault();
		this.chat.$el.find('.wschat_email_transcript_form_popup').find('input').val('');

		// On history page we dont need to hide the popup
		if (this.chat.options.transcript && this.chat.options.transcript.do_not_hide) {
			return;
		}

		this.email_form.addClass('d-none');
		this.chat.$el.find('.wschat_email_transcript_form_popup').removeClass('active');
    }

	show_form() {
		this.remove_form();
		this.email_form.addClass('active').removeClass('d-none');
		let email = this.chat.conversation.user.meta.email;
		if ( !email ||  this.chat.conversation.meta.pre_chat_form && this.chat.conversation.meta.pre_chat_form.email ) {
			email = this.chat.conversation.meta.pre_chat_form.email.value;
		}

		this.email_form.find('input').val(email).focus();
	}

}

EmailTranscriptPlugin.prototype.ACTION_EMAIL_TRANSCRIPT = 'wschat_email_transcript';

export const BTN_TEMPLATE = `
	<button id="wschat_email_transcript_picker" class="icon-btn elex-ws-chat-email-open-btn p-1"  data-bs-toggle="tooltip" data-bs-placement="right" title="Email transcript" data-bs-custom-class="tooltip-outline-primary">
		<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg " width="23.057 " height="20 "
			viewBox="0 0 23.057 20 ">
			<g id="Icon_feather-mail " data-name="Icon feather-mail "
				transform="translate(1.403 1) ">
				<path id="Path_22 " data-name="Path 22 "
					d="M3.525,3h16.2A2.153,2.153,0,0,1,21.75,5.25v13.5A2.153,2.153,0,0,1,19.725,21H3.525A2.153,2.153,0,0,1,1.5,18.75V5.25A2.153,2.153,0,0,1,3.525,3Z "
					transform="translate(-1.5 -3) " fill="none " stroke="#fff "
					stroke-linecap="round " stroke-linejoin="round "
					stroke-width="2 " />
				<path id="Path_23 " data-name="Path 23 "
					d="M21.75,4.5,11.625,12.375,1.5,4.5 "
					transform="translate(-1.5 -2.25) " fill="none " stroke="#fff "
					stroke-linecap="round " stroke-linejoin="round "
					stroke-width="2 " />
			</g>
		</svg>
	</button>
`;

export const CONFIRM_EMAIL_TEMPLATE = `
<div class="position-absolute p-2 elex-ws-chat-email wschat_email_transcript_form_popup d-none">
    <form id="wschat_email_transcript_form" class="">
		<div class=" d-flex align-items-center justify-content-between">
			<div class="xs"><b>Send this conversation through email</b></div>
			<button class="btn btn-sm py-0 elex-ws-chat-email-close-btn" id="wschat_email_transcript_form_close">
				<svg xmlns="http://www.w3.org/2000/svg" width="10.5" height="10.5"
					viewBox="0 0 10.5 10.5">
					<path id="Icon_material-close" data-name="Icon material-close"
						d="M14.25,4.807,13.193,3.75,9,7.943,4.807,3.75,3.75,4.807,7.943,9,3.75,13.193,4.807,14.25,9,10.057l4.193,4.193,1.057-1.057L10.057,9Z"
						transform="translate(-3.75 -3.75)"></path>
				</svg>
			</button>
		</div>

		<div class="d-flex d-flex  gap-1 align-items-end">
			<div class="flex-fill">
				<label class="xs">Email Id</label>
				<input id="email_transcript_field_email" type="text" class="form-control " placeholder="name@example.com">
				</div>

					<!-- send button -->
					<button class="btn btn-sm  wschat-bg-primary lh-1 p-2 rounded-circle"
						type="submit"
						id="elex-ws-chat-email-toast-msg-btn">
						<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="21" height="21"
							viewBox="0 0 22.829 22.445">
							<g id="Icon_feather-send" data-name="Icon feather-send"
								transform="translate(1.5 2.121)">
								<path id="Path_16" data-name="Path 16" d="M21.564,2,11,12.353"
									transform="translate(-2.357 -2)" fill="none" stroke="#fff"
									stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
								<path id="Path_17" data-name="Path 17"
									d="M21.208,2,14.485,20.824l-3.842-8.471L2,8.588Z"
									transform="translate(-2 -2)" fill="none" stroke="#fff"
									stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
							</g>
						</svg>
					</button>
			</div>
        </form>
	</div>

					`;
const SUCCESS_ALERT = `
						<div class="position-absolute bottom-0 start-50 translate-middle-x p-2 d-none" style="z-index: 119">
							<div id="elex-ws-chat-email-toast-msg"
								class="toast show align-items-center text-white bg-primary w-auto" role="alert"
								aria-live="assertive" aria-atomic="true">
								<div class="toast-body text-center text-nowrap">
									Email Sent Succesfully
								</div>
							</div>
						</div>
`

