export const CreateTag = props => {

	const createTag = () => {
		const data = {
			name: props.search,
			action: 'wschat_admin_add_a_tag'
		};
		jQuery.post(wschat_ajax_obj.ajax_url, data, res => {
			props.onCreate(res.data.tag);
		});
	};

	return (
		<div
			class="d-flex flex-column justify-content-between gap-1 message-item-no-tag-found" >
            <div class="text-center">
                <div class="xs ">'{props.search}'</div>
                <div class="xs">Not Found in the List</div>
            </div>

            <button class="btn btn-sm btn-primary w-100" onClick={createTag}>Add to Tags & Assign</button>
        </div>
	);
}
