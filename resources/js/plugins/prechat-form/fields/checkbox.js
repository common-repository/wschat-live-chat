export class FieldCheckbox {

	constructor(data) {
		this.data = data;
	}

	template = `
		<div class="">
            <label class="form-label"></label>
			<div class="invalid-feedback"></div>
		</div>
	`;

	checkbox_template = `<div class="form-check">
      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
      <label class="form-check-label" for="flexCheckDefault">
        Default checkbox
      </label>
    </div>`;

	render() {
	    const element = jQuery(this.template);
        // element.find('label').text(this.data.name);
        element.find('label').html(this.data.name + ( this.data.mandatory ? '<sup class="text-danger">*</sup>' : '' ));

	    this.data.options.forEach(option => {
			let box = jQuery(this.checkbox_template);

			box.find('.form-check-input').val(option.label)
				.attr('name', this.data.slug+'[]')
				.attr('id', 'field_checkbox_' + option.value);


			box.find('.form-check-label').attr('for', 'field_checkbox_' + option.value)
				.text(option.label);

			element.append(box);
	    });

	    return element;
	}
}


