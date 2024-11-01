<?php
use WSChat\WSAgent;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wschat_roles = array_keys( WSAgent::get_roles() );

$roles_not_in = array_merge( $wschat_roles, array( 'Customer', 'Subscriber' ) );
$users_data   = get_users(
	array(
		'role__not_in' => $roles_not_in,
	)
);
$users        = array();
$select       = array();
for ( $i = 0;$i < count( $users_data );$i++ ) {
	$current = $users_data[ $i ];
	$temp    = array();
	$current_roles   = $current->roles;
	foreach ( $current_roles as $value ) {
		$current_role = $value;
		$temp[ $i ]   = ucfirst( str_replace( '_', ' ', $current_role ) );
	}
	$users[ implode( ' & ', $temp ) ][ $current->ID ] = $current->data->display_name;
	$select[ $current->ID ]                           = md5( $current->data->user_email );
}
?>

<div class="wschat-wrapper wschat-pages">
	<div class="container-fluid py-3 mt-3">
		<div class="d-flex justify-content-between align-items-center mt-3">
			<h6 class=""><?php echo esc_attr__( 'Agents', 'wschat' ); ?></h6>
			<a class="btn btn-primary btn-sm rounded px-4" onclick="jQuery('.add-agent-popup').toggleClass('show')">
				<?php echo esc_attr__( 'Add Agent', 'wschat' ); ?>
			</a>
		</div>
		<?php wp_nonce_field( 'delete-an-agent', 'delete-an-agent' ); ?>
		<table class="table table-seperated table-border-v-space table-borderless align-middle table-fixed">
			<thead>
				<tr class="bg-info">
					<th><?php echo esc_attr__( 'Agent Name', 'wschat' ); ?></th>
					<th><?php echo esc_attr__( 'Email', 'wschat' ); ?></th>
					<th><?php echo esc_attr__( 'Role', 'wschat' ); ?></th>
					<th width="150"><?php echo esc_attr__( 'Action', 'wschat' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $agents as $agent ) { ?>
					<tr class="shadow-sm rounded m-1">
						<td class="rounded-start bg-white"><?php echo esc_attr( $agent->user_login ); ?></td>
						<td class="bg-white">
							<?php echo esc_attr( $agent->user_email ); ?>
						</td>
						<td class="bg-white">
							<?php
							global $wp_roles;
							foreach ( $agent->roles as $agent_role ) {
								if ( isset( $wp_roles->roles[ $agent_role ] ) ) {
									echo esc_attr( $wp_roles->roles[ $agent_role ]['name'] ) . ' ';
								}
							}
							?>
						</td>
						<td class="rounded-end bg-white d-flex justify-content-between align-items-center gap-1">
							<?php if ( in_array( 'administrator', $agent->roles, true ) === false ) { ?>
								<button type="button" class="btn btn-outline-primary btn-sm rounded d-flex align-items-center gap-1 wschat-edit-agent-trigger" onclick="jQuery('.wschat-edit-agent-popup').toggleClass('show')" data-id="<?php echo esc_attr( $agent->ID ); ?>">
									<i class="material-icons ">edit</i>
									<?php echo esc_attr__( 'Edit' ); ?>
								</button>
								<button type="button" class="btn btn-outline-danger btn-sm rounded d-flex align-items-center gap-1 delete-agent" data-id="<?php echo esc_attr( $agent->ID ); ?>">
									<i class="material-icons ">delete</i>
									<?php echo esc_attr__( 'Delete' ); ?>
								</button>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<div class="mt-2 d-flex flex-row justify-content-between align-items-center">
			<p>Showing <span><?php echo esc_attr( ( $page_no - 1 ) * $limit + 1 ); ?></span> to <span><?php echo esc_attr( ( $page_no * $limit ) > $total ? $total : $page_no * $limit ); ?></span> of <span><?php echo esc_attr( $total ); ?></span></p>
			<div>
				<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => 1 === $page_no ? 1 : ( $page_no - 1 ) ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( 1 === $page_no ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Prev', 'wschat' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page_no' => $total > $page_no * $limit ? $page_no + 1 : $page_no ) ) ); ?>" class="btn btn-sm btn-outline-primary <?php echo esc_html( $page_no === $total_pages ? 'disabled' : '' ); ?>"><?php echo esc_html__( 'Next', 'wschat' ); ?></a>
			</div>
		</div>
	</div>
	<div class="bg-white d-fixed h-100 p-3 top-0 w-25 d-flex flex-column shadow overflow-auto wschat-popup wschat-popup-right add-agent-popup">
		<div class="d-flex mb-3">
			<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.add-agent-popup').toggleClass('show')"></button>
			<h5 class="flex-fill text-center"><?php echo esc_html__( 'Add Agent', 'wschat' ); ?></h5>
		</div>
		<form id="wschat-add-new-agent-frm" class="flex-fill position-relative d-flex flex-column pb-5">
			<div class="alert hidden" role="alert"></div>
			<input type="hidden" value="wschat_edit_existing_agent" name="action">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'add-an-agent' ) ); ?>" />
			<div class="mb-3">
				<input type="checkbox" id="new-user">
				<label for="new-user" class="form-label"><?php echo esc_html__( 'Create new user', 'wschat' ); ?></label>
			</div>
			<div class="mb-3" id="usernameContainer" style="display: none;">
				<label for="RoleName" class="form-label"><?php echo esc_html__( 'Username', 'wschat' ); ?></label>
				<input type="text" class="form-control form-control-sm" name="name" id="RoleName" placeholder="Enter Username">
			</div>
			
			<div class="mb-3" id="emailContainer" style="display: none;">
				<label for="email" class="form-label"><?php echo esc_html__( 'Email', 'wschat' ); ?></label>
				<input type="email" class="form-control form-control-sm" name="email" id="email" placeholder="Enter Email">
			</div>
			<div id="existing-user-Container">
				<div class="mb-3">
					<label for="existingUsers" class="form-label"><?php echo esc_html__( 'Search Existing Users', 'wschat' ); ?></label>
						<select name="existingUsers" id="existingUsers" class="form-control form-select elex_select2 " >
						<?php
						foreach ( $users as $key => $value ) {
							?>
								<optgroup label="<?php echo esc_html( $key ); ?>">
							<?php
							foreach ( $value as $uid => $name ) {
								?>
									<option value="<?php echo esc_html( $uid ); ?>"><?php echo esc_html( $name ); ?></option>
									<?php
							}
							?>
								</optgroup>
							<?php
						}
						?>
					</select>
				</div>
			</div>
			<div class="mb-3">
				<label for="role" class="form-label"><?php echo esc_html__( 'Role', 'wschat' ); ?></label>
				<select name="role" class="form-select form-select-sm w-100">
					<?php foreach ( $roles as $slug => $agent_role ) { ?>
						<option value="<?php echo esc_html( $slug ); ?>"><?php echo esc_html( $agent_role['name'] ); ?></option>
					<?php } ?>
				</select>
			</div>
			<label for="capabilities" class="form-label"><?php echo esc_html__( 'Capabilities', 'wschat' ); ?></label>
			<?php foreach ( $wschat_capabilities as $i => $capability ) { ?>
				<div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-2 rounded mb-1 capability-item">
					<label for="wschat-add-new-agent-capability-<?php echo esc_attr__( $i ); ?>" class="form-label m-0"><?php echo esc_html( $capability ); ?></label>
					<label class="switch d-none">
						<input disabled type="checkbox" id="wschat-add-new-agent-capability-<?php echo esc_attr__( $i ); ?>" class="wschat-role-capability" name="wschat_role_capability[<?php echo esc_html( $capability ); ?>]" />
						<span class="slider round"></span>
					</label>
					<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
					<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
				</div>
			<?php } ?>
			<div class="position-absolute w-100 bottom-0 start-0">
				<button class="btn w-100 btn-primary btn-sm float-end" type="button" id="wschat-add-new-agent-btn"><?php echo esc_html__( 'Submit', 'wschat' ); ?></button>
			</div>
		</form>
	</div>
	<div class="bg-white d-fixed h-100 p-3 top-0 w-25 wschat-popup wschat-popup-right wschat-edit-agent-popup">
		<div class="d-flex mb-3">
			<button type="button" class="btn-close" aria-label="Close" onclick="jQuery('.wschat-edit-agent-popup').toggleClass('show')"></button>
			<h5 class="flex-fill text-center"><?php echo esc_html__( 'Edit Agent', 'wschat' ); ?></h5>
		</div>
		<form id="wschat-edit-agent-frm">
			<div class="alert hidden" role="alert"></div>
			<input type="hidden" value="wschat_edit_agent" name="action">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'edit-an-agent' ) ); ?>" />
			<div class="mb-3">
				<label for="RoleName" class="form-label"><?php echo esc_html__( 'Username', 'wschat' ); ?></label>
				<input type="text" class="form-control form-control-sm" disabled name="name" placeholder="Enter Username">
				<input type="hidden" class="form-control form-control-sm" name="id">
			</div>
			<div class="mb-3">
				<label for="email" class="form-label"><?php echo esc_html__( 'Email', 'wschat' ); ?></label>
				<input type="email" class="form-control form-control-sm" name="email" placeholder="Enter Email">
			</div>
			<div class="mb-3">
				<label for="role" class="form-label"><?php echo esc_html__( 'Role', 'wschat' ); ?></label>
				<select name="role" class="form-select form-select-sm w-100">
					<?php foreach ( $roles as $slug => $agent_role ) { ?>
						<option value="<?php echo esc_html( $slug ); ?>"><?php echo esc_html( $agent_role['name'] ); ?></option>
					<?php } ?>
				</select>
			</div>
			<label for="capabilities" class="form-label"><?php echo esc_html__( 'Capabilities', 'wschat' ); ?></label>
			<?php foreach ( $wschat_capabilities as $capability ) { ?>
				<div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-1 capability-item">
					<label for="capabilities" class="form-label m-0"><?php echo esc_html( $capability ); ?></label>
					<label class="switch d-none">
						<input disabled type="checkbox" disabled class="wschat-role-capability" name="wschat_role_capability[<?php echo esc_html( $capability ); ?>]" />
						<span class="slider round"></span>
					</label>
					<span class="d-none switch-label-on"><?php echo esc_attr__( 'On', 'wschat' ); ?></span>
					<span class="d-none switch-label-off"><?php echo esc_attr__( 'Off', 'wschat' ); ?></span>
				</div>
			<?php } ?>
			<div class="text-right">
				<button type="submit" class="btn btn-primary btn-sm float-end" type="button" id="wschat-edit-agent-btn"><?php echo esc_html__( 'Save & Update', 'wschat' ); ?></button>
			</div>
		</form>
	</div>
</div>
<script>
		window.roles = <?php echo wp_json_encode( $roles ); ?>;
		window.agents =
			<?php
			echo wp_json_encode(
				array_map(
					function ( $agent ) {
						return array(
							'ID'           => $agent->ID,
							'display_name' => $agent->display_name,
							'user_email'   => $agent->user_email,
							'roles'        => $agent->roles,
							'caps'         => $agent->get_role_caps(),
						);
					},
					$agents
				)
			);
			?>
			;

</script>
