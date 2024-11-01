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

