<?php

namespace WSChat\Integrations\Dialogflow;

use Illuminate\Support\Arr;
use WSChat\Models\Message;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class Client {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function detect_intent( Message $message ) {
		$session_client = new SessionsClient(
			array(
				'credentials' => $this->settings->get_credentials(),
			)
		);

		$session_name = $session_client->sessionName( $this->settings->get_project_id(), $message->conversation_id );

		$text_input = new TextInput();
		$text_input->setText( $message->get_text() );
		$text_input->setLanguageCode( $this->settings->get_language_code() );

		$query_input = new QueryInput();
		$query_input->setText( $text_input );

		$response = $session_client->detectIntent( $session_name, $query_input );
		$session_client->close();

		$json_response = $response->getQueryResult()->serializeToJsonString();

		$json_response = json_decode( $this->normalize_json( $json_response ), true );

		return $json_response;
	}

	public function get_fulfillment_text( $json_response ) {
		return Arr::get( $json_response, 'fulfillmentText', __( 'Sorry,can you please say that again!', 'wschat' ) );
	}

	public function normalize_json( $input ) {
		return preg_replace( '/("[^"]*":)([,}])/', '$1null$2', $input );
	}

}
