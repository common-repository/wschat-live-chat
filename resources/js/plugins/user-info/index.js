import {render} from '@wordpress/element';
import { EVENTS } from '../../events';
import UserInfoCard from './user-info-card';
import Header from './header';
import { WooOrders } from './woocommerce';
import { SiteNavigation } from './navigation';
import { DeleteConversation } from './delete_conversation';
import { AgentInfo } from './agents';
import { PrechatFormComponent } from '../prechat-form/prechat-form';

export class UserInfoPlugin {
	constructor(chat) {
		this.chat = chat;
	}

	init() {
		if (!this.chat.options.is_admin) {
			return;
		}

		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
            if (! this.chat.conversation) {
                return;
            }
    		this.chat.$el.find('.elex-ws-chat-customer-info-close-button').trigger('click');
			const container = document.getElementById('elex-ws-chat-customer-info')
			render(<UserInfo chat={this.chat}/>, container, () => {
				this.chat.trigger(EVENTS.WSCHAT_ON_RENDER_USER_META, jQuery(container));
			});
		});
	}
}

export const UserInfo = props => {
    const onClose = () => {
        jQuery('#elex-ws-chat-customer-info').removeClass('active');
    }

	return (
		<div className="ms-auto  p-2 shadow bg-white elex-ws-chat-customer-info-content" id="elex-ws-chat-customer-info-content">
			<Header onClose={onClose}/>
			<UserInfoCard conversation={props.chat.conversation} user={props.chat.conversation.user}/>
            <AgentInfo
            	chat={props.chat}
            	conversation={props.chat.conversation}
            	agents={props.chat.conversation.agents}
            	participants={props.chat.conversation.participants}
            	canAddAgents={wschat_ajax_obj.capabilities.administrator || wschat_ajax_obj.capabilities.wschat_invite_agent }
            />
            <WooOrders conversation={props.chat.conversation} />
            <SiteNavigation user={props.chat.conversation.user} />
            <PrechatFormComponent key={props.chat.conversation.id} conversation={props.chat.conversation} />
            	{ wschat_ajax_obj.capabilities.administrator || wschat_ajax_obj.capabilities.wschat_delete_chat ? <DeleteConversation chat={props.chat} /> : '' }
		</div>
	);
}

