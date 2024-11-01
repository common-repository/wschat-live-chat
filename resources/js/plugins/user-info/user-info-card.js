const UserInforCard = props => {
	const location = get_location(props.conversation);
	return (
		<div className="border-0 border-bottom border-secondary mb-2">
			<div className="d-flex gap-3 mb-2">
				<div className="elex-ws-chat-list-profile-pic">
					<div className="ratio ratio-1x1 rounded-circle overflow-hidden">
						<img src={props.user.meta.avatar} alt="" />
					</div>
				</div>

				<div className="d-flex flex-column flex-fill justify-content-center">
					<h6 className="mb-1">{props.user.meta.name}</h6>
					<div className="xs text-secondary">{props.user.meta.email}</div>
				</div>
				<div className="align-items-center bg-white d-flex justify-content-center">
					<div className={"d-inline-block browser " + props.user.meta.browser.toLowerCase()} title={props.user.meta.browser}></div>
					<div className={"d-inline-block os " + props.user.meta.os.toLowerCase()} title={props.user.meta.os}></div>
				</div>
			</div>

            {location && location.country ? <div className="d-flex xs text-secondary mb-1 align-items-center">
                <i className="fa-solid fa-location-crosshairs"></i> {location.regionName}, {location.country}
            </div>: ''}
            {props.conversation.time_spent ? <div className="d-flex xs text-secondary mb-1 align-items-center">
                <i className="fa-regular fa-clock"></i> &nbsp; {props.conversation.time_spent.hour}h {props.conversation.time_spent.minutes}m
            </div>: ''}

		</div>
	);
}

const get_location = (conversation) => {
	if (!conversation.meta.geo_ips) {
		return;
	}

	const ip = Object.keys(conversation.meta.geo_ips).pop();

	if (!ip) {
		return;
	}

	return conversation.meta.geo_ips[ip];
}

export default UserInforCard
