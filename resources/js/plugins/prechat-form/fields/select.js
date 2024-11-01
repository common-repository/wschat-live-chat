export class FieldSelect {

	constructor(data) {
		this.data = data;
	}

	template = `
		<div class="">
            <label class="form-label"></label>
            <select class="form-select">
            </select>
			<div class="invalid-feedback"></div>
		</div>
	`;

	render() {
	    const element = jQuery(this.template);
        element.find('label').html(this.data.name + ( this.data.mandatory ? '<sup class="text-danger">*</sup>' : '' ));
        const select = element.find('select');

        select.attr('name', this.data.slug);
        select.attr('required', this.data.mandatory)

	    this.data.options.forEach(option => {
			let box = `<option value="${option.value}">${option.label}</option>`;

			select.append(box);
	    });

	    return element;
	}
}

