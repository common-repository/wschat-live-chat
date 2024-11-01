import {EVENTS} from '../events';
import RecordRTC from 'recordrtc';

/**
 * Video plugin
 */
export class VideoPlugin {
	constructor(chat){
		this.chat = chat;
        this.recorder = null;
        this.on_camera_stop_event = 'on_camera_stop_event';
	}

	init() {
		this.prepare_ui();
		this.chat.on(EVENTS.WSCHAT_BUILD_FORM_DATA, frmData => this.build_form_data(frmData));
		this.chat.on(EVENTS.WSCHAT_ON_SENT_A_MESSAGE, () => this.reset_files());
		// this.chat.on(EVENTS.WSCHAT_RENDER_CHAT_CONTENT, message => this.format_content(message));
		this.chat.on(EVENTS.WSCHAT_CAN_SEND_EMPTY_MESSAGE, (pass) => this.video_data ? true : pass);

        this.chat.$el.on('click', '.elex-ws-chat-start-video-btn', e => this.start(e));
        this.chat.$el.on('click', '.elex-ws-chat-stop-video', e => this.stop(e));
        this.chat.$el.on('click', '.elex-ws-chat-retake-video', e => this.start(e));
        this.chat.$el.on('click', '.elex-ws-chat-video-close-btn', () => this.reset_files() );
        this.chat.$el.on('click', '.wschat_video_player_action_download', e => this.download(e));
	}

	prepare_ui() {
		const picker = jQuery(BTN_TEMPLATE);
		this.file_input = jQuery(VIDEO_CONTAINER);
		picker.on('click', () => this.show_picker());
        this.video_element = this.file_input.find('video');

		this.chat.$el.find('.attachment-wrapper .attachment-list > div').append(picker);
		this.chat.$el.find('.chat-box-footer').prepend(this.file_input);
	}

    build_form_data(frmData) {
        if (this.video_data) {
			frmData.append('attachments[]', this.video_data, 'video.mp4');
			frmData.append('attachments[]', this.thumbnail);
        }

        this.file_input.addClass('d-none');

        return frmData;
    }

    async reset_files() {
        await this.stop();
        this.video = undefined;
        // this.recorder = undefined;
        this.video_data = undefined;
		this.chat.$el.find('.elex-ws-chat-more-input-video').remove();
		this.chat.$el.find('.elex-ws-chat-video-open-btn').remove();
        this.chat.$el.find('.elex-ws-chat-retake-video').addClass('d-none')

		this.prepare_ui();
    }

	show_picker() {
		// TODO: Toggle visiblity
		this.chat.$el.find('.elex-ws-chat-more-input-video').removeClass('d-none');
        this.chat.$el.find('.elex-ws-chat-retake-video').addClass('d-none')
        this.get_stream();
	}

    has_media_device() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            return true;
        }

        return false;
    }

    async get_stream() {
        if (this.has_media_device() === false) {
            this.camera_was_not_available('Camera is not available');
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: true,
                video: true
            });

            this.video_element[0].srcObject = stream;
            this.video_element[0].play();

            this.recorder = RecordRTC(stream, {type: 'video', mimeType: 'video/mp4', disableLogs: true});
            this.chat.on(this.on_camera_stop_event, () => {
            	stream.getTracks().forEach(t => t.stop());
            });
        } catch(f) {
            this.camera_was_not_available(f);
            return;
        }
    }

    camera_was_not_available(msg) {
            this.info(msg);
            this.chat.$el.find('.camera-not-available-info').removeClass('d-none');
    }

    download() {
        if (! this.recorder) {
            return;
        }

        this.recorder.save();
    }

    async start() {
        if (! this.recorder) {
        	this.info('No devices available now..');
            return;
        }

        await this.get_stream();

        this.video_element[0].play();

        this.recorder.startRecording();
        this.info('Recording');

        this.chat.$el.find('.elex-ws-chat-start-video').addClass('d-none')
        this.chat.$el.find('.elex-ws-chat-stop-video').removeClass('d-none')
        this.chat.$el.find('.elex-ws-chat-retake-video').addClass('d-none')

        this.counter = 0;
        this.timer = setInterval(() => {
			this.counter++;
			const minutes = Math.floor(this.counter/60);
			const seconds = this.counter - minutes*60;
			this.chat.$el.find('.timer').text( (minutes +':').padStart(3, 0) + (seconds+'').padStart(2, 0) );
        }, 1000);
    }

    capture_thumbnail() {
        const canvas = document.createElement("canvas");
        canvas.width = this.video_element[0].videoWidth;
        canvas.height = this.video_element[0].videoHeight;
		canvas.getContext('2d').drawImage(this.video_element[0], 0, 0);

        return new Promise(resolve => {
            canvas.toBlob(blob => {
                resolve(blob);
            }, 'image/png');
        });
    }

    async stop() {
        if (! this.recorder) {
            return;
        }
        this.video_element[0].pause();
        this.thumbnail = new File([await this.capture_thumbnail()], 'thumbnail.png', { type: 'image/png' });

        this.recorder.stopRecording(() => {
            this.video_data = this.recorder.getBlob();
        	this.info('Stopped');
        	this.recorder.reset();
        	this.resetTimer();
        	this.chat.trigger(this.on_camera_stop_event);
        });

        if (this.recorder.state === 'inactive' ) {
        	this.chat.trigger(this.on_camera_stop_event);
        }

        this.chat.$el.find('.elex-ws-chat-stop-video').addClass('d-none');
        this.chat.$el.find('.elex-ws-chat-retake-video').removeClass('d-none');
    }

    startTimer() {
        this.counter = 0;
        this.timer = setInterval(() => {
			this.counter++;
			const minutes = Math.floor(this.counter/60);
			const seconds = this.counter - minutes*60;
			this.chat.$el.find('.timer').text( minutes +':' + seconds );
        }, 1000);

    }

    resetTimer() {
        this.counter = 0;
        clearInterval(this.timer);
    }

    info(msg) {
    	this.chat.$el.find('.wschat_video_info').text(msg).attr('title', msg);
    }
}

export const BTN_TEMPLATE = `
	<button class="icon-btn elex-ws-chat-video-open-btn p-1"  data-bs-toggle="tooltip" data-bs-placement="right" title="Video Message" data-bs-custom-class="tooltip-outline-primary">
		<svg class="wschat-icon-color" xmlns="http://www.w3.org/2000/svg " width="24.5 " height="17.75 "
			viewBox="0 0 24.5 17.75 ">
			<g id="Icon_feather-video " data-name="Icon feather-video "
				transform="translate(1 1) ">
				<path id="Path_18 " data-name="Path 18 "
					d="M23.159,7,16,12.625l7.159,5.625Z "
					transform="translate(-0.659 -4.75) " fill="none " stroke="#fff "
					stroke-linecap="round " stroke-linejoin="round "
					stroke-width="2 " />
				<path id="Path_19 " data-name="Path 19 "
					d="M3.045,5H14.3a2.155,2.155,0,0,1,2.045,2.25V18.5A2.155,2.155,0,0,1,14.3,20.75H3.045A2.155,2.155,0,0,1,1,18.5V7.25A2.155,2.155,0,0,1,3.045,5Z "
					transform="translate(-1 -5) " fill="none " stroke="#fff "
					stroke-linecap="round " stroke-linejoin="round "
					stroke-width="2 " />
			</g>
		</svg>
	</button>
`;

export const VIDEO_CONTAINER = `
	<div class="py-1 px-3 bg-light elex-ws-chat-more-input-video position-absolute bottom-100 w-100 start-0 d-none" style="z-index:2">
		<div class="d-flex justify-content-between align-items-center">
			<div class="xs ">Camera Preview</div>
			<button class="btn btn-sm elex-ws-chat-video-close-btn">
				<svg xmlns="http://www.w3.org/2000/svg " width="9.313 " height="9.313 "
					viewBox="0 0 9.313 9.313 ">
					<path id="Icon_ionic-md-close " data-name="Icon ionic-md-close "
						d="M12.656,4.275l-.931-.931L8,7.069,4.275,3.344l-.931.931L7.069,8,3.344,11.725l.931.931L8,8.931l3.725,3.725.931-.931L8.931,8Z "
						transform="translate(-3.344 -3.344) " />
				</svg>
			</button>
		</div>
		<div class="bg-secondary mb-2 position-relative">
			<div class="row justify-content-center">
			<div class=" col-lg-8 col-md-10">
				<div class="ratio ratio-16x9 bg-dark ">
            		<video muted id="wschat_video_attachment_player"></video>
				</div>
			</div>
			</div>
            <div class="camera-not-available-info d-flex flex-column position-absolute top-0 w-100 h-100 align-items-center justify-content-center text-center d-none">
                <p class="text-sm text-muted">Could not start video recording.<br /> Please check your camera settings and try again </p>
                <button class="btn btn-sm wschat-bg-primary elex-ws-chat-video-close-btn">
                    <span class="ms-1 xs wschat-text-primary">Okay</span>
                </button>
            </div>
		</div>

		<!-- start recording -->
		<div class="position-relative text-center elex-ws-chat-video elex-ws-chat-start-video">
			<button class="btn btn-sm wschat-bg-primary elex-ws-chat-start-video-btn">
				<svg xmlns="http://www.w3.org/2000/svg" width="10.167" height="12.5"
					viewBox="0 0 10.167 12.5">
					<path id="Icon_feather-play" data-name="Icon feather-play"
						d="M2.917,1.75,11.083,7,2.917,12.25Z" transform="translate(-1.917 -0.75)"
						fill="none" stroke="var(--wschat-text-primary)" stroke-linecap="round" stroke-linejoin="round"
						stroke-width="2" />
				</svg>
                <span class="ms-1 xs wschat-text-primary">Start Recording</span>
            </button>
            <div class="w-25 text-truncate position-absolute xs text-secondary top-50 start-0 translate-middle-y wschat_video_info"></div>
		</div>

		<!-- stop recording -->
		<div
			class="text-center elex-ws-chat-video position-relative elex-ws-chat-stop-video d-none">
			<button class="btn btn-sm btn-primary elex-ws-chat-stop-video-btn">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13">
					<g id="stop_icon" data-name="stop icon" fill="none" stroke="#fff"
						stroke-width="2">
						<rect width="12" height="13" rx="2" stroke="none" />
						<rect x="1" y="1" width="10" height="11" rx="1" fill="none" />
					</g>
				</svg>
				<span class="ms-1 xs">Stop Recording</span></button>
			</button>
            	<div class="position-absolute xs text-secondary top-50 start-0 translate-middle-y wschat_video_info">Stopped Recording</div>
				<div class="position-absolute xs text-secondary top-50 end-0 translate-middle-y timer">00:00
			</div>
		</div>

		<!-- retake recording -->
		<div
			class="text-center elex-ws-chat-video position-relative elex-ws-chat-retake-video d-none">
			<button
				class="btn btn-sm bg-white border border-primary text-primary elex-ws-chat-retake-video-btn">
				<svg xmlns="http://www.w3.org/2000/svg" width="13.983" height="14"
					viewBox="0 0 13.983 14">
					<path id="Retake_Icon" data-name="Retake Icon"
						d="M7,0a7,7,0,1,0,4.97,11.97l-1.26-1.26A5.254,5.254,0,1,1,6.982,1.75,5.091,5.091,0,0,1,10.64,3.342L8.732,5.25h5.25V0L11.9,2.082A6.961,6.961,0,0,0,6.982,0Z"
						fill="#2489db" />
				</svg>
				<span class="ms-1 xs">Retake Recording</span></button>
			</button>
			<div class="position-absolute xs text-secondary top-50 start-0 translate-middle-y wschat_video_info">
				Stopped Recording</div>
			<div class="position-absolute xs text-secondary top-50 end-0 translate-middle-y timer">00:00
			</div>
		</div>
	</div>
`;

