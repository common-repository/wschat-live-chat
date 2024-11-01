/**
 * @jest-environment jsdom
 */
import { act } from "react-dom/test-utils";
import { render, unmountComponentAtNode } from "react-dom";

import {AlertControl} from './chat_alert_notifications';

let container = null;
beforeEach(() => {
  // setup a DOM element as a render target
  container = document.createElement("div");
  document.body.appendChild(container);
});

afterEach(() => {
  // cleanup on exiting
  unmountComponentAtNode(container);
  container.remove();
  container = null;
});


describe("Test alert component", () => {

	it("renders alert component", () => {
    	const controller = {
        	toggle : jest.fn()
    	};

    	act(() => {
        	render(<AlertControl controller={controller} />, container);
    	});

    	const button = document.querySelector('#wschat_alert_toggle');

    	expect(button.querySelector('.volume_mute')).toBeFalsy();

    	act(() => {
        	button.dispatchEvent(new MouseEvent("click", { bubbles: true }));
    	});

    	expect(button.querySelector('.volume_mute')).toBeTruthy();

    	expect(controller.toggle).toHaveBeenCalledTimes(1)
	});
});
