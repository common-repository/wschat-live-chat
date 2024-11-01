<div class="wschat-wrapper">
	<div class="container-fluid">
		<div class="d-flex flex-row justify-content-between align-items-center">
			<h6 class="mt-3"><?php echo esc_html__( 'Chat History', 'wschat' ); ?></h6>
			<div class="align-right">
				<form method="GET">
					<input name="page" type="hidden" value="wschat_history" />
					<input name="search" type="text" placeholder="<?php esc_attr_e( 'Search', 'wschat' ); ?>" value="<?php echo esc_attr( $search ); ?>" />
				</form>
			</div>
		</div>
		<?php wp_nonce_field( 'wschat-ajax-nonce', 'wschat_ajax_nonce' ); ?>
		<table class="table table-seperated table-border-v-space table-borderless align-middle table-fixed">
			<thead>
				<tr class="bg-info">
					<th><?php echo esc_html__( 'User', 'wschat' ); ?></th>
					<th><?php echo esc_html__( 'From', 'wschat' ); ?></th>
					<th><?php echo esc_html__( 'Chat Ended on', 'wschat' ); ?></th>
					<th width="150"><?php echo esc_html__( 'Action', 'wschat' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $conversations as $conversation ) { ?>
					<tr class="shadow-sm rounded m-1">
						<td class="rounded-start bg-white">
							<?php if ( ! $conversation['user']['user_id'] && isset( $conversation['meta']['pre_chat_form']['name'] ) ) : ?>
								<?php echo esc_attr( $conversation['meta']['pre_chat_form']['name']['value'] ); ?>
							<?php else : ?>
								<?php echo esc_attr( $conversation['user']['meta']['name'] ); ?>
							<?php endif; ?>
						</td>
						<td class="bg-white">
							<div class="d-inline-block browser <?php echo esc_attr( strtolower( $conversation['user']['meta']['browser'] ) ); ?>" title="<?php echo esc_attr( strtolower( $conversation['user']['meta']['browser'] ) ); ?>"></div>
							<div class="d-inline-block os <?php echo esc_attr( strtolower( $conversation['user']['meta']['os'] ) ); ?>" title="<?php echo esc_attr( strtolower( $conversation['user']['meta']['os'] ) ); ?>"></div>
						</td>
						<td class="bg-white"><?php echo esc_attr( isset( $conversation['meta']['session']['ended_at'] ) ? $conversation['meta']['session']['ended_at'] : $conversation['updated_at'] ); ?></td>
						<td class="rounded-end bg-white">
							<a title="<?php esc_attr_e( 'View', 'wschat' ); ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View History"
								data-bs-custom-class="tooltip-white" class="btn btn-sm rounded " href="
						<?php
						echo esc_url(
							wp_nonce_url(
								add_query_arg(
									array(
										'page'            => 'wschat_history',
										'conversation_id' => $conversation['id'],
									)
								)
							)
						);
						?>
						">
								<svg height="24" width="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
									<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
								</svg>
							</a>
<?php if ( \WSChat\WSAgent::can( 'wschat_delete_chat' ) ) : ?>
							<a title="<?php esc_attr_e( 'Delete', 'wschat' ); ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"
								data-bs-custom-class="tooltip-danger" class="btn btn-sm rounded delete-conversation" data-conversation-id="<?php echo esc_attr( $conversation['id'] ); ?>">
								<svg xheight="24" width="24" mlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
									<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
								</svg>
							</a>
										<?php endif; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php if ( count( $conversations ) === 0 ) { ?>
			<div class="row h-100 justify-content-center align-items-center">
				<div class="col-lg-6 col-md-8 col-10">
					<img src="<?php echo esc_url( \WSChat\Utils::get_resource_url( '/resources/img/conversation/empty_chat_history_illustration.svg' ) ); ?>" alt="" class="w-100">
					<h3 class="text-center my-3"><?php echo esc_attr__( 'No History found' ); ?></h3>
				</div>
			</div>
		<?php } ?>
		<div class="pb-2 d-flex flex-row justify-content-between align-items-center">
			<p class="m-0">
				<?php
				/* translators: %d showing from */
				esc_attr_e( sprintf( 'Showing %d to %d of %d', ( $page_no - 1 ) * $limit + 1, ( ( $page_no * $limit ) > $total ? $total : $page_no * $limit ), $total ), 'wschat' );
				?>
			</p>
			<div>
				<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => 1 === $page_no ? 1 : ( $page_no - 1 ) ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( 1 === $page_no ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Prev', 'wschat' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => $total > $page_no * $limit ? $page_no + 1 : $page_no ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( $page_no === $total_pages ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Next', 'wschat' ); ?></a>
			</div>
		</div>
	</div>
</div>

