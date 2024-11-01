import { EVENTS } from '../../events';
import {
    FieldText,
    FieldTextarea,
	FieldEmail,
	FieldNumber,
    FieldCheckbox,
    FieldRadio,
    FieldSelect
} from './fields';


/**
 * Chat alert notification plugin
 *
 * You can customize chat ringtone using chat settings like below
 *
 * `options: {
 *      ...
 *      prechatform: {
 *      }
 *  }`
 */
export class PreChatForm {

    constructor(chat){
        this.chat = chat;
    }

    init() {

        if (!this.chat.options.prechatform ) {
        	return;
		}

        this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.render_form());
        this.chat.on(EVENTS.WSCHAT_ON_START_FAILURE, () => this.render_form());
        this.chat.on(EVENTS.WSCHAT_ON_RESIZE_WIDGET, () => this.resize_widget());

		this.toast = jQuery(SUCCESS_ALERT);
		this.chat.$el.find('.elex-ws-chat-convo-body').append(this.toast);

        this.chat.$el.find('.pre-chat-form-btn-submit').click((e) => this.submit_form(e));
    }

    resize_widget(height) {
        this.chat.$el.find('.pre-chat-panel').css({
            'min-height': height
        });
    }

    render_form() {
    	if (!this.chat.conversation.prechat_form.must_show_form) {
    		return;
    	}
        const el = this.chat.$el.find('.pre-chat-panel .pre-chat-form');

        this.chat.$el.find('.pre-chat-panel').removeClass('d-none');
        this.chat.$el.find('.chat-panel').addClass('d-none');
        this.chat.$el.find('.chat-box-footer').addClass('d-none');

        this.chat.$el.find('.pre-chat-form-title').text(this.chat.options.prechatform.label);

        this.chat.options.prechatform.fields.forEach(field => {
            if (field.status) {
            	this.inputs[field.type] && el.append(new this.inputs[field.type](field).render(this.chat.conversation.user));
            }
        });

        el.append(`<input type="hidden" name="action" value="${this.chat.options.prechatform.action}" /> `)
        el.append(`<input type="hidden" name="wschat_ajax_nonce" value="${wschat_ajax_obj.nonce}" /> `)
    }

    submit_form(e) {
        e.preventDefault();
        const frm = this.chat.$el.find('.pre-chat-form');

        frm.find('.is-invalid').removeClass('is-invalid');

        const data = frm.serialize();
        jQuery(e.target).prop('disabled', true);

        jQuery.post(this.chat.options.api.endpoint, data, (res) => {
		
			if (!this.chat.conversation.user) {
				this.chat.conversation.user = {}; // Create a new user object if it doesn't exist
			}
			  
			this.chat.conversation.user.meta = this.chat.conversation.user.meta || {};
			this.chat.conversation.user.meta.email = frm.find('input[name=email]').val();
			  
         	this.chat.$el.find('.pre-chat-panel').remove();
         	this.chat.$el.find('.chat-panel').removeClass('d-none');
        	this.chat.$el.find('.chat-box-footer').removeClass('d-none');
         	this.toast.find('.toast-body').text(res.data.message);
			this.toast.removeClass('d-none');
			this.toast.find('#elex-ws-chat-email-toast-msg').removeClass('hide').addClass('show');
            setTimeout(() => {
                this.toast.addClass('d-none');
            }, 3000);
        }).fail(function (f) {
        	jQuery(e.target).prop('disabled', false);

        	if (f.status !== 422) {
        		return;
        	}
        	const response = f.responseJSON;

        	for (let name in response.data) {
				frm.find(`[name="${name}"], [name="${name}[]"]`)
					.addClass('is-invalid')
					.parent().find('.invalid-feedback')
					.text(response.data[name].required);
        	}
        });
    }
}

PreChatForm.prototype.inputs = {
    text: FieldText,
    textarea: FieldTextarea,
	email: FieldEmail,
	number: FieldNumber,
    checkbox: FieldCheckbox,
    radio: FieldRadio,
    select: FieldSelect,
}

export const PrechatFormComponent = props => {
	const data = props.conversation;

	if (!data.meta || !data.meta.pre_chat_form) {
		return '';
	}

	const pre_chat_data = Object.values(data.meta.pre_chat_form);

	return (
		<div className="row mb-1 conversation-pre-chat-form ">
			<div className="col ">
				<h3 className="pre-chat-form-title">Prechat form  data</h3>
				<table className="table pre-chat-form-table">
					<tbody>
						{pre_chat_data.map(field => {
							return (
								<tr key={field.name}>
									<th>{field.name}{field.mandatory ? `<sup class="text-danger">*</sup>` : ''}</th>
									<td>{field.value}</td>
								</tr>
							);
						})}
					</tbody>
				</table>
			</div>
		</div>
	)
}

const SUCCESS_ALERT = `
	<div class="position-absolute bottom-0 start-50 translate-middle-x p-2 d-none" style="z-index: 119">
		<div id="elex-wschat-email-toast-msg"
			class="toast show align-items-center text-white bg-primary w-auto" role="alert"
			aria-live="assertive" aria-atomic="true">
			<div class="toast-body text-center text-nowrap">
				Email Sent Succesfully
			</div>
		</div>
	</div>
`
