	<div class="wschat-wrapper elex-ws-chat-wrap">
	<div class="d-none no-conversation-alert alert alert-warning m-1">
		<p class="text-danget text-center m-0"><?php echo esc_attr__( 'No conversations found', 'wschat' ); ?></p>
	</div>
		<div class="p-2 d-flex gap-3 vh-95 ">

			<!-- ================side panel=============== -->
			<div class="elex-ws-chat-side-panel h-100 bg-white">
				<div class="rounded-3 primary-shadow overflow-hidden h-100">
					<div class="wschat-bg-primary d-flex gap-2 p-2 align-items-center ">
						<div class="elex-ws-chat-profile-img">
							<div class="ratio ratio-1x1 rounded-circle overflow-hidden elex-ws-chat-admin-img-shadow">
								<img src="<?php echo esc_html( get_avatar_url( wp_get_current_user()->ID ) ); ?>" alt="">
							</div>
						</div>

						<div class="wschat-text-primary">
							<h6><?php echo esc_html( wp_get_current_user()->display_name ); ?></h6>
							<div class="btn-group agent-status-dropdown">
							<button data-status="<?php esc_attr_e( get_user_meta( get_current_user_id(), 'wschat_online_status', true ) ); ?>" type="button" class="btn btn-sm wshat-bg-priamry wschat-text-primary  border-white rounded-pill text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
										<span class="badge me-2 p-1  rounded-circle online">
											<span class="visually-hidden">online</span>
										</span>
									<?php esc_html_e( 'Online', 'wschat' ); ?>
								</button>
								<ul class="dropdown-menu">
									<li class=" position-relative">
										<a class="dropdown-item" href="#" data-status="online">
											<span class="badge me-2 p-1  rounded-circle online">
												<span class="visually-hidden">online</span>
											</span>
											<?php esc_html_e( 'Online', 'wschat' ); ?>
										</a>
									</li>
									  <li class="">
										<a class="dropdown-item" href="#" data-status="offline">
										<span class="badge me-2 p-1 rounded-circle offline">
											<span class="visually-hidden">offline</span>
										</span>
										<?php esc_html_e( 'Offline', 'wschat' ); ?>
										</a>
									</li>
									<li class="">
										<a class="dropdown-item" href="#" data-status="busy">
										<span class="badge me-2 p-1 rounded-circle busy">
											<span class="visually-hidden">busy</span>
										</span>
										<?php esc_html_e( 'Busy', 'wschat' ); ?>
										</a>
									</li>
								</ul>
							  </div>
						</div>
					</div>

					<!-- chat navigation -->
					<div class=" wschat-border-primary bg-primary bg-opacity-10 p-1 m-2 mb-3 rounded-3 ">
						<nav class="nav nav-pills nav-fill gap-1 conversation_types">
							<button class="nav-link wschat-admin-convo-nav active" aria-current="page" data-type="all"><?php esc_attr_e( 'All' ); ?></button>
							<div>
								<svg xmlns="http://www.w3.org/2000/svg" width="1.5" height="35" viewBox="0 0 1 18.679">
									<path id="Path_150" data-name="Path 150" d="M1757,805.078v17.679"
										transform="translate(-1756.5 -804.578)" fill="none" stroke="#707070"
										stroke-linecap="round" stroke-width="1" />
								</svg>
							</div>
							<button class="nav-link wschat-admin-convo-nav" aria-current="page" data-type="unassigned"><?php esc_attr_e( 'Unassigned' ); ?></button>
							<div>
								<svg xmlns="http://www.w3.org/2000/svg" width="1.5" height="35" viewBox="0 0 1 18.679">
									<path id="Path_150" data-name="Path 150" d="M1757,805.078v17.679"
										transform="translate(-1756.5 -804.578)" fill="none" stroke="#707070"
										stroke-linecap="round" stroke-width="1" />
								</svg>
							</div>
							<button class="nav-link wschat-admin-convo-nav" aria-current="page" data-type="my_chats"><?php esc_attr_e( 'My Chat' ); ?></button>
						</nav>
					</div>

					<!-- search option -->
					<div class="shadow-sm rounded-3 m-2 mb-3 d-flex ">
						<div class="p-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="18.414" height="18.414"
								viewBox="0 0 18.414 18.414">
								<g id="Icon_feather-search_gery" data-name="Icon feather-search gery"
									transform="translate(1 1)">
									<path id="Path_5" data-name="Path 5"
										d="M17.222,10.111A7.111,7.111,0,1,1,10.111,3,7.111,7.111,0,0,1,17.222,10.111Z"
										transform="translate(-3 -3)" fill="none" stroke="#5e5e5e" stroke-linecap="round"
										stroke-linejoin="round" stroke-width="2" />
									<path id="Path_6" data-name="Path 6" d="M20.517,20.517,16.65,16.65"
										transform="translate(-4.517 -4.517)" fill="none" stroke="#5e5e5e"
										stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
								</g>
							</svg>
						</div>

						<div class="input-group input-group-sm search-box">
							<input type="text" name="search-control" class="form-control border-0" placeholder="<?php esc_html_e( 'Search Users' ); ?>">
						</div>
					</div>

					<!-- chat list -->
					<div class="elex-ws-chat-contact-list">
						<ul class="nav flex-column nav-pills conversation-list">
						</ul>
					</div>
				</div>
			</div>

			<!-- ================Main-content=============== -->
			<div class="elex-ws-chat-main-content h-100 flex-fill position-relative bg-white">
				<div class="rounded-3 primary-shadow overflow-hidden h-100 d-flex flex-column conversation-list-is-empty d-none">
					<div class="w-50 m-auto">
						<img class="img-fluid"  src="<?php echo esc_url( \WSChat\Utils::get_resource_url( '/resources/img/conversation/conversation-list-is-empty.svg' ) ); ?>" />
						<h3 class="text-center my-3"><?php esc_attr_e( 'Your conversation list is empty', 'wsdesk' ); ?></h3>
					</div>
				</div>
				<div class="rounded-3 primary-shadow overflow-hidden h-100 d-flex flex-column select-a-conversation">
					<div class="w-50 m-auto">
						<img class="img-fluid" src="<?php echo esc_url( \WSChat\Utils::get_resource_url( '/resources/img/conversation/select-a-conversation.svg' ) ); ?>" />
						<h3 class="text-center my-3"><?php esc_attr_e( 'Select a Conversation', 'wsdesk' ); ?></h3>
					</div>
				</div>
				<div class="rounded-3 primary-shadow overflow-hidden h-100 d-flex flex-column d-none">

					<!-- chatbox header -->
					<div class="wschat-bg-primary d-flex justify-content-between p-2 align-items-center chat-panel-header">
						<div class="d-flex gap-2 align-items-center">
							<div class="elex-ws-chat-profile-img">
								<div class="ratio ratio-1x1 rounded-circle overflow-hidden elex-ws-chat-admin-img-shadow">
									<img class="conversation-user-avatar">
								</div>
							</div>

							<div class="wschat-text-primary">
								<h6 class="wschat-text-primary"><span class="username wschat-text-primary"></span> </h6>

								<div class="d-flex align-items-center gap-1">
									<span class="badge wschat-text-primary p-1 d-block rounded-circle status"></span><small>Online</small>
								</div>
							</div>
						</div>
						<div class="d-flex gap-2 conversation-header-actions">

							<!-- search Button -->
							<button class="btn btn-sm rounded-circle elex-ws-chat-search-open-btn search-box-toggle"
								data-bs-toggle="tooltip" data-bs-placement="bottom" title="Search"
								data-bs-custom-class="tooltip-white">
								<svg xmlns="http://www.w3.org/2000/svg" class="wschat-icon-color" width="17.016" height="17.016"
									viewBox="0 0 17.016 17.016">
									<g id="Icon_feather-search" data-name="Icon feather-search"
										transform="translate(-1.165 -1.165)">
										<path id="Path_42" data-name="Path 42"
											d="M15.667,8.833A6.833,6.833,0,1,1,8.833,2,6.833,6.833,0,0,1,15.667,8.833Z"
											transform="translate(0 0)" fill="none" stroke="#fff" stroke-linecap="round"
											stroke-linejoin="round" stroke-width="1.67" />
										<path id="Path_43" data-name="Path 43" d="M14,14l-2.9-2.9"
											transform="translate(3 3)" fill="none" stroke="#fff" stroke-linecap="round"
											stroke-linejoin="round" stroke-width="1.67" />
									</g>
								</svg>
							</button>

							<!-- customer info button -->
							<button class="btn btn-sm rounded-circle user-meta-info-toggle"
								data-bs-toggle="tooltip" data-bs-placement="bottom" title="Customer info"
								data-bs-custom-class="tooltip-white">
								<svg xmlns="http://www.w3.org/2000/svg" class="wschat-icon-color" width="18.333" height="18.333"
									viewBox="0 0 18.333 18.333">
									<g id="Icon_feather-info" data-name="Icon feather-info"
										transform="translate(-0.833 -0.833)">
										<path id="Path_54" data-name="Path 54"
											d="M18.333,10A8.333,8.333,0,1,1,10,1.667,8.333,8.333,0,0,1,18.333,10Z"
											fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round"
											stroke-width="1.667" />
										<path id="Path_55" data-name="Path 55" d="M10,13.333V10" fill="none"
											stroke="#fff" stroke-linecap="round" stroke-linejoin="round"
											stroke-width="1.667" />
										<path id="Path_56" data-name="Path 56" d="M10,6.667h0" fill="none" stroke="#fff"
											stroke-linecap="round" stroke-linejoin="round" stroke-width="1.667" />
									</g>
								</svg>
							</button>
						</div>
					</div>
					<!-- end of chatbox header -->

					<!-- chat box main -->
					<div class="d-flex elex-ws-chat-convo-body flex-column flex-fill justify-content-lg-end  position-relative">
						<div class="d-flex overflow-auto">
							<div class="d-flex flex-column flex-content-end elex-ws-chat-convo w-100 py-3 chat-panel">

							</div>
						</div>
						<!--==================popup===================-->
						<!--search popup -->
						<div class="elex-ws-chat-popup  shadow bg-white p-2" id="elex-ws-chat-search"></div>
							<!-- customer info -->
							<div class="elex-ws-chat-popup  w-100" id="elex-ws-chat-customer-info"></div>





							<!-- chat end toast message -->
							<div class="position-absolute bottom-0 start-50 translate-middle-x p-2" style="z-index: 11">
								<div id="elex-ws-chat-end-toast-msg"
									class="toast hide align-items-center text-dark bg-white primary-shadow w-auto"
									role="alert" aria-live="assertive" aria-atomic="true">
									<div class="toast-body text-center text-nowrap">
										Chat has been Ended Succesfully
									</div>
								</div>
							</div>
							<!-- =================end of toast message=============== -->

						</div>
					<!-- end of chat box main -->

					<!-- chatbox footer -->
					<div class="bg-light p-2 position-relative chat-box-footer">
						<!--attach file -->
						<div class="p-2 bg-light elex-ws-chat-convo-attach-file d-none">
							<div class="  bg-white rounded-3  p-2 ">
								<div class=" elex-ws-chat-convo-attach-file-loader">
									<div class="text-secondary"><span>Uploading file..</span></div>
									<div class="elex-ws-chat-convo-attach-file-loader-inner">
										0%
									</div>
								</div>

								<div class=" elex-ws-chat-convo-attached-file">
									<div class="text-secondary">
										<svg class="wschat-icon-fill" xmlns="http://www.w3.org/2000/svg" width="10.497" height="11.997"
											viewBox="0 0 10.497 11.997">
											<path id="Icon_metro-attachment_grey" data-name="Icon metro-attachment grey"
												d="M8.8,4.581l-.761-.761-3.805,3.8A1.614,1.614,0,0,0,6.517,9.908l4.566-4.566a2.69,2.69,0,0,0-3.8-3.8L2.484,6.332l-.01.01a3.752,3.752,0,0,0,5.307,5.306l.01-.01h0l3.273-3.272L10.3,7.605,7.03,10.877l-.01.01A2.676,2.676,0,0,1,3.235,7.1l.01-.01h0L8.039,2.3a1.614,1.614,0,0,1,2.283,2.283L5.756,9.147A.538.538,0,0,1,5,8.386L8.8,4.581Z"
												transform="translate(-1.375 -0.75)" fill="#5e5e5e" />
										</svg>
										<span>File.jpg</span>
									</div>
									<button
										class="btn btn-sm py-0 shadow-none rounded-circle elex-ws-chat-convo-attach-file-remover">
										<svg xmlns="http://www.w3.org/2000/svg" width="9.313" height="9.313"
											viewBox="0 0 9.313 9.313">
											<path id="Icon_ionic-md-close" data-name="Icon ionic-md-close"
												d="M12.656,4.275l-.931-.931L8,7.069,4.275,3.344l-.931.931L7.069,8,3.344,11.725l.931.931L8,8.931l3.725,3.725.931-.931L8.931,8Z"
												transform="translate(-3.344 -3.344)" />
										</svg>
									</button>
								</div>

							</div>
						</div>
						<div class="loading-msg d-none">
							<button class="btn btn-outline-secondary" style="background-color: transparent; border-color: transparent; margin: 0;" type="button" disabled>
								Generating results...
								<span class="spinner-grow text-primary spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-secondary spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-success spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-danger spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-warning spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-info spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-light spinner-grow-sm" role="status" aria-hidden="true"></span>
								<span class="spinner-grow text-dark spinner-grow-sm" role="status" aria-hidden="true"></span>
							</button>
						</div>
						<!-- end of attach file -->
						<div class="d-flex gap-2 align-items-center position-relative alert-gpt-error ">
							<!-- other types input -->
							<div class="position-relative attachment-wrapper" >
								<button class="icon-btn elex-ws-chat-diff-inputs-btn " id="attachment_picker">
									<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg " width="16 " height="16 "
										viewBox="0 0 21.659 21.286 ">
										<g id="Icon_feather-plus " data-name="Icon feather-plus "
											transform="translate(1.5 1.5) ">
											<path id="Path_14 " data-name="Path 14 " d="M12,5V23.286 "
												transform="translate(-2.671 -5) " fill="none " stroke="#fff "
												stroke-linecap="round " stroke-linejoin="round " stroke-width="3 " />
											<path id="Path_15 " data-name="Path 15 " d="M5,12H23.659 "
												transform="translate(-5 -2.857) " fill="none " stroke="#fff "
												stroke-linecap="round " stroke-linejoin="round " stroke-width="3 " />
										</g>
									</svg>
								</button>


								<div class="elex-ws-chat-diff-inputs attachment-list pb-1">
									<div class="d-flex flex-column gap-2 ">

									</div>
								</div>


							</div>

							<!-- input emoji -->
							<div>
							<button class="icon-btn " id="wschat_emoji_picker">
								<svg class="wschat-icon-fill" xmlns="http://www.w3.org/2000/svg " width="16 " height="16 " viewBox="0 0 24 24 ">
									<path id="Icon_material-tag-faces " data-name="Icon material-tag-faces " d="M13.988,2A12,12,0,1,0,26,14,11.994,11.994,0,0,0,13.988,2ZM14,23.6A9.6,9.6,0,1,1,23.6,14,9.6,9.6,0,0,1,14,23.6Zm4.2-10.8A1.8,1.8,0,1,0,16.4,11,1.8,1.8,0,0,0,18.2,12.8Zm-8.4,0A1.8,1.8,0,1,0,8,11,1.8,1.8,0,0,0,9.8,12.8ZM14,20.6a6.6,6.6,0,0,0,6.132-4.2H7.868A6.6,6.6,0,0,0,14,20.6Z
														" transform="translate(-2 -2) " fill="#fff " />
								</svg>
							</button>
							</div>

							<!-- normal text input -->
							<div class="flex-fill ">
								<input class="form-control " id="wschat_message_input" rows="2 " placeholder="Type your message here">
							</div>

							
							<!-- send button -->
							<button class="btn elex-ws-chat-send-btn" id="wschat_send_message">
							<span class="me-1">Send</span>
								<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg " width="16 " height="16 "
									viewBox="0 0 22.829 22.445 ">
									<g id="Icon_feather-send " data-name="Icon feather-send "
										transform="translate(1.5 2.121) ">
										<path id="Path_16 " data-name="Path 16 " d="M21.564,2,11,12.353 "
											transform="translate(-2.357 -2) " fill="none " stroke="#fff "
											stroke-linecap="round " stroke-linejoin="round " stroke-width="3 " />
										<path id="Path_17 " data-name="Path 17 "
											d="M21.208,2,14.485,20.824l-3.842-8.471L2,8.588Z "
											transform="translate(-2 -2) " fill="none " stroke="#fff "
											stroke-linecap="round " stroke-linejoin="round " stroke-width="3 " />
									</g>
								</svg>
							</button>


						</div>

						<!-- send mail -->
						<div class=" position-absolute p-2 elex-ws-chat-email">

							<div class=" d-flex align-items-center justify-content-between">
								<div class="xs"><b>Send this conversation through email</b></div>
								<button class="btn btn-sm py-0 elex-ws-chat-email-close-btn">
									<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="10.5" height="10.5"
										viewBox="0 0 10.5 10.5">
										<path id="Icon_material-close" data-name="Icon material-close"
											d="M14.25,4.807,13.193,3.75,9,7.943,4.807,3.75,3.75,4.807,7.943,9,3.75,13.193,4.807,14.25,9,10.057l4.193,4.193,1.057-1.057L10.057,9Z"
											transform="translate(-3.75 -3.75)"></path>
									</svg>
								</button>
							</div>


							<div class="d-flex d-flex  gap-1 align-items-end">
								<div class="flex-fill">
									<label class="xs">Email Id</label>
									<input type="text" class="form-control " placeholder="name@example.com">
								</div>

								<!-- send button -->
								<button class="btn btn-sm btn-primary lh-1 p-2 rounded-circle"
									id="elex-ws-chat-email-toast-msg-btn">
									<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
										viewBox="0 0 22.829 22.445">
										<g id="Icon_feather-send" data-name="Icon feather-send"
											transform="translate(1.5 2.121)">
											<path id="Path_16" data-name="Path 16" d="M21.564,2,11,12.353"
												transform="translate(-2.357 -2)" fill="none" stroke="#fff"
												stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
											<path id="Path_17" data-name="Path 17"
												d="M21.208,2,14.485,20.824l-3.842-8.471L2,8.588Z"
												transform="translate(-2 -2)" fill="none" stroke="#fff"
												stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
										</g>
									</svg>
								</button>
							</div>

						</div>
					</div>
					<!-- end of chatbox footer -->

					<!-- when agent goes offline illustration (hide main convo and footer) -->
					<div class="text-center d-none h-100 ">
							<div class="row h-100 justify-content-center align-items-center">
								<div class="col-lg-6 col-md-8 col-10">
									<img src="<?php echo esc_url( \WSChat\Utils::get_resource_url( '/resources/img/conversation/offline illustration.svg' ) ); ?>" alt="" class="w-100">
									<h3 class="text-center my-3"><?php esc_attr_e( 'Your current status is offline. Go online to start with the chat.', 'wsdesk' ); ?></h3>
								</div>
							</div>
						</div>
				</div>


			</div>
		</div>

	</div>

