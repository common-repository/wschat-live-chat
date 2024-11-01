export class FieldEmail {

	constructor(data) {
		this.data = data;
	}

	template = `
		<div class="">
            <label class="form-label"></label>
			<input type="email" name="prechat-form-text-input" class="form-control form-control-sm" />
			<div class="invalid-feedback"></div>
		</div>
	`;

	render(user) {
	    const element = jQuery(this.template);

        element.find('input').attr('name', this.data.slug)
        	.attr('required', this.data.mandatory);

        if (this.data.slug === 'email' && parseInt(user.user_id) && user.meta) {
			element.find('input').val(user.meta ? user.meta.email : '');
        }

        element.find('label').html(this.data.name + ( this.data.mandatory ? '<sup class="text-danger">*</sup>' : '' ));

        return element;
	}
}

