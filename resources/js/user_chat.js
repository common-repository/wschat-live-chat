import {WSChat} from './chat';
import { UserApiConnector } from './user_api_connector';
import { UserPusherConnector } from './user_pusher_connector';
import { EVENTS } from './events';
import { EmojiButton } from '@joeattardi/emoji-button';
import {DialogflowPlugin} from './plugins/dialogflow';
import moment from 'moment-timezone';

jQuery(document).ready(function () {
    jQuery('.elex-ws-chat-diff-inputs-btn').click(function (){
        // jQuery(this).toggleClass('rotate');
        if(jQuery('.elex-ws-chat-diff-inputs').hasClass('active')){
            jQuery('.elex-ws-chat-convo').removeClass('elex-ws-chat-convo-body-blur');
            jQuery('.elex-ws-chat-diff-inputs-btn').removeClass('rotate');
        }else{
            jQuery('.elex-ws-chat-convo').addClass('elex-ws-chat-convo-body-blur');
            jQuery('.elex-ws-chat-diff-inputs-btn').addClass('rotate');
        }
    })

    const wrapper = jQuery('.wschat-wrapper');

    if (wrapper.length === 0) {
    	return;
    }

    const CHAT_BUBBLE_TEMPLATE = `
			<div class="align-items-start d-flex elex-ws-chat-convo-text-box flex-column mb-4 message-item {{OFFSET}} " data-message-id="{{MESSAGE_ID}}">
				<div class="d-flex flex-column mx-3">
					<div class="chat-bubble chat-bubble--{{POS}} position-relative">
						{{CONTENT}}
					</div>
					<div class="xs wschat-text-gray d-flex justify-content-end {{OFFSET}}">
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
    };

    jQuery.ajaxSetup({
        data: {
            wschat_ajax_nonce: wschat_ajax_obj.nonce
        }
    });

    var chat = new WSChat(jQuery('.wschat-wrapper'), {
        connector: wschat_ajax_obj.settings.communication_protocol === 'pusher' ? UserPusherConnector : UserApiConnector,
        api: {
            endpoint: wschat_ajax_obj.ajax_url,
            interval: 3000,
            wschat_ajax_nonce: wschat_ajax_obj.nonce,
            pusher: {
				key: wschat_ajax_obj.settings.pusher.app_key,
				cluster: wschat_ajax_obj.settings.pusher.cluster,
			}
        },
        prechatform: wschat_ajax_obj.settings.prechatform,
        alert: {
        	url: wschat_ajax_obj.settings.alert_tone_url
        },
        plugins: [
        	DialogflowPlugin
        ],
        header: {
        	status_text: wschat_ajax_obj.settings.widget_status === 'online' ? wschat_ajax_obj.settings.header_online_text : wschat_ajax_obj.settings.header_offline_text,
        }
    });

    const chat_popup = chat.$el.find('.wschat-popup');
    const chat_panel = chat.$el.find('.chat-panel');
    const chat_panel_header = chat.$el.find('.chat-panel-header');
    const chat_tray_box = chat.$el.find('.chat-box-tray');
    const message_input = jQuery('#wschat_message_input');
    const MESSAGE_INFO = {
        min: 0,
        max: 0,
    };
    let PAST_REQUEST_IS_PENDING = false;
    let SCROLL_PAUSED = false;
    let DISABLE_SCROLL_LOCK = false;
    const SCROLL_OFFSET = 100;

    chat_panel_header.find('.status').text(chat.options.header.status_text);

    if (wschat_ajax_obj.settings) {
		for(let key in wschat_ajax_obj.settings.colors) {
			key && chat.$el.get(0).style.setProperty(key,  '#' +wschat_ajax_obj.settings.colors[key]);
		}

		if (wschat_ajax_obj.settings.font_family) {
			chat.$el.css({'font-family': wschat_ajax_obj.settings.font_family})
		}

		chat_panel_header.find('.username').text(wschat_ajax_obj.settings.header_text);
    }


    // TODO: Update this to match user case
    chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
        message_input.focus();
        MESSAGE_INFO.min = 0;
        MESSAGE_INFO.max = 0;
        DISABLE_SCROLL_LOCK = true;

        setTimeout(() => DISABLE_SCROLL_LOCK = false, 1000);
    });

    chat.on(EVENTS.WSCHAT_ON_FETCH_MESSAGES, (data) => {
    	const messages = data.messages;
    	messages.sort((a,b) => a.id-b.id ? -1 : 1);
        for (let i = 0; i < messages.length; i++) {
            let row = data.messages[i];

            if (row.is_agent === true) {
                BUBBLE_TEMPLATE_DEFAULTS['{{OFFSET}}'] = 'justify-content-start';
                BUBBLE_TEMPLATE_DEFAULTS['{{POS}}'] = 'left';
            } else {
                BUBBLE_TEMPLATE_DEFAULTS['{{OFFSET}}'] = 'align-items-end ms-auto';
                BUBBLE_TEMPLATE_DEFAULTS['{{POS}}'] = 'right';
            }

            BUBBLE_TEMPLATE_DEFAULTS['{{MESSAGE_ID}}'] = row.id;
            BUBBLE_TEMPLATE_DEFAULTS['{{CONTENT}}'] = row.body.formatted_content;
            BUBBLE_TEMPLATE_DEFAULTS['{{TIMESTAMP}}'] = moment.tz(row.created_at, 'utc').tz(moment.tz.guess()).format('LLL');

            let row_template = CHAT_BUBBLE_TEMPLATE;
            if (row.type === 'info') {
                row_template = `
                  <div class="row g-0 w-100 message-item text-center xs pb-2" data-message-id="{{MESSAGE_ID}}">
                    <div class="text-center p-2 mb-4 shadow w-50 m-auto rounded">
                        ${row.body.text}
                    <div>
                  </div>
                `;
            }

            row_template = row_template.replace(new RegExp(Object.keys(BUBBLE_TEMPLATE_DEFAULTS).join('|'), 'g'), match => BUBBLE_TEMPLATE_DEFAULTS[match]);

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

            if (MESSAGE_INFO.min === 0) {
               scrollIfNotPaused();
            }

            MESSAGE_INFO.min = MESSAGE_INFO.min || row.id;
            MESSAGE_INFO.max = MESSAGE_INFO.max || row.id;
        }

        if (DISABLE_SCROLL_LOCK === true) {
            scrollIfNotPaused();
        }
    });

    chat.on(EVENTS.WSCHAT_ON_SENT_A_MESSAGE, (data) => {
    	send_btn.prop('disabled', false);
    	send_btn.html(chat.submit_btn_html);
    	if (data.data.offline_reply) {
            BUBBLE_TEMPLATE_DEFAULTS['{{OFFSET}}'] = 'mb-2';
            BUBBLE_TEMPLATE_DEFAULTS['{{POS}}'] = 'left';
            BUBBLE_TEMPLATE_DEFAULTS['{{MESSAGE_ID}}'] = data.data.id;
            BUBBLE_TEMPLATE_DEFAULTS['{{CONTENT}}'] = data.data.offline_reply;
            BUBBLE_TEMPLATE_DEFAULTS['{{TIMESTAMP}}'] = '';

            let row_template = CHAT_BUBBLE_TEMPLATE;

            row_template = row_template.replace(new RegExp(Object.keys(BUBBLE_TEMPLATE_DEFAULTS).join('|'), 'g'), match => BUBBLE_TEMPLATE_DEFAULTS[match]);

            setTimeout(() => {
            	row_template = jQuery(row_template).data('messages-id', MESSAGE_INFO.max);
                chat_panel.find('[data-message-id]').last().after(row_template);
            	chat_panel.append(row_template);
            	scrollIfNotPaused();
            }, 1000);
    	}
    });

    chat.on(EVENTS.WSCHAT_ON_PONG, (data) => {
        let drawer = chat_panel_header.find('.status');
		let unread_count_el = chat.$el.find('.unread-count');

        unread_count_el.text(data.unread_count);

        if (data.unread_count) {
            unread_count_el.removeClass('d-none');
        } else {
            unread_count_el.addClass('d-none');
        }
        drawer.text(data.status);
        if (data.is_online) {
        	drawer.parent().find('.rounded-circle').removeClass('bg-danger').addClass('bg-success');
        } else {
        	drawer.parent().find('.rounded-circle').removeClass('bg-success').addClass('bg-danger');
        }
    });

    const scrollIfNotPaused = () => {
        if (SCROLL_PAUSED === false || DISABLE_SCROLL_LOCK === true) {
            chat_panel[0].scrollTop = chat_panel[0].scrollHeight;
        }
    }

    const send_btn = jQuery('#wschat_send_message').on('click', function() {
        const btn = jQuery(this);
        let msg = message_input.val();

        if (msg.trim() === '' && chat.trigger(EVENTS.WSCHAT_CAN_SEND_EMPTY_MESSAGE, false, true) === false) {
            return false;
        }

        chat.send_message({
            wschat_ajax_nonce: wschat_ajax_obj.nonce,
            type: 'text',
            'content[text]': message_input.val()
        });
        btn.prop('disabled', true);
        if (! chat.submit_btn_html) {
        	chat.submit_btn_html =  btn.html();
        }
        btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`);

        message_input.val('').focus().trigger('change');
    });

    message_input.keyup(function(e) {
        e.key === 'Enter' && send_btn.click();
    });

    message_input.on('click', function() {
        let unread_count = jQuery(document).find('.unread-count').addClass('d-none').text();

        if (parseInt(unread_count) > 0) {
        	chat.trigger(EVENTS.WSCHAT_ON_READ_ALL_MESSAGE);
        }
    });

    // chat_panel.on('scroll', function () {
    //     if (DISABLE_SCROLL_LOCK) {
    //         SCROLL_PAUSED = false;
    //         return;
    //     }
    //     if (this.scrollTop < SCROLL_OFFSET && chat_panel.children().length) {
    //         if (PAST_REQUEST_IS_PENDING === false) {
    //             PAST_REQUEST_IS_PENDING = true;
    //             chat.connector.get_messages({
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
    // });
    chat_panel.on('scroll', function () {
        if (DISABLE_SCROLL_LOCK) {
            SCROLL_PAUSED = false;
            return;
        }
    
        if (this.scrollTop < SCROLL_OFFSET && chat_panel.children().length) {
            if (!PAST_REQUEST_IS_PENDING) {
                PAST_REQUEST_IS_PENDING = true;
                chat.connector.get_messages({
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

    const resizeChat = () => {
		const BREAKPOINT = 576;
		const window_height = jQuery(window).innerHeight();

		if (jQuery(window).width() <= BREAKPOINT) {
			const height = window_height - (
                chat_panel_header.height()*2 + chat_tray_box.height() + chat_popup.find('.powered-by-tag').height()
            );
			chat.trigger(EVENTS.WSCHAT_ON_RESIZE_WIDGET, height);
		} else {
			chat.trigger(EVENTS.WSCHAT_ON_RESIZE_WIDGET, window_height/2);
		}
    };

    jQuery(window).resize(() => resizeChat());

    const emojiPicker = document.getElementById('wschat_emoji_picker');
    const emoji = new EmojiButton({
        style: 'twemoji',
        twemojiOptions: {
         base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
        },
        rootElement: emojiPicker.parentElement,
        position: 'top',
    });


    emojiPicker.addEventListener('click', function() {
        emoji.togglePicker();
        if (chat.$el.find('.attachment-list').hasClass('active') ) {
        	chat.$el.find('#attachment_picker').click();
        }
    });

    emoji.on('emoji', function(selection) {
        message_input.val(message_input.val() + selection.emoji).focus();
        setTimeout(() => message_input.focus(), 500)
    });

    // Attachment toggler
    chat.$el.find('#attachment_picker').click(function (e) {
        e.preventDefault();
        const list = chat.$el.find('.attachment-list').toggleClass('active');
        list.find('.attachment-list-item').sort(() => -1).each((i, item) => {
            setTimeout( () => jQuery(item).toggleClass('show'), i*100)
        });
    });



    chat.$el.find('.attachment-list').on('click','button', function () {
        chat.$el.find('#attachment_picker').click();
    });

    chat.$el.find(".elex-ws-chat-widget-convo-box").hide();
    chat.$el.find(".elex-ws-chat-widget-open-convo-box").click(function() {
    	chat.$el.trigger('on_widget_open')
        chat.$el.find(".elex-ws-chat-widget-convo-box").show("slow", scrollIfNotPaused);
        chat.$el.find(".elex-ws-chat-widget-open-convo-box").hide("slow");
    });
    chat.$el.find(".elex-ws-chat-widget-close-convo-box").click(function() {
    	chat.$el.trigger('on_widget_close')
        chat.$el.find(".elex-ws-chat-widget-convo-box").hide("slow");
        chat.$el.find(".elex-ws-chat-widget-open-convo-box").show("slow");
    });

    chat.on(EVENTS.WSCHAT_ON_UPLOAD_FILE_PROGRESS, percent => {
		chat.$el.find('.elex-ws-chat-upload-progress').removeClass('d-none')
			.find('.progress-percentage').text(parseInt(percent) + '%');
        if (parseInt(percent) === 100) {
            chat.$el.find('.elex-ws-chat-upload-progress').addClass('d-none');
        }
    });

    chat.on(EVENTS.WSCHAT_ON_SENT_A_MESSAGE, () => {
		chat.$el.find('.elex-ws-chat-upload-progress').addClass('d-none');
    });

    if (window.localStorage) {
    	chat.$el.on('on_widget_open', () => {
    		localStorage.setItem('wschat_widget_state', 'open');
    	});

    	chat.$el.on('on_widget_close', () => {
    		localStorage.setItem('wschat_widget_state', 'close');
    	});

    	setTimeout(() => {
    		const widget_state = localStorage.getItem('wschat_widget_state');
    		if (widget_state === 'open') {
				chat.$el.find('.elex-ws-chat-widget-open-convo-box').trigger('click');
    		}
    	}, 500);
    }
});
