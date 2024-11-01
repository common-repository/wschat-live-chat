export default (conversation) => jQuery(`
	<li class="nav-item elex-ws-chat-contact" >
		<a class="nav-link wschat-admin-convo-nav rounded-0 gap-2 d-flex p-2 align-items-center ${conversation.is_selected ? 'active' : ''}" data-conversation-id="${conversation.id}">
            <div class="position-relative">
                <div
                    class="elex-ws-chat-admin-img-shadow ratio ratio-1x1 rounded-circle overflow-hidden elex-ws-chat-list-profile-pic ">
                    <img src="${conversation.user.meta.avatar}"
                        alt="">
                </div>

                <div class="position-absolute bottom-0 end-0">
                    ${conversation.is_user_online ? `<span class="badge p-2 online shadow  rounded-circle">
                        <span class="visually-hidden">Online</span>
                    </span>` : `<span class="badge p-2 offline shadow  rounded-circle">
                        <span class="visually-hidden">Offline</span>
                    </span>`}

                    <span class="d-none badge p-2 busy shadow rounded-circle">
                        <span class="visually-hidden">Busy</span>
                    </span>
                </div>

            </div>

            <div class="w-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex gap-2 align-items-start">
                            <h6 >${conversation.user.meta.name}</h6>
                            ${conversation.invited ? `<span class="elex-ws-chat-contact-joined">${bell_icon}</span>` : ''}
                        </div>

                       <div class="xs">${!conversation.user.meta.email && conversation.meta.pre_chat_form && conversation.meta.pre_chat_form.email ? conversation.meta.pre_chat_form.email.value : conversation.user.meta.email}</div>
                    </div>

                    <div class="d-flex flex-column align-items-end gap-1">
                        ${conversation.unread_count ? `<div class="elex-ws-chat-contact-unread-message p-1 lh-1 xs unread-count">
                            ${conversation.unread_count}
                        </div>` : ''}
                        ${conversation.agent ? `<div class="btn btn-sm btn-outline-dark p-1 lh-1 elex-wschat-agent-name-root">${conversation.agent.display_name}</div>` : ''}
                    </div>
                </div>
            </div>
        </a>
	</li>
	`);

const bell_icon = `
<svg
  xmlns="http://www.w3.org/2000/svg"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
>
  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
  <path d="M13.73 21a2 2 0 0 1-3.46 0" />
</svg>
`;
