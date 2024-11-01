<?php

namespace WSChat\PreChatForm\Fields;

use Illuminate\Support\Fluent;

class FormFieldOption extends Fluent {

	public function value() {
		return $this->value ? $this->value : \Illuminate\Support\Str::slug( $this->label );
	}

	public function label() {
		return $this->label;
	}

	public function setValue( $value ) {
		$this->value = $value ? $value : \Illuminate\Support\Str::slug( $this->label );
	}

	public function setLabel( $label ) {
		$this->label = $label;
	}

	/**
	 * Build FormField statically
	 *
	 * @param string|array|self $value
	 * @param string $label
	 *
	 * @return self
	 */
	public static function build( $label, $value = null ) {
		if ( $label instanceof self ) {
			return $label;
		}

		if ( is_array( $label ) ) {
			return new self( $label );
		}

		$self = new self();

		$self->setLabel( $label );
		$self->setValue( $value );

		return $self;
	}
}
