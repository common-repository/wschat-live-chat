import {useState, render, useEffect} from '@wordpress/element';
import {EVENTS} from '../events'

export class SearchPlugin {
	constructor(chat) {
		this.chat = chat;
	}

	init() {
		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => this.render_view());

        // Get conv and message ids from url param and search to support tags
		this.chat.on(EVENTS.WSCHAT_ON_SET_CONVERSATION, () => {
            const url = new URL(location.href);
            const conversation_id = url.searchParams.get('conversation_id');
            const message_id = url.searchParams.get('message_id');

            if ( message_id && conversation_id == this.chat.conversation.id) {
                setTimeout(() => this.fetch_messages(message_id), 1000);
            }
		});

        // On search item click or tag link, while fetching the messages check for the searched item and highlight
		this.chat.on(EVENTS.WSCHAT_ON_FETCH_MESSAGES, () => {
			if (! this.chat.conversation.search_message_id) {
				return;
			}

			const item = this.find_message_item_by_id(this.chat.conversation.search_message_id);

			if (item) {
				this.scroll_to(item);
			}
		});
	}

	render_view() {
    	this.chat.$el.find('.elex-ws-chat-search-close-btn').trigger('click');
		render(<SearchProductsAndChat chat={this.chat} plugin={this} />, document.getElementById('elex-ws-chat-search'))
	}

	fetch_messages(message_id) {
        this.chat.conversation.search_message_id = message_id;
		const item = this.find_message_item_by_id(message_id);

		if (item.length) {
			return this.scroll_to(item);
		}

		const data = {
			after: 0,
			before: parseInt(message_id, 10) + 1,
			limit: 5,
			no_pong: true,
		};

		this.chat.connector.get_messages(data);
		data.before = 0;
		data.after = message_id;
		this.chat.connector.get_messages(data);
	}

	find_message_item_by_id(id) {
		return jQuery('.message-item[data-message-id=' + id + ']');
	}

	scroll_to(item) {
        this.chat.conversation.search_message_id = undefined;
		setTimeout(() => {
			jQuery('.message-item.active').removeClass('active')
			item.addClass('active');
			if (item.length) {
				const top = item.offset().top - 500;
                const chat_panel = this.chat.$el.find('.chat-panel');
				chat_panel.scrollTop(chat_panel.scrollTop() + top);
			}
        }, 500);
	}
}

export const SearchProductsAndChat =  props => {
	const [params, setParams] = useState({mode: 'posts', search: ''});
	const [posts, setPosts] = useState([]);
	const [messages, setMessages] = useState([]);

	const search = () => {
		const data = {
			search: params.search.trim(),
			type: params.mode,
			conversation_id: props.chat.conversation.id,
			action: 'wschat_admin_get_messages'
		};

		if (data.search.length === 0) {
			setPosts([]);
			setMessages([]);
		}

		if (data.search.length < 2) {
			return;
		}

		if (data.type === 'posts') {
			data.action = 'wschat_admin_search_posts';
		}

		const progress = jQuery(document).find('.wschat-search-progress-bar');
		progress.removeClass('d-none');

		jQuery.post(props.chat.options.api.endpoint, data, res => {
			if (data.type == 'messages') {
				setMessages(res.data.messages);
			} else {
				setPosts(res.data.posts);
			}
			progress.addClass('d-none');
		}).fail(f => {
			progress.removeClass('d-none');
		});
	}

	const onModeChange = mode => {
		params.mode = mode;
		setParams(params);
		search();
	};

	const onSearchChange = (query) => {
		params.search = query;
		setParams(params);
		search();
	};


	return (
		<div>
			<Header plugin={props.plugin} chat={props.chat} onModeChange={onModeChange} onSearchChange={onSearchChange} mode={params.mode} search={params.search} />
			<div className="wschat-search-progress-bar d-none mb-3">
				<div className="progress">
  					<div style={{width: '100%'}} className="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" ></div>
				</div>
			</div>
            {params.mode === 'messages' && messages.length ?
                <MessageSearchResults messages={messages} onClickMessageItem={message => props.plugin.fetch_messages(message.id)} /> :
                ''
            }
            {params.mode === 'posts' && posts.filter(p => p.posts.length).length ?
                <PostSearchResults chat={props.chat} posts={posts} /> : ''
            }
            {params.search.length && ((params.mode === 'messages' && messages.length === 0) || (params.mode === 'posts' && posts.filter(p => p.posts.length).length === 0)) ?
            	<div className="alert alert-warning">No results found</div>
            : '' }
		</div>
	);
}

export const Header = (props) => {
	const [mode, setMode] = useState(props.mode);
	const [search, setSearch] = useState(props.search || '');

	const onModeChange = e => {
		setMode(e.target.value);
		props.onModeChange(e.target.value);
	}

	const onSearchChange = e => {
		setSearch(e.target.value);
		props.onSearchChange(e.target.value);
	}

	useEffect(() => {
		props.onSearchChange(search);
		if (props.chat.options.search ) {
			const mode = props.chat.options.search.modules.indexOf('posts') > -1 ? 'posts' : 'messages';
			setMode(mode);
		}
		props.onModeChange(mode);
	}, [search])

	return (
		<div className="mb-3">
			<div className="d-flex gap-2 align-items-center">
				<button onClick={() => setSearch('')} className="btn btn-sm rounded-circle elex-ws-chat-search-close-btn">
					<svg xmlns="http://www.w3.org/2000/svg" width="9.313" height="9.313"
						viewBox="0 0 9.313 9.313">
						<path id="Icon_ionic-md-close" data-name="Icon ionic-md-close"
							d="M12.656,4.275l-.931-.931L8,7.069,4.275,3.344l-.931.931L7.069,8,3.344,11.725l.931.931L8,8.931l3.725,3.725.931-.931L8.931,8Z"
							transform="translate(-3.344 -3.344)" />
					</svg>
				</button>
				<p className="mb-0"><b>Search</b></p>
			</div>
			<div className="input-group border border-primary rounded-3">
				<input type="text" className="form-control" value={search} onChange={onSearchChange} />
				<select
					name="elex-ws-chat-select-search"
					id="elex-ws-chat-select-search"
					value={mode}
					onChange={onModeChange}
					className="input-group-text border border-primary border-0 border-start bg-white text-primary pe-4">
					{! props.chat.options.search || props.chat.options.search.modules.indexOf('posts') >= 0 ? <option value="posts">Product and Docs</option> : ''}
					{! props.chat.options.search || props.chat.options.search.modules.indexOf('messages') >= 0 ? <option value="messages">Chats</option> : ''}
				</select>
			</div>
		</div>
	);
}

export const PostSearchItem = props => {
	const sendPostLink = e => {
			props.chat.send_message({
				// Type is text by default now, it needs to changed based on the selection content
				wschat_ajax_nonce: wschat_ajax_obj.nonce,
				type: 'text',
				'content[text]': props.post.url,
				'content[url]': props.post.url,
			});
	};

	return (
		<div className="p-1 shadow-sm d-flex gap-2 mb-2">
            {props.post.thumbnail ? <div className="elex-ws-chat-list-profile-pic">
				<div className="ratio ratio-1x1 rounded-3 overflow-hidden">
					<img src={props.post.thumbnail} alt=" " />
				</div>
			</div> : ''}
			<div className="flex-fill ">
				<h6>{props.post.title}</h6>
				{props.post.price ? <small>{props.post.price}</small> : ''}
			</div>
			<button className="btn btn-primary px-3 rounded-3" onClick={sendPostLink}>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <line x1="22" y1="2" x2="11" y2="13" />
                  <polygon points="22 2 15 22 11 13 2 9 22 2" />
                </svg>
			</button>
		</div>
	);
}

const MessageSearchResults = props => {
	return (
		<div className="elex-ws-chat-live-convo-search">
			<div className="accordion" id="chatsearch">
				<div className="accordion-item border-0">
					<h2 className="accordion-header" id="chat-search-heading">
						<button className="accordion-button bg-white py-1 px-0 text-dark shadow-none"
							type="button" data-bs-toggle="collapse"
							data-bs-target="#chat-search-collapse" aria-expanded="true"
							aria-controls="chat-search-collapse">
							Chats
						</button>
					</h2>
					<div id="chat-search-collapse" className="accordion-collapse collapse show"
						aria-labelledby="chat-search-heading" data-bs-parent="chatsearch">
						<div className="accordion-body py-1 px-0  overflow-auto">
							{props.messages.map(message => {
								return <MessageItem
									message={message}
									onClickItem={() => props.onClickMessageItem(message)}
									key={message.id} />
							})}
							{props.messages.length === 0 ? <p> No results found </p> : ''}
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}

const MessageItem = props => {
	return (
		<a href="#" className="mb-3 text-dark text-decoration-none" onClick={props.onClickItem} >
			<div><small>{props.message.created_at}</small> </div>
			<h6><span>{props.message.user.name}</span>: <span dangerouslySetInnerHTML={{__html: props.message.body.text}} / ></h6>
		</a>
	);
}

const PostSearchResults = props => {
	return (
		<div className="elex-ws-chat-live-docs-search">
		{props.posts.map(post_type => {
            if (post_type.posts.length === 0 ) {
                return '';
            }
			return (<div className="accordion" key={post_type.type} id={"accordion_" + post_type.type}>
				<div className="accordion-item border-0">
					<h2 id={"heading_" + post_type.type}>
						<button
							className="border-0 accordion-button bg-white py-1 px-0 text-dark shadow-none"
							type="button" data-bs-toggle="collapse" data-bs-target={"#collapse_" + post_type.type}
							aria-expanded="true" aria-controls={"collapse_" + post_type.type}>
							{post_type.title}
						</button>
					</h2>
					<div id={"collapse_" + post_type.type} className=" accordion-collapse collapse show"
						aria-labelledby={"heading_" + post_type.type} data-bs-parent={"#accordion_" + post_type.type}>
						<div className="accordion-body px-0">
							{post_type.posts.map(post => <PostSearchItem chat={props.chat} key={post.url} post={post} />)}
						</div>
					</div>
				</div>
				</div>)
		})}
		</div>
	)
};
