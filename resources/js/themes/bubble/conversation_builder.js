import ConversationTemplate from './components/conversation'
import {EVENTS} from '../../events';
import {delay} from '../../utils';
import tinyColor from 'tinycolor2';

export class ConversationBuilder  {
	// @var ConversationItem[]
	items = [];
    doing_ajax = false;
    loaded_all_items = false;

	constructor(chat) {
		this.chat = chat;
		this.el = this.chat.$el.find('.conversation-list')
        this.subscribe();
        const color = tinyColor(wschat_ajax_obj.settings.colors["--wschat-bg-primary"]);
        this.chat.$el.css('--wschat-bg-hover-primary', color.isLight() ? color.darken(15) : color.lighten(15))
	}

    subscribe() {
        this.chat.on(EVENTS.WSCHAT_ON_NO_CONVERSATIONS, () => this.on_no_conversations())
        this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.on_set_conversation())
        this.chat.on(EVENTS.WSCHAT_ON_FETCH_CONVERSATIONS, (conversations) => this.on_fetch_conversations(conversations));
		this.chat.on(EVENTS.WSCHAT_ON_DELETE_CONVERSATIONS, id => this.on_delete(id));

        this.chat.$el.find('.search-box input[name=conversation_status]').on('change', () => {
			this.reset();
			// this.chat.connector.start_conversation_chain();
        	// this.chat.connector.start_conversation(this.build_params());
        	this.chat.connector.params = this.build_params();
        });

        this.chat.$el.find('.conversation_types button').on('click', (e) => {
			this.reset();
            const btn = jQuery(e.target);
            this.chat.connector.type = btn.data('type');
            btn.parent().find('button').removeClass('active');
            btn.addClass('active');
			// this.chat.connector.start_conversation_chain();
        	// this.chat.connector.start_conversation();
        	this.chat.connector.params = this.build_params();
        });

        this.chat.$el.find('.search-box input[name=search-control]').on('keyup', delay(() => {
			// this.reset();
			this.chat.connector.params = this.build_params();
			this.chat.connector.get_conversations();

			// if (params.search.trim() === '') {
			// 	this.chat.connector.start_conversation_chain();
			// 	this.chat.connector.start_conversation();
			// } else {
			// 	this.chat.connector.stop_conversation_chain();
			// 	this.chat.connector.get_conversations(params);
			// }
		}, 500));
    }

    on_set_conversation() {
        this.chat.$el.find('.conversation-list-is-empty').addClass('d-none');
        this.chat.$el.find('.select-a-conversation').addClass('d-none');
        this.chat.$el.find('.chat-panel-header').removeClass('d-none');
        this.chat.$el.find('.elex-ws-chat-convo-body').parent().addClass('d-none');
    }

    on_no_conversations() {
        if (this.chat.conversation) {
            const no_conversation_alert = this.chat.$el.find('.no-conversation-alert').clone();
            this.el.html(no_conversation_alert.removeClass('d-none'));

        	return;
        }

        this.chat.$el.find('.conversation-list-is-empty').removeClass('d-none');
        this.chat.$el.find('.select-a-conversation').addClass('d-none');
        this.chat.$el.find('.chat-panel-header').parent().addClass('d-none');
    }

    on_fetch_conversations(conversations) {
        this.items = [];
        conversations.forEach(conversation => this.add(conversation));
        this.render();

        if (this.items.length !== 0 && ! this.chat.conversation) {
        	this.chat.$el.find('.conversation-list-is-empty').addClass('d-none');
        	this.chat.$el.find('.select-a-conversation').removeClass('d-none');
        } else {
        	this.chat.$el.find('.select-a-conversation').addClass('d-none');
        }

		this.set_conversation_if_not();
    }

    on_delete(id) {
		// this.chat.connector.stop_conversation_chain();
		const item = this.items.find(item => item.conversation.id === id);
		item.delete();
		this.items = this.items.filter(item => item.conversation.id != id);

        this.chat.$el.find('.elex-ws-chat-convo-body').parent().addClass('d-none');

		this.render();

		this.set_conversation_if_not();
    }

    set_conversation_if_not() {
        setTimeout(() => {
            const activeItem = this.el.find('.active[data-conversation-id]').length
            if (activeItem === 0) {
            	const query_conversation_id = this.get_conversion_id_from_url();
            	if (query_conversation_id) {
            		const div = this.el.find(`[data-conversation-id=${query_conversation_id}]`);
            		if (div.length) {
            			div.click();
            		} else {
        				this.chat.connector.join_conversation(query_conversation_id);
            		}
            	} else {
            		this.el.find('[data-conversation-id]').eq(0).click();
            	}
            }
        }, 1000);
    }

    get_conversion_id_from_url() {
    	const params = new URLSearchParams(location.search);

		return params.get('conversation_id');
    }

    build_params() {
        const params = {};

        // const length = this.items.length;

        // if (length) {
        //     params.before = this.items[length - 1].conversation.updated_at;
        // }

        params.search = this.chat.$el.find('.search-box input[name=search-control]').val();

        return params;
    }

	add(conversation) {
		// const exists = this.find(conversation);
		// TO ignore updating
		const exists = false;

		conversation.is_selected = this.chat.conversation && conversation.id === this.chat.conversation.id;

		if (exists) {
			exists.replace(conversation);
		} else {
			this.items.push(new ConversationItem(conversation));
		}
	}

	find(conversation) {
		return this.items.find(item => item.conversation.id === conversation.id);
	}

	sort() {
		this.items.sort((a,b) => {
			if (a.conversation.last_message && b.conversation.last_message) {
				return b.conversation.last_message - a.conversation.last_message;
			}
			return new Date(a.conversation.last_update) > new Date(b.conversation.last_update) ? -1 : 1;
		});
	}

	render() {
		this.sort();

		this.el.html('');

		for (let item of this.items) {
            this.el.append(item.domEl);
		}

		if (this.items.length === 0) {
			this.on_no_conversations();
		}

        this.doing_ajax = false;
	}

	reset() {
		this.items = [];
		this.el.html('');
	}
}

export class ConversationItem {
	constructor(conversation) {
		this.conversation = conversation;

		this.domEl = ConversationTemplate(conversation);

		this.update();
	}

	update() {
		if (this.conversation.is_user_online) {
			this.domEl.hasClass('online') === false && this.domEl.addClass('online');
		} else {
			this.domEl.hasClass('online') && this.domEl.removeClass('online');
		}

		const conv = this.domEl.find(['data-conversation-id']);
		if (this.conversation.is_selected) {
			conv.hasClass('active') === false && conv.addClass('active');
		} else {
			conv.hasClass('active') && conv.removeClass('active');
		}

        if (this.conversation.user && this.conversation.user.meta.avatar) {
			this.domEl.find('img.profile-image').attr('src', this.conversation.user.meta.avatar)
        }
	}

	replace(conversation) {
		this.conversation = conversation;
		const newEl = ConversationTemplate(conversation);
		this.domEl.html(newEl.html());

		this.update();
	}

	delete() {
		this.domEl.remove();
	}
}

const SCROLL_BUFFER = 100;
