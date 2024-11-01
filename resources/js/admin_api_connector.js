import {UserApiConnector} from './user_api_connector';

import {EVENTS} from './events';

export class AdminApiConnector extends UserApiConnector {

	_stop_conversation_chain = false;
	conversation_chain = 0;
    is_admin = true;
    params = {};

	start_conversation() {
		if (this.get_conversations_timer) {
			clearInterval(this.get_conversations_timer);
		} else {
			this.get_conversations();
		}

		this.get_conversations_timer = setInterval(() => this.get_conversations(), 5000);
	}

    async get_conversations() {
    	if (this.is_requesting === true) {
    		this.start_conversation();
    	}
    	this.is_requesting = true;

    	const params = this.params || {};
        params.action = this.ACTION_GET_CONVERSATIONS;
        params.type = this.type;
        params.limit = 200;

        const data = await jQuery.post(this.options.endpoint, params);
    	this.is_requesting = false;

        if (data.data.length === 0) {
            this.chat.trigger(EVENTS.WSCHAT_ON_NO_CONVERSATIONS);
        }

        this.chat.trigger(EVENTS.WSCHAT_ON_FETCH_CONVERSATIONS, data.data);
    }

    join_conversation(id, agent_id) {
        this.reset_filters();

        jQuery.post(this.options.endpoint, {
            action: this.ACTION_JOIN_CONVERSATION,
            conversation_id: id,
            agent_id: agent_id
        }, (data) => {
            this.chat.set_conversation(data.data);
        });
    }

    delete_conversation(id) {
        this.reset_filters();

        jQuery.post(this.options.endpoint, {
            action: this.ACTION_DELETE_CONVERSATION,
            conversation_id: id,
        }, () => {
            this.chat.trigger(EVENTS.WSCHAT_ON_DELETE_CONVERSATIONS, id);
        });
    }
}

AdminApiConnector.prototype.ACTION_SEND_MESSAGE = 'wschat_admin_send_message';
AdminApiConnector.prototype.ACTION_GET_MESSAGE = 'wschat_admin_get_messages';
AdminApiConnector.prototype.ACTION_READ_ALL = 'wschat_admin_read_all';
AdminApiConnector.prototype.ACTION_GET_CONVERSATIONS = 'wschat_admin_get_conversations';
AdminApiConnector.prototype.ACTION_JOIN_CONVERSATION = 'wschat_admin_join_conversation';
AdminApiConnector.prototype.ACTION_DEASSIGN_AGENT = 'wschat_admin_deAssign_Agent';
const ACTION_DELETE_CONVERSATION = 'wschat_admin_delete_conversation';
