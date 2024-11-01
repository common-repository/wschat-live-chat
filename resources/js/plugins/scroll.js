import { weekdays } from 'moment';
import { EVENTS } from '../events';

export class ScrollPlugin {
	constructor(chat) {
		this.chat = chat;
	}

	init() {
		if (!this.chat.options.is_admin) {
			return true;
		}

		this.btn = jQuery(BTN_TEMPLATE);
		this.chat.$el.find('.elex-ws-chat-convo').parent().append(this.btn);
		this.chat.$el.find('.chat-panel').on('scroll', () => this.show_or_hide_btn());

		this.btn.click(e => this.scroll_down());
        this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.show_or_hide_btn()); }

	scroll_down() {
		const panel = this.chat.$el.find('.chat-panel');
		panel.scrollTop(panel.prop('scrollHeight'));
	}

	show_or_hide_btn() {
		const last_message = this.chat.$el.find('.chat-panel').children().last();

        if (last_message.length === 0 || last_message.offset().top - this.chat.$el.find('.chat-box-footer').offset().top < 100) {
			this.btn.addClass('d-none');
            return;
        } else {
        	this.btn.removeClass('d-none');
        }
	}
}

export const BTN_TEMPLATE = `
	<button id="wschat_scroll_down_chat_panel" class="btn btn-sm btn-outline-primary border-0 position-absolute shadow d-none p-1 rounded-circle bg-white" title="Scroll Down">
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="19" viewBox="0 0 25 19">
          <g id="move_to_bottom_icon" data-name="move to bottom icon" transform="translate(-1195.379 -944.352)">
            <g id="Icon_feather-arrow-down" data-name="Icon feather-arrow-down" transform="translate(1190 934)">
              <path id="Path_143" data-name="Path 143" d="M28.5,18,18,28.5,7.5,18" fill="none" stroke="#2489db" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
            </g>
            <g id="Icon_feather-arrow-down-2" data-name="Icon feather-arrow-down" transform="translate(1192.741 928.473)">
              <path id="Path_143-2" data-name="Path 143" d="M23.017,18l-7.759,7.554L7.5,18" transform="translate(0)" fill="none" stroke="#2489db" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
            </g>
          </g>
        </svg>
	</button>
`;

