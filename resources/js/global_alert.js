import { pusher_auth_action } from './admin_pusher_connector';
import { ChatNotificationAlertPlugin} from './plugins/chat_alert_notifications';
import Echo from 'laravel-echo';

const AlertOnNewMessage = params => {
	let unread_count = parseInt( params.unread_count ) || 0;
	const alert_plugin = new ChatNotificationAlertPlugin();
	alert_plugin.set_url(wschat_ajax_obj.settings.alert_tone_url);

    jQuery(window).on('click', () => {
        alert_plugin.setup_player();
    });

    jQuery(window).on('wschat_user_unread_count', (e, count) => {
    	if (count ===parseInt( unread_count )) {
    		return;
    	}

    	unread_count = parseInt( count );

    	unread_count && alert_plugin.play();
    });
}

const GetUserUnreadCountPusher = () => {
	const options = {
		key: wschat_ajax_obj.settings.pusher.app_key,
		cluster: wschat_ajax_obj.settings.pusher.cluster,
		broadcaster: 'pusher',
		forceTLS: true,
		authEndpoint: ajaxurl,
    	auth: {
    		params: {
    			wschat_ajax_nonce: wschat_ajax_obj.nonce,
    			action: pusher_auth_action
    		}
    	}
	};
	const echo = new Echo(options);
	const channel = echo.join('agent_' + wschat_ajax_obj.user_id);

	channel.listen('.new_message', () => {
		wschat_ajax_obj.unread_count++;
		jQuery(window).trigger('wschat_user_unread_count', wschat_ajax_obj.unread_count);
	});
}

const GetUserUnreadCountHttp = () => {
	const data ={
    	wschat_ajax_nonce: wschat_ajax_obj.nonce,
    	action: 'wschat_get_agent_unread_count',
	};
	jQuery.post(ajaxurl, data, res => {
		if (res.status === false) {
			return;
		}

		jQuery(window).trigger('wschat_user_unread_count', res.data.unread_count);
	}).fail(() => {
	});
}

jQuery(function () {
	jQuery(window).on('click', function () {
		AlertOnNewMessage({unread_count: wschat_ajax_obj.unread_count});
		if (wschat_ajax_obj.settings.communication_protocol === 'pusher') {
			GetUserUnreadCountPusher();
			return;
		}

		setInterval(GetUserUnreadCountHttp, 2000);
	});
});
