<?php
namespace WSChat\PreChatForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use WSChat\PreChatForm\Fields\FormField;
use WSChat\WSAgent;
use WSChat\WSConversation;
use WSChat\WSUser;

/**
 * The pre chat form
 *
 * @property bool $enabled
 * @property int $mode
 * @property string $label
 * @property FormField[] $fields
 */
class PreChatForm extends Fluent {
	const MODE_ALL     = 0;
	const MODE_OFFLINE = 1;

	public function enable() {
		$this->enable = true;
		return $this;
	}

	public function disable() {
		$this->enable = false;
		return $this;
	}

	public function enabled() {
		return $this->get( 'enable', false );
	}

	public function disabled() {
		return $this->enabled() === false;
	}

	public function is_mode( $mode ) {
		return (int) $this->mode === (int) $mode;
	}

	/**
	 * Get all the fields
	 *
	 * @return FormField[]
	 */
	public function get_fields() {
		return $this->get( 'fields', array() );
	}

	public function set_fields( array $fields ) {
		$this->fields = $fields;
	}

	public function add_fields( array $fields ) {
		foreach ( $fields as $field ) {
			$this->add_field( FormField::build( $field ) );
		}

		return $this;
	}

	public function add_field( FormField $field ) {
		$fields = $this->get( 'fields', array() );

		$fields[] = $field;

		$this->fields = $fields;

		return $this;
	}

	public function has_field( FormField $field ) {
		$fields = $this->get( 'fields', array() );

		foreach ( $fields as $existing_field ) {
			if ( $existing_field->name === $field->name ) {
				return true;
			}
			if ( $existing_field->slug === $field->slug ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Find a field
	 *
	 * @param string $name
	 *
	 * @return FormField|bool
	 */
	public function find_field( $name ) {
		$fields = $this->get( 'fields', array() );

		foreach ( $fields as $existing_field ) {
			if ( $existing_field->name === $name ) {
				return $existing_field;
			}
		}

		return false;
	}

	public function remove_field( FormField $field ) {
		$fields = $this->get( 'fields', array() );

		$fields = array_filter(
			$fields,
			function ( $existing_field ) use ( $field ) {
				return $existing_field->name !== $field->name;
			}
		);

		$this->fields = $fields;

		return $this;
	}

	public static function build( array $attributes ) {
		$fields = Arr::get( $attributes, 'fields', array() );

		$attributes = Arr::except( $attributes, 'fields' );

		$self = new self( $attributes );

		foreach ( $fields as $field ) {
			$self->add_field( FormField::build( $field ) );
		}

		$self->fields = $self->fields ? $self->fields : array();

		return $self;
	}

	public function toArray() {
		$data = parent::toArray();

		$fields = Arr::get( $data, 'fields', array() );

		$data['fields'] = array();

		foreach ( $fields as $field ) {
			if ( null !== $field ) {
				$data['fields'][] = $field->toArray();
			}
		}

		return $data;
	}

	public function needs_to_show_form() {
		if ( $this->disabled() ) {
			return false;
		}

		$user = ( new WSUser() )->get_user( false );

		if ( false === $user ) {
			if ( $this->is_mode( self::MODE_OFFLINE ) && WSAgent::is_agents_online( WSAgent::get_agents( array( 'fields' => 'ids' ) ) ) === true ) {
				return false;
			}

			return true;
		}

		$conversation = ( new WSConversation() )->get_conversation( $user );

		if ( ! $conversation->is_session_ended() ) {
			return false;
		}

		if ( $this->is_mode( self::MODE_OFFLINE ) && $conversation->are_agents_online() === true ) {
			return false;
		}

		return true;
	}
}
