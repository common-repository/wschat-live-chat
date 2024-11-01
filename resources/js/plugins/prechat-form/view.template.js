const template = `
<div class="row mb-1 conversation-pre-chat-form d-none">
	<div class="col ">
		<h3 class="pre-chat-form-title">Prechat form  data</h3>
		<table class="table pre-chat-form-table">
			<tbody></tbody>
		</table>
	</div>
</div>
`;
const View = (data) => {
	const el = jQuery(template);

	const render = () => {

		const table = el.find('.pre-chat-form-table tbody');

		data.forEach(field => {
			table.append(`
				<tr>
					<th>${field.name} ${field.mandatory ? `<sup class="text-danger">*</sup>` : ''}</th>
					<td>${field.value}</td>
				</tr>
			`);
		})
		return el;
	}

	const title = (title_text) => {
		el.find('.pre-chat-form-title').text(title_text);
	}

	return  {
		render,
		title
	}

}


export {
	View
}
