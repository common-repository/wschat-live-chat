import { EVENTS } from '../events';

/**
 * File attachments plugin
 */
export class FileAttachmentPlugin {
    constructor(chat) {
        this.chat = chat;
        this.files = [];
    }

    init() {
        this.chat.on(EVENTS.WSCHAT_BUILD_FORM_DATA, frmData => this.build_form_data(frmData));
        this.chat.on(EVENTS.WSCHAT_ON_SENT_A_MESSAGE, () => this.reset_files());
        this.chat.on(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, message => this.format_content(message));
        this.chat.on(EVENTS.WSCHAT_CAN_SEND_EMPTY_MESSAGE, () => this.files.length > 0 ? true : false);

        const picker = jQuery(BTN_TEMPLATE);
        this.chat.$el.find('.attachment-wrapper .attachment-list > div ').append(picker);

        this.file_input = jQuery(FILE_INPUT_TEMPLATE);
        this.chat.$el.find('.attachment-wrapper').append(this.file_input);

		this.preview_container = jQuery(PREVIEW_TEMPLATE);
		this.chat.$el.find('.chat-box-footer').prepend(this.preview_container);

        this.file_input.change(e => this.on_input_change(e));
        picker.click(() => this.file_input.trigger('click'));
    }

    on_input_change(e) {
        let total_upload_size = 0;
        this.files.forEach(file => {
        	total_upload_size += file.size;
        });
        const valid_files = Array.from(e.target.files).filter(file => {
        	if ( wschat_ajax_obj.allowed_mime_types.indexOf(file.type) < 0 ) {
        		return false;
        	}

        	if ( total_upload_size+file.size > wschat_ajax_obj.max_upload_size ) {
        		return false;
        	}

			total_upload_size += file.size;
			this.files.push(file);
        	return true;
        });

        if (e.target.files.length > valid_files.length ) {
        	this.show_file_mime_or_size_error();
        }

        if(valid_files.length === 0){
        	return;
        }
        this.render_preview();
    }

    show_file_mime_or_size_error() {
		const toast = jQuery(FILE_UPLOAD_ERROR).removeClass('d-none');
		this.chat.$el.find('.elex-ws-chat-convo-body').append(toast).removeClass('d-none');

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    render_preview() {
        if (this.files.length === 0) {
        	this.preview_container.addClass('d-none');
        	return;
        }

        this.preview_container.removeClass('d-none');
        this.preview_container.find('.elex-ws-chat-convo-attached-files-container').html('');

        this.files.forEach((file, i) => {
            const file_preview = jQuery(FILE_PREVIEW_TEMPLATE);
            file_preview.find('.preview-content').text(file.name).attr('title', file.name);
            file_preview.data('file-attachment-index', i);
            file_preview.find('.elex-ws-chat-convo-attach-file-remover').click(e => this.remove_file(e));
            this.preview_container.find('.elex-ws-chat-convo-attached-files-container').append(file_preview);
        });
    }

    build_form_data(frmData) {
        this.files.forEach(file => {
            frmData.append('attachments[]', file);
        });

        this.preview_container.addClass('d-none');

        return frmData;
    }

    remove_file(e) {
        let item = jQuery(e.target).parents('.elex-ws-chat-convo-attached-file');

        const index = item.data('file-attachment-index');

        this.files.splice(index, 1);
        this.file_input.val('');
        this.render_preview();
    }

    reset_files() {
        this.files = [];
        this.render_preview();
    }

    render_attachment_preview(attachment) {
        let attachFilename = attachment.name
        if (attachment.type.indexOf('image')==0) {
            return `<div class="d-flex align-items-center justify-content-center elex-ws-chat-convo-img-bg" style='background-image:url("${attachment.url}");'>
                </div>`;
        }

        if (attachment.type.indexOf('video')==0) {
            return `
            <div class="elex-wschat-video-attach-play-btn d-flex justify-content-center align-items-center" style='background-image:url("${attachment.thumbnail}"); background-size: cover;'>
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="26" viewBox="0 0 34 26">
            <g id="Group_327" data-name="Group 327" transform="translate(-1568 -692)">
                <rect id="Rectangle_49" data-name="Rectangle 49" width="34" height="26" rx="6" transform="translate(1568 692)" fill="#fcf8f8"/>
                <path id="Icon_feather-play" data-name="Icon feather-play" d="M3.75,2.25,14.25,9,3.75,15.75Z" transform="translate(1575.75 696.25)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </g>
            </svg>
            </div>`;
        }

        if (attachFilename.indexOf('.pdf') == attachFilename.length-4) {
            return `<div class="d-flex align-items-center flex-column justify-content-center elex-ws-chat-convo-default-bg" p
            <h5 class="text-dark fw-bold">PDF</h5>
            <div class="mt-1"><small>${attachment.name}</small></div>
            </div>`
        }
        return `<div class="d-flex align-items-center flex-column justify-content-center elex-ws-chat-convo-default-bg p-2" >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 60%;max-width: 80px" viewBox="0 0 384 512"><path d="M0 64C0 28.65 28.65 0 64 0H229.5C246.5 0 262.7 6.743 274.7 18.75L365.3 109.3C377.3 121.3 384 137.5 384 154.5V448C384
                 483.3 355.3 512 320 512H64C28.65 512 0 483.3 0 448V64zM336 448V160H256C238.3 160 224 145.7 224 128V48H64C55.16 48 48 55.16 48 64V448C48 456.8 55.16 464 64 464H320C328.8 464 336 456.8 336
                  448z"/></svg>
        <div class="mt-1"><small>${attachment.name}</small></div>
        </div>`
    }

    format_content(message) {
        if (!message.body.attachments || message.body.attachments.length === 0) {
            return message;
        }

        let markup = '<div class="attachment-links rounded d-flex flex-wrap  mb-2 position-relative">';

        message.body.attachments.forEach(attachment => {

            markup += `

            	<a target="_blank" href="${attachment.url}" class="d-inline-block m-2 ratio ratio-16x9 rounded-3 overflow-hidden elex-ws-chat-convo-pdf" poster="${attachment.url}" >
                    ${this.render_attachment_preview(attachment) }
                </a>
            `;
        });

        markup += '</div>';

        message.body.formatted_content = message.body.formatted_content || '';

        message.body.formatted_content = markup + message.body.formatted_content;

        return message;
    }
}

export const BTN_TEMPLATE = `
	<button class="icon-btn elex-ws-chat-attach-file-btn p-1"  data-bs-toggle="tooltip" data-bs-placement="right" title="Attach file" data-bs-custom-class="tooltip-outline-primary">
		<svg class=" wschat-icon-fill" xmlns="http://www.w3.org/2000/svg " width="20 " height="20.5 "
			viewBox="0 0 20 20.5 ">
			<path id="Icon_metro-attachment " data-name="Icon metro-attachment "
				d="M15.915,7.511l-1.45-1.3-7.25,6.5a2.562,2.562,0,0,0,0,3.9,3.332,3.332,0,0,0,4.35,0l8.7-7.8a4.268,4.268,0,0,0,0-6.5,5.551,5.551,0,0,0-7.25,0L3.881,10.5l-.02.017a5.954,5.954,0,0,0,0,9.067,7.745,7.745,0,0,0,10.111,0l.018-.018h0l6.235-5.591-1.451-1.3-6.235,5.591-.019.017a5.53,5.53,0,0,1-7.212,0,4.25,4.25,0,0,1,0-6.466l.02-.017h0l9.136-8.191a3.335,3.335,0,0,1,4.35,0,2.565,2.565,0,0,1,0,3.9l-8.7,7.8a1.112,1.112,0,0,1-1.45,0,.855.855,0,0,1,0-1.3l7.25-6.5Z "
				transform="translate(-1.767 -0.964) " fill="#fff " />
		</svg>
	</button>
	<button id="wschat_file_attachement_picker" class="d-none btn btn-sm attachment-list-item wschat-icon-color" data-bs-toggle="tooltip" data-bs-placement="right" title="Upload Files">
		<i class="material-icons align-bottom">attach_file</i>
	</button>
`;

export const FILE_INPUT_TEMPLATE = `
	<input type="file" multiple name="wschat_file_attachments[]" class="d-none" />
`;

export const FILE_PREVIEW_TEMPLATE = `
			<div class=" elex-ws-chat-convo-attached-file active">
				<div class="text-secondary">
					<svg xmlns="http://www.w3.org/2000/svg" width="10.497" height="11.997"
						viewBox="0 0 10.497 11.997">
						<path id="Icon_metro-attachment_grey" data-name="Icon metro-attachment grey"
							d="M8.8,4.581l-.761-.761-3.805,3.8A1.614,1.614,0,0,0,6.517,9.908l4.566-4.566a2.69,2.69,0,0,0-3.8-3.8L2.484,6.332l-.01.01a3.752,3.752,0,0,0,5.307,5.306l.01-.01h0l3.273-3.272L10.3,7.605,7.03,10.877l-.01.01A2.676,2.676,0,0,1,3.235,7.1l.01-.01h0L8.039,2.3a1.614,1.614,0,0,1,2.283,2.283L5.756,9.147A.538.538,0,0,1,5,8.386L8.8,4.581Z"
							transform="translate(-1.375 -0.75)" fill="#5e5e5e" />
					</svg>
					<span class="preview-content">File.jpg</span>
				</div>
				<button
					class="btn btn-sm py-0 shadow-none rounded-circle elex-ws-chat-convo-attach-file-remover">
					<svg xmlns="http://www.w3.org/2000/svg" width="9.313" height="9.313"
						viewBox="0 0 9.313 9.313">
						<path id="Icon_ionic-md-close" data-name="Icon ionic-md-close"
							d="M12.656,4.275l-.931-.931L8,7.069,4.275,3.344l-.931.931L7.069,8,3.344,11.725l.931.931L8,8.931l3.725,3.725.931-.931L8.931,8Z"
							transform="translate(-3.344 -3.344)" />
					</svg>
				</button>
			</div>
`;

export const PREVIEW_TEMPLATE = `
	<div class="p-2 bg-light elex-ws-chat-convo-attach-file d-none">
		<div class="bg-white rounded-3 p-2 ">
			<div class=" elex-ws-chat-convo-attach-file-loader">
				<div class="text-secondary"><span>Uploading file..</span></div>
				<div class="elex-ws-chat-convo-attach-file-loader-inner">
					0%
				</div>
			</div>

			<div class="elex-ws-chat-convo-attached-files-container">
			</div>

		</div>
	</div>
`;

const FILE_UPLOAD_ERROR = `
						<div class="position-absolute bottom-0 start-50 translate-middle-x p-2 d-none" style="z-index: 119">
							<div id="elex-ws-chat-file-upload-toast-msg"
								class="toast show align-items-center text-white bg-warning w-auto" role="alert"
								aria-live="assertive" aria-atomic="true">
								<div class="toast-body text-center text-wrap">
									Invalid file formats or Reached maximum upload size (${wschat_ajax_obj.max_upload_size} bytes)
								</div>
							</div>
						</div>
`
