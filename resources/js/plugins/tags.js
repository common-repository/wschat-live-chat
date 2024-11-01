import { EVENTS } from '../events';
import { createPopper } from '@popperjs/core';
import {render} from '@wordpress/element';
import { Tag } from './tag/tag'

export class TagsPlugin {
	/**
	 * Chat core object
	 */
	chat;

	constructor(chat){
		this.chat = chat;
	}

	init() {
		if ( ! this.chat.options.enable_tags) {
			return;
		}
		// this.chat.on(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, content => this.add_tags(content));
		this.chat.on(EVENTS.WSCHAT_ON_RENDER_MESSAGE, params => this.add_tags(params.message_item, params.row));

		this.chat.$el.on('click', '.chat-bubble  .show-tags', (e) => {
			e.preventDefault();
			this.chat.$el.find('.tags-list').addClass('visually-hidden');
            const list = jQuery(e.target).parents('.message-item').find('.tags-list');
            list.toggleClass('visually-hidden');
            createPopper(jQuery(e.target).parents('.message-item-action-badges')[0], list[0], {
                placement: 'right',
            });
		});

        jQuery('body').on('click', (e) => {
			if (jQuery(e.target).is('.message-item .chat-bubble')) {
				return;
			}

			if (jQuery(e.target).parents('.message-item .chat-bubble').length ) {
				return;
			}

			if (jQuery(e.target).parents('.tags-list').length) {
				return;
			}
			this.chat.$el.find('.tags-list').addClass('visually-hidden');
		});
	}

	add_tags(message_item, content) {
		if (content.is_agent === true) {
			return content;
		}

		const tag_id = content.body.tag;
		const tag = this.chat.options.tags.find(t => t.id == tag_id);
		const tags = tag_id ? this.chat.options.tags.filter(t => t.id != tag_id) : this.chat.options.tags;

		const temp = tags_list_template(tags, tag);
		message_item.find('.message-item-action-badges').append(temp);
        message_item.append('<div class="tags-list elex-ws-chat-untag visually-hidden"></div>');
        const list = message_item.find('.tags-list').data('message', content);
        render(<Tag key={content.id} message={content} chat={this.chat} />, list[0], () => {
            createPopper(message_item.find('.message-item-action-badges')[0], list[0], {
                placement: 'right',
            });
        });
	}

	rerender(message_item, tag_id) {
		const tag = this.chat.options.tags.find(t => t.id == tag_id);
		const tags = tag_id ? this.chat.options.tags.filter(t => t.id != tag_id) : this.chat.options.tags;

		const tag_template = jQuery(tags_list_template(tags, tag));
		message_item.find('.message-item-action-badge-tag').html(tag_template.html());
	}
}

const tags_list_template = (tags, selected_tag) => `
	<div class="message-item-action-badge-tag">
        <span class="badge p-0 shadow-none ">
			<a href="#" title="Add Tag" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="tooltip-white" class="${selected_tag ? '' : 'text-dark'} show-tags py-1 px-2 rounded-pill d-inline-block" ${selected_tag ? `style="color: #${selected_tag.color}"` : ''}>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                    viewBox="0 0 14.92 14.778">
                    <g id="Icon_feather-untag_blue"
                        data-name="Icon feather-untag blue"
                        transform="translate(-0.333 -0.333)">
                        <path id="Path_139" data-name="Path 139"
                            d="M13.727,8.94l-4.78,4.78a1.333,1.333,0,0,1-1.887,0L1.333,8V1.333H8L13.727,7.06a1.333,1.333,0,0,1,0,1.88Z"
                            fill="none" stroke="#${selected_tag ? selected_tag.color : '707070'}" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" />
                        <path id="Path_140" data-name="Path 140" d="M4.667,4.667h0"
                            transform="translate(1 1)" fill="none" stroke="#${selected_tag ? selected_tag.color : '707070'}"
                            stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" />
                    </g>
                </svg>
			</a>
		</span>
	</div>
`;
