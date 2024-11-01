import {EVENTS} from '../events'; /**
 * Agent Invitation
 */
export class AgentInvitePlugin {
	/**
	 * Chat core object
	 */
	chat;

	constructor(chat){
		this.chat = chat;
	}

	init() {
		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
			this.chat.$el.find('.chat-box-footer').find('.agent-invitation-action').remove();

			if (this.chat.conversation.invited) {
				this.chat.$el.find('.chat-box-footer').append(INVITATION_TEMPLATE).find('.agent-invitation-message').text(this.chat.conversation.invited_msg);
				this.chat.$el.find('.chat-box-footer .agent-invitation-join').on('click', () => {this.accept_invitation()});
				this.chat.$el.find('.chat-box-footer .agent-invitation-decline').on('click', () => {this.decline_invitation()});
			}
		});
	}

    accept_invitation() {
		const data = {
			action: 'wschat_agent_accept_invitaion',
			conversation_id: this.chat.conversation.id
		};
		jQuery.post(this.chat.options.api.endpoint, data, res => {
			this.chat.$el.find('.chat-box-footer').find('.agent-invitation-action').remove();
		}).fail(f => {
			console.log(f);
		});
    }

    decline_invitation() {
		const data = {
			action: 'wschat_agent_decline_invitaion',
			conversation_id: this.chat.conversation.id
		};
		jQuery.post(this.chat.options.api.endpoint, data, res => {
			window.location.reload();
		}).fail(f => {
			console.log(f);
		});
    }
}

const INVITATION_TEMPLATE = `
<div class="agent-invitation-action position-absolute w-100 h-100 top-0 start-0 p-2 bg-light" style="z-index: 999">
    <div class="d-flex h-100 align-items-center justify-content-between">
        <h6 class="mb-0 agent-invitation-message">Craig Gibson have added you to the chat</h6>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary agent-invitation-decline">
                <small>Decline</small>
            </button>

            <button class="btn btn-primary agent-invitation-join">
                <small>Join Chat</small>
            </button>
        </div>
    </div>

</div>
`;
