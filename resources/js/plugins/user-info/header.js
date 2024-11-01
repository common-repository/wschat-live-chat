const Header = props => {
	return (
		<div className="d-flex gap-2 align-items-center mb-2">
			<button onClick={props.onClose} className="btn btn-sm rounded-circle elex-ws-chat-customer-info-close-button">
				<svg xmlns="http://www.w3.org/2000/svg" width="9.313" height="9.313"
					viewBox="0 0 9.313 9.313">
					<path id="Icon_ionic-md-close" data-name="Icon ionic-md-close"
						d="M12.656,4.275l-.931-.931L8,7.069,4.275,3.344l-.931.931L7.069,8,3.344,11.725l.931.931L8,8.931l3.725,3.725.931-.931L8.931,8Z"
						transform="translate(-3.344 -3.344)" />
				</svg>
			</button>
			<p className="mb-0"><b>Customer Information</b></p>
		</div>
	);
}

export default Header;
