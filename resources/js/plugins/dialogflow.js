import { EVENTS } from '../events';

/**
 * Dialogflow plugin
 */
export class DialogflowPlugin {
    constructor(chat) {
        this.chat = chat;
    }

    init() {
        this.chat.on(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, message => this.format_content(message));
        const chat = this.chat;
        this.chat.$el.on('click', '.wschat-card-response .card-link:not(.has-link), .wschat-suggestions-response .btn.rounded-pill, .wschat-quick-replies-response .btn.rounded-pill', function(e) {
        	e.preventDefault();
        	chat.send_message({
            	wschat_ajax_nonce: wschat_ajax_obj.nonce,
            	type: 'text',
            	'content[text]': jQuery(this).text()
        	});
        });
    }

    format_content(message) {
        if (!message.body.dialogflow || !message.body.dialogflow.messages) {
            return message;
        }

    	message.body.dialogflow.messages.fulfillmentMessages.forEach( response => {
			Object.keys(response).forEach(type => {
				if (!this.responses[type]) {
					return;
				}
        		message.body.formatted_content = message.body.formatted_content || '';
				message.body.formatted_content += this.responses[type](response[type]);
			});
        });

        return message;
    }
}

const ImageResponse = image => {
	return `
		<div class="wschat-image-response mb-2">
			<img src="${image.imageUri}" class="img-fluid" />
		</div>
	`;
};

const CardResponse = card => {
	return `
		<div class="wschat-card-response card mb-2" >
			  <img class="card-img-top" src="${card.imageUri}" >
  	  	  <div class="card-body">
    		<h5 class="card-title">${card.title}</h5>
    		<h6 class="card-subtitle mb-2 text-muted">${card.subtitle}</h6>
    		${card.buttons.map(button => `<a class="card-link ${button.postback ? 'has-link' : ''}" target="_blank" href="${button.href || button.postback || '#'}">${button.text}</a>`).join(' ')}
  	  	  </div>
		</div>
	`;
};

const SuggestionsResponse = suggestions => {
	return `
		<div class="wschat-suggestions-response mb-2" >
			${suggestions.suggestions.map(suggestion => `<button type="button" class="btn btn-sm btn-primary rounded-pill mr-1">${suggestion.title}</button>`).join('')}
		</div>
	`
};

const QuickRepliesResponse = quickReplies => {
	return `
		<div class="wschat-quick-replies-response mb-2" >
			${quickReplies.quickReplies.map(suggestion => `<button type="button" class="btn btn-sm btn-primary rounded-pill mr-1">${suggestion}</button>`).join('')}
		</div>
	`;
};

const TextResponse = text => {
	return `
		<div class="wschat-text-response mb-2" >
			${text.text.join(' ')}
		</div>
	`;
};

const ListSelectResponse = list => {
	return `
		<div class="wschat-list-select-response mb-2" >
			<h3>${list.title}</h3>
			${list.items.map(item => {
				const card = {title: item.title, subtitle: item.description, imageUri: item.image.imageUri, buttons: []};
				return CardResponse(card)
			}).join(' ')}
		</div>
	`;
};

DialogflowPlugin.prototype.responses = {
	image: ImageResponse,
	card: CardResponse,
	suggestions: SuggestionsResponse,
	quickReplies: QuickRepliesResponse,
	// text: TextResponse,
	listSelect: ListSelectResponse,
};

