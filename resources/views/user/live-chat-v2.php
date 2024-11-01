<div class="wschat-wrapper elex-ws-chat-wrap-widget">
				<button
					class="btn wschat-bg-primary rounded-circle  position-fixed  elex-ws-chat-widget-open-convo-box">
					<span class="unread-count position-absolute elex-wschat-unread-widget-badge top-0 d-none" style="right: 0"></span>

				<svg  xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36.018">
					<g id="widget_msg_icon" data-name="widget msg icon" transform="translate(-14 -14.991)">
					<g id="Union_1" data-name="Union 1" transform="translate(14 14.991)" fill="#fff">
					<path
							d="M 12.50009727478027 34.21516418457031 L 9.253589630126953 30.13890647888184 L 8.803190231323242 29.57340621948242 L 8.080249786376953 29.57340621948242 L 6.00029993057251 29.57340621948242 C 3.518830060958862 29.57340621948242 1.5 27.55498504638672 1.5 25.07400512695312 L 1.5 5.999405860900879 C 1.5 3.518425703048706 3.518830060958862 1.499995708465576 6.00029993057251 1.499995708465576 L 29.99970054626465 1.499995708465576 C 32.48117065429688 1.499995708465576 34.5 3.518425703048706 34.5 5.999405860900879 L 34.5 25.07400512695312 C 34.5 27.55498504638672 32.48117065429688 29.57340621948242 29.99970054626465 29.57340621948242 L 16.91995048522949 29.57340621948242 L 16.1970100402832 29.57340621948242 L 15.74660968780518 30.13890647888184 L 12.50009727478027 34.21516418457031 Z"
							stroke="none" />
					<path
							d="M 12.50010108947754 31.80746078491211 L 15.47406005859375 28.07340621948242 L 29.99970054626465 28.07340621948242 C 31.6540699005127 28.07340621948242 33 26.72787666320801 33 25.07400512695312 L 33 5.999405860900879 C 33 4.345525741577148 31.6540699005127 2.999995708465576 29.99970054626465 2.999995708465576 L 6.00029993057251 2.999995708465576 C 4.345930099487305 2.999995708465576 3 4.345525741577148 3 5.999405860900879 L 3 25.07400512695312 C 3 26.72787666320801 4.345930099487305 28.07340621948242 6.00029993057251 28.07340621948242 L 9.526140213012695 28.07340621948242 L 12.50010108947754 31.80746078491211 M 12.50010013580322 36.01755523681641 C 12.20917987823486 36.01755523681641 11.91825008392334 35.89200592041016 11.7180004119873 35.64089584350586 L 8.080249786376953 31.07340621948242 L 6.00029993057251 31.07340621948242 C 2.686500072479248 31.07340621948242 0 28.38779640197754 0 25.07400512695312 L 0 5.999405860900879 C 0 2.685605764389038 2.686500072479248 -4.288940544938669e-06 6.00029993057251 -4.288940544938669e-06 L 29.99970054626465 -4.288940544938669e-06 C 33.31349945068359 -4.288940544938669e-06 36 2.685605764389038 36 5.999405860900879 L 36 25.07400512695312 C 36 28.38779640197754 33.31349945068359 31.07340621948242 29.99970054626465 31.07340621948242 L 16.91995048522949 31.07340621948242 L 13.28219985961914 35.64089584350586 C 13.08195018768311 35.89200592041016 12.79102993011475 36.01755523681641 12.50010013580322 36.01755523681641 Z"
							stroke="none" fill="#49e1ff" />
					</g>
					<circle id="Ellipse_5" data-name="Ellipse 5" cx="2.5" cy="2.5" r="2.5" transform="translate(38 33)"
																						   fill="#49e1ff" />
					<circle id="Ellipse_6" data-name="Ellipse 6" cx="2.5" cy="2.5" r="2.5" transform="translate(30 33)"
																						   fill="#49e1ff" />
					<circle id="Ellipse_7" data-name="Ellipse 7" cx="2.5" cy="2.5" r="2.5" transform="translate(22 33)"
																						   fill="#49e1ff" />
					</g>
				</svg>
			</button>

			<div class="elex-ws-chat-widget-convo-box bg-white rounded-3 overflow-hidden position-fixed " style="display: none">
				<!-- header -->
				<div class="wschat-bg-primary py-2 px-3 d-flex align-items-center gap-3 chat-panel-header">
					<div class="elex-ws-chat-profile-pic">
						<div class="ratio ratio-1x1 rounded-circle overflow-hidden elex-ws-chat-admin-img-shadow">
							<img class="profile-image" src="<?php echo esc_url( get_avatar_url( get_current_user_id() ) ); ?>" alt="">
						</div>
					</div>

					<div class="wschat-text-primary flex-fill">
						<h6 class="wschat-text-primary"><span class="username"></span> </h6>
						<div class="d-flex align-items-center gap-2"><span
							 class="badge p-1 d-block rounded-circle bg-success"></span><small class="status wschat-text-primary">Online</small></div>
					</div>

					<div class="d-flex wschat-panel-header-actions">
						<!-- for minimizing -->
						<button class="btn btn-sm rounded-circle elex-ws-chat-widget-close-convo-box" data-bs-toggle="tooltip"
																									  data-bs-placement="right" title="Minimize" data-bs-custom-class="tooltip-primary">
							<svg xmlns="http://www.w3.org/2000/svg" width="15.744" height="9.002" viewBox="0 0 15.744 9.002" class="wschat-icon-fill">
								<path id="Icon_ionic-ios-arrow-down" data-name="Icon ionic-ios-arrow-down"
																	 d="M12,13.786l5.953-5.958a1.12,1.12,0,0,1,1.589,0,1.135,1.135,0,0,1,0,1.594L12.8,16.172a1.123,1.123,0,0,1-1.552.033L4.453,9.427A1.125,1.125,0,0,1,6.042,7.833Z"
																	 transform="translate(-4.125 -7.498)" fill="#fff" />
							</svg>
						</button>
					</div>
				</div>

				<!-- main -->
				<div class="bg-white flex-fill overflow-auto position-relative elex-ws-chat-convo-body">

					<!-- help us to Know you better form -->
					<div class="h-100 d-flex align-items-center  elex-ws-chat-widget-help-us d-none">
						<div class="align-self-center w-100 p-2">
							<h5 class="text-center mb-4">Help Us to Know You Better</h5>

							<form>
								<div class=" mb-3">
									<h6>Full Name</h6>
									<input type="text" placeholder="Enter Your Full Name" class="form-control form-control-sm">
								</div>
								<div class=" mb-3">
									<h6>Email id</h6>
									<input type="text" placeholder="Enter Your Email id" class="form-control form-control-sm">
								</div>
								<div class=" mb-3">
									<h6>Subject</h6>
									<input type="text" placeholder="Enter Subject" class="form-control form-control-sm">
								</div>
								<button type="button" class="btn btn-sm btn-primary w-100 elex-ws-chat-widget-form-submit">Submit</button>
							</form>
						</div>
					</div>


					<!-- conversation -->
					<div class="h-100 d-flex align-items-end position-relative elex-ws-chat-convo-content ">
					<div class="pre-chat-panel flex-fill d-flex flex-column bg-white p-1 d-none h-100">
						<div class="pre-chat-form-header">
							<h3 class="pre-chat-form-title text-center h6"></h3>
						</div>
						<form class="pre-chat-form m-2">
						</form>
						<div class="m-2 text-center">
							<button type="button" class="btn btn-sm btn-primary pre-chat-form-btn-submit"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
						</div>
					</div>
						<div class="elex-ws-chat-convo py-3 w-100 mh-100 h-auto chat-panel">
						</div>
					</div>


					<!-- toast message -->

					<!-- attach toast message -->
					<div class="position-absolute bottom-0 start-50 translate-middle-x p-2" style="z-index: 11">
						<div id="elex-ws-chat-widget-attach-toast-msg"
							 class="toast hide align-items-center text-white bg-danger w-auto" role="alert" aria-live="assertive"
																											aria-atomic="true">
							<div class="toast-body text-center text-nowrap">
								File to Size to big to send
							</div>
						</div>
					</div>
				</div>


				<!-- footer -->
				<div class="">
					<div class="chat-box-footer position-relative wschat-bg-secondary">
						<div class="p-2 bg-light xs elex-ws-chat-upload-progress d-flex justify-content-between d-none">
							<?php esc_attr_e( 'Uploading file...' ); ?>
							<span class="progress-percentage">0%</span>
						</div>
						<!-- send msg  -->
						<div class="d-flex p-2 gap-1 align-items-center position-relative elex-ws-chat-widget-convo-input attachment-wrapper">
							<!-- more input button -->
							<div class="d-flex gap-1 position-relative" >
							<button class="btn  btn-sm wschat-bg-primary lh-1 p-1 rounded-circle elex-ws-chat-diff-inputs-btn" id="attachment_picker" type="button">
								<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21.659 21.286">
									<g id="Icon_feather-plus" data-name="Icon feather-plus" transform="translate(1.5 1.5)">
									<path id="Path_14" data-name="Path 14" d="M12,5V23.286" transform="translate(-2.671 -5)" fill="none"
																															 stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
									<path id="Path_15" data-name="Path 15" d="M5,12H23.659" transform="translate(-5 -2.857)" fill="none"
																															 stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
									</g>
								</svg>
							</button>

							<!-- emoji button -->
							<button class="btn  btn-sm wschat-bg-primary lh-1 p-1 rounded-circle" id="wschat_emoji_picker">
								<svg class=" wschat-icon-fill" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
									<path id="Icon_material-tag-faces" data-name="Icon material-tag-faces"
																	   d="M13.988,2A12,12,0,1,0,26,14,11.994,11.994,0,0,0,13.988,2ZM14,23.6A9.6,9.6,0,1,1,23.6,14,9.6,9.6,0,0,1,14,23.6Zm4.2-10.8A1.8,1.8,0,1,0,16.4,11,1.8,1.8,0,0,0,18.2,12.8Zm-8.4,0A1.8,1.8,0,1,0,8,11,1.8,1.8,0,0,0,9.8,12.8ZM14,20.6a6.6,6.6,0,0,0,6.132-4.2H7.868A6.6,6.6,0,0,0,14,20.6Z"
																	   transform="translate(-2 -2)" fill="#fff" />
								</svg>
							</button>
							</div>

							<!-- normal input chat field -->
							<input type="text" id="wschat_message_input" class="form-control flex-fill xs shadow-none elex-ws-chat-text-input" placeholder="Type yor message here">

							<!-- send button -->
							<button class="btn btn-sm wschat-bg-primary lh-1 p-1 rounded-circle  elex-ws-chat-send-btn " id="wschat_send_message">
								<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 22.829 22.445">
									<g id="Icon_feather-send" data-name="Icon feather-send" transform="translate(1.5 2.121)">
									<path id="Path_16" data-name="Path 16" d="M21.564,2,11,12.353" transform="translate(-2.357 -2)"
																								   fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
									<path id="Path_17" data-name="Path 17" d="M21.208,2,14.485,20.824l-3.842-8.471L2,8.588Z"
																		   transform="translate(-2 -2)" fill="none" stroke="#fff" stroke-linecap="round"
																																  stroke-linejoin="round" stroke-width="2" />
									</g>
								</svg>
							</button>


							<!-- more input option -->
							<div class="position-absolute elex-ws-chat-widget-more-inputs p-2 elex-ws-chat-diff-inputs attachment-list">
								<div class="d-flex flex-column gap-2 align-items-start justify-content-end" >

								</div>
							</div>


							<!-- send mail -->
							<div class=" position-absolute p-2 elex-ws-chat-widget-email d-none">

								<div class=" d-flex align-items-center justify-content-between">
									<div class="xs"><b>Send this conversation through email</b></div>
									<button class="btn btn-sm py-0 elex-ws-chat-widget-email-close-btn">
										<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="10.5" height="10.5" viewBox="0 0 10.5 10.5">
											<path id="Icon_material-close" data-name="Icon material-close"
																		   d="M14.25,4.807,13.193,3.75,9,7.943,4.807,3.75,3.75,4.807,7.943,9,3.75,13.193,4.807,14.25,9,10.057l4.193,4.193,1.057-1.057L10.057,9Z"
																		   transform="translate(-3.75 -3.75)"></path>
										</svg>
									</button>
								</div>


								<div class="d-flex  gap-1 align-items-end">
									<div class="flex-fill">
										<label class="xs">Email Id</label>
										<input type="text" class="form-control form-control-sm" placeholder="name@example.com">
									</div>

									<!-- send button -->
									<button class="btn btn-sm btn-primary lh-1 p-1 rounded-circle"
											id="elex-ws-chat-widget-email-toast-msg-btn">
										<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 22.829 22.445">
											<g id="Icon_feather-send" data-name="Icon feather-send" transform="translate(1.5 2.121)">
											<path id="Path_16" data-name="Path 16" d="M21.564,2,11,12.353" transform="translate(-2.357 -2)"
																										   fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
											<path id="Path_17" data-name="Path 17" d="M21.208,2,14.485,20.824l-3.842-8.471L2,8.588Z"
																				   transform="translate(-2 -2)" fill="none" stroke="#fff" stroke-linecap="round"
																																		  stroke-linejoin="round" stroke-width="3" />
											</g>
										</svg>
									</button>
								</div>

							</div>

							<!-- Progress -->
							<!-- give us feedback -->
							<div class=" position-absolute p-2 elex-ws-chat-widget-feedback d-none">

								<div class=" d-flex align-items-center justify-content-between mb-2">
									<div class="xs flex-fill text-center ms-4"><b>Give us Your feedback</b></div>
									<button class="btn btn-sm py-0 elex-ws-chat-widget-feedback-close-btn">
										<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="10.5" height="10.5" viewBox="0 0 10.5 10.5">
											<path id="Icon_material-close" data-name="Icon material-close"
																		   d="M14.25,4.807,13.193,3.75,9,7.943,4.807,3.75,3.75,4.807,7.943,9,3.75,13.193,4.807,14.25,9,10.057l4.193,4.193,1.057-1.057L10.057,9Z"
																		   transform="translate(-3.75 -3.75)"></path>
										</svg>
									</button>
								</div>
								<div class=" d-flex  gap-2 align-items-center justify-content-center">
									<button class="btn btn-sm xs px-4 border border-secondary elex-ws-chat-widget-feedback-toast-msg-btn">
										<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.266 16.5">
											<path id="Icon_feather-thumbs_down" data-name="Icon feather-thumbs down"
																				d="M7.262,11.25v3a2.25,2.25,0,0,0,2.25,2.25l3-6.75V1.5H4.052a1.5,1.5,0,0,0-1.5,1.275L1.517,9.525a1.5,1.5,0,0,0,1.5,1.725Zm5.25-9.75h2.25a1.5,1.5,0,0,1,1.5,1.5V8.25a1.5,1.5,0,0,1-1.5,1.5h-2.25"
																				transform="translate(-0.746 -0.75)" fill="none" stroke="#707070" stroke-linecap="round"
																																				 stroke-linejoin="round" stroke-width="1.5" />
										</svg>
										Bad</button>
									<button class="btn btn-sm xs px-4 wsdesk-bg-primary elex-ws-chat-widget-feedback-toast-msg-btn">
										<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.266 16.5">
											<path id="Icon_feather-thumbs-up" data-name="Icon feather-thumbs-up"
																			  d="M10.5,6.75v-3A2.25,2.25,0,0,0,8.25,1.5l-3,6.75V16.5h8.46a1.5,1.5,0,0,0,1.5-1.275l1.035-6.75a1.5,1.5,0,0,0-1.5-1.725ZM5.25,16.5H3A1.5,1.5,0,0,1,1.5,15V9.75A1.5,1.5,0,0,1,3,8.25H5.25"
																			  transform="translate(-0.75 -0.75)" fill="none" stroke="#fff" stroke-linecap="round"
																																		   stroke-linejoin="round" stroke-width="1.5" />
										</svg>
										Good</button>
								</div>
							</div>
						</div>
					</div>
					<div class="text-center xs p-2 text-secondary">
						Powered by <b>WSChat</b>
					</div>
				</div>



			</div>

</div>
