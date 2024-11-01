import { WSChat } from './chat';
import { AdminApiConnector } from './admin_api_connector';
import { AdminPusherConnector } from './admin_pusher_connector';
import { EVENTS } from './events';
import { EmojiButton } from '@joeattardi/emoji-button';
import UserMetaInfo from './components/user_meta_info.html'
import { ConversationBuilder  } from './themes/bubble/conversation_builder';
import {SearchPlugin} from './plugins/search';
import {ScrollPlugin} from './plugins/scroll';
import {SessionPlugin} from './plugins/session';
import {TagsPlugin} from './plugins/tags';
import {WSDeskPlugin} from './plugins/wsdesk';
import {UserInfoPlugin} from './plugins/user-info';
import {AgentInvitePlugin} from './plugins/invite';
import { clippingParents } from '@popperjs/core';
import { error } from 'laravel-mix/src/Log';

var gptOptions = [];

jQuery(document).ready(function() {
    // attachment btn rotate on active
    jQuery('.elex-ws-chat-diff-inputs-btn').click(function (){
        // jQuery(this).toggleClass('rotate')
        if(jQuery('.elex-ws-chat-diff-inputs').hasClass('active')){
            jQuery('.elex-ws-chat-convo-body').removeClass('elex-ws-chat-convo-body-blur');
            jQuery('.elex-ws-chat-diff-inputs-btn').removeClass('rotate');
        }else{
            jQuery('.elex-ws-chat-convo-body').addClass('elex-ws-chat-convo-body-blur');
            jQuery('.elex-ws-chat-diff-inputs-btn').addClass('rotate');
        }
    })
    jQuery(document).click(function(e) {
        if (jQuery(e.target).is('.elex-ws-chat-diff-inputs-btn, .elex-ws-chat-diff-inputs-btn *'));
        else {
            jQuery('.elex-ws-chat-diff-inputs').removeClass('active')
            jQuery('.elex-ws-chat-convo-body').removeClass('elex-ws-chat-convo-body-blur');
            jQuery('.elex-ws-chat-diff-inputs-btn').removeClass('rotate');
        };
    });

    const wrapper = jQuery('.wschat-wrapper');

    if (wrapper.length === 0) {
    	return;
    }

		// <div class="row message-item mb-3 position-relative" data-message-id="{{MESSAGE_ID}}">
    const CHAT_BUBBLE_TEMPLATE = `
			<div class="align-items-start d-flex elex-ws-chat-convo-text-box flex-column mb-4 message-item {{OFFSET}} " data-message-id="{{MESSAGE_ID}}">
				<div class="d-flex flex-column mx-3">
                    <div class="xs wschat-text-gray d-flex justify-content-start ">
                        {{AGENT_NAME}}
					</div>
					<div class="chat-bubble chat-bubble--{{POS}} position-relative">
						{{CONTENT}}
						<div
                        	class="position-absolute top-0 end-0 translate-middle-y rounded-pill badge shadow-sm d-flex justify-content-around message-item-action-badges bg-white p-0 align-items-center">
                    	</div>
					</div>
					<div class="xs wschat-text-gray d-flex justify-content-start message-item-timestamp">
						{{TIMESTAMP}}
					</div>
				</div>
			</div>
		`;

    const BUBBLE_TEMPLATE_DEFAULTS = {
        '{{OFFSET}}': '',
        '{{POS}}': 'left',
        '{{CONTENT}}': '',
        '{{TIMESTAMP}}': '',
        '{{MESSAGE_ID}}': '',
        '{{AGENT_NAME}}': '',
    };

    jQuery.ajaxSetup({
        data: {
            wschat_ajax_nonce: wschat_ajax_obj.nonce
        }
    });

    var chat = new WSChat(jQuery('.wschat-wrapper'), {
        connector: wschat_ajax_obj.settings.communication_protocol === 'pusher' ? AdminPusherConnector : AdminApiConnector,
        api: {
            endpoint: wschat_ajax_obj.ajax_url,
            interval: 5000,
            wschat_ajax_nonce: wschat_ajax_obj.nonce,
            pusher: {
				key: wschat_ajax_obj.settings.pusher.app_key,
				cluster: wschat_ajax_obj.settings.pusher.cluster,
			}
        },
        wsdesk: wschat_ajax_obj.settings.wsdesk_enabled,
        is_admin: true,
        plugins: [
        	SearchPlugin,
            WSDeskPlugin,
        	TagsPlugin,
        	SessionPlugin,
            ScrollPlugin,
            UserInfoPlugin,
            AgentInvitePlugin,
        ],
        tags: wschat_ajax_obj.tags,
        enable_tags: wschat_ajax_obj.enable_tags,
        alert: {
        	url: wschat_ajax_obj.settings.alert_tone_url
        },
        header: {
        	status_text: wschat_ajax_obj.settings.widget_status === 'online' ? wschat_ajax_obj.settings.header_online_text : wschat_ajax_obj.settings.header_offline_text,
        }
    });

    if (wschat_ajax_obj.settings) {
		for(let key in wschat_ajax_obj.settings.colors) {
			key && chat.$el.get(0).style.setProperty(key,  '#' +wschat_ajax_obj.settings.colors[key]);
		}
		chat.$el.get(0).style.setProperty('--wschat-font-family',  wschat_ajax_obj.settings.font_family);
    }

    const chat_panel = chat.$el.find('.chat-panel');
    const conversation_panel = chat.$el.find('.conversation-list');
    const chat_panel_header = chat.$el.find('.chat-panel-header');
    const message_input = jQuery('#wschat_message_input');
    const MESSAGE_INFO = {
        min: 0,
        max: 0,
    };

    let PAST_REQUEST_IS_PENDING = false;
    let SCROLL_PAUSED = false;
    let DISABLE_SCROLL_LOCK = false;
    const SCROLL_OFFSET = 100;
    new ConversationBuilder(chat);

    chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, (data) => {
        chat_panel.html('');
        if (data.user) {
            chat_panel_header.find('.username').text(data.user.meta.name);
            chat_panel_header.find('.conversation-user-avatar').attr('src', data.user.meta.avatar);
        }
        let info = chat.$el.find('.user-meta-info').html(UserMetaInfo);

        chat_panel_header.parent().removeClass('d-none')

		info.find('.name').html(data.user.meta.name);
		info.find('.browser').html(data.user.meta.browser);
		info.find('.os').html(data.user.meta.os);
		info.find('.device').html(data.user.meta.device);
		info.find('.url').html(data.user.meta.current_url);


		chat.trigger(EVENTS.WSCHAT_ON_RENDER_USER_META, info);

		const agents = info.find('.conversation-agents');
		const agents_dropdown = info.find('.conversation-agents-control');

		agents.html('')
		agents_dropdown.html('')

		data.participants.forEach(agent => {
			agents.append('<li class="list-group-item">' + agent.display_name + '</li>');
		});

		data.agents.forEach(agent => {
			agents_dropdown.append(`<option value="${agent.ID}">${agent.display_name}</option> `);
		});

        message_input.focus();
        MESSAGE_INFO.min = 0;
        MESSAGE_INFO.max = 0;
        DISABLE_SCROLL_LOCK = true;

        setTimeout(() => DISABLE_SCROLL_LOCK = false, 1000);
    });

    chat.$el.on('click', '.add_agent_to_conversation', function () {
    	const agents = jQuery('.conversation-agents-control');

    	chat.connector.join_conversation(chat.conversation.id, agents.val());
    });

    chat.on(EVENTS.WSCHAT_ON_FETCH_MESSAGES, (data) => {
    	const messages = data.messages;
    	messages.sort((a,b) => a.id-b.id ? -1 : 1);
        for (let i = 0; i < messages.length; i++) {
            let row = data.messages[i];
			if (!chat.conversation || row.conversation_id != chat.conversation.id) {
				continue;
			}

            if (row.is_agent === true) {
                BUBBLE_TEMPLATE_DEFAULTS['{{OFFSET}}'] = 'justify-content-end ms-auto';
                BUBBLE_TEMPLATE_DEFAULTS['{{POS}}'] = 'right';
                BUBBLE_TEMPLATE_DEFAULTS['{{AGENT_NAME}}'] = row.user.name;
            } else {
                BUBBLE_TEMPLATE_DEFAULTS['{{OFFSET}}'] = 'justify-content-start';
                BUBBLE_TEMPLATE_DEFAULTS['{{POS}}'] = 'left';
                BUBBLE_TEMPLATE_DEFAULTS['{{AGENT_NAME}}'] = '';
            }
            BUBBLE_TEMPLATE_DEFAULTS['{{MESSAGE_ID}}'] = row.id;
            BUBBLE_TEMPLATE_DEFAULTS['{{CONTENT}}'] = row.body.formatted_content;
            BUBBLE_TEMPLATE_DEFAULTS['{{TIMESTAMP}}'] = row.created_at;
            
            let row_template = CHAT_BUBBLE_TEMPLATE;

            if (row.type === 'info') {
                row_template = `
                  <div class="row g-0 w-100 message-item text-center" data-message-id="{{MESSAGE_ID}}">
                    <div class="text-center mb-4 p-2 shadow w-50 m-auto rounded">
                        ${row.body.text}
                    <div>
                  </div>
                `;
            }

            row_template = row_template.replace(new RegExp(Object.keys(BUBBLE_TEMPLATE_DEFAULTS).join('|'), 'g'), match => BUBBLE_TEMPLATE_DEFAULTS[match]);
            row_template = jQuery(row_template);
            if (row.is_agent) {
                row_template.find('.message-item-timestamp').removeClass('justify-content-start').addClass('justify-content-end');
            }

            if (MESSAGE_INFO.min === 0) {
                chat_panel.append('<span data-message-id="0"></span>');
            }

            if (MESSAGE_INFO.min > row.id) {
                chat_panel.find('[data-message-id='+MESSAGE_INFO.min+']').last().before(row_template);
                MESSAGE_INFO.min = row.id;
            }

            if (MESSAGE_INFO.max === 0 || MESSAGE_INFO.max < row.id) {
                chat_panel.find('[data-message-id='+MESSAGE_INFO.max+']').last().after(row_template);
                MESSAGE_INFO.max = row.id;
                scrollIfNotPaused();
            }

            chat.trigger(EVENTS.WSCHAT_ON_RENDER_MESSAGE, {message_item: row_template, row});

            if (MESSAGE_INFO.min === 0) {
               scrollIfNotPaused();
            }

            MESSAGE_INFO.min = MESSAGE_INFO.min || row.id;
            MESSAGE_INFO.max = MESSAGE_INFO.max || row.id;

    		if (chat.scroll_to == row.id) {
				setTimeout(() => {
					jQuery('.message-item.bg-warning').removeClass('bg-warning')
					const el = jQuery('.message-item[data-message-id='+row.id+']').addClass('bg-warning');
                    if (el.length) {
                        const top = el.offset().top - 500;
                        chat_panel.scrollTop(chat_panel.scrollTop() +  top);
                        chat.scroll_to = undefined;
                    }
				}, 500)
    		}
        }

        if (DISABLE_SCROLL_LOCK === true) {
            scrollIfNotPaused();
        }

    });

    chat.on(EVENTS.WSCHAT_ON_PONG, (data) => {
        let drawer = chat_panel_header.find('.friend-drawer');
		let row_template = conversation_panel.find('[data-conversation-id='+data.id+']');
		let row_unread_count = row_template.find('.unread-count');
		let header_unread_count = chat_panel_header.find('.unread-count');

        data.status && chat_panel_header.find('.status').removeClass('online offline').addClass(data.status.toLowerCase()).parent().find('small').text(data.status)
        header_unread_count.text(data.unread_count);
        row_unread_count.text(data.unread_count || '');

        if (parseInt(header_unread_count.text())) {
            header_unread_count.removeClass('d-none');
        } else {
            header_unread_count.addClass('d-none');
        }

        if (data.is_online) {
            drawer.addClass('online');
            row_template.addClass('online');
        } else {
            drawer.removeClass('online');
            row_template.removeClass('online');
        }
    });

    const scrollIfNotPaused = (offset) => {
        if (SCROLL_PAUSED === false || DISABLE_SCROLL_LOCK === true) {
            chat_panel[0].scrollTop = chat_panel[0].scrollHeight + (offset || 0);
        }
    }

    const send_btn = jQuery('#wschat_send_message').on('click', function() {


		if (! chat.conversation) {
			return;
		}

        let msg = message_input.val();

        if (msg.trim() === '' && chat.trigger(EVENTS.WSCHAT_CAN_SEND_EMPTY_MESSAGE, false, true) === false) {
            return false;
        }

        chat.send_message({
            // Type is text by default now, it needs to changed based on the selection content
            wschat_ajax_nonce: wschat_ajax_obj.nonce,
            type: 'text',
            'content[text]': message_input.val()
        });
        message_input.val('').focus();
    });


          

    message_input.keyup(function(e) {
        e.key === 'Enter' && send_btn.click();
    });

    chat.$el.find('.search-messages').keyup(function(e) {
        if (e.key !== 'Enter' || this.value.trim() === '') {
			return;
		}

        chat.connector.get_messages({
        	after: 0,
        	before: 0,
            search: this.value
        });

    });

    message_input.on('click', function() {
        // let unread_count = chat_panel_header.find('.unread-count').addClass('d-none').text();

        let unread_count = conversation_panel.find('[data-conversation-id='+ chat.conversation.id +'].active .unread-count').text();
        if (parseInt(unread_count.trim()) > 0) {
        	chat.trigger(EVENTS.WSCHAT_ON_READ_ALL_MESSAGE);
        }
    });

    chat_panel_header.on('click', '.user-meta-info-toggle', function () {
    	chat.$el.find('#elex-ws-chat-search').removeClass('active');
    	chat.$el.find('#elex-ws-chat-customer-info').toggleClass('active');
    });

    chat_panel_header.on('click', '.search-box-toggle', function () {
    	chat.$el.find('#elex-ws-chat-customer-info').removeClass('active');
    	chat.$el.find('#elex-ws-chat-search').toggleClass('active');
    });

    jQuery(document).on('click', '.elex-ws-chat-search-close-btn', function (e) {
    	e.preventDefault();
    	chat.$el.find('#elex-ws-chat-search').removeClass('active');
    });

    jQuery(document).on('click', '.elex-ws-chat-customer-info-close-button', function (e) {
    	e.preventDefault();
    	chat.$el.find('#elex-ws-chat-customer-info').removeClass('active');
    });

    conversation_panel.on('click', '[data-conversation-id]:not(.active)', function() {
        chat_panel.html('');
        let item = jQuery(this);
        let converssation_id = item.data('conversation-id');
        conversation_panel.find('[data-conversation-id]').removeClass('active');
        item.addClass('active');
        chat.connector.join_conversation(converssation_id);
    });

    // chat_panel.on('scroll', function () {
    //     if (DISABLE_SCROLL_LOCK) {
    //         SCROLL_PAUSED = false;
    //         return;
    //     }
    //     if (this.scrollTop < SCROLL_OFFSET) {
    //         if (PAST_REQUEST_IS_PENDING === false) {
    //             PAST_REQUEST_IS_PENDING = true;
    //             chat.connector.get_messages({
    //                 no_pong: true,
    //                 after: 0,
    //                 before: MESSAGE_INFO.min
    //             });
    //             setTimeout(() => PAST_REQUEST_IS_PENDING = false, 500);
    //         }
    //     }

    //     if (this.offsetHeight + this.scrollTop >= this.scrollHeight - SCROLL_OFFSET) {
    //         SCROLL_PAUSED = false;
    //     } else {
    //         SCROLL_PAUSED = true;
    //     }
    //         console.log(this.scrollHeight);
    // });
    chat_panel.on('scroll', function () {
        if (DISABLE_SCROLL_LOCK) {
            SCROLL_PAUSED = false;
            return;
        }
        if (this.scrollTop < SCROLL_OFFSET) {
            if (!PAST_REQUEST_IS_PENDING) {
                PAST_REQUEST_IS_PENDING = true;
                chat.connector.get_messages({
                    no_pong: true,
                    after: 0,
                    before: MESSAGE_INFO.min
                }).then(() => {
                    PAST_REQUEST_IS_PENDING = false;
                }).catch((error) => {
                    console.error('Error fetching messages:', error);
                    PAST_REQUEST_IS_PENDING = false;
                });
            }
        }

        if (this.offsetHeight + this.scrollTop >= this.scrollHeight - SCROLL_OFFSET) {
            SCROLL_PAUSED = false;
        } else {
            SCROLL_PAUSED = true;
        }
    });

    const emojiPicker = document.getElementById('wschat_emoji_picker');
    const emoji = new EmojiButton({
        style: 'twemoji',
        twemojiOptions: {
         base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
        },
        rootElement: document.getElementsByClassName('chat-box-footer')[0],
        // position: 'top'
    });


    emojiPicker.addEventListener('click', function() {
        emoji.togglePicker();
    });

    emoji.on('emoji', function(selection) {
        message_input.val(message_input.val() + selection.emoji).focus();
        setTimeout(() => message_input.focus(), 500)
    });


    // Attachment toggler
    chat.$el.find('#attachment_picker').click(function (e) {
        e.preventDefault();
        jQuery(this).parent().find('.attachment-list').toggleClass('active');
    });

    chat.$el.find('.attachment-list').on('click','button', function () {
        jQuery(this).parents('.attachment-list').toggleClass('active');
    });

    chat.$el.find('.agent-status-dropdown').on('click', 'a.dropdown-item', function (e) {
		e.preventDefault();

		jQuery(this).parents('.agent-status-dropdown').find('button').html(this.innerHTML);

		const data = {
			action: 'wschat_set_agent_status',
			status: this.getAttribute('data-status'),
		};

		jQuery.post(ajaxurl, data, function (res) {
			console.log(res);
		});
    });

    const status = chat.$el.find('.agent-status-dropdown button[data-status]').data('status');
    if (status) {
		const html = jQuery('.agent-status-dropdown a.dropdown-item[data-status='+status+']').html();
		chat.$el.find('.agent-status-dropdown button[data-status]').html(html);
    }
});
