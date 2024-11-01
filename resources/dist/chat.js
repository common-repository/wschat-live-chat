/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/chat.js":
/*!******************************!*\
  !*** ./resources/js/chat.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Chat)
/* harmony export */ });
/* harmony import */ var _events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./events */ "./resources/js/events.js");
/* harmony import */ var _user_api_connector__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./user_api_connector */ "./resources/js/user_api_connector.js");
var _this = undefined;

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }




var Chat = /*#__PURE__*/function () {
  function Chat(el, options) {
    _classCallCheck(this, Chat);

    _defineProperty(this, "options", {});

    _defineProperty(this, "defaults", {
      type: 'user',
      preform: false,
      connector: _user_api_connector__WEBPACK_IMPORTED_MODULE_1__.UserApiConnector,
      api: {
        endpoint: '',
        interval: 10000
      }
    });

    _defineProperty(this, "connector", void 0);

    _defineProperty(this, "event_listeners", {});

    this.options = jQuery.extend({}, this.defaults, options);
    this.$el = jQuery(el);
    this.init();
  }

  _createClass(Chat, [{
    key: "init",
    value: function init() {
      this.trigger(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_ON_INIT);
      this.connector = this.options.connector ? new this.options.connector(this, this.options.api) : false;
    }
  }, {
    key: "on",
    value: function on(e, callback) {
      if (this.event_listeners[e] === undefined) {
        this.event_listeners[e] = [];
      }

      this.event_listeners[e].push(callback);
    }
  }, {
    key: "trigger",
    value: function trigger(e, args) {
      if (this.event_listeners[e] === undefined) {
        return;
      }

      this.event_listeners[e].forEach(function (callback) {
        callback(args);
      });
    }
  }, {
    key: "setConversation",
    value: function setConversation(conversation) {
      this.conversation = conversation;
      this.trigger(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_ON_SET_CONVERSATION, conversation);
    }
  }, {
    key: "sendMessage",
    value: function sendMessage(data) {
      this.trigger(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_SEND_MESSAGE, data);
    }
  }]);

  return Chat;
}();



jQuery.fn.WSChat = function (options) {
  return new Chat(_this, options || {});
};

/***/ }),

/***/ "./resources/js/events.js":
/*!********************************!*\
  !*** ./resources/js/events.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "EVENTS": () => (/* binding */ EVENTS)
/* harmony export */ });
var EVENTS = {
  // Before init WSChat
  WSCHAT_ON_INIT: 'wschat_on_init',
  WSCHAT_ON_FETCH_MESSAGES: 'wschat_on_fetch_messages',
  WSCHAT_ON_SEND_MESSAGE: 'wschat_on_send_message',
  WSCHAT_ON_SENT_A_MESSAGE: 'wschat_on_sent_a_message',
  WSCHAT_ON_SET_CONVERSATION: 'wschat_on_set_conversation'
};

/***/ }),

/***/ "./resources/js/user_api_connector.js":
/*!********************************************!*\
  !*** ./resources/js/user_api_connector.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "UserApiConnector": () => (/* binding */ UserApiConnector)
/* harmony export */ });
/* harmony import */ var _events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./events */ "./resources/js/events.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) { symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); } keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }


var UserApiConnector = /*#__PURE__*/function () {
  function UserApiConnector(chat, options) {
    _classCallCheck(this, UserApiConnector);

    _defineProperty(this, "filters", {
      before: false,
      after: false
    });

    _defineProperty(this, "options", {
      endpoint: '',
      interval: 10000
    });

    this.chat = chat;
    this.options = jQuery.extend({}, this.options, options);
    this.start_conversation();
    this.subscribe();
  }

  _createClass(UserApiConnector, [{
    key: "subscribe",
    value: function subscribe() {
      var _this = this;

      this.chat.on(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_ON_SET_CONVERSATION, function () {
        _this.interval && clearInterval(_this.interval);

        _this.get_messages();

        _this.interval = setInterval(function () {
          _this.get_messages();
        }, _this.options.interval);
      });
      this.chat.on(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_SEND_MESSAGE, function (data) {
        _this.send_message(data);
      });
    }
  }, {
    key: "start_conversation",
    value: function start_conversation() {
      var _this2 = this;

      jQuery.post(this.options.endpoint, {
        action: this.ACTION_START_CONVERSATION
      }, function (data) {
        _this2.chat.setConversation(data.data);
      });
    }
  }, {
    key: "get_messages",
    value: function get_messages() {
      var _this3 = this;

      var data = _objectSpread(_objectSpread({}, this.filters), {}, {
        action: this.ACTION_GET_MESSAGE
      });

      data.conversation_id = this.chat.conversation.id, jQuery.post(this.options.endpoint, data, function (res) {
        if (res.data.length === 0) {
          return;
        }

        _this3.filters.after = res.data[0].id;

        _this3.chat.trigger(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_ON_FETCH_MESSAGES, res);
      });
    }
  }, {
    key: "send_message",
    value: function send_message(data) {
      var _this4 = this;

      data.action = this.ACTION_SEND_MESSAGE;
      data.conversation_id = this.chat.conversation.id;
      jQuery.post(this.options.endpoint, data, function (message) {
        _this4.chat.trigger(_events__WEBPACK_IMPORTED_MODULE_0__.EVENTS.WSCHAT_ON_SENT_A_MESSAGE, message);
      });
    }
  }]);

  return UserApiConnector;
}();
UserApiConnector.prototype.ACTION_SEND_MESSAGE = 'wschat_send_message';
UserApiConnector.prototype.ACTION_GET_MESSAGE = 'wschat_get_messages';
UserApiConnector.prototype.ACTION_START_CONVERSATION = 'wschat_start_conversation';

/***/ }),

/***/ "./resources/scss/bootstrap.scss":
/*!***************************************!*\
  !*** ./resources/scss/bootstrap.scss ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					result = fn();
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/resources/dist/chat": 0,
/******/ 			"resources/dist/base": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) var result = runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkwschat"] = self["webpackChunkwschat"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["resources/dist/base"], () => (__webpack_require__("./resources/js/chat.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["resources/dist/base"], () => (__webpack_require__("./resources/scss/bootstrap.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;