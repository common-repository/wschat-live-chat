import { EVENTS } from '../events';
import {Modal} from 'bootstrap';

export class WSDeskPlugin {
	constructor(chat) {
		this.chat = chat;
	}

	init() {
		if (!this.chat.options.wsdesk) {
			return false;
		}

		if (!this.chat.options.is_admin) {
			return true;
		}

		this.chat.on(EVENTS.WSCHAT_ON_RENDER_MESSAGE, params => this.add_icon(params.message_item, params.row));

        this.chat.$el.on('click', '.message-item-action-badge-wsdesk',  (e) => {
            e.preventDefault();
            this.show_popup(jQuery(e.target));
        });

        this.chat.$el.on('click', '#wschat_wsdesk_create_ticket',  (e) => {
            e.preventDefault();
            this.submit_ticket(jQuery(e.target));
        });

	}

	add_icon(message_item, content) {
		if (content.is_agent === true) {
			return content;
		}
		const template = jQuery(icon_template).data('message-id', content.id);
		if (content.body && content.body.ticket_id) {
			template.addClass('elex-wschat-tag-active').data('ticket_id', content.body.ticket_id);
		}
		message_item.find('.message-item-action-badges').append(template);
	}

    show_popup(btn) {
        const row = {
            email: this.chat.conversation.user.meta.email || '',
            message_id: btn.data('message-id'),
            text: btn.parents('.chat-bubble').find('.message-item-text').text().trim(),
        };

        if (!btn.hasClass('message-item-action-badge-wsdesk')) {
        	btn = btn.parents('.message-item-action-badge-wsdesk');
        	row.message_id = btn.data('message-id');
        }

    	if (btn.hasClass('elex-wschat-tag-active')) {
    		const url = new URL(window.location);
    		url.searchParams.set('page', 'wsdesk_tickets');
    		url.searchParams.set('tid', btn.data('ticket_id'));
    		window.open(url);
    		return;
    	}

        jQuery(document).find('#wschat_wsdesk_create_ticket_modal').remove();
        this.chat.$el.append(render_modal(row));

        const modal_el = document.getElementById('wschat_wsdesk_create_ticket_modal');
        const wsdesk_ticket_model = new Modal(modal_el);

        // Relative parent compatibility
        // Model adds backdrop to body but we loaded BS inside the wrapper
        modal_el.addEventListener('shown.bs.modal', () => {
        	jQuery('.modal-backdrop').appendTo('.wschat-wrapper');
        });

        wsdesk_ticket_model.show();
    }

    submit_ticket() {
        const data = {
            'email': jQuery('#wschat_wsdesk_email').val(),
            'subject': jQuery('#wschat_wsdesk_subject').val(),
            'subject': jQuery('#wschat_wsdesk_subject').val(),
            'description': jQuery('#wschat_wsdesk_description').val(),
            'message_id': jQuery('#wschat_wsdesk_message_id').val(),
            'action': 'wschat_admin_create_wsdesk_ticket'
        };

        const modal = jQuery('#wschat_wsdesk_create_ticket_modal');
        const btn = jQuery('#wschat_wsdesk_create_ticket');

        btn.prop('disabled', true);
        modal.find('.alert').removeClass('alert-success').removeClass('alert-danger').text('');

		jQuery.post(this.chat.options.api.endpoint, data, res => {
            modal.find('.alert').addClass('alert-success').removeClass('alert-danger').text(res.data.message);
            this.chat.$el.find('[data-message-id='+data.message_id+'] .message-item-action-badge-wsdesk').addClass('elex-wschat-tag-active');
            setTimeout(() => {
                modal.modal('hide')
            }, 2000);
        }).fail(f => {
            modal.find('.alert').addClass('alert-danger').removeClass('alert-success').text(f.responseJSON.data.message);
            btn.prop('disable', false)
        });
    }
}

const icon_template = `
	<div class="message-item-action-badge-wsdesk">
		<span class=" badge  p-0 shadow-none elex-ws-chat-untag-open-button" >
			<a href="#" title="Raise a Ticket" class="text-dark py-1 px-2 rounded-pill"  data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-primary" >
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10"
                    viewBox="0 0 18 15">
                    <g id="ticket_icon" data-name="ticket icon"
                        transform="translate(-32.651 -322.034)">
                        <g id="Rectangle_16" data-name="Rectangle 16"
                            transform="translate(32.651 322.034)" fill="none"
                            stroke="#707070" stroke-width="2">
                            <rect width="18" height="15" rx="1" stroke="none" />
                            <rect x="1" y="1" width="16" height="13" fill="none" />
                        </g>
                        <path id="Path_14" data-name="Path 14"
                            d="M43.512,335.389h6.351"
                            transform="translate(-3.012 -9.183)" fill="none"
                            stroke="#707070" stroke-width="2" />
                        <path id="Path_15" data-name="Path 15"
                            d="M43.512,335.389h6.351"
                            transform="translate(-3.012 -2.526)" fill="none"
                            stroke="#707070" stroke-width="2" />
                        <path id="Path_16" data-name="Path 16"
                            d="M43.512,335.389h6.351"
                            transform="translate(-3.012 -5.854)" fill="none"
                            stroke="#707070" stroke-width="2" />
                    </g>
                </svg>
			</a>
		</span>
	</div>
`;

const render_modal = row => {
 return `<div class="modal" tabindex="-1" id="wschat_wsdesk_create_ticket_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create WSDesk Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert"></div>
        <div class="mb-3">
          <label for="wschat_wsdesk_email" class="form-label">Email</label>
          <input type="email" class="form-control" name="wschat_wsdesk_email" id="wschat_wsdesk_email" placeholder="name@example.com" value="${row.email}">
          <input type="hidden" id="wschat_wsdesk_message_id" name="message_id" value="${row.message_id}">
        </div>
        <div class="mb-3">
          <label for="wschat_wsdesk_subject" class="form-label">Subject</label>
          <input type="text" class="form-control" name="wschat_wsdesk_subject" id="wschat_wsdesk_subject" placeholder="" value="${row.text}">
        </div>
        <div class="mb-3">
          <label for="wschat_wsdesk_description" class="form-label">Description</label>
          <textarea class="form-control" id="wschat_wsdesk_description" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="wschat_wsdesk_create_ticket" type="button" class="btn btn-primary">Create</button>
      </div>
    </div>
  </div>
</div>`;
}

