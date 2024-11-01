<?php

namespace WSChat\PreChatForm\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Pre Chat Form field
 *
 * @property string $type
 * @property bool $deletable
 * @property bool $status
 * @property bool $mandatory
 * @property string $name
 * @property string $slug
 * @property string $placeholder
 * @property FormFieldOption[] $options
 */
class FormField extends Fluent {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE   = 1;

	const TYPE_TEXT     = 'text';
	const TYPE_TEXTAREA = 'textarea';
	const TYPE_EMAIL = 'email';
	const TYPE_NUMBER = 'number';
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_RADIO    = 'radio';
	const TYPE_SELECT   = 'select';

	public function active() {
		return $this->get( 'status', self::STATUS_INACTIVE ) === self::STATUS_ACTIVE;
	}

	public function deactive() {
		return ! $this->active();
	}

	public function activate() {
		$this->status = self::STATUS_ACTIVE;

		return $this;
	}

	public function deactivate() {
		$this->status = self::STATUS_INACTIVE;

		return $this;
	}

	/**
	 * Check whether this field is mandatory or not
	 *
	 * @return bool
	 */
	public function mandatory() {
		return $this->get( 'mandatory', false );
	}

	public function deletable() {
		return $this->get( 'deletable', true );
	}

	public function add_option( FormFieldOption $option ) {
		$options = $this->get( 'options', array() );

		$options[] = $option;

		$this->options = $options;

		return $this;
	}

	public function has_option( FormFieldOption $option ) {
		$options = $this->get( 'options', array() );

		foreach ( $options as $existing_option ) {
			if ( $existing_option->value() === $option->value() ) {
				return true;
			}
		}

		return false;
	}

	public function remove_option( FormFieldOption $option ) {
		$options = $this->get( 'options', array() );

		$options = array_filter(
			$options,
			function ( $existing_option ) use ( $option ) {
				return $existing_option->value() === $option->value();
			}
		);

		$this->options = $options;

		return $this;
	}

	public static function build( array $attributes ) {
		$options = Arr::get( $attributes, 'options', array() );

		$attributes = Arr::except( $attributes, 'options' );

		$self = new self( $attributes );

		foreach ( $options as $option ) {
			$self->add_option( FormFieldOption::build( $option ) );
		}

		return $self;
	}

	public function toArray() {
		$data = parent::toArray();

		$options = Arr::get( $data, 'options', array() );

		$data['options'] = array();

		foreach ( $options as $option ) {
			$data['options'][] = $option->toArray();
		}

		return $data;
	}

	public function must_have_options() {
		return in_array( $this->type, array( self::TYPE_TEXT, self::TYPE_TEXTAREA, self::TYPE_EMAIL, self::TYPE_NUMBER ), true ) === false;
	}

	public function has_options() {
		return count( $this->options ) > 0;
	}
}
