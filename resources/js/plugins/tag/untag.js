import {useState} from '@wordpress/element';

export const UnTag = props => {
    const [tag, setTag] = useState(props.tag);

    const unTag = () => {
        const data = {
            message_id : props.message.id,
            action: 'wschat_admin_untag_a_message',
        };

        jQuery.post(ajaxurl, data, res => {
            props.onUnTag(res);
            setTag(undefined);
        });
    }

	return (
		tag ? <a hre="#" className="d-block btn btn-sm btn-link unlink-tag" onClick={unTag}><h6>Untag</h6></a> : ''
	);
}
