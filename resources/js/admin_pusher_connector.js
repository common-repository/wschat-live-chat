import { EVENTS } from './events';
import Echo from 'laravel-echo';
import 'pusher-js';
import { AdminApiConnector } from './admin_api_connector'

export class AdminPusherConnector extends AdminApiConnector {
    subscribe() {
        this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
            this.pause = false;
            this.get_messages();

			this.set_echo();
            this.leave_channel_if_joined();

            this.subscribe_pusher();
        });

        this.chat.on(EVENTS.WSCHAT_ON_SEND_MESSAGE, (data) => {
            this.send_message(data);
        });

        this.chat.on(EVENTS.WSCHAT_ON_READ_ALL_MESSAGE, data => this.read_all(data));
    }

    set_echo() {
    	this.options.pusher.authEndpoint = this.options.endpoint;
    	this.options.pusher.auth.params.wschat_ajax_nonce = this.options.wschat_ajax_nonce;
    	this.options.pusher.auth.params.action = pusher_auth_action;

    	this.Echo = this.Echo || new Echo(this.options.pusher);
    }

    check_user_is_online() {
    	let members = this.echo_channel.subscription.members.members;
    	members = Object.keys(members).filter(i => members[i].type !== 'agent');

        this.chat.trigger(EVENTS.WSCHAT_ON_PONG, {
        	messages: [],
        	is_online: members.length >  0,
        	status: members.length ? 'Online' : 'Offline'
        });
    }

    leave_channel_if_joined() {
        if (this.echo_channel) {
            this.Echo.leaveChannel('presence-' + this.channel_name);
        }
    }

    subscribe_pusher() {

        this.channel_name = 'conversation_' + this.chat.conversation.id;

        this.echo_channel = this.Echo.join('conversation_' + this.chat.conversation.id);

        setTimeout( () => this.check_user_is_online(), 500);

        this.echo_channel.here(() => {
        	this.check_user_is_online()
        });

        this.echo_channel.joining(() => {
        	this.check_user_is_online()
        });

        this.echo_channel.leaving(() => {
        	this.check_user_is_online()
        });

        this.echo_channel.listen('.message', (data) => {
            data.messages.forEach((row, i) => {
                data.messages[i] = this.chat.trigger(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, row, true);
            });

            // Update unread_count
            const unread_count = parseInt(this.chat.$el.find('[data-conversation-id='+this.chat.conversation.id+'] .unread-count').text()) || 0;
            data.unread_count = unread_count + data.messages.filter(m => m.is_agent === false).length;
			this.chat.$el.find('.unread_count').text(data.unread_count);

            this.chat.trigger(EVENTS.WSCHAT_ON_PONG, data);
            this.chat.trigger(EVENTS.WSCHAT_ON_FETCH_MESSAGES, data);
        });

        this.echo_channel.listen('.conversation', (conversations) => {
            this.chat.trigger(EVENTS.WSCHAT_ON_FETCH_CONVERSATIONS, conversations);
        });
    }
}

export const pusher_auth_action = 'wschat_admin_pusher_auth';
