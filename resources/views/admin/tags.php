<div class="wschat-wrapper wschat-pages">
	<div class="container-fluid py-3 mt-3">
		<div class="row">
			<div class="col-3 overflow-auto" style="min-height: 85vh; max-height: 100vh;">
				<div class="shadow rounded bg-white h-100">
					<div class="px-3 pt-2">
						<div class="align-items-center d-flex header justify-between justify-content-between">
							<h6 class="m-0"><?php echo esc_attr__( 'Tags' ); ?></h6>
							<button onclick="jQuery('.wschat-add-new-tag-popup').toggleClass('show').find('input[name=name]').val('').focus()" class="btn btn-default p-0"><i class="material-icons">add</i></button>
						</div>
						<div class="wschat-tags-search">
							<div class="input-group input-group-sm mb-3">
								<span class="input-group-text bg-white" id="basic-addon1"><i class="material-icons">search</i></span>
								<input type="text" class="search-tag form-control form-control-sm" placeholder="Search tags" aria-label="Username" aria-describedby="basic-addon1">
							</div>
						</div>
					</div>
					<ul class="list-group list-group-flush tag-list-group pb-3">
						<?php foreach ( $tags as $tag_item ) { ?>
							<a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( 'page_no', add_query_arg( array( 'tag_id' => $tag_item['id'] ) ) ), 'tags_filter' ) ); ?>" class="list-group-item border-0 d-flex align-items-center <?php echo $tag_item['id'] === $tag_id ? 'active' : ''; ?>" >
								<span
									class="d-inline-block pr-1 rounded-circle tags-badge"
									style="background-color: #<?php echo esc_attr( $tag_item['color'] ); ?>"
								></span>
								<?php echo esc_html( $tag_item['name'] ); ?>
							</a>
						<?php } ?>
					</ul>
					<?php if ( count( $tags ) === 0 ) { ?>
						<div class="no-tags-were-created">
							<h3 class="text-center text-muted"><?php esc_attr_e( 'No tag created yet! Click on "+" icon to create your first tag' ); ?></h3>
						</div>
					<?php } ?>
					<div class="no-tags-were-found d-none">
						<h3 class="text-center text-muted"><?php esc_attr_e( 'No Results Found' ); ?></h3>
					</div>
				</div>
			</div>
			<div class="col-9">
				<div class="shadow rounded bg-white h-100 d-flex flex-column">
					<?php if ( count( $tags ) > 0 ) : ?>
					<div class="px-3 pt-2 rounded-top tag-details-header">
						<div class="d-flex justify-content-between align-items-center">
							<h6>
								<span
									class="d-inline-block pr-1 rounded-circle tags-badge"
									style="background-color: #<?php echo esc_attr( $tags[ $tag_id ]['color'] ); ?>"
								></span>
								<?php echo esc_html( $tags[ $tag_id ]['name'] ); ?>
							</h6>
							<div class="">
								<button onclick="jQuery('.wschat-edit-tag-popup').toggleClass('show').find('input[name=name]').eq(0).focus()" class="btn btn-sm btn-link text-decoration-none"><i class="material-icons">edit</i> <?php echo esc_html__( 'Edit', 'wschat' ); ?></button>
								<button class="btn btn-sm btn-link text-decoration-none delete-tag" data-name="<?php echo esc_html( $tags[ $tag_id ]['name'] ); ?>" data-id="<?php echo esc_html( $tag_id ); ?>"><i class="material-icons">delete</i> <?php echo esc_html__( 'Delete', 'wschat' ); ?></button>
							</div>
						</div>
						<form class="align-items-center row" method="post">
							<?php wp_nonce_field( 'tags_filter' ); ?>
							<div class="col-md-2">
								<label><?php echo esc_html__( 'Date Peroid', 'wschat' ); ?></lable>
								<select name="date_period" class="date_period form-select form-select-sm mb-3">
								<option <?php echo esc_html( '' === $date_period ? 'selected' : '' ); ?> value=""><?php esc_attr_e( 'Overall', 'wschat' ); ?></option>
								<option <?php echo esc_html( '1' === $date_period ? 'selected' : '' ); ?> value="1"><?php esc_attr_e( 'Today', 'wschat' ); ?></option>
								<option <?php echo esc_html( '7' === $date_period ? 'selected' : '' ); ?> value="7"><?php esc_attr_e( 'Last 7 Days', 'wschat' ); ?></option>
									<option <?php echo esc_html( '30' === $date_period ? 'selected' : '' ); ?> value="30"><?php esc_attr_e( 'Last 30 Days', 'wschat' ); ?></option>
									<option <?php echo esc_html( 'custom' === $date_period ? 'selected' : '' ); ?> value="custom"><?php echo esc_html__( 'Custom Date', 'wschat' ); ?></option>
								</select>
							</div>
							<div class="col-md-8 custom-date-period d-none">
								<div class="row">
									<div class="col-md-6">
										<label><?php echo esc_html__( 'Custom Date Period', 'wschat' ); ?></label>
										<div class="input-group input-group-sm mb-3">
											<span class="input-group-text" id="basic-addon1"><?php echo esc_html__( 'From', 'wschat' ); ?></span>
											<input type="date" max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" value="<?php echo isset( $created_at['min'] ) ? esc_html( get_date_from_gmt( $created_at['min'], 'Y-m-d' ) ) : ''; ?>" name="created_at[min]" id="from-date" class="form-control form-control-sm" aria-describedby="from date">
											<input type="hidden" name="page" value="wschat_tags">
										</div>
									</div>
									<div class="col-md-6">
										<label></label>
										<div class="input-group input-group-sm mb-3">
											<span class="input-group-text" id="basic-addon1"><?php echo esc_html__( 'To', 'wschat' ); ?></span>
											<input type="date" max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" value="<?php echo isset( $max_date ) ? esc_html( date_create( $max_date )->format( 'Y-m-d' ) ) : ''; ?>" name="created_at[max]" id="to-date" class="form-control form-control-sm" aria-describedby="to date">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-2 ">
								<button class="btn btn-sm btn-primary"><?php echo esc_html__( 'Filter', 'wschat' ); ?></button>
							</div>
						</form>
					</div>
					<?php endif; ?>
					<div class="m-2 h-100 d-flex flex-column <?php echo esc_attr( count( $messages ) === 0 ? 'justify-content-center align-items-center' : 'justify-content-between' ); ?>">
						<div class="list-group">
							<?php foreach ( $messages as $message ) { ?>
								<div class="list-group-item d-flex justify-content-between" data-message-id="<?php echo esc_attr( $message['id'] ); ?>">
									<div class="d-flex flex-column justify-content-between ">
										<a href="
											<?php
												echo esc_url(
													add_query_arg(
														array(
															'page' => 'wschat_chat',
															'conversation_id' => $message['conversation_id'],
															'message_id' => $message['id'],
														)
													)
												);
											?>
										" class="text-decoration-none"></a>
										<div class="d-flex gap-2 flex-wrap my-2">
											<?php 
											echo esc_html( $message['body']['text'] ); 
											?>
												
											<?php 
											if ( isset( $message['body']['attachments'] ) ) {
												foreach ( $message['body']['attachments'] as $file ) {
													?>
													
													<a href="<?php echo esc_html( $file['url'] ); ?>" target="_blank" class="text-dark align-items-center border  border-2 btn d-flex gap-2 px-3 rounded-pill">
													<?php if ( 'image' === substr( $file['type'], 0, 5 ) ) { ?> 
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M384 121.941V128H256V0h6.059a24 24 0 0 1 16.97 7.029l97.941 97.941a24.002 24.002 0 0 1 7.03 16.971zM248 160c-13.2 0-24-10.8-24-24V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248zm-135.455 16c26.51 0 48 21.49 48 48s-21.49 48-48 48-48-21.49-48-48 21.491-48 48-48zm208 240h-256l.485-48.485L104.545 328c4.686-4.686 11.799-4.201 16.485.485L160.545 368 264.06 264.485c4.686-4.686 12.284-4.686 16.971 0L320.545 304v112z"/>
														</svg>
														
													<?php } else if ( 'audio' === substr( $file['type'], 0, 5 ) ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm-64 268c0 10.7-12.9 16-20.5 8.5L104 376H76c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h28l35.5-36.5c7.6-7.6 20.5-2.2 20.5 8.5v136zm33.2-47.6c9.1-9.3 9.1-24.1 0-33.4-22.1-22.8 12.2-56.2 34.4-33.5 27.2 27.9 27.2 72.4 0 100.4-21.8 22.3-56.9-10.4-34.4-33.5zm86-117.1c54.4 55.9 54.4 144.8 0 200.8-21.8 22.4-57-10.3-34.4-33.5 36.2-37.2 36.3-96.5 0-133.8-22.1-22.8 12.3-56.3 34.4-33.5zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
														</svg>

													<?php } else if ( 'video' === substr( $file['type'], 0, 5 ) ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M384 121.941V128H256V0h6.059c6.365 0 12.47 2.529 16.971 7.029l97.941 97.941A24.005 24.005 0 0 1 384 121.941zM224 136V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248c-13.2 0-24-10.8-24-24zm96 144.016v111.963c0 21.445-25.943 31.998-40.971 16.971L224 353.941V392c0 13.255-10.745 24-24 24H88c-13.255 0-24-10.745-24-24V280c0-13.255 10.745-24 24-24h112c13.255 0 24 10.745 24 24v38.059l55.029-55.013c15.011-15.01 40.971-4.491 40.971 16.97z"/>
														</svg>

													<?php } else if ( 'application/pdf' === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z"/>
														</svg>

													<?php } else if ( 'application/javascript' === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 448 512">
															<path d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zM243.8 381.4c0 43.6-25.6 63.5-62.9 63.5-33.7 0-53.2-17.4-63.2-38.5l34.3-20.7c6.6 11.7 12.6 21.6 27.1 21.6 13.8 0 22.6-5.4 22.6-26.5V237.7h42.1v143.7zm99.6 63.5c-39.1 0-64.4-18.6-76.7-43l34.3-19.8c9 14.7 20.8 25.6 41.5 25.6 17.4 0 28.6-8.7 28.6-20.8 0-14.4-11.4-19.5-30.7-28l-10.5-4.5c-30.4-12.9-50.5-29.2-50.5-63.5 0-31.6 24.1-55.6 61.6-55.6 26.8 0 46 9.3 59.8 33.7L368 290c-7.2-12.9-15-18-27.1-18-12.3 0-20.1 7.8-20.1 18 0 12.6 7.8 17.7 25.9 25.6l10.5 4.5c35.8 15.3 55.9 31 55.9 66.2 0 37.8-29.8 58.6-69.7 58.6z"/>
														</svg>

													<?php } else if ( 'application/x-gzip' === $file['type'] || 'application/rar' === $file['type'] || 'application/x-7z-compressed' === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M128.3 160v32h32v-32zm64-96h-32v32h32zm-64 32v32h32V96zm64 32h-32v32h32zm177.6-30.1L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM256 51.9l76.1 76.1H256zM336 464H48V48h79.7v16h32V48H208v104c0 13.3 10.7 24 24 24h104zM194.2 265.7c-1.1-5.6-6-9.7-11.8-9.7h-22.1v-32h-32v32l-19.7 97.1C102 385.6 126.8 416 160 416c33.1 0 57.9-30.2 51.5-62.6zm-33.9 124.4c-17.9 0-32.4-12.1-32.4-27s14.5-27 32.4-27 32.4 12.1 32.4 27-14.5 27-32.4 27zm32-198.1h-32v32h32z"/>
														</svg>

													<?php } else if ( 'application/msword' === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm57.1 120H305c7.7 0 13.4 7.1 11.7 14.7l-38 168c-1.2 5.5-6.1 9.3-11.7 9.3h-38c-5.5 0-10.3-3.8-11.6-9.1-25.8-103.5-20.8-81.2-25.6-110.5h-.5c-1.1 14.3-2.4 17.4-25.6 110.5-1.3 5.3-6.1 9.1-11.6 9.1H117c-5.6 0-10.5-3.9-11.7-9.4l-37.8-168c-1.7-7.5 4-14.6 11.7-14.6h24.5c5.7 0 10.7 4 11.8 9.7 15.6 78 20.1 109.5 21 122.2 1.6-10.2 7.3-32.7 29.4-122.7 1.3-5.4 6.1-9.1 11.7-9.1h29.1c5.6 0 10.4 3.8 11.7 9.2 24 100.4 28.8 124 29.6 129.4-.2-11.2-2.6-17.8 21.6-129.2 1-5.6 5.9-9.5 11.5-9.5zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
														</svg>

													<?php } else if ( "'application/vnd.ms-powerpoint'" === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M193.7 271.2c8.8 0 15.5 2.7 20.3 8.1 9.6 10.9 9.8 32.7-.2 44.1-4.9 5.6-11.9 8.5-21.1 8.5h-26.9v-60.7h27.9zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-153 31V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm53 165.2c0 90.3-88.8 77.6-111.1 77.6V436c0 6.6-5.4 12-12 12h-30.8c-6.6 0-12-5.4-12-12V236.2c0-6.6 5.4-12 12-12h81c44.5 0 72.9 32.8 72.9 77z"/>
														</svg>
													<?php } else if ( "'application/vnd.ms-excel'" === $file['type'] ) { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
														<path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm60.1 106.5L224 336l60.1 93.5c5.1 8-.6 18.5-10.1 18.5h-34.9c-4.4 0-8.5-2.4-10.6-6.3C208.9 405.5 192 373 192 373c-6.4 14.8-10 20-36.6 68.8-2.1 3.9-6.1 6.3-10.5 6.3H110c-9.5 0-15.2-10.5-10.1-18.5l60.3-93.5-60.3-93.5c-5.2-8 .6-18.5 10.1-18.5h34.8c4.4 0 8.5 2.4 10.6 6.3 26.1 48.8 20 33.6 36.6 68.5 0 0 6.1-11.7 36.6-68.5 2.1-3.9 6.2-6.3 10.6-6.3H274c9.5-.1 15.2 10.4 10.1 18.4zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
													</svg>

													<?php } else { ?>
														<svg xmlns="http://www.w3.org/2000/svg" width="13" viewBox="0 0 384 512">
															<path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm160-14.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
														</svg>

													<?php 
													} 
														echo esc_html( $file['name'] ); 
													?>
														 
													</a>
													<?php 
												}
											}
											?>
											</div>
										<p class="text-sm text-muted m-0"><?php echo esc_html( \Carbon\Carbon::parse( $message['created_at'] )->tz( wp_timezone() )->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></p>
									</div>
									<div class="d-flex flex-column align-items-start">
										<button class="btn btn-sm btn-link text-decoration-none text-nowrap untag"><i class="material-icons">local_offer</i> <?php echo esc_html__( 'Untag', 'wschat' ); ?></button>
										<button class="btn btn-sm btn-link text-decoration-none text-nowrap change-tag"><i class="material-icons">swap_horiz</i> <?php echo esc_html__( 'Change Tag', 'wschat' ); ?></button>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php if ( count( $messages ) > 0 ) { ?>
							<div class="m-2 d-flex flex-row justify-content-between align-items-center">
								<p>Showing <span><?php echo esc_attr( ( $page_no - 1 ) * $limit + 1 ); ?></span> to <span><?php echo esc_attr( ( $page_no * $limit ) > $total ? $total : $page_no * $limit ); ?></span> of <span><?php echo esc_attr( $total ); ?></span></p>
								<div>
									<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => 1 === $page_no ? 1 : ( $page_no - 1 ) ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( 1 === $page_no ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Prev', 'wschat' ); ?></a>
									<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => $total > $page_no * $limit ? $page_no + 1 : $page_no ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( $page_no === $total_pages ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Next', 'wschat' ); ?></a>
								</div>
							</div>
						<?php } elseif ( count( $tags ) ) { ?>
							<img src="<?php echo esc_url( \WSChat\Utils::get_resource_url( '/resources/img/no-messages-assiated-with-a-tag.svg' ) ); ?>" alt="<?php echo esc_attr__( 'No messages were tagged' ); ?>"/>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="shadow bg-white d-fixed h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-add-new-tag-popup">
	<div class="d-flex mb-3">
		<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-popup').removeClass('show')"></button>
		<h5 class="flex-fill text-center"><?php echo esc_html__( 'Add New Tag', 'wschat' ); ?></h5>
	</div>
	<form id="wschat-add-new-tag-frm">
	<div class="wschat-add-new-tag-alert alert d-none"></div>
		<div class="alert hidden" role="alert"></div>
		<input type="hidden" value="wschat_admin_add_a_tag" name="action">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<div class="mb-3">
			<label for="name" class="form-label"><?php echo esc_html__( 'Tag Name', 'wschat' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="name" id="name" placeholder="<?php echo esc_attr__( 'Enter Tag Name', 'wschat' ); ?>" required >
		</div>
		<div class="mb-3">
			<label for="color" class="form-label"><?php echo esc_html__( 'Color', 'wschat' ); ?></label>
			<input type="text" value="<?php esc_attr_e( $default_tag_color ); ?>" data-jscolor="{zIndex: 500000}" class="form-control form-control-sm jscolor" name="color" id="color" autocomplete="off" required>
		</div>
		<div class="text-right">
			<button class="btn btn-primary btn-sm float-end" type="button" id="wschat-add-new-tag"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
		</div>
	</form>
</div>
<?php if ( count( $tags ) && isset( $tags[ $tag_id ] ) ) : ?>
<div class="shadow bg-white d-fixed h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-edit-tag-popup">
	<div class="d-flex mb-3">
		<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-popup').removeClass('show')"></button>
		<h5 class="flex-fill text-center"><?php echo esc_html__( 'Edit Tag', 'wschat' ); ?></h5>
	</div>
	<form id="wschat-edit-tag-frm">
		<div class="wschat-edit-tag-alert alert d-none"></div>
		<input type="hidden" value="wschat_admin_edit_a_tag" name="action">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<input type="hidden" name="id" value="<?php echo esc_attr( $tag_id ); ?>" />
		<div class="mb-3">
			<label for="name" class="form-label"><?php echo esc_html__( 'Tag Name', 'wschat' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo esc_html( $tags[ $tag_id ]['name'] ); ?>" placeholder="<?php echo esc_attr__( 'Enter Tag Name', 'wschat' ); ?>" required >
		</div>
		<div class="mb-3">
			<label for="color" class="form-label"><?php echo esc_html__( 'Color', 'wschat' ); ?></label>
			<input type="text" data-jscolor="{zIndex: 500000}" class="form-control form-control-sm jscolor" value="<?php echo esc_html( $tags[ $tag_id ]['color'] ); ?>" name="color" id="color" autocomplete="off" required>
		</div>
		<div class="text-right">
			<button class="btn btn-primary btn-sm float-end" type="button" id="wschat-edit-tag"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
		</div>
	</form>
</div>
<div class="shadow bg-white d-fixed h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-change-tag-popup">
	<div class="d-flex mb-3">
		<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-popup').removeClass('show')"></button>
		<h5 class="flex-fill text-center"><?php echo esc_html__( 'Change Tag', 'wschat' ); ?></h5>
	</div>
	<form id="wschat-change-tag-frm">
		<div class="alert hidden" role="alert"></div>
		<input type="hidden" value="wschat_admin_tag_a_message" name="action">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<input type="hidden" name="message_id" class="message_id" value="" />
		<div class="mb-3">
			<label for="name" class="form-label"><?php echo esc_html__( 'Current Tag Name', 'wschat' ); ?></label>
			<input type="text" class="form-control form-control-sm" name="name" value="<?php echo esc_attr( $tags[ $tag_id ]['name'] ); ?>" disabled >
		</div>
		<div class="bg-white h-100">
			<div class="pt-2">
				<div class="wschat-tags-search">
					<div class="input-group input-group-sm mb-3">
						<span class="input-group-text bg-white" id="basic-addon1"><i class="material-icons">search</i></span>
						<input type="text" class="search-tag form-control form-control-sm" placeholder="Search tags" aria-label="Search Tags" aria-describedby="basic-addon1">
					</div>
				</div>
			</div>
			<ul class="list-group tag-list-group pb-3">
				<?php foreach ( $tags as $tag_item ) { ?>
					<?php if ( $tag_item['id'] !== $tag_id ) { ?>
					<a href="#" class="list-group-item d-flex align-items-center" data-message-id="<?php echo esc_attr( $tag_item['id'] ); ?>">
							<span
								class="d-inline-block pr-1 rounded-circle tags-badge"
								style="background-color: #<?php echo esc_attr( $tag_item['color'] ); ?>"
							></span>
							<?php echo esc_html( $tag_item['name'] ); ?>
						</a>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
		<div class="text-right">
			<button class="btn btn-primary btn-sm float-end" type="button" id="wschat_tag_a_message"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
		</div>
	</form>
</div>
<?php endif; ?>
</div>


