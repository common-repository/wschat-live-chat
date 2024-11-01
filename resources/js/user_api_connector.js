import { EVENTS } from './events';

export class UserApiConnector {
    filters = {
        before: 0,
        after: 0,
    };

    options = {
        endpoint: '',
        interval: 3000,
        wschat_ajax_nonce: '',
        do_not_loop: false,
        pusher: {
            authEndpoint: '/wp-admin/admin-ajax.php',
            auth: {
                params: {
                    action: 'wschat_pusher_auth'
                }
            },
            broadcaster: 'pusher',
            key: 'fc9df0026d0a3bf24f3a',
            cluster: 'ap2',
            forceTLS: true
        }
    };

    is_requesting = false;

    constructor(chat, options) {
        this.chat = chat;
        options.pusher = options.pusher ? {...this.options.pusher, ...options.pusher} : {};
        this.options = {...this.options, ...options}

        this.start_conversation();
        this.subscribe();
    }

    subscribe() {

        this.chat.on(EVENTS.WSCHAT_ON_SEND_MESSAGE, (data) => {
            this.send_message(data);
        });

        this.chat.on(EVENTS.WSCHAT_ON_READ_ALL_MESSAGE, data => this.read_all(data));

        this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
            this.pause = false;
            if (this.started_getting_message !== true) {
            	this.restart_message_chain();
            }
            this.started_getting_message = true;
        });
    }

    start_conversation() {
        jQuery.post(this.options.endpoint, {
            action: this.ACTION_START_CONVERSATION,
            current_url: window.location.href,
            title: jQuery(document).attr('title'),
        }, (data) => {
            this.chat.set_conversation(data.data);
        });
    }

    restart_message_chain() {
		if (this.get_messages_interval) {
			clearInterval(this.get_messages_interval);
		}
        this.get_messages_interval = setInterval(() => this.get_messages(), 1500);
    }

    async get_messages(params) {
        if (this.is_requesting === true) {
            this.restart_message_chain();
        }
        this.is_requesting = true;

        if (!this.chat.conversation) {
            this.started_getting_message = false;
            return false;
        }

        params = params || {};
        params = { ...this.filters, ...params };

        const data = {
            ...params,
            action: this.ACTION_GET_MESSAGE,
        };

        data.conversation_id = this.chat.conversation.id;

        try {
        	const res = await jQuery.post(this.options.endpoint, data);
        	this.is_requesting = false;

        	if (typeof res !== 'object') {
            	return;
        	}

        	if (!data.no_pong && parseInt(res.data.conversation_id) === parseInt(data.conversation_id)) {
            	this.chat.trigger(EVENTS.WSCHAT_ON_PONG, res.data);
        	}

        	let mLength = res.data.messages.length;

        	this.filters.after = mLength && (this.filters.after  < res.data.messages[0].id) ?
            	res.data.messages[0].id :
            	this.filters.after;

        	res.data.messages.forEach((row, i) => {
            	res.data.messages[i] = this.chat.trigger(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, row, true);
        	});

        	this.chat.trigger(EVENTS.WSCHAT_ON_FETCH_MESSAGES, res.data);
        } catch (f) {
        	if (f.status === 403) {
        		this.chat.$el.find('.chat-box-footer').addClass('d-none');

        		if (f.responseJSON && f.responseJSON.data && f.responseJSON.data.message) {
        			this.chat.$el.find('.session-was-expired').remove();
        			this.chat.$el.find('.chat-box-footer').after(`
        				<div class="text-center xs p-2 text-secondary session-was-expired">
        					${f.responseJSON.data.message}
        					<a class="btn btn-primary" onclick="window.location.reload()">Reload</a>
        				</div>
        			`);
				}
        	}
        }
    }

    send_message(data) {
        data.action = this.ACTION_SEND_MESSAGE;
        data.conversation_id = this.chat.conversation.id;

        let frmData = new FormData();

        for (let key in data) {
            frmData.append(key, data[key]);
        }

    	frmData = this.chat.trigger(EVENTS.WSCHAT_BUILD_FORM_DATA, frmData, true);

        jQuery.ajax({
        	xhr: () => {
                const xhr = new window.XMLHttpRequest();

                if (!frmData.get('attachments[]')) {
                    return xhr;
                }

                xhr.upload.addEventListener("progress", (evt) => {
                    if (!evt.lengthComputable) {
                        return;
                    }

                    const percentComplete = (evt.loaded / evt.total) * 100;
                    this.chat.trigger(EVENTS.WSCHAT_ON_UPLOAD_FILE_PROGRESS, percentComplete);
                }, false);

                return xhr;
            },
            method: 'post',
            data: frmData,
            url: this.options.endpoint,
            cache: false,
            processData: false,
            contentType: false,
            success: message => {
                this.chat.trigger(EVENTS.WSCHAT_ON_SENT_A_MESSAGE, message);
            },
        });
    }

    read_all(data) {
        data = data || {};
        data.action = this.ACTION_READ_ALL;
        data.conversation_id = this.chat.conversation.id;

        jQuery.post(this.options.endpoint, data);
    }

    reset_filters() {
        this.filters.after = 0;
        this.filters.before = 0;
        this.pause = true;
    }
}

UserApiConnector.prototype.ACTION_SEND_MESSAGE = 'wschat_send_message';
UserApiConnector.prototype.ACTION_GET_MESSAGE = 'wschat_get_messages';
UserApiConnector.prototype.ACTION_READ_ALL = 'wschat_read_all';
UserApiConnector.prototype.ACTION_START_CONVERSATION = 'wschat_start_conversation';
