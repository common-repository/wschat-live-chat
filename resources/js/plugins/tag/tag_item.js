export const TagItem = props => {

	const assignTag =() => {
			props.onAssign(props.tag);
	}

	return (
		<div onClick={assignTag} key={props.tag.id} className="position-relative ps-4 p-1 elex-wschat-tags-li">
            <span
                className="position-absolute top-50 start-0 mx-2 translate-middle p-2 border border-light rounded-circle"
                style={{backgroundColor: '#' + props.tag.color}}
            ></span>
            <p className="mb-0 xs text-dark">{props.tag.name}</p>
        </div> 
    );
}
