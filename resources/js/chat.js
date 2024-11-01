import { EVENTS } from './events';
import moment from 'moment';
import { UserApiConnector } from './user_api_connector';
import { ChatMessageTextUrlParserPlugin } from './plugins/chat_content_text_url_parser';
import { ChatNotificationAlertPlugin } from './plugins/chat_alert_notifications';
import { FileAttachmentPlugin } from './plugins/file_attachment';
import { EmailTranscriptPlugin } from './plugins/email_transcript';
import { FeedbackPlugin } from './plugins/feedback';
import { VideoPlugin } from './plugins/video';
import { PreChatForm } from './plugins/prechat-form/prechat-form';

export const WSChat = class Chat {

    options = {};

    defaults = {
        type: 'user',
        preform: false,
        connector: UserApiConnector,
        api: {
            endpoint: '',
            interval: 1000
        },
        plugins: []
    };

    connector;

    plugins = [
    	ChatMessageTextUrlParserPlugin,
    	ChatNotificationAlertPlugin,
    	FileAttachmentPlugin,
        EmailTranscriptPlugin,
        FeedbackPlugin,
        VideoPlugin,
    	PreChatForm,
    ];

    event_listeners = {};

    constructor(el, options) {
        this.options = jQuery.extend({}, this.defaults, options);
        this.$el = jQuery(el);

        this.init();
    }

    init() {
		this.plugins = this.plugins.concat(this.options.plugins);

        this.connector = this.options.connector ? new this.options.connector(this, this.options.api) : false;
        this.trigger(EVENTS.WSCHAT_ON_INIT);
        this.load_plugins();
    }

    load_plugins() {
		this.plugins.forEach(plugin => {
			let instance = new plugin(this);

			instance.init();
		});
    }

    on(e, callback, key) {
        if (this.event_listeners[e] === undefined) {
            this.event_listeners[e] = [];
        }

        this.event_listeners[e].push(callback);
    }

    trigger(e, args, override_and_return) {
        if (this.event_listeners[e] === undefined) {
            return args;
        }

        this.event_listeners[e].forEach((callback) => {
            let res = callback(args);

            args = override_and_return === true ? res : args;

            if (override_and_return === true && typeof args === 'undefined') {
            	throw new Error('Expected output for the event ' + e + '. But the hook function returns undefined');
            }
        });

        return args;
    }

    set_conversation(conversation) {
        this.conversation = conversation;

        this.trigger(EVENTS.WSCHAT_ON_SET_CONVERSATION, conversation);
    }

    send_message(data) {
        this.trigger(EVENTS.WSCHAT_ON_SEND_MESSAGE, data);
    }

    update_conversation_meta(key, value) {
        if (!this.conversation ) {
            return false;
        }

        if (!this.conversation.meta ) {
            this.conversation.meta = {};
        }

        this.conversation.meta[key] = value;
    }

    get_conversation_meta(key) {
        if (!this.conversation ) {
            return false;
        }

        if (!this.conversation.meta ) {
            return false;
        }

        return this.conversation.meta[key];
    }
}

export const formatDate = (date_string) => moment(date_string).format('MMM DD, h:m a');

WSChat.prototype.EVENTS = EVENTS;

jQuery.fn.WSChat = function (options) {
    return new WSChat(this, options || {});
}
