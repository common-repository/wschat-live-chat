import { EVENTS } from '../events';
import {useState, render} from '@wordpress/element';

/**
 * Chat alert notification plugin
 *
 * You can customize chat ringtone using chat settings like below
 *
 * `options: {
 *      ...
 *      alert: {
 *        url: 'https://domain.tdl/path/to/tone',
 *        enable: true
 *      }
 *  }`
 */
export class ChatNotificationAlertPlugin {

	constructor(chat){
		this.chat = chat;
        this.url ='https://dobrian.github.io/cmp/topics/sample-recording-and-playback-with-web-audio-api/freejazz.wav';
        this.unread_count = 0;
        this.enable = true;

        const enabled = localStorage.getItem('wschat-alert-notification');
        if ( enabled !== null) {
        	this.enable = enabled;
        }
	}

	init() {
        if (this.chat.options.alert && this.chat.options.alert.hide) {
            return;
        }
        if (this.chat.options.alert && this.chat.options.alert.url) {
            this.set_url(this.chat.options.alert.url);
        }

        if (this.chat.options.alert && this.chat.options.alert.enable) {
            this.enable = this.chat.options.alert.enable;
        }

        jQuery(window).click(() => {
            this.setup_player();
        });

		const picker = jQuery(BTN_TEMPLATE);

		if (this.chat.options.is_admin) {
			this.chat.$el.find('.chat-panel-header .search-box-toggle').before(picker);
		} else {
			this.chat.$el.find('.chat-panel-header .wschat-panel-header-actions').prepend(picker);
		}

		const controller = this;

		render(<AlertControl enabled={controller.enable} controller={controller}/>, document.getElementById('wschat_alert_toggle_container'))

		this.chat.on(EVENTS.WSCHAT_ON_PONG, data => this.on_pong(data));
		this.chat.on(EVENTS.WSCHAT_ON_READ_ALL_MESSAGE, () => this.reset_unread_count());
		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.reset_unread_count());
		this.chat.on(EVENTS.WSCHAT_PLAY_NOTIFICATION_TONE, () => this.play());
	}

	set_url(url) {
		this.url = url;
	}

	setup_player() {
	    if (this.player) {
	        return;
	    }

        this.player = new Audio(this.url);
        this.player.volume = 0.5;
	}

	reset_unread_count() {
	    // this.unread_count = this.chat.conversation.unread_count;
	}

	on_pong(data) {
	    if (!data.unread_count || !this.player || this.player.error || this.unread_count == data.unread_count) {
	        return false;
	    }

	    this.unread_count = data.unread_count;

	    this.play();
	}


	toggle(enabled) {
		this.enable = enabled;
	}

	enabled() {
		return this.enable;
	}

	play() {
	    this.enable && this.player && this.player.play();
	}
}

export const BTN_TEMPLATE = `
	<div id="wschat_alert_toggle_container"></div>
`;

export const VOLUME_UP = () => (
<svg
  className="wschat-icon-color"
  xmlns="http://www.w3.org/2000/svg"
  width="24"
  height="24"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor"
  strokeWidth="2"
  strokeLinecap="round"
  strokeLinejoin="round"
>
  <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
  <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07" />
</svg>
);

export const VOLUME_MUTE = () =>  (
<svg
  className="wschat-icon-color volume_mute"
  xmlns="http://www.w3.org/2000/svg"
  width="24"
  height="24"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor"
  strokeWidth="2"
  strokeLinecap="round"
  strokeLinejoin="round"
>
  <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
  <line x1="23" y1="9" x2="17" y2="15" />
  <line x1="17" y1="9" x2="23" y2="15" />
</svg>
);

export const AlertControl = (props) => {

	const [enabled, setEnabled] = useState(props.enabled);

	const toggleState = () => {
		let currentState = !enabled;
		setEnabled(currentState);
		localStorage.setItem('wschat-alert-notification', currentState);
		props.controller.toggle(currentState);
	}

	return (<button id="wschat_alert_toggle" onClick={toggleState} className="btn btn-sm rounded-circle text-white" data-bs-toggle="tooltip"
		data-bs-placement="bottom" title="Toggle alert" data-bs-custom-class="tooltip-white">
		{enabled ? <VOLUME_UP /> : <VOLUME_MUTE />}
	</button>);
}
