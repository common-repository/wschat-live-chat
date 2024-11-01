import { EVENTS } from '../events';

export class SessionPlugin {
	constructor(chat) {
		this.chat = chat;
	}

	init() {
		if (!this.chat.options.is_admin) {
			return true;
		}

		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.render_session_button());
		this.chat.on(EVENTS.WSCHAT_ON_SEND_MESSAGE, () => this.start_session());
		this.chat.on(EVENTS.WSCHAT_ON_END_SESSION, () => this.on_end_session());

		this.btn = jQuery(BTN_TEMPLATE);
		this.chat.$el.find('.conversation-header-actions').prepend(this.btn);

		this.btn.click(e => this.end_session());
	}

	render_session_button() {
		if (this.chat.conversation.meta.session && this.chat.conversation.meta.session.is_ended === false) {
			this.btn.removeClass('d-none');
		} else {
			this.btn.addClass('d-none');
		}
	}

	end_session() {
		this.btn.addClass('disabled');
		const data = {
			action: 'wschat_admin_end_session',
			conversation_id: this.chat.conversation.id
		};
		jQuery.post(this.chat.options.api.endpoint, data, res => {
			this.chat.conversation.meta.session = res.data.meta.session;
			this.btn.removeClass('disabled');
			this.btn.addClass('d-none');
			this.render_session_button();
            this.chat.trigger(EVENTS.WSCHAT_ON_END_SESSION, this.chat.conversation);
		}).fail(f => {
		});
	}

    on_end_session() {
        this.chat.conversation = undefined;
    }

	start_session() {
		const meta = this.chat.conversation.meta;
		if (meta.session) {
			meta.session.is_ended = false;
		} else {
			meta.session = {is_ended: false};
		}

		this.chat.conversation.meta = meta;
		this.render_session_button();
	}
}

export const BTN_TEMPLATE = `
<button class="btn btn-sm  border-white text-white" id="elex-ws-chat-end-toast-msg-btn">
	<svg xmlns="http://www.w3.org/2000/svg" width="13.333" height="13.333"
		viewBox="0 0 13.333 13.333">
		<g id="wschat_end_session" data-name="end chat icon"
			transform="translate(-1638.133 -91.333)">
			<path id="Icon_material-chat_bubble_outline"
				data-name="Icon material-chat_bubble_outline"
				d="M13.333,1.333H2.667A1.337,1.337,0,0,0,1.333,2.667v12L4,12h9.333a1.337,1.337,0,0,0,1.333-1.333v-8A1.337,1.337,0,0,0,13.333,1.333Zm0,9.333H4L2.667,12V2.667H13.333v8Z"
				transform="translate(1636.8 90)" fill="#fff" />
			<path id="Path_119" data-name="Path 119" d="M3617.371,1387.5l4.394-3.767"
				transform="translate(-1974.769 -1288.917)" fill="none" stroke="#fff"
				stroke-linecap="round" stroke-width="1" />
		</g>
	</svg>
	End Chat
</button>
`;
