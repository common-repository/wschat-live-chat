<div class="wschat-wrapper">
	<div class="d-none no-conversation-alert alert alert-warning m-1">
		<p class="text-danget text-center m-0"><?php echo esc_attr__( 'No conversations found', 'wschat' ); ?></p>
	</div>
	<div class="container-fluid mt-3">
		<div class="row">
			<div class="col-3 d-flex flex-column shadow shadow-sm p-0" style="max-height: 85vh">
					<div class="bg-primary p-3 rounded-top text-white">
						<img class="profile-image shadow-sm" src="<?php echo esc_html( get_avatar_url( wp_get_current_user()->ID ) ); ?>" alt="Profile img">
					</div>
					<div class="search-box ">
						<div class="btn-group w-100 mb-1 status-control" role="group" aria-label="Basic radio toggle button group">
							<input type="radio" class="btn-check" name="conversation_status" value="all" id="conversation_status1" autocomplete="off" checked>
							<label class="btn btn-outline-primary btn-sm" for="conversation_status1"><?php echo esc_attr__( 'All', 'wschat' ); ?></label>

							<input type="radio" class="btn-check" name="conversation_status" value="active" id="conversation_status2" autocomplete="off">
							<label class="btn btn-outline-primary btn-sm" for="conversation_status2"><?php echo esc_attr__( 'Active', 'wschat' ); ?></label>

							<input type="radio" class="btn-check" name="conversation_status" value="recent" id="conversation_status3" autocomplete="off">
							<label class="btn btn-outline-primary btn-sm" for="conversation_status3"><?php echo esc_attr__( 'Recent', 'wschat' ); ?></label>
						</div>
						<div class="input-wrapper">
							<i class="material-icons">search</i>
							<input name="search-control" placeholder="Search here " type="text">
						</div>
					</div>
					<div class="conversation-list">
					</div>
			</div>
				<div class="col-md-9 " style="max-height: 85vh;">
				<div class="d-flex flex-column align-items-stretch conversation-wrapper d-none h-100 bg-white shadow rounded-bottom">
					<div class="wschat-bg-primary p-3 settings-tray rounded-top chat-panel-header">
						<div class="p-0 d-flex">
							<img class="profile-image shadow-sm" src="https://ui-avatars.com/api/?rounded=true&name=Guest" alt="">
							<div class="ps-2 text w-100">
								<h6><span class="username text-white"></span> <span class="unread-count badge rounded-pill d-none"></span></h6>
								<p class="m-0 ps-2 status text-white"></p>
							</div>
							<div class="d-flex flex-row align-items-center conversation-header-actions">
								<button class="btn btn-outline-primary border-0 search-box-toggle text-white" title="Toggle Search">
									<i class="material-icons ">search</i>
								</button>
								<button class="btn btn-outline-primary border-0 user-meta-info-toggle text-white" title="Toggle User Info">
									<i class="material-icons ">info</i>
								</span>
							</div>
						</div>
					</div>
					<div class="row g-0 flex-fill bg-white overflow-hidden">
						<div class="col h-100 overflow-auto position-relative">
							<div class="chat-panel h-100 flex-fill d-flex flex-column border-end">
							</div>
						</div>
						<div class="col-4 user-meta-info border-start p-2 d-none h-100 overflow-auto">
						</div>
						<div class="col-4 wschat-search-container border-start p-2 d-none d-flex flex-column h-100">
							<div>
								<h6><?php echo esc_html__( 'Search', 'wschat' ); ?></h6>
								<div class="input-group input-group-sm">
									<input type="text" aria-label="Search Input" id="wschat-search-input" class="form-control w-50">
									<select class="form-control" id="wschat-search-mode">
										<option value="posts"><?php echo esc_html__( 'Products & Docs', 'wschat' ); ?></option>
										<option value="messages"><?php echo esc_html__( 'Chat', 'wschat' ); ?></option>
									</select>
								</div>
								<div class="progress d-none wschat-search-progress-bar my-2" style="height: 1px">
												<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
								</div>
							</div>
							<div class="wschat-search-results mt-2 overflow-auto">
							</div>
						</div>
					</div>
					<div class="row g-0 chat-panel-footer">
						<div class="col-12">
							<div class="attachment-wrapper">
								<div class="attachment-content">
									<div class="attachments-preview-container d-flex flex-wrap">
									</div>
									<div class="attachment-list fade d-none">
									</div>
								</div>
							</div>
							<?php if ( \WSChat\Utils::is_widget_online() === false ) { ?>
							<div>
								<p class="text-center wschat-notice">
									<?php echo esc_attr__( 'Widget is offline. So, You are not able to reply.', 'wschat' ); ?>
									<a href="<?php echo esc_html( \WSChat\Utils::get_url( 'wschat_settings' ) ); ?>" ><?php echo esc_attr__( 'settings', 'wschat' ); ?></a>
								</p>
							</div>
							<?php } ?>
							<div class="chat-box-tray <?php echo \WSChat\Utils::is_widget_online() ? '' : esc_html( 'd-none' ); ?>">
								<button class="btn btn-sm btn-default rounded" id="attachment_picker">
									<i class="material-icons">add</i>
								</button>
								<button class="btn btn-sm btn-default rounded" id="wschat_emoji_picker">
									<i class="material-icons">sentiment_very_satisfied</i>
								</button>
								<textarea rows="1" id="wschat_message_input" class="w-100 bg-white p-2" placeholder="Type your message here..."></textarea>
								<button class="btn btn-sm" id="wschat_send_message">
									<i class="material-icons">send</i>
								</button>
							</div>
						</div>
					</div>
					</div>
				</div>
		</div>
	</div>
	<div class="m-1">
		<div class="container-fluid bg-white g-0 shadow-lg">
			<div class="row g-0">
				<div class="col-md-3 border-end">
				</div>
			</div>
		</div>
	</div>
</div>
