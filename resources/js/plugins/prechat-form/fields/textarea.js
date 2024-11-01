export class FieldTextarea {

	constructor(data) {
		this.data = data;
	}

	template = `
		<div class="">
            <label class="form-label"></label>
			<textarea name="prechat-form-text-input" class="form-control form-control-sm" ></textarea>
			<div class="invalid-feedback"></div>
		</div>
	`;

	render() {
	    const element = jQuery(this.template);

        element.find('textarea')
            .attr('name', this.data.slug)
        	.attr('required', this.data.mandatory);

        element.find('label').html(this.data.name + ( this.data.mandatory ? '<sup class="text-danger">*</sup>' : '' ));

        return element;
	}
}

