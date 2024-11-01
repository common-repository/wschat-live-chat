export class FieldNumber {

	constructor(data) {
		this.data = data;
	}

	template = `
		<div class="">
            <label class="form-label"></label>
			<input type="number" name="prechat-form-text-input" class="form-control form-control-sm" />
			<div class="invalid-feedback"></div>
		</div>
	`;

	render() {
	    const element = jQuery(this.template);

        element.find('input').attr('name', this.data.slug)
        	.attr('required', this.data.mandatory)
        	.on('keypress', evt => {
        		evt = (evt) ? evt : window.event;
    			var charCode = (evt.which) ? evt.which : evt.keyCode;
    			if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        			return false;
    			}
    			return true;
        	});

        element.find('label').html(this.data.name + ( this.data.mandatory ? '<sup class="text-danger">*</sup>' : '' ));

        return element;
	}
}

