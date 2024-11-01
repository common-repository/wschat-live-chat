<div class="wschat-wrapper">
	<div class="wschat-popup">
		<div class="container-fluid g-0 shadow-lg wschat-popup-content">
			<div class="row g-0">
				<div class="col-md-12 d-flex flex-column align-items-stretch bg-white">
					<div class="settings-tray rounded-top chat-panel-header">
						<div class="friend-drawer friend-drawer--grey p-0 d-flex">
						<img class="profile-image" src="<?php echo esc_html( \WSChat\Utils::get_resource_url( '/resources/img/wschat_logo.png' ) ); ?>" alt="">
							<div class="text w-100">
								<h6 class="text-truncate"><span class="username">Guest</span> <span class="unread-count badge rounded-pill d-none"></span></h6>
								<small class="status">Online</small>
							</div>
							<span class="settings-tray--right wschat-chat-toggle">
								<i class="material-icons text-lg">arrow_drop_down</i>
							</span>
						</div>
					</div>
					<div class="pre-chat-panel flex-fill d-flex flex-column bg-white p-1 d-none">
						<div class="pre-chat-form-header">
							<h3 class="pre-chat-form-title text-center h6"></h3>
						</div>
						<form class="pre-chat-form m-2">
						</form>
						<div class="m-2 text-center">
							<button type="button" class="btn btn-sm btn-primary pre-chat-form-btn-submit"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
						</div>
					</div>
					<div class="chat-panel flex-fill d-flex flex-column ">
					</div>
					<div class="row g-0">
						<div class="col-12">
							<div class="attachment-wrapper">
								<div class="attachment-content">
									<div class="attachments-preview-container d-flex flex-wrap">
									</div>
									<div class="attachment-list d-none">
									</div>
								</div>
							</div>
							<div class="chat-box-tray rounded-bottom">
								<button class="btn btn-sm" id="attachment_picker" title="<?php echo esc_attr__( 'Click to add Attachments', 'wschat' ); ?>">
									<i class="material-icons">add</i>
								</button>
								<button class="btn btn-sm" id="wschat_emoji_picker">
									<i class="material-icons">sentiment_very_satisfied</i>
								</button>
								<textarea rows="1" id="wschat_message_input" class="w-100 bg-white" placeholder="Type your message here..."></textarea>
								<button class="btn btn-sm" id="wschat_send_message">
									<i class="material-icons">send</i>
								</button>
							</div>
						</div>
					</div>
					<?php 
					/** 
					 * Fire a filter hook for removing widget powered by
					 * 
					 * @since 2.0.0
					 */
					if ( apply_filters( 'wschat_remove_widget_powered_by', false ) === false ) { 
						?>
						<div class="powered-by-tag">
							<p class="text-muted text-center bg-white m-0 p-1 small">
								<?php echo esc_attr__( 'Powered by', 'wschat' ); ?>
								<a href="https://elextensions.com/plugin/wschat-wordpress-live-chat-plugin/" target="_blank">
									<?php echo esc_attr__( 'WSchat', 'wschat' ); ?>
								</a>
							</p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="d-flex justify-content-end">
			<button type="button" class="btn btn-sm m-4 rounded-pill wschat-chat-toggle wschat-fab">
				<i class="material-icons icon-chat">chat</i>
				<i class="material-icons icon-close">close</i>
				<span class="unread-count badge rounded-pill"></span>
			</button>
		</div>
	</div>
</div>
