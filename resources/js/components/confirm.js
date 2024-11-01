import {useState} from '@wordpress/element';

export const Confirm = props => {
  	const [show, setShow] = useState(props.show || false);

  	const handleClose = () => setShow(false);
  	const handleShow = () => setShow(true);

  	return (
    	<>
    		<div className={`modal fade  ${ show ? "show d-block" : ''}`} id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabIndex="-1">
  				<div className="modal-dialog modal-dialog-centered">
    				<div className="modal-content">
      					<div className="modal-header">
        					<h1 className="modal-title fs-5" id="exampleModalToggleLabel">{props.title}</h1>
        					<button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      					</div>
      					<div className="modal-body">
        					{props.message}
      					</div>
      					<div className="modal-footer">
        					<button
        						type="button"
        						className="btn btn-secondary"
        						data-bs-dismiss="modal"
        						onClick={handleClose}
        					>Close</button>
        					<button
        						type="button"
        						className="btn btn-primary"
        						onClick={handleShow}
        					>Save changes</button>
      					</div>
    				</div>
  				</div>
			</div>
    </>
  );
}

