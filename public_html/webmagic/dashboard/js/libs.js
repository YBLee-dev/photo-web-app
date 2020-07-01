/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/js/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	__webpack_require__(1);

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	__webpack_require__(2);
	
	__webpack_require__(6);
	
	__webpack_require__(7);
	
	__webpack_require__(8);
	
	__webpack_require__(9);
	
	__webpack_require__(11);
	
	__webpack_require__(12);
	
	__webpack_require__(13);
	
	//JQuery
	__webpack_require__(15);
	
	//JQuery Mask
	// import './maskedinput.min.js';
	
	//magnificPopup
	
	
	//Spinner
	
	
	//Bootstrap
	// import '../../bower_components/adminlte/bootstrap/js/bootstrap.min.js'
	// import '../../node_modules/bootstrap/dist/js/bootstrap'
	
	//Admin Lte
	
	
	/**
	 * Save state for menu
	 */
	$(document).on('collapsed.pushMenu', function () {
	  localStorage.setItem("sidebarCollapse", true);
	});
	$(document).on('expanded.pushMenu', function () {
	  localStorage.setItem("sidebarCollapse", false);
	});
	
	//Data Table
	// import '../../node_modules/datatables/media/js/jquery.dataTables'
	// import '../../node_modules/datatables.net-select/js/dataTables.select'
	// import '../../node_modules/jquery-datatables-checkboxes/js/dataTables.checkboxes.min'
	// import './dataTables.bootstrap.js'

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! Magnific Popup - v1.1.0 - 2016-02-20
	* http://dimsemenov.com/plugins/magnific-popup/
	* Copyright (c) 2016 Dmitry Semenov; */
	;(function (factory) { 
	if (true) { 
	 // AMD. Register as an anonymous module. 
	 !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(3)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)); 
	 } else if (typeof exports === 'object') { 
	 // Node/CommonJS 
	 factory(require('jquery')); 
	 } else { 
	 // Browser globals 
	 factory(window.jQuery || window.Zepto); 
	 } 
	 }(function($) { 
	
	/*>>core*/
	/**
	 * 
	 * Magnific Popup Core JS file
	 * 
	 */
	
	
	/**
	 * Private static constants
	 */
	var CLOSE_EVENT = 'Close',
		BEFORE_CLOSE_EVENT = 'BeforeClose',
		AFTER_CLOSE_EVENT = 'AfterClose',
		BEFORE_APPEND_EVENT = 'BeforeAppend',
		MARKUP_PARSE_EVENT = 'MarkupParse',
		OPEN_EVENT = 'Open',
		CHANGE_EVENT = 'Change',
		NS = 'mfp',
		EVENT_NS = '.' + NS,
		READY_CLASS = 'mfp-ready',
		REMOVING_CLASS = 'mfp-removing',
		PREVENT_CLOSE_CLASS = 'mfp-prevent-close';
	
	
	/**
	 * Private vars 
	 */
	/*jshint -W079 */
	var mfp, // As we have only one instance of MagnificPopup object, we define it locally to not to use 'this'
		MagnificPopup = function(){},
		_isJQ = !!(window.jQuery),
		_prevStatus,
		_window = $(window),
		_document,
		_prevContentType,
		_wrapClasses,
		_currPopupType;
	
	
	/**
	 * Private functions
	 */
	var _mfpOn = function(name, f) {
			mfp.ev.on(NS + name + EVENT_NS, f);
		},
		_getEl = function(className, appendTo, html, raw) {
			var el = document.createElement('div');
			el.className = 'mfp-'+className;
			if(html) {
				el.innerHTML = html;
			}
			if(!raw) {
				el = $(el);
				if(appendTo) {
					el.appendTo(appendTo);
				}
			} else if(appendTo) {
				appendTo.appendChild(el);
			}
			return el;
		},
		_mfpTrigger = function(e, data) {
			mfp.ev.triggerHandler(NS + e, data);
	
			if(mfp.st.callbacks) {
				// converts "mfpEventName" to "eventName" callback and triggers it if it's present
				e = e.charAt(0).toLowerCase() + e.slice(1);
				if(mfp.st.callbacks[e]) {
					mfp.st.callbacks[e].apply(mfp, $.isArray(data) ? data : [data]);
				}
			}
		},
		_getCloseBtn = function(type) {
			if(type !== _currPopupType || !mfp.currTemplate.closeBtn) {
				mfp.currTemplate.closeBtn = $( mfp.st.closeMarkup.replace('%title%', mfp.st.tClose ) );
				_currPopupType = type;
			}
			return mfp.currTemplate.closeBtn;
		},
		// Initialize Magnific Popup only when called at least once
		_checkInstance = function() {
			if(!$.magnificPopup.instance) {
				/*jshint -W020 */
				mfp = new MagnificPopup();
				mfp.init();
				$.magnificPopup.instance = mfp;
			}
		},
		// CSS transition detection, http://stackoverflow.com/questions/7264899/detect-css-transitions-using-javascript-and-without-modernizr
		supportsTransitions = function() {
			var s = document.createElement('p').style, // 's' for style. better to create an element if body yet to exist
				v = ['ms','O','Moz','Webkit']; // 'v' for vendor
	
			if( s['transition'] !== undefined ) {
				return true; 
			}
				
			while( v.length ) {
				if( v.pop() + 'Transition' in s ) {
					return true;
				}
			}
					
			return false;
		};
	
	
	
	/**
	 * Public functions
	 */
	MagnificPopup.prototype = {
	
		constructor: MagnificPopup,
	
		/**
		 * Initializes Magnific Popup plugin. 
		 * This function is triggered only once when $.fn.magnificPopup or $.magnificPopup is executed
		 */
		init: function() {
			var appVersion = navigator.appVersion;
			mfp.isLowIE = mfp.isIE8 = document.all && !document.addEventListener;
			mfp.isAndroid = (/android/gi).test(appVersion);
			mfp.isIOS = (/iphone|ipad|ipod/gi).test(appVersion);
			mfp.supportsTransition = supportsTransitions();
	
			// We disable fixed positioned lightbox on devices that don't handle it nicely.
			// If you know a better way of detecting this - let me know.
			mfp.probablyMobile = (mfp.isAndroid || mfp.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent) );
			_document = $(document);
	
			mfp.popupsCache = {};
		},
	
		/**
		 * Opens popup
		 * @param  data [description]
		 */
		open: function(data) {
	
			var i;
	
			if(data.isObj === false) { 
				// convert jQuery collection to array to avoid conflicts later
				mfp.items = data.items.toArray();
	
				mfp.index = 0;
				var items = data.items,
					item;
				for(i = 0; i < items.length; i++) {
					item = items[i];
					if(item.parsed) {
						item = item.el[0];
					}
					if(item === data.el[0]) {
						mfp.index = i;
						break;
					}
				}
			} else {
				mfp.items = $.isArray(data.items) ? data.items : [data.items];
				mfp.index = data.index || 0;
			}
	
			// if popup is already opened - we just update the content
			if(mfp.isOpen) {
				mfp.updateItemHTML();
				return;
			}
			
			mfp.types = []; 
			_wrapClasses = '';
			if(data.mainEl && data.mainEl.length) {
				mfp.ev = data.mainEl.eq(0);
			} else {
				mfp.ev = _document;
			}
	
			if(data.key) {
				if(!mfp.popupsCache[data.key]) {
					mfp.popupsCache[data.key] = {};
				}
				mfp.currTemplate = mfp.popupsCache[data.key];
			} else {
				mfp.currTemplate = {};
			}
	
	
	
			mfp.st = $.extend(true, {}, $.magnificPopup.defaults, data ); 
			mfp.fixedContentPos = mfp.st.fixedContentPos === 'auto' ? !mfp.probablyMobile : mfp.st.fixedContentPos;
	
			if(mfp.st.modal) {
				mfp.st.closeOnContentClick = false;
				mfp.st.closeOnBgClick = false;
				mfp.st.showCloseBtn = false;
				mfp.st.enableEscapeKey = false;
			}
			
	
			// Building markup
			// main containers are created only once
			if(!mfp.bgOverlay) {
	
				// Dark overlay
				mfp.bgOverlay = _getEl('bg').on('click'+EVENT_NS, function() {
					mfp.close();
				});
	
				mfp.wrap = _getEl('wrap').attr('tabindex', -1).on('click'+EVENT_NS, function(e) {
					if(mfp._checkIfClose(e.target)) {
						mfp.close();
					}
				});
	
				mfp.container = _getEl('container', mfp.wrap);
			}
	
			mfp.contentContainer = _getEl('content');
			if(mfp.st.preloader) {
				mfp.preloader = _getEl('preloader', mfp.container, mfp.st.tLoading);
			}
	
	
			// Initializing modules
			var modules = $.magnificPopup.modules;
			for(i = 0; i < modules.length; i++) {
				var n = modules[i];
				n = n.charAt(0).toUpperCase() + n.slice(1);
				mfp['init'+n].call(mfp);
			}
			_mfpTrigger('BeforeOpen');
	
	
			if(mfp.st.showCloseBtn) {
				// Close button
				if(!mfp.st.closeBtnInside) {
					mfp.wrap.append( _getCloseBtn() );
				} else {
					_mfpOn(MARKUP_PARSE_EVENT, function(e, template, values, item) {
						values.close_replaceWith = _getCloseBtn(item.type);
					});
					_wrapClasses += ' mfp-close-btn-in';
				}
			}
	
			if(mfp.st.alignTop) {
				_wrapClasses += ' mfp-align-top';
			}
	
		
	
			if(mfp.fixedContentPos) {
				mfp.wrap.css({
					overflow: mfp.st.overflowY,
					overflowX: 'hidden',
					overflowY: mfp.st.overflowY
				});
			} else {
				mfp.wrap.css({ 
					top: _window.scrollTop(),
					position: 'absolute'
				});
			}
			if( mfp.st.fixedBgPos === false || (mfp.st.fixedBgPos === 'auto' && !mfp.fixedContentPos) ) {
				mfp.bgOverlay.css({
					height: _document.height(),
					position: 'absolute'
				});
			}
	
			
	
			if(mfp.st.enableEscapeKey) {
				// Close on ESC key
				_document.on('keyup' + EVENT_NS, function(e) {
					if(e.keyCode === 27) {
						mfp.close();
					}
				});
			}
	
			_window.on('resize' + EVENT_NS, function() {
				mfp.updateSize();
			});
	
	
			if(!mfp.st.closeOnContentClick) {
				_wrapClasses += ' mfp-auto-cursor';
			}
			
			if(_wrapClasses)
				mfp.wrap.addClass(_wrapClasses);
	
	
			// this triggers recalculation of layout, so we get it once to not to trigger twice
			var windowHeight = mfp.wH = _window.height();
	
			
			var windowStyles = {};
	
			if( mfp.fixedContentPos ) {
	            if(mfp._hasScrollBar(windowHeight)){
	                var s = mfp._getScrollbarSize();
	                if(s) {
	                    windowStyles.marginRight = s;
	                }
	            }
	        }
	
			if(mfp.fixedContentPos) {
				if(!mfp.isIE7) {
					windowStyles.overflow = 'hidden';
				} else {
					// ie7 double-scroll bug
					$('body, html').css('overflow', 'hidden');
				}
			}
	
			
			
			var classesToadd = mfp.st.mainClass;
			if(mfp.isIE7) {
				classesToadd += ' mfp-ie7';
			}
			if(classesToadd) {
				mfp._addClassToMFP( classesToadd );
			}
	
			// add content
			mfp.updateItemHTML();
	
			_mfpTrigger('BuildControls');
	
			// remove scrollbar, add margin e.t.c
			$('html').css(windowStyles);
			
			// add everything to DOM
			mfp.bgOverlay.add(mfp.wrap).prependTo( mfp.st.prependTo || $(document.body) );
	
			// Save last focused element
			mfp._lastFocusedEl = document.activeElement;
			
			// Wait for next cycle to allow CSS transition
			setTimeout(function() {
				
				if(mfp.content) {
					mfp._addClassToMFP(READY_CLASS);
					mfp._setFocus();
				} else {
					// if content is not defined (not loaded e.t.c) we add class only for BG
					mfp.bgOverlay.addClass(READY_CLASS);
				}
				
				// Trap the focus in popup
				_document.on('focusin' + EVENT_NS, mfp._onFocusIn);
	
			}, 16);
	
			mfp.isOpen = true;
			mfp.updateSize(windowHeight);
			_mfpTrigger(OPEN_EVENT);
	
			return data;
		},
	
		/**
		 * Closes the popup
		 */
		close: function() {
			if(!mfp.isOpen) return;
			_mfpTrigger(BEFORE_CLOSE_EVENT);
	
			mfp.isOpen = false;
			// for CSS3 animation
			if(mfp.st.removalDelay && !mfp.isLowIE && mfp.supportsTransition )  {
				mfp._addClassToMFP(REMOVING_CLASS);
				setTimeout(function() {
					mfp._close();
				}, mfp.st.removalDelay);
			} else {
				mfp._close();
			}
		},
	
		/**
		 * Helper for close() function
		 */
		_close: function() {
			_mfpTrigger(CLOSE_EVENT);
	
			var classesToRemove = REMOVING_CLASS + ' ' + READY_CLASS + ' ';
	
			mfp.bgOverlay.detach();
			mfp.wrap.detach();
			mfp.container.empty();
	
			if(mfp.st.mainClass) {
				classesToRemove += mfp.st.mainClass + ' ';
			}
	
			mfp._removeClassFromMFP(classesToRemove);
	
			if(mfp.fixedContentPos) {
				var windowStyles = {marginRight: ''};
				if(mfp.isIE7) {
					$('body, html').css('overflow', '');
				} else {
					windowStyles.overflow = '';
				}
				$('html').css(windowStyles);
			}
			
			_document.off('keyup' + EVENT_NS + ' focusin' + EVENT_NS);
			mfp.ev.off(EVENT_NS);
	
			// clean up DOM elements that aren't removed
			mfp.wrap.attr('class', 'mfp-wrap').removeAttr('style');
			mfp.bgOverlay.attr('class', 'mfp-bg');
			mfp.container.attr('class', 'mfp-container');
	
			// remove close button from target element
			if(mfp.st.showCloseBtn &&
			(!mfp.st.closeBtnInside || mfp.currTemplate[mfp.currItem.type] === true)) {
				if(mfp.currTemplate.closeBtn)
					mfp.currTemplate.closeBtn.detach();
			}
	
	
			if(mfp.st.autoFocusLast && mfp._lastFocusedEl) {
				$(mfp._lastFocusedEl).focus(); // put tab focus back
			}
			mfp.currItem = null;	
			mfp.content = null;
			mfp.currTemplate = null;
			mfp.prevHeight = 0;
	
			_mfpTrigger(AFTER_CLOSE_EVENT);
		},
		
		updateSize: function(winHeight) {
	
			if(mfp.isIOS) {
				// fixes iOS nav bars https://github.com/dimsemenov/Magnific-Popup/issues/2
				var zoomLevel = document.documentElement.clientWidth / window.innerWidth;
				var height = window.innerHeight * zoomLevel;
				mfp.wrap.css('height', height);
				mfp.wH = height;
			} else {
				mfp.wH = winHeight || _window.height();
			}
			// Fixes #84: popup incorrectly positioned with position:relative on body
			if(!mfp.fixedContentPos) {
				mfp.wrap.css('height', mfp.wH);
			}
	
			_mfpTrigger('Resize');
	
		},
	
		/**
		 * Set content of popup based on current index
		 */
		updateItemHTML: function() {
			var item = mfp.items[mfp.index];
	
			// Detach and perform modifications
			mfp.contentContainer.detach();
	
			if(mfp.content)
				mfp.content.detach();
	
			if(!item.parsed) {
				item = mfp.parseEl( mfp.index );
			}
	
			var type = item.type;
	
			_mfpTrigger('BeforeChange', [mfp.currItem ? mfp.currItem.type : '', type]);
			// BeforeChange event works like so:
			// _mfpOn('BeforeChange', function(e, prevType, newType) { });
	
			mfp.currItem = item;
	
			if(!mfp.currTemplate[type]) {
				var markup = mfp.st[type] ? mfp.st[type].markup : false;
	
				// allows to modify markup
				_mfpTrigger('FirstMarkupParse', markup);
	
				if(markup) {
					mfp.currTemplate[type] = $(markup);
				} else {
					// if there is no markup found we just define that template is parsed
					mfp.currTemplate[type] = true;
				}
			}
	
			if(_prevContentType && _prevContentType !== item.type) {
				mfp.container.removeClass('mfp-'+_prevContentType+'-holder');
			}
	
			var newContent = mfp['get' + type.charAt(0).toUpperCase() + type.slice(1)](item, mfp.currTemplate[type]);
			mfp.appendContent(newContent, type);
	
			item.preloaded = true;
	
			_mfpTrigger(CHANGE_EVENT, item);
			_prevContentType = item.type;
	
			// Append container back after its content changed
			mfp.container.prepend(mfp.contentContainer);
	
			_mfpTrigger('AfterChange');
		},
	
	
		/**
		 * Set HTML content of popup
		 */
		appendContent: function(newContent, type) {
			mfp.content = newContent;
	
			if(newContent) {
				if(mfp.st.showCloseBtn && mfp.st.closeBtnInside &&
					mfp.currTemplate[type] === true) {
					// if there is no markup, we just append close button element inside
					if(!mfp.content.find('.mfp-close').length) {
						mfp.content.append(_getCloseBtn());
					}
				} else {
					mfp.content = newContent;
				}
			} else {
				mfp.content = '';
			}
	
			_mfpTrigger(BEFORE_APPEND_EVENT);
			mfp.container.addClass('mfp-'+type+'-holder');
	
			mfp.contentContainer.append(mfp.content);
		},
	
	
		/**
		 * Creates Magnific Popup data object based on given data
		 * @param  {int} index Index of item to parse
		 */
		parseEl: function(index) {
			var item = mfp.items[index],
				type;
	
			if(item.tagName) {
				item = { el: $(item) };
			} else {
				type = item.type;
				item = { data: item, src: item.src };
			}
	
			if(item.el) {
				var types = mfp.types;
	
				// check for 'mfp-TYPE' class
				for(var i = 0; i < types.length; i++) {
					if( item.el.hasClass('mfp-'+types[i]) ) {
						type = types[i];
						break;
					}
				}
	
				item.src = item.el.attr('data-mfp-src');
				if(!item.src) {
					item.src = item.el.attr('href');
				}
			}
	
			item.type = type || mfp.st.type || 'inline';
			item.index = index;
			item.parsed = true;
			mfp.items[index] = item;
			_mfpTrigger('ElementParse', item);
	
			return mfp.items[index];
		},
	
	
		/**
		 * Initializes single popup or a group of popups
		 */
		addGroup: function(el, options) {
			var eHandler = function(e) {
				e.mfpEl = this;
				mfp._openClick(e, el, options);
			};
	
			if(!options) {
				options = {};
			}
	
			var eName = 'click.magnificPopup';
			options.mainEl = el;
	
			if(options.items) {
				options.isObj = true;
				el.off(eName).on(eName, eHandler);
			} else {
				options.isObj = false;
				if(options.delegate) {
					el.off(eName).on(eName, options.delegate , eHandler);
				} else {
					options.items = el;
					el.off(eName).on(eName, eHandler);
				}
			}
		},
		_openClick: function(e, el, options) {
			var midClick = options.midClick !== undefined ? options.midClick : $.magnificPopup.defaults.midClick;
	
	
			if(!midClick && ( e.which === 2 || e.ctrlKey || e.metaKey || e.altKey || e.shiftKey ) ) {
				return;
			}
	
			var disableOn = options.disableOn !== undefined ? options.disableOn : $.magnificPopup.defaults.disableOn;
	
			if(disableOn) {
				if($.isFunction(disableOn)) {
					if( !disableOn.call(mfp) ) {
						return true;
					}
				} else { // else it's number
					if( _window.width() < disableOn ) {
						return true;
					}
				}
			}
	
			if(e.type) {
				e.preventDefault();
	
				// This will prevent popup from closing if element is inside and popup is already opened
				if(mfp.isOpen) {
					e.stopPropagation();
				}
			}
	
			options.el = $(e.mfpEl);
			if(options.delegate) {
				options.items = el.find(options.delegate);
			}
			mfp.open(options);
		},
	
	
		/**
		 * Updates text on preloader
		 */
		updateStatus: function(status, text) {
	
			if(mfp.preloader) {
				if(_prevStatus !== status) {
					mfp.container.removeClass('mfp-s-'+_prevStatus);
				}
	
				if(!text && status === 'loading') {
					text = mfp.st.tLoading;
				}
	
				var data = {
					status: status,
					text: text
				};
				// allows to modify status
				_mfpTrigger('UpdateStatus', data);
	
				status = data.status;
				text = data.text;
	
				mfp.preloader.html(text);
	
				mfp.preloader.find('a').on('click', function(e) {
					e.stopImmediatePropagation();
				});
	
				mfp.container.addClass('mfp-s-'+status);
				_prevStatus = status;
			}
		},
	
	
		/*
			"Private" helpers that aren't private at all
		 */
		// Check to close popup or not
		// "target" is an element that was clicked
		_checkIfClose: function(target) {
	
			if($(target).hasClass(PREVENT_CLOSE_CLASS)) {
				return;
			}
	
			var closeOnContent = mfp.st.closeOnContentClick;
			var closeOnBg = mfp.st.closeOnBgClick;
	
			if(closeOnContent && closeOnBg) {
				return true;
			} else {
	
				// We close the popup if click is on close button or on preloader. Or if there is no content.
				if(!mfp.content || $(target).hasClass('mfp-close') || (mfp.preloader && target === mfp.preloader[0]) ) {
					return true;
				}
	
				// if click is outside the content
				if(  (target !== mfp.content[0] && !$.contains(mfp.content[0], target))  ) {
					if(closeOnBg) {
						// last check, if the clicked element is in DOM, (in case it's removed onclick)
						if( $.contains(document, target) ) {
							return true;
						}
					}
				} else if(closeOnContent) {
					return true;
				}
	
			}
			return false;
		},
		_addClassToMFP: function(cName) {
			mfp.bgOverlay.addClass(cName);
			mfp.wrap.addClass(cName);
		},
		_removeClassFromMFP: function(cName) {
			this.bgOverlay.removeClass(cName);
			mfp.wrap.removeClass(cName);
		},
		_hasScrollBar: function(winHeight) {
			return (  (mfp.isIE7 ? _document.height() : document.body.scrollHeight) > (winHeight || _window.height()) );
		},
		_setFocus: function() {
			(mfp.st.focus ? mfp.content.find(mfp.st.focus).eq(0) : mfp.wrap).focus();
		},
		_onFocusIn: function(e) {
			if( e.target !== mfp.wrap[0] && !$.contains(mfp.wrap[0], e.target) ) {
				mfp._setFocus();
				return false;
			}
		},
		_parseMarkup: function(template, values, item) {
			var arr;
			if(item.data) {
				values = $.extend(item.data, values);
			}
			_mfpTrigger(MARKUP_PARSE_EVENT, [template, values, item] );
	
			$.each(values, function(key, value) {
				if(value === undefined || value === false) {
					return true;
				}
				arr = key.split('_');
				if(arr.length > 1) {
					var el = template.find(EVENT_NS + '-'+arr[0]);
	
					if(el.length > 0) {
						var attr = arr[1];
						if(attr === 'replaceWith') {
							if(el[0] !== value[0]) {
								el.replaceWith(value);
							}
						} else if(attr === 'img') {
							if(el.is('img')) {
								el.attr('src', value);
							} else {
								el.replaceWith( $('<img>').attr('src', value).attr('class', el.attr('class')) );
							}
						} else {
							el.attr(arr[1], value);
						}
					}
	
				} else {
					template.find(EVENT_NS + '-'+key).html(value);
				}
			});
		},
	
		_getScrollbarSize: function() {
			// thx David
			if(mfp.scrollbarSize === undefined) {
				var scrollDiv = document.createElement("div");
				scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
				document.body.appendChild(scrollDiv);
				mfp.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
				document.body.removeChild(scrollDiv);
			}
			return mfp.scrollbarSize;
		}
	
	}; /* MagnificPopup core prototype end */
	
	
	
	
	/**
	 * Public static functions
	 */
	$.magnificPopup = {
		instance: null,
		proto: MagnificPopup.prototype,
		modules: [],
	
		open: function(options, index) {
			_checkInstance();
	
			if(!options) {
				options = {};
			} else {
				options = $.extend(true, {}, options);
			}
	
			options.isObj = true;
			options.index = index || 0;
			return this.instance.open(options);
		},
	
		close: function() {
			return $.magnificPopup.instance && $.magnificPopup.instance.close();
		},
	
		registerModule: function(name, module) {
			if(module.options) {
				$.magnificPopup.defaults[name] = module.options;
			}
			$.extend(this.proto, module.proto);
			this.modules.push(name);
		},
	
		defaults: {
	
			// Info about options is in docs:
			// http://dimsemenov.com/plugins/magnific-popup/documentation.html#options
	
			disableOn: 0,
	
			key: null,
	
			midClick: false,
	
			mainClass: '',
	
			preloader: true,
	
			focus: '', // CSS selector of input to focus after popup is opened
	
			closeOnContentClick: false,
	
			closeOnBgClick: true,
	
			closeBtnInside: true,
	
			showCloseBtn: true,
	
			enableEscapeKey: true,
	
			modal: false,
	
			alignTop: false,
	
			removalDelay: 0,
	
			prependTo: null,
	
			fixedContentPos: 'auto',
	
			fixedBgPos: 'auto',
	
			overflowY: 'auto',
	
			closeMarkup: '<button title="%title%" type="button" class="mfp-close">&#215;</button>',
	
			tClose: 'Close (Esc)',
	
			tLoading: 'Loading...',
	
			autoFocusLast: true
	
		}
	};
	
	
	
	$.fn.magnificPopup = function(options) {
		_checkInstance();
	
		var jqEl = $(this);
	
		// We call some API method of first param is a string
		if (typeof options === "string" ) {
	
			if(options === 'open') {
				var items,
					itemOpts = _isJQ ? jqEl.data('magnificPopup') : jqEl[0].magnificPopup,
					index = parseInt(arguments[1], 10) || 0;
	
				if(itemOpts.items) {
					items = itemOpts.items[index];
				} else {
					items = jqEl;
					if(itemOpts.delegate) {
						items = items.find(itemOpts.delegate);
					}
					items = items.eq( index );
				}
				mfp._openClick({mfpEl:items}, jqEl, itemOpts);
			} else {
				if(mfp.isOpen)
					mfp[options].apply(mfp, Array.prototype.slice.call(arguments, 1));
			}
	
		} else {
			// clone options obj
			options = $.extend(true, {}, options);
	
			/*
			 * As Zepto doesn't support .data() method for objects
			 * and it works only in normal browsers
			 * we assign "options" object directly to the DOM element. FTW!
			 */
			if(_isJQ) {
				jqEl.data('magnificPopup', options);
			} else {
				jqEl[0].magnificPopup = options;
			}
	
			mfp.addGroup(jqEl, options);
	
		}
		return jqEl;
	};
	
	/*>>core*/
	
	/*>>inline*/
	
	var INLINE_NS = 'inline',
		_hiddenClass,
		_inlinePlaceholder,
		_lastInlineElement,
		_putInlineElementsBack = function() {
			if(_lastInlineElement) {
				_inlinePlaceholder.after( _lastInlineElement.addClass(_hiddenClass) ).detach();
				_lastInlineElement = null;
			}
		};
	
	$.magnificPopup.registerModule(INLINE_NS, {
		options: {
			hiddenClass: 'hide', // will be appended with `mfp-` prefix
			markup: '',
			tNotFound: 'Content not found'
		},
		proto: {
	
			initInline: function() {
				mfp.types.push(INLINE_NS);
	
				_mfpOn(CLOSE_EVENT+'.'+INLINE_NS, function() {
					_putInlineElementsBack();
				});
			},
	
			getInline: function(item, template) {
	
				_putInlineElementsBack();
	
				if(item.src) {
					var inlineSt = mfp.st.inline,
						el = $(item.src);
	
					if(el.length) {
	
						// If target element has parent - we replace it with placeholder and put it back after popup is closed
						var parent = el[0].parentNode;
						if(parent && parent.tagName) {
							if(!_inlinePlaceholder) {
								_hiddenClass = inlineSt.hiddenClass;
								_inlinePlaceholder = _getEl(_hiddenClass);
								_hiddenClass = 'mfp-'+_hiddenClass;
							}
							// replace target inline element with placeholder
							_lastInlineElement = el.after(_inlinePlaceholder).detach().removeClass(_hiddenClass);
						}
	
						mfp.updateStatus('ready');
					} else {
						mfp.updateStatus('error', inlineSt.tNotFound);
						el = $('<div>');
					}
	
					item.inlineElement = el;
					return el;
				}
	
				mfp.updateStatus('ready');
				mfp._parseMarkup(template, {}, item);
				return template;
			}
		}
	});
	
	/*>>inline*/
	
	/*>>ajax*/
	var AJAX_NS = 'ajax',
		_ajaxCur,
		_removeAjaxCursor = function() {
			if(_ajaxCur) {
				$(document.body).removeClass(_ajaxCur);
			}
		},
		_destroyAjaxRequest = function() {
			_removeAjaxCursor();
			if(mfp.req) {
				mfp.req.abort();
			}
		};
	
	$.magnificPopup.registerModule(AJAX_NS, {
	
		options: {
			settings: null,
			cursor: 'mfp-ajax-cur',
			tError: '<a href="%url%">The content</a> could not be loaded.'
		},
	
		proto: {
			initAjax: function() {
				mfp.types.push(AJAX_NS);
				_ajaxCur = mfp.st.ajax.cursor;
	
				_mfpOn(CLOSE_EVENT+'.'+AJAX_NS, _destroyAjaxRequest);
				_mfpOn('BeforeChange.' + AJAX_NS, _destroyAjaxRequest);
			},
			getAjax: function(item) {
	
				if(_ajaxCur) {
					$(document.body).addClass(_ajaxCur);
				}
	
				mfp.updateStatus('loading');
	
				var opts = $.extend({
					url: item.src,
					success: function(data, textStatus, jqXHR) {
						var temp = {
							data:data,
							xhr:jqXHR
						};
	
						_mfpTrigger('ParseAjax', temp);
	
						mfp.appendContent( $(temp.data), AJAX_NS );
	
						item.finished = true;
	
						_removeAjaxCursor();
	
						mfp._setFocus();
	
						setTimeout(function() {
							mfp.wrap.addClass(READY_CLASS);
						}, 16);
	
						mfp.updateStatus('ready');
	
						_mfpTrigger('AjaxContentAdded');
					},
					error: function() {
						_removeAjaxCursor();
						item.finished = item.loadError = true;
						mfp.updateStatus('error', mfp.st.ajax.tError.replace('%url%', item.src));
					}
				}, mfp.st.ajax.settings);
	
				mfp.req = $.ajax(opts);
	
				return '';
			}
		}
	});
	
	/*>>ajax*/
	
	/*>>image*/
	var _imgInterval,
		_getTitle = function(item) {
			if(item.data && item.data.title !== undefined)
				return item.data.title;
	
			var src = mfp.st.image.titleSrc;
	
			if(src) {
				if($.isFunction(src)) {
					return src.call(mfp, item);
				} else if(item.el) {
					return item.el.attr(src) || '';
				}
			}
			return '';
		};
	
	$.magnificPopup.registerModule('image', {
	
		options: {
			markup: '<div class="mfp-figure">'+
						'<div class="mfp-close"></div>'+
						'<figure>'+
							'<div class="mfp-img"></div>'+
							'<figcaption>'+
								'<div class="mfp-bottom-bar">'+
									'<div class="mfp-title"></div>'+
									'<div class="mfp-counter"></div>'+
								'</div>'+
							'</figcaption>'+
						'</figure>'+
					'</div>',
			cursor: 'mfp-zoom-out-cur',
			titleSrc: 'title',
			verticalFit: true,
			tError: '<a href="%url%">The image</a> could not be loaded.'
		},
	
		proto: {
			initImage: function() {
				var imgSt = mfp.st.image,
					ns = '.image';
	
				mfp.types.push('image');
	
				_mfpOn(OPEN_EVENT+ns, function() {
					if(mfp.currItem.type === 'image' && imgSt.cursor) {
						$(document.body).addClass(imgSt.cursor);
					}
				});
	
				_mfpOn(CLOSE_EVENT+ns, function() {
					if(imgSt.cursor) {
						$(document.body).removeClass(imgSt.cursor);
					}
					_window.off('resize' + EVENT_NS);
				});
	
				_mfpOn('Resize'+ns, mfp.resizeImage);
				if(mfp.isLowIE) {
					_mfpOn('AfterChange', mfp.resizeImage);
				}
			},
			resizeImage: function() {
				var item = mfp.currItem;
				if(!item || !item.img) return;
	
				if(mfp.st.image.verticalFit) {
					var decr = 0;
					// fix box-sizing in ie7/8
					if(mfp.isLowIE) {
						decr = parseInt(item.img.css('padding-top'), 10) + parseInt(item.img.css('padding-bottom'),10);
					}
					item.img.css('max-height', mfp.wH-decr);
				}
			},
			_onImageHasSize: function(item) {
				if(item.img) {
	
					item.hasSize = true;
	
					if(_imgInterval) {
						clearInterval(_imgInterval);
					}
	
					item.isCheckingImgSize = false;
	
					_mfpTrigger('ImageHasSize', item);
	
					if(item.imgHidden) {
						if(mfp.content)
							mfp.content.removeClass('mfp-loading');
	
						item.imgHidden = false;
					}
	
				}
			},
	
			/**
			 * Function that loops until the image has size to display elements that rely on it asap
			 */
			findImageSize: function(item) {
	
				var counter = 0,
					img = item.img[0],
					mfpSetInterval = function(delay) {
	
						if(_imgInterval) {
							clearInterval(_imgInterval);
						}
						// decelerating interval that checks for size of an image
						_imgInterval = setInterval(function() {
							if(img.naturalWidth > 0) {
								mfp._onImageHasSize(item);
								return;
							}
	
							if(counter > 200) {
								clearInterval(_imgInterval);
							}
	
							counter++;
							if(counter === 3) {
								mfpSetInterval(10);
							} else if(counter === 40) {
								mfpSetInterval(50);
							} else if(counter === 100) {
								mfpSetInterval(500);
							}
						}, delay);
					};
	
				mfpSetInterval(1);
			},
	
			getImage: function(item, template) {
	
				var guard = 0,
	
					// image load complete handler
					onLoadComplete = function() {
						if(item) {
							if (item.img[0].complete) {
								item.img.off('.mfploader');
	
								if(item === mfp.currItem){
									mfp._onImageHasSize(item);
	
									mfp.updateStatus('ready');
								}
	
								item.hasSize = true;
								item.loaded = true;
	
								_mfpTrigger('ImageLoadComplete');
	
							}
							else {
								// if image complete check fails 200 times (20 sec), we assume that there was an error.
								guard++;
								if(guard < 200) {
									setTimeout(onLoadComplete,100);
								} else {
									onLoadError();
								}
							}
						}
					},
	
					// image error handler
					onLoadError = function() {
						if(item) {
							item.img.off('.mfploader');
							if(item === mfp.currItem){
								mfp._onImageHasSize(item);
								mfp.updateStatus('error', imgSt.tError.replace('%url%', item.src) );
							}
	
							item.hasSize = true;
							item.loaded = true;
							item.loadError = true;
						}
					},
					imgSt = mfp.st.image;
	
	
				var el = template.find('.mfp-img');
				if(el.length) {
					var img = document.createElement('img');
					img.className = 'mfp-img';
					if(item.el && item.el.find('img').length) {
						img.alt = item.el.find('img').attr('alt');
					}
					item.img = $(img).on('load.mfploader', onLoadComplete).on('error.mfploader', onLoadError);
					img.src = item.src;
	
					// without clone() "error" event is not firing when IMG is replaced by new IMG
					// TODO: find a way to avoid such cloning
					if(el.is('img')) {
						item.img = item.img.clone();
					}
	
					img = item.img[0];
					if(img.naturalWidth > 0) {
						item.hasSize = true;
					} else if(!img.width) {
						item.hasSize = false;
					}
				}
	
				mfp._parseMarkup(template, {
					title: _getTitle(item),
					img_replaceWith: item.img
				}, item);
	
				mfp.resizeImage();
	
				if(item.hasSize) {
					if(_imgInterval) clearInterval(_imgInterval);
	
					if(item.loadError) {
						template.addClass('mfp-loading');
						mfp.updateStatus('error', imgSt.tError.replace('%url%', item.src) );
					} else {
						template.removeClass('mfp-loading');
						mfp.updateStatus('ready');
					}
					return template;
				}
	
				mfp.updateStatus('loading');
				item.loading = true;
	
				if(!item.hasSize) {
					item.imgHidden = true;
					template.addClass('mfp-loading');
					mfp.findImageSize(item);
				}
	
				return template;
			}
		}
	});
	
	/*>>image*/
	
	/*>>zoom*/
	var hasMozTransform,
		getHasMozTransform = function() {
			if(hasMozTransform === undefined) {
				hasMozTransform = document.createElement('p').style.MozTransform !== undefined;
			}
			return hasMozTransform;
		};
	
	$.magnificPopup.registerModule('zoom', {
	
		options: {
			enabled: false,
			easing: 'ease-in-out',
			duration: 300,
			opener: function(element) {
				return element.is('img') ? element : element.find('img');
			}
		},
	
		proto: {
	
			initZoom: function() {
				var zoomSt = mfp.st.zoom,
					ns = '.zoom',
					image;
	
				if(!zoomSt.enabled || !mfp.supportsTransition) {
					return;
				}
	
				var duration = zoomSt.duration,
					getElToAnimate = function(image) {
						var newImg = image.clone().removeAttr('style').removeAttr('class').addClass('mfp-animated-image'),
							transition = 'all '+(zoomSt.duration/1000)+'s ' + zoomSt.easing,
							cssObj = {
								position: 'fixed',
								zIndex: 9999,
								left: 0,
								top: 0,
								'-webkit-backface-visibility': 'hidden'
							},
							t = 'transition';
	
						cssObj['-webkit-'+t] = cssObj['-moz-'+t] = cssObj['-o-'+t] = cssObj[t] = transition;
	
						newImg.css(cssObj);
						return newImg;
					},
					showMainContent = function() {
						mfp.content.css('visibility', 'visible');
					},
					openTimeout,
					animatedImg;
	
				_mfpOn('BuildControls'+ns, function() {
					if(mfp._allowZoom()) {
	
						clearTimeout(openTimeout);
						mfp.content.css('visibility', 'hidden');
	
						// Basically, all code below does is clones existing image, puts in on top of the current one and animated it
	
						image = mfp._getItemToZoom();
	
						if(!image) {
							showMainContent();
							return;
						}
	
						animatedImg = getElToAnimate(image);
	
						animatedImg.css( mfp._getOffset() );
	
						mfp.wrap.append(animatedImg);
	
						openTimeout = setTimeout(function() {
							animatedImg.css( mfp._getOffset( true ) );
							openTimeout = setTimeout(function() {
	
								showMainContent();
	
								setTimeout(function() {
									animatedImg.remove();
									image = animatedImg = null;
									_mfpTrigger('ZoomAnimationEnded');
								}, 16); // avoid blink when switching images
	
							}, duration); // this timeout equals animation duration
	
						}, 16); // by adding this timeout we avoid short glitch at the beginning of animation
	
	
						// Lots of timeouts...
					}
				});
				_mfpOn(BEFORE_CLOSE_EVENT+ns, function() {
					if(mfp._allowZoom()) {
	
						clearTimeout(openTimeout);
	
						mfp.st.removalDelay = duration;
	
						if(!image) {
							image = mfp._getItemToZoom();
							if(!image) {
								return;
							}
							animatedImg = getElToAnimate(image);
						}
	
						animatedImg.css( mfp._getOffset(true) );
						mfp.wrap.append(animatedImg);
						mfp.content.css('visibility', 'hidden');
	
						setTimeout(function() {
							animatedImg.css( mfp._getOffset() );
						}, 16);
					}
	
				});
	
				_mfpOn(CLOSE_EVENT+ns, function() {
					if(mfp._allowZoom()) {
						showMainContent();
						if(animatedImg) {
							animatedImg.remove();
						}
						image = null;
					}
				});
			},
	
			_allowZoom: function() {
				return mfp.currItem.type === 'image';
			},
	
			_getItemToZoom: function() {
				if(mfp.currItem.hasSize) {
					return mfp.currItem.img;
				} else {
					return false;
				}
			},
	
			// Get element postion relative to viewport
			_getOffset: function(isLarge) {
				var el;
				if(isLarge) {
					el = mfp.currItem.img;
				} else {
					el = mfp.st.zoom.opener(mfp.currItem.el || mfp.currItem);
				}
	
				var offset = el.offset();
				var paddingTop = parseInt(el.css('padding-top'),10);
				var paddingBottom = parseInt(el.css('padding-bottom'),10);
				offset.top -= ( $(window).scrollTop() - paddingTop );
	
	
				/*
	
				Animating left + top + width/height looks glitchy in Firefox, but perfect in Chrome. And vice-versa.
	
				 */
				var obj = {
					width: el.width(),
					// fix Zepto height+padding issue
					height: (_isJQ ? el.innerHeight() : el[0].offsetHeight) - paddingBottom - paddingTop
				};
	
				// I hate to do this, but there is no another option
				if( getHasMozTransform() ) {
					obj['-moz-transform'] = obj['transform'] = 'translate(' + offset.left + 'px,' + offset.top + 'px)';
				} else {
					obj.left = offset.left;
					obj.top = offset.top;
				}
				return obj;
			}
	
		}
	});
	
	
	
	/*>>zoom*/
	
	/*>>iframe*/
	
	var IFRAME_NS = 'iframe',
		_emptyPage = '//about:blank',
	
		_fixIframeBugs = function(isShowing) {
			if(mfp.currTemplate[IFRAME_NS]) {
				var el = mfp.currTemplate[IFRAME_NS].find('iframe');
				if(el.length) {
					// reset src after the popup is closed to avoid "video keeps playing after popup is closed" bug
					if(!isShowing) {
						el[0].src = _emptyPage;
					}
	
					// IE8 black screen bug fix
					if(mfp.isIE8) {
						el.css('display', isShowing ? 'block' : 'none');
					}
				}
			}
		};
	
	$.magnificPopup.registerModule(IFRAME_NS, {
	
		options: {
			markup: '<div class="mfp-iframe-scaler">'+
						'<div class="mfp-close"></div>'+
						'<iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe>'+
					'</div>',
	
			srcAction: 'iframe_src',
	
			// we don't care and support only one default type of URL by default
			patterns: {
				youtube: {
					index: 'youtube.com',
					id: 'v=',
					src: '//www.youtube.com/embed/%id%?autoplay=1'
				},
				vimeo: {
					index: 'vimeo.com/',
					id: '/',
					src: '//player.vimeo.com/video/%id%?autoplay=1'
				},
				gmaps: {
					index: '//maps.google.',
					src: '%id%&output=embed'
				}
			}
		},
	
		proto: {
			initIframe: function() {
				mfp.types.push(IFRAME_NS);
	
				_mfpOn('BeforeChange', function(e, prevType, newType) {
					if(prevType !== newType) {
						if(prevType === IFRAME_NS) {
							_fixIframeBugs(); // iframe if removed
						} else if(newType === IFRAME_NS) {
							_fixIframeBugs(true); // iframe is showing
						}
					}// else {
						// iframe source is switched, don't do anything
					//}
				});
	
				_mfpOn(CLOSE_EVENT + '.' + IFRAME_NS, function() {
					_fixIframeBugs();
				});
			},
	
			getIframe: function(item, template) {
				var embedSrc = item.src;
				var iframeSt = mfp.st.iframe;
	
				$.each(iframeSt.patterns, function() {
					if(embedSrc.indexOf( this.index ) > -1) {
						if(this.id) {
							if(typeof this.id === 'string') {
								embedSrc = embedSrc.substr(embedSrc.lastIndexOf(this.id)+this.id.length, embedSrc.length);
							} else {
								embedSrc = this.id.call( this, embedSrc );
							}
						}
						embedSrc = this.src.replace('%id%', embedSrc );
						return false; // break;
					}
				});
	
				var dataObj = {};
				if(iframeSt.srcAction) {
					dataObj[iframeSt.srcAction] = embedSrc;
				}
				mfp._parseMarkup(template, dataObj, item);
	
				mfp.updateStatus('ready');
	
				return template;
			}
		}
	});
	
	
	
	/*>>iframe*/
	
	/*>>gallery*/
	/**
	 * Get looped index depending on number of slides
	 */
	var _getLoopedId = function(index) {
			var numSlides = mfp.items.length;
			if(index > numSlides - 1) {
				return index - numSlides;
			} else  if(index < 0) {
				return numSlides + index;
			}
			return index;
		},
		_replaceCurrTotal = function(text, curr, total) {
			return text.replace(/%curr%/gi, curr + 1).replace(/%total%/gi, total);
		};
	
	$.magnificPopup.registerModule('gallery', {
	
		options: {
			enabled: false,
			arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
			preload: [0,2],
			navigateByImgClick: true,
			arrows: true,
	
			tPrev: 'Previous (Left arrow key)',
			tNext: 'Next (Right arrow key)',
			tCounter: '%curr% of %total%'
		},
	
		proto: {
			initGallery: function() {
	
				var gSt = mfp.st.gallery,
					ns = '.mfp-gallery';
	
				mfp.direction = true; // true - next, false - prev
	
				if(!gSt || !gSt.enabled ) return false;
	
				_wrapClasses += ' mfp-gallery';
	
				_mfpOn(OPEN_EVENT+ns, function() {
	
					if(gSt.navigateByImgClick) {
						mfp.wrap.on('click'+ns, '.mfp-img', function() {
							if(mfp.items.length > 1) {
								mfp.next();
								return false;
							}
						});
					}
	
					_document.on('keydown'+ns, function(e) {
						if (e.keyCode === 37) {
							mfp.prev();
						} else if (e.keyCode === 39) {
							mfp.next();
						}
					});
				});
	
				_mfpOn('UpdateStatus'+ns, function(e, data) {
					if(data.text) {
						data.text = _replaceCurrTotal(data.text, mfp.currItem.index, mfp.items.length);
					}
				});
	
				_mfpOn(MARKUP_PARSE_EVENT+ns, function(e, element, values, item) {
					var l = mfp.items.length;
					values.counter = l > 1 ? _replaceCurrTotal(gSt.tCounter, item.index, l) : '';
				});
	
				_mfpOn('BuildControls' + ns, function() {
					if(mfp.items.length > 1 && gSt.arrows && !mfp.arrowLeft) {
						var markup = gSt.arrowMarkup,
							arrowLeft = mfp.arrowLeft = $( markup.replace(/%title%/gi, gSt.tPrev).replace(/%dir%/gi, 'left') ).addClass(PREVENT_CLOSE_CLASS),
							arrowRight = mfp.arrowRight = $( markup.replace(/%title%/gi, gSt.tNext).replace(/%dir%/gi, 'right') ).addClass(PREVENT_CLOSE_CLASS);
	
						arrowLeft.click(function() {
							mfp.prev();
						});
						arrowRight.click(function() {
							mfp.next();
						});
	
						mfp.container.append(arrowLeft.add(arrowRight));
					}
				});
	
				_mfpOn(CHANGE_EVENT+ns, function() {
					if(mfp._preloadTimeout) clearTimeout(mfp._preloadTimeout);
	
					mfp._preloadTimeout = setTimeout(function() {
						mfp.preloadNearbyImages();
						mfp._preloadTimeout = null;
					}, 16);
				});
	
	
				_mfpOn(CLOSE_EVENT+ns, function() {
					_document.off(ns);
					mfp.wrap.off('click'+ns);
					mfp.arrowRight = mfp.arrowLeft = null;
				});
	
			},
			next: function() {
				mfp.direction = true;
				mfp.index = _getLoopedId(mfp.index + 1);
				mfp.updateItemHTML();
			},
			prev: function() {
				mfp.direction = false;
				mfp.index = _getLoopedId(mfp.index - 1);
				mfp.updateItemHTML();
			},
			goTo: function(newIndex) {
				mfp.direction = (newIndex >= mfp.index);
				mfp.index = newIndex;
				mfp.updateItemHTML();
			},
			preloadNearbyImages: function() {
				var p = mfp.st.gallery.preload,
					preloadBefore = Math.min(p[0], mfp.items.length),
					preloadAfter = Math.min(p[1], mfp.items.length),
					i;
	
				for(i = 1; i <= (mfp.direction ? preloadAfter : preloadBefore); i++) {
					mfp._preloadItem(mfp.index+i);
				}
				for(i = 1; i <= (mfp.direction ? preloadBefore : preloadAfter); i++) {
					mfp._preloadItem(mfp.index-i);
				}
			},
			_preloadItem: function(index) {
				index = _getLoopedId(index);
	
				if(mfp.items[index].preloaded) {
					return;
				}
	
				var item = mfp.items[index];
				if(!item.parsed) {
					item = mfp.parseEl( index );
				}
	
				_mfpTrigger('LazyLoad', item);
	
				if(item.type === 'image') {
					item.img = $('<img class="mfp-img" />').on('load.mfploader', function() {
						item.hasSize = true;
					}).on('error.mfploader', function() {
						item.hasSize = true;
						item.loadError = true;
						_mfpTrigger('LazyLoadError', item);
					}).attr('src', item.src);
				}
	
	
				item.preloaded = true;
			}
		}
	});
	
	/*>>gallery*/
	
	/*>>retina*/
	
	var RETINA_NS = 'retina';
	
	$.magnificPopup.registerModule(RETINA_NS, {
		options: {
			replaceSrc: function(item) {
				return item.src.replace(/\.\w+$/, function(m) { return '@2x' + m; });
			},
			ratio: 1 // Function or number.  Set to 1 to disable.
		},
		proto: {
			initRetina: function() {
				if(window.devicePixelRatio > 1) {
	
					var st = mfp.st.retina,
						ratio = st.ratio;
	
					ratio = !isNaN(ratio) ? ratio : ratio();
	
					if(ratio > 1) {
						_mfpOn('ImageHasSize' + '.' + RETINA_NS, function(e, item) {
							item.img.css({
								'max-width': item.img[0].naturalWidth / ratio,
								'width': '100%'
							});
						});
						_mfpOn('ElementParse' + '.' + RETINA_NS, function(e, item) {
							item.src = st.replaceSrc(item, ratio);
						});
					}
				}
	
			}
		}
	});
	
	/*>>retina*/
	 _checkInstance(); }));

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["jQuery"] = __webpack_require__(4);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["$"] = __webpack_require__(5);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery JavaScript Library v2.2.4
	 * http://jquery.com/
	 *
	 * Includes Sizzle.js
	 * http://sizzlejs.com/
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2016-05-20T17:23Z
	 */
	
	(function( global, factory ) {
	
		if ( typeof module === "object" && typeof module.exports === "object" ) {
			// For CommonJS and CommonJS-like environments where a proper `window`
			// is present, execute the factory and get jQuery.
			// For environments that do not have a `window` with a `document`
			// (such as Node.js), expose a factory as module.exports.
			// This accentuates the need for the creation of a real `window`.
			// e.g. var jQuery = require("jquery")(window);
			// See ticket #14549 for more info.
			module.exports = global.document ?
				factory( global, true ) :
				function( w ) {
					if ( !w.document ) {
						throw new Error( "jQuery requires a window with a document" );
					}
					return factory( w );
				};
		} else {
			factory( global );
		}
	
	// Pass this if window is not defined yet
	}(typeof window !== "undefined" ? window : this, function( window, noGlobal ) {
	
	// Support: Firefox 18+
	// Can't be in strict mode, several libs including ASP.NET trace
	// the stack via arguments.caller.callee and Firefox dies if
	// you try to trace through "use strict" call chains. (#13335)
	//"use strict";
	var arr = [];
	
	var document = window.document;
	
	var slice = arr.slice;
	
	var concat = arr.concat;
	
	var push = arr.push;
	
	var indexOf = arr.indexOf;
	
	var class2type = {};
	
	var toString = class2type.toString;
	
	var hasOwn = class2type.hasOwnProperty;
	
	var support = {};
	
	
	
	var
		version = "2.2.4",
	
		// Define a local copy of jQuery
		jQuery = function( selector, context ) {
	
			// The jQuery object is actually just the init constructor 'enhanced'
			// Need init if jQuery is called (just allow error to be thrown if not included)
			return new jQuery.fn.init( selector, context );
		},
	
		// Support: Android<4.1
		// Make sure we trim BOM and NBSP
		rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
	
		// Matches dashed string for camelizing
		rmsPrefix = /^-ms-/,
		rdashAlpha = /-([\da-z])/gi,
	
		// Used by jQuery.camelCase as callback to replace()
		fcamelCase = function( all, letter ) {
			return letter.toUpperCase();
		};
	
	jQuery.fn = jQuery.prototype = {
	
		// The current version of jQuery being used
		jquery: version,
	
		constructor: jQuery,
	
		// Start with an empty selector
		selector: "",
	
		// The default length of a jQuery object is 0
		length: 0,
	
		toArray: function() {
			return slice.call( this );
		},
	
		// Get the Nth element in the matched element set OR
		// Get the whole matched element set as a clean array
		get: function( num ) {
			return num != null ?
	
				// Return just the one element from the set
				( num < 0 ? this[ num + this.length ] : this[ num ] ) :
	
				// Return all the elements in a clean array
				slice.call( this );
		},
	
		// Take an array of elements and push it onto the stack
		// (returning the new matched element set)
		pushStack: function( elems ) {
	
			// Build a new jQuery matched element set
			var ret = jQuery.merge( this.constructor(), elems );
	
			// Add the old object onto the stack (as a reference)
			ret.prevObject = this;
			ret.context = this.context;
	
			// Return the newly-formed element set
			return ret;
		},
	
		// Execute a callback for every element in the matched set.
		each: function( callback ) {
			return jQuery.each( this, callback );
		},
	
		map: function( callback ) {
			return this.pushStack( jQuery.map( this, function( elem, i ) {
				return callback.call( elem, i, elem );
			} ) );
		},
	
		slice: function() {
			return this.pushStack( slice.apply( this, arguments ) );
		},
	
		first: function() {
			return this.eq( 0 );
		},
	
		last: function() {
			return this.eq( -1 );
		},
	
		eq: function( i ) {
			var len = this.length,
				j = +i + ( i < 0 ? len : 0 );
			return this.pushStack( j >= 0 && j < len ? [ this[ j ] ] : [] );
		},
	
		end: function() {
			return this.prevObject || this.constructor();
		},
	
		// For internal use only.
		// Behaves like an Array's method, not like a jQuery method.
		push: push,
		sort: arr.sort,
		splice: arr.splice
	};
	
	jQuery.extend = jQuery.fn.extend = function() {
		var options, name, src, copy, copyIsArray, clone,
			target = arguments[ 0 ] || {},
			i = 1,
			length = arguments.length,
			deep = false;
	
		// Handle a deep copy situation
		if ( typeof target === "boolean" ) {
			deep = target;
	
			// Skip the boolean and the target
			target = arguments[ i ] || {};
			i++;
		}
	
		// Handle case when target is a string or something (possible in deep copy)
		if ( typeof target !== "object" && !jQuery.isFunction( target ) ) {
			target = {};
		}
	
		// Extend jQuery itself if only one argument is passed
		if ( i === length ) {
			target = this;
			i--;
		}
	
		for ( ; i < length; i++ ) {
	
			// Only deal with non-null/undefined values
			if ( ( options = arguments[ i ] ) != null ) {
	
				// Extend the base object
				for ( name in options ) {
					src = target[ name ];
					copy = options[ name ];
	
					// Prevent never-ending loop
					if ( target === copy ) {
						continue;
					}
	
					// Recurse if we're merging plain objects or arrays
					if ( deep && copy && ( jQuery.isPlainObject( copy ) ||
						( copyIsArray = jQuery.isArray( copy ) ) ) ) {
	
						if ( copyIsArray ) {
							copyIsArray = false;
							clone = src && jQuery.isArray( src ) ? src : [];
	
						} else {
							clone = src && jQuery.isPlainObject( src ) ? src : {};
						}
	
						// Never move original objects, clone them
						target[ name ] = jQuery.extend( deep, clone, copy );
	
					// Don't bring in undefined values
					} else if ( copy !== undefined ) {
						target[ name ] = copy;
					}
				}
			}
		}
	
		// Return the modified object
		return target;
	};
	
	jQuery.extend( {
	
		// Unique for each copy of jQuery on the page
		expando: "jQuery" + ( version + Math.random() ).replace( /\D/g, "" ),
	
		// Assume jQuery is ready without the ready module
		isReady: true,
	
		error: function( msg ) {
			throw new Error( msg );
		},
	
		noop: function() {},
	
		isFunction: function( obj ) {
			return jQuery.type( obj ) === "function";
		},
	
		isArray: Array.isArray,
	
		isWindow: function( obj ) {
			return obj != null && obj === obj.window;
		},
	
		isNumeric: function( obj ) {
	
			// parseFloat NaNs numeric-cast false positives (null|true|false|"")
			// ...but misinterprets leading-number strings, particularly hex literals ("0x...")
			// subtraction forces infinities to NaN
			// adding 1 corrects loss of precision from parseFloat (#15100)
			var realStringObj = obj && obj.toString();
			return !jQuery.isArray( obj ) && ( realStringObj - parseFloat( realStringObj ) + 1 ) >= 0;
		},
	
		isPlainObject: function( obj ) {
			var key;
	
			// Not plain objects:
			// - Any object or value whose internal [[Class]] property is not "[object Object]"
			// - DOM nodes
			// - window
			if ( jQuery.type( obj ) !== "object" || obj.nodeType || jQuery.isWindow( obj ) ) {
				return false;
			}
	
			// Not own constructor property must be Object
			if ( obj.constructor &&
					!hasOwn.call( obj, "constructor" ) &&
					!hasOwn.call( obj.constructor.prototype || {}, "isPrototypeOf" ) ) {
				return false;
			}
	
			// Own properties are enumerated firstly, so to speed up,
			// if last one is own, then all properties are own
			for ( key in obj ) {}
	
			return key === undefined || hasOwn.call( obj, key );
		},
	
		isEmptyObject: function( obj ) {
			var name;
			for ( name in obj ) {
				return false;
			}
			return true;
		},
	
		type: function( obj ) {
			if ( obj == null ) {
				return obj + "";
			}
	
			// Support: Android<4.0, iOS<6 (functionish RegExp)
			return typeof obj === "object" || typeof obj === "function" ?
				class2type[ toString.call( obj ) ] || "object" :
				typeof obj;
		},
	
		// Evaluates a script in a global context
		globalEval: function( code ) {
			var script,
				indirect = eval;
	
			code = jQuery.trim( code );
	
			if ( code ) {
	
				// If the code includes a valid, prologue position
				// strict mode pragma, execute code by injecting a
				// script tag into the document.
				if ( code.indexOf( "use strict" ) === 1 ) {
					script = document.createElement( "script" );
					script.text = code;
					document.head.appendChild( script ).parentNode.removeChild( script );
				} else {
	
					// Otherwise, avoid the DOM node creation, insertion
					// and removal by using an indirect global eval
	
					indirect( code );
				}
			}
		},
	
		// Convert dashed to camelCase; used by the css and data modules
		// Support: IE9-11+
		// Microsoft forgot to hump their vendor prefix (#9572)
		camelCase: function( string ) {
			return string.replace( rmsPrefix, "ms-" ).replace( rdashAlpha, fcamelCase );
		},
	
		nodeName: function( elem, name ) {
			return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();
		},
	
		each: function( obj, callback ) {
			var length, i = 0;
	
			if ( isArrayLike( obj ) ) {
				length = obj.length;
				for ( ; i < length; i++ ) {
					if ( callback.call( obj[ i ], i, obj[ i ] ) === false ) {
						break;
					}
				}
			} else {
				for ( i in obj ) {
					if ( callback.call( obj[ i ], i, obj[ i ] ) === false ) {
						break;
					}
				}
			}
	
			return obj;
		},
	
		// Support: Android<4.1
		trim: function( text ) {
			return text == null ?
				"" :
				( text + "" ).replace( rtrim, "" );
		},
	
		// results is for internal usage only
		makeArray: function( arr, results ) {
			var ret = results || [];
	
			if ( arr != null ) {
				if ( isArrayLike( Object( arr ) ) ) {
					jQuery.merge( ret,
						typeof arr === "string" ?
						[ arr ] : arr
					);
				} else {
					push.call( ret, arr );
				}
			}
	
			return ret;
		},
	
		inArray: function( elem, arr, i ) {
			return arr == null ? -1 : indexOf.call( arr, elem, i );
		},
	
		merge: function( first, second ) {
			var len = +second.length,
				j = 0,
				i = first.length;
	
			for ( ; j < len; j++ ) {
				first[ i++ ] = second[ j ];
			}
	
			first.length = i;
	
			return first;
		},
	
		grep: function( elems, callback, invert ) {
			var callbackInverse,
				matches = [],
				i = 0,
				length = elems.length,
				callbackExpect = !invert;
	
			// Go through the array, only saving the items
			// that pass the validator function
			for ( ; i < length; i++ ) {
				callbackInverse = !callback( elems[ i ], i );
				if ( callbackInverse !== callbackExpect ) {
					matches.push( elems[ i ] );
				}
			}
	
			return matches;
		},
	
		// arg is for internal usage only
		map: function( elems, callback, arg ) {
			var length, value,
				i = 0,
				ret = [];
	
			// Go through the array, translating each of the items to their new values
			if ( isArrayLike( elems ) ) {
				length = elems.length;
				for ( ; i < length; i++ ) {
					value = callback( elems[ i ], i, arg );
	
					if ( value != null ) {
						ret.push( value );
					}
				}
	
			// Go through every key on the object,
			} else {
				for ( i in elems ) {
					value = callback( elems[ i ], i, arg );
	
					if ( value != null ) {
						ret.push( value );
					}
				}
			}
	
			// Flatten any nested arrays
			return concat.apply( [], ret );
		},
	
		// A global GUID counter for objects
		guid: 1,
	
		// Bind a function to a context, optionally partially applying any
		// arguments.
		proxy: function( fn, context ) {
			var tmp, args, proxy;
	
			if ( typeof context === "string" ) {
				tmp = fn[ context ];
				context = fn;
				fn = tmp;
			}
	
			// Quick check to determine if target is callable, in the spec
			// this throws a TypeError, but we will just return undefined.
			if ( !jQuery.isFunction( fn ) ) {
				return undefined;
			}
	
			// Simulated bind
			args = slice.call( arguments, 2 );
			proxy = function() {
				return fn.apply( context || this, args.concat( slice.call( arguments ) ) );
			};
	
			// Set the guid of unique handler to the same of original handler, so it can be removed
			proxy.guid = fn.guid = fn.guid || jQuery.guid++;
	
			return proxy;
		},
	
		now: Date.now,
	
		// jQuery.support is not used in Core but other projects attach their
		// properties to it so it needs to exist.
		support: support
	} );
	
	// JSHint would error on this code due to the Symbol not being defined in ES5.
	// Defining this global in .jshintrc would create a danger of using the global
	// unguarded in another place, it seems safer to just disable JSHint for these
	// three lines.
	/* jshint ignore: start */
	if ( typeof Symbol === "function" ) {
		jQuery.fn[ Symbol.iterator ] = arr[ Symbol.iterator ];
	}
	/* jshint ignore: end */
	
	// Populate the class2type map
	jQuery.each( "Boolean Number String Function Array Date RegExp Object Error Symbol".split( " " ),
	function( i, name ) {
		class2type[ "[object " + name + "]" ] = name.toLowerCase();
	} );
	
	function isArrayLike( obj ) {
	
		// Support: iOS 8.2 (not reproducible in simulator)
		// `in` check used to prevent JIT error (gh-2145)
		// hasOwn isn't used here due to false negatives
		// regarding Nodelist length in IE
		var length = !!obj && "length" in obj && obj.length,
			type = jQuery.type( obj );
	
		if ( type === "function" || jQuery.isWindow( obj ) ) {
			return false;
		}
	
		return type === "array" || length === 0 ||
			typeof length === "number" && length > 0 && ( length - 1 ) in obj;
	}
	var Sizzle =
	/*!
	 * Sizzle CSS Selector Engine v2.2.1
	 * http://sizzlejs.com/
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-10-17
	 */
	(function( window ) {
	
	var i,
		support,
		Expr,
		getText,
		isXML,
		tokenize,
		compile,
		select,
		outermostContext,
		sortInput,
		hasDuplicate,
	
		// Local document vars
		setDocument,
		document,
		docElem,
		documentIsHTML,
		rbuggyQSA,
		rbuggyMatches,
		matches,
		contains,
	
		// Instance-specific data
		expando = "sizzle" + 1 * new Date(),
		preferredDoc = window.document,
		dirruns = 0,
		done = 0,
		classCache = createCache(),
		tokenCache = createCache(),
		compilerCache = createCache(),
		sortOrder = function( a, b ) {
			if ( a === b ) {
				hasDuplicate = true;
			}
			return 0;
		},
	
		// General-purpose constants
		MAX_NEGATIVE = 1 << 31,
	
		// Instance methods
		hasOwn = ({}).hasOwnProperty,
		arr = [],
		pop = arr.pop,
		push_native = arr.push,
		push = arr.push,
		slice = arr.slice,
		// Use a stripped-down indexOf as it's faster than native
		// http://jsperf.com/thor-indexof-vs-for/5
		indexOf = function( list, elem ) {
			var i = 0,
				len = list.length;
			for ( ; i < len; i++ ) {
				if ( list[i] === elem ) {
					return i;
				}
			}
			return -1;
		},
	
		booleans = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
	
		// Regular expressions
	
		// http://www.w3.org/TR/css3-selectors/#whitespace
		whitespace = "[\\x20\\t\\r\\n\\f]",
	
		// http://www.w3.org/TR/CSS21/syndata.html#value-def-identifier
		identifier = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",
	
		// Attribute selectors: http://www.w3.org/TR/selectors/#attribute-selectors
		attributes = "\\[" + whitespace + "*(" + identifier + ")(?:" + whitespace +
			// Operator (capture 2)
			"*([*^$|!~]?=)" + whitespace +
			// "Attribute values must be CSS identifiers [capture 5] or strings [capture 3 or capture 4]"
			"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + identifier + "))|)" + whitespace +
			"*\\]",
	
		pseudos = ":(" + identifier + ")(?:\\((" +
			// To reduce the number of selectors needing tokenize in the preFilter, prefer arguments:
			// 1. quoted (capture 3; capture 4 or capture 5)
			"('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|" +
			// 2. simple (capture 6)
			"((?:\\\\.|[^\\\\()[\\]]|" + attributes + ")*)|" +
			// 3. anything else (capture 2)
			".*" +
			")\\)|)",
	
		// Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
		rwhitespace = new RegExp( whitespace + "+", "g" ),
		rtrim = new RegExp( "^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" + whitespace + "+$", "g" ),
	
		rcomma = new RegExp( "^" + whitespace + "*," + whitespace + "*" ),
		rcombinators = new RegExp( "^" + whitespace + "*([>+~]|" + whitespace + ")" + whitespace + "*" ),
	
		rattributeQuotes = new RegExp( "=" + whitespace + "*([^\\]'\"]*?)" + whitespace + "*\\]", "g" ),
	
		rpseudo = new RegExp( pseudos ),
		ridentifier = new RegExp( "^" + identifier + "$" ),
	
		matchExpr = {
			"ID": new RegExp( "^#(" + identifier + ")" ),
			"CLASS": new RegExp( "^\\.(" + identifier + ")" ),
			"TAG": new RegExp( "^(" + identifier + "|[*])" ),
			"ATTR": new RegExp( "^" + attributes ),
			"PSEUDO": new RegExp( "^" + pseudos ),
			"CHILD": new RegExp( "^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + whitespace +
				"*(even|odd|(([+-]|)(\\d*)n|)" + whitespace + "*(?:([+-]|)" + whitespace +
				"*(\\d+)|))" + whitespace + "*\\)|)", "i" ),
			"bool": new RegExp( "^(?:" + booleans + ")$", "i" ),
			// For use in libraries implementing .is()
			// We use this for POS matching in `select`
			"needsContext": new RegExp( "^" + whitespace + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" +
				whitespace + "*((?:-\\d)?\\d*)" + whitespace + "*\\)|)(?=[^-]|$)", "i" )
		},
	
		rinputs = /^(?:input|select|textarea|button)$/i,
		rheader = /^h\d$/i,
	
		rnative = /^[^{]+\{\s*\[native \w/,
	
		// Easily-parseable/retrievable ID or TAG or CLASS selectors
		rquickExpr = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
	
		rsibling = /[+~]/,
		rescape = /'|\\/g,
	
		// CSS escapes http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
		runescape = new RegExp( "\\\\([\\da-f]{1,6}" + whitespace + "?|(" + whitespace + ")|.)", "ig" ),
		funescape = function( _, escaped, escapedWhitespace ) {
			var high = "0x" + escaped - 0x10000;
			// NaN means non-codepoint
			// Support: Firefox<24
			// Workaround erroneous numeric interpretation of +"0x"
			return high !== high || escapedWhitespace ?
				escaped :
				high < 0 ?
					// BMP codepoint
					String.fromCharCode( high + 0x10000 ) :
					// Supplemental Plane codepoint (surrogate pair)
					String.fromCharCode( high >> 10 | 0xD800, high & 0x3FF | 0xDC00 );
		},
	
		// Used for iframes
		// See setDocument()
		// Removing the function wrapper causes a "Permission Denied"
		// error in IE
		unloadHandler = function() {
			setDocument();
		};
	
	// Optimize for push.apply( _, NodeList )
	try {
		push.apply(
			(arr = slice.call( preferredDoc.childNodes )),
			preferredDoc.childNodes
		);
		// Support: Android<4.0
		// Detect silently failing push.apply
		arr[ preferredDoc.childNodes.length ].nodeType;
	} catch ( e ) {
		push = { apply: arr.length ?
	
			// Leverage slice if possible
			function( target, els ) {
				push_native.apply( target, slice.call(els) );
			} :
	
			// Support: IE<9
			// Otherwise append directly
			function( target, els ) {
				var j = target.length,
					i = 0;
				// Can't trust NodeList.length
				while ( (target[j++] = els[i++]) ) {}
				target.length = j - 1;
			}
		};
	}
	
	function Sizzle( selector, context, results, seed ) {
		var m, i, elem, nid, nidselect, match, groups, newSelector,
			newContext = context && context.ownerDocument,
	
			// nodeType defaults to 9, since context defaults to document
			nodeType = context ? context.nodeType : 9;
	
		results = results || [];
	
		// Return early from calls with invalid selector or context
		if ( typeof selector !== "string" || !selector ||
			nodeType !== 1 && nodeType !== 9 && nodeType !== 11 ) {
	
			return results;
		}
	
		// Try to shortcut find operations (as opposed to filters) in HTML documents
		if ( !seed ) {
	
			if ( ( context ? context.ownerDocument || context : preferredDoc ) !== document ) {
				setDocument( context );
			}
			context = context || document;
	
			if ( documentIsHTML ) {
	
				// If the selector is sufficiently simple, try using a "get*By*" DOM method
				// (excepting DocumentFragment context, where the methods don't exist)
				if ( nodeType !== 11 && (match = rquickExpr.exec( selector )) ) {
	
					// ID selector
					if ( (m = match[1]) ) {
	
						// Document context
						if ( nodeType === 9 ) {
							if ( (elem = context.getElementById( m )) ) {
	
								// Support: IE, Opera, Webkit
								// TODO: identify versions
								// getElementById can match elements by name instead of ID
								if ( elem.id === m ) {
									results.push( elem );
									return results;
								}
							} else {
								return results;
							}
	
						// Element context
						} else {
	
							// Support: IE, Opera, Webkit
							// TODO: identify versions
							// getElementById can match elements by name instead of ID
							if ( newContext && (elem = newContext.getElementById( m )) &&
								contains( context, elem ) &&
								elem.id === m ) {
	
								results.push( elem );
								return results;
							}
						}
	
					// Type selector
					} else if ( match[2] ) {
						push.apply( results, context.getElementsByTagName( selector ) );
						return results;
	
					// Class selector
					} else if ( (m = match[3]) && support.getElementsByClassName &&
						context.getElementsByClassName ) {
	
						push.apply( results, context.getElementsByClassName( m ) );
						return results;
					}
				}
	
				// Take advantage of querySelectorAll
				if ( support.qsa &&
					!compilerCache[ selector + " " ] &&
					(!rbuggyQSA || !rbuggyQSA.test( selector )) ) {
	
					if ( nodeType !== 1 ) {
						newContext = context;
						newSelector = selector;
	
					// qSA looks outside Element context, which is not what we want
					// Thanks to Andrew Dupont for this workaround technique
					// Support: IE <=8
					// Exclude object elements
					} else if ( context.nodeName.toLowerCase() !== "object" ) {
	
						// Capture the context ID, setting it first if necessary
						if ( (nid = context.getAttribute( "id" )) ) {
							nid = nid.replace( rescape, "\\$&" );
						} else {
							context.setAttribute( "id", (nid = expando) );
						}
	
						// Prefix every selector in the list
						groups = tokenize( selector );
						i = groups.length;
						nidselect = ridentifier.test( nid ) ? "#" + nid : "[id='" + nid + "']";
						while ( i-- ) {
							groups[i] = nidselect + " " + toSelector( groups[i] );
						}
						newSelector = groups.join( "," );
	
						// Expand context for sibling selectors
						newContext = rsibling.test( selector ) && testContext( context.parentNode ) ||
							context;
					}
	
					if ( newSelector ) {
						try {
							push.apply( results,
								newContext.querySelectorAll( newSelector )
							);
							return results;
						} catch ( qsaError ) {
						} finally {
							if ( nid === expando ) {
								context.removeAttribute( "id" );
							}
						}
					}
				}
			}
		}
	
		// All others
		return select( selector.replace( rtrim, "$1" ), context, results, seed );
	}
	
	/**
	 * Create key-value caches of limited size
	 * @returns {function(string, object)} Returns the Object data after storing it on itself with
	 *	property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
	 *	deleting the oldest entry
	 */
	function createCache() {
		var keys = [];
	
		function cache( key, value ) {
			// Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
			if ( keys.push( key + " " ) > Expr.cacheLength ) {
				// Only keep the most recent entries
				delete cache[ keys.shift() ];
			}
			return (cache[ key + " " ] = value);
		}
		return cache;
	}
	
	/**
	 * Mark a function for special use by Sizzle
	 * @param {Function} fn The function to mark
	 */
	function markFunction( fn ) {
		fn[ expando ] = true;
		return fn;
	}
	
	/**
	 * Support testing using an element
	 * @param {Function} fn Passed the created div and expects a boolean result
	 */
	function assert( fn ) {
		var div = document.createElement("div");
	
		try {
			return !!fn( div );
		} catch (e) {
			return false;
		} finally {
			// Remove from its parent by default
			if ( div.parentNode ) {
				div.parentNode.removeChild( div );
			}
			// release memory in IE
			div = null;
		}
	}
	
	/**
	 * Adds the same handler for all of the specified attrs
	 * @param {String} attrs Pipe-separated list of attributes
	 * @param {Function} handler The method that will be applied
	 */
	function addHandle( attrs, handler ) {
		var arr = attrs.split("|"),
			i = arr.length;
	
		while ( i-- ) {
			Expr.attrHandle[ arr[i] ] = handler;
		}
	}
	
	/**
	 * Checks document order of two siblings
	 * @param {Element} a
	 * @param {Element} b
	 * @returns {Number} Returns less than 0 if a precedes b, greater than 0 if a follows b
	 */
	function siblingCheck( a, b ) {
		var cur = b && a,
			diff = cur && a.nodeType === 1 && b.nodeType === 1 &&
				( ~b.sourceIndex || MAX_NEGATIVE ) -
				( ~a.sourceIndex || MAX_NEGATIVE );
	
		// Use IE sourceIndex if available on both nodes
		if ( diff ) {
			return diff;
		}
	
		// Check if b follows a
		if ( cur ) {
			while ( (cur = cur.nextSibling) ) {
				if ( cur === b ) {
					return -1;
				}
			}
		}
	
		return a ? 1 : -1;
	}
	
	/**
	 * Returns a function to use in pseudos for input types
	 * @param {String} type
	 */
	function createInputPseudo( type ) {
		return function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return name === "input" && elem.type === type;
		};
	}
	
	/**
	 * Returns a function to use in pseudos for buttons
	 * @param {String} type
	 */
	function createButtonPseudo( type ) {
		return function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return (name === "input" || name === "button") && elem.type === type;
		};
	}
	
	/**
	 * Returns a function to use in pseudos for positionals
	 * @param {Function} fn
	 */
	function createPositionalPseudo( fn ) {
		return markFunction(function( argument ) {
			argument = +argument;
			return markFunction(function( seed, matches ) {
				var j,
					matchIndexes = fn( [], seed.length, argument ),
					i = matchIndexes.length;
	
				// Match elements found at the specified indexes
				while ( i-- ) {
					if ( seed[ (j = matchIndexes[i]) ] ) {
						seed[j] = !(matches[j] = seed[j]);
					}
				}
			});
		});
	}
	
	/**
	 * Checks a node for validity as a Sizzle context
	 * @param {Element|Object=} context
	 * @returns {Element|Object|Boolean} The input node if acceptable, otherwise a falsy value
	 */
	function testContext( context ) {
		return context && typeof context.getElementsByTagName !== "undefined" && context;
	}
	
	// Expose support vars for convenience
	support = Sizzle.support = {};
	
	/**
	 * Detects XML nodes
	 * @param {Element|Object} elem An element or a document
	 * @returns {Boolean} True iff elem is a non-HTML XML node
	 */
	isXML = Sizzle.isXML = function( elem ) {
		// documentElement is verified for cases where it doesn't yet exist
		// (such as loading iframes in IE - #4833)
		var documentElement = elem && (elem.ownerDocument || elem).documentElement;
		return documentElement ? documentElement.nodeName !== "HTML" : false;
	};
	
	/**
	 * Sets document-related variables once based on the current document
	 * @param {Element|Object} [doc] An element or document object to use to set the document
	 * @returns {Object} Returns the current document
	 */
	setDocument = Sizzle.setDocument = function( node ) {
		var hasCompare, parent,
			doc = node ? node.ownerDocument || node : preferredDoc;
	
		// Return early if doc is invalid or already selected
		if ( doc === document || doc.nodeType !== 9 || !doc.documentElement ) {
			return document;
		}
	
		// Update global variables
		document = doc;
		docElem = document.documentElement;
		documentIsHTML = !isXML( document );
	
		// Support: IE 9-11, Edge
		// Accessing iframe documents after unload throws "permission denied" errors (jQuery #13936)
		if ( (parent = document.defaultView) && parent.top !== parent ) {
			// Support: IE 11
			if ( parent.addEventListener ) {
				parent.addEventListener( "unload", unloadHandler, false );
	
			// Support: IE 9 - 10 only
			} else if ( parent.attachEvent ) {
				parent.attachEvent( "onunload", unloadHandler );
			}
		}
	
		/* Attributes
		---------------------------------------------------------------------- */
	
		// Support: IE<8
		// Verify that getAttribute really returns attributes and not properties
		// (excepting IE8 booleans)
		support.attributes = assert(function( div ) {
			div.className = "i";
			return !div.getAttribute("className");
		});
	
		/* getElement(s)By*
		---------------------------------------------------------------------- */
	
		// Check if getElementsByTagName("*") returns only elements
		support.getElementsByTagName = assert(function( div ) {
			div.appendChild( document.createComment("") );
			return !div.getElementsByTagName("*").length;
		});
	
		// Support: IE<9
		support.getElementsByClassName = rnative.test( document.getElementsByClassName );
	
		// Support: IE<10
		// Check if getElementById returns elements by name
		// The broken getElementById methods don't pick up programatically-set names,
		// so use a roundabout getElementsByName test
		support.getById = assert(function( div ) {
			docElem.appendChild( div ).id = expando;
			return !document.getElementsByName || !document.getElementsByName( expando ).length;
		});
	
		// ID find and filter
		if ( support.getById ) {
			Expr.find["ID"] = function( id, context ) {
				if ( typeof context.getElementById !== "undefined" && documentIsHTML ) {
					var m = context.getElementById( id );
					return m ? [ m ] : [];
				}
			};
			Expr.filter["ID"] = function( id ) {
				var attrId = id.replace( runescape, funescape );
				return function( elem ) {
					return elem.getAttribute("id") === attrId;
				};
			};
		} else {
			// Support: IE6/7
			// getElementById is not reliable as a find shortcut
			delete Expr.find["ID"];
	
			Expr.filter["ID"] =  function( id ) {
				var attrId = id.replace( runescape, funescape );
				return function( elem ) {
					var node = typeof elem.getAttributeNode !== "undefined" &&
						elem.getAttributeNode("id");
					return node && node.value === attrId;
				};
			};
		}
	
		// Tag
		Expr.find["TAG"] = support.getElementsByTagName ?
			function( tag, context ) {
				if ( typeof context.getElementsByTagName !== "undefined" ) {
					return context.getElementsByTagName( tag );
	
				// DocumentFragment nodes don't have gEBTN
				} else if ( support.qsa ) {
					return context.querySelectorAll( tag );
				}
			} :
	
			function( tag, context ) {
				var elem,
					tmp = [],
					i = 0,
					// By happy coincidence, a (broken) gEBTN appears on DocumentFragment nodes too
					results = context.getElementsByTagName( tag );
	
				// Filter out possible comments
				if ( tag === "*" ) {
					while ( (elem = results[i++]) ) {
						if ( elem.nodeType === 1 ) {
							tmp.push( elem );
						}
					}
	
					return tmp;
				}
				return results;
			};
	
		// Class
		Expr.find["CLASS"] = support.getElementsByClassName && function( className, context ) {
			if ( typeof context.getElementsByClassName !== "undefined" && documentIsHTML ) {
				return context.getElementsByClassName( className );
			}
		};
	
		/* QSA/matchesSelector
		---------------------------------------------------------------------- */
	
		// QSA and matchesSelector support
	
		// matchesSelector(:active) reports false when true (IE9/Opera 11.5)
		rbuggyMatches = [];
	
		// qSa(:focus) reports false when true (Chrome 21)
		// We allow this because of a bug in IE8/9 that throws an error
		// whenever `document.activeElement` is accessed on an iframe
		// So, we allow :focus to pass through QSA all the time to avoid the IE error
		// See http://bugs.jquery.com/ticket/13378
		rbuggyQSA = [];
	
		if ( (support.qsa = rnative.test( document.querySelectorAll )) ) {
			// Build QSA regex
			// Regex strategy adopted from Diego Perini
			assert(function( div ) {
				// Select is set to empty string on purpose
				// This is to test IE's treatment of not explicitly
				// setting a boolean content attribute,
				// since its presence should be enough
				// http://bugs.jquery.com/ticket/12359
				docElem.appendChild( div ).innerHTML = "<a id='" + expando + "'></a>" +
					"<select id='" + expando + "-\r\\' msallowcapture=''>" +
					"<option selected=''></option></select>";
	
				// Support: IE8, Opera 11-12.16
				// Nothing should be selected when empty strings follow ^= or $= or *=
				// The test attribute must be unknown in Opera but "safe" for WinRT
				// http://msdn.microsoft.com/en-us/library/ie/hh465388.aspx#attribute_section
				if ( div.querySelectorAll("[msallowcapture^='']").length ) {
					rbuggyQSA.push( "[*^$]=" + whitespace + "*(?:''|\"\")" );
				}
	
				// Support: IE8
				// Boolean attributes and "value" are not treated correctly
				if ( !div.querySelectorAll("[selected]").length ) {
					rbuggyQSA.push( "\\[" + whitespace + "*(?:value|" + booleans + ")" );
				}
	
				// Support: Chrome<29, Android<4.4, Safari<7.0+, iOS<7.0+, PhantomJS<1.9.8+
				if ( !div.querySelectorAll( "[id~=" + expando + "-]" ).length ) {
					rbuggyQSA.push("~=");
				}
	
				// Webkit/Opera - :checked should return selected option elements
				// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
				// IE8 throws error here and will not see later tests
				if ( !div.querySelectorAll(":checked").length ) {
					rbuggyQSA.push(":checked");
				}
	
				// Support: Safari 8+, iOS 8+
				// https://bugs.webkit.org/show_bug.cgi?id=136851
				// In-page `selector#id sibing-combinator selector` fails
				if ( !div.querySelectorAll( "a#" + expando + "+*" ).length ) {
					rbuggyQSA.push(".#.+[+~]");
				}
			});
	
			assert(function( div ) {
				// Support: Windows 8 Native Apps
				// The type and name attributes are restricted during .innerHTML assignment
				var input = document.createElement("input");
				input.setAttribute( "type", "hidden" );
				div.appendChild( input ).setAttribute( "name", "D" );
	
				// Support: IE8
				// Enforce case-sensitivity of name attribute
				if ( div.querySelectorAll("[name=d]").length ) {
					rbuggyQSA.push( "name" + whitespace + "*[*^$|!~]?=" );
				}
	
				// FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
				// IE8 throws error here and will not see later tests
				if ( !div.querySelectorAll(":enabled").length ) {
					rbuggyQSA.push( ":enabled", ":disabled" );
				}
	
				// Opera 10-11 does not throw on post-comma invalid pseudos
				div.querySelectorAll("*,:x");
				rbuggyQSA.push(",.*:");
			});
		}
	
		if ( (support.matchesSelector = rnative.test( (matches = docElem.matches ||
			docElem.webkitMatchesSelector ||
			docElem.mozMatchesSelector ||
			docElem.oMatchesSelector ||
			docElem.msMatchesSelector) )) ) {
	
			assert(function( div ) {
				// Check to see if it's possible to do matchesSelector
				// on a disconnected node (IE 9)
				support.disconnectedMatch = matches.call( div, "div" );
	
				// This should fail with an exception
				// Gecko does not error, returns false instead
				matches.call( div, "[s!='']:x" );
				rbuggyMatches.push( "!=", pseudos );
			});
		}
	
		rbuggyQSA = rbuggyQSA.length && new RegExp( rbuggyQSA.join("|") );
		rbuggyMatches = rbuggyMatches.length && new RegExp( rbuggyMatches.join("|") );
	
		/* Contains
		---------------------------------------------------------------------- */
		hasCompare = rnative.test( docElem.compareDocumentPosition );
	
		// Element contains another
		// Purposefully self-exclusive
		// As in, an element does not contain itself
		contains = hasCompare || rnative.test( docElem.contains ) ?
			function( a, b ) {
				var adown = a.nodeType === 9 ? a.documentElement : a,
					bup = b && b.parentNode;
				return a === bup || !!( bup && bup.nodeType === 1 && (
					adown.contains ?
						adown.contains( bup ) :
						a.compareDocumentPosition && a.compareDocumentPosition( bup ) & 16
				));
			} :
			function( a, b ) {
				if ( b ) {
					while ( (b = b.parentNode) ) {
						if ( b === a ) {
							return true;
						}
					}
				}
				return false;
			};
	
		/* Sorting
		---------------------------------------------------------------------- */
	
		// Document order sorting
		sortOrder = hasCompare ?
		function( a, b ) {
	
			// Flag for duplicate removal
			if ( a === b ) {
				hasDuplicate = true;
				return 0;
			}
	
			// Sort on method existence if only one input has compareDocumentPosition
			var compare = !a.compareDocumentPosition - !b.compareDocumentPosition;
			if ( compare ) {
				return compare;
			}
	
			// Calculate position if both inputs belong to the same document
			compare = ( a.ownerDocument || a ) === ( b.ownerDocument || b ) ?
				a.compareDocumentPosition( b ) :
	
				// Otherwise we know they are disconnected
				1;
	
			// Disconnected nodes
			if ( compare & 1 ||
				(!support.sortDetached && b.compareDocumentPosition( a ) === compare) ) {
	
				// Choose the first element that is related to our preferred document
				if ( a === document || a.ownerDocument === preferredDoc && contains(preferredDoc, a) ) {
					return -1;
				}
				if ( b === document || b.ownerDocument === preferredDoc && contains(preferredDoc, b) ) {
					return 1;
				}
	
				// Maintain original order
				return sortInput ?
					( indexOf( sortInput, a ) - indexOf( sortInput, b ) ) :
					0;
			}
	
			return compare & 4 ? -1 : 1;
		} :
		function( a, b ) {
			// Exit early if the nodes are identical
			if ( a === b ) {
				hasDuplicate = true;
				return 0;
			}
	
			var cur,
				i = 0,
				aup = a.parentNode,
				bup = b.parentNode,
				ap = [ a ],
				bp = [ b ];
	
			// Parentless nodes are either documents or disconnected
			if ( !aup || !bup ) {
				return a === document ? -1 :
					b === document ? 1 :
					aup ? -1 :
					bup ? 1 :
					sortInput ?
					( indexOf( sortInput, a ) - indexOf( sortInput, b ) ) :
					0;
	
			// If the nodes are siblings, we can do a quick check
			} else if ( aup === bup ) {
				return siblingCheck( a, b );
			}
	
			// Otherwise we need full lists of their ancestors for comparison
			cur = a;
			while ( (cur = cur.parentNode) ) {
				ap.unshift( cur );
			}
			cur = b;
			while ( (cur = cur.parentNode) ) {
				bp.unshift( cur );
			}
	
			// Walk down the tree looking for a discrepancy
			while ( ap[i] === bp[i] ) {
				i++;
			}
	
			return i ?
				// Do a sibling check if the nodes have a common ancestor
				siblingCheck( ap[i], bp[i] ) :
	
				// Otherwise nodes in our document sort first
				ap[i] === preferredDoc ? -1 :
				bp[i] === preferredDoc ? 1 :
				0;
		};
	
		return document;
	};
	
	Sizzle.matches = function( expr, elements ) {
		return Sizzle( expr, null, null, elements );
	};
	
	Sizzle.matchesSelector = function( elem, expr ) {
		// Set document vars if needed
		if ( ( elem.ownerDocument || elem ) !== document ) {
			setDocument( elem );
		}
	
		// Make sure that attribute selectors are quoted
		expr = expr.replace( rattributeQuotes, "='$1']" );
	
		if ( support.matchesSelector && documentIsHTML &&
			!compilerCache[ expr + " " ] &&
			( !rbuggyMatches || !rbuggyMatches.test( expr ) ) &&
			( !rbuggyQSA     || !rbuggyQSA.test( expr ) ) ) {
	
			try {
				var ret = matches.call( elem, expr );
	
				// IE 9's matchesSelector returns false on disconnected nodes
				if ( ret || support.disconnectedMatch ||
						// As well, disconnected nodes are said to be in a document
						// fragment in IE 9
						elem.document && elem.document.nodeType !== 11 ) {
					return ret;
				}
			} catch (e) {}
		}
	
		return Sizzle( expr, document, null, [ elem ] ).length > 0;
	};
	
	Sizzle.contains = function( context, elem ) {
		// Set document vars if needed
		if ( ( context.ownerDocument || context ) !== document ) {
			setDocument( context );
		}
		return contains( context, elem );
	};
	
	Sizzle.attr = function( elem, name ) {
		// Set document vars if needed
		if ( ( elem.ownerDocument || elem ) !== document ) {
			setDocument( elem );
		}
	
		var fn = Expr.attrHandle[ name.toLowerCase() ],
			// Don't get fooled by Object.prototype properties (jQuery #13807)
			val = fn && hasOwn.call( Expr.attrHandle, name.toLowerCase() ) ?
				fn( elem, name, !documentIsHTML ) :
				undefined;
	
		return val !== undefined ?
			val :
			support.attributes || !documentIsHTML ?
				elem.getAttribute( name ) :
				(val = elem.getAttributeNode(name)) && val.specified ?
					val.value :
					null;
	};
	
	Sizzle.error = function( msg ) {
		throw new Error( "Syntax error, unrecognized expression: " + msg );
	};
	
	/**
	 * Document sorting and removing duplicates
	 * @param {ArrayLike} results
	 */
	Sizzle.uniqueSort = function( results ) {
		var elem,
			duplicates = [],
			j = 0,
			i = 0;
	
		// Unless we *know* we can detect duplicates, assume their presence
		hasDuplicate = !support.detectDuplicates;
		sortInput = !support.sortStable && results.slice( 0 );
		results.sort( sortOrder );
	
		if ( hasDuplicate ) {
			while ( (elem = results[i++]) ) {
				if ( elem === results[ i ] ) {
					j = duplicates.push( i );
				}
			}
			while ( j-- ) {
				results.splice( duplicates[ j ], 1 );
			}
		}
	
		// Clear input after sorting to release objects
		// See https://github.com/jquery/sizzle/pull/225
		sortInput = null;
	
		return results;
	};
	
	/**
	 * Utility function for retrieving the text value of an array of DOM nodes
	 * @param {Array|Element} elem
	 */
	getText = Sizzle.getText = function( elem ) {
		var node,
			ret = "",
			i = 0,
			nodeType = elem.nodeType;
	
		if ( !nodeType ) {
			// If no nodeType, this is expected to be an array
			while ( (node = elem[i++]) ) {
				// Do not traverse comment nodes
				ret += getText( node );
			}
		} else if ( nodeType === 1 || nodeType === 9 || nodeType === 11 ) {
			// Use textContent for elements
			// innerText usage removed for consistency of new lines (jQuery #11153)
			if ( typeof elem.textContent === "string" ) {
				return elem.textContent;
			} else {
				// Traverse its children
				for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
					ret += getText( elem );
				}
			}
		} else if ( nodeType === 3 || nodeType === 4 ) {
			return elem.nodeValue;
		}
		// Do not include comment or processing instruction nodes
	
		return ret;
	};
	
	Expr = Sizzle.selectors = {
	
		// Can be adjusted by the user
		cacheLength: 50,
	
		createPseudo: markFunction,
	
		match: matchExpr,
	
		attrHandle: {},
	
		find: {},
	
		relative: {
			">": { dir: "parentNode", first: true },
			" ": { dir: "parentNode" },
			"+": { dir: "previousSibling", first: true },
			"~": { dir: "previousSibling" }
		},
	
		preFilter: {
			"ATTR": function( match ) {
				match[1] = match[1].replace( runescape, funescape );
	
				// Move the given value to match[3] whether quoted or unquoted
				match[3] = ( match[3] || match[4] || match[5] || "" ).replace( runescape, funescape );
	
				if ( match[2] === "~=" ) {
					match[3] = " " + match[3] + " ";
				}
	
				return match.slice( 0, 4 );
			},
	
			"CHILD": function( match ) {
				/* matches from matchExpr["CHILD"]
					1 type (only|nth|...)
					2 what (child|of-type)
					3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
					4 xn-component of xn+y argument ([+-]?\d*n|)
					5 sign of xn-component
					6 x of xn-component
					7 sign of y-component
					8 y of y-component
				*/
				match[1] = match[1].toLowerCase();
	
				if ( match[1].slice( 0, 3 ) === "nth" ) {
					// nth-* requires argument
					if ( !match[3] ) {
						Sizzle.error( match[0] );
					}
	
					// numeric x and y parameters for Expr.filter.CHILD
					// remember that false/true cast respectively to 0/1
					match[4] = +( match[4] ? match[5] + (match[6] || 1) : 2 * ( match[3] === "even" || match[3] === "odd" ) );
					match[5] = +( ( match[7] + match[8] ) || match[3] === "odd" );
	
				// other types prohibit arguments
				} else if ( match[3] ) {
					Sizzle.error( match[0] );
				}
	
				return match;
			},
	
			"PSEUDO": function( match ) {
				var excess,
					unquoted = !match[6] && match[2];
	
				if ( matchExpr["CHILD"].test( match[0] ) ) {
					return null;
				}
	
				// Accept quoted arguments as-is
				if ( match[3] ) {
					match[2] = match[4] || match[5] || "";
	
				// Strip excess characters from unquoted arguments
				} else if ( unquoted && rpseudo.test( unquoted ) &&
					// Get excess from tokenize (recursively)
					(excess = tokenize( unquoted, true )) &&
					// advance to the next closing parenthesis
					(excess = unquoted.indexOf( ")", unquoted.length - excess ) - unquoted.length) ) {
	
					// excess is a negative index
					match[0] = match[0].slice( 0, excess );
					match[2] = unquoted.slice( 0, excess );
				}
	
				// Return only captures needed by the pseudo filter method (type and argument)
				return match.slice( 0, 3 );
			}
		},
	
		filter: {
	
			"TAG": function( nodeNameSelector ) {
				var nodeName = nodeNameSelector.replace( runescape, funescape ).toLowerCase();
				return nodeNameSelector === "*" ?
					function() { return true; } :
					function( elem ) {
						return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;
					};
			},
	
			"CLASS": function( className ) {
				var pattern = classCache[ className + " " ];
	
				return pattern ||
					(pattern = new RegExp( "(^|" + whitespace + ")" + className + "(" + whitespace + "|$)" )) &&
					classCache( className, function( elem ) {
						return pattern.test( typeof elem.className === "string" && elem.className || typeof elem.getAttribute !== "undefined" && elem.getAttribute("class") || "" );
					});
			},
	
			"ATTR": function( name, operator, check ) {
				return function( elem ) {
					var result = Sizzle.attr( elem, name );
	
					if ( result == null ) {
						return operator === "!=";
					}
					if ( !operator ) {
						return true;
					}
	
					result += "";
	
					return operator === "=" ? result === check :
						operator === "!=" ? result !== check :
						operator === "^=" ? check && result.indexOf( check ) === 0 :
						operator === "*=" ? check && result.indexOf( check ) > -1 :
						operator === "$=" ? check && result.slice( -check.length ) === check :
						operator === "~=" ? ( " " + result.replace( rwhitespace, " " ) + " " ).indexOf( check ) > -1 :
						operator === "|=" ? result === check || result.slice( 0, check.length + 1 ) === check + "-" :
						false;
				};
			},
	
			"CHILD": function( type, what, argument, first, last ) {
				var simple = type.slice( 0, 3 ) !== "nth",
					forward = type.slice( -4 ) !== "last",
					ofType = what === "of-type";
	
				return first === 1 && last === 0 ?
	
					// Shortcut for :nth-*(n)
					function( elem ) {
						return !!elem.parentNode;
					} :
	
					function( elem, context, xml ) {
						var cache, uniqueCache, outerCache, node, nodeIndex, start,
							dir = simple !== forward ? "nextSibling" : "previousSibling",
							parent = elem.parentNode,
							name = ofType && elem.nodeName.toLowerCase(),
							useCache = !xml && !ofType,
							diff = false;
	
						if ( parent ) {
	
							// :(first|last|only)-(child|of-type)
							if ( simple ) {
								while ( dir ) {
									node = elem;
									while ( (node = node[ dir ]) ) {
										if ( ofType ?
											node.nodeName.toLowerCase() === name :
											node.nodeType === 1 ) {
	
											return false;
										}
									}
									// Reverse direction for :only-* (if we haven't yet done so)
									start = dir = type === "only" && !start && "nextSibling";
								}
								return true;
							}
	
							start = [ forward ? parent.firstChild : parent.lastChild ];
	
							// non-xml :nth-child(...) stores cache data on `parent`
							if ( forward && useCache ) {
	
								// Seek `elem` from a previously-cached index
	
								// ...in a gzip-friendly way
								node = parent;
								outerCache = node[ expando ] || (node[ expando ] = {});
	
								// Support: IE <9 only
								// Defend against cloned attroperties (jQuery gh-1709)
								uniqueCache = outerCache[ node.uniqueID ] ||
									(outerCache[ node.uniqueID ] = {});
	
								cache = uniqueCache[ type ] || [];
								nodeIndex = cache[ 0 ] === dirruns && cache[ 1 ];
								diff = nodeIndex && cache[ 2 ];
								node = nodeIndex && parent.childNodes[ nodeIndex ];
	
								while ( (node = ++nodeIndex && node && node[ dir ] ||
	
									// Fallback to seeking `elem` from the start
									(diff = nodeIndex = 0) || start.pop()) ) {
	
									// When found, cache indexes on `parent` and break
									if ( node.nodeType === 1 && ++diff && node === elem ) {
										uniqueCache[ type ] = [ dirruns, nodeIndex, diff ];
										break;
									}
								}
	
							} else {
								// Use previously-cached element index if available
								if ( useCache ) {
									// ...in a gzip-friendly way
									node = elem;
									outerCache = node[ expando ] || (node[ expando ] = {});
	
									// Support: IE <9 only
									// Defend against cloned attroperties (jQuery gh-1709)
									uniqueCache = outerCache[ node.uniqueID ] ||
										(outerCache[ node.uniqueID ] = {});
	
									cache = uniqueCache[ type ] || [];
									nodeIndex = cache[ 0 ] === dirruns && cache[ 1 ];
									diff = nodeIndex;
								}
	
								// xml :nth-child(...)
								// or :nth-last-child(...) or :nth(-last)?-of-type(...)
								if ( diff === false ) {
									// Use the same loop as above to seek `elem` from the start
									while ( (node = ++nodeIndex && node && node[ dir ] ||
										(diff = nodeIndex = 0) || start.pop()) ) {
	
										if ( ( ofType ?
											node.nodeName.toLowerCase() === name :
											node.nodeType === 1 ) &&
											++diff ) {
	
											// Cache the index of each encountered element
											if ( useCache ) {
												outerCache = node[ expando ] || (node[ expando ] = {});
	
												// Support: IE <9 only
												// Defend against cloned attroperties (jQuery gh-1709)
												uniqueCache = outerCache[ node.uniqueID ] ||
													(outerCache[ node.uniqueID ] = {});
	
												uniqueCache[ type ] = [ dirruns, diff ];
											}
	
											if ( node === elem ) {
												break;
											}
										}
									}
								}
							}
	
							// Incorporate the offset, then check against cycle size
							diff -= last;
							return diff === first || ( diff % first === 0 && diff / first >= 0 );
						}
					};
			},
	
			"PSEUDO": function( pseudo, argument ) {
				// pseudo-class names are case-insensitive
				// http://www.w3.org/TR/selectors/#pseudo-classes
				// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
				// Remember that setFilters inherits from pseudos
				var args,
					fn = Expr.pseudos[ pseudo ] || Expr.setFilters[ pseudo.toLowerCase() ] ||
						Sizzle.error( "unsupported pseudo: " + pseudo );
	
				// The user may use createPseudo to indicate that
				// arguments are needed to create the filter function
				// just as Sizzle does
				if ( fn[ expando ] ) {
					return fn( argument );
				}
	
				// But maintain support for old signatures
				if ( fn.length > 1 ) {
					args = [ pseudo, pseudo, "", argument ];
					return Expr.setFilters.hasOwnProperty( pseudo.toLowerCase() ) ?
						markFunction(function( seed, matches ) {
							var idx,
								matched = fn( seed, argument ),
								i = matched.length;
							while ( i-- ) {
								idx = indexOf( seed, matched[i] );
								seed[ idx ] = !( matches[ idx ] = matched[i] );
							}
						}) :
						function( elem ) {
							return fn( elem, 0, args );
						};
				}
	
				return fn;
			}
		},
	
		pseudos: {
			// Potentially complex pseudos
			"not": markFunction(function( selector ) {
				// Trim the selector passed to compile
				// to avoid treating leading and trailing
				// spaces as combinators
				var input = [],
					results = [],
					matcher = compile( selector.replace( rtrim, "$1" ) );
	
				return matcher[ expando ] ?
					markFunction(function( seed, matches, context, xml ) {
						var elem,
							unmatched = matcher( seed, null, xml, [] ),
							i = seed.length;
	
						// Match elements unmatched by `matcher`
						while ( i-- ) {
							if ( (elem = unmatched[i]) ) {
								seed[i] = !(matches[i] = elem);
							}
						}
					}) :
					function( elem, context, xml ) {
						input[0] = elem;
						matcher( input, null, xml, results );
						// Don't keep the element (issue #299)
						input[0] = null;
						return !results.pop();
					};
			}),
	
			"has": markFunction(function( selector ) {
				return function( elem ) {
					return Sizzle( selector, elem ).length > 0;
				};
			}),
	
			"contains": markFunction(function( text ) {
				text = text.replace( runescape, funescape );
				return function( elem ) {
					return ( elem.textContent || elem.innerText || getText( elem ) ).indexOf( text ) > -1;
				};
			}),
	
			// "Whether an element is represented by a :lang() selector
			// is based solely on the element's language value
			// being equal to the identifier C,
			// or beginning with the identifier C immediately followed by "-".
			// The matching of C against the element's language value is performed case-insensitively.
			// The identifier C does not have to be a valid language name."
			// http://www.w3.org/TR/selectors/#lang-pseudo
			"lang": markFunction( function( lang ) {
				// lang value must be a valid identifier
				if ( !ridentifier.test(lang || "") ) {
					Sizzle.error( "unsupported lang: " + lang );
				}
				lang = lang.replace( runescape, funescape ).toLowerCase();
				return function( elem ) {
					var elemLang;
					do {
						if ( (elemLang = documentIsHTML ?
							elem.lang :
							elem.getAttribute("xml:lang") || elem.getAttribute("lang")) ) {
	
							elemLang = elemLang.toLowerCase();
							return elemLang === lang || elemLang.indexOf( lang + "-" ) === 0;
						}
					} while ( (elem = elem.parentNode) && elem.nodeType === 1 );
					return false;
				};
			}),
	
			// Miscellaneous
			"target": function( elem ) {
				var hash = window.location && window.location.hash;
				return hash && hash.slice( 1 ) === elem.id;
			},
	
			"root": function( elem ) {
				return elem === docElem;
			},
	
			"focus": function( elem ) {
				return elem === document.activeElement && (!document.hasFocus || document.hasFocus()) && !!(elem.type || elem.href || ~elem.tabIndex);
			},
	
			// Boolean properties
			"enabled": function( elem ) {
				return elem.disabled === false;
			},
	
			"disabled": function( elem ) {
				return elem.disabled === true;
			},
	
			"checked": function( elem ) {
				// In CSS3, :checked should return both checked and selected elements
				// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
				var nodeName = elem.nodeName.toLowerCase();
				return (nodeName === "input" && !!elem.checked) || (nodeName === "option" && !!elem.selected);
			},
	
			"selected": function( elem ) {
				// Accessing this property makes selected-by-default
				// options in Safari work properly
				if ( elem.parentNode ) {
					elem.parentNode.selectedIndex;
				}
	
				return elem.selected === true;
			},
	
			// Contents
			"empty": function( elem ) {
				// http://www.w3.org/TR/selectors/#empty-pseudo
				// :empty is negated by element (1) or content nodes (text: 3; cdata: 4; entity ref: 5),
				//   but not by others (comment: 8; processing instruction: 7; etc.)
				// nodeType < 6 works because attributes (2) do not appear as children
				for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
					if ( elem.nodeType < 6 ) {
						return false;
					}
				}
				return true;
			},
	
			"parent": function( elem ) {
				return !Expr.pseudos["empty"]( elem );
			},
	
			// Element/input types
			"header": function( elem ) {
				return rheader.test( elem.nodeName );
			},
	
			"input": function( elem ) {
				return rinputs.test( elem.nodeName );
			},
	
			"button": function( elem ) {
				var name = elem.nodeName.toLowerCase();
				return name === "input" && elem.type === "button" || name === "button";
			},
	
			"text": function( elem ) {
				var attr;
				return elem.nodeName.toLowerCase() === "input" &&
					elem.type === "text" &&
	
					// Support: IE<8
					// New HTML5 attribute values (e.g., "search") appear with elem.type === "text"
					( (attr = elem.getAttribute("type")) == null || attr.toLowerCase() === "text" );
			},
	
			// Position-in-collection
			"first": createPositionalPseudo(function() {
				return [ 0 ];
			}),
	
			"last": createPositionalPseudo(function( matchIndexes, length ) {
				return [ length - 1 ];
			}),
	
			"eq": createPositionalPseudo(function( matchIndexes, length, argument ) {
				return [ argument < 0 ? argument + length : argument ];
			}),
	
			"even": createPositionalPseudo(function( matchIndexes, length ) {
				var i = 0;
				for ( ; i < length; i += 2 ) {
					matchIndexes.push( i );
				}
				return matchIndexes;
			}),
	
			"odd": createPositionalPseudo(function( matchIndexes, length ) {
				var i = 1;
				for ( ; i < length; i += 2 ) {
					matchIndexes.push( i );
				}
				return matchIndexes;
			}),
	
			"lt": createPositionalPseudo(function( matchIndexes, length, argument ) {
				var i = argument < 0 ? argument + length : argument;
				for ( ; --i >= 0; ) {
					matchIndexes.push( i );
				}
				return matchIndexes;
			}),
	
			"gt": createPositionalPseudo(function( matchIndexes, length, argument ) {
				var i = argument < 0 ? argument + length : argument;
				for ( ; ++i < length; ) {
					matchIndexes.push( i );
				}
				return matchIndexes;
			})
		}
	};
	
	Expr.pseudos["nth"] = Expr.pseudos["eq"];
	
	// Add button/input type pseudos
	for ( i in { radio: true, checkbox: true, file: true, password: true, image: true } ) {
		Expr.pseudos[ i ] = createInputPseudo( i );
	}
	for ( i in { submit: true, reset: true } ) {
		Expr.pseudos[ i ] = createButtonPseudo( i );
	}
	
	// Easy API for creating new setFilters
	function setFilters() {}
	setFilters.prototype = Expr.filters = Expr.pseudos;
	Expr.setFilters = new setFilters();
	
	tokenize = Sizzle.tokenize = function( selector, parseOnly ) {
		var matched, match, tokens, type,
			soFar, groups, preFilters,
			cached = tokenCache[ selector + " " ];
	
		if ( cached ) {
			return parseOnly ? 0 : cached.slice( 0 );
		}
	
		soFar = selector;
		groups = [];
		preFilters = Expr.preFilter;
	
		while ( soFar ) {
	
			// Comma and first run
			if ( !matched || (match = rcomma.exec( soFar )) ) {
				if ( match ) {
					// Don't consume trailing commas as valid
					soFar = soFar.slice( match[0].length ) || soFar;
				}
				groups.push( (tokens = []) );
			}
	
			matched = false;
	
			// Combinators
			if ( (match = rcombinators.exec( soFar )) ) {
				matched = match.shift();
				tokens.push({
					value: matched,
					// Cast descendant combinators to space
					type: match[0].replace( rtrim, " " )
				});
				soFar = soFar.slice( matched.length );
			}
	
			// Filters
			for ( type in Expr.filter ) {
				if ( (match = matchExpr[ type ].exec( soFar )) && (!preFilters[ type ] ||
					(match = preFilters[ type ]( match ))) ) {
					matched = match.shift();
					tokens.push({
						value: matched,
						type: type,
						matches: match
					});
					soFar = soFar.slice( matched.length );
				}
			}
	
			if ( !matched ) {
				break;
			}
		}
	
		// Return the length of the invalid excess
		// if we're just parsing
		// Otherwise, throw an error or return tokens
		return parseOnly ?
			soFar.length :
			soFar ?
				Sizzle.error( selector ) :
				// Cache the tokens
				tokenCache( selector, groups ).slice( 0 );
	};
	
	function toSelector( tokens ) {
		var i = 0,
			len = tokens.length,
			selector = "";
		for ( ; i < len; i++ ) {
			selector += tokens[i].value;
		}
		return selector;
	}
	
	function addCombinator( matcher, combinator, base ) {
		var dir = combinator.dir,
			checkNonElements = base && dir === "parentNode",
			doneName = done++;
	
		return combinator.first ?
			// Check against closest ancestor/preceding element
			function( elem, context, xml ) {
				while ( (elem = elem[ dir ]) ) {
					if ( elem.nodeType === 1 || checkNonElements ) {
						return matcher( elem, context, xml );
					}
				}
			} :
	
			// Check against all ancestor/preceding elements
			function( elem, context, xml ) {
				var oldCache, uniqueCache, outerCache,
					newCache = [ dirruns, doneName ];
	
				// We can't set arbitrary data on XML nodes, so they don't benefit from combinator caching
				if ( xml ) {
					while ( (elem = elem[ dir ]) ) {
						if ( elem.nodeType === 1 || checkNonElements ) {
							if ( matcher( elem, context, xml ) ) {
								return true;
							}
						}
					}
				} else {
					while ( (elem = elem[ dir ]) ) {
						if ( elem.nodeType === 1 || checkNonElements ) {
							outerCache = elem[ expando ] || (elem[ expando ] = {});
	
							// Support: IE <9 only
							// Defend against cloned attroperties (jQuery gh-1709)
							uniqueCache = outerCache[ elem.uniqueID ] || (outerCache[ elem.uniqueID ] = {});
	
							if ( (oldCache = uniqueCache[ dir ]) &&
								oldCache[ 0 ] === dirruns && oldCache[ 1 ] === doneName ) {
	
								// Assign to newCache so results back-propagate to previous elements
								return (newCache[ 2 ] = oldCache[ 2 ]);
							} else {
								// Reuse newcache so results back-propagate to previous elements
								uniqueCache[ dir ] = newCache;
	
								// A match means we're done; a fail means we have to keep checking
								if ( (newCache[ 2 ] = matcher( elem, context, xml )) ) {
									return true;
								}
							}
						}
					}
				}
			};
	}
	
	function elementMatcher( matchers ) {
		return matchers.length > 1 ?
			function( elem, context, xml ) {
				var i = matchers.length;
				while ( i-- ) {
					if ( !matchers[i]( elem, context, xml ) ) {
						return false;
					}
				}
				return true;
			} :
			matchers[0];
	}
	
	function multipleContexts( selector, contexts, results ) {
		var i = 0,
			len = contexts.length;
		for ( ; i < len; i++ ) {
			Sizzle( selector, contexts[i], results );
		}
		return results;
	}
	
	function condense( unmatched, map, filter, context, xml ) {
		var elem,
			newUnmatched = [],
			i = 0,
			len = unmatched.length,
			mapped = map != null;
	
		for ( ; i < len; i++ ) {
			if ( (elem = unmatched[i]) ) {
				if ( !filter || filter( elem, context, xml ) ) {
					newUnmatched.push( elem );
					if ( mapped ) {
						map.push( i );
					}
				}
			}
		}
	
		return newUnmatched;
	}
	
	function setMatcher( preFilter, selector, matcher, postFilter, postFinder, postSelector ) {
		if ( postFilter && !postFilter[ expando ] ) {
			postFilter = setMatcher( postFilter );
		}
		if ( postFinder && !postFinder[ expando ] ) {
			postFinder = setMatcher( postFinder, postSelector );
		}
		return markFunction(function( seed, results, context, xml ) {
			var temp, i, elem,
				preMap = [],
				postMap = [],
				preexisting = results.length,
	
				// Get initial elements from seed or context
				elems = seed || multipleContexts( selector || "*", context.nodeType ? [ context ] : context, [] ),
	
				// Prefilter to get matcher input, preserving a map for seed-results synchronization
				matcherIn = preFilter && ( seed || !selector ) ?
					condense( elems, preMap, preFilter, context, xml ) :
					elems,
	
				matcherOut = matcher ?
					// If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
					postFinder || ( seed ? preFilter : preexisting || postFilter ) ?
	
						// ...intermediate processing is necessary
						[] :
	
						// ...otherwise use results directly
						results :
					matcherIn;
	
			// Find primary matches
			if ( matcher ) {
				matcher( matcherIn, matcherOut, context, xml );
			}
	
			// Apply postFilter
			if ( postFilter ) {
				temp = condense( matcherOut, postMap );
				postFilter( temp, [], context, xml );
	
				// Un-match failing elements by moving them back to matcherIn
				i = temp.length;
				while ( i-- ) {
					if ( (elem = temp[i]) ) {
						matcherOut[ postMap[i] ] = !(matcherIn[ postMap[i] ] = elem);
					}
				}
			}
	
			if ( seed ) {
				if ( postFinder || preFilter ) {
					if ( postFinder ) {
						// Get the final matcherOut by condensing this intermediate into postFinder contexts
						temp = [];
						i = matcherOut.length;
						while ( i-- ) {
							if ( (elem = matcherOut[i]) ) {
								// Restore matcherIn since elem is not yet a final match
								temp.push( (matcherIn[i] = elem) );
							}
						}
						postFinder( null, (matcherOut = []), temp, xml );
					}
	
					// Move matched elements from seed to results to keep them synchronized
					i = matcherOut.length;
					while ( i-- ) {
						if ( (elem = matcherOut[i]) &&
							(temp = postFinder ? indexOf( seed, elem ) : preMap[i]) > -1 ) {
	
							seed[temp] = !(results[temp] = elem);
						}
					}
				}
	
			// Add elements to results, through postFinder if defined
			} else {
				matcherOut = condense(
					matcherOut === results ?
						matcherOut.splice( preexisting, matcherOut.length ) :
						matcherOut
				);
				if ( postFinder ) {
					postFinder( null, results, matcherOut, xml );
				} else {
					push.apply( results, matcherOut );
				}
			}
		});
	}
	
	function matcherFromTokens( tokens ) {
		var checkContext, matcher, j,
			len = tokens.length,
			leadingRelative = Expr.relative[ tokens[0].type ],
			implicitRelative = leadingRelative || Expr.relative[" "],
			i = leadingRelative ? 1 : 0,
	
			// The foundational matcher ensures that elements are reachable from top-level context(s)
			matchContext = addCombinator( function( elem ) {
				return elem === checkContext;
			}, implicitRelative, true ),
			matchAnyContext = addCombinator( function( elem ) {
				return indexOf( checkContext, elem ) > -1;
			}, implicitRelative, true ),
			matchers = [ function( elem, context, xml ) {
				var ret = ( !leadingRelative && ( xml || context !== outermostContext ) ) || (
					(checkContext = context).nodeType ?
						matchContext( elem, context, xml ) :
						matchAnyContext( elem, context, xml ) );
				// Avoid hanging onto element (issue #299)
				checkContext = null;
				return ret;
			} ];
	
		for ( ; i < len; i++ ) {
			if ( (matcher = Expr.relative[ tokens[i].type ]) ) {
				matchers = [ addCombinator(elementMatcher( matchers ), matcher) ];
			} else {
				matcher = Expr.filter[ tokens[i].type ].apply( null, tokens[i].matches );
	
				// Return special upon seeing a positional matcher
				if ( matcher[ expando ] ) {
					// Find the next relative operator (if any) for proper handling
					j = ++i;
					for ( ; j < len; j++ ) {
						if ( Expr.relative[ tokens[j].type ] ) {
							break;
						}
					}
					return setMatcher(
						i > 1 && elementMatcher( matchers ),
						i > 1 && toSelector(
							// If the preceding token was a descendant combinator, insert an implicit any-element `*`
							tokens.slice( 0, i - 1 ).concat({ value: tokens[ i - 2 ].type === " " ? "*" : "" })
						).replace( rtrim, "$1" ),
						matcher,
						i < j && matcherFromTokens( tokens.slice( i, j ) ),
						j < len && matcherFromTokens( (tokens = tokens.slice( j )) ),
						j < len && toSelector( tokens )
					);
				}
				matchers.push( matcher );
			}
		}
	
		return elementMatcher( matchers );
	}
	
	function matcherFromGroupMatchers( elementMatchers, setMatchers ) {
		var bySet = setMatchers.length > 0,
			byElement = elementMatchers.length > 0,
			superMatcher = function( seed, context, xml, results, outermost ) {
				var elem, j, matcher,
					matchedCount = 0,
					i = "0",
					unmatched = seed && [],
					setMatched = [],
					contextBackup = outermostContext,
					// We must always have either seed elements or outermost context
					elems = seed || byElement && Expr.find["TAG"]( "*", outermost ),
					// Use integer dirruns iff this is the outermost matcher
					dirrunsUnique = (dirruns += contextBackup == null ? 1 : Math.random() || 0.1),
					len = elems.length;
	
				if ( outermost ) {
					outermostContext = context === document || context || outermost;
				}
	
				// Add elements passing elementMatchers directly to results
				// Support: IE<9, Safari
				// Tolerate NodeList properties (IE: "length"; Safari: <number>) matching elements by id
				for ( ; i !== len && (elem = elems[i]) != null; i++ ) {
					if ( byElement && elem ) {
						j = 0;
						if ( !context && elem.ownerDocument !== document ) {
							setDocument( elem );
							xml = !documentIsHTML;
						}
						while ( (matcher = elementMatchers[j++]) ) {
							if ( matcher( elem, context || document, xml) ) {
								results.push( elem );
								break;
							}
						}
						if ( outermost ) {
							dirruns = dirrunsUnique;
						}
					}
	
					// Track unmatched elements for set filters
					if ( bySet ) {
						// They will have gone through all possible matchers
						if ( (elem = !matcher && elem) ) {
							matchedCount--;
						}
	
						// Lengthen the array for every element, matched or not
						if ( seed ) {
							unmatched.push( elem );
						}
					}
				}
	
				// `i` is now the count of elements visited above, and adding it to `matchedCount`
				// makes the latter nonnegative.
				matchedCount += i;
	
				// Apply set filters to unmatched elements
				// NOTE: This can be skipped if there are no unmatched elements (i.e., `matchedCount`
				// equals `i`), unless we didn't visit _any_ elements in the above loop because we have
				// no element matchers and no seed.
				// Incrementing an initially-string "0" `i` allows `i` to remain a string only in that
				// case, which will result in a "00" `matchedCount` that differs from `i` but is also
				// numerically zero.
				if ( bySet && i !== matchedCount ) {
					j = 0;
					while ( (matcher = setMatchers[j++]) ) {
						matcher( unmatched, setMatched, context, xml );
					}
	
					if ( seed ) {
						// Reintegrate element matches to eliminate the need for sorting
						if ( matchedCount > 0 ) {
							while ( i-- ) {
								if ( !(unmatched[i] || setMatched[i]) ) {
									setMatched[i] = pop.call( results );
								}
							}
						}
	
						// Discard index placeholder values to get only actual matches
						setMatched = condense( setMatched );
					}
	
					// Add matches to results
					push.apply( results, setMatched );
	
					// Seedless set matches succeeding multiple successful matchers stipulate sorting
					if ( outermost && !seed && setMatched.length > 0 &&
						( matchedCount + setMatchers.length ) > 1 ) {
	
						Sizzle.uniqueSort( results );
					}
				}
	
				// Override manipulation of globals by nested matchers
				if ( outermost ) {
					dirruns = dirrunsUnique;
					outermostContext = contextBackup;
				}
	
				return unmatched;
			};
	
		return bySet ?
			markFunction( superMatcher ) :
			superMatcher;
	}
	
	compile = Sizzle.compile = function( selector, match /* Internal Use Only */ ) {
		var i,
			setMatchers = [],
			elementMatchers = [],
			cached = compilerCache[ selector + " " ];
	
		if ( !cached ) {
			// Generate a function of recursive functions that can be used to check each element
			if ( !match ) {
				match = tokenize( selector );
			}
			i = match.length;
			while ( i-- ) {
				cached = matcherFromTokens( match[i] );
				if ( cached[ expando ] ) {
					setMatchers.push( cached );
				} else {
					elementMatchers.push( cached );
				}
			}
	
			// Cache the compiled function
			cached = compilerCache( selector, matcherFromGroupMatchers( elementMatchers, setMatchers ) );
	
			// Save selector and tokenization
			cached.selector = selector;
		}
		return cached;
	};
	
	/**
	 * A low-level selection function that works with Sizzle's compiled
	 *  selector functions
	 * @param {String|Function} selector A selector or a pre-compiled
	 *  selector function built with Sizzle.compile
	 * @param {Element} context
	 * @param {Array} [results]
	 * @param {Array} [seed] A set of elements to match against
	 */
	select = Sizzle.select = function( selector, context, results, seed ) {
		var i, tokens, token, type, find,
			compiled = typeof selector === "function" && selector,
			match = !seed && tokenize( (selector = compiled.selector || selector) );
	
		results = results || [];
	
		// Try to minimize operations if there is only one selector in the list and no seed
		// (the latter of which guarantees us context)
		if ( match.length === 1 ) {
	
			// Reduce context if the leading compound selector is an ID
			tokens = match[0] = match[0].slice( 0 );
			if ( tokens.length > 2 && (token = tokens[0]).type === "ID" &&
					support.getById && context.nodeType === 9 && documentIsHTML &&
					Expr.relative[ tokens[1].type ] ) {
	
				context = ( Expr.find["ID"]( token.matches[0].replace(runescape, funescape), context ) || [] )[0];
				if ( !context ) {
					return results;
	
				// Precompiled matchers will still verify ancestry, so step up a level
				} else if ( compiled ) {
					context = context.parentNode;
				}
	
				selector = selector.slice( tokens.shift().value.length );
			}
	
			// Fetch a seed set for right-to-left matching
			i = matchExpr["needsContext"].test( selector ) ? 0 : tokens.length;
			while ( i-- ) {
				token = tokens[i];
	
				// Abort if we hit a combinator
				if ( Expr.relative[ (type = token.type) ] ) {
					break;
				}
				if ( (find = Expr.find[ type ]) ) {
					// Search, expanding context for leading sibling combinators
					if ( (seed = find(
						token.matches[0].replace( runescape, funescape ),
						rsibling.test( tokens[0].type ) && testContext( context.parentNode ) || context
					)) ) {
	
						// If seed is empty or no tokens remain, we can return early
						tokens.splice( i, 1 );
						selector = seed.length && toSelector( tokens );
						if ( !selector ) {
							push.apply( results, seed );
							return results;
						}
	
						break;
					}
				}
			}
		}
	
		// Compile and execute a filtering function if one is not provided
		// Provide `match` to avoid retokenization if we modified the selector above
		( compiled || compile( selector, match ) )(
			seed,
			context,
			!documentIsHTML,
			results,
			!context || rsibling.test( selector ) && testContext( context.parentNode ) || context
		);
		return results;
	};
	
	// One-time assignments
	
	// Sort stability
	support.sortStable = expando.split("").sort( sortOrder ).join("") === expando;
	
	// Support: Chrome 14-35+
	// Always assume duplicates if they aren't passed to the comparison function
	support.detectDuplicates = !!hasDuplicate;
	
	// Initialize against the default document
	setDocument();
	
	// Support: Webkit<537.32 - Safari 6.0.3/Chrome 25 (fixed in Chrome 27)
	// Detached nodes confoundingly follow *each other*
	support.sortDetached = assert(function( div1 ) {
		// Should return 1, but returns 4 (following)
		return div1.compareDocumentPosition( document.createElement("div") ) & 1;
	});
	
	// Support: IE<8
	// Prevent attribute/property "interpolation"
	// http://msdn.microsoft.com/en-us/library/ms536429%28VS.85%29.aspx
	if ( !assert(function( div ) {
		div.innerHTML = "<a href='#'></a>";
		return div.firstChild.getAttribute("href") === "#" ;
	}) ) {
		addHandle( "type|href|height|width", function( elem, name, isXML ) {
			if ( !isXML ) {
				return elem.getAttribute( name, name.toLowerCase() === "type" ? 1 : 2 );
			}
		});
	}
	
	// Support: IE<9
	// Use defaultValue in place of getAttribute("value")
	if ( !support.attributes || !assert(function( div ) {
		div.innerHTML = "<input/>";
		div.firstChild.setAttribute( "value", "" );
		return div.firstChild.getAttribute( "value" ) === "";
	}) ) {
		addHandle( "value", function( elem, name, isXML ) {
			if ( !isXML && elem.nodeName.toLowerCase() === "input" ) {
				return elem.defaultValue;
			}
		});
	}
	
	// Support: IE<9
	// Use getAttributeNode to fetch booleans when getAttribute lies
	if ( !assert(function( div ) {
		return div.getAttribute("disabled") == null;
	}) ) {
		addHandle( booleans, function( elem, name, isXML ) {
			var val;
			if ( !isXML ) {
				return elem[ name ] === true ? name.toLowerCase() :
						(val = elem.getAttributeNode( name )) && val.specified ?
						val.value :
					null;
			}
		});
	}
	
	return Sizzle;
	
	})( window );
	
	
	
	jQuery.find = Sizzle;
	jQuery.expr = Sizzle.selectors;
	jQuery.expr[ ":" ] = jQuery.expr.pseudos;
	jQuery.uniqueSort = jQuery.unique = Sizzle.uniqueSort;
	jQuery.text = Sizzle.getText;
	jQuery.isXMLDoc = Sizzle.isXML;
	jQuery.contains = Sizzle.contains;
	
	
	
	var dir = function( elem, dir, until ) {
		var matched = [],
			truncate = until !== undefined;
	
		while ( ( elem = elem[ dir ] ) && elem.nodeType !== 9 ) {
			if ( elem.nodeType === 1 ) {
				if ( truncate && jQuery( elem ).is( until ) ) {
					break;
				}
				matched.push( elem );
			}
		}
		return matched;
	};
	
	
	var siblings = function( n, elem ) {
		var matched = [];
	
		for ( ; n; n = n.nextSibling ) {
			if ( n.nodeType === 1 && n !== elem ) {
				matched.push( n );
			}
		}
	
		return matched;
	};
	
	
	var rneedsContext = jQuery.expr.match.needsContext;
	
	var rsingleTag = ( /^<([\w-]+)\s*\/?>(?:<\/\1>|)$/ );
	
	
	
	var risSimple = /^.[^:#\[\.,]*$/;
	
	// Implement the identical functionality for filter and not
	function winnow( elements, qualifier, not ) {
		if ( jQuery.isFunction( qualifier ) ) {
			return jQuery.grep( elements, function( elem, i ) {
				/* jshint -W018 */
				return !!qualifier.call( elem, i, elem ) !== not;
			} );
	
		}
	
		if ( qualifier.nodeType ) {
			return jQuery.grep( elements, function( elem ) {
				return ( elem === qualifier ) !== not;
			} );
	
		}
	
		if ( typeof qualifier === "string" ) {
			if ( risSimple.test( qualifier ) ) {
				return jQuery.filter( qualifier, elements, not );
			}
	
			qualifier = jQuery.filter( qualifier, elements );
		}
	
		return jQuery.grep( elements, function( elem ) {
			return ( indexOf.call( qualifier, elem ) > -1 ) !== not;
		} );
	}
	
	jQuery.filter = function( expr, elems, not ) {
		var elem = elems[ 0 ];
	
		if ( not ) {
			expr = ":not(" + expr + ")";
		}
	
		return elems.length === 1 && elem.nodeType === 1 ?
			jQuery.find.matchesSelector( elem, expr ) ? [ elem ] : [] :
			jQuery.find.matches( expr, jQuery.grep( elems, function( elem ) {
				return elem.nodeType === 1;
			} ) );
	};
	
	jQuery.fn.extend( {
		find: function( selector ) {
			var i,
				len = this.length,
				ret = [],
				self = this;
	
			if ( typeof selector !== "string" ) {
				return this.pushStack( jQuery( selector ).filter( function() {
					for ( i = 0; i < len; i++ ) {
						if ( jQuery.contains( self[ i ], this ) ) {
							return true;
						}
					}
				} ) );
			}
	
			for ( i = 0; i < len; i++ ) {
				jQuery.find( selector, self[ i ], ret );
			}
	
			// Needed because $( selector, context ) becomes $( context ).find( selector )
			ret = this.pushStack( len > 1 ? jQuery.unique( ret ) : ret );
			ret.selector = this.selector ? this.selector + " " + selector : selector;
			return ret;
		},
		filter: function( selector ) {
			return this.pushStack( winnow( this, selector || [], false ) );
		},
		not: function( selector ) {
			return this.pushStack( winnow( this, selector || [], true ) );
		},
		is: function( selector ) {
			return !!winnow(
				this,
	
				// If this is a positional/relative selector, check membership in the returned set
				// so $("p:first").is("p:last") won't return true for a doc with two "p".
				typeof selector === "string" && rneedsContext.test( selector ) ?
					jQuery( selector ) :
					selector || [],
				false
			).length;
		}
	} );
	
	
	// Initialize a jQuery object
	
	
	// A central reference to the root jQuery(document)
	var rootjQuery,
	
		// A simple way to check for HTML strings
		// Prioritize #id over <tag> to avoid XSS via location.hash (#9521)
		// Strict HTML recognition (#11290: must start with <)
		rquickExpr = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,
	
		init = jQuery.fn.init = function( selector, context, root ) {
			var match, elem;
	
			// HANDLE: $(""), $(null), $(undefined), $(false)
			if ( !selector ) {
				return this;
			}
	
			// Method init() accepts an alternate rootjQuery
			// so migrate can support jQuery.sub (gh-2101)
			root = root || rootjQuery;
	
			// Handle HTML strings
			if ( typeof selector === "string" ) {
				if ( selector[ 0 ] === "<" &&
					selector[ selector.length - 1 ] === ">" &&
					selector.length >= 3 ) {
	
					// Assume that strings that start and end with <> are HTML and skip the regex check
					match = [ null, selector, null ];
	
				} else {
					match = rquickExpr.exec( selector );
				}
	
				// Match html or make sure no context is specified for #id
				if ( match && ( match[ 1 ] || !context ) ) {
	
					// HANDLE: $(html) -> $(array)
					if ( match[ 1 ] ) {
						context = context instanceof jQuery ? context[ 0 ] : context;
	
						// Option to run scripts is true for back-compat
						// Intentionally let the error be thrown if parseHTML is not present
						jQuery.merge( this, jQuery.parseHTML(
							match[ 1 ],
							context && context.nodeType ? context.ownerDocument || context : document,
							true
						) );
	
						// HANDLE: $(html, props)
						if ( rsingleTag.test( match[ 1 ] ) && jQuery.isPlainObject( context ) ) {
							for ( match in context ) {
	
								// Properties of context are called as methods if possible
								if ( jQuery.isFunction( this[ match ] ) ) {
									this[ match ]( context[ match ] );
	
								// ...and otherwise set as attributes
								} else {
									this.attr( match, context[ match ] );
								}
							}
						}
	
						return this;
	
					// HANDLE: $(#id)
					} else {
						elem = document.getElementById( match[ 2 ] );
	
						// Support: Blackberry 4.6
						// gEBID returns nodes no longer in the document (#6963)
						if ( elem && elem.parentNode ) {
	
							// Inject the element directly into the jQuery object
							this.length = 1;
							this[ 0 ] = elem;
						}
	
						this.context = document;
						this.selector = selector;
						return this;
					}
	
				// HANDLE: $(expr, $(...))
				} else if ( !context || context.jquery ) {
					return ( context || root ).find( selector );
	
				// HANDLE: $(expr, context)
				// (which is just equivalent to: $(context).find(expr)
				} else {
					return this.constructor( context ).find( selector );
				}
	
			// HANDLE: $(DOMElement)
			} else if ( selector.nodeType ) {
				this.context = this[ 0 ] = selector;
				this.length = 1;
				return this;
	
			// HANDLE: $(function)
			// Shortcut for document ready
			} else if ( jQuery.isFunction( selector ) ) {
				return root.ready !== undefined ?
					root.ready( selector ) :
	
					// Execute immediately if ready is not present
					selector( jQuery );
			}
	
			if ( selector.selector !== undefined ) {
				this.selector = selector.selector;
				this.context = selector.context;
			}
	
			return jQuery.makeArray( selector, this );
		};
	
	// Give the init function the jQuery prototype for later instantiation
	init.prototype = jQuery.fn;
	
	// Initialize central reference
	rootjQuery = jQuery( document );
	
	
	var rparentsprev = /^(?:parents|prev(?:Until|All))/,
	
		// Methods guaranteed to produce a unique set when starting from a unique set
		guaranteedUnique = {
			children: true,
			contents: true,
			next: true,
			prev: true
		};
	
	jQuery.fn.extend( {
		has: function( target ) {
			var targets = jQuery( target, this ),
				l = targets.length;
	
			return this.filter( function() {
				var i = 0;
				for ( ; i < l; i++ ) {
					if ( jQuery.contains( this, targets[ i ] ) ) {
						return true;
					}
				}
			} );
		},
	
		closest: function( selectors, context ) {
			var cur,
				i = 0,
				l = this.length,
				matched = [],
				pos = rneedsContext.test( selectors ) || typeof selectors !== "string" ?
					jQuery( selectors, context || this.context ) :
					0;
	
			for ( ; i < l; i++ ) {
				for ( cur = this[ i ]; cur && cur !== context; cur = cur.parentNode ) {
	
					// Always skip document fragments
					if ( cur.nodeType < 11 && ( pos ?
						pos.index( cur ) > -1 :
	
						// Don't pass non-elements to Sizzle
						cur.nodeType === 1 &&
							jQuery.find.matchesSelector( cur, selectors ) ) ) {
	
						matched.push( cur );
						break;
					}
				}
			}
	
			return this.pushStack( matched.length > 1 ? jQuery.uniqueSort( matched ) : matched );
		},
	
		// Determine the position of an element within the set
		index: function( elem ) {
	
			// No argument, return index in parent
			if ( !elem ) {
				return ( this[ 0 ] && this[ 0 ].parentNode ) ? this.first().prevAll().length : -1;
			}
	
			// Index in selector
			if ( typeof elem === "string" ) {
				return indexOf.call( jQuery( elem ), this[ 0 ] );
			}
	
			// Locate the position of the desired element
			return indexOf.call( this,
	
				// If it receives a jQuery object, the first element is used
				elem.jquery ? elem[ 0 ] : elem
			);
		},
	
		add: function( selector, context ) {
			return this.pushStack(
				jQuery.uniqueSort(
					jQuery.merge( this.get(), jQuery( selector, context ) )
				)
			);
		},
	
		addBack: function( selector ) {
			return this.add( selector == null ?
				this.prevObject : this.prevObject.filter( selector )
			);
		}
	} );
	
	function sibling( cur, dir ) {
		while ( ( cur = cur[ dir ] ) && cur.nodeType !== 1 ) {}
		return cur;
	}
	
	jQuery.each( {
		parent: function( elem ) {
			var parent = elem.parentNode;
			return parent && parent.nodeType !== 11 ? parent : null;
		},
		parents: function( elem ) {
			return dir( elem, "parentNode" );
		},
		parentsUntil: function( elem, i, until ) {
			return dir( elem, "parentNode", until );
		},
		next: function( elem ) {
			return sibling( elem, "nextSibling" );
		},
		prev: function( elem ) {
			return sibling( elem, "previousSibling" );
		},
		nextAll: function( elem ) {
			return dir( elem, "nextSibling" );
		},
		prevAll: function( elem ) {
			return dir( elem, "previousSibling" );
		},
		nextUntil: function( elem, i, until ) {
			return dir( elem, "nextSibling", until );
		},
		prevUntil: function( elem, i, until ) {
			return dir( elem, "previousSibling", until );
		},
		siblings: function( elem ) {
			return siblings( ( elem.parentNode || {} ).firstChild, elem );
		},
		children: function( elem ) {
			return siblings( elem.firstChild );
		},
		contents: function( elem ) {
			return elem.contentDocument || jQuery.merge( [], elem.childNodes );
		}
	}, function( name, fn ) {
		jQuery.fn[ name ] = function( until, selector ) {
			var matched = jQuery.map( this, fn, until );
	
			if ( name.slice( -5 ) !== "Until" ) {
				selector = until;
			}
	
			if ( selector && typeof selector === "string" ) {
				matched = jQuery.filter( selector, matched );
			}
	
			if ( this.length > 1 ) {
	
				// Remove duplicates
				if ( !guaranteedUnique[ name ] ) {
					jQuery.uniqueSort( matched );
				}
	
				// Reverse order for parents* and prev-derivatives
				if ( rparentsprev.test( name ) ) {
					matched.reverse();
				}
			}
	
			return this.pushStack( matched );
		};
	} );
	var rnotwhite = ( /\S+/g );
	
	
	
	// Convert String-formatted options into Object-formatted ones
	function createOptions( options ) {
		var object = {};
		jQuery.each( options.match( rnotwhite ) || [], function( _, flag ) {
			object[ flag ] = true;
		} );
		return object;
	}
	
	/*
	 * Create a callback list using the following parameters:
	 *
	 *	options: an optional list of space-separated options that will change how
	 *			the callback list behaves or a more traditional option object
	 *
	 * By default a callback list will act like an event callback list and can be
	 * "fired" multiple times.
	 *
	 * Possible options:
	 *
	 *	once:			will ensure the callback list can only be fired once (like a Deferred)
	 *
	 *	memory:			will keep track of previous values and will call any callback added
	 *					after the list has been fired right away with the latest "memorized"
	 *					values (like a Deferred)
	 *
	 *	unique:			will ensure a callback can only be added once (no duplicate in the list)
	 *
	 *	stopOnFalse:	interrupt callings when a callback returns false
	 *
	 */
	jQuery.Callbacks = function( options ) {
	
		// Convert options from String-formatted to Object-formatted if needed
		// (we check in cache first)
		options = typeof options === "string" ?
			createOptions( options ) :
			jQuery.extend( {}, options );
	
		var // Flag to know if list is currently firing
			firing,
	
			// Last fire value for non-forgettable lists
			memory,
	
			// Flag to know if list was already fired
			fired,
	
			// Flag to prevent firing
			locked,
	
			// Actual callback list
			list = [],
	
			// Queue of execution data for repeatable lists
			queue = [],
	
			// Index of currently firing callback (modified by add/remove as needed)
			firingIndex = -1,
	
			// Fire callbacks
			fire = function() {
	
				// Enforce single-firing
				locked = options.once;
	
				// Execute callbacks for all pending executions,
				// respecting firingIndex overrides and runtime changes
				fired = firing = true;
				for ( ; queue.length; firingIndex = -1 ) {
					memory = queue.shift();
					while ( ++firingIndex < list.length ) {
	
						// Run callback and check for early termination
						if ( list[ firingIndex ].apply( memory[ 0 ], memory[ 1 ] ) === false &&
							options.stopOnFalse ) {
	
							// Jump to end and forget the data so .add doesn't re-fire
							firingIndex = list.length;
							memory = false;
						}
					}
				}
	
				// Forget the data if we're done with it
				if ( !options.memory ) {
					memory = false;
				}
	
				firing = false;
	
				// Clean up if we're done firing for good
				if ( locked ) {
	
					// Keep an empty list if we have data for future add calls
					if ( memory ) {
						list = [];
	
					// Otherwise, this object is spent
					} else {
						list = "";
					}
				}
			},
	
			// Actual Callbacks object
			self = {
	
				// Add a callback or a collection of callbacks to the list
				add: function() {
					if ( list ) {
	
						// If we have memory from a past run, we should fire after adding
						if ( memory && !firing ) {
							firingIndex = list.length - 1;
							queue.push( memory );
						}
	
						( function add( args ) {
							jQuery.each( args, function( _, arg ) {
								if ( jQuery.isFunction( arg ) ) {
									if ( !options.unique || !self.has( arg ) ) {
										list.push( arg );
									}
								} else if ( arg && arg.length && jQuery.type( arg ) !== "string" ) {
	
									// Inspect recursively
									add( arg );
								}
							} );
						} )( arguments );
	
						if ( memory && !firing ) {
							fire();
						}
					}
					return this;
				},
	
				// Remove a callback from the list
				remove: function() {
					jQuery.each( arguments, function( _, arg ) {
						var index;
						while ( ( index = jQuery.inArray( arg, list, index ) ) > -1 ) {
							list.splice( index, 1 );
	
							// Handle firing indexes
							if ( index <= firingIndex ) {
								firingIndex--;
							}
						}
					} );
					return this;
				},
	
				// Check if a given callback is in the list.
				// If no argument is given, return whether or not list has callbacks attached.
				has: function( fn ) {
					return fn ?
						jQuery.inArray( fn, list ) > -1 :
						list.length > 0;
				},
	
				// Remove all callbacks from the list
				empty: function() {
					if ( list ) {
						list = [];
					}
					return this;
				},
	
				// Disable .fire and .add
				// Abort any current/pending executions
				// Clear all callbacks and values
				disable: function() {
					locked = queue = [];
					list = memory = "";
					return this;
				},
				disabled: function() {
					return !list;
				},
	
				// Disable .fire
				// Also disable .add unless we have memory (since it would have no effect)
				// Abort any pending executions
				lock: function() {
					locked = queue = [];
					if ( !memory ) {
						list = memory = "";
					}
					return this;
				},
				locked: function() {
					return !!locked;
				},
	
				// Call all callbacks with the given context and arguments
				fireWith: function( context, args ) {
					if ( !locked ) {
						args = args || [];
						args = [ context, args.slice ? args.slice() : args ];
						queue.push( args );
						if ( !firing ) {
							fire();
						}
					}
					return this;
				},
	
				// Call all the callbacks with the given arguments
				fire: function() {
					self.fireWith( this, arguments );
					return this;
				},
	
				// To know if the callbacks have already been called at least once
				fired: function() {
					return !!fired;
				}
			};
	
		return self;
	};
	
	
	jQuery.extend( {
	
		Deferred: function( func ) {
			var tuples = [
	
					// action, add listener, listener list, final state
					[ "resolve", "done", jQuery.Callbacks( "once memory" ), "resolved" ],
					[ "reject", "fail", jQuery.Callbacks( "once memory" ), "rejected" ],
					[ "notify", "progress", jQuery.Callbacks( "memory" ) ]
				],
				state = "pending",
				promise = {
					state: function() {
						return state;
					},
					always: function() {
						deferred.done( arguments ).fail( arguments );
						return this;
					},
					then: function( /* fnDone, fnFail, fnProgress */ ) {
						var fns = arguments;
						return jQuery.Deferred( function( newDefer ) {
							jQuery.each( tuples, function( i, tuple ) {
								var fn = jQuery.isFunction( fns[ i ] ) && fns[ i ];
	
								// deferred[ done | fail | progress ] for forwarding actions to newDefer
								deferred[ tuple[ 1 ] ]( function() {
									var returned = fn && fn.apply( this, arguments );
									if ( returned && jQuery.isFunction( returned.promise ) ) {
										returned.promise()
											.progress( newDefer.notify )
											.done( newDefer.resolve )
											.fail( newDefer.reject );
									} else {
										newDefer[ tuple[ 0 ] + "With" ](
											this === promise ? newDefer.promise() : this,
											fn ? [ returned ] : arguments
										);
									}
								} );
							} );
							fns = null;
						} ).promise();
					},
	
					// Get a promise for this deferred
					// If obj is provided, the promise aspect is added to the object
					promise: function( obj ) {
						return obj != null ? jQuery.extend( obj, promise ) : promise;
					}
				},
				deferred = {};
	
			// Keep pipe for back-compat
			promise.pipe = promise.then;
	
			// Add list-specific methods
			jQuery.each( tuples, function( i, tuple ) {
				var list = tuple[ 2 ],
					stateString = tuple[ 3 ];
	
				// promise[ done | fail | progress ] = list.add
				promise[ tuple[ 1 ] ] = list.add;
	
				// Handle state
				if ( stateString ) {
					list.add( function() {
	
						// state = [ resolved | rejected ]
						state = stateString;
	
					// [ reject_list | resolve_list ].disable; progress_list.lock
					}, tuples[ i ^ 1 ][ 2 ].disable, tuples[ 2 ][ 2 ].lock );
				}
	
				// deferred[ resolve | reject | notify ]
				deferred[ tuple[ 0 ] ] = function() {
					deferred[ tuple[ 0 ] + "With" ]( this === deferred ? promise : this, arguments );
					return this;
				};
				deferred[ tuple[ 0 ] + "With" ] = list.fireWith;
			} );
	
			// Make the deferred a promise
			promise.promise( deferred );
	
			// Call given func if any
			if ( func ) {
				func.call( deferred, deferred );
			}
	
			// All done!
			return deferred;
		},
	
		// Deferred helper
		when: function( subordinate /* , ..., subordinateN */ ) {
			var i = 0,
				resolveValues = slice.call( arguments ),
				length = resolveValues.length,
	
				// the count of uncompleted subordinates
				remaining = length !== 1 ||
					( subordinate && jQuery.isFunction( subordinate.promise ) ) ? length : 0,
	
				// the master Deferred.
				// If resolveValues consist of only a single Deferred, just use that.
				deferred = remaining === 1 ? subordinate : jQuery.Deferred(),
	
				// Update function for both resolve and progress values
				updateFunc = function( i, contexts, values ) {
					return function( value ) {
						contexts[ i ] = this;
						values[ i ] = arguments.length > 1 ? slice.call( arguments ) : value;
						if ( values === progressValues ) {
							deferred.notifyWith( contexts, values );
						} else if ( !( --remaining ) ) {
							deferred.resolveWith( contexts, values );
						}
					};
				},
	
				progressValues, progressContexts, resolveContexts;
	
			// Add listeners to Deferred subordinates; treat others as resolved
			if ( length > 1 ) {
				progressValues = new Array( length );
				progressContexts = new Array( length );
				resolveContexts = new Array( length );
				for ( ; i < length; i++ ) {
					if ( resolveValues[ i ] && jQuery.isFunction( resolveValues[ i ].promise ) ) {
						resolveValues[ i ].promise()
							.progress( updateFunc( i, progressContexts, progressValues ) )
							.done( updateFunc( i, resolveContexts, resolveValues ) )
							.fail( deferred.reject );
					} else {
						--remaining;
					}
				}
			}
	
			// If we're not waiting on anything, resolve the master
			if ( !remaining ) {
				deferred.resolveWith( resolveContexts, resolveValues );
			}
	
			return deferred.promise();
		}
	} );
	
	
	// The deferred used on DOM ready
	var readyList;
	
	jQuery.fn.ready = function( fn ) {
	
		// Add the callback
		jQuery.ready.promise().done( fn );
	
		return this;
	};
	
	jQuery.extend( {
	
		// Is the DOM ready to be used? Set to true once it occurs.
		isReady: false,
	
		// A counter to track how many items to wait for before
		// the ready event fires. See #6781
		readyWait: 1,
	
		// Hold (or release) the ready event
		holdReady: function( hold ) {
			if ( hold ) {
				jQuery.readyWait++;
			} else {
				jQuery.ready( true );
			}
		},
	
		// Handle when the DOM is ready
		ready: function( wait ) {
	
			// Abort if there are pending holds or we're already ready
			if ( wait === true ? --jQuery.readyWait : jQuery.isReady ) {
				return;
			}
	
			// Remember that the DOM is ready
			jQuery.isReady = true;
	
			// If a normal DOM Ready event fired, decrement, and wait if need be
			if ( wait !== true && --jQuery.readyWait > 0 ) {
				return;
			}
	
			// If there are functions bound, to execute
			readyList.resolveWith( document, [ jQuery ] );
	
			// Trigger any bound ready events
			if ( jQuery.fn.triggerHandler ) {
				jQuery( document ).triggerHandler( "ready" );
				jQuery( document ).off( "ready" );
			}
		}
	} );
	
	/**
	 * The ready event handler and self cleanup method
	 */
	function completed() {
		document.removeEventListener( "DOMContentLoaded", completed );
		window.removeEventListener( "load", completed );
		jQuery.ready();
	}
	
	jQuery.ready.promise = function( obj ) {
		if ( !readyList ) {
	
			readyList = jQuery.Deferred();
	
			// Catch cases where $(document).ready() is called
			// after the browser event has already occurred.
			// Support: IE9-10 only
			// Older IE sometimes signals "interactive" too soon
			if ( document.readyState === "complete" ||
				( document.readyState !== "loading" && !document.documentElement.doScroll ) ) {
	
				// Handle it asynchronously to allow scripts the opportunity to delay ready
				window.setTimeout( jQuery.ready );
	
			} else {
	
				// Use the handy event callback
				document.addEventListener( "DOMContentLoaded", completed );
	
				// A fallback to window.onload, that will always work
				window.addEventListener( "load", completed );
			}
		}
		return readyList.promise( obj );
	};
	
	// Kick off the DOM ready check even if the user does not
	jQuery.ready.promise();
	
	
	
	
	// Multifunctional method to get and set values of a collection
	// The value/s can optionally be executed if it's a function
	var access = function( elems, fn, key, value, chainable, emptyGet, raw ) {
		var i = 0,
			len = elems.length,
			bulk = key == null;
	
		// Sets many values
		if ( jQuery.type( key ) === "object" ) {
			chainable = true;
			for ( i in key ) {
				access( elems, fn, i, key[ i ], true, emptyGet, raw );
			}
	
		// Sets one value
		} else if ( value !== undefined ) {
			chainable = true;
	
			if ( !jQuery.isFunction( value ) ) {
				raw = true;
			}
	
			if ( bulk ) {
	
				// Bulk operations run against the entire set
				if ( raw ) {
					fn.call( elems, value );
					fn = null;
	
				// ...except when executing function values
				} else {
					bulk = fn;
					fn = function( elem, key, value ) {
						return bulk.call( jQuery( elem ), value );
					};
				}
			}
	
			if ( fn ) {
				for ( ; i < len; i++ ) {
					fn(
						elems[ i ], key, raw ?
						value :
						value.call( elems[ i ], i, fn( elems[ i ], key ) )
					);
				}
			}
		}
	
		return chainable ?
			elems :
	
			// Gets
			bulk ?
				fn.call( elems ) :
				len ? fn( elems[ 0 ], key ) : emptyGet;
	};
	var acceptData = function( owner ) {
	
		// Accepts only:
		//  - Node
		//    - Node.ELEMENT_NODE
		//    - Node.DOCUMENT_NODE
		//  - Object
		//    - Any
		/* jshint -W018 */
		return owner.nodeType === 1 || owner.nodeType === 9 || !( +owner.nodeType );
	};
	
	
	
	
	function Data() {
		this.expando = jQuery.expando + Data.uid++;
	}
	
	Data.uid = 1;
	
	Data.prototype = {
	
		register: function( owner, initial ) {
			var value = initial || {};
	
			// If it is a node unlikely to be stringify-ed or looped over
			// use plain assignment
			if ( owner.nodeType ) {
				owner[ this.expando ] = value;
	
			// Otherwise secure it in a non-enumerable, non-writable property
			// configurability must be true to allow the property to be
			// deleted with the delete operator
			} else {
				Object.defineProperty( owner, this.expando, {
					value: value,
					writable: true,
					configurable: true
				} );
			}
			return owner[ this.expando ];
		},
		cache: function( owner ) {
	
			// We can accept data for non-element nodes in modern browsers,
			// but we should not, see #8335.
			// Always return an empty object.
			if ( !acceptData( owner ) ) {
				return {};
			}
	
			// Check if the owner object already has a cache
			var value = owner[ this.expando ];
	
			// If not, create one
			if ( !value ) {
				value = {};
	
				// We can accept data for non-element nodes in modern browsers,
				// but we should not, see #8335.
				// Always return an empty object.
				if ( acceptData( owner ) ) {
	
					// If it is a node unlikely to be stringify-ed or looped over
					// use plain assignment
					if ( owner.nodeType ) {
						owner[ this.expando ] = value;
	
					// Otherwise secure it in a non-enumerable property
					// configurable must be true to allow the property to be
					// deleted when data is removed
					} else {
						Object.defineProperty( owner, this.expando, {
							value: value,
							configurable: true
						} );
					}
				}
			}
	
			return value;
		},
		set: function( owner, data, value ) {
			var prop,
				cache = this.cache( owner );
	
			// Handle: [ owner, key, value ] args
			if ( typeof data === "string" ) {
				cache[ data ] = value;
	
			// Handle: [ owner, { properties } ] args
			} else {
	
				// Copy the properties one-by-one to the cache object
				for ( prop in data ) {
					cache[ prop ] = data[ prop ];
				}
			}
			return cache;
		},
		get: function( owner, key ) {
			return key === undefined ?
				this.cache( owner ) :
				owner[ this.expando ] && owner[ this.expando ][ key ];
		},
		access: function( owner, key, value ) {
			var stored;
	
			// In cases where either:
			//
			//   1. No key was specified
			//   2. A string key was specified, but no value provided
			//
			// Take the "read" path and allow the get method to determine
			// which value to return, respectively either:
			//
			//   1. The entire cache object
			//   2. The data stored at the key
			//
			if ( key === undefined ||
					( ( key && typeof key === "string" ) && value === undefined ) ) {
	
				stored = this.get( owner, key );
	
				return stored !== undefined ?
					stored : this.get( owner, jQuery.camelCase( key ) );
			}
	
			// When the key is not a string, or both a key and value
			// are specified, set or extend (existing objects) with either:
			//
			//   1. An object of properties
			//   2. A key and value
			//
			this.set( owner, key, value );
	
			// Since the "set" path can have two possible entry points
			// return the expected data based on which path was taken[*]
			return value !== undefined ? value : key;
		},
		remove: function( owner, key ) {
			var i, name, camel,
				cache = owner[ this.expando ];
	
			if ( cache === undefined ) {
				return;
			}
	
			if ( key === undefined ) {
				this.register( owner );
	
			} else {
	
				// Support array or space separated string of keys
				if ( jQuery.isArray( key ) ) {
	
					// If "name" is an array of keys...
					// When data is initially created, via ("key", "val") signature,
					// keys will be converted to camelCase.
					// Since there is no way to tell _how_ a key was added, remove
					// both plain key and camelCase key. #12786
					// This will only penalize the array argument path.
					name = key.concat( key.map( jQuery.camelCase ) );
				} else {
					camel = jQuery.camelCase( key );
	
					// Try the string as a key before any manipulation
					if ( key in cache ) {
						name = [ key, camel ];
					} else {
	
						// If a key with the spaces exists, use it.
						// Otherwise, create an array by matching non-whitespace
						name = camel;
						name = name in cache ?
							[ name ] : ( name.match( rnotwhite ) || [] );
					}
				}
	
				i = name.length;
	
				while ( i-- ) {
					delete cache[ name[ i ] ];
				}
			}
	
			// Remove the expando if there's no more data
			if ( key === undefined || jQuery.isEmptyObject( cache ) ) {
	
				// Support: Chrome <= 35-45+
				// Webkit & Blink performance suffers when deleting properties
				// from DOM nodes, so set to undefined instead
				// https://code.google.com/p/chromium/issues/detail?id=378607
				if ( owner.nodeType ) {
					owner[ this.expando ] = undefined;
				} else {
					delete owner[ this.expando ];
				}
			}
		},
		hasData: function( owner ) {
			var cache = owner[ this.expando ];
			return cache !== undefined && !jQuery.isEmptyObject( cache );
		}
	};
	var dataPriv = new Data();
	
	var dataUser = new Data();
	
	
	
	//	Implementation Summary
	//
	//	1. Enforce API surface and semantic compatibility with 1.9.x branch
	//	2. Improve the module's maintainability by reducing the storage
	//		paths to a single mechanism.
	//	3. Use the same single mechanism to support "private" and "user" data.
	//	4. _Never_ expose "private" data to user code (TODO: Drop _data, _removeData)
	//	5. Avoid exposing implementation details on user objects (eg. expando properties)
	//	6. Provide a clear path for implementation upgrade to WeakMap in 2014
	
	var rbrace = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
		rmultiDash = /[A-Z]/g;
	
	function dataAttr( elem, key, data ) {
		var name;
	
		// If nothing was found internally, try to fetch any
		// data from the HTML5 data-* attribute
		if ( data === undefined && elem.nodeType === 1 ) {
			name = "data-" + key.replace( rmultiDash, "-$&" ).toLowerCase();
			data = elem.getAttribute( name );
	
			if ( typeof data === "string" ) {
				try {
					data = data === "true" ? true :
						data === "false" ? false :
						data === "null" ? null :
	
						// Only convert to a number if it doesn't change the string
						+data + "" === data ? +data :
						rbrace.test( data ) ? jQuery.parseJSON( data ) :
						data;
				} catch ( e ) {}
	
				// Make sure we set the data so it isn't changed later
				dataUser.set( elem, key, data );
			} else {
				data = undefined;
			}
		}
		return data;
	}
	
	jQuery.extend( {
		hasData: function( elem ) {
			return dataUser.hasData( elem ) || dataPriv.hasData( elem );
		},
	
		data: function( elem, name, data ) {
			return dataUser.access( elem, name, data );
		},
	
		removeData: function( elem, name ) {
			dataUser.remove( elem, name );
		},
	
		// TODO: Now that all calls to _data and _removeData have been replaced
		// with direct calls to dataPriv methods, these can be deprecated.
		_data: function( elem, name, data ) {
			return dataPriv.access( elem, name, data );
		},
	
		_removeData: function( elem, name ) {
			dataPriv.remove( elem, name );
		}
	} );
	
	jQuery.fn.extend( {
		data: function( key, value ) {
			var i, name, data,
				elem = this[ 0 ],
				attrs = elem && elem.attributes;
	
			// Gets all values
			if ( key === undefined ) {
				if ( this.length ) {
					data = dataUser.get( elem );
	
					if ( elem.nodeType === 1 && !dataPriv.get( elem, "hasDataAttrs" ) ) {
						i = attrs.length;
						while ( i-- ) {
	
							// Support: IE11+
							// The attrs elements can be null (#14894)
							if ( attrs[ i ] ) {
								name = attrs[ i ].name;
								if ( name.indexOf( "data-" ) === 0 ) {
									name = jQuery.camelCase( name.slice( 5 ) );
									dataAttr( elem, name, data[ name ] );
								}
							}
						}
						dataPriv.set( elem, "hasDataAttrs", true );
					}
				}
	
				return data;
			}
	
			// Sets multiple values
			if ( typeof key === "object" ) {
				return this.each( function() {
					dataUser.set( this, key );
				} );
			}
	
			return access( this, function( value ) {
				var data, camelKey;
	
				// The calling jQuery object (element matches) is not empty
				// (and therefore has an element appears at this[ 0 ]) and the
				// `value` parameter was not undefined. An empty jQuery object
				// will result in `undefined` for elem = this[ 0 ] which will
				// throw an exception if an attempt to read a data cache is made.
				if ( elem && value === undefined ) {
	
					// Attempt to get data from the cache
					// with the key as-is
					data = dataUser.get( elem, key ) ||
	
						// Try to find dashed key if it exists (gh-2779)
						// This is for 2.2.x only
						dataUser.get( elem, key.replace( rmultiDash, "-$&" ).toLowerCase() );
	
					if ( data !== undefined ) {
						return data;
					}
	
					camelKey = jQuery.camelCase( key );
	
					// Attempt to get data from the cache
					// with the key camelized
					data = dataUser.get( elem, camelKey );
					if ( data !== undefined ) {
						return data;
					}
	
					// Attempt to "discover" the data in
					// HTML5 custom data-* attrs
					data = dataAttr( elem, camelKey, undefined );
					if ( data !== undefined ) {
						return data;
					}
	
					// We tried really hard, but the data doesn't exist.
					return;
				}
	
				// Set the data...
				camelKey = jQuery.camelCase( key );
				this.each( function() {
	
					// First, attempt to store a copy or reference of any
					// data that might've been store with a camelCased key.
					var data = dataUser.get( this, camelKey );
	
					// For HTML5 data-* attribute interop, we have to
					// store property names with dashes in a camelCase form.
					// This might not apply to all properties...*
					dataUser.set( this, camelKey, value );
	
					// *... In the case of properties that might _actually_
					// have dashes, we need to also store a copy of that
					// unchanged property.
					if ( key.indexOf( "-" ) > -1 && data !== undefined ) {
						dataUser.set( this, key, value );
					}
				} );
			}, null, value, arguments.length > 1, null, true );
		},
	
		removeData: function( key ) {
			return this.each( function() {
				dataUser.remove( this, key );
			} );
		}
	} );
	
	
	jQuery.extend( {
		queue: function( elem, type, data ) {
			var queue;
	
			if ( elem ) {
				type = ( type || "fx" ) + "queue";
				queue = dataPriv.get( elem, type );
	
				// Speed up dequeue by getting out quickly if this is just a lookup
				if ( data ) {
					if ( !queue || jQuery.isArray( data ) ) {
						queue = dataPriv.access( elem, type, jQuery.makeArray( data ) );
					} else {
						queue.push( data );
					}
				}
				return queue || [];
			}
		},
	
		dequeue: function( elem, type ) {
			type = type || "fx";
	
			var queue = jQuery.queue( elem, type ),
				startLength = queue.length,
				fn = queue.shift(),
				hooks = jQuery._queueHooks( elem, type ),
				next = function() {
					jQuery.dequeue( elem, type );
				};
	
			// If the fx queue is dequeued, always remove the progress sentinel
			if ( fn === "inprogress" ) {
				fn = queue.shift();
				startLength--;
			}
	
			if ( fn ) {
	
				// Add a progress sentinel to prevent the fx queue from being
				// automatically dequeued
				if ( type === "fx" ) {
					queue.unshift( "inprogress" );
				}
	
				// Clear up the last queue stop function
				delete hooks.stop;
				fn.call( elem, next, hooks );
			}
	
			if ( !startLength && hooks ) {
				hooks.empty.fire();
			}
		},
	
		// Not public - generate a queueHooks object, or return the current one
		_queueHooks: function( elem, type ) {
			var key = type + "queueHooks";
			return dataPriv.get( elem, key ) || dataPriv.access( elem, key, {
				empty: jQuery.Callbacks( "once memory" ).add( function() {
					dataPriv.remove( elem, [ type + "queue", key ] );
				} )
			} );
		}
	} );
	
	jQuery.fn.extend( {
		queue: function( type, data ) {
			var setter = 2;
	
			if ( typeof type !== "string" ) {
				data = type;
				type = "fx";
				setter--;
			}
	
			if ( arguments.length < setter ) {
				return jQuery.queue( this[ 0 ], type );
			}
	
			return data === undefined ?
				this :
				this.each( function() {
					var queue = jQuery.queue( this, type, data );
	
					// Ensure a hooks for this queue
					jQuery._queueHooks( this, type );
	
					if ( type === "fx" && queue[ 0 ] !== "inprogress" ) {
						jQuery.dequeue( this, type );
					}
				} );
		},
		dequeue: function( type ) {
			return this.each( function() {
				jQuery.dequeue( this, type );
			} );
		},
		clearQueue: function( type ) {
			return this.queue( type || "fx", [] );
		},
	
		// Get a promise resolved when queues of a certain type
		// are emptied (fx is the type by default)
		promise: function( type, obj ) {
			var tmp,
				count = 1,
				defer = jQuery.Deferred(),
				elements = this,
				i = this.length,
				resolve = function() {
					if ( !( --count ) ) {
						defer.resolveWith( elements, [ elements ] );
					}
				};
	
			if ( typeof type !== "string" ) {
				obj = type;
				type = undefined;
			}
			type = type || "fx";
	
			while ( i-- ) {
				tmp = dataPriv.get( elements[ i ], type + "queueHooks" );
				if ( tmp && tmp.empty ) {
					count++;
					tmp.empty.add( resolve );
				}
			}
			resolve();
			return defer.promise( obj );
		}
	} );
	var pnum = ( /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/ ).source;
	
	var rcssNum = new RegExp( "^(?:([+-])=|)(" + pnum + ")([a-z%]*)$", "i" );
	
	
	var cssExpand = [ "Top", "Right", "Bottom", "Left" ];
	
	var isHidden = function( elem, el ) {
	
			// isHidden might be called from jQuery#filter function;
			// in that case, element will be second argument
			elem = el || elem;
			return jQuery.css( elem, "display" ) === "none" ||
				!jQuery.contains( elem.ownerDocument, elem );
		};
	
	
	
	function adjustCSS( elem, prop, valueParts, tween ) {
		var adjusted,
			scale = 1,
			maxIterations = 20,
			currentValue = tween ?
				function() { return tween.cur(); } :
				function() { return jQuery.css( elem, prop, "" ); },
			initial = currentValue(),
			unit = valueParts && valueParts[ 3 ] || ( jQuery.cssNumber[ prop ] ? "" : "px" ),
	
			// Starting value computation is required for potential unit mismatches
			initialInUnit = ( jQuery.cssNumber[ prop ] || unit !== "px" && +initial ) &&
				rcssNum.exec( jQuery.css( elem, prop ) );
	
		if ( initialInUnit && initialInUnit[ 3 ] !== unit ) {
	
			// Trust units reported by jQuery.css
			unit = unit || initialInUnit[ 3 ];
	
			// Make sure we update the tween properties later on
			valueParts = valueParts || [];
	
			// Iteratively approximate from a nonzero starting point
			initialInUnit = +initial || 1;
	
			do {
	
				// If previous iteration zeroed out, double until we get *something*.
				// Use string for doubling so we don't accidentally see scale as unchanged below
				scale = scale || ".5";
	
				// Adjust and apply
				initialInUnit = initialInUnit / scale;
				jQuery.style( elem, prop, initialInUnit + unit );
	
			// Update scale, tolerating zero or NaN from tween.cur()
			// Break the loop if scale is unchanged or perfect, or if we've just had enough.
			} while (
				scale !== ( scale = currentValue() / initial ) && scale !== 1 && --maxIterations
			);
		}
	
		if ( valueParts ) {
			initialInUnit = +initialInUnit || +initial || 0;
	
			// Apply relative offset (+=/-=) if specified
			adjusted = valueParts[ 1 ] ?
				initialInUnit + ( valueParts[ 1 ] + 1 ) * valueParts[ 2 ] :
				+valueParts[ 2 ];
			if ( tween ) {
				tween.unit = unit;
				tween.start = initialInUnit;
				tween.end = adjusted;
			}
		}
		return adjusted;
	}
	var rcheckableType = ( /^(?:checkbox|radio)$/i );
	
	var rtagName = ( /<([\w:-]+)/ );
	
	var rscriptType = ( /^$|\/(?:java|ecma)script/i );
	
	
	
	// We have to close these tags to support XHTML (#13200)
	var wrapMap = {
	
		// Support: IE9
		option: [ 1, "<select multiple='multiple'>", "</select>" ],
	
		// XHTML parsers do not magically insert elements in the
		// same way that tag soup parsers do. So we cannot shorten
		// this by omitting <tbody> or other required elements.
		thead: [ 1, "<table>", "</table>" ],
		col: [ 2, "<table><colgroup>", "</colgroup></table>" ],
		tr: [ 2, "<table><tbody>", "</tbody></table>" ],
		td: [ 3, "<table><tbody><tr>", "</tr></tbody></table>" ],
	
		_default: [ 0, "", "" ]
	};
	
	// Support: IE9
	wrapMap.optgroup = wrapMap.option;
	
	wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
	wrapMap.th = wrapMap.td;
	
	
	function getAll( context, tag ) {
	
		// Support: IE9-11+
		// Use typeof to avoid zero-argument method invocation on host objects (#15151)
		var ret = typeof context.getElementsByTagName !== "undefined" ?
				context.getElementsByTagName( tag || "*" ) :
				typeof context.querySelectorAll !== "undefined" ?
					context.querySelectorAll( tag || "*" ) :
				[];
	
		return tag === undefined || tag && jQuery.nodeName( context, tag ) ?
			jQuery.merge( [ context ], ret ) :
			ret;
	}
	
	
	// Mark scripts as having already been evaluated
	function setGlobalEval( elems, refElements ) {
		var i = 0,
			l = elems.length;
	
		for ( ; i < l; i++ ) {
			dataPriv.set(
				elems[ i ],
				"globalEval",
				!refElements || dataPriv.get( refElements[ i ], "globalEval" )
			);
		}
	}
	
	
	var rhtml = /<|&#?\w+;/;
	
	function buildFragment( elems, context, scripts, selection, ignored ) {
		var elem, tmp, tag, wrap, contains, j,
			fragment = context.createDocumentFragment(),
			nodes = [],
			i = 0,
			l = elems.length;
	
		for ( ; i < l; i++ ) {
			elem = elems[ i ];
	
			if ( elem || elem === 0 ) {
	
				// Add nodes directly
				if ( jQuery.type( elem ) === "object" ) {
	
					// Support: Android<4.1, PhantomJS<2
					// push.apply(_, arraylike) throws on ancient WebKit
					jQuery.merge( nodes, elem.nodeType ? [ elem ] : elem );
	
				// Convert non-html into a text node
				} else if ( !rhtml.test( elem ) ) {
					nodes.push( context.createTextNode( elem ) );
	
				// Convert html into DOM nodes
				} else {
					tmp = tmp || fragment.appendChild( context.createElement( "div" ) );
	
					// Deserialize a standard representation
					tag = ( rtagName.exec( elem ) || [ "", "" ] )[ 1 ].toLowerCase();
					wrap = wrapMap[ tag ] || wrapMap._default;
					tmp.innerHTML = wrap[ 1 ] + jQuery.htmlPrefilter( elem ) + wrap[ 2 ];
	
					// Descend through wrappers to the right content
					j = wrap[ 0 ];
					while ( j-- ) {
						tmp = tmp.lastChild;
					}
	
					// Support: Android<4.1, PhantomJS<2
					// push.apply(_, arraylike) throws on ancient WebKit
					jQuery.merge( nodes, tmp.childNodes );
	
					// Remember the top-level container
					tmp = fragment.firstChild;
	
					// Ensure the created nodes are orphaned (#12392)
					tmp.textContent = "";
				}
			}
		}
	
		// Remove wrapper from fragment
		fragment.textContent = "";
	
		i = 0;
		while ( ( elem = nodes[ i++ ] ) ) {
	
			// Skip elements already in the context collection (trac-4087)
			if ( selection && jQuery.inArray( elem, selection ) > -1 ) {
				if ( ignored ) {
					ignored.push( elem );
				}
				continue;
			}
	
			contains = jQuery.contains( elem.ownerDocument, elem );
	
			// Append to fragment
			tmp = getAll( fragment.appendChild( elem ), "script" );
	
			// Preserve script evaluation history
			if ( contains ) {
				setGlobalEval( tmp );
			}
	
			// Capture executables
			if ( scripts ) {
				j = 0;
				while ( ( elem = tmp[ j++ ] ) ) {
					if ( rscriptType.test( elem.type || "" ) ) {
						scripts.push( elem );
					}
				}
			}
		}
	
		return fragment;
	}
	
	
	( function() {
		var fragment = document.createDocumentFragment(),
			div = fragment.appendChild( document.createElement( "div" ) ),
			input = document.createElement( "input" );
	
		// Support: Android 4.0-4.3, Safari<=5.1
		// Check state lost if the name is set (#11217)
		// Support: Windows Web Apps (WWA)
		// `name` and `type` must use .setAttribute for WWA (#14901)
		input.setAttribute( "type", "radio" );
		input.setAttribute( "checked", "checked" );
		input.setAttribute( "name", "t" );
	
		div.appendChild( input );
	
		// Support: Safari<=5.1, Android<4.2
		// Older WebKit doesn't clone checked state correctly in fragments
		support.checkClone = div.cloneNode( true ).cloneNode( true ).lastChild.checked;
	
		// Support: IE<=11+
		// Make sure textarea (and checkbox) defaultValue is properly cloned
		div.innerHTML = "<textarea>x</textarea>";
		support.noCloneChecked = !!div.cloneNode( true ).lastChild.defaultValue;
	} )();
	
	
	var
		rkeyEvent = /^key/,
		rmouseEvent = /^(?:mouse|pointer|contextmenu|drag|drop)|click/,
		rtypenamespace = /^([^.]*)(?:\.(.+)|)/;
	
	function returnTrue() {
		return true;
	}
	
	function returnFalse() {
		return false;
	}
	
	// Support: IE9
	// See #13393 for more info
	function safeActiveElement() {
		try {
			return document.activeElement;
		} catch ( err ) { }
	}
	
	function on( elem, types, selector, data, fn, one ) {
		var origFn, type;
	
		// Types can be a map of types/handlers
		if ( typeof types === "object" ) {
	
			// ( types-Object, selector, data )
			if ( typeof selector !== "string" ) {
	
				// ( types-Object, data )
				data = data || selector;
				selector = undefined;
			}
			for ( type in types ) {
				on( elem, type, selector, data, types[ type ], one );
			}
			return elem;
		}
	
		if ( data == null && fn == null ) {
	
			// ( types, fn )
			fn = selector;
			data = selector = undefined;
		} else if ( fn == null ) {
			if ( typeof selector === "string" ) {
	
				// ( types, selector, fn )
				fn = data;
				data = undefined;
			} else {
	
				// ( types, data, fn )
				fn = data;
				data = selector;
				selector = undefined;
			}
		}
		if ( fn === false ) {
			fn = returnFalse;
		} else if ( !fn ) {
			return elem;
		}
	
		if ( one === 1 ) {
			origFn = fn;
			fn = function( event ) {
	
				// Can use an empty set, since event contains the info
				jQuery().off( event );
				return origFn.apply( this, arguments );
			};
	
			// Use same guid so caller can remove using origFn
			fn.guid = origFn.guid || ( origFn.guid = jQuery.guid++ );
		}
		return elem.each( function() {
			jQuery.event.add( this, types, fn, data, selector );
		} );
	}
	
	/*
	 * Helper functions for managing events -- not part of the public interface.
	 * Props to Dean Edwards' addEvent library for many of the ideas.
	 */
	jQuery.event = {
	
		global: {},
	
		add: function( elem, types, handler, data, selector ) {
	
			var handleObjIn, eventHandle, tmp,
				events, t, handleObj,
				special, handlers, type, namespaces, origType,
				elemData = dataPriv.get( elem );
	
			// Don't attach events to noData or text/comment nodes (but allow plain objects)
			if ( !elemData ) {
				return;
			}
	
			// Caller can pass in an object of custom data in lieu of the handler
			if ( handler.handler ) {
				handleObjIn = handler;
				handler = handleObjIn.handler;
				selector = handleObjIn.selector;
			}
	
			// Make sure that the handler has a unique ID, used to find/remove it later
			if ( !handler.guid ) {
				handler.guid = jQuery.guid++;
			}
	
			// Init the element's event structure and main handler, if this is the first
			if ( !( events = elemData.events ) ) {
				events = elemData.events = {};
			}
			if ( !( eventHandle = elemData.handle ) ) {
				eventHandle = elemData.handle = function( e ) {
	
					// Discard the second event of a jQuery.event.trigger() and
					// when an event is called after a page has unloaded
					return typeof jQuery !== "undefined" && jQuery.event.triggered !== e.type ?
						jQuery.event.dispatch.apply( elem, arguments ) : undefined;
				};
			}
	
			// Handle multiple events separated by a space
			types = ( types || "" ).match( rnotwhite ) || [ "" ];
			t = types.length;
			while ( t-- ) {
				tmp = rtypenamespace.exec( types[ t ] ) || [];
				type = origType = tmp[ 1 ];
				namespaces = ( tmp[ 2 ] || "" ).split( "." ).sort();
	
				// There *must* be a type, no attaching namespace-only handlers
				if ( !type ) {
					continue;
				}
	
				// If event changes its type, use the special event handlers for the changed type
				special = jQuery.event.special[ type ] || {};
	
				// If selector defined, determine special event api type, otherwise given type
				type = ( selector ? special.delegateType : special.bindType ) || type;
	
				// Update special based on newly reset type
				special = jQuery.event.special[ type ] || {};
	
				// handleObj is passed to all event handlers
				handleObj = jQuery.extend( {
					type: type,
					origType: origType,
					data: data,
					handler: handler,
					guid: handler.guid,
					selector: selector,
					needsContext: selector && jQuery.expr.match.needsContext.test( selector ),
					namespace: namespaces.join( "." )
				}, handleObjIn );
	
				// Init the event handler queue if we're the first
				if ( !( handlers = events[ type ] ) ) {
					handlers = events[ type ] = [];
					handlers.delegateCount = 0;
	
					// Only use addEventListener if the special events handler returns false
					if ( !special.setup ||
						special.setup.call( elem, data, namespaces, eventHandle ) === false ) {
	
						if ( elem.addEventListener ) {
							elem.addEventListener( type, eventHandle );
						}
					}
				}
	
				if ( special.add ) {
					special.add.call( elem, handleObj );
	
					if ( !handleObj.handler.guid ) {
						handleObj.handler.guid = handler.guid;
					}
				}
	
				// Add to the element's handler list, delegates in front
				if ( selector ) {
					handlers.splice( handlers.delegateCount++, 0, handleObj );
				} else {
					handlers.push( handleObj );
				}
	
				// Keep track of which events have ever been used, for event optimization
				jQuery.event.global[ type ] = true;
			}
	
		},
	
		// Detach an event or set of events from an element
		remove: function( elem, types, handler, selector, mappedTypes ) {
	
			var j, origCount, tmp,
				events, t, handleObj,
				special, handlers, type, namespaces, origType,
				elemData = dataPriv.hasData( elem ) && dataPriv.get( elem );
	
			if ( !elemData || !( events = elemData.events ) ) {
				return;
			}
	
			// Once for each type.namespace in types; type may be omitted
			types = ( types || "" ).match( rnotwhite ) || [ "" ];
			t = types.length;
			while ( t-- ) {
				tmp = rtypenamespace.exec( types[ t ] ) || [];
				type = origType = tmp[ 1 ];
				namespaces = ( tmp[ 2 ] || "" ).split( "." ).sort();
	
				// Unbind all events (on this namespace, if provided) for the element
				if ( !type ) {
					for ( type in events ) {
						jQuery.event.remove( elem, type + types[ t ], handler, selector, true );
					}
					continue;
				}
	
				special = jQuery.event.special[ type ] || {};
				type = ( selector ? special.delegateType : special.bindType ) || type;
				handlers = events[ type ] || [];
				tmp = tmp[ 2 ] &&
					new RegExp( "(^|\\.)" + namespaces.join( "\\.(?:.*\\.|)" ) + "(\\.|$)" );
	
				// Remove matching events
				origCount = j = handlers.length;
				while ( j-- ) {
					handleObj = handlers[ j ];
	
					if ( ( mappedTypes || origType === handleObj.origType ) &&
						( !handler || handler.guid === handleObj.guid ) &&
						( !tmp || tmp.test( handleObj.namespace ) ) &&
						( !selector || selector === handleObj.selector ||
							selector === "**" && handleObj.selector ) ) {
						handlers.splice( j, 1 );
	
						if ( handleObj.selector ) {
							handlers.delegateCount--;
						}
						if ( special.remove ) {
							special.remove.call( elem, handleObj );
						}
					}
				}
	
				// Remove generic event handler if we removed something and no more handlers exist
				// (avoids potential for endless recursion during removal of special event handlers)
				if ( origCount && !handlers.length ) {
					if ( !special.teardown ||
						special.teardown.call( elem, namespaces, elemData.handle ) === false ) {
	
						jQuery.removeEvent( elem, type, elemData.handle );
					}
	
					delete events[ type ];
				}
			}
	
			// Remove data and the expando if it's no longer used
			if ( jQuery.isEmptyObject( events ) ) {
				dataPriv.remove( elem, "handle events" );
			}
		},
	
		dispatch: function( event ) {
	
			// Make a writable jQuery.Event from the native event object
			event = jQuery.event.fix( event );
	
			var i, j, ret, matched, handleObj,
				handlerQueue = [],
				args = slice.call( arguments ),
				handlers = ( dataPriv.get( this, "events" ) || {} )[ event.type ] || [],
				special = jQuery.event.special[ event.type ] || {};
	
			// Use the fix-ed jQuery.Event rather than the (read-only) native event
			args[ 0 ] = event;
			event.delegateTarget = this;
	
			// Call the preDispatch hook for the mapped type, and let it bail if desired
			if ( special.preDispatch && special.preDispatch.call( this, event ) === false ) {
				return;
			}
	
			// Determine handlers
			handlerQueue = jQuery.event.handlers.call( this, event, handlers );
	
			// Run delegates first; they may want to stop propagation beneath us
			i = 0;
			while ( ( matched = handlerQueue[ i++ ] ) && !event.isPropagationStopped() ) {
				event.currentTarget = matched.elem;
	
				j = 0;
				while ( ( handleObj = matched.handlers[ j++ ] ) &&
					!event.isImmediatePropagationStopped() ) {
	
					// Triggered event must either 1) have no namespace, or 2) have namespace(s)
					// a subset or equal to those in the bound event (both can have no namespace).
					if ( !event.rnamespace || event.rnamespace.test( handleObj.namespace ) ) {
	
						event.handleObj = handleObj;
						event.data = handleObj.data;
	
						ret = ( ( jQuery.event.special[ handleObj.origType ] || {} ).handle ||
							handleObj.handler ).apply( matched.elem, args );
	
						if ( ret !== undefined ) {
							if ( ( event.result = ret ) === false ) {
								event.preventDefault();
								event.stopPropagation();
							}
						}
					}
				}
			}
	
			// Call the postDispatch hook for the mapped type
			if ( special.postDispatch ) {
				special.postDispatch.call( this, event );
			}
	
			return event.result;
		},
	
		handlers: function( event, handlers ) {
			var i, matches, sel, handleObj,
				handlerQueue = [],
				delegateCount = handlers.delegateCount,
				cur = event.target;
	
			// Support (at least): Chrome, IE9
			// Find delegate handlers
			// Black-hole SVG <use> instance trees (#13180)
			//
			// Support: Firefox<=42+
			// Avoid non-left-click in FF but don't block IE radio events (#3861, gh-2343)
			if ( delegateCount && cur.nodeType &&
				( event.type !== "click" || isNaN( event.button ) || event.button < 1 ) ) {
	
				for ( ; cur !== this; cur = cur.parentNode || this ) {
	
					// Don't check non-elements (#13208)
					// Don't process clicks on disabled elements (#6911, #8165, #11382, #11764)
					if ( cur.nodeType === 1 && ( cur.disabled !== true || event.type !== "click" ) ) {
						matches = [];
						for ( i = 0; i < delegateCount; i++ ) {
							handleObj = handlers[ i ];
	
							// Don't conflict with Object.prototype properties (#13203)
							sel = handleObj.selector + " ";
	
							if ( matches[ sel ] === undefined ) {
								matches[ sel ] = handleObj.needsContext ?
									jQuery( sel, this ).index( cur ) > -1 :
									jQuery.find( sel, this, null, [ cur ] ).length;
							}
							if ( matches[ sel ] ) {
								matches.push( handleObj );
							}
						}
						if ( matches.length ) {
							handlerQueue.push( { elem: cur, handlers: matches } );
						}
					}
				}
			}
	
			// Add the remaining (directly-bound) handlers
			if ( delegateCount < handlers.length ) {
				handlerQueue.push( { elem: this, handlers: handlers.slice( delegateCount ) } );
			}
	
			return handlerQueue;
		},
	
		// Includes some event props shared by KeyEvent and MouseEvent
		props: ( "altKey bubbles cancelable ctrlKey currentTarget detail eventPhase " +
			"metaKey relatedTarget shiftKey target timeStamp view which" ).split( " " ),
	
		fixHooks: {},
	
		keyHooks: {
			props: "char charCode key keyCode".split( " " ),
			filter: function( event, original ) {
	
				// Add which for key events
				if ( event.which == null ) {
					event.which = original.charCode != null ? original.charCode : original.keyCode;
				}
	
				return event;
			}
		},
	
		mouseHooks: {
			props: ( "button buttons clientX clientY offsetX offsetY pageX pageY " +
				"screenX screenY toElement" ).split( " " ),
			filter: function( event, original ) {
				var eventDoc, doc, body,
					button = original.button;
	
				// Calculate pageX/Y if missing and clientX/Y available
				if ( event.pageX == null && original.clientX != null ) {
					eventDoc = event.target.ownerDocument || document;
					doc = eventDoc.documentElement;
					body = eventDoc.body;
	
					event.pageX = original.clientX +
						( doc && doc.scrollLeft || body && body.scrollLeft || 0 ) -
						( doc && doc.clientLeft || body && body.clientLeft || 0 );
					event.pageY = original.clientY +
						( doc && doc.scrollTop  || body && body.scrollTop  || 0 ) -
						( doc && doc.clientTop  || body && body.clientTop  || 0 );
				}
	
				// Add which for click: 1 === left; 2 === middle; 3 === right
				// Note: button is not normalized, so don't use it
				if ( !event.which && button !== undefined ) {
					event.which = ( button & 1 ? 1 : ( button & 2 ? 3 : ( button & 4 ? 2 : 0 ) ) );
				}
	
				return event;
			}
		},
	
		fix: function( event ) {
			if ( event[ jQuery.expando ] ) {
				return event;
			}
	
			// Create a writable copy of the event object and normalize some properties
			var i, prop, copy,
				type = event.type,
				originalEvent = event,
				fixHook = this.fixHooks[ type ];
	
			if ( !fixHook ) {
				this.fixHooks[ type ] = fixHook =
					rmouseEvent.test( type ) ? this.mouseHooks :
					rkeyEvent.test( type ) ? this.keyHooks :
					{};
			}
			copy = fixHook.props ? this.props.concat( fixHook.props ) : this.props;
	
			event = new jQuery.Event( originalEvent );
	
			i = copy.length;
			while ( i-- ) {
				prop = copy[ i ];
				event[ prop ] = originalEvent[ prop ];
			}
	
			// Support: Cordova 2.5 (WebKit) (#13255)
			// All events should have a target; Cordova deviceready doesn't
			if ( !event.target ) {
				event.target = document;
			}
	
			// Support: Safari 6.0+, Chrome<28
			// Target should not be a text node (#504, #13143)
			if ( event.target.nodeType === 3 ) {
				event.target = event.target.parentNode;
			}
	
			return fixHook.filter ? fixHook.filter( event, originalEvent ) : event;
		},
	
		special: {
			load: {
	
				// Prevent triggered image.load events from bubbling to window.load
				noBubble: true
			},
			focus: {
	
				// Fire native event if possible so blur/focus sequence is correct
				trigger: function() {
					if ( this !== safeActiveElement() && this.focus ) {
						this.focus();
						return false;
					}
				},
				delegateType: "focusin"
			},
			blur: {
				trigger: function() {
					if ( this === safeActiveElement() && this.blur ) {
						this.blur();
						return false;
					}
				},
				delegateType: "focusout"
			},
			click: {
	
				// For checkbox, fire native event so checked state will be right
				trigger: function() {
					if ( this.type === "checkbox" && this.click && jQuery.nodeName( this, "input" ) ) {
						this.click();
						return false;
					}
				},
	
				// For cross-browser consistency, don't fire native .click() on links
				_default: function( event ) {
					return jQuery.nodeName( event.target, "a" );
				}
			},
	
			beforeunload: {
				postDispatch: function( event ) {
	
					// Support: Firefox 20+
					// Firefox doesn't alert if the returnValue field is not set.
					if ( event.result !== undefined && event.originalEvent ) {
						event.originalEvent.returnValue = event.result;
					}
				}
			}
		}
	};
	
	jQuery.removeEvent = function( elem, type, handle ) {
	
		// This "if" is needed for plain objects
		if ( elem.removeEventListener ) {
			elem.removeEventListener( type, handle );
		}
	};
	
	jQuery.Event = function( src, props ) {
	
		// Allow instantiation without the 'new' keyword
		if ( !( this instanceof jQuery.Event ) ) {
			return new jQuery.Event( src, props );
		}
	
		// Event object
		if ( src && src.type ) {
			this.originalEvent = src;
			this.type = src.type;
	
			// Events bubbling up the document may have been marked as prevented
			// by a handler lower down the tree; reflect the correct value.
			this.isDefaultPrevented = src.defaultPrevented ||
					src.defaultPrevented === undefined &&
	
					// Support: Android<4.0
					src.returnValue === false ?
				returnTrue :
				returnFalse;
	
		// Event type
		} else {
			this.type = src;
		}
	
		// Put explicitly provided properties onto the event object
		if ( props ) {
			jQuery.extend( this, props );
		}
	
		// Create a timestamp if incoming event doesn't have one
		this.timeStamp = src && src.timeStamp || jQuery.now();
	
		// Mark it as fixed
		this[ jQuery.expando ] = true;
	};
	
	// jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
	// http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
	jQuery.Event.prototype = {
		constructor: jQuery.Event,
		isDefaultPrevented: returnFalse,
		isPropagationStopped: returnFalse,
		isImmediatePropagationStopped: returnFalse,
		isSimulated: false,
	
		preventDefault: function() {
			var e = this.originalEvent;
	
			this.isDefaultPrevented = returnTrue;
	
			if ( e && !this.isSimulated ) {
				e.preventDefault();
			}
		},
		stopPropagation: function() {
			var e = this.originalEvent;
	
			this.isPropagationStopped = returnTrue;
	
			if ( e && !this.isSimulated ) {
				e.stopPropagation();
			}
		},
		stopImmediatePropagation: function() {
			var e = this.originalEvent;
	
			this.isImmediatePropagationStopped = returnTrue;
	
			if ( e && !this.isSimulated ) {
				e.stopImmediatePropagation();
			}
	
			this.stopPropagation();
		}
	};
	
	// Create mouseenter/leave events using mouseover/out and event-time checks
	// so that event delegation works in jQuery.
	// Do the same for pointerenter/pointerleave and pointerover/pointerout
	//
	// Support: Safari 7 only
	// Safari sends mouseenter too often; see:
	// https://code.google.com/p/chromium/issues/detail?id=470258
	// for the description of the bug (it existed in older Chrome versions as well).
	jQuery.each( {
		mouseenter: "mouseover",
		mouseleave: "mouseout",
		pointerenter: "pointerover",
		pointerleave: "pointerout"
	}, function( orig, fix ) {
		jQuery.event.special[ orig ] = {
			delegateType: fix,
			bindType: fix,
	
			handle: function( event ) {
				var ret,
					target = this,
					related = event.relatedTarget,
					handleObj = event.handleObj;
	
				// For mouseenter/leave call the handler if related is outside the target.
				// NB: No relatedTarget if the mouse left/entered the browser window
				if ( !related || ( related !== target && !jQuery.contains( target, related ) ) ) {
					event.type = handleObj.origType;
					ret = handleObj.handler.apply( this, arguments );
					event.type = fix;
				}
				return ret;
			}
		};
	} );
	
	jQuery.fn.extend( {
		on: function( types, selector, data, fn ) {
			return on( this, types, selector, data, fn );
		},
		one: function( types, selector, data, fn ) {
			return on( this, types, selector, data, fn, 1 );
		},
		off: function( types, selector, fn ) {
			var handleObj, type;
			if ( types && types.preventDefault && types.handleObj ) {
	
				// ( event )  dispatched jQuery.Event
				handleObj = types.handleObj;
				jQuery( types.delegateTarget ).off(
					handleObj.namespace ?
						handleObj.origType + "." + handleObj.namespace :
						handleObj.origType,
					handleObj.selector,
					handleObj.handler
				);
				return this;
			}
			if ( typeof types === "object" ) {
	
				// ( types-object [, selector] )
				for ( type in types ) {
					this.off( type, selector, types[ type ] );
				}
				return this;
			}
			if ( selector === false || typeof selector === "function" ) {
	
				// ( types [, fn] )
				fn = selector;
				selector = undefined;
			}
			if ( fn === false ) {
				fn = returnFalse;
			}
			return this.each( function() {
				jQuery.event.remove( this, types, fn, selector );
			} );
		}
	} );
	
	
	var
		rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi,
	
		// Support: IE 10-11, Edge 10240+
		// In IE/Edge using regex groups here causes severe slowdowns.
		// See https://connect.microsoft.com/IE/feedback/details/1736512/
		rnoInnerhtml = /<script|<style|<link/i,
	
		// checked="checked" or checked
		rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i,
		rscriptTypeMasked = /^true\/(.*)/,
		rcleanScript = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;
	
	// Manipulating tables requires a tbody
	function manipulationTarget( elem, content ) {
		return jQuery.nodeName( elem, "table" ) &&
			jQuery.nodeName( content.nodeType !== 11 ? content : content.firstChild, "tr" ) ?
	
			elem.getElementsByTagName( "tbody" )[ 0 ] ||
				elem.appendChild( elem.ownerDocument.createElement( "tbody" ) ) :
			elem;
	}
	
	// Replace/restore the type attribute of script elements for safe DOM manipulation
	function disableScript( elem ) {
		elem.type = ( elem.getAttribute( "type" ) !== null ) + "/" + elem.type;
		return elem;
	}
	function restoreScript( elem ) {
		var match = rscriptTypeMasked.exec( elem.type );
	
		if ( match ) {
			elem.type = match[ 1 ];
		} else {
			elem.removeAttribute( "type" );
		}
	
		return elem;
	}
	
	function cloneCopyEvent( src, dest ) {
		var i, l, type, pdataOld, pdataCur, udataOld, udataCur, events;
	
		if ( dest.nodeType !== 1 ) {
			return;
		}
	
		// 1. Copy private data: events, handlers, etc.
		if ( dataPriv.hasData( src ) ) {
			pdataOld = dataPriv.access( src );
			pdataCur = dataPriv.set( dest, pdataOld );
			events = pdataOld.events;
	
			if ( events ) {
				delete pdataCur.handle;
				pdataCur.events = {};
	
				for ( type in events ) {
					for ( i = 0, l = events[ type ].length; i < l; i++ ) {
						jQuery.event.add( dest, type, events[ type ][ i ] );
					}
				}
			}
		}
	
		// 2. Copy user data
		if ( dataUser.hasData( src ) ) {
			udataOld = dataUser.access( src );
			udataCur = jQuery.extend( {}, udataOld );
	
			dataUser.set( dest, udataCur );
		}
	}
	
	// Fix IE bugs, see support tests
	function fixInput( src, dest ) {
		var nodeName = dest.nodeName.toLowerCase();
	
		// Fails to persist the checked state of a cloned checkbox or radio button.
		if ( nodeName === "input" && rcheckableType.test( src.type ) ) {
			dest.checked = src.checked;
	
		// Fails to return the selected option to the default selected state when cloning options
		} else if ( nodeName === "input" || nodeName === "textarea" ) {
			dest.defaultValue = src.defaultValue;
		}
	}
	
	function domManip( collection, args, callback, ignored ) {
	
		// Flatten any nested arrays
		args = concat.apply( [], args );
	
		var fragment, first, scripts, hasScripts, node, doc,
			i = 0,
			l = collection.length,
			iNoClone = l - 1,
			value = args[ 0 ],
			isFunction = jQuery.isFunction( value );
	
		// We can't cloneNode fragments that contain checked, in WebKit
		if ( isFunction ||
				( l > 1 && typeof value === "string" &&
					!support.checkClone && rchecked.test( value ) ) ) {
			return collection.each( function( index ) {
				var self = collection.eq( index );
				if ( isFunction ) {
					args[ 0 ] = value.call( this, index, self.html() );
				}
				domManip( self, args, callback, ignored );
			} );
		}
	
		if ( l ) {
			fragment = buildFragment( args, collection[ 0 ].ownerDocument, false, collection, ignored );
			first = fragment.firstChild;
	
			if ( fragment.childNodes.length === 1 ) {
				fragment = first;
			}
	
			// Require either new content or an interest in ignored elements to invoke the callback
			if ( first || ignored ) {
				scripts = jQuery.map( getAll( fragment, "script" ), disableScript );
				hasScripts = scripts.length;
	
				// Use the original fragment for the last item
				// instead of the first because it can end up
				// being emptied incorrectly in certain situations (#8070).
				for ( ; i < l; i++ ) {
					node = fragment;
	
					if ( i !== iNoClone ) {
						node = jQuery.clone( node, true, true );
	
						// Keep references to cloned scripts for later restoration
						if ( hasScripts ) {
	
							// Support: Android<4.1, PhantomJS<2
							// push.apply(_, arraylike) throws on ancient WebKit
							jQuery.merge( scripts, getAll( node, "script" ) );
						}
					}
	
					callback.call( collection[ i ], node, i );
				}
	
				if ( hasScripts ) {
					doc = scripts[ scripts.length - 1 ].ownerDocument;
	
					// Reenable scripts
					jQuery.map( scripts, restoreScript );
	
					// Evaluate executable scripts on first document insertion
					for ( i = 0; i < hasScripts; i++ ) {
						node = scripts[ i ];
						if ( rscriptType.test( node.type || "" ) &&
							!dataPriv.access( node, "globalEval" ) &&
							jQuery.contains( doc, node ) ) {
	
							if ( node.src ) {
	
								// Optional AJAX dependency, but won't run scripts if not present
								if ( jQuery._evalUrl ) {
									jQuery._evalUrl( node.src );
								}
							} else {
								jQuery.globalEval( node.textContent.replace( rcleanScript, "" ) );
							}
						}
					}
				}
			}
		}
	
		return collection;
	}
	
	function remove( elem, selector, keepData ) {
		var node,
			nodes = selector ? jQuery.filter( selector, elem ) : elem,
			i = 0;
	
		for ( ; ( node = nodes[ i ] ) != null; i++ ) {
			if ( !keepData && node.nodeType === 1 ) {
				jQuery.cleanData( getAll( node ) );
			}
	
			if ( node.parentNode ) {
				if ( keepData && jQuery.contains( node.ownerDocument, node ) ) {
					setGlobalEval( getAll( node, "script" ) );
				}
				node.parentNode.removeChild( node );
			}
		}
	
		return elem;
	}
	
	jQuery.extend( {
		htmlPrefilter: function( html ) {
			return html.replace( rxhtmlTag, "<$1></$2>" );
		},
	
		clone: function( elem, dataAndEvents, deepDataAndEvents ) {
			var i, l, srcElements, destElements,
				clone = elem.cloneNode( true ),
				inPage = jQuery.contains( elem.ownerDocument, elem );
	
			// Fix IE cloning issues
			if ( !support.noCloneChecked && ( elem.nodeType === 1 || elem.nodeType === 11 ) &&
					!jQuery.isXMLDoc( elem ) ) {
	
				// We eschew Sizzle here for performance reasons: http://jsperf.com/getall-vs-sizzle/2
				destElements = getAll( clone );
				srcElements = getAll( elem );
	
				for ( i = 0, l = srcElements.length; i < l; i++ ) {
					fixInput( srcElements[ i ], destElements[ i ] );
				}
			}
	
			// Copy the events from the original to the clone
			if ( dataAndEvents ) {
				if ( deepDataAndEvents ) {
					srcElements = srcElements || getAll( elem );
					destElements = destElements || getAll( clone );
	
					for ( i = 0, l = srcElements.length; i < l; i++ ) {
						cloneCopyEvent( srcElements[ i ], destElements[ i ] );
					}
				} else {
					cloneCopyEvent( elem, clone );
				}
			}
	
			// Preserve script evaluation history
			destElements = getAll( clone, "script" );
			if ( destElements.length > 0 ) {
				setGlobalEval( destElements, !inPage && getAll( elem, "script" ) );
			}
	
			// Return the cloned set
			return clone;
		},
	
		cleanData: function( elems ) {
			var data, elem, type,
				special = jQuery.event.special,
				i = 0;
	
			for ( ; ( elem = elems[ i ] ) !== undefined; i++ ) {
				if ( acceptData( elem ) ) {
					if ( ( data = elem[ dataPriv.expando ] ) ) {
						if ( data.events ) {
							for ( type in data.events ) {
								if ( special[ type ] ) {
									jQuery.event.remove( elem, type );
	
								// This is a shortcut to avoid jQuery.event.remove's overhead
								} else {
									jQuery.removeEvent( elem, type, data.handle );
								}
							}
						}
	
						// Support: Chrome <= 35-45+
						// Assign undefined instead of using delete, see Data#remove
						elem[ dataPriv.expando ] = undefined;
					}
					if ( elem[ dataUser.expando ] ) {
	
						// Support: Chrome <= 35-45+
						// Assign undefined instead of using delete, see Data#remove
						elem[ dataUser.expando ] = undefined;
					}
				}
			}
		}
	} );
	
	jQuery.fn.extend( {
	
		// Keep domManip exposed until 3.0 (gh-2225)
		domManip: domManip,
	
		detach: function( selector ) {
			return remove( this, selector, true );
		},
	
		remove: function( selector ) {
			return remove( this, selector );
		},
	
		text: function( value ) {
			return access( this, function( value ) {
				return value === undefined ?
					jQuery.text( this ) :
					this.empty().each( function() {
						if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
							this.textContent = value;
						}
					} );
			}, null, value, arguments.length );
		},
	
		append: function() {
			return domManip( this, arguments, function( elem ) {
				if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
					var target = manipulationTarget( this, elem );
					target.appendChild( elem );
				}
			} );
		},
	
		prepend: function() {
			return domManip( this, arguments, function( elem ) {
				if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
					var target = manipulationTarget( this, elem );
					target.insertBefore( elem, target.firstChild );
				}
			} );
		},
	
		before: function() {
			return domManip( this, arguments, function( elem ) {
				if ( this.parentNode ) {
					this.parentNode.insertBefore( elem, this );
				}
			} );
		},
	
		after: function() {
			return domManip( this, arguments, function( elem ) {
				if ( this.parentNode ) {
					this.parentNode.insertBefore( elem, this.nextSibling );
				}
			} );
		},
	
		empty: function() {
			var elem,
				i = 0;
	
			for ( ; ( elem = this[ i ] ) != null; i++ ) {
				if ( elem.nodeType === 1 ) {
	
					// Prevent memory leaks
					jQuery.cleanData( getAll( elem, false ) );
	
					// Remove any remaining nodes
					elem.textContent = "";
				}
			}
	
			return this;
		},
	
		clone: function( dataAndEvents, deepDataAndEvents ) {
			dataAndEvents = dataAndEvents == null ? false : dataAndEvents;
			deepDataAndEvents = deepDataAndEvents == null ? dataAndEvents : deepDataAndEvents;
	
			return this.map( function() {
				return jQuery.clone( this, dataAndEvents, deepDataAndEvents );
			} );
		},
	
		html: function( value ) {
			return access( this, function( value ) {
				var elem = this[ 0 ] || {},
					i = 0,
					l = this.length;
	
				if ( value === undefined && elem.nodeType === 1 ) {
					return elem.innerHTML;
				}
	
				// See if we can take a shortcut and just use innerHTML
				if ( typeof value === "string" && !rnoInnerhtml.test( value ) &&
					!wrapMap[ ( rtagName.exec( value ) || [ "", "" ] )[ 1 ].toLowerCase() ] ) {
	
					value = jQuery.htmlPrefilter( value );
	
					try {
						for ( ; i < l; i++ ) {
							elem = this[ i ] || {};
	
							// Remove element nodes and prevent memory leaks
							if ( elem.nodeType === 1 ) {
								jQuery.cleanData( getAll( elem, false ) );
								elem.innerHTML = value;
							}
						}
	
						elem = 0;
	
					// If using innerHTML throws an exception, use the fallback method
					} catch ( e ) {}
				}
	
				if ( elem ) {
					this.empty().append( value );
				}
			}, null, value, arguments.length );
		},
	
		replaceWith: function() {
			var ignored = [];
	
			// Make the changes, replacing each non-ignored context element with the new content
			return domManip( this, arguments, function( elem ) {
				var parent = this.parentNode;
	
				if ( jQuery.inArray( this, ignored ) < 0 ) {
					jQuery.cleanData( getAll( this ) );
					if ( parent ) {
						parent.replaceChild( elem, this );
					}
				}
	
			// Force callback invocation
			}, ignored );
		}
	} );
	
	jQuery.each( {
		appendTo: "append",
		prependTo: "prepend",
		insertBefore: "before",
		insertAfter: "after",
		replaceAll: "replaceWith"
	}, function( name, original ) {
		jQuery.fn[ name ] = function( selector ) {
			var elems,
				ret = [],
				insert = jQuery( selector ),
				last = insert.length - 1,
				i = 0;
	
			for ( ; i <= last; i++ ) {
				elems = i === last ? this : this.clone( true );
				jQuery( insert[ i ] )[ original ]( elems );
	
				// Support: QtWebKit
				// .get() because push.apply(_, arraylike) throws
				push.apply( ret, elems.get() );
			}
	
			return this.pushStack( ret );
		};
	} );
	
	
	var iframe,
		elemdisplay = {
	
			// Support: Firefox
			// We have to pre-define these values for FF (#10227)
			HTML: "block",
			BODY: "block"
		};
	
	/**
	 * Retrieve the actual display of a element
	 * @param {String} name nodeName of the element
	 * @param {Object} doc Document object
	 */
	
	// Called only from within defaultDisplay
	function actualDisplay( name, doc ) {
		var elem = jQuery( doc.createElement( name ) ).appendTo( doc.body ),
	
			display = jQuery.css( elem[ 0 ], "display" );
	
		// We don't have any data stored on the element,
		// so use "detach" method as fast way to get rid of the element
		elem.detach();
	
		return display;
	}
	
	/**
	 * Try to determine the default display value of an element
	 * @param {String} nodeName
	 */
	function defaultDisplay( nodeName ) {
		var doc = document,
			display = elemdisplay[ nodeName ];
	
		if ( !display ) {
			display = actualDisplay( nodeName, doc );
	
			// If the simple way fails, read from inside an iframe
			if ( display === "none" || !display ) {
	
				// Use the already-created iframe if possible
				iframe = ( iframe || jQuery( "<iframe frameborder='0' width='0' height='0'/>" ) )
					.appendTo( doc.documentElement );
	
				// Always write a new HTML skeleton so Webkit and Firefox don't choke on reuse
				doc = iframe[ 0 ].contentDocument;
	
				// Support: IE
				doc.write();
				doc.close();
	
				display = actualDisplay( nodeName, doc );
				iframe.detach();
			}
	
			// Store the correct default display
			elemdisplay[ nodeName ] = display;
		}
	
		return display;
	}
	var rmargin = ( /^margin/ );
	
	var rnumnonpx = new RegExp( "^(" + pnum + ")(?!px)[a-z%]+$", "i" );
	
	var getStyles = function( elem ) {
	
			// Support: IE<=11+, Firefox<=30+ (#15098, #14150)
			// IE throws on elements created in popups
			// FF meanwhile throws on frame elements through "defaultView.getComputedStyle"
			var view = elem.ownerDocument.defaultView;
	
			if ( !view || !view.opener ) {
				view = window;
			}
	
			return view.getComputedStyle( elem );
		};
	
	var swap = function( elem, options, callback, args ) {
		var ret, name,
			old = {};
	
		// Remember the old values, and insert the new ones
		for ( name in options ) {
			old[ name ] = elem.style[ name ];
			elem.style[ name ] = options[ name ];
		}
	
		ret = callback.apply( elem, args || [] );
	
		// Revert the old values
		for ( name in options ) {
			elem.style[ name ] = old[ name ];
		}
	
		return ret;
	};
	
	
	var documentElement = document.documentElement;
	
	
	
	( function() {
		var pixelPositionVal, boxSizingReliableVal, pixelMarginRightVal, reliableMarginLeftVal,
			container = document.createElement( "div" ),
			div = document.createElement( "div" );
	
		// Finish early in limited (non-browser) environments
		if ( !div.style ) {
			return;
		}
	
		// Support: IE9-11+
		// Style of cloned element affects source element cloned (#8908)
		div.style.backgroundClip = "content-box";
		div.cloneNode( true ).style.backgroundClip = "";
		support.clearCloneStyle = div.style.backgroundClip === "content-box";
	
		container.style.cssText = "border:0;width:8px;height:0;top:0;left:-9999px;" +
			"padding:0;margin-top:1px;position:absolute";
		container.appendChild( div );
	
		// Executing both pixelPosition & boxSizingReliable tests require only one layout
		// so they're executed at the same time to save the second computation.
		function computeStyleTests() {
			div.style.cssText =
	
				// Support: Firefox<29, Android 2.3
				// Vendor-prefix box-sizing
				"-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;" +
				"position:relative;display:block;" +
				"margin:auto;border:1px;padding:1px;" +
				"top:1%;width:50%";
			div.innerHTML = "";
			documentElement.appendChild( container );
	
			var divStyle = window.getComputedStyle( div );
			pixelPositionVal = divStyle.top !== "1%";
			reliableMarginLeftVal = divStyle.marginLeft === "2px";
			boxSizingReliableVal = divStyle.width === "4px";
	
			// Support: Android 4.0 - 4.3 only
			// Some styles come back with percentage values, even though they shouldn't
			div.style.marginRight = "50%";
			pixelMarginRightVal = divStyle.marginRight === "4px";
	
			documentElement.removeChild( container );
		}
	
		jQuery.extend( support, {
			pixelPosition: function() {
	
				// This test is executed only once but we still do memoizing
				// since we can use the boxSizingReliable pre-computing.
				// No need to check if the test was already performed, though.
				computeStyleTests();
				return pixelPositionVal;
			},
			boxSizingReliable: function() {
				if ( boxSizingReliableVal == null ) {
					computeStyleTests();
				}
				return boxSizingReliableVal;
			},
			pixelMarginRight: function() {
	
				// Support: Android 4.0-4.3
				// We're checking for boxSizingReliableVal here instead of pixelMarginRightVal
				// since that compresses better and they're computed together anyway.
				if ( boxSizingReliableVal == null ) {
					computeStyleTests();
				}
				return pixelMarginRightVal;
			},
			reliableMarginLeft: function() {
	
				// Support: IE <=8 only, Android 4.0 - 4.3 only, Firefox <=3 - 37
				if ( boxSizingReliableVal == null ) {
					computeStyleTests();
				}
				return reliableMarginLeftVal;
			},
			reliableMarginRight: function() {
	
				// Support: Android 2.3
				// Check if div with explicit width and no margin-right incorrectly
				// gets computed margin-right based on width of container. (#3333)
				// WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
				// This support function is only executed once so no memoizing is needed.
				var ret,
					marginDiv = div.appendChild( document.createElement( "div" ) );
	
				// Reset CSS: box-sizing; display; margin; border; padding
				marginDiv.style.cssText = div.style.cssText =
	
					// Support: Android 2.3
					// Vendor-prefix box-sizing
					"-webkit-box-sizing:content-box;box-sizing:content-box;" +
					"display:block;margin:0;border:0;padding:0";
				marginDiv.style.marginRight = marginDiv.style.width = "0";
				div.style.width = "1px";
				documentElement.appendChild( container );
	
				ret = !parseFloat( window.getComputedStyle( marginDiv ).marginRight );
	
				documentElement.removeChild( container );
				div.removeChild( marginDiv );
	
				return ret;
			}
		} );
	} )();
	
	
	function curCSS( elem, name, computed ) {
		var width, minWidth, maxWidth, ret,
			style = elem.style;
	
		computed = computed || getStyles( elem );
		ret = computed ? computed.getPropertyValue( name ) || computed[ name ] : undefined;
	
		// Support: Opera 12.1x only
		// Fall back to style even without computed
		// computed is undefined for elems on document fragments
		if ( ( ret === "" || ret === undefined ) && !jQuery.contains( elem.ownerDocument, elem ) ) {
			ret = jQuery.style( elem, name );
		}
	
		// Support: IE9
		// getPropertyValue is only needed for .css('filter') (#12537)
		if ( computed ) {
	
			// A tribute to the "awesome hack by Dean Edwards"
			// Android Browser returns percentage for some values,
			// but width seems to be reliably pixels.
			// This is against the CSSOM draft spec:
			// http://dev.w3.org/csswg/cssom/#resolved-values
			if ( !support.pixelMarginRight() && rnumnonpx.test( ret ) && rmargin.test( name ) ) {
	
				// Remember the original values
				width = style.width;
				minWidth = style.minWidth;
				maxWidth = style.maxWidth;
	
				// Put in the new values to get a computed value out
				style.minWidth = style.maxWidth = style.width = ret;
				ret = computed.width;
	
				// Revert the changed values
				style.width = width;
				style.minWidth = minWidth;
				style.maxWidth = maxWidth;
			}
		}
	
		return ret !== undefined ?
	
			// Support: IE9-11+
			// IE returns zIndex value as an integer.
			ret + "" :
			ret;
	}
	
	
	function addGetHookIf( conditionFn, hookFn ) {
	
		// Define the hook, we'll check on the first run if it's really needed.
		return {
			get: function() {
				if ( conditionFn() ) {
	
					// Hook not needed (or it's not possible to use it due
					// to missing dependency), remove it.
					delete this.get;
					return;
				}
	
				// Hook needed; redefine it so that the support test is not executed again.
				return ( this.get = hookFn ).apply( this, arguments );
			}
		};
	}
	
	
	var
	
		// Swappable if display is none or starts with table
		// except "table", "table-cell", or "table-caption"
		// See here for display values: https://developer.mozilla.org/en-US/docs/CSS/display
		rdisplayswap = /^(none|table(?!-c[ea]).+)/,
	
		cssShow = { position: "absolute", visibility: "hidden", display: "block" },
		cssNormalTransform = {
			letterSpacing: "0",
			fontWeight: "400"
		},
	
		cssPrefixes = [ "Webkit", "O", "Moz", "ms" ],
		emptyStyle = document.createElement( "div" ).style;
	
	// Return a css property mapped to a potentially vendor prefixed property
	function vendorPropName( name ) {
	
		// Shortcut for names that are not vendor prefixed
		if ( name in emptyStyle ) {
			return name;
		}
	
		// Check for vendor prefixed names
		var capName = name[ 0 ].toUpperCase() + name.slice( 1 ),
			i = cssPrefixes.length;
	
		while ( i-- ) {
			name = cssPrefixes[ i ] + capName;
			if ( name in emptyStyle ) {
				return name;
			}
		}
	}
	
	function setPositiveNumber( elem, value, subtract ) {
	
		// Any relative (+/-) values have already been
		// normalized at this point
		var matches = rcssNum.exec( value );
		return matches ?
	
			// Guard against undefined "subtract", e.g., when used as in cssHooks
			Math.max( 0, matches[ 2 ] - ( subtract || 0 ) ) + ( matches[ 3 ] || "px" ) :
			value;
	}
	
	function augmentWidthOrHeight( elem, name, extra, isBorderBox, styles ) {
		var i = extra === ( isBorderBox ? "border" : "content" ) ?
	
			// If we already have the right measurement, avoid augmentation
			4 :
	
			// Otherwise initialize for horizontal or vertical properties
			name === "width" ? 1 : 0,
	
			val = 0;
	
		for ( ; i < 4; i += 2 ) {
	
			// Both box models exclude margin, so add it if we want it
			if ( extra === "margin" ) {
				val += jQuery.css( elem, extra + cssExpand[ i ], true, styles );
			}
	
			if ( isBorderBox ) {
	
				// border-box includes padding, so remove it if we want content
				if ( extra === "content" ) {
					val -= jQuery.css( elem, "padding" + cssExpand[ i ], true, styles );
				}
	
				// At this point, extra isn't border nor margin, so remove border
				if ( extra !== "margin" ) {
					val -= jQuery.css( elem, "border" + cssExpand[ i ] + "Width", true, styles );
				}
			} else {
	
				// At this point, extra isn't content, so add padding
				val += jQuery.css( elem, "padding" + cssExpand[ i ], true, styles );
	
				// At this point, extra isn't content nor padding, so add border
				if ( extra !== "padding" ) {
					val += jQuery.css( elem, "border" + cssExpand[ i ] + "Width", true, styles );
				}
			}
		}
	
		return val;
	}
	
	function getWidthOrHeight( elem, name, extra ) {
	
		// Start with offset property, which is equivalent to the border-box value
		var valueIsBorderBox = true,
			val = name === "width" ? elem.offsetWidth : elem.offsetHeight,
			styles = getStyles( elem ),
			isBorderBox = jQuery.css( elem, "boxSizing", false, styles ) === "border-box";
	
		// Some non-html elements return undefined for offsetWidth, so check for null/undefined
		// svg - https://bugzilla.mozilla.org/show_bug.cgi?id=649285
		// MathML - https://bugzilla.mozilla.org/show_bug.cgi?id=491668
		if ( val <= 0 || val == null ) {
	
			// Fall back to computed then uncomputed css if necessary
			val = curCSS( elem, name, styles );
			if ( val < 0 || val == null ) {
				val = elem.style[ name ];
			}
	
			// Computed unit is not pixels. Stop here and return.
			if ( rnumnonpx.test( val ) ) {
				return val;
			}
	
			// Check for style in case a browser which returns unreliable values
			// for getComputedStyle silently falls back to the reliable elem.style
			valueIsBorderBox = isBorderBox &&
				( support.boxSizingReliable() || val === elem.style[ name ] );
	
			// Normalize "", auto, and prepare for extra
			val = parseFloat( val ) || 0;
		}
	
		// Use the active box-sizing model to add/subtract irrelevant styles
		return ( val +
			augmentWidthOrHeight(
				elem,
				name,
				extra || ( isBorderBox ? "border" : "content" ),
				valueIsBorderBox,
				styles
			)
		) + "px";
	}
	
	function showHide( elements, show ) {
		var display, elem, hidden,
			values = [],
			index = 0,
			length = elements.length;
	
		for ( ; index < length; index++ ) {
			elem = elements[ index ];
			if ( !elem.style ) {
				continue;
			}
	
			values[ index ] = dataPriv.get( elem, "olddisplay" );
			display = elem.style.display;
			if ( show ) {
	
				// Reset the inline display of this element to learn if it is
				// being hidden by cascaded rules or not
				if ( !values[ index ] && display === "none" ) {
					elem.style.display = "";
				}
	
				// Set elements which have been overridden with display: none
				// in a stylesheet to whatever the default browser style is
				// for such an element
				if ( elem.style.display === "" && isHidden( elem ) ) {
					values[ index ] = dataPriv.access(
						elem,
						"olddisplay",
						defaultDisplay( elem.nodeName )
					);
				}
			} else {
				hidden = isHidden( elem );
	
				if ( display !== "none" || !hidden ) {
					dataPriv.set(
						elem,
						"olddisplay",
						hidden ? display : jQuery.css( elem, "display" )
					);
				}
			}
		}
	
		// Set the display of most of the elements in a second loop
		// to avoid the constant reflow
		for ( index = 0; index < length; index++ ) {
			elem = elements[ index ];
			if ( !elem.style ) {
				continue;
			}
			if ( !show || elem.style.display === "none" || elem.style.display === "" ) {
				elem.style.display = show ? values[ index ] || "" : "none";
			}
		}
	
		return elements;
	}
	
	jQuery.extend( {
	
		// Add in style property hooks for overriding the default
		// behavior of getting and setting a style property
		cssHooks: {
			opacity: {
				get: function( elem, computed ) {
					if ( computed ) {
	
						// We should always get a number back from opacity
						var ret = curCSS( elem, "opacity" );
						return ret === "" ? "1" : ret;
					}
				}
			}
		},
	
		// Don't automatically add "px" to these possibly-unitless properties
		cssNumber: {
			"animationIterationCount": true,
			"columnCount": true,
			"fillOpacity": true,
			"flexGrow": true,
			"flexShrink": true,
			"fontWeight": true,
			"lineHeight": true,
			"opacity": true,
			"order": true,
			"orphans": true,
			"widows": true,
			"zIndex": true,
			"zoom": true
		},
	
		// Add in properties whose names you wish to fix before
		// setting or getting the value
		cssProps: {
			"float": "cssFloat"
		},
	
		// Get and set the style property on a DOM Node
		style: function( elem, name, value, extra ) {
	
			// Don't set styles on text and comment nodes
			if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style ) {
				return;
			}
	
			// Make sure that we're working with the right name
			var ret, type, hooks,
				origName = jQuery.camelCase( name ),
				style = elem.style;
	
			name = jQuery.cssProps[ origName ] ||
				( jQuery.cssProps[ origName ] = vendorPropName( origName ) || origName );
	
			// Gets hook for the prefixed version, then unprefixed version
			hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];
	
			// Check if we're setting a value
			if ( value !== undefined ) {
				type = typeof value;
	
				// Convert "+=" or "-=" to relative numbers (#7345)
				if ( type === "string" && ( ret = rcssNum.exec( value ) ) && ret[ 1 ] ) {
					value = adjustCSS( elem, name, ret );
	
					// Fixes bug #9237
					type = "number";
				}
	
				// Make sure that null and NaN values aren't set (#7116)
				if ( value == null || value !== value ) {
					return;
				}
	
				// If a number was passed in, add the unit (except for certain CSS properties)
				if ( type === "number" ) {
					value += ret && ret[ 3 ] || ( jQuery.cssNumber[ origName ] ? "" : "px" );
				}
	
				// Support: IE9-11+
				// background-* props affect original clone's values
				if ( !support.clearCloneStyle && value === "" && name.indexOf( "background" ) === 0 ) {
					style[ name ] = "inherit";
				}
	
				// If a hook was provided, use that value, otherwise just set the specified value
				if ( !hooks || !( "set" in hooks ) ||
					( value = hooks.set( elem, value, extra ) ) !== undefined ) {
	
					style[ name ] = value;
				}
	
			} else {
	
				// If a hook was provided get the non-computed value from there
				if ( hooks && "get" in hooks &&
					( ret = hooks.get( elem, false, extra ) ) !== undefined ) {
	
					return ret;
				}
	
				// Otherwise just get the value from the style object
				return style[ name ];
			}
		},
	
		css: function( elem, name, extra, styles ) {
			var val, num, hooks,
				origName = jQuery.camelCase( name );
	
			// Make sure that we're working with the right name
			name = jQuery.cssProps[ origName ] ||
				( jQuery.cssProps[ origName ] = vendorPropName( origName ) || origName );
	
			// Try prefixed name followed by the unprefixed name
			hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];
	
			// If a hook was provided get the computed value from there
			if ( hooks && "get" in hooks ) {
				val = hooks.get( elem, true, extra );
			}
	
			// Otherwise, if a way to get the computed value exists, use that
			if ( val === undefined ) {
				val = curCSS( elem, name, styles );
			}
	
			// Convert "normal" to computed value
			if ( val === "normal" && name in cssNormalTransform ) {
				val = cssNormalTransform[ name ];
			}
	
			// Make numeric if forced or a qualifier was provided and val looks numeric
			if ( extra === "" || extra ) {
				num = parseFloat( val );
				return extra === true || isFinite( num ) ? num || 0 : val;
			}
			return val;
		}
	} );
	
	jQuery.each( [ "height", "width" ], function( i, name ) {
		jQuery.cssHooks[ name ] = {
			get: function( elem, computed, extra ) {
				if ( computed ) {
	
					// Certain elements can have dimension info if we invisibly show them
					// but it must have a current display style that would benefit
					return rdisplayswap.test( jQuery.css( elem, "display" ) ) &&
						elem.offsetWidth === 0 ?
							swap( elem, cssShow, function() {
								return getWidthOrHeight( elem, name, extra );
							} ) :
							getWidthOrHeight( elem, name, extra );
				}
			},
	
			set: function( elem, value, extra ) {
				var matches,
					styles = extra && getStyles( elem ),
					subtract = extra && augmentWidthOrHeight(
						elem,
						name,
						extra,
						jQuery.css( elem, "boxSizing", false, styles ) === "border-box",
						styles
					);
	
				// Convert to pixels if value adjustment is needed
				if ( subtract && ( matches = rcssNum.exec( value ) ) &&
					( matches[ 3 ] || "px" ) !== "px" ) {
	
					elem.style[ name ] = value;
					value = jQuery.css( elem, name );
				}
	
				return setPositiveNumber( elem, value, subtract );
			}
		};
	} );
	
	jQuery.cssHooks.marginLeft = addGetHookIf( support.reliableMarginLeft,
		function( elem, computed ) {
			if ( computed ) {
				return ( parseFloat( curCSS( elem, "marginLeft" ) ) ||
					elem.getBoundingClientRect().left -
						swap( elem, { marginLeft: 0 }, function() {
							return elem.getBoundingClientRect().left;
						} )
					) + "px";
			}
		}
	);
	
	// Support: Android 2.3
	jQuery.cssHooks.marginRight = addGetHookIf( support.reliableMarginRight,
		function( elem, computed ) {
			if ( computed ) {
				return swap( elem, { "display": "inline-block" },
					curCSS, [ elem, "marginRight" ] );
			}
		}
	);
	
	// These hooks are used by animate to expand properties
	jQuery.each( {
		margin: "",
		padding: "",
		border: "Width"
	}, function( prefix, suffix ) {
		jQuery.cssHooks[ prefix + suffix ] = {
			expand: function( value ) {
				var i = 0,
					expanded = {},
	
					// Assumes a single number if not a string
					parts = typeof value === "string" ? value.split( " " ) : [ value ];
	
				for ( ; i < 4; i++ ) {
					expanded[ prefix + cssExpand[ i ] + suffix ] =
						parts[ i ] || parts[ i - 2 ] || parts[ 0 ];
				}
	
				return expanded;
			}
		};
	
		if ( !rmargin.test( prefix ) ) {
			jQuery.cssHooks[ prefix + suffix ].set = setPositiveNumber;
		}
	} );
	
	jQuery.fn.extend( {
		css: function( name, value ) {
			return access( this, function( elem, name, value ) {
				var styles, len,
					map = {},
					i = 0;
	
				if ( jQuery.isArray( name ) ) {
					styles = getStyles( elem );
					len = name.length;
	
					for ( ; i < len; i++ ) {
						map[ name[ i ] ] = jQuery.css( elem, name[ i ], false, styles );
					}
	
					return map;
				}
	
				return value !== undefined ?
					jQuery.style( elem, name, value ) :
					jQuery.css( elem, name );
			}, name, value, arguments.length > 1 );
		},
		show: function() {
			return showHide( this, true );
		},
		hide: function() {
			return showHide( this );
		},
		toggle: function( state ) {
			if ( typeof state === "boolean" ) {
				return state ? this.show() : this.hide();
			}
	
			return this.each( function() {
				if ( isHidden( this ) ) {
					jQuery( this ).show();
				} else {
					jQuery( this ).hide();
				}
			} );
		}
	} );
	
	
	function Tween( elem, options, prop, end, easing ) {
		return new Tween.prototype.init( elem, options, prop, end, easing );
	}
	jQuery.Tween = Tween;
	
	Tween.prototype = {
		constructor: Tween,
		init: function( elem, options, prop, end, easing, unit ) {
			this.elem = elem;
			this.prop = prop;
			this.easing = easing || jQuery.easing._default;
			this.options = options;
			this.start = this.now = this.cur();
			this.end = end;
			this.unit = unit || ( jQuery.cssNumber[ prop ] ? "" : "px" );
		},
		cur: function() {
			var hooks = Tween.propHooks[ this.prop ];
	
			return hooks && hooks.get ?
				hooks.get( this ) :
				Tween.propHooks._default.get( this );
		},
		run: function( percent ) {
			var eased,
				hooks = Tween.propHooks[ this.prop ];
	
			if ( this.options.duration ) {
				this.pos = eased = jQuery.easing[ this.easing ](
					percent, this.options.duration * percent, 0, 1, this.options.duration
				);
			} else {
				this.pos = eased = percent;
			}
			this.now = ( this.end - this.start ) * eased + this.start;
	
			if ( this.options.step ) {
				this.options.step.call( this.elem, this.now, this );
			}
	
			if ( hooks && hooks.set ) {
				hooks.set( this );
			} else {
				Tween.propHooks._default.set( this );
			}
			return this;
		}
	};
	
	Tween.prototype.init.prototype = Tween.prototype;
	
	Tween.propHooks = {
		_default: {
			get: function( tween ) {
				var result;
	
				// Use a property on the element directly when it is not a DOM element,
				// or when there is no matching style property that exists.
				if ( tween.elem.nodeType !== 1 ||
					tween.elem[ tween.prop ] != null && tween.elem.style[ tween.prop ] == null ) {
					return tween.elem[ tween.prop ];
				}
	
				// Passing an empty string as a 3rd parameter to .css will automatically
				// attempt a parseFloat and fallback to a string if the parse fails.
				// Simple values such as "10px" are parsed to Float;
				// complex values such as "rotate(1rad)" are returned as-is.
				result = jQuery.css( tween.elem, tween.prop, "" );
	
				// Empty strings, null, undefined and "auto" are converted to 0.
				return !result || result === "auto" ? 0 : result;
			},
			set: function( tween ) {
	
				// Use step hook for back compat.
				// Use cssHook if its there.
				// Use .style if available and use plain properties where available.
				if ( jQuery.fx.step[ tween.prop ] ) {
					jQuery.fx.step[ tween.prop ]( tween );
				} else if ( tween.elem.nodeType === 1 &&
					( tween.elem.style[ jQuery.cssProps[ tween.prop ] ] != null ||
						jQuery.cssHooks[ tween.prop ] ) ) {
					jQuery.style( tween.elem, tween.prop, tween.now + tween.unit );
				} else {
					tween.elem[ tween.prop ] = tween.now;
				}
			}
		}
	};
	
	// Support: IE9
	// Panic based approach to setting things on disconnected nodes
	Tween.propHooks.scrollTop = Tween.propHooks.scrollLeft = {
		set: function( tween ) {
			if ( tween.elem.nodeType && tween.elem.parentNode ) {
				tween.elem[ tween.prop ] = tween.now;
			}
		}
	};
	
	jQuery.easing = {
		linear: function( p ) {
			return p;
		},
		swing: function( p ) {
			return 0.5 - Math.cos( p * Math.PI ) / 2;
		},
		_default: "swing"
	};
	
	jQuery.fx = Tween.prototype.init;
	
	// Back Compat <1.8 extension point
	jQuery.fx.step = {};
	
	
	
	
	var
		fxNow, timerId,
		rfxtypes = /^(?:toggle|show|hide)$/,
		rrun = /queueHooks$/;
	
	// Animations created synchronously will run synchronously
	function createFxNow() {
		window.setTimeout( function() {
			fxNow = undefined;
		} );
		return ( fxNow = jQuery.now() );
	}
	
	// Generate parameters to create a standard animation
	function genFx( type, includeWidth ) {
		var which,
			i = 0,
			attrs = { height: type };
	
		// If we include width, step value is 1 to do all cssExpand values,
		// otherwise step value is 2 to skip over Left and Right
		includeWidth = includeWidth ? 1 : 0;
		for ( ; i < 4 ; i += 2 - includeWidth ) {
			which = cssExpand[ i ];
			attrs[ "margin" + which ] = attrs[ "padding" + which ] = type;
		}
	
		if ( includeWidth ) {
			attrs.opacity = attrs.width = type;
		}
	
		return attrs;
	}
	
	function createTween( value, prop, animation ) {
		var tween,
			collection = ( Animation.tweeners[ prop ] || [] ).concat( Animation.tweeners[ "*" ] ),
			index = 0,
			length = collection.length;
		for ( ; index < length; index++ ) {
			if ( ( tween = collection[ index ].call( animation, prop, value ) ) ) {
	
				// We're done with this property
				return tween;
			}
		}
	}
	
	function defaultPrefilter( elem, props, opts ) {
		/* jshint validthis: true */
		var prop, value, toggle, tween, hooks, oldfire, display, checkDisplay,
			anim = this,
			orig = {},
			style = elem.style,
			hidden = elem.nodeType && isHidden( elem ),
			dataShow = dataPriv.get( elem, "fxshow" );
	
		// Handle queue: false promises
		if ( !opts.queue ) {
			hooks = jQuery._queueHooks( elem, "fx" );
			if ( hooks.unqueued == null ) {
				hooks.unqueued = 0;
				oldfire = hooks.empty.fire;
				hooks.empty.fire = function() {
					if ( !hooks.unqueued ) {
						oldfire();
					}
				};
			}
			hooks.unqueued++;
	
			anim.always( function() {
	
				// Ensure the complete handler is called before this completes
				anim.always( function() {
					hooks.unqueued--;
					if ( !jQuery.queue( elem, "fx" ).length ) {
						hooks.empty.fire();
					}
				} );
			} );
		}
	
		// Height/width overflow pass
		if ( elem.nodeType === 1 && ( "height" in props || "width" in props ) ) {
	
			// Make sure that nothing sneaks out
			// Record all 3 overflow attributes because IE9-10 do not
			// change the overflow attribute when overflowX and
			// overflowY are set to the same value
			opts.overflow = [ style.overflow, style.overflowX, style.overflowY ];
	
			// Set display property to inline-block for height/width
			// animations on inline elements that are having width/height animated
			display = jQuery.css( elem, "display" );
	
			// Test default display if display is currently "none"
			checkDisplay = display === "none" ?
				dataPriv.get( elem, "olddisplay" ) || defaultDisplay( elem.nodeName ) : display;
	
			if ( checkDisplay === "inline" && jQuery.css( elem, "float" ) === "none" ) {
				style.display = "inline-block";
			}
		}
	
		if ( opts.overflow ) {
			style.overflow = "hidden";
			anim.always( function() {
				style.overflow = opts.overflow[ 0 ];
				style.overflowX = opts.overflow[ 1 ];
				style.overflowY = opts.overflow[ 2 ];
			} );
		}
	
		// show/hide pass
		for ( prop in props ) {
			value = props[ prop ];
			if ( rfxtypes.exec( value ) ) {
				delete props[ prop ];
				toggle = toggle || value === "toggle";
				if ( value === ( hidden ? "hide" : "show" ) ) {
	
					// If there is dataShow left over from a stopped hide or show
					// and we are going to proceed with show, we should pretend to be hidden
					if ( value === "show" && dataShow && dataShow[ prop ] !== undefined ) {
						hidden = true;
					} else {
						continue;
					}
				}
				orig[ prop ] = dataShow && dataShow[ prop ] || jQuery.style( elem, prop );
	
			// Any non-fx value stops us from restoring the original display value
			} else {
				display = undefined;
			}
		}
	
		if ( !jQuery.isEmptyObject( orig ) ) {
			if ( dataShow ) {
				if ( "hidden" in dataShow ) {
					hidden = dataShow.hidden;
				}
			} else {
				dataShow = dataPriv.access( elem, "fxshow", {} );
			}
	
			// Store state if its toggle - enables .stop().toggle() to "reverse"
			if ( toggle ) {
				dataShow.hidden = !hidden;
			}
			if ( hidden ) {
				jQuery( elem ).show();
			} else {
				anim.done( function() {
					jQuery( elem ).hide();
				} );
			}
			anim.done( function() {
				var prop;
	
				dataPriv.remove( elem, "fxshow" );
				for ( prop in orig ) {
					jQuery.style( elem, prop, orig[ prop ] );
				}
			} );
			for ( prop in orig ) {
				tween = createTween( hidden ? dataShow[ prop ] : 0, prop, anim );
	
				if ( !( prop in dataShow ) ) {
					dataShow[ prop ] = tween.start;
					if ( hidden ) {
						tween.end = tween.start;
						tween.start = prop === "width" || prop === "height" ? 1 : 0;
					}
				}
			}
	
		// If this is a noop like .hide().hide(), restore an overwritten display value
		} else if ( ( display === "none" ? defaultDisplay( elem.nodeName ) : display ) === "inline" ) {
			style.display = display;
		}
	}
	
	function propFilter( props, specialEasing ) {
		var index, name, easing, value, hooks;
	
		// camelCase, specialEasing and expand cssHook pass
		for ( index in props ) {
			name = jQuery.camelCase( index );
			easing = specialEasing[ name ];
			value = props[ index ];
			if ( jQuery.isArray( value ) ) {
				easing = value[ 1 ];
				value = props[ index ] = value[ 0 ];
			}
	
			if ( index !== name ) {
				props[ name ] = value;
				delete props[ index ];
			}
	
			hooks = jQuery.cssHooks[ name ];
			if ( hooks && "expand" in hooks ) {
				value = hooks.expand( value );
				delete props[ name ];
	
				// Not quite $.extend, this won't overwrite existing keys.
				// Reusing 'index' because we have the correct "name"
				for ( index in value ) {
					if ( !( index in props ) ) {
						props[ index ] = value[ index ];
						specialEasing[ index ] = easing;
					}
				}
			} else {
				specialEasing[ name ] = easing;
			}
		}
	}
	
	function Animation( elem, properties, options ) {
		var result,
			stopped,
			index = 0,
			length = Animation.prefilters.length,
			deferred = jQuery.Deferred().always( function() {
	
				// Don't match elem in the :animated selector
				delete tick.elem;
			} ),
			tick = function() {
				if ( stopped ) {
					return false;
				}
				var currentTime = fxNow || createFxNow(),
					remaining = Math.max( 0, animation.startTime + animation.duration - currentTime ),
	
					// Support: Android 2.3
					// Archaic crash bug won't allow us to use `1 - ( 0.5 || 0 )` (#12497)
					temp = remaining / animation.duration || 0,
					percent = 1 - temp,
					index = 0,
					length = animation.tweens.length;
	
				for ( ; index < length ; index++ ) {
					animation.tweens[ index ].run( percent );
				}
	
				deferred.notifyWith( elem, [ animation, percent, remaining ] );
	
				if ( percent < 1 && length ) {
					return remaining;
				} else {
					deferred.resolveWith( elem, [ animation ] );
					return false;
				}
			},
			animation = deferred.promise( {
				elem: elem,
				props: jQuery.extend( {}, properties ),
				opts: jQuery.extend( true, {
					specialEasing: {},
					easing: jQuery.easing._default
				}, options ),
				originalProperties: properties,
				originalOptions: options,
				startTime: fxNow || createFxNow(),
				duration: options.duration,
				tweens: [],
				createTween: function( prop, end ) {
					var tween = jQuery.Tween( elem, animation.opts, prop, end,
							animation.opts.specialEasing[ prop ] || animation.opts.easing );
					animation.tweens.push( tween );
					return tween;
				},
				stop: function( gotoEnd ) {
					var index = 0,
	
						// If we are going to the end, we want to run all the tweens
						// otherwise we skip this part
						length = gotoEnd ? animation.tweens.length : 0;
					if ( stopped ) {
						return this;
					}
					stopped = true;
					for ( ; index < length ; index++ ) {
						animation.tweens[ index ].run( 1 );
					}
	
					// Resolve when we played the last frame; otherwise, reject
					if ( gotoEnd ) {
						deferred.notifyWith( elem, [ animation, 1, 0 ] );
						deferred.resolveWith( elem, [ animation, gotoEnd ] );
					} else {
						deferred.rejectWith( elem, [ animation, gotoEnd ] );
					}
					return this;
				}
			} ),
			props = animation.props;
	
		propFilter( props, animation.opts.specialEasing );
	
		for ( ; index < length ; index++ ) {
			result = Animation.prefilters[ index ].call( animation, elem, props, animation.opts );
			if ( result ) {
				if ( jQuery.isFunction( result.stop ) ) {
					jQuery._queueHooks( animation.elem, animation.opts.queue ).stop =
						jQuery.proxy( result.stop, result );
				}
				return result;
			}
		}
	
		jQuery.map( props, createTween, animation );
	
		if ( jQuery.isFunction( animation.opts.start ) ) {
			animation.opts.start.call( elem, animation );
		}
	
		jQuery.fx.timer(
			jQuery.extend( tick, {
				elem: elem,
				anim: animation,
				queue: animation.opts.queue
			} )
		);
	
		// attach callbacks from options
		return animation.progress( animation.opts.progress )
			.done( animation.opts.done, animation.opts.complete )
			.fail( animation.opts.fail )
			.always( animation.opts.always );
	}
	
	jQuery.Animation = jQuery.extend( Animation, {
		tweeners: {
			"*": [ function( prop, value ) {
				var tween = this.createTween( prop, value );
				adjustCSS( tween.elem, prop, rcssNum.exec( value ), tween );
				return tween;
			} ]
		},
	
		tweener: function( props, callback ) {
			if ( jQuery.isFunction( props ) ) {
				callback = props;
				props = [ "*" ];
			} else {
				props = props.match( rnotwhite );
			}
	
			var prop,
				index = 0,
				length = props.length;
	
			for ( ; index < length ; index++ ) {
				prop = props[ index ];
				Animation.tweeners[ prop ] = Animation.tweeners[ prop ] || [];
				Animation.tweeners[ prop ].unshift( callback );
			}
		},
	
		prefilters: [ defaultPrefilter ],
	
		prefilter: function( callback, prepend ) {
			if ( prepend ) {
				Animation.prefilters.unshift( callback );
			} else {
				Animation.prefilters.push( callback );
			}
		}
	} );
	
	jQuery.speed = function( speed, easing, fn ) {
		var opt = speed && typeof speed === "object" ? jQuery.extend( {}, speed ) : {
			complete: fn || !fn && easing ||
				jQuery.isFunction( speed ) && speed,
			duration: speed,
			easing: fn && easing || easing && !jQuery.isFunction( easing ) && easing
		};
	
		opt.duration = jQuery.fx.off ? 0 : typeof opt.duration === "number" ?
			opt.duration : opt.duration in jQuery.fx.speeds ?
				jQuery.fx.speeds[ opt.duration ] : jQuery.fx.speeds._default;
	
		// Normalize opt.queue - true/undefined/null -> "fx"
		if ( opt.queue == null || opt.queue === true ) {
			opt.queue = "fx";
		}
	
		// Queueing
		opt.old = opt.complete;
	
		opt.complete = function() {
			if ( jQuery.isFunction( opt.old ) ) {
				opt.old.call( this );
			}
	
			if ( opt.queue ) {
				jQuery.dequeue( this, opt.queue );
			}
		};
	
		return opt;
	};
	
	jQuery.fn.extend( {
		fadeTo: function( speed, to, easing, callback ) {
	
			// Show any hidden elements after setting opacity to 0
			return this.filter( isHidden ).css( "opacity", 0 ).show()
	
				// Animate to the value specified
				.end().animate( { opacity: to }, speed, easing, callback );
		},
		animate: function( prop, speed, easing, callback ) {
			var empty = jQuery.isEmptyObject( prop ),
				optall = jQuery.speed( speed, easing, callback ),
				doAnimation = function() {
	
					// Operate on a copy of prop so per-property easing won't be lost
					var anim = Animation( this, jQuery.extend( {}, prop ), optall );
	
					// Empty animations, or finishing resolves immediately
					if ( empty || dataPriv.get( this, "finish" ) ) {
						anim.stop( true );
					}
				};
				doAnimation.finish = doAnimation;
	
			return empty || optall.queue === false ?
				this.each( doAnimation ) :
				this.queue( optall.queue, doAnimation );
		},
		stop: function( type, clearQueue, gotoEnd ) {
			var stopQueue = function( hooks ) {
				var stop = hooks.stop;
				delete hooks.stop;
				stop( gotoEnd );
			};
	
			if ( typeof type !== "string" ) {
				gotoEnd = clearQueue;
				clearQueue = type;
				type = undefined;
			}
			if ( clearQueue && type !== false ) {
				this.queue( type || "fx", [] );
			}
	
			return this.each( function() {
				var dequeue = true,
					index = type != null && type + "queueHooks",
					timers = jQuery.timers,
					data = dataPriv.get( this );
	
				if ( index ) {
					if ( data[ index ] && data[ index ].stop ) {
						stopQueue( data[ index ] );
					}
				} else {
					for ( index in data ) {
						if ( data[ index ] && data[ index ].stop && rrun.test( index ) ) {
							stopQueue( data[ index ] );
						}
					}
				}
	
				for ( index = timers.length; index--; ) {
					if ( timers[ index ].elem === this &&
						( type == null || timers[ index ].queue === type ) ) {
	
						timers[ index ].anim.stop( gotoEnd );
						dequeue = false;
						timers.splice( index, 1 );
					}
				}
	
				// Start the next in the queue if the last step wasn't forced.
				// Timers currently will call their complete callbacks, which
				// will dequeue but only if they were gotoEnd.
				if ( dequeue || !gotoEnd ) {
					jQuery.dequeue( this, type );
				}
			} );
		},
		finish: function( type ) {
			if ( type !== false ) {
				type = type || "fx";
			}
			return this.each( function() {
				var index,
					data = dataPriv.get( this ),
					queue = data[ type + "queue" ],
					hooks = data[ type + "queueHooks" ],
					timers = jQuery.timers,
					length = queue ? queue.length : 0;
	
				// Enable finishing flag on private data
				data.finish = true;
	
				// Empty the queue first
				jQuery.queue( this, type, [] );
	
				if ( hooks && hooks.stop ) {
					hooks.stop.call( this, true );
				}
	
				// Look for any active animations, and finish them
				for ( index = timers.length; index--; ) {
					if ( timers[ index ].elem === this && timers[ index ].queue === type ) {
						timers[ index ].anim.stop( true );
						timers.splice( index, 1 );
					}
				}
	
				// Look for any animations in the old queue and finish them
				for ( index = 0; index < length; index++ ) {
					if ( queue[ index ] && queue[ index ].finish ) {
						queue[ index ].finish.call( this );
					}
				}
	
				// Turn off finishing flag
				delete data.finish;
			} );
		}
	} );
	
	jQuery.each( [ "toggle", "show", "hide" ], function( i, name ) {
		var cssFn = jQuery.fn[ name ];
		jQuery.fn[ name ] = function( speed, easing, callback ) {
			return speed == null || typeof speed === "boolean" ?
				cssFn.apply( this, arguments ) :
				this.animate( genFx( name, true ), speed, easing, callback );
		};
	} );
	
	// Generate shortcuts for custom animations
	jQuery.each( {
		slideDown: genFx( "show" ),
		slideUp: genFx( "hide" ),
		slideToggle: genFx( "toggle" ),
		fadeIn: { opacity: "show" },
		fadeOut: { opacity: "hide" },
		fadeToggle: { opacity: "toggle" }
	}, function( name, props ) {
		jQuery.fn[ name ] = function( speed, easing, callback ) {
			return this.animate( props, speed, easing, callback );
		};
	} );
	
	jQuery.timers = [];
	jQuery.fx.tick = function() {
		var timer,
			i = 0,
			timers = jQuery.timers;
	
		fxNow = jQuery.now();
	
		for ( ; i < timers.length; i++ ) {
			timer = timers[ i ];
	
			// Checks the timer has not already been removed
			if ( !timer() && timers[ i ] === timer ) {
				timers.splice( i--, 1 );
			}
		}
	
		if ( !timers.length ) {
			jQuery.fx.stop();
		}
		fxNow = undefined;
	};
	
	jQuery.fx.timer = function( timer ) {
		jQuery.timers.push( timer );
		if ( timer() ) {
			jQuery.fx.start();
		} else {
			jQuery.timers.pop();
		}
	};
	
	jQuery.fx.interval = 13;
	jQuery.fx.start = function() {
		if ( !timerId ) {
			timerId = window.setInterval( jQuery.fx.tick, jQuery.fx.interval );
		}
	};
	
	jQuery.fx.stop = function() {
		window.clearInterval( timerId );
	
		timerId = null;
	};
	
	jQuery.fx.speeds = {
		slow: 600,
		fast: 200,
	
		// Default speed
		_default: 400
	};
	
	
	// Based off of the plugin by Clint Helfers, with permission.
	// http://web.archive.org/web/20100324014747/http://blindsignals.com/index.php/2009/07/jquery-delay/
	jQuery.fn.delay = function( time, type ) {
		time = jQuery.fx ? jQuery.fx.speeds[ time ] || time : time;
		type = type || "fx";
	
		return this.queue( type, function( next, hooks ) {
			var timeout = window.setTimeout( next, time );
			hooks.stop = function() {
				window.clearTimeout( timeout );
			};
		} );
	};
	
	
	( function() {
		var input = document.createElement( "input" ),
			select = document.createElement( "select" ),
			opt = select.appendChild( document.createElement( "option" ) );
	
		input.type = "checkbox";
	
		// Support: iOS<=5.1, Android<=4.2+
		// Default value for a checkbox should be "on"
		support.checkOn = input.value !== "";
	
		// Support: IE<=11+
		// Must access selectedIndex to make default options select
		support.optSelected = opt.selected;
	
		// Support: Android<=2.3
		// Options inside disabled selects are incorrectly marked as disabled
		select.disabled = true;
		support.optDisabled = !opt.disabled;
	
		// Support: IE<=11+
		// An input loses its value after becoming a radio
		input = document.createElement( "input" );
		input.value = "t";
		input.type = "radio";
		support.radioValue = input.value === "t";
	} )();
	
	
	var boolHook,
		attrHandle = jQuery.expr.attrHandle;
	
	jQuery.fn.extend( {
		attr: function( name, value ) {
			return access( this, jQuery.attr, name, value, arguments.length > 1 );
		},
	
		removeAttr: function( name ) {
			return this.each( function() {
				jQuery.removeAttr( this, name );
			} );
		}
	} );
	
	jQuery.extend( {
		attr: function( elem, name, value ) {
			var ret, hooks,
				nType = elem.nodeType;
	
			// Don't get/set attributes on text, comment and attribute nodes
			if ( nType === 3 || nType === 8 || nType === 2 ) {
				return;
			}
	
			// Fallback to prop when attributes are not supported
			if ( typeof elem.getAttribute === "undefined" ) {
				return jQuery.prop( elem, name, value );
			}
	
			// All attributes are lowercase
			// Grab necessary hook if one is defined
			if ( nType !== 1 || !jQuery.isXMLDoc( elem ) ) {
				name = name.toLowerCase();
				hooks = jQuery.attrHooks[ name ] ||
					( jQuery.expr.match.bool.test( name ) ? boolHook : undefined );
			}
	
			if ( value !== undefined ) {
				if ( value === null ) {
					jQuery.removeAttr( elem, name );
					return;
				}
	
				if ( hooks && "set" in hooks &&
					( ret = hooks.set( elem, value, name ) ) !== undefined ) {
					return ret;
				}
	
				elem.setAttribute( name, value + "" );
				return value;
			}
	
			if ( hooks && "get" in hooks && ( ret = hooks.get( elem, name ) ) !== null ) {
				return ret;
			}
	
			ret = jQuery.find.attr( elem, name );
	
			// Non-existent attributes return null, we normalize to undefined
			return ret == null ? undefined : ret;
		},
	
		attrHooks: {
			type: {
				set: function( elem, value ) {
					if ( !support.radioValue && value === "radio" &&
						jQuery.nodeName( elem, "input" ) ) {
						var val = elem.value;
						elem.setAttribute( "type", value );
						if ( val ) {
							elem.value = val;
						}
						return value;
					}
				}
			}
		},
	
		removeAttr: function( elem, value ) {
			var name, propName,
				i = 0,
				attrNames = value && value.match( rnotwhite );
	
			if ( attrNames && elem.nodeType === 1 ) {
				while ( ( name = attrNames[ i++ ] ) ) {
					propName = jQuery.propFix[ name ] || name;
	
					// Boolean attributes get special treatment (#10870)
					if ( jQuery.expr.match.bool.test( name ) ) {
	
						// Set corresponding property to false
						elem[ propName ] = false;
					}
	
					elem.removeAttribute( name );
				}
			}
		}
	} );
	
	// Hooks for boolean attributes
	boolHook = {
		set: function( elem, value, name ) {
			if ( value === false ) {
	
				// Remove boolean attributes when set to false
				jQuery.removeAttr( elem, name );
			} else {
				elem.setAttribute( name, name );
			}
			return name;
		}
	};
	jQuery.each( jQuery.expr.match.bool.source.match( /\w+/g ), function( i, name ) {
		var getter = attrHandle[ name ] || jQuery.find.attr;
	
		attrHandle[ name ] = function( elem, name, isXML ) {
			var ret, handle;
			if ( !isXML ) {
	
				// Avoid an infinite loop by temporarily removing this function from the getter
				handle = attrHandle[ name ];
				attrHandle[ name ] = ret;
				ret = getter( elem, name, isXML ) != null ?
					name.toLowerCase() :
					null;
				attrHandle[ name ] = handle;
			}
			return ret;
		};
	} );
	
	
	
	
	var rfocusable = /^(?:input|select|textarea|button)$/i,
		rclickable = /^(?:a|area)$/i;
	
	jQuery.fn.extend( {
		prop: function( name, value ) {
			return access( this, jQuery.prop, name, value, arguments.length > 1 );
		},
	
		removeProp: function( name ) {
			return this.each( function() {
				delete this[ jQuery.propFix[ name ] || name ];
			} );
		}
	} );
	
	jQuery.extend( {
		prop: function( elem, name, value ) {
			var ret, hooks,
				nType = elem.nodeType;
	
			// Don't get/set properties on text, comment and attribute nodes
			if ( nType === 3 || nType === 8 || nType === 2 ) {
				return;
			}
	
			if ( nType !== 1 || !jQuery.isXMLDoc( elem ) ) {
	
				// Fix name and attach hooks
				name = jQuery.propFix[ name ] || name;
				hooks = jQuery.propHooks[ name ];
			}
	
			if ( value !== undefined ) {
				if ( hooks && "set" in hooks &&
					( ret = hooks.set( elem, value, name ) ) !== undefined ) {
					return ret;
				}
	
				return ( elem[ name ] = value );
			}
	
			if ( hooks && "get" in hooks && ( ret = hooks.get( elem, name ) ) !== null ) {
				return ret;
			}
	
			return elem[ name ];
		},
	
		propHooks: {
			tabIndex: {
				get: function( elem ) {
	
					// elem.tabIndex doesn't always return the
					// correct value when it hasn't been explicitly set
					// http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
					// Use proper attribute retrieval(#12072)
					var tabindex = jQuery.find.attr( elem, "tabindex" );
	
					return tabindex ?
						parseInt( tabindex, 10 ) :
						rfocusable.test( elem.nodeName ) ||
							rclickable.test( elem.nodeName ) && elem.href ?
								0 :
								-1;
				}
			}
		},
	
		propFix: {
			"for": "htmlFor",
			"class": "className"
		}
	} );
	
	// Support: IE <=11 only
	// Accessing the selectedIndex property
	// forces the browser to respect setting selected
	// on the option
	// The getter ensures a default option is selected
	// when in an optgroup
	if ( !support.optSelected ) {
		jQuery.propHooks.selected = {
			get: function( elem ) {
				var parent = elem.parentNode;
				if ( parent && parent.parentNode ) {
					parent.parentNode.selectedIndex;
				}
				return null;
			},
			set: function( elem ) {
				var parent = elem.parentNode;
				if ( parent ) {
					parent.selectedIndex;
	
					if ( parent.parentNode ) {
						parent.parentNode.selectedIndex;
					}
				}
			}
		};
	}
	
	jQuery.each( [
		"tabIndex",
		"readOnly",
		"maxLength",
		"cellSpacing",
		"cellPadding",
		"rowSpan",
		"colSpan",
		"useMap",
		"frameBorder",
		"contentEditable"
	], function() {
		jQuery.propFix[ this.toLowerCase() ] = this;
	} );
	
	
	
	
	var rclass = /[\t\r\n\f]/g;
	
	function getClass( elem ) {
		return elem.getAttribute && elem.getAttribute( "class" ) || "";
	}
	
	jQuery.fn.extend( {
		addClass: function( value ) {
			var classes, elem, cur, curValue, clazz, j, finalValue,
				i = 0;
	
			if ( jQuery.isFunction( value ) ) {
				return this.each( function( j ) {
					jQuery( this ).addClass( value.call( this, j, getClass( this ) ) );
				} );
			}
	
			if ( typeof value === "string" && value ) {
				classes = value.match( rnotwhite ) || [];
	
				while ( ( elem = this[ i++ ] ) ) {
					curValue = getClass( elem );
					cur = elem.nodeType === 1 &&
						( " " + curValue + " " ).replace( rclass, " " );
	
					if ( cur ) {
						j = 0;
						while ( ( clazz = classes[ j++ ] ) ) {
							if ( cur.indexOf( " " + clazz + " " ) < 0 ) {
								cur += clazz + " ";
							}
						}
	
						// Only assign if different to avoid unneeded rendering.
						finalValue = jQuery.trim( cur );
						if ( curValue !== finalValue ) {
							elem.setAttribute( "class", finalValue );
						}
					}
				}
			}
	
			return this;
		},
	
		removeClass: function( value ) {
			var classes, elem, cur, curValue, clazz, j, finalValue,
				i = 0;
	
			if ( jQuery.isFunction( value ) ) {
				return this.each( function( j ) {
					jQuery( this ).removeClass( value.call( this, j, getClass( this ) ) );
				} );
			}
	
			if ( !arguments.length ) {
				return this.attr( "class", "" );
			}
	
			if ( typeof value === "string" && value ) {
				classes = value.match( rnotwhite ) || [];
	
				while ( ( elem = this[ i++ ] ) ) {
					curValue = getClass( elem );
	
					// This expression is here for better compressibility (see addClass)
					cur = elem.nodeType === 1 &&
						( " " + curValue + " " ).replace( rclass, " " );
	
					if ( cur ) {
						j = 0;
						while ( ( clazz = classes[ j++ ] ) ) {
	
							// Remove *all* instances
							while ( cur.indexOf( " " + clazz + " " ) > -1 ) {
								cur = cur.replace( " " + clazz + " ", " " );
							}
						}
	
						// Only assign if different to avoid unneeded rendering.
						finalValue = jQuery.trim( cur );
						if ( curValue !== finalValue ) {
							elem.setAttribute( "class", finalValue );
						}
					}
				}
			}
	
			return this;
		},
	
		toggleClass: function( value, stateVal ) {
			var type = typeof value;
	
			if ( typeof stateVal === "boolean" && type === "string" ) {
				return stateVal ? this.addClass( value ) : this.removeClass( value );
			}
	
			if ( jQuery.isFunction( value ) ) {
				return this.each( function( i ) {
					jQuery( this ).toggleClass(
						value.call( this, i, getClass( this ), stateVal ),
						stateVal
					);
				} );
			}
	
			return this.each( function() {
				var className, i, self, classNames;
	
				if ( type === "string" ) {
	
					// Toggle individual class names
					i = 0;
					self = jQuery( this );
					classNames = value.match( rnotwhite ) || [];
	
					while ( ( className = classNames[ i++ ] ) ) {
	
						// Check each className given, space separated list
						if ( self.hasClass( className ) ) {
							self.removeClass( className );
						} else {
							self.addClass( className );
						}
					}
	
				// Toggle whole class name
				} else if ( value === undefined || type === "boolean" ) {
					className = getClass( this );
					if ( className ) {
	
						// Store className if set
						dataPriv.set( this, "__className__", className );
					}
	
					// If the element has a class name or if we're passed `false`,
					// then remove the whole classname (if there was one, the above saved it).
					// Otherwise bring back whatever was previously saved (if anything),
					// falling back to the empty string if nothing was stored.
					if ( this.setAttribute ) {
						this.setAttribute( "class",
							className || value === false ?
							"" :
							dataPriv.get( this, "__className__" ) || ""
						);
					}
				}
			} );
		},
	
		hasClass: function( selector ) {
			var className, elem,
				i = 0;
	
			className = " " + selector + " ";
			while ( ( elem = this[ i++ ] ) ) {
				if ( elem.nodeType === 1 &&
					( " " + getClass( elem ) + " " ).replace( rclass, " " )
						.indexOf( className ) > -1
				) {
					return true;
				}
			}
	
			return false;
		}
	} );
	
	
	
	
	var rreturn = /\r/g,
		rspaces = /[\x20\t\r\n\f]+/g;
	
	jQuery.fn.extend( {
		val: function( value ) {
			var hooks, ret, isFunction,
				elem = this[ 0 ];
	
			if ( !arguments.length ) {
				if ( elem ) {
					hooks = jQuery.valHooks[ elem.type ] ||
						jQuery.valHooks[ elem.nodeName.toLowerCase() ];
	
					if ( hooks &&
						"get" in hooks &&
						( ret = hooks.get( elem, "value" ) ) !== undefined
					) {
						return ret;
					}
	
					ret = elem.value;
	
					return typeof ret === "string" ?
	
						// Handle most common string cases
						ret.replace( rreturn, "" ) :
	
						// Handle cases where value is null/undef or number
						ret == null ? "" : ret;
				}
	
				return;
			}
	
			isFunction = jQuery.isFunction( value );
	
			return this.each( function( i ) {
				var val;
	
				if ( this.nodeType !== 1 ) {
					return;
				}
	
				if ( isFunction ) {
					val = value.call( this, i, jQuery( this ).val() );
				} else {
					val = value;
				}
	
				// Treat null/undefined as ""; convert numbers to string
				if ( val == null ) {
					val = "";
	
				} else if ( typeof val === "number" ) {
					val += "";
	
				} else if ( jQuery.isArray( val ) ) {
					val = jQuery.map( val, function( value ) {
						return value == null ? "" : value + "";
					} );
				}
	
				hooks = jQuery.valHooks[ this.type ] || jQuery.valHooks[ this.nodeName.toLowerCase() ];
	
				// If set returns undefined, fall back to normal setting
				if ( !hooks || !( "set" in hooks ) || hooks.set( this, val, "value" ) === undefined ) {
					this.value = val;
				}
			} );
		}
	} );
	
	jQuery.extend( {
		valHooks: {
			option: {
				get: function( elem ) {
	
					var val = jQuery.find.attr( elem, "value" );
					return val != null ?
						val :
	
						// Support: IE10-11+
						// option.text throws exceptions (#14686, #14858)
						// Strip and collapse whitespace
						// https://html.spec.whatwg.org/#strip-and-collapse-whitespace
						jQuery.trim( jQuery.text( elem ) ).replace( rspaces, " " );
				}
			},
			select: {
				get: function( elem ) {
					var value, option,
						options = elem.options,
						index = elem.selectedIndex,
						one = elem.type === "select-one" || index < 0,
						values = one ? null : [],
						max = one ? index + 1 : options.length,
						i = index < 0 ?
							max :
							one ? index : 0;
	
					// Loop through all the selected options
					for ( ; i < max; i++ ) {
						option = options[ i ];
	
						// IE8-9 doesn't update selected after form reset (#2551)
						if ( ( option.selected || i === index ) &&
	
								// Don't return options that are disabled or in a disabled optgroup
								( support.optDisabled ?
									!option.disabled : option.getAttribute( "disabled" ) === null ) &&
								( !option.parentNode.disabled ||
									!jQuery.nodeName( option.parentNode, "optgroup" ) ) ) {
	
							// Get the specific value for the option
							value = jQuery( option ).val();
	
							// We don't need an array for one selects
							if ( one ) {
								return value;
							}
	
							// Multi-Selects return an array
							values.push( value );
						}
					}
	
					return values;
				},
	
				set: function( elem, value ) {
					var optionSet, option,
						options = elem.options,
						values = jQuery.makeArray( value ),
						i = options.length;
	
					while ( i-- ) {
						option = options[ i ];
						if ( option.selected =
							jQuery.inArray( jQuery.valHooks.option.get( option ), values ) > -1
						) {
							optionSet = true;
						}
					}
	
					// Force browsers to behave consistently when non-matching value is set
					if ( !optionSet ) {
						elem.selectedIndex = -1;
					}
					return values;
				}
			}
		}
	} );
	
	// Radios and checkboxes getter/setter
	jQuery.each( [ "radio", "checkbox" ], function() {
		jQuery.valHooks[ this ] = {
			set: function( elem, value ) {
				if ( jQuery.isArray( value ) ) {
					return ( elem.checked = jQuery.inArray( jQuery( elem ).val(), value ) > -1 );
				}
			}
		};
		if ( !support.checkOn ) {
			jQuery.valHooks[ this ].get = function( elem ) {
				return elem.getAttribute( "value" ) === null ? "on" : elem.value;
			};
		}
	} );
	
	
	
	
	// Return jQuery for attributes-only inclusion
	
	
	var rfocusMorph = /^(?:focusinfocus|focusoutblur)$/;
	
	jQuery.extend( jQuery.event, {
	
		trigger: function( event, data, elem, onlyHandlers ) {
	
			var i, cur, tmp, bubbleType, ontype, handle, special,
				eventPath = [ elem || document ],
				type = hasOwn.call( event, "type" ) ? event.type : event,
				namespaces = hasOwn.call( event, "namespace" ) ? event.namespace.split( "." ) : [];
	
			cur = tmp = elem = elem || document;
	
			// Don't do events on text and comment nodes
			if ( elem.nodeType === 3 || elem.nodeType === 8 ) {
				return;
			}
	
			// focus/blur morphs to focusin/out; ensure we're not firing them right now
			if ( rfocusMorph.test( type + jQuery.event.triggered ) ) {
				return;
			}
	
			if ( type.indexOf( "." ) > -1 ) {
	
				// Namespaced trigger; create a regexp to match event type in handle()
				namespaces = type.split( "." );
				type = namespaces.shift();
				namespaces.sort();
			}
			ontype = type.indexOf( ":" ) < 0 && "on" + type;
	
			// Caller can pass in a jQuery.Event object, Object, or just an event type string
			event = event[ jQuery.expando ] ?
				event :
				new jQuery.Event( type, typeof event === "object" && event );
	
			// Trigger bitmask: & 1 for native handlers; & 2 for jQuery (always true)
			event.isTrigger = onlyHandlers ? 2 : 3;
			event.namespace = namespaces.join( "." );
			event.rnamespace = event.namespace ?
				new RegExp( "(^|\\.)" + namespaces.join( "\\.(?:.*\\.|)" ) + "(\\.|$)" ) :
				null;
	
			// Clean up the event in case it is being reused
			event.result = undefined;
			if ( !event.target ) {
				event.target = elem;
			}
	
			// Clone any incoming data and prepend the event, creating the handler arg list
			data = data == null ?
				[ event ] :
				jQuery.makeArray( data, [ event ] );
	
			// Allow special events to draw outside the lines
			special = jQuery.event.special[ type ] || {};
			if ( !onlyHandlers && special.trigger && special.trigger.apply( elem, data ) === false ) {
				return;
			}
	
			// Determine event propagation path in advance, per W3C events spec (#9951)
			// Bubble up to document, then to window; watch for a global ownerDocument var (#9724)
			if ( !onlyHandlers && !special.noBubble && !jQuery.isWindow( elem ) ) {
	
				bubbleType = special.delegateType || type;
				if ( !rfocusMorph.test( bubbleType + type ) ) {
					cur = cur.parentNode;
				}
				for ( ; cur; cur = cur.parentNode ) {
					eventPath.push( cur );
					tmp = cur;
				}
	
				// Only add window if we got to document (e.g., not plain obj or detached DOM)
				if ( tmp === ( elem.ownerDocument || document ) ) {
					eventPath.push( tmp.defaultView || tmp.parentWindow || window );
				}
			}
	
			// Fire handlers on the event path
			i = 0;
			while ( ( cur = eventPath[ i++ ] ) && !event.isPropagationStopped() ) {
	
				event.type = i > 1 ?
					bubbleType :
					special.bindType || type;
	
				// jQuery handler
				handle = ( dataPriv.get( cur, "events" ) || {} )[ event.type ] &&
					dataPriv.get( cur, "handle" );
				if ( handle ) {
					handle.apply( cur, data );
				}
	
				// Native handler
				handle = ontype && cur[ ontype ];
				if ( handle && handle.apply && acceptData( cur ) ) {
					event.result = handle.apply( cur, data );
					if ( event.result === false ) {
						event.preventDefault();
					}
				}
			}
			event.type = type;
	
			// If nobody prevented the default action, do it now
			if ( !onlyHandlers && !event.isDefaultPrevented() ) {
	
				if ( ( !special._default ||
					special._default.apply( eventPath.pop(), data ) === false ) &&
					acceptData( elem ) ) {
	
					// Call a native DOM method on the target with the same name name as the event.
					// Don't do default actions on window, that's where global variables be (#6170)
					if ( ontype && jQuery.isFunction( elem[ type ] ) && !jQuery.isWindow( elem ) ) {
	
						// Don't re-trigger an onFOO event when we call its FOO() method
						tmp = elem[ ontype ];
	
						if ( tmp ) {
							elem[ ontype ] = null;
						}
	
						// Prevent re-triggering of the same event, since we already bubbled it above
						jQuery.event.triggered = type;
						elem[ type ]();
						jQuery.event.triggered = undefined;
	
						if ( tmp ) {
							elem[ ontype ] = tmp;
						}
					}
				}
			}
	
			return event.result;
		},
	
		// Piggyback on a donor event to simulate a different one
		// Used only for `focus(in | out)` events
		simulate: function( type, elem, event ) {
			var e = jQuery.extend(
				new jQuery.Event(),
				event,
				{
					type: type,
					isSimulated: true
				}
			);
	
			jQuery.event.trigger( e, null, elem );
		}
	
	} );
	
	jQuery.fn.extend( {
	
		trigger: function( type, data ) {
			return this.each( function() {
				jQuery.event.trigger( type, data, this );
			} );
		},
		triggerHandler: function( type, data ) {
			var elem = this[ 0 ];
			if ( elem ) {
				return jQuery.event.trigger( type, data, elem, true );
			}
		}
	} );
	
	
	jQuery.each( ( "blur focus focusin focusout load resize scroll unload click dblclick " +
		"mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " +
		"change select submit keydown keypress keyup error contextmenu" ).split( " " ),
		function( i, name ) {
	
		// Handle event binding
		jQuery.fn[ name ] = function( data, fn ) {
			return arguments.length > 0 ?
				this.on( name, null, data, fn ) :
				this.trigger( name );
		};
	} );
	
	jQuery.fn.extend( {
		hover: function( fnOver, fnOut ) {
			return this.mouseenter( fnOver ).mouseleave( fnOut || fnOver );
		}
	} );
	
	
	
	
	support.focusin = "onfocusin" in window;
	
	
	// Support: Firefox
	// Firefox doesn't have focus(in | out) events
	// Related ticket - https://bugzilla.mozilla.org/show_bug.cgi?id=687787
	//
	// Support: Chrome, Safari
	// focus(in | out) events fire after focus & blur events,
	// which is spec violation - http://www.w3.org/TR/DOM-Level-3-Events/#events-focusevent-event-order
	// Related ticket - https://code.google.com/p/chromium/issues/detail?id=449857
	if ( !support.focusin ) {
		jQuery.each( { focus: "focusin", blur: "focusout" }, function( orig, fix ) {
	
			// Attach a single capturing handler on the document while someone wants focusin/focusout
			var handler = function( event ) {
				jQuery.event.simulate( fix, event.target, jQuery.event.fix( event ) );
			};
	
			jQuery.event.special[ fix ] = {
				setup: function() {
					var doc = this.ownerDocument || this,
						attaches = dataPriv.access( doc, fix );
	
					if ( !attaches ) {
						doc.addEventListener( orig, handler, true );
					}
					dataPriv.access( doc, fix, ( attaches || 0 ) + 1 );
				},
				teardown: function() {
					var doc = this.ownerDocument || this,
						attaches = dataPriv.access( doc, fix ) - 1;
	
					if ( !attaches ) {
						doc.removeEventListener( orig, handler, true );
						dataPriv.remove( doc, fix );
	
					} else {
						dataPriv.access( doc, fix, attaches );
					}
				}
			};
		} );
	}
	var location = window.location;
	
	var nonce = jQuery.now();
	
	var rquery = ( /\?/ );
	
	
	
	// Support: Android 2.3
	// Workaround failure to string-cast null input
	jQuery.parseJSON = function( data ) {
		return JSON.parse( data + "" );
	};
	
	
	// Cross-browser xml parsing
	jQuery.parseXML = function( data ) {
		var xml;
		if ( !data || typeof data !== "string" ) {
			return null;
		}
	
		// Support: IE9
		try {
			xml = ( new window.DOMParser() ).parseFromString( data, "text/xml" );
		} catch ( e ) {
			xml = undefined;
		}
	
		if ( !xml || xml.getElementsByTagName( "parsererror" ).length ) {
			jQuery.error( "Invalid XML: " + data );
		}
		return xml;
	};
	
	
	var
		rhash = /#.*$/,
		rts = /([?&])_=[^&]*/,
		rheaders = /^(.*?):[ \t]*([^\r\n]*)$/mg,
	
		// #7653, #8125, #8152: local protocol detection
		rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
		rnoContent = /^(?:GET|HEAD)$/,
		rprotocol = /^\/\//,
	
		/* Prefilters
		 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
		 * 2) These are called:
		 *    - BEFORE asking for a transport
		 *    - AFTER param serialization (s.data is a string if s.processData is true)
		 * 3) key is the dataType
		 * 4) the catchall symbol "*" can be used
		 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
		 */
		prefilters = {},
	
		/* Transports bindings
		 * 1) key is the dataType
		 * 2) the catchall symbol "*" can be used
		 * 3) selection will start with transport dataType and THEN go to "*" if needed
		 */
		transports = {},
	
		// Avoid comment-prolog char sequence (#10098); must appease lint and evade compression
		allTypes = "*/".concat( "*" ),
	
		// Anchor tag for parsing the document origin
		originAnchor = document.createElement( "a" );
		originAnchor.href = location.href;
	
	// Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
	function addToPrefiltersOrTransports( structure ) {
	
		// dataTypeExpression is optional and defaults to "*"
		return function( dataTypeExpression, func ) {
	
			if ( typeof dataTypeExpression !== "string" ) {
				func = dataTypeExpression;
				dataTypeExpression = "*";
			}
	
			var dataType,
				i = 0,
				dataTypes = dataTypeExpression.toLowerCase().match( rnotwhite ) || [];
	
			if ( jQuery.isFunction( func ) ) {
	
				// For each dataType in the dataTypeExpression
				while ( ( dataType = dataTypes[ i++ ] ) ) {
	
					// Prepend if requested
					if ( dataType[ 0 ] === "+" ) {
						dataType = dataType.slice( 1 ) || "*";
						( structure[ dataType ] = structure[ dataType ] || [] ).unshift( func );
	
					// Otherwise append
					} else {
						( structure[ dataType ] = structure[ dataType ] || [] ).push( func );
					}
				}
			}
		};
	}
	
	// Base inspection function for prefilters and transports
	function inspectPrefiltersOrTransports( structure, options, originalOptions, jqXHR ) {
	
		var inspected = {},
			seekingTransport = ( structure === transports );
	
		function inspect( dataType ) {
			var selected;
			inspected[ dataType ] = true;
			jQuery.each( structure[ dataType ] || [], function( _, prefilterOrFactory ) {
				var dataTypeOrTransport = prefilterOrFactory( options, originalOptions, jqXHR );
				if ( typeof dataTypeOrTransport === "string" &&
					!seekingTransport && !inspected[ dataTypeOrTransport ] ) {
	
					options.dataTypes.unshift( dataTypeOrTransport );
					inspect( dataTypeOrTransport );
					return false;
				} else if ( seekingTransport ) {
					return !( selected = dataTypeOrTransport );
				}
			} );
			return selected;
		}
	
		return inspect( options.dataTypes[ 0 ] ) || !inspected[ "*" ] && inspect( "*" );
	}
	
	// A special extend for ajax options
	// that takes "flat" options (not to be deep extended)
	// Fixes #9887
	function ajaxExtend( target, src ) {
		var key, deep,
			flatOptions = jQuery.ajaxSettings.flatOptions || {};
	
		for ( key in src ) {
			if ( src[ key ] !== undefined ) {
				( flatOptions[ key ] ? target : ( deep || ( deep = {} ) ) )[ key ] = src[ key ];
			}
		}
		if ( deep ) {
			jQuery.extend( true, target, deep );
		}
	
		return target;
	}
	
	/* Handles responses to an ajax request:
	 * - finds the right dataType (mediates between content-type and expected dataType)
	 * - returns the corresponding response
	 */
	function ajaxHandleResponses( s, jqXHR, responses ) {
	
		var ct, type, finalDataType, firstDataType,
			contents = s.contents,
			dataTypes = s.dataTypes;
	
		// Remove auto dataType and get content-type in the process
		while ( dataTypes[ 0 ] === "*" ) {
			dataTypes.shift();
			if ( ct === undefined ) {
				ct = s.mimeType || jqXHR.getResponseHeader( "Content-Type" );
			}
		}
	
		// Check if we're dealing with a known content-type
		if ( ct ) {
			for ( type in contents ) {
				if ( contents[ type ] && contents[ type ].test( ct ) ) {
					dataTypes.unshift( type );
					break;
				}
			}
		}
	
		// Check to see if we have a response for the expected dataType
		if ( dataTypes[ 0 ] in responses ) {
			finalDataType = dataTypes[ 0 ];
		} else {
	
			// Try convertible dataTypes
			for ( type in responses ) {
				if ( !dataTypes[ 0 ] || s.converters[ type + " " + dataTypes[ 0 ] ] ) {
					finalDataType = type;
					break;
				}
				if ( !firstDataType ) {
					firstDataType = type;
				}
			}
	
			// Or just use first one
			finalDataType = finalDataType || firstDataType;
		}
	
		// If we found a dataType
		// We add the dataType to the list if needed
		// and return the corresponding response
		if ( finalDataType ) {
			if ( finalDataType !== dataTypes[ 0 ] ) {
				dataTypes.unshift( finalDataType );
			}
			return responses[ finalDataType ];
		}
	}
	
	/* Chain conversions given the request and the original response
	 * Also sets the responseXXX fields on the jqXHR instance
	 */
	function ajaxConvert( s, response, jqXHR, isSuccess ) {
		var conv2, current, conv, tmp, prev,
			converters = {},
	
			// Work with a copy of dataTypes in case we need to modify it for conversion
			dataTypes = s.dataTypes.slice();
	
		// Create converters map with lowercased keys
		if ( dataTypes[ 1 ] ) {
			for ( conv in s.converters ) {
				converters[ conv.toLowerCase() ] = s.converters[ conv ];
			}
		}
	
		current = dataTypes.shift();
	
		// Convert to each sequential dataType
		while ( current ) {
	
			if ( s.responseFields[ current ] ) {
				jqXHR[ s.responseFields[ current ] ] = response;
			}
	
			// Apply the dataFilter if provided
			if ( !prev && isSuccess && s.dataFilter ) {
				response = s.dataFilter( response, s.dataType );
			}
	
			prev = current;
			current = dataTypes.shift();
	
			if ( current ) {
	
			// There's only work to do if current dataType is non-auto
				if ( current === "*" ) {
	
					current = prev;
	
				// Convert response if prev dataType is non-auto and differs from current
				} else if ( prev !== "*" && prev !== current ) {
	
					// Seek a direct converter
					conv = converters[ prev + " " + current ] || converters[ "* " + current ];
	
					// If none found, seek a pair
					if ( !conv ) {
						for ( conv2 in converters ) {
	
							// If conv2 outputs current
							tmp = conv2.split( " " );
							if ( tmp[ 1 ] === current ) {
	
								// If prev can be converted to accepted input
								conv = converters[ prev + " " + tmp[ 0 ] ] ||
									converters[ "* " + tmp[ 0 ] ];
								if ( conv ) {
	
									// Condense equivalence converters
									if ( conv === true ) {
										conv = converters[ conv2 ];
	
									// Otherwise, insert the intermediate dataType
									} else if ( converters[ conv2 ] !== true ) {
										current = tmp[ 0 ];
										dataTypes.unshift( tmp[ 1 ] );
									}
									break;
								}
							}
						}
					}
	
					// Apply converter (if not an equivalence)
					if ( conv !== true ) {
	
						// Unless errors are allowed to bubble, catch and return them
						if ( conv && s.throws ) {
							response = conv( response );
						} else {
							try {
								response = conv( response );
							} catch ( e ) {
								return {
									state: "parsererror",
									error: conv ? e : "No conversion from " + prev + " to " + current
								};
							}
						}
					}
				}
			}
		}
	
		return { state: "success", data: response };
	}
	
	jQuery.extend( {
	
		// Counter for holding the number of active queries
		active: 0,
	
		// Last-Modified header cache for next request
		lastModified: {},
		etag: {},
	
		ajaxSettings: {
			url: location.href,
			type: "GET",
			isLocal: rlocalProtocol.test( location.protocol ),
			global: true,
			processData: true,
			async: true,
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			/*
			timeout: 0,
			data: null,
			dataType: null,
			username: null,
			password: null,
			cache: null,
			throws: false,
			traditional: false,
			headers: {},
			*/
	
			accepts: {
				"*": allTypes,
				text: "text/plain",
				html: "text/html",
				xml: "application/xml, text/xml",
				json: "application/json, text/javascript"
			},
	
			contents: {
				xml: /\bxml\b/,
				html: /\bhtml/,
				json: /\bjson\b/
			},
	
			responseFields: {
				xml: "responseXML",
				text: "responseText",
				json: "responseJSON"
			},
	
			// Data converters
			// Keys separate source (or catchall "*") and destination types with a single space
			converters: {
	
				// Convert anything to text
				"* text": String,
	
				// Text to html (true = no transformation)
				"text html": true,
	
				// Evaluate text as a json expression
				"text json": jQuery.parseJSON,
	
				// Parse text as xml
				"text xml": jQuery.parseXML
			},
	
			// For options that shouldn't be deep extended:
			// you can add your own custom options here if
			// and when you create one that shouldn't be
			// deep extended (see ajaxExtend)
			flatOptions: {
				url: true,
				context: true
			}
		},
	
		// Creates a full fledged settings object into target
		// with both ajaxSettings and settings fields.
		// If target is omitted, writes into ajaxSettings.
		ajaxSetup: function( target, settings ) {
			return settings ?
	
				// Building a settings object
				ajaxExtend( ajaxExtend( target, jQuery.ajaxSettings ), settings ) :
	
				// Extending ajaxSettings
				ajaxExtend( jQuery.ajaxSettings, target );
		},
	
		ajaxPrefilter: addToPrefiltersOrTransports( prefilters ),
		ajaxTransport: addToPrefiltersOrTransports( transports ),
	
		// Main method
		ajax: function( url, options ) {
	
			// If url is an object, simulate pre-1.5 signature
			if ( typeof url === "object" ) {
				options = url;
				url = undefined;
			}
	
			// Force options to be an object
			options = options || {};
	
			var transport,
	
				// URL without anti-cache param
				cacheURL,
	
				// Response headers
				responseHeadersString,
				responseHeaders,
	
				// timeout handle
				timeoutTimer,
	
				// Url cleanup var
				urlAnchor,
	
				// To know if global events are to be dispatched
				fireGlobals,
	
				// Loop variable
				i,
	
				// Create the final options object
				s = jQuery.ajaxSetup( {}, options ),
	
				// Callbacks context
				callbackContext = s.context || s,
	
				// Context for global events is callbackContext if it is a DOM node or jQuery collection
				globalEventContext = s.context &&
					( callbackContext.nodeType || callbackContext.jquery ) ?
						jQuery( callbackContext ) :
						jQuery.event,
	
				// Deferreds
				deferred = jQuery.Deferred(),
				completeDeferred = jQuery.Callbacks( "once memory" ),
	
				// Status-dependent callbacks
				statusCode = s.statusCode || {},
	
				// Headers (they are sent all at once)
				requestHeaders = {},
				requestHeadersNames = {},
	
				// The jqXHR state
				state = 0,
	
				// Default abort message
				strAbort = "canceled",
	
				// Fake xhr
				jqXHR = {
					readyState: 0,
	
					// Builds headers hashtable if needed
					getResponseHeader: function( key ) {
						var match;
						if ( state === 2 ) {
							if ( !responseHeaders ) {
								responseHeaders = {};
								while ( ( match = rheaders.exec( responseHeadersString ) ) ) {
									responseHeaders[ match[ 1 ].toLowerCase() ] = match[ 2 ];
								}
							}
							match = responseHeaders[ key.toLowerCase() ];
						}
						return match == null ? null : match;
					},
	
					// Raw string
					getAllResponseHeaders: function() {
						return state === 2 ? responseHeadersString : null;
					},
	
					// Caches the header
					setRequestHeader: function( name, value ) {
						var lname = name.toLowerCase();
						if ( !state ) {
							name = requestHeadersNames[ lname ] = requestHeadersNames[ lname ] || name;
							requestHeaders[ name ] = value;
						}
						return this;
					},
	
					// Overrides response content-type header
					overrideMimeType: function( type ) {
						if ( !state ) {
							s.mimeType = type;
						}
						return this;
					},
	
					// Status-dependent callbacks
					statusCode: function( map ) {
						var code;
						if ( map ) {
							if ( state < 2 ) {
								for ( code in map ) {
	
									// Lazy-add the new callback in a way that preserves old ones
									statusCode[ code ] = [ statusCode[ code ], map[ code ] ];
								}
							} else {
	
								// Execute the appropriate callbacks
								jqXHR.always( map[ jqXHR.status ] );
							}
						}
						return this;
					},
	
					// Cancel the request
					abort: function( statusText ) {
						var finalText = statusText || strAbort;
						if ( transport ) {
							transport.abort( finalText );
						}
						done( 0, finalText );
						return this;
					}
				};
	
			// Attach deferreds
			deferred.promise( jqXHR ).complete = completeDeferred.add;
			jqXHR.success = jqXHR.done;
			jqXHR.error = jqXHR.fail;
	
			// Remove hash character (#7531: and string promotion)
			// Add protocol if not provided (prefilters might expect it)
			// Handle falsy url in the settings object (#10093: consistency with old signature)
			// We also use the url parameter if available
			s.url = ( ( url || s.url || location.href ) + "" ).replace( rhash, "" )
				.replace( rprotocol, location.protocol + "//" );
	
			// Alias method option to type as per ticket #12004
			s.type = options.method || options.type || s.method || s.type;
	
			// Extract dataTypes list
			s.dataTypes = jQuery.trim( s.dataType || "*" ).toLowerCase().match( rnotwhite ) || [ "" ];
	
			// A cross-domain request is in order when the origin doesn't match the current origin.
			if ( s.crossDomain == null ) {
				urlAnchor = document.createElement( "a" );
	
				// Support: IE8-11+
				// IE throws exception if url is malformed, e.g. http://example.com:80x/
				try {
					urlAnchor.href = s.url;
	
					// Support: IE8-11+
					// Anchor's host property isn't correctly set when s.url is relative
					urlAnchor.href = urlAnchor.href;
					s.crossDomain = originAnchor.protocol + "//" + originAnchor.host !==
						urlAnchor.protocol + "//" + urlAnchor.host;
				} catch ( e ) {
	
					// If there is an error parsing the URL, assume it is crossDomain,
					// it can be rejected by the transport if it is invalid
					s.crossDomain = true;
				}
			}
	
			// Convert data if not already a string
			if ( s.data && s.processData && typeof s.data !== "string" ) {
				s.data = jQuery.param( s.data, s.traditional );
			}
	
			// Apply prefilters
			inspectPrefiltersOrTransports( prefilters, s, options, jqXHR );
	
			// If request was aborted inside a prefilter, stop there
			if ( state === 2 ) {
				return jqXHR;
			}
	
			// We can fire global events as of now if asked to
			// Don't fire events if jQuery.event is undefined in an AMD-usage scenario (#15118)
			fireGlobals = jQuery.event && s.global;
	
			// Watch for a new set of requests
			if ( fireGlobals && jQuery.active++ === 0 ) {
				jQuery.event.trigger( "ajaxStart" );
			}
	
			// Uppercase the type
			s.type = s.type.toUpperCase();
	
			// Determine if request has content
			s.hasContent = !rnoContent.test( s.type );
	
			// Save the URL in case we're toying with the If-Modified-Since
			// and/or If-None-Match header later on
			cacheURL = s.url;
	
			// More options handling for requests with no content
			if ( !s.hasContent ) {
	
				// If data is available, append data to url
				if ( s.data ) {
					cacheURL = ( s.url += ( rquery.test( cacheURL ) ? "&" : "?" ) + s.data );
	
					// #9682: remove data so that it's not used in an eventual retry
					delete s.data;
				}
	
				// Add anti-cache in url if needed
				if ( s.cache === false ) {
					s.url = rts.test( cacheURL ) ?
	
						// If there is already a '_' parameter, set its value
						cacheURL.replace( rts, "$1_=" + nonce++ ) :
	
						// Otherwise add one to the end
						cacheURL + ( rquery.test( cacheURL ) ? "&" : "?" ) + "_=" + nonce++;
				}
			}
	
			// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
			if ( s.ifModified ) {
				if ( jQuery.lastModified[ cacheURL ] ) {
					jqXHR.setRequestHeader( "If-Modified-Since", jQuery.lastModified[ cacheURL ] );
				}
				if ( jQuery.etag[ cacheURL ] ) {
					jqXHR.setRequestHeader( "If-None-Match", jQuery.etag[ cacheURL ] );
				}
			}
	
			// Set the correct header, if data is being sent
			if ( s.data && s.hasContent && s.contentType !== false || options.contentType ) {
				jqXHR.setRequestHeader( "Content-Type", s.contentType );
			}
	
			// Set the Accepts header for the server, depending on the dataType
			jqXHR.setRequestHeader(
				"Accept",
				s.dataTypes[ 0 ] && s.accepts[ s.dataTypes[ 0 ] ] ?
					s.accepts[ s.dataTypes[ 0 ] ] +
						( s.dataTypes[ 0 ] !== "*" ? ", " + allTypes + "; q=0.01" : "" ) :
					s.accepts[ "*" ]
			);
	
			// Check for headers option
			for ( i in s.headers ) {
				jqXHR.setRequestHeader( i, s.headers[ i ] );
			}
	
			// Allow custom headers/mimetypes and early abort
			if ( s.beforeSend &&
				( s.beforeSend.call( callbackContext, jqXHR, s ) === false || state === 2 ) ) {
	
				// Abort if not done already and return
				return jqXHR.abort();
			}
	
			// Aborting is no longer a cancellation
			strAbort = "abort";
	
			// Install callbacks on deferreds
			for ( i in { success: 1, error: 1, complete: 1 } ) {
				jqXHR[ i ]( s[ i ] );
			}
	
			// Get transport
			transport = inspectPrefiltersOrTransports( transports, s, options, jqXHR );
	
			// If no transport, we auto-abort
			if ( !transport ) {
				done( -1, "No Transport" );
			} else {
				jqXHR.readyState = 1;
	
				// Send global event
				if ( fireGlobals ) {
					globalEventContext.trigger( "ajaxSend", [ jqXHR, s ] );
				}
	
				// If request was aborted inside ajaxSend, stop there
				if ( state === 2 ) {
					return jqXHR;
				}
	
				// Timeout
				if ( s.async && s.timeout > 0 ) {
					timeoutTimer = window.setTimeout( function() {
						jqXHR.abort( "timeout" );
					}, s.timeout );
				}
	
				try {
					state = 1;
					transport.send( requestHeaders, done );
				} catch ( e ) {
	
					// Propagate exception as error if not done
					if ( state < 2 ) {
						done( -1, e );
	
					// Simply rethrow otherwise
					} else {
						throw e;
					}
				}
			}
	
			// Callback for when everything is done
			function done( status, nativeStatusText, responses, headers ) {
				var isSuccess, success, error, response, modified,
					statusText = nativeStatusText;
	
				// Called once
				if ( state === 2 ) {
					return;
				}
	
				// State is "done" now
				state = 2;
	
				// Clear timeout if it exists
				if ( timeoutTimer ) {
					window.clearTimeout( timeoutTimer );
				}
	
				// Dereference transport for early garbage collection
				// (no matter how long the jqXHR object will be used)
				transport = undefined;
	
				// Cache response headers
				responseHeadersString = headers || "";
	
				// Set readyState
				jqXHR.readyState = status > 0 ? 4 : 0;
	
				// Determine if successful
				isSuccess = status >= 200 && status < 300 || status === 304;
	
				// Get response data
				if ( responses ) {
					response = ajaxHandleResponses( s, jqXHR, responses );
				}
	
				// Convert no matter what (that way responseXXX fields are always set)
				response = ajaxConvert( s, response, jqXHR, isSuccess );
	
				// If successful, handle type chaining
				if ( isSuccess ) {
	
					// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
					if ( s.ifModified ) {
						modified = jqXHR.getResponseHeader( "Last-Modified" );
						if ( modified ) {
							jQuery.lastModified[ cacheURL ] = modified;
						}
						modified = jqXHR.getResponseHeader( "etag" );
						if ( modified ) {
							jQuery.etag[ cacheURL ] = modified;
						}
					}
	
					// if no content
					if ( status === 204 || s.type === "HEAD" ) {
						statusText = "nocontent";
	
					// if not modified
					} else if ( status === 304 ) {
						statusText = "notmodified";
	
					// If we have data, let's convert it
					} else {
						statusText = response.state;
						success = response.data;
						error = response.error;
						isSuccess = !error;
					}
				} else {
	
					// Extract error from statusText and normalize for non-aborts
					error = statusText;
					if ( status || !statusText ) {
						statusText = "error";
						if ( status < 0 ) {
							status = 0;
						}
					}
				}
	
				// Set data for the fake xhr object
				jqXHR.status = status;
				jqXHR.statusText = ( nativeStatusText || statusText ) + "";
	
				// Success/Error
				if ( isSuccess ) {
					deferred.resolveWith( callbackContext, [ success, statusText, jqXHR ] );
				} else {
					deferred.rejectWith( callbackContext, [ jqXHR, statusText, error ] );
				}
	
				// Status-dependent callbacks
				jqXHR.statusCode( statusCode );
				statusCode = undefined;
	
				if ( fireGlobals ) {
					globalEventContext.trigger( isSuccess ? "ajaxSuccess" : "ajaxError",
						[ jqXHR, s, isSuccess ? success : error ] );
				}
	
				// Complete
				completeDeferred.fireWith( callbackContext, [ jqXHR, statusText ] );
	
				if ( fireGlobals ) {
					globalEventContext.trigger( "ajaxComplete", [ jqXHR, s ] );
	
					// Handle the global AJAX counter
					if ( !( --jQuery.active ) ) {
						jQuery.event.trigger( "ajaxStop" );
					}
				}
			}
	
			return jqXHR;
		},
	
		getJSON: function( url, data, callback ) {
			return jQuery.get( url, data, callback, "json" );
		},
	
		getScript: function( url, callback ) {
			return jQuery.get( url, undefined, callback, "script" );
		}
	} );
	
	jQuery.each( [ "get", "post" ], function( i, method ) {
		jQuery[ method ] = function( url, data, callback, type ) {
	
			// Shift arguments if data argument was omitted
			if ( jQuery.isFunction( data ) ) {
				type = type || callback;
				callback = data;
				data = undefined;
			}
	
			// The url can be an options object (which then must have .url)
			return jQuery.ajax( jQuery.extend( {
				url: url,
				type: method,
				dataType: type,
				data: data,
				success: callback
			}, jQuery.isPlainObject( url ) && url ) );
		};
	} );
	
	
	jQuery._evalUrl = function( url ) {
		return jQuery.ajax( {
			url: url,
	
			// Make this explicit, since user can override this through ajaxSetup (#11264)
			type: "GET",
			dataType: "script",
			async: false,
			global: false,
			"throws": true
		} );
	};
	
	
	jQuery.fn.extend( {
		wrapAll: function( html ) {
			var wrap;
	
			if ( jQuery.isFunction( html ) ) {
				return this.each( function( i ) {
					jQuery( this ).wrapAll( html.call( this, i ) );
				} );
			}
	
			if ( this[ 0 ] ) {
	
				// The elements to wrap the target around
				wrap = jQuery( html, this[ 0 ].ownerDocument ).eq( 0 ).clone( true );
	
				if ( this[ 0 ].parentNode ) {
					wrap.insertBefore( this[ 0 ] );
				}
	
				wrap.map( function() {
					var elem = this;
	
					while ( elem.firstElementChild ) {
						elem = elem.firstElementChild;
					}
	
					return elem;
				} ).append( this );
			}
	
			return this;
		},
	
		wrapInner: function( html ) {
			if ( jQuery.isFunction( html ) ) {
				return this.each( function( i ) {
					jQuery( this ).wrapInner( html.call( this, i ) );
				} );
			}
	
			return this.each( function() {
				var self = jQuery( this ),
					contents = self.contents();
	
				if ( contents.length ) {
					contents.wrapAll( html );
	
				} else {
					self.append( html );
				}
			} );
		},
	
		wrap: function( html ) {
			var isFunction = jQuery.isFunction( html );
	
			return this.each( function( i ) {
				jQuery( this ).wrapAll( isFunction ? html.call( this, i ) : html );
			} );
		},
	
		unwrap: function() {
			return this.parent().each( function() {
				if ( !jQuery.nodeName( this, "body" ) ) {
					jQuery( this ).replaceWith( this.childNodes );
				}
			} ).end();
		}
	} );
	
	
	jQuery.expr.filters.hidden = function( elem ) {
		return !jQuery.expr.filters.visible( elem );
	};
	jQuery.expr.filters.visible = function( elem ) {
	
		// Support: Opera <= 12.12
		// Opera reports offsetWidths and offsetHeights less than zero on some elements
		// Use OR instead of AND as the element is not visible if either is true
		// See tickets #10406 and #13132
		return elem.offsetWidth > 0 || elem.offsetHeight > 0 || elem.getClientRects().length > 0;
	};
	
	
	
	
	var r20 = /%20/g,
		rbracket = /\[\]$/,
		rCRLF = /\r?\n/g,
		rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i,
		rsubmittable = /^(?:input|select|textarea|keygen)/i;
	
	function buildParams( prefix, obj, traditional, add ) {
		var name;
	
		if ( jQuery.isArray( obj ) ) {
	
			// Serialize array item.
			jQuery.each( obj, function( i, v ) {
				if ( traditional || rbracket.test( prefix ) ) {
	
					// Treat each array item as a scalar.
					add( prefix, v );
	
				} else {
	
					// Item is non-scalar (array or object), encode its numeric index.
					buildParams(
						prefix + "[" + ( typeof v === "object" && v != null ? i : "" ) + "]",
						v,
						traditional,
						add
					);
				}
			} );
	
		} else if ( !traditional && jQuery.type( obj ) === "object" ) {
	
			// Serialize object item.
			for ( name in obj ) {
				buildParams( prefix + "[" + name + "]", obj[ name ], traditional, add );
			}
	
		} else {
	
			// Serialize scalar item.
			add( prefix, obj );
		}
	}
	
	// Serialize an array of form elements or a set of
	// key/values into a query string
	jQuery.param = function( a, traditional ) {
		var prefix,
			s = [],
			add = function( key, value ) {
	
				// If value is a function, invoke it and return its value
				value = jQuery.isFunction( value ) ? value() : ( value == null ? "" : value );
				s[ s.length ] = encodeURIComponent( key ) + "=" + encodeURIComponent( value );
			};
	
		// Set traditional to true for jQuery <= 1.3.2 behavior.
		if ( traditional === undefined ) {
			traditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;
		}
	
		// If an array was passed in, assume that it is an array of form elements.
		if ( jQuery.isArray( a ) || ( a.jquery && !jQuery.isPlainObject( a ) ) ) {
	
			// Serialize the form elements
			jQuery.each( a, function() {
				add( this.name, this.value );
			} );
	
		} else {
	
			// If traditional, encode the "old" way (the way 1.3.2 or older
			// did it), otherwise encode params recursively.
			for ( prefix in a ) {
				buildParams( prefix, a[ prefix ], traditional, add );
			}
		}
	
		// Return the resulting serialization
		return s.join( "&" ).replace( r20, "+" );
	};
	
	jQuery.fn.extend( {
		serialize: function() {
			return jQuery.param( this.serializeArray() );
		},
		serializeArray: function() {
			return this.map( function() {
	
				// Can add propHook for "elements" to filter or add form elements
				var elements = jQuery.prop( this, "elements" );
				return elements ? jQuery.makeArray( elements ) : this;
			} )
			.filter( function() {
				var type = this.type;
	
				// Use .is( ":disabled" ) so that fieldset[disabled] works
				return this.name && !jQuery( this ).is( ":disabled" ) &&
					rsubmittable.test( this.nodeName ) && !rsubmitterTypes.test( type ) &&
					( this.checked || !rcheckableType.test( type ) );
			} )
			.map( function( i, elem ) {
				var val = jQuery( this ).val();
	
				return val == null ?
					null :
					jQuery.isArray( val ) ?
						jQuery.map( val, function( val ) {
							return { name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
						} ) :
						{ name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
			} ).get();
		}
	} );
	
	
	jQuery.ajaxSettings.xhr = function() {
		try {
			return new window.XMLHttpRequest();
		} catch ( e ) {}
	};
	
	var xhrSuccessStatus = {
	
			// File protocol always yields status code 0, assume 200
			0: 200,
	
			// Support: IE9
			// #1450: sometimes IE returns 1223 when it should be 204
			1223: 204
		},
		xhrSupported = jQuery.ajaxSettings.xhr();
	
	support.cors = !!xhrSupported && ( "withCredentials" in xhrSupported );
	support.ajax = xhrSupported = !!xhrSupported;
	
	jQuery.ajaxTransport( function( options ) {
		var callback, errorCallback;
	
		// Cross domain only allowed if supported through XMLHttpRequest
		if ( support.cors || xhrSupported && !options.crossDomain ) {
			return {
				send: function( headers, complete ) {
					var i,
						xhr = options.xhr();
	
					xhr.open(
						options.type,
						options.url,
						options.async,
						options.username,
						options.password
					);
	
					// Apply custom fields if provided
					if ( options.xhrFields ) {
						for ( i in options.xhrFields ) {
							xhr[ i ] = options.xhrFields[ i ];
						}
					}
	
					// Override mime type if needed
					if ( options.mimeType && xhr.overrideMimeType ) {
						xhr.overrideMimeType( options.mimeType );
					}
	
					// X-Requested-With header
					// For cross-domain requests, seeing as conditions for a preflight are
					// akin to a jigsaw puzzle, we simply never set it to be sure.
					// (it can always be set on a per-request basis or even using ajaxSetup)
					// For same-domain requests, won't change header if already provided.
					if ( !options.crossDomain && !headers[ "X-Requested-With" ] ) {
						headers[ "X-Requested-With" ] = "XMLHttpRequest";
					}
	
					// Set headers
					for ( i in headers ) {
						xhr.setRequestHeader( i, headers[ i ] );
					}
	
					// Callback
					callback = function( type ) {
						return function() {
							if ( callback ) {
								callback = errorCallback = xhr.onload =
									xhr.onerror = xhr.onabort = xhr.onreadystatechange = null;
	
								if ( type === "abort" ) {
									xhr.abort();
								} else if ( type === "error" ) {
	
									// Support: IE9
									// On a manual native abort, IE9 throws
									// errors on any property access that is not readyState
									if ( typeof xhr.status !== "number" ) {
										complete( 0, "error" );
									} else {
										complete(
	
											// File: protocol always yields status 0; see #8605, #14207
											xhr.status,
											xhr.statusText
										);
									}
								} else {
									complete(
										xhrSuccessStatus[ xhr.status ] || xhr.status,
										xhr.statusText,
	
										// Support: IE9 only
										// IE9 has no XHR2 but throws on binary (trac-11426)
										// For XHR2 non-text, let the caller handle it (gh-2498)
										( xhr.responseType || "text" ) !== "text"  ||
										typeof xhr.responseText !== "string" ?
											{ binary: xhr.response } :
											{ text: xhr.responseText },
										xhr.getAllResponseHeaders()
									);
								}
							}
						};
					};
	
					// Listen to events
					xhr.onload = callback();
					errorCallback = xhr.onerror = callback( "error" );
	
					// Support: IE9
					// Use onreadystatechange to replace onabort
					// to handle uncaught aborts
					if ( xhr.onabort !== undefined ) {
						xhr.onabort = errorCallback;
					} else {
						xhr.onreadystatechange = function() {
	
							// Check readyState before timeout as it changes
							if ( xhr.readyState === 4 ) {
	
								// Allow onerror to be called first,
								// but that will not handle a native abort
								// Also, save errorCallback to a variable
								// as xhr.onerror cannot be accessed
								window.setTimeout( function() {
									if ( callback ) {
										errorCallback();
									}
								} );
							}
						};
					}
	
					// Create the abort callback
					callback = callback( "abort" );
	
					try {
	
						// Do send the request (this may raise an exception)
						xhr.send( options.hasContent && options.data || null );
					} catch ( e ) {
	
						// #14683: Only rethrow if this hasn't been notified as an error yet
						if ( callback ) {
							throw e;
						}
					}
				},
	
				abort: function() {
					if ( callback ) {
						callback();
					}
				}
			};
		}
	} );
	
	
	
	
	// Install script dataType
	jQuery.ajaxSetup( {
		accepts: {
			script: "text/javascript, application/javascript, " +
				"application/ecmascript, application/x-ecmascript"
		},
		contents: {
			script: /\b(?:java|ecma)script\b/
		},
		converters: {
			"text script": function( text ) {
				jQuery.globalEval( text );
				return text;
			}
		}
	} );
	
	// Handle cache's special case and crossDomain
	jQuery.ajaxPrefilter( "script", function( s ) {
		if ( s.cache === undefined ) {
			s.cache = false;
		}
		if ( s.crossDomain ) {
			s.type = "GET";
		}
	} );
	
	// Bind script tag hack transport
	jQuery.ajaxTransport( "script", function( s ) {
	
		// This transport only deals with cross domain requests
		if ( s.crossDomain ) {
			var script, callback;
			return {
				send: function( _, complete ) {
					script = jQuery( "<script>" ).prop( {
						charset: s.scriptCharset,
						src: s.url
					} ).on(
						"load error",
						callback = function( evt ) {
							script.remove();
							callback = null;
							if ( evt ) {
								complete( evt.type === "error" ? 404 : 200, evt.type );
							}
						}
					);
	
					// Use native DOM manipulation to avoid our domManip AJAX trickery
					document.head.appendChild( script[ 0 ] );
				},
				abort: function() {
					if ( callback ) {
						callback();
					}
				}
			};
		}
	} );
	
	
	
	
	var oldCallbacks = [],
		rjsonp = /(=)\?(?=&|$)|\?\?/;
	
	// Default jsonp settings
	jQuery.ajaxSetup( {
		jsonp: "callback",
		jsonpCallback: function() {
			var callback = oldCallbacks.pop() || ( jQuery.expando + "_" + ( nonce++ ) );
			this[ callback ] = true;
			return callback;
		}
	} );
	
	// Detect, normalize options and install callbacks for jsonp requests
	jQuery.ajaxPrefilter( "json jsonp", function( s, originalSettings, jqXHR ) {
	
		var callbackName, overwritten, responseContainer,
			jsonProp = s.jsonp !== false && ( rjsonp.test( s.url ) ?
				"url" :
				typeof s.data === "string" &&
					( s.contentType || "" )
						.indexOf( "application/x-www-form-urlencoded" ) === 0 &&
					rjsonp.test( s.data ) && "data"
			);
	
		// Handle iff the expected data type is "jsonp" or we have a parameter to set
		if ( jsonProp || s.dataTypes[ 0 ] === "jsonp" ) {
	
			// Get callback name, remembering preexisting value associated with it
			callbackName = s.jsonpCallback = jQuery.isFunction( s.jsonpCallback ) ?
				s.jsonpCallback() :
				s.jsonpCallback;
	
			// Insert callback into url or form data
			if ( jsonProp ) {
				s[ jsonProp ] = s[ jsonProp ].replace( rjsonp, "$1" + callbackName );
			} else if ( s.jsonp !== false ) {
				s.url += ( rquery.test( s.url ) ? "&" : "?" ) + s.jsonp + "=" + callbackName;
			}
	
			// Use data converter to retrieve json after script execution
			s.converters[ "script json" ] = function() {
				if ( !responseContainer ) {
					jQuery.error( callbackName + " was not called" );
				}
				return responseContainer[ 0 ];
			};
	
			// Force json dataType
			s.dataTypes[ 0 ] = "json";
	
			// Install callback
			overwritten = window[ callbackName ];
			window[ callbackName ] = function() {
				responseContainer = arguments;
			};
	
			// Clean-up function (fires after converters)
			jqXHR.always( function() {
	
				// If previous value didn't exist - remove it
				if ( overwritten === undefined ) {
					jQuery( window ).removeProp( callbackName );
	
				// Otherwise restore preexisting value
				} else {
					window[ callbackName ] = overwritten;
				}
	
				// Save back as free
				if ( s[ callbackName ] ) {
	
					// Make sure that re-using the options doesn't screw things around
					s.jsonpCallback = originalSettings.jsonpCallback;
	
					// Save the callback name for future use
					oldCallbacks.push( callbackName );
				}
	
				// Call if it was a function and we have a response
				if ( responseContainer && jQuery.isFunction( overwritten ) ) {
					overwritten( responseContainer[ 0 ] );
				}
	
				responseContainer = overwritten = undefined;
			} );
	
			// Delegate to script
			return "script";
		}
	} );
	
	
	
	
	// Argument "data" should be string of html
	// context (optional): If specified, the fragment will be created in this context,
	// defaults to document
	// keepScripts (optional): If true, will include scripts passed in the html string
	jQuery.parseHTML = function( data, context, keepScripts ) {
		if ( !data || typeof data !== "string" ) {
			return null;
		}
		if ( typeof context === "boolean" ) {
			keepScripts = context;
			context = false;
		}
		context = context || document;
	
		var parsed = rsingleTag.exec( data ),
			scripts = !keepScripts && [];
	
		// Single tag
		if ( parsed ) {
			return [ context.createElement( parsed[ 1 ] ) ];
		}
	
		parsed = buildFragment( [ data ], context, scripts );
	
		if ( scripts && scripts.length ) {
			jQuery( scripts ).remove();
		}
	
		return jQuery.merge( [], parsed.childNodes );
	};
	
	
	// Keep a copy of the old load method
	var _load = jQuery.fn.load;
	
	/**
	 * Load a url into a page
	 */
	jQuery.fn.load = function( url, params, callback ) {
		if ( typeof url !== "string" && _load ) {
			return _load.apply( this, arguments );
		}
	
		var selector, type, response,
			self = this,
			off = url.indexOf( " " );
	
		if ( off > -1 ) {
			selector = jQuery.trim( url.slice( off ) );
			url = url.slice( 0, off );
		}
	
		// If it's a function
		if ( jQuery.isFunction( params ) ) {
	
			// We assume that it's the callback
			callback = params;
			params = undefined;
	
		// Otherwise, build a param string
		} else if ( params && typeof params === "object" ) {
			type = "POST";
		}
	
		// If we have elements to modify, make the request
		if ( self.length > 0 ) {
			jQuery.ajax( {
				url: url,
	
				// If "type" variable is undefined, then "GET" method will be used.
				// Make value of this field explicit since
				// user can override it through ajaxSetup method
				type: type || "GET",
				dataType: "html",
				data: params
			} ).done( function( responseText ) {
	
				// Save response for use in complete callback
				response = arguments;
	
				self.html( selector ?
	
					// If a selector was specified, locate the right elements in a dummy div
					// Exclude scripts to avoid IE 'Permission Denied' errors
					jQuery( "<div>" ).append( jQuery.parseHTML( responseText ) ).find( selector ) :
	
					// Otherwise use the full result
					responseText );
	
			// If the request succeeds, this function gets "data", "status", "jqXHR"
			// but they are ignored because response was set above.
			// If it fails, this function gets "jqXHR", "status", "error"
			} ).always( callback && function( jqXHR, status ) {
				self.each( function() {
					callback.apply( this, response || [ jqXHR.responseText, status, jqXHR ] );
				} );
			} );
		}
	
		return this;
	};
	
	
	
	
	// Attach a bunch of functions for handling common AJAX events
	jQuery.each( [
		"ajaxStart",
		"ajaxStop",
		"ajaxComplete",
		"ajaxError",
		"ajaxSuccess",
		"ajaxSend"
	], function( i, type ) {
		jQuery.fn[ type ] = function( fn ) {
			return this.on( type, fn );
		};
	} );
	
	
	
	
	jQuery.expr.filters.animated = function( elem ) {
		return jQuery.grep( jQuery.timers, function( fn ) {
			return elem === fn.elem;
		} ).length;
	};
	
	
	
	
	/**
	 * Gets a window from an element
	 */
	function getWindow( elem ) {
		return jQuery.isWindow( elem ) ? elem : elem.nodeType === 9 && elem.defaultView;
	}
	
	jQuery.offset = {
		setOffset: function( elem, options, i ) {
			var curPosition, curLeft, curCSSTop, curTop, curOffset, curCSSLeft, calculatePosition,
				position = jQuery.css( elem, "position" ),
				curElem = jQuery( elem ),
				props = {};
	
			// Set position first, in-case top/left are set even on static elem
			if ( position === "static" ) {
				elem.style.position = "relative";
			}
	
			curOffset = curElem.offset();
			curCSSTop = jQuery.css( elem, "top" );
			curCSSLeft = jQuery.css( elem, "left" );
			calculatePosition = ( position === "absolute" || position === "fixed" ) &&
				( curCSSTop + curCSSLeft ).indexOf( "auto" ) > -1;
	
			// Need to be able to calculate position if either
			// top or left is auto and position is either absolute or fixed
			if ( calculatePosition ) {
				curPosition = curElem.position();
				curTop = curPosition.top;
				curLeft = curPosition.left;
	
			} else {
				curTop = parseFloat( curCSSTop ) || 0;
				curLeft = parseFloat( curCSSLeft ) || 0;
			}
	
			if ( jQuery.isFunction( options ) ) {
	
				// Use jQuery.extend here to allow modification of coordinates argument (gh-1848)
				options = options.call( elem, i, jQuery.extend( {}, curOffset ) );
			}
	
			if ( options.top != null ) {
				props.top = ( options.top - curOffset.top ) + curTop;
			}
			if ( options.left != null ) {
				props.left = ( options.left - curOffset.left ) + curLeft;
			}
	
			if ( "using" in options ) {
				options.using.call( elem, props );
	
			} else {
				curElem.css( props );
			}
		}
	};
	
	jQuery.fn.extend( {
		offset: function( options ) {
			if ( arguments.length ) {
				return options === undefined ?
					this :
					this.each( function( i ) {
						jQuery.offset.setOffset( this, options, i );
					} );
			}
	
			var docElem, win,
				elem = this[ 0 ],
				box = { top: 0, left: 0 },
				doc = elem && elem.ownerDocument;
	
			if ( !doc ) {
				return;
			}
	
			docElem = doc.documentElement;
	
			// Make sure it's not a disconnected DOM node
			if ( !jQuery.contains( docElem, elem ) ) {
				return box;
			}
	
			box = elem.getBoundingClientRect();
			win = getWindow( doc );
			return {
				top: box.top + win.pageYOffset - docElem.clientTop,
				left: box.left + win.pageXOffset - docElem.clientLeft
			};
		},
	
		position: function() {
			if ( !this[ 0 ] ) {
				return;
			}
	
			var offsetParent, offset,
				elem = this[ 0 ],
				parentOffset = { top: 0, left: 0 };
	
			// Fixed elements are offset from window (parentOffset = {top:0, left: 0},
			// because it is its only offset parent
			if ( jQuery.css( elem, "position" ) === "fixed" ) {
	
				// Assume getBoundingClientRect is there when computed position is fixed
				offset = elem.getBoundingClientRect();
	
			} else {
	
				// Get *real* offsetParent
				offsetParent = this.offsetParent();
	
				// Get correct offsets
				offset = this.offset();
				if ( !jQuery.nodeName( offsetParent[ 0 ], "html" ) ) {
					parentOffset = offsetParent.offset();
				}
	
				// Add offsetParent borders
				parentOffset.top += jQuery.css( offsetParent[ 0 ], "borderTopWidth", true );
				parentOffset.left += jQuery.css( offsetParent[ 0 ], "borderLeftWidth", true );
			}
	
			// Subtract parent offsets and element margins
			return {
				top: offset.top - parentOffset.top - jQuery.css( elem, "marginTop", true ),
				left: offset.left - parentOffset.left - jQuery.css( elem, "marginLeft", true )
			};
		},
	
		// This method will return documentElement in the following cases:
		// 1) For the element inside the iframe without offsetParent, this method will return
		//    documentElement of the parent window
		// 2) For the hidden or detached element
		// 3) For body or html element, i.e. in case of the html node - it will return itself
		//
		// but those exceptions were never presented as a real life use-cases
		// and might be considered as more preferable results.
		//
		// This logic, however, is not guaranteed and can change at any point in the future
		offsetParent: function() {
			return this.map( function() {
				var offsetParent = this.offsetParent;
	
				while ( offsetParent && jQuery.css( offsetParent, "position" ) === "static" ) {
					offsetParent = offsetParent.offsetParent;
				}
	
				return offsetParent || documentElement;
			} );
		}
	} );
	
	// Create scrollLeft and scrollTop methods
	jQuery.each( { scrollLeft: "pageXOffset", scrollTop: "pageYOffset" }, function( method, prop ) {
		var top = "pageYOffset" === prop;
	
		jQuery.fn[ method ] = function( val ) {
			return access( this, function( elem, method, val ) {
				var win = getWindow( elem );
	
				if ( val === undefined ) {
					return win ? win[ prop ] : elem[ method ];
				}
	
				if ( win ) {
					win.scrollTo(
						!top ? val : win.pageXOffset,
						top ? val : win.pageYOffset
					);
	
				} else {
					elem[ method ] = val;
				}
			}, method, val, arguments.length );
		};
	} );
	
	// Support: Safari<7-8+, Chrome<37-44+
	// Add the top/left cssHooks using jQuery.fn.position
	// Webkit bug: https://bugs.webkit.org/show_bug.cgi?id=29084
	// Blink bug: https://code.google.com/p/chromium/issues/detail?id=229280
	// getComputedStyle returns percent when specified for top/left/bottom/right;
	// rather than make the css module depend on the offset module, just check for it here
	jQuery.each( [ "top", "left" ], function( i, prop ) {
		jQuery.cssHooks[ prop ] = addGetHookIf( support.pixelPosition,
			function( elem, computed ) {
				if ( computed ) {
					computed = curCSS( elem, prop );
	
					// If curCSS returns percentage, fallback to offset
					return rnumnonpx.test( computed ) ?
						jQuery( elem ).position()[ prop ] + "px" :
						computed;
				}
			}
		);
	} );
	
	
	// Create innerHeight, innerWidth, height, width, outerHeight and outerWidth methods
	jQuery.each( { Height: "height", Width: "width" }, function( name, type ) {
		jQuery.each( { padding: "inner" + name, content: type, "": "outer" + name },
			function( defaultExtra, funcName ) {
	
			// Margin is only for outerHeight, outerWidth
			jQuery.fn[ funcName ] = function( margin, value ) {
				var chainable = arguments.length && ( defaultExtra || typeof margin !== "boolean" ),
					extra = defaultExtra || ( margin === true || value === true ? "margin" : "border" );
	
				return access( this, function( elem, type, value ) {
					var doc;
	
					if ( jQuery.isWindow( elem ) ) {
	
						// As of 5/8/2012 this will yield incorrect results for Mobile Safari, but there
						// isn't a whole lot we can do. See pull request at this URL for discussion:
						// https://github.com/jquery/jquery/pull/764
						return elem.document.documentElement[ "client" + name ];
					}
	
					// Get document width or height
					if ( elem.nodeType === 9 ) {
						doc = elem.documentElement;
	
						// Either scroll[Width/Height] or offset[Width/Height] or client[Width/Height],
						// whichever is greatest
						return Math.max(
							elem.body[ "scroll" + name ], doc[ "scroll" + name ],
							elem.body[ "offset" + name ], doc[ "offset" + name ],
							doc[ "client" + name ]
						);
					}
	
					return value === undefined ?
	
						// Get width or height on the element, requesting but not forcing parseFloat
						jQuery.css( elem, type, extra ) :
	
						// Set width or height on the element
						jQuery.style( elem, type, value, extra );
				}, type, chainable ? margin : undefined, chainable, null );
			};
		} );
	} );
	
	
	jQuery.fn.extend( {
	
		bind: function( types, data, fn ) {
			return this.on( types, null, data, fn );
		},
		unbind: function( types, fn ) {
			return this.off( types, null, fn );
		},
	
		delegate: function( selector, types, data, fn ) {
			return this.on( types, selector, data, fn );
		},
		undelegate: function( selector, types, fn ) {
	
			// ( namespace ) or ( selector, types [, fn] )
			return arguments.length === 1 ?
				this.off( selector, "**" ) :
				this.off( types, selector || "**", fn );
		},
		size: function() {
			return this.length;
		}
	} );
	
	jQuery.fn.andSelf = jQuery.fn.addBack;
	
	
	
	
	// Register as a named AMD module, since jQuery can be concatenated with other
	// files that may use define, but not via a proper concatenation script that
	// understands anonymous AMD modules. A named AMD is safest and most robust
	// way to register. Lowercase jquery is used because AMD module names are
	// derived from file names, and jQuery is normally delivered in a lowercase
	// file name. Do this after creating the global so that if an AMD module wants
	// to call noConflict to hide this version of jQuery, it will work.
	
	// Note that for maximum portability, libraries that are not jQuery should
	// declare themselves as anonymous modules, and avoid setting a global if an
	// AMD loader is present. jQuery is a special case. For more information, see
	// https://github.com/jrburke/requirejs/wiki/Updating-existing-libraries#wiki-anon
	
	if ( true ) {
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function() {
			return jQuery;
		}.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	}
	
	
	
	var
	
		// Map over jQuery in case of overwrite
		_jQuery = window.jQuery,
	
		// Map over the $ in case of overwrite
		_$ = window.$;
	
	jQuery.noConflict = function( deep ) {
		if ( window.$ === jQuery ) {
			window.$ = _$;
		}
	
		if ( deep && window.jQuery === jQuery ) {
			window.jQuery = _jQuery;
		}
	
		return jQuery;
	};
	
	// Expose jQuery and $ identifiers, even in AMD
	// (#7102#comment:10, https://github.com/jquery/jquery/pull/557)
	// and CommonJS for browser emulators (#13566)
	if ( !noGlobal ) {
		window.jQuery = window.$ = jQuery;
	}
	
	return jQuery;
	}));


/***/ }),
/* 6 */
/***/ (function(module, exports) {

	'use strict';
	
	/*
	 * Spinner functions.
	 */
	$(document).ready(function () {
	  /**
	   * Create jQuery function.
	   */
	  (function ($) {
	    /*
	     * Adding spinner into element.
	     * @param spinnerId{String} - id of spinner
	     */
	    $.fn.spinnerAdd = function (spinnerId) {
	      var loaderClass = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'loader';
	
	      if (!spinnerId) spinnerId = 'js_spinner';
	
	      $(this).append('<div id="' + spinnerId + '" class="loaderWrapper"><div class="' + loaderClass + '"></div></div>');
	    };
	    /*
	     * Remove spinner.
	     * @param{String} spinnerId - id of spinner
	     */
	    $.spinnerRemove = function (spinnerId) {
	      if (!spinnerId) spinnerId = 'js_spinner';
	      $('#' + spinnerId + '.loaderWrapper').remove();
	    };
	    /*
	     * Adding spinner into body.
	     * @param{String} spinnerId - id of spinner
	     */
	    $.spinnerAdd = function (spinnerId) {
	      var loaderClass = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'loader';
	
	      if (!spinnerId) spinnerId = 'js_spinner';
	      $('body').append('<div id="' + spinnerId + '" class="loaderWrapper"><div class="' + loaderClass + '"></div></div>');
	    };
	  })(jQuery);
	});

/***/ }),
/* 7 */
/***/ (function(module, exports) {

	/*! AdminLTE app.js
	* ================
	* Main JS application file for AdminLTE v2. This file
	* should be included in all pages. It controls some layout
	* options and implements exclusive AdminLTE plugins.
	*
	* @Author  Almsaeed Studio
	* @Support <https://www.almsaeedstudio.com>
	* @Email   <abdullah@almsaeedstudio.com>
	* @version 2.4.8
	* @repository git://github.com/almasaeed2010/AdminLTE.git
	* @license MIT <http://opensource.org/licenses/MIT>
	*/
	
	// Make sure jQuery has been loaded
	if (typeof jQuery === 'undefined') {
	throw new Error('AdminLTE requires jQuery')
	}
	
	/* BoxRefresh()
	 * =========
	 * Adds AJAX content control to a box.
	 *
	 * @Usage: $('#my-box').boxRefresh(options)
	 *         or add [data-widget="box-refresh"] to the box element
	 *         Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.boxrefresh';
	
	  var Default = {
	    source         : '',
	    params         : {},
	    trigger        : '.refresh-btn',
	    content        : '.box-body',
	    loadInContent  : true,
	    responseType   : '',
	    overlayTemplate: '<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>',
	    onLoadStart    : function () {
	    },
	    onLoadDone     : function (response) {
	      return response;
	    }
	  };
	
	  var Selector = {
	    data: '[data-widget="box-refresh"]'
	  };
	
	  // BoxRefresh Class Definition
	  // =========================
	  var BoxRefresh = function (element, options) {
	    this.element  = element;
	    this.options  = options;
	    this.$overlay = $(options.overlay);
	
	    if (options.source === '') {
	      throw new Error('Source url was not defined. Please specify a url in your BoxRefresh source option.');
	    }
	
	    this._setUpListeners();
	    this.load();
	  };
	
	  BoxRefresh.prototype.load = function () {
	    this._addOverlay();
	    this.options.onLoadStart.call($(this));
	
	    $.get(this.options.source, this.options.params, function (response) {
	      if (this.options.loadInContent) {
	        $(this.options.content).html(response);
	      }
	      this.options.onLoadDone.call($(this), response);
	      this._removeOverlay();
	    }.bind(this), this.options.responseType !== '' && this.options.responseType);
	  };
	
	  // Private
	
	  BoxRefresh.prototype._setUpListeners = function () {
	    $(this.element).on('click', Selector.trigger, function (event) {
	      if (event) event.preventDefault();
	      this.load();
	    }.bind(this));
	  };
	
	  BoxRefresh.prototype._addOverlay = function () {
	    $(this.element).append(this.$overlay);
	  };
	
	  BoxRefresh.prototype._removeOverlay = function () {
	    $(this.element).remove(this.$overlay);
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, (data = new BoxRefresh($this, options)));
	      }
	
	      if (typeof data == 'string') {
	        if (typeof data[option] == 'undefined') {
	          throw new Error('No method named ' + option);
	        }
	        data[option]();
	      }
	    });
	  }
	
	  var old = $.fn.boxRefresh;
	
	  $.fn.boxRefresh             = Plugin;
	  $.fn.boxRefresh.Constructor = BoxRefresh;
	
	  // No Conflict Mode
	  // ================
	  $.fn.boxRefresh.noConflict = function () {
	    $.fn.boxRefresh = old;
	    return this;
	  };
	
	  // BoxRefresh Data API
	  // =================
	  $(window).on('load', function () {
	    $(Selector.data).each(function () {
	      Plugin.call($(this));
	    });
	  });
	
	}(jQuery);
	
	
	/* BoxWidget()
	 * ======
	 * Adds box widget functions to boxes.
	 *
	 * @Usage: $('.my-box').boxWidget(options)
	 *         This plugin auto activates on any element using the `.box` class
	 *         Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.boxwidget';
	
	  var Default = {
	    animationSpeed : 500,
	    collapseTrigger: '[data-widget="collapse"]',
	    removeTrigger  : '[data-widget="remove"]',
	    collapseIcon   : 'fa-minus',
	    expandIcon     : 'fa-plus',
	    removeIcon     : 'fa-times'
	  };
	
	  var Selector = {
	    data     : '.box',
	    collapsed: '.collapsed-box',
	    header   : '.box-header',
	    body     : '.box-body',
	    footer   : '.box-footer',
	    tools    : '.box-tools'
	  };
	
	  var ClassName = {
	    collapsed: 'collapsed-box'
	  };
	
	  var Event = {
	    collapsed: 'collapsed.boxwidget',
	    expanded : 'expanded.boxwidget',
	    removed  : 'removed.boxwidget'
	  };
	
	  // BoxWidget Class Definition
	  // =====================
	  var BoxWidget = function (element, options) {
	    this.element = element;
	    this.options = options;
	
	    this._setUpListeners();
	  };
	
	  BoxWidget.prototype.toggle = function () {
	    var isOpen = !$(this.element).is(Selector.collapsed);
	
	    if (isOpen) {
	      this.collapse();
	    } else {
	      this.expand();
	    }
	  };
	
	  BoxWidget.prototype.expand = function () {
	    var expandedEvent = $.Event(Event.expanded);
	    var collapseIcon  = this.options.collapseIcon;
	    var expandIcon    = this.options.expandIcon;
	
	    $(this.element).removeClass(ClassName.collapsed);
	
	    $(this.element)
	      .children(Selector.header + ', ' + Selector.body + ', ' + Selector.footer)
	      .children(Selector.tools)
	      .find('.' + expandIcon)
	      .removeClass(expandIcon)
	      .addClass(collapseIcon);
	
	    $(this.element).children(Selector.body + ', ' + Selector.footer)
	      .slideDown(this.options.animationSpeed, function () {
	        $(this.element).trigger(expandedEvent);
	      }.bind(this));
	  };
	
	  BoxWidget.prototype.collapse = function () {
	    var collapsedEvent = $.Event(Event.collapsed);
	    var collapseIcon   = this.options.collapseIcon;
	    var expandIcon     = this.options.expandIcon;
	
	    $(this.element)
	      .children(Selector.header + ', ' + Selector.body + ', ' + Selector.footer)
	      .children(Selector.tools)
	      .find('.' + collapseIcon)
	      .removeClass(collapseIcon)
	      .addClass(expandIcon);
	
	    $(this.element).children(Selector.body + ', ' + Selector.footer)
	      .slideUp(this.options.animationSpeed, function () {
	        $(this.element).addClass(ClassName.collapsed);
	        $(this.element).trigger(collapsedEvent);
	      }.bind(this));
	  };
	
	  BoxWidget.prototype.remove = function () {
	    var removedEvent = $.Event(Event.removed);
	
	    $(this.element).slideUp(this.options.animationSpeed, function () {
	      $(this.element).trigger(removedEvent);
	      $(this.element).remove();
	    }.bind(this));
	  };
	
	  // Private
	
	  BoxWidget.prototype._setUpListeners = function () {
	    var that = this;
	
	    $(this.element).on('click', this.options.collapseTrigger, function (event) {
	      if (event) event.preventDefault();
	      that.toggle($(this));
	      return false;
	    });
	
	    $(this.element).on('click', this.options.removeTrigger, function (event) {
	      if (event) event.preventDefault();
	      that.remove($(this));
	      return false;
	    });
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, (data = new BoxWidget($this, options)));
	      }
	
	      if (typeof option == 'string') {
	        if (typeof data[option] == 'undefined') {
	          throw new Error('No method named ' + option);
	        }
	        data[option]();
	      }
	    });
	  }
	
	  var old = $.fn.boxWidget;
	
	  $.fn.boxWidget             = Plugin;
	  $.fn.boxWidget.Constructor = BoxWidget;
	
	  // No Conflict Mode
	  // ================
	  $.fn.boxWidget.noConflict = function () {
	    $.fn.boxWidget = old;
	    return this;
	  };
	
	  // BoxWidget Data API
	  // ==================
	  $(window).on('load', function () {
	    $(Selector.data).each(function () {
	      Plugin.call($(this));
	    });
	  });
	}(jQuery);
	
	
	/* ControlSidebar()
	 * ===============
	 * Toggles the state of the control sidebar
	 *
	 * @Usage: $('#control-sidebar-trigger').controlSidebar(options)
	 *         or add [data-toggle="control-sidebar"] to the trigger
	 *         Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.controlsidebar';
	
	  var Default = {
	    slide: true
	  };
	
	  var Selector = {
	    sidebar: '.control-sidebar',
	    data   : '[data-toggle="control-sidebar"]',
	    open   : '.control-sidebar-open',
	    bg     : '.control-sidebar-bg',
	    wrapper: '.wrapper',
	    content: '.content-wrapper',
	    boxed  : '.layout-boxed'
	  };
	
	  var ClassName = {
	    open : 'control-sidebar-open',
	    fixed: 'fixed'
	  };
	
	  var Event = {
	    collapsed: 'collapsed.controlsidebar',
	    expanded : 'expanded.controlsidebar'
	  };
	
	  // ControlSidebar Class Definition
	  // ===============================
	  var ControlSidebar = function (element, options) {
	    this.element         = element;
	    this.options         = options;
	    this.hasBindedResize = false;
	
	    this.init();
	  };
	
	  ControlSidebar.prototype.init = function () {
	    // Add click listener if the element hasn't been
	    // initialized using the data API
	    if (!$(this.element).is(Selector.data)) {
	      $(this).on('click', this.toggle);
	    }
	
	    this.fix();
	    $(window).resize(function () {
	      this.fix();
	    }.bind(this));
	  };
	
	  ControlSidebar.prototype.toggle = function (event) {
	    if (event) event.preventDefault();
	
	    this.fix();
	
	    if (!$(Selector.sidebar).is(Selector.open) && !$('body').is(Selector.open)) {
	      this.expand();
	    } else {
	      this.collapse();
	    }
	  };
	
	  ControlSidebar.prototype.expand = function () {
	    if (!this.options.slide) {
	      $('body').addClass(ClassName.open);
	    } else {
	      $(Selector.sidebar).addClass(ClassName.open);
	    }
	
	    $(this.element).trigger($.Event(Event.expanded));
	  };
	
	  ControlSidebar.prototype.collapse = function () {
	    $('body, ' + Selector.sidebar).removeClass(ClassName.open);
	    $(this.element).trigger($.Event(Event.collapsed));
	  };
	
	  ControlSidebar.prototype.fix = function () {
	    if ($('body').is(Selector.boxed)) {
	      this._fixForBoxed($(Selector.bg));
	    }
	  };
	
	  // Private
	
	  ControlSidebar.prototype._fixForBoxed = function (bg) {
	    bg.css({
	      position: 'absolute',
	      height  : $(Selector.wrapper).height()
	    });
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, (data = new ControlSidebar($this, options)));
	      }
	
	      if (typeof option == 'string') data.toggle();
	    });
	  }
	
	  var old = $.fn.controlSidebar;
	
	  $.fn.controlSidebar             = Plugin;
	  $.fn.controlSidebar.Constructor = ControlSidebar;
	
	  // No Conflict Mode
	  // ================
	  $.fn.controlSidebar.noConflict = function () {
	    $.fn.controlSidebar = old;
	    return this;
	  };
	
	  // ControlSidebar Data API
	  // =======================
	  $(document).on('click', Selector.data, function (event) {
	    if (event) event.preventDefault();
	    Plugin.call($(this), 'toggle');
	  });
	
	}(jQuery);
	
	
	/* DirectChat()
	 * ===============
	 * Toggles the state of the control sidebar
	 *
	 * @Usage: $('#my-chat-box').directChat()
	 *         or add [data-widget="direct-chat"] to the trigger
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.directchat';
	
	  var Selector = {
	    data: '[data-widget="chat-pane-toggle"]',
	    box : '.direct-chat'
	  };
	
	  var ClassName = {
	    open: 'direct-chat-contacts-open'
	  };
	
	  // DirectChat Class Definition
	  // ===========================
	  var DirectChat = function (element) {
	    this.element = element;
	  };
	
	  DirectChat.prototype.toggle = function ($trigger) {
	    $trigger.parents(Selector.box).first().toggleClass(ClassName.open);
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        $this.data(DataKey, (data = new DirectChat($this)));
	      }
	
	      if (typeof option == 'string') data.toggle($this);
	    });
	  }
	
	  var old = $.fn.directChat;
	
	  $.fn.directChat             = Plugin;
	  $.fn.directChat.Constructor = DirectChat;
	
	  // No Conflict Mode
	  // ================
	  $.fn.directChat.noConflict = function () {
	    $.fn.directChat = old;
	    return this;
	  };
	
	  // DirectChat Data API
	  // ===================
	  $(document).on('click', Selector.data, function (event) {
	    if (event) event.preventDefault();
	    Plugin.call($(this), 'toggle');
	  });
	
	}(jQuery);
	
	
	/* Layout()
	 * ========
	 * Implements AdminLTE layout.
	 * Fixes the layout height in case min-height fails.
	 *
	 * @usage activated automatically upon window load.
	 *        Configure any options by passing data-option="value"
	 *        to the body tag.
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.layout';
	
	  var Default = {
	    slimscroll : true,
	    resetHeight: true
	  };
	
	  var Selector = {
	    wrapper       : '.wrapper',
	    contentWrapper: '.content-wrapper',
	    layoutBoxed   : '.layout-boxed',
	    mainFooter    : '.main-footer',
	    mainHeader    : '.main-header',
	    sidebar       : '.sidebar',
	    controlSidebar: '.control-sidebar',
	    fixed         : '.fixed',
	    sidebarMenu   : '.sidebar-menu',
	    logo          : '.main-header .logo'
	  };
	
	  var ClassName = {
	    fixed         : 'fixed',
	    holdTransition: 'hold-transition'
	  };
	
	  var Layout = function (options) {
	    this.options      = options;
	    this.bindedResize = false;
	    this.activate();
	  };
	
	  Layout.prototype.activate = function () {
	    this.fix();
	    this.fixSidebar();
	
	    $('body').removeClass(ClassName.holdTransition);
	
	    if (this.options.resetHeight) {
	      $('body, html, ' + Selector.wrapper).css({
	        'height'    : 'auto',
	        'min-height': '100%'
	      });
	    }
	
	    if (!this.bindedResize) {
	      $(window).resize(function () {
	        this.fix();
	        this.fixSidebar();
	
	        $(Selector.logo + ', ' + Selector.sidebar).one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
	          this.fix();
	          this.fixSidebar();
	        }.bind(this));
	      }.bind(this));
	
	      this.bindedResize = true;
	    }
	
	    $(Selector.sidebarMenu).on('expanded.tree', function () {
	      this.fix();
	      this.fixSidebar();
	    }.bind(this));
	
	    $(Selector.sidebarMenu).on('collapsed.tree', function () {
	      this.fix();
	      this.fixSidebar();
	    }.bind(this));
	  };
	
	  Layout.prototype.fix = function () {
	    // Remove overflow from .wrapper if layout-boxed exists
	    $(Selector.layoutBoxed + ' > ' + Selector.wrapper).css('overflow', 'hidden');
	
	    // Get window height and the wrapper height
	    var footerHeight = $(Selector.mainFooter).outerHeight() || 0;
	    var headerHeight  = $(Selector.mainHeader).outerHeight() || 0;
	    var neg           = headerHeight + footerHeight;
	    var windowHeight  = $(window).height();
	    var sidebarHeight = $(Selector.sidebar).height() || 0;
	
	    // Set the min-height of the content and sidebar based on
	    // the height of the document.
	    if ($('body').hasClass(ClassName.fixed)) {
	      $(Selector.contentWrapper).css('min-height', windowHeight - footerHeight);
	    } else {
	      var postSetHeight;
	
	      if (windowHeight >= sidebarHeight) {
	        $(Selector.contentWrapper).css('min-height', windowHeight - neg);
	        postSetHeight = windowHeight - neg;
	      } else {
	        $(Selector.contentWrapper).css('min-height', sidebarHeight);
	        postSetHeight = sidebarHeight;
	      }
	
	      // Fix for the control sidebar height
	      var $controlSidebar = $(Selector.controlSidebar);
	      if (typeof $controlSidebar !== 'undefined') {
	        if ($controlSidebar.height() > postSetHeight)
	          $(Selector.contentWrapper).css('min-height', $controlSidebar.height());
	      }
	    }
	  };
	
	  Layout.prototype.fixSidebar = function () {
	    // Make sure the body tag has the .fixed class
	    if (!$('body').hasClass(ClassName.fixed)) {
	      if (typeof $.fn.slimScroll !== 'undefined') {
	        $(Selector.sidebar).slimScroll({ destroy: true }).height('auto');
	      }
	      return;
	    }
	
	    // Enable slimscroll for fixed layout
	    if (this.options.slimscroll) {
	      if (typeof $.fn.slimScroll !== 'undefined') {
	        // Destroy if it exists
	        // $(Selector.sidebar).slimScroll({ destroy: true }).height('auto')
	
	        // Add slimscroll
	        $(Selector.sidebar).slimScroll({
	          height: ($(window).height() - $(Selector.mainHeader).height()) + 'px'
	        });
	      }
	    }
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option);
	        $this.data(DataKey, (data = new Layout(options)));
	      }
	
	      if (typeof option === 'string') {
	        if (typeof data[option] === 'undefined') {
	          throw new Error('No method named ' + option);
	        }
	        data[option]();
	      }
	    });
	  }
	
	  var old = $.fn.layout;
	
	  $.fn.layout            = Plugin;
	  $.fn.layout.Constuctor = Layout;
	
	  // No conflict mode
	  // ================
	  $.fn.layout.noConflict = function () {
	    $.fn.layout = old;
	    return this;
	  };
	
	  // Layout DATA-API
	  // ===============
	  $(window).on('load', function () {
	    Plugin.call($('body'));
	  });
	}(jQuery);
	
	
	/* PushMenu()
	 * ==========
	 * Adds the push menu functionality to the sidebar.
	 *
	 * @usage: $('.btn').pushMenu(options)
	 *          or add [data-toggle="push-menu"] to any button
	 *          Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.pushmenu';
	
	  var Default = {
	    collapseScreenSize   : 767,
	    expandOnHover        : false,
	    expandTransitionDelay: 200
	  };
	
	  var Selector = {
	    collapsed     : '.sidebar-collapse',
	    open          : '.sidebar-open',
	    mainSidebar   : '.main-sidebar',
	    contentWrapper: '.content-wrapper',
	    searchInput   : '.sidebar-form .form-control',
	    button        : '[data-toggle="push-menu"]',
	    mini          : '.sidebar-mini',
	    expanded      : '.sidebar-expanded-on-hover',
	    layoutFixed   : '.fixed'
	  };
	
	  var ClassName = {
	    collapsed    : 'sidebar-collapse',
	    open         : 'sidebar-open',
	    mini         : 'sidebar-mini',
	    expanded     : 'sidebar-expanded-on-hover',
	    expandFeature: 'sidebar-mini-expand-feature',
	    layoutFixed  : 'fixed'
	  };
	
	  var Event = {
	    expanded : 'expanded.pushMenu',
	    collapsed: 'collapsed.pushMenu'
	  };
	
	  // PushMenu Class Definition
	  // =========================
	  var PushMenu = function (options) {
	    this.options = options;
	    this.init();
	  };
	
	  PushMenu.prototype.init = function () {
	    if (this.options.expandOnHover
	      || ($('body').is(Selector.mini + Selector.layoutFixed))) {
	      this.expandOnHover();
	      $('body').addClass(ClassName.expandFeature);
	    }
	
	    $(Selector.contentWrapper).click(function () {
	      // Enable hide menu when clicking on the content-wrapper on small screens
	      if ($(window).width() <= this.options.collapseScreenSize && $('body').hasClass(ClassName.open)) {
	        this.close();
	      }
	    }.bind(this));
	
	    // __Fix for android devices
	    $(Selector.searchInput).click(function (e) {
	      e.stopPropagation();
	    });
	  };
	
	  PushMenu.prototype.toggle = function () {
	    var windowWidth = $(window).width();
	    var isOpen      = !$('body').hasClass(ClassName.collapsed);
	
	    if (windowWidth <= this.options.collapseScreenSize) {
	      isOpen = $('body').hasClass(ClassName.open);
	    }
	
	    if (!isOpen) {
	      this.open();
	    } else {
	      this.close();
	    }
	  };
	
	  PushMenu.prototype.open = function () {
	    var windowWidth = $(window).width();
	
	    if (windowWidth > this.options.collapseScreenSize) {
	      $('body').removeClass(ClassName.collapsed)
	        .trigger($.Event(Event.expanded));
	    }
	    else {
	      $('body').addClass(ClassName.open)
	        .trigger($.Event(Event.expanded));
	    }
	  };
	
	  PushMenu.prototype.close = function () {
	    var windowWidth = $(window).width();
	    if (windowWidth > this.options.collapseScreenSize) {
	      $('body').addClass(ClassName.collapsed)
	        .trigger($.Event(Event.collapsed));
	    } else {
	      $('body').removeClass(ClassName.open + ' ' + ClassName.collapsed)
	        .trigger($.Event(Event.collapsed));
	    }
	  };
	
	  PushMenu.prototype.expandOnHover = function () {
	    $(Selector.mainSidebar).hover(function () {
	      if ($('body').is(Selector.mini + Selector.collapsed)
	        && $(window).width() > this.options.collapseScreenSize) {
	        this.expand();
	      }
	    }.bind(this), function () {
	      if ($('body').is(Selector.expanded)) {
	        this.collapse();
	      }
	    }.bind(this));
	  };
	
	  PushMenu.prototype.expand = function () {
	    setTimeout(function () {
	      $('body').removeClass(ClassName.collapsed)
	        .addClass(ClassName.expanded);
	    }, this.options.expandTransitionDelay);
	  };
	
	  PushMenu.prototype.collapse = function () {
	    setTimeout(function () {
	      $('body').removeClass(ClassName.expanded)
	        .addClass(ClassName.collapsed);
	    }, this.options.expandTransitionDelay);
	  };
	
	  // PushMenu Plugin Definition
	  // ==========================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, (data = new PushMenu(options)));
	      }
	
	      if (option === 'toggle') data.toggle();
	    });
	  }
	
	  var old = $.fn.pushMenu;
	
	  $.fn.pushMenu             = Plugin;
	  $.fn.pushMenu.Constructor = PushMenu;
	
	  // No Conflict Mode
	  // ================
	  $.fn.pushMenu.noConflict = function () {
	    $.fn.pushMenu = old;
	    return this;
	  };
	
	  // Data API
	  // ========
	  $(document).on('click', Selector.button, function (e) {
	    e.preventDefault();
	    Plugin.call($(this), 'toggle');
	  });
	  $(window).on('load', function () {
	    Plugin.call($(Selector.button));
	  });
	}(jQuery);
	
	
	/* TodoList()
	 * =========
	 * Converts a list into a todoList.
	 *
	 * @Usage: $('.my-list').todoList(options)
	 *         or add [data-widget="todo-list"] to the ul element
	 *         Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.todolist';
	
	  var Default = {
	    onCheck  : function (item) {
	      return item;
	    },
	    onUnCheck: function (item) {
	      return item;
	    }
	  };
	
	  var Selector = {
	    data: '[data-widget="todo-list"]'
	  };
	
	  var ClassName = {
	    done: 'done'
	  };
	
	  // TodoList Class Definition
	  // =========================
	  var TodoList = function (element, options) {
	    this.element = element;
	    this.options = options;
	
	    this._setUpListeners();
	  };
	
	  TodoList.prototype.toggle = function (item) {
	    item.parents(Selector.li).first().toggleClass(ClassName.done);
	    if (!item.prop('checked')) {
	      this.unCheck(item);
	      return;
	    }
	
	    this.check(item);
	  };
	
	  TodoList.prototype.check = function (item) {
	    this.options.onCheck.call(item);
	  };
	
	  TodoList.prototype.unCheck = function (item) {
	    this.options.onUnCheck.call(item);
	  };
	
	  // Private
	
	  TodoList.prototype._setUpListeners = function () {
	    var that = this;
	    $(this.element).on('change ifChanged', 'input:checkbox', function () {
	      that.toggle($(this));
	    });
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, (data = new TodoList($this, options)));
	      }
	
	      if (typeof data == 'string') {
	        if (typeof data[option] == 'undefined') {
	          throw new Error('No method named ' + option);
	        }
	        data[option]();
	      }
	    });
	  }
	
	  var old = $.fn.todoList;
	
	  $.fn.todoList             = Plugin;
	  $.fn.todoList.Constructor = TodoList;
	
	  // No Conflict Mode
	  // ================
	  $.fn.todoList.noConflict = function () {
	    $.fn.todoList = old;
	    return this;
	  };
	
	  // TodoList Data API
	  // =================
	  $(window).on('load', function () {
	    $(Selector.data).each(function () {
	      Plugin.call($(this));
	    });
	  });
	
	}(jQuery);
	
	
	/* Tree()
	 * ======
	 * Converts a nested list into a multilevel
	 * tree view menu.
	 *
	 * @Usage: $('.my-menu').tree(options)
	 *         or add [data-widget="tree"] to the ul element
	 *         Pass any option as data-option="value"
	 */
	+function ($) {
	  'use strict';
	
	  var DataKey = 'lte.tree';
	
	  var Default = {
	    animationSpeed: 500,
	    accordion     : true,
	    followLink    : false,
	    trigger       : '.treeview a'
	  };
	
	  var Selector = {
	    tree        : '.tree',
	    treeview    : '.treeview',
	    treeviewMenu: '.treeview-menu',
	    open        : '.menu-open, .active',
	    li          : 'li',
	    data        : '[data-widget="tree"]',
	    active      : '.active'
	  };
	
	  var ClassName = {
	    open: 'menu-open',
	    tree: 'tree'
	  };
	
	  var Event = {
	    collapsed: 'collapsed.tree',
	    expanded : 'expanded.tree'
	  };
	
	  // Tree Class Definition
	  // =====================
	  var Tree = function (element, options) {
	    this.element = element;
	    this.options = options;
	
	    $(this.element).addClass(ClassName.tree);
	
	    $(Selector.treeview + Selector.active, this.element).addClass(ClassName.open);
	
	    this._setUpListeners();
	  };
	
	  Tree.prototype.toggle = function (link, event) {
	    var treeviewMenu = link.next(Selector.treeviewMenu);
	    var parentLi     = link.parent();
	    var isOpen       = parentLi.hasClass(ClassName.open);
	
	    if (!parentLi.is(Selector.treeview)) {
	      return;
	    }
	
	    if (!this.options.followLink || link.attr('href') === '#') {
	      event.preventDefault();
	    }
	
	    if (isOpen) {
	      this.collapse(treeviewMenu, parentLi);
	    } else {
	      this.expand(treeviewMenu, parentLi);
	    }
	  };
	
	  Tree.prototype.expand = function (tree, parent) {
	    var expandedEvent = $.Event(Event.expanded);
	
	    if (this.options.accordion) {
	      var openMenuLi = parent.siblings(Selector.open);
	      var openTree   = openMenuLi.children(Selector.treeviewMenu);
	      this.collapse(openTree, openMenuLi);
	    }
	
	    parent.addClass(ClassName.open);
	    tree.slideDown(this.options.animationSpeed, function () {
	      $(this.element).trigger(expandedEvent);
	    }.bind(this));
	  };
	
	  Tree.prototype.collapse = function (tree, parentLi) {
	    var collapsedEvent = $.Event(Event.collapsed);
	
	    //tree.find(Selector.open).removeClass(ClassName.open);
	    parentLi.removeClass(ClassName.open);
	    tree.slideUp(this.options.animationSpeed, function () {
	      //tree.find(Selector.open + ' > ' + Selector.treeview).slideUp();
	      $(this.element).trigger(collapsedEvent);
	    }.bind(this));
	  };
	
	  // Private
	
	  Tree.prototype._setUpListeners = function () {
	    var that = this;
	
	    $(this.element).on('click', this.options.trigger, function (event) {
	      that.toggle($(this), event);
	    });
	  };
	
	  // Plugin Definition
	  // =================
	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this);
	      var data  = $this.data(DataKey);
	
	      if (!data) {
	        var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option);
	        $this.data(DataKey, new Tree($this, options));
	      }
	    });
	  }
	
	  var old = $.fn.tree;
	
	  $.fn.tree             = Plugin;
	  $.fn.tree.Constructor = Tree;
	
	  // No Conflict Mode
	  // ================
	  $.fn.tree.noConflict = function () {
	    $.fn.tree = old;
	    return this;
	  };
	
	  // Tree Data API
	  // =============
	  $(window).on('load', function () {
	    $(Selector.data).each(function () {
	      Plugin.call($(this));
	    });
	  });
	
	}(jQuery);


/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_RESULT__;// Fine Uploader 5.11.5 - (c) 2013-present Widen Enterprises, Inc. MIT licensed. http://fineuploader.com
	!function(global){var qq=function(e){"use strict";return{hide:function(){return e.style.display="none",this},attach:function(t,n){return e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,n),function(){qq(e).detach(t,n)}},detach:function(t,n){return e.removeEventListener?e.removeEventListener(t,n,!1):e.attachEvent&&e.detachEvent("on"+t,n),this},contains:function(t){return!!t&&(e===t||(e.contains?e.contains(t):!!(8&t.compareDocumentPosition(e))))},insertBefore:function(t){return t.parentNode.insertBefore(e,t),this},remove:function(){return e.parentNode.removeChild(e),this},css:function(t){if(null==e.style)throw new qq.Error("Can't apply style to node as it is not on the HTMLElement prototype chain!");return null!=t.opacity&&"string"!=typeof e.style.opacity&&"undefined"!=typeof e.filters&&(t.filter="alpha(opacity="+Math.round(100*t.opacity)+")"),qq.extend(e.style,t),this},hasClass:function(t,n){var i=new RegExp("(^| )"+t+"( |$)");return i.test(e.className)||!(!n||!i.test(e.parentNode.className))},addClass:function(t){return qq(e).hasClass(t)||(e.className+=" "+t),this},removeClass:function(t){var n=new RegExp("(^| )"+t+"( |$)");return e.className=e.className.replace(n," ").replace(/^\s+|\s+$/g,""),this},getByClass:function(t,n){var i,o=[];return n&&e.querySelector?e.querySelector("."+t):e.querySelectorAll?e.querySelectorAll("."+t):(i=e.getElementsByTagName("*"),qq.each(i,function(e,n){qq(n).hasClass(t)&&o.push(n)}),n?o[0]:o)},getFirstByClass:function(t){return qq(e).getByClass(t,!0)},children:function(){for(var t=[],n=e.firstChild;n;)1===n.nodeType&&t.push(n),n=n.nextSibling;return t},setText:function(t){return e.innerText=t,e.textContent=t,this},clearText:function(){return qq(e).setText("")},hasAttribute:function(t){var n;return e.hasAttribute?!!e.hasAttribute(t)&&null==/^false$/i.exec(e.getAttribute(t)):(n=e[t],void 0!==n&&null==/^false$/i.exec(n))}}};!function(){"use strict";qq.canvasToBlob=function(e,t,n){return qq.dataUriToBlob(e.toDataURL(t,n))},qq.dataUriToBlob=function(e){var t,n,i,o,r=function(e,t){var n=window.BlobBuilder||window.WebKitBlobBuilder||window.MozBlobBuilder||window.MSBlobBuilder,i=n&&new n;return i?(i.append(e),i.getBlob(t)):new Blob([e],{type:t})};return n=e.split(",")[0].indexOf("base64")>=0?atob(e.split(",")[1]):decodeURI(e.split(",")[1]),o=e.split(",")[0].split(":")[1].split(";")[0],t=new ArrayBuffer(n.length),i=new Uint8Array(t),qq.each(n,function(e,t){i[e]=t.charCodeAt(0)}),r(t,o)},qq.log=function(e,t){window.console&&(t&&"info"!==t?window.console[t]?window.console[t](e):window.console.log("<"+t+"> "+e):window.console.log(e))},qq.isObject=function(e){return e&&!e.nodeType&&"[object Object]"===Object.prototype.toString.call(e)},qq.isFunction=function(e){return"function"==typeof e},qq.isArray=function(e){return"[object Array]"===Object.prototype.toString.call(e)||e&&window.ArrayBuffer&&e.buffer&&e.buffer.constructor===ArrayBuffer},qq.isItemList=function(e){return"[object DataTransferItemList]"===Object.prototype.toString.call(e)},qq.isNodeList=function(e){return"[object NodeList]"===Object.prototype.toString.call(e)||e.item&&e.namedItem},qq.isString=function(e){return"[object String]"===Object.prototype.toString.call(e)},qq.trimStr=function(e){return String.prototype.trim?e.trim():e.replace(/^\s+|\s+$/g,"")},qq.format=function(e){var t=Array.prototype.slice.call(arguments,1),n=e,i=n.indexOf("{}");return qq.each(t,function(e,t){var o=n.substring(0,i),r=n.substring(i+2);if(n=o+t+r,i=n.indexOf("{}",i+t.length),i<0)return!1}),n},qq.isFile=function(e){return window.File&&"[object File]"===Object.prototype.toString.call(e)},qq.isFileList=function(e){return window.FileList&&"[object FileList]"===Object.prototype.toString.call(e)},qq.isFileOrInput=function(e){return qq.isFile(e)||qq.isInput(e)},qq.isInput=function(e,t){var n=function(e){var n=e.toLowerCase();return t?"file"!==n:"file"===n};return!!(window.HTMLInputElement&&"[object HTMLInputElement]"===Object.prototype.toString.call(e)&&e.type&&n(e.type))||!!(e.tagName&&"input"===e.tagName.toLowerCase()&&e.type&&n(e.type))},qq.isBlob=function(e){if(window.Blob&&"[object Blob]"===Object.prototype.toString.call(e))return!0},qq.isXhrUploadSupported=function(){var e=document.createElement("input");return e.type="file",void 0!==e.multiple&&"undefined"!=typeof File&&"undefined"!=typeof FormData&&"undefined"!=typeof qq.createXhrInstance().upload},qq.createXhrInstance=function(){if(window.XMLHttpRequest)return new XMLHttpRequest;try{return new ActiveXObject("MSXML2.XMLHTTP.3.0")}catch(e){return qq.log("Neither XHR or ActiveX are supported!","error"),null}},qq.isFolderDropSupported=function(e){return e.items&&e.items.length>0&&e.items[0].webkitGetAsEntry},qq.isFileChunkingSupported=function(){return!qq.androidStock()&&qq.isXhrUploadSupported()&&(void 0!==File.prototype.slice||void 0!==File.prototype.webkitSlice||void 0!==File.prototype.mozSlice)},qq.sliceBlob=function(e,t,n){var i=e.slice||e.mozSlice||e.webkitSlice;return i.call(e,t,n)},qq.arrayBufferToHex=function(e){var t="",n=new Uint8Array(e);return qq.each(n,function(e,n){var i=n.toString(16);i.length<2&&(i="0"+i),t+=i}),t},qq.readBlobToHex=function(e,t,n){var i=qq.sliceBlob(e,t,t+n),o=new FileReader,r=new qq.Promise;return o.onload=function(){r.success(qq.arrayBufferToHex(o.result))},o.onerror=r.failure,o.readAsArrayBuffer(i),r},qq.extend=function(e,t,n){return qq.each(t,function(t,i){n&&qq.isObject(i)?(void 0===e[t]&&(e[t]={}),qq.extend(e[t],i,!0)):e[t]=i}),e},qq.override=function(e,t){var n={},i=t(n);return qq.each(i,function(t,i){void 0!==e[t]&&(n[t]=e[t]),e[t]=i}),e},qq.indexOf=function(e,t,n){if(e.indexOf)return e.indexOf(t,n);n=n||0;var i=e.length;for(n<0&&(n+=i);n<i;n+=1)if(e.hasOwnProperty(n)&&e[n]===t)return n;return-1},qq.getUniqueId=function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(e){var t=16*Math.random()|0,n="x"==e?t:3&t|8;return n.toString(16)})},qq.ie=function(){return navigator.userAgent.indexOf("MSIE")!==-1||navigator.userAgent.indexOf("Trident")!==-1},qq.ie7=function(){return navigator.userAgent.indexOf("MSIE 7")!==-1},qq.ie8=function(){return navigator.userAgent.indexOf("MSIE 8")!==-1},qq.ie10=function(){return navigator.userAgent.indexOf("MSIE 10")!==-1},qq.ie11=function(){return qq.ie()&&navigator.userAgent.indexOf("rv:11")!==-1},qq.edge=function(){return navigator.userAgent.indexOf("Edge")>=0},qq.safari=function(){return void 0!==navigator.vendor&&navigator.vendor.indexOf("Apple")!==-1},qq.chrome=function(){return void 0!==navigator.vendor&&navigator.vendor.indexOf("Google")!==-1},qq.opera=function(){return void 0!==navigator.vendor&&navigator.vendor.indexOf("Opera")!==-1},qq.firefox=function(){return!qq.edge()&&!qq.ie11()&&navigator.userAgent.indexOf("Mozilla")!==-1&&void 0!==navigator.vendor&&""===navigator.vendor},qq.windows=function(){return"Win32"===navigator.platform},qq.android=function(){return navigator.userAgent.toLowerCase().indexOf("android")!==-1},qq.androidStock=function(){return qq.android()&&navigator.userAgent.toLowerCase().indexOf("chrome")<0},qq.ios6=function(){return qq.ios()&&navigator.userAgent.indexOf(" OS 6_")!==-1},qq.ios7=function(){return qq.ios()&&navigator.userAgent.indexOf(" OS 7_")!==-1},qq.ios8=function(){return qq.ios()&&navigator.userAgent.indexOf(" OS 8_")!==-1},qq.ios800=function(){return qq.ios()&&navigator.userAgent.indexOf(" OS 8_0 ")!==-1},qq.ios=function(){return navigator.userAgent.indexOf("iPad")!==-1||navigator.userAgent.indexOf("iPod")!==-1||navigator.userAgent.indexOf("iPhone")!==-1},qq.iosChrome=function(){return qq.ios()&&navigator.userAgent.indexOf("CriOS")!==-1},qq.iosSafari=function(){return qq.ios()&&!qq.iosChrome()&&navigator.userAgent.indexOf("Safari")!==-1},qq.iosSafariWebView=function(){return qq.ios()&&!qq.iosChrome()&&!qq.iosSafari()},qq.preventDefault=function(e){e.preventDefault?e.preventDefault():e.returnValue=!1},qq.toElement=function(){var e=document.createElement("div");return function(t){e.innerHTML=t;var n=e.firstChild;return e.removeChild(n),n}}(),qq.each=function(e,t){var n,i;if(e)if(window.Storage&&e.constructor===window.Storage)for(n=0;n<e.length&&(i=t(e.key(n),e.getItem(e.key(n))),i!==!1);n++);else if(qq.isArray(e)||qq.isItemList(e)||qq.isNodeList(e))for(n=0;n<e.length&&(i=t(n,e[n]),i!==!1);n++);else if(qq.isString(e))for(n=0;n<e.length&&(i=t(n,e.charAt(n)),i!==!1);n++);else for(n in e)if(Object.prototype.hasOwnProperty.call(e,n)&&(i=t(n,e[n]),i===!1))break},qq.bind=function(e,t){if(qq.isFunction(e)){var n=Array.prototype.slice.call(arguments,2);return function(){var i=qq.extend([],n);return arguments.length&&(i=i.concat(Array.prototype.slice.call(arguments))),e.apply(t,i)}}throw new Error("first parameter must be a function!")},qq.obj2url=function(e,t,n){var i=[],o="&",r=function(e,n){var o=t?/\[\]$/.test(t)?t:t+"["+n+"]":n;"undefined"!==o&&"undefined"!==n&&i.push("object"==typeof e?qq.obj2url(e,o,!0):"[object Function]"===Object.prototype.toString.call(e)?encodeURIComponent(o)+"="+encodeURIComponent(e()):encodeURIComponent(o)+"="+encodeURIComponent(e))};return!n&&t?(o=/\?/.test(t)?/\?$/.test(t)?"":"&":"?",i.push(t),i.push(qq.obj2url(e))):"[object Array]"===Object.prototype.toString.call(e)&&"undefined"!=typeof e?qq.each(e,function(e,t){r(t,e)}):"undefined"!=typeof e&&null!==e&&"object"==typeof e?qq.each(e,function(e,t){r(t,e)}):i.push(encodeURIComponent(t)+"="+encodeURIComponent(e)),t?i.join(o):i.join(o).replace(/^&/,"").replace(/%20/g,"+")},qq.obj2FormData=function(e,t,n){return t||(t=new FormData),qq.each(e,function(e,i){e=n?n+"["+e+"]":e,qq.isObject(i)?qq.obj2FormData(i,t,e):qq.isFunction(i)?t.append(e,i()):t.append(e,i)}),t},qq.obj2Inputs=function(e,t){var n;return t||(t=document.createElement("form")),qq.obj2FormData(e,{append:function(e,i){n=document.createElement("input"),n.setAttribute("name",e),n.setAttribute("value",i),t.appendChild(n)}}),t},qq.parseJson=function(json){return window.JSON&&qq.isFunction(JSON.parse)?JSON.parse(json):eval("("+json+")")},qq.getExtension=function(e){var t=e.lastIndexOf(".")+1;if(t>0)return e.substr(t,e.length-t)},qq.getFilename=function(e){return qq.isInput(e)?e.value.replace(/.*(\/|\\)/,""):qq.isFile(e)&&null!==e.fileName&&void 0!==e.fileName?e.fileName:e.name},qq.DisposeSupport=function(){var e=[];return{dispose:function(){var t;do t=e.shift(),t&&t();while(t)},attach:function(){var e=arguments;this.addDisposer(qq(e[0]).attach.apply(this,Array.prototype.slice.call(arguments,1)))},addDisposer:function(t){e.push(t)}}}}(),function(){"use strict"; true?!(__WEBPACK_AMD_DEFINE_RESULT__ = function(){return qq}.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)):"undefined"!=typeof module&&module.exports?module.exports=qq:global.qq=qq}(),function(){"use strict";qq.Error=function(e){this.message="[Fine Uploader "+qq.version+"] "+e},qq.Error.prototype=new Error}(),qq.version="5.11.5",qq.supportedFeatures=function(){"use strict";function e(){var e,t=!0;try{e=document.createElement("input"),e.type="file",qq(e).hide(),e.disabled&&(t=!1)}catch(e){t=!1}return t}function t(){return(qq.chrome()||qq.opera())&&void 0!==navigator.userAgent.match(/Chrome\/[2][1-9]|Chrome\/[3-9][0-9]/)}function n(){return(qq.chrome()||qq.opera())&&void 0!==navigator.userAgent.match(/Chrome\/[1][4-9]|Chrome\/[2-9][0-9]/)}function i(){if(window.XMLHttpRequest){var e=qq.createXhrInstance();return void 0!==e.withCredentials}return!1}function o(){return void 0!==window.XDomainRequest}function r(){return!!i()||o()}function a(){return void 0!==document.createElement("input").webkitdirectory}function s(){try{return!!window.localStorage&&qq.isFunction(window.localStorage.setItem)}catch(e){return!1}}function l(){var e=document.createElement("span");return("draggable"in e||"ondragstart"in e&&"ondrop"in e)&&!qq.android()&&!qq.ios()}var u,c,d,p,h,q,f,m,g,_,b,v,y,S,w;return u=e(),p=u&&qq.isXhrUploadSupported(),c=p&&!qq.androidStock(),d=p&&l(),h=d&&t(),q=p&&qq.isFileChunkingSupported(),f=p&&q&&s(),m=p&&n(),g=u&&(void 0!==window.postMessage||p),b=i(),_=o(),v=r(),y=a(),S=p&&void 0!==window.FileReader,w=function(){return!!p&&(!qq.androidStock()&&!qq.iosChrome())}(),{ajaxUploading:p,blobUploading:c,canDetermineSize:p,chunking:q,deleteFileCors:v,deleteFileCorsXdr:_,deleteFileCorsXhr:b,dialogElement:!!window.HTMLDialogElement,fileDrop:d,folderDrop:h,folderSelection:y,imagePreviews:S,imageValidation:S,itemSizeValidation:p,pause:q,progressBar:w,resume:f,scaling:S&&c,tiffPreviews:qq.safari(),unlimitedScaledImageSize:!qq.ios(),uploading:u,uploadCors:g,uploadCustomHeaders:p,uploadNonMultipart:p,uploadViaPaste:m}}(),qq.isGenericPromise=function(e){"use strict";return!!(e&&e.then&&qq.isFunction(e.then))},qq.Promise=function(){"use strict";var e,t,n=[],i=[],o=[],r=0;qq.extend(this,{then:function(o,a){return 0===r?(o&&n.push(o),a&&i.push(a)):r===-1?a&&a.apply(null,t):o&&o.apply(null,e),this},done:function(n){return 0===r?o.push(n):n.apply(null,void 0===t?e:t),this},success:function(){return r=1,e=arguments,n.length&&qq.each(n,function(t,n){n.apply(null,e)}),o.length&&qq.each(o,function(t,n){n.apply(null,e)}),this},failure:function(){return r=-1,t=arguments,i.length&&qq.each(i,function(e,n){n.apply(null,t)}),o.length&&qq.each(o,function(e,n){n.apply(null,t)}),this}})},qq.BlobProxy=function(e,t){"use strict";qq.extend(this,{referenceBlob:e,create:function(){return t(e)}})},qq.UploadButton=function(e){"use strict";function t(){var e=document.createElement("input");return e.setAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME,i),e.setAttribute("title",a.title),o.setMultiple(a.multiple,e),a.folders&&qq.supportedFeatures.folderSelection&&e.setAttribute("webkitdirectory",""),a.acceptFiles&&e.setAttribute("accept",a.acceptFiles),e.setAttribute("type","file"),e.setAttribute("name",a.name),qq(e).css({position:"absolute",right:0,top:0,fontFamily:"Arial",fontSize:qq.ie()&&!qq.ie8()?"3500px":"118px",margin:0,padding:0,cursor:"pointer",opacity:0}),!qq.ie7()&&qq(e).css({height:"100%"}),a.element.appendChild(e),r.attach(e,"change",function(){a.onChange(e)}),r.attach(e,"mouseover",function(){qq(a.element).addClass(a.hoverClass)}),r.attach(e,"mouseout",function(){qq(a.element).removeClass(a.hoverClass)}),r.attach(e,"focus",function(){qq(a.element).addClass(a.focusClass)}),r.attach(e,"blur",function(){qq(a.element).removeClass(a.focusClass)}),e}var n,i,o=this,r=new qq.DisposeSupport,a={acceptFiles:null,element:null,focusClass:"qq-upload-button-focus",folders:!1,hoverClass:"qq-upload-button-hover",ios8BrowserCrashWorkaround:!1,multiple:!1,name:"qqfile",onChange:function(e){},title:null};qq.extend(a,e),i=qq.getUniqueId(),qq(a.element).css({position:"relative",overflow:"hidden",direction:"ltr"}),qq.extend(this,{getInput:function(){return n},getButtonId:function(){return i},setMultiple:function(e,t){var n=t||this.getInput();a.ios8BrowserCrashWorkaround&&qq.ios8()&&(qq.iosChrome()||qq.iosSafariWebView())?n.setAttribute("multiple",""):e?n.setAttribute("multiple",""):n.removeAttribute("multiple")},setAcceptFiles:function(e){e!==a.acceptFiles&&n.setAttribute("accept",e)},reset:function(){n.parentNode&&qq(n).remove(),qq(a.element).removeClass(a.focusClass),n=null,n=t()}}),n=t()},qq.UploadButton.BUTTON_ID_ATTR_NAME="qq-button-id",qq.UploadData=function(e){"use strict";function t(e){if(qq.isArray(e)){var t=[];return qq.each(e,function(e,n){t.push(o[n])}),t}return o[e]}function n(e){if(qq.isArray(e)){var t=[];return qq.each(e,function(e,n){t.push(o[r[n]])}),t}return o[r[e]]}function i(e){var t=[],n=[].concat(e);return qq.each(n,function(e,n){var i=a[n];void 0!==i&&qq.each(i,function(e,n){t.push(o[n])})}),t}var o=[],r={},a={},s={},l={};qq.extend(this,{addFile:function(t){var n=t.status||qq.status.SUBMITTING,i=o.push({name:t.name,originalName:t.name,uuid:t.uuid,size:null==t.size?-1:t.size,status:n})-1;return t.batchId&&(o[i].batchId=t.batchId,void 0===l[t.batchId]&&(l[t.batchId]=[]),l[t.batchId].push(i)),t.proxyGroupId&&(o[i].proxyGroupId=t.proxyGroupId,void 0===s[t.proxyGroupId]&&(s[t.proxyGroupId]=[]),s[t.proxyGroupId].push(i)),o[i].id=i,r[t.uuid]=i,void 0===a[n]&&(a[n]=[]),a[n].push(i),e.onStatusChange(i,null,n),i},retrieve:function(e){return qq.isObject(e)&&o.length?void 0!==e.id?t(e.id):void 0!==e.uuid?n(e.uuid):e.status?i(e.status):void 0:qq.extend([],o,!0)},reset:function(){o=[],r={},a={},l={}},setStatus:function(t,n){var i=o[t].status,r=qq.indexOf(a[i],t);a[i].splice(r,1),o[t].status=n,void 0===a[n]&&(a[n]=[]),a[n].push(t),e.onStatusChange(t,i,n)},uuidChanged:function(e,t){var n=o[e].uuid;o[e].uuid=t,r[t]=e,delete r[n]},updateName:function(e,t){o[e].name=t},updateSize:function(e,t){o[e].size=t},setParentId:function(e,t){o[e].parentId=t},getIdsInProxyGroup:function(e){var t=o[e].proxyGroupId;return t?s[t]:[]},getIdsInBatch:function(e){var t=o[e].batchId;return l[t]}})},qq.status={SUBMITTING:"submitting",SUBMITTED:"submitted",REJECTED:"rejected",QUEUED:"queued",CANCELED:"canceled",PAUSED:"paused",UPLOADING:"uploading",UPLOAD_RETRYING:"retrying upload",UPLOAD_SUCCESSFUL:"upload successful",UPLOAD_FAILED:"upload failed",DELETE_FAILED:"delete failed",DELETING:"deleting",DELETED:"deleted"},function(){"use strict";qq.basePublicApi={addBlobs:function(e,t,n){this.addFiles(e,t,n)},addInitialFiles:function(e){var t=this;qq.each(e,function(e,n){t._addCannedFile(n)})},addFiles:function(e,t,n){this._maybeHandleIos8SafariWorkaround();var i=0===this._storedIds.length?qq.getUniqueId():this._currentBatchId,o=qq.bind(function(e){this._handleNewFile({blob:e,name:this._options.blobs.defaultName},i,d)},this),r=qq.bind(function(e){this._handleNewFile(e,i,d)},this),a=qq.bind(function(e){var t=qq.canvasToBlob(e);this._handleNewFile({blob:t,name:this._options.blobs.defaultName+".png"},i,d)},this),s=qq.bind(function(e){var t=e.quality&&e.quality/100,n=qq.canvasToBlob(e.canvas,e.type,t);this._handleNewFile({blob:n,name:e.name},i,d)},this),l=qq.bind(function(e){if(qq.isInput(e)&&qq.supportedFeatures.ajaxUploading){var t=Array.prototype.slice.call(e.files),n=this;qq.each(t,function(e,t){n._handleNewFile(t,i,d)})}else this._handleNewFile(e,i,d)},this),u=function(){qq.isFileList(e)&&(e=Array.prototype.slice.call(e)),e=[].concat(e)},c=this,d=[];this._currentBatchId=i,e&&(u(),qq.each(e,function(e,t){qq.isFileOrInput(t)?l(t):qq.isBlob(t)?o(t):qq.isObject(t)?t.blob&&t.name?r(t):t.canvas&&t.name&&s(t):t.tagName&&"canvas"===t.tagName.toLowerCase()?a(t):c.log(t+" is not a valid file container!  Ignoring!","warn")}),this.log("Received "+d.length+" files."),this._prepareItemsForUpload(d,t,n))},cancel:function(e){this._handler.cancel(e)},cancelAll:function(){var e=[],t=this;qq.extend(e,this._storedIds),qq.each(e,function(e,n){t.cancel(n)}),this._handler.cancelAll()},clearStoredFiles:function(){this._storedIds=[]},continueUpload:function(e){var t=this._uploadData.retrieve({id:e});return!(!qq.supportedFeatures.pause||!this._options.chunking.enabled)&&(t.status===qq.status.PAUSED?(this.log(qq.format("Paused file ID {} ({}) will be continued.  Not paused.",e,this.getName(e))),this._uploadFile(e),!0):(this.log(qq.format("Ignoring continue for file ID {} ({}).  Not paused.",e,this.getName(e)),"error"),!1))},deleteFile:function(e){return this._onSubmitDelete(e)},doesExist:function(e){return this._handler.isValid(e)},drawThumbnail:function(e,t,n,i,o){var r,a,s=new qq.Promise;return this._imageGenerator?(r=this._thumbnailUrls[e],a={customResizeFunction:o,maxSize:n>0?n:null,scale:n>0},!i&&qq.supportedFeatures.imagePreviews&&(r=this.getFile(e)),null==r?s.failure({container:t,error:"File or URL not found."}):this._imageGenerator.generate(r,t,a).then(function(e){s.success(e)},function(e,t){s.failure({container:e,error:t||"Problem generating thumbnail"})})):s.failure({container:t,error:"Missing image generator module"}),s},getButton:function(e){return this._getButton(this._buttonIdsForFileIds[e])},getEndpoint:function(e){return this._endpointStore.get(e)},getFile:function(e){return this._handler.getFile(e)||null},getInProgress:function(){return this._uploadData.retrieve({status:[qq.status.UPLOADING,qq.status.UPLOAD_RETRYING,qq.status.QUEUED]}).length},getName:function(e){return this._uploadData.retrieve({id:e}).name},getParentId:function(e){var t=this.getUploads({id:e}),n=null;return t&&void 0!==t.parentId&&(n=t.parentId),n},getResumableFilesData:function(){return this._handler.getResumableFilesData()},getSize:function(e){return this._uploadData.retrieve({id:e}).size},getNetUploads:function(){return this._netUploaded},getRemainingAllowedItems:function(){var e=this._currentItemLimit;return e>0?e-this._netUploadedOrQueued:null},getUploads:function(e){return this._uploadData.retrieve(e)},getUuid:function(e){return this._uploadData.retrieve({id:e}).uuid},log:function(e,t){!this._options.debug||t&&"info"!==t?t&&"info"!==t&&qq.log("[Fine Uploader "+qq.version+"] "+e,t):qq.log("[Fine Uploader "+qq.version+"] "+e)},pauseUpload:function(e){var t=this._uploadData.retrieve({id:e});if(!qq.supportedFeatures.pause||!this._options.chunking.enabled)return!1;if(qq.indexOf([qq.status.UPLOADING,qq.status.UPLOAD_RETRYING],t.status)>=0){if(this._handler.pause(e))return this._uploadData.setStatus(e,qq.status.PAUSED),!0;this.log(qq.format("Unable to pause file ID {} ({}).",e,this.getName(e)),"error")}else this.log(qq.format("Ignoring pause for file ID {} ({}).  Not in progress.",e,this.getName(e)),"error");return!1},reset:function(){this.log("Resetting uploader..."),this._handler.reset(),this._storedIds=[],this._autoRetries=[],this._retryTimeouts=[],this._preventRetries=[],this._thumbnailUrls=[],qq.each(this._buttons,function(e,t){t.reset()}),this._paramsStore.reset(),this._endpointStore.reset(),this._netUploadedOrQueued=0,this._netUploaded=0,this._uploadData.reset(),this._buttonIdsForFileIds=[],this._pasteHandler&&this._pasteHandler.reset(),this._options.session.refreshOnReset&&this._refreshSessionData(),this._succeededSinceLastAllComplete=[],this._failedSinceLastAllComplete=[],this._totalProgress&&this._totalProgress.reset()},retry:function(e){return this._manualRetry(e)},scaleImage:function(e,t){var n=this;return qq.Scaler.prototype.scaleImage(e,t,{log:qq.bind(n.log,n),getFile:qq.bind(n.getFile,n),uploadData:n._uploadData})},setCustomHeaders:function(e,t){this._customHeadersStore.set(e,t)},setDeleteFileCustomHeaders:function(e,t){this._deleteFileCustomHeadersStore.set(e,t)},setDeleteFileEndpoint:function(e,t){this._deleteFileEndpointStore.set(e,t)},setDeleteFileParams:function(e,t){this._deleteFileParamsStore.set(e,t)},setEndpoint:function(e,t){this._endpointStore.set(e,t)},setForm:function(e){this._updateFormSupportAndParams(e)},setItemLimit:function(e){this._currentItemLimit=e},setName:function(e,t){this._uploadData.updateName(e,t)},setParams:function(e,t){this._paramsStore.set(e,t)},setUuid:function(e,t){return this._uploadData.uuidChanged(e,t)},uploadStoredFiles:function(){0===this._storedIds.length?this._itemError("noFilesError"):this._uploadStoredFiles()}},qq.basePrivateApi={_addCannedFile:function(e){var t=this._uploadData.addFile({uuid:e.uuid,name:e.name,size:e.size,status:qq.status.UPLOAD_SUCCESSFUL});return e.deleteFileEndpoint&&this.setDeleteFileEndpoint(e.deleteFileEndpoint,t),e.deleteFileParams&&this.setDeleteFileParams(e.deleteFileParams,t),e.thumbnailUrl&&(this._thumbnailUrls[t]=e.thumbnailUrl),this._netUploaded++,this._netUploadedOrQueued++,t},_annotateWithButtonId:function(e,t){qq.isFile(e)&&(e.qqButtonId=this._getButtonId(t))},_batchError:function(e){this._options.callbacks.onError(null,null,e,void 0)},_createDeleteHandler:function(){var e=this;return new qq.DeleteFileAjaxRequester({method:this._options.deleteFile.method.toUpperCase(),maxConnections:this._options.maxConnections,uuidParamName:this._options.request.uuidName,customHeaders:this._deleteFileCustomHeadersStore,paramsStore:this._deleteFileParamsStore,endpointStore:this._deleteFileEndpointStore,cors:this._options.cors,log:qq.bind(e.log,e),onDelete:function(t){e._onDelete(t),e._options.callbacks.onDelete(t)},onDeleteComplete:function(t,n,i){e._onDeleteComplete(t,n,i),e._options.callbacks.onDeleteComplete(t,n,i)}})},_createPasteHandler:function(){var e=this;return new qq.PasteSupport({targetElement:this._options.paste.targetElement,callbacks:{log:qq.bind(e.log,e),pasteReceived:function(t){e._handleCheckedCallback({name:"onPasteReceived",callback:qq.bind(e._options.callbacks.onPasteReceived,e,t),onSuccess:qq.bind(e._handlePasteSuccess,e,t),identifier:"pasted image"})}}})},_createStore:function(e,t){var n={},i=e,o={},r=t,a=function(e){return qq.isObject(e)?qq.extend({},e):e},s=function(){return qq.isFunction(r)?r():r},l=function(e,t){r&&qq.isObject(t)&&qq.extend(t,s()),o[e]&&qq.extend(t,o[e])};return{set:function(e,t){null==t?(n={},i=a(e)):n[t]=a(e)},get:function(e){var t;return t=null!=e&&n[e]?n[e]:a(i),l(e,t),a(t)},addReadOnly:function(e,t){qq.isObject(n)&&(null===e?qq.isFunction(t)?r=t:(r=r||{},qq.extend(r,t)):(o[e]=o[e]||{},qq.extend(o[e],t)))},remove:function(e){return delete n[e]},reset:function(){n={},o={},i=e}}},_createUploadDataTracker:function(){var e=this;return new qq.UploadData({getName:function(t){return e.getName(t)},getUuid:function(t){return e.getUuid(t)},getSize:function(t){return e.getSize(t)},onStatusChange:function(t,n,i){e._onUploadStatusChange(t,n,i),e._options.callbacks.onStatusChange(t,n,i),e._maybeAllComplete(t,i),e._totalProgress&&setTimeout(function(){e._totalProgress.onStatusChange(t,n,i)},0)}})},_createUploadButton:function(e){function t(){return!!qq.supportedFeatures.ajaxUploading&&(!(i._options.workarounds.iosEmptyVideos&&qq.ios()&&!qq.ios6()&&i._isAllowedExtension(r,".mov"))&&(void 0===e.multiple?i._options.multiple:e.multiple))}var n,i=this,o=e.accept||this._options.validation.acceptFiles,r=e.allowedExtensions||this._options.validation.allowedExtensions;return n=new qq.UploadButton({acceptFiles:o,element:e.element,focusClass:this._options.classes.buttonFocus,folders:e.folders,hoverClass:this._options.classes.buttonHover,ios8BrowserCrashWorkaround:this._options.workarounds.ios8BrowserCrash,multiple:t(),name:this._options.request.inputName,onChange:function(e){i._onInputChange(e)},title:null==e.title?this._options.text.fileInputTitle:e.title}),this._disposeSupport.addDisposer(function(){n.dispose()}),i._buttons.push(n),n},_createUploadHandler:function(e,t){var n=this,i={},o={debug:this._options.debug,maxConnections:this._options.maxConnections,cors:this._options.cors,paramsStore:this._paramsStore,endpointStore:this._endpointStore,chunking:this._options.chunking,resume:this._options.resume,blobs:this._options.blobs,log:qq.bind(n.log,n),preventRetryParam:this._options.retry.preventRetryResponseProperty,onProgress:function(e,t,o,r){o<0||r<0||(i[e]?i[e].loaded===o&&i[e].total===r||(n._onProgress(e,t,o,r),n._options.callbacks.onProgress(e,t,o,r)):(n._onProgress(e,t,o,r),n._options.callbacks.onProgress(e,t,o,r)),i[e]={loaded:o,total:r})},onComplete:function(e,t,o,r){delete i[e];var a,s=n.getUploads({id:e}).status;s!==qq.status.UPLOAD_SUCCESSFUL&&s!==qq.status.UPLOAD_FAILED&&(a=n._onComplete(e,t,o,r),a instanceof qq.Promise?a.done(function(){n._options.callbacks.onComplete(e,t,o,r)}):n._options.callbacks.onComplete(e,t,o,r))},onCancel:function(e,t,i){var o=new qq.Promise;return n._handleCheckedCallback({name:"onCancel",callback:qq.bind(n._options.callbacks.onCancel,n,e,t),onFailure:o.failure,onSuccess:function(){i.then(function(){n._onCancel(e,t)}),o.success()},identifier:e}),o},onUploadPrep:qq.bind(this._onUploadPrep,this),onUpload:function(e,t){n._onUpload(e,t),n._options.callbacks.onUpload(e,t)},onUploadChunk:function(e,t,i){n._onUploadChunk(e,i),n._options.callbacks.onUploadChunk(e,t,i)},onUploadChunkSuccess:function(e,t,i,o){n._options.callbacks.onUploadChunkSuccess.apply(n,arguments)},onResume:function(e,t,i){return n._options.callbacks.onResume(e,t,i)},onAutoRetry:function(e,t,i,o){return n._onAutoRetry.apply(n,arguments)},onUuidChanged:function(e,t){n.log("Server requested UUID change from '"+n.getUuid(e)+"' to '"+t+"'"),n.setUuid(e,t)},getName:qq.bind(n.getName,n),getUuid:qq.bind(n.getUuid,n),getSize:qq.bind(n.getSize,n),setSize:qq.bind(n._setSize,n),getDataByUuid:function(e){return n.getUploads({uuid:e})},isQueued:function(e){var t=n.getUploads({id:e}).status;return t===qq.status.QUEUED||t===qq.status.SUBMITTED||t===qq.status.UPLOAD_RETRYING||t===qq.status.PAUSED},getIdsInProxyGroup:n._uploadData.getIdsInProxyGroup,getIdsInBatch:n._uploadData.getIdsInBatch};return qq.each(this._options.request,function(e,t){o[e]=t}),o.customHeaders=this._customHeadersStore,e&&qq.each(e,function(e,t){o[e]=t}),new qq.UploadHandlerController(o,t)},_fileOrBlobRejected:function(e){this._netUploadedOrQueued--,this._uploadData.setStatus(e,qq.status.REJECTED)},_formatSize:function(e){var t=-1;do e/=1e3,t++;while(e>999);return Math.max(e,.1).toFixed(1)+this._options.text.sizeSymbols[t]},_generateExtraButtonSpecs:function(){var e=this;this._extraButtonSpecs={},qq.each(this._options.extraButtons,function(t,n){var i=n.multiple,o=qq.extend({},e._options.validation,!0),r=qq.extend({},n);void 0===i&&(i=e._options.multiple),r.validation&&qq.extend(o,n.validation,!0),qq.extend(r,{multiple:i,validation:o},!0),e._initExtraButton(r)})},_getButton:function(e){var t=this._extraButtonSpecs[e];return t?t.element:e===this._defaultButtonId?this._options.button:void 0},_getButtonId:function(e){var t,n,i=e;if(i instanceof qq.BlobProxy&&(i=i.referenceBlob),i&&!qq.isBlob(i)){if(qq.isFile(i))return i.qqButtonId;if("input"===i.tagName.toLowerCase()&&"file"===i.type.toLowerCase())return i.getAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME);if(t=i.getElementsByTagName("input"),qq.each(t,function(e,t){if("file"===t.getAttribute("type"))return n=t,!1}),n)return n.getAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME)}},_getNotFinished:function(){return this._uploadData.retrieve({status:[qq.status.UPLOADING,qq.status.UPLOAD_RETRYING,qq.status.QUEUED,qq.status.SUBMITTING,qq.status.SUBMITTED,qq.status.PAUSED]}).length},_getValidationBase:function(e){var t=this._extraButtonSpecs[e];return t?t.validation:this._options.validation},_getValidationDescriptor:function(e){return e.file instanceof qq.BlobProxy?{name:qq.getFilename(e.file.referenceBlob),size:e.file.referenceBlob.size}:{name:this.getUploads({id:e.id}).name,size:this.getUploads({id:e.id}).size}},_getValidationDescriptors:function(e){var t=this,n=[];return qq.each(e,function(e,i){n.push(t._getValidationDescriptor(i))}),n},_handleCameraAccess:function(){if(this._options.camera.ios&&qq.ios()){var e="image/*;capture=camera",t=this._options.camera.button,n=t?this._getButtonId(t):this._defaultButtonId,i=this._options;n&&n!==this._defaultButtonId&&(i=this._extraButtonSpecs[n]),i.multiple=!1,null===i.validation.acceptFiles?i.validation.acceptFiles=e:i.validation.acceptFiles+=","+e,qq.each(this._buttons,function(e,t){if(t.getButtonId()===n)return t.setMultiple(i.multiple),t.setAcceptFiles(i.acceptFiles),!1})}},_handleCheckedCallback:function(e){var t=this,n=e.callback();return qq.isGenericPromise(n)?(this.log(e.name+" - waiting for "+e.name+" promise to be fulfilled for "+e.identifier),n.then(function(n){t.log(e.name+" promise success for "+e.identifier),e.onSuccess(n)},function(){e.onFailure?(t.log(e.name+" promise failure for "+e.identifier),e.onFailure()):t.log(e.name+" promise failure for "+e.identifier)})):(n!==!1?e.onSuccess(n):e.onFailure?(this.log(e.name+" - return value was 'false' for "+e.identifier+".  Invoking failure callback."),e.onFailure()):this.log(e.name+" - return value was 'false' for "+e.identifier+".  Will not proceed."),n)},_handleNewFile:function(e,t,n){var i=this,o=qq.getUniqueId(),r=-1,a=qq.getFilename(e),s=e.blob||e,l=this._customNewFileHandler?this._customNewFileHandler:qq.bind(i._handleNewFileGeneric,i);!qq.isInput(s)&&s.size>=0&&(r=s.size),l(s,a,o,r,n,t,this._options.request.uuidName,{uploadData:i._uploadData,paramsStore:i._paramsStore,addFileToHandler:function(e,t){i._handler.add(e,t),i._netUploadedOrQueued++,i._trackButton(e)}})},_handleNewFileGeneric:function(e,t,n,i,o,r){
	var a=this._uploadData.addFile({uuid:n,name:t,size:i,batchId:r});this._handler.add(a,e),this._trackButton(a),this._netUploadedOrQueued++,o.push({id:a,file:e})},_handlePasteSuccess:function(e,t){var n=e.type.split("/")[1],i=t;null==i&&(i=this._options.paste.defaultName),i+="."+n,this.addFiles({name:i,blob:e})},_initExtraButton:function(e){var t=this._createUploadButton({accept:e.validation.acceptFiles,allowedExtensions:e.validation.allowedExtensions,element:e.element,folders:e.folders,multiple:e.multiple,title:e.fileInputTitle});this._extraButtonSpecs[t.getButtonId()]=e},_initFormSupportAndParams:function(){this._formSupport=qq.FormSupport&&new qq.FormSupport(this._options.form,qq.bind(this.uploadStoredFiles,this),qq.bind(this.log,this)),this._formSupport&&this._formSupport.attachedToForm?(this._paramsStore=this._createStore(this._options.request.params,this._formSupport.getFormInputsAsObject),this._options.autoUpload=this._formSupport.newAutoUpload,this._formSupport.newEndpoint&&(this._options.request.endpoint=this._formSupport.newEndpoint)):this._paramsStore=this._createStore(this._options.request.params)},_isDeletePossible:function(){return!(!qq.DeleteFileAjaxRequester||!this._options.deleteFile.enabled)&&(!this._options.cors.expected||(!!qq.supportedFeatures.deleteFileCorsXhr||!(!qq.supportedFeatures.deleteFileCorsXdr||!this._options.cors.allowXdr)))},_isAllowedExtension:function(e,t){var n=!1;return!e.length||(qq.each(e,function(e,i){if(qq.isString(i)){var o=new RegExp("\\."+i+"$","i");if(null!=t.match(o))return n=!0,!1}}),n)},_itemError:function(e,t,n){function i(e,t){a=a.replace(e,t)}var o,r,a=this._options.messages[e],s=[],l=[].concat(t),u=l[0],c=this._getButtonId(n),d=this._getValidationBase(c);return qq.each(d.allowedExtensions,function(e,t){qq.isString(t)&&s.push(t)}),o=s.join(", ").toLowerCase(),i("{file}",this._options.formatFileName(u)),i("{extensions}",o),i("{sizeLimit}",this._formatSize(d.sizeLimit)),i("{minSizeLimit}",this._formatSize(d.minSizeLimit)),r=a.match(/(\{\w+\})/g),null!==r&&qq.each(r,function(e,t){i(t,l[e])}),this._options.callbacks.onError(null,u,a,void 0),a},_manualRetry:function(e,t){if(this._onBeforeManualRetry(e))return this._netUploadedOrQueued++,this._uploadData.setStatus(e,qq.status.UPLOAD_RETRYING),t?t(e):this._handler.retry(e),!0},_maybeAllComplete:function(e,t){var n=this,i=this._getNotFinished();t===qq.status.UPLOAD_SUCCESSFUL?this._succeededSinceLastAllComplete.push(e):t===qq.status.UPLOAD_FAILED&&this._failedSinceLastAllComplete.push(e),0===i&&(this._succeededSinceLastAllComplete.length||this._failedSinceLastAllComplete.length)&&setTimeout(function(){n._onAllComplete(n._succeededSinceLastAllComplete,n._failedSinceLastAllComplete)},0)},_maybeHandleIos8SafariWorkaround:function(){var e=this;if(this._options.workarounds.ios8SafariUploads&&qq.ios800()&&qq.iosSafari())throw setTimeout(function(){window.alert(e._options.messages.unsupportedBrowserIos8Safari)},0),new qq.Error(this._options.messages.unsupportedBrowserIos8Safari)},_maybeParseAndSendUploadError:function(e,t,n,i){if(!n.success)if(i&&200!==i.status&&!n.error)this._options.callbacks.onError(e,t,"XHR returned response code "+i.status,i);else{var o=n.error?n.error:this._options.text.defaultResponseError;this._options.callbacks.onError(e,t,o,i)}},_maybeProcessNextItemAfterOnValidateCallback:function(e,t,n,i,o){var r=this;if(t.length>n)if(e||!this._options.validation.stopOnFirstInvalidFile)setTimeout(function(){var e=r._getValidationDescriptor(t[n]),a=r._getButtonId(t[n].file),s=r._getButton(a);r._handleCheckedCallback({name:"onValidate",callback:qq.bind(r._options.callbacks.onValidate,r,e,s),onSuccess:qq.bind(r._onValidateCallbackSuccess,r,t,n,i,o),onFailure:qq.bind(r._onValidateCallbackFailure,r,t,n,i,o),identifier:"Item '"+e.name+"', size: "+e.size})},0);else if(!e)for(;n<t.length;n++)r._fileOrBlobRejected(t[n].id)},_onAllComplete:function(e,t){this._totalProgress&&this._totalProgress.onAllComplete(e,t,this._preventRetries),this._options.callbacks.onAllComplete(qq.extend([],e),qq.extend([],t)),this._succeededSinceLastAllComplete=[],this._failedSinceLastAllComplete=[]},_onAutoRetry:function(e,t,n,i,o){var r=this;if(r._preventRetries[e]=n[r._options.retry.preventRetryResponseProperty],r._shouldAutoRetry(e,t,n))return r._maybeParseAndSendUploadError.apply(r,arguments),r._options.callbacks.onAutoRetry(e,t,r._autoRetries[e]),r._onBeforeAutoRetry(e,t),r._retryTimeouts[e]=setTimeout(function(){r.log("Retrying "+t+"..."),r._uploadData.setStatus(e,qq.status.UPLOAD_RETRYING),o?o(e):r._handler.retry(e)},1e3*r._options.retry.autoAttemptDelay),!0},_onBeforeAutoRetry:function(e,t){this.log("Waiting "+this._options.retry.autoAttemptDelay+" seconds before retrying "+t+"...")},_onBeforeManualRetry:function(e){var t,n=this._currentItemLimit;return this._preventRetries[e]?(this.log("Retries are forbidden for id "+e,"warn"),!1):this._handler.isValid(e)?(t=this.getName(e),this._options.callbacks.onManualRetry(e,t)!==!1&&(n>0&&this._netUploadedOrQueued+1>n?(this._itemError("retryFailTooManyItems"),!1):(this.log("Retrying upload for '"+t+"' (id: "+e+")..."),!0))):(this.log("'"+e+"' is not a valid file ID","error"),!1)},_onCancel:function(e,t){this._netUploadedOrQueued--,clearTimeout(this._retryTimeouts[e]);var n=qq.indexOf(this._storedIds,e);!this._options.autoUpload&&n>=0&&this._storedIds.splice(n,1),this._uploadData.setStatus(e,qq.status.CANCELED)},_onComplete:function(e,t,n,i){return n.success?(n.thumbnailUrl&&(this._thumbnailUrls[e]=n.thumbnailUrl),this._netUploaded++,this._uploadData.setStatus(e,qq.status.UPLOAD_SUCCESSFUL)):(this._netUploadedOrQueued--,this._uploadData.setStatus(e,qq.status.UPLOAD_FAILED),n[this._options.retry.preventRetryResponseProperty]===!0&&(this._preventRetries[e]=!0)),this._maybeParseAndSendUploadError(e,t,n,i),!!n.success},_onDelete:function(e){this._uploadData.setStatus(e,qq.status.DELETING)},_onDeleteComplete:function(e,t,n){var i=this.getName(e);n?(this._uploadData.setStatus(e,qq.status.DELETE_FAILED),this.log("Delete request for '"+i+"' has failed.","error"),void 0===t.withCredentials?this._options.callbacks.onError(e,i,"Delete request failed",t):this._options.callbacks.onError(e,i,"Delete request failed with response code "+t.status,t)):(this._netUploadedOrQueued--,this._netUploaded--,this._handler.expunge(e),this._uploadData.setStatus(e,qq.status.DELETED),this.log("Delete request for '"+i+"' has succeeded."))},_onInputChange:function(e){var t;if(qq.supportedFeatures.ajaxUploading){for(t=0;t<e.files.length;t++)this._annotateWithButtonId(e.files[t],e);this.addFiles(e.files)}else e.value.length>0&&this.addFiles(e);qq.each(this._buttons,function(e,t){t.reset()})},_onProgress:function(e,t,n,i){this._totalProgress&&this._totalProgress.onIndividualProgress(e,n,i)},_onSubmit:function(e,t){},_onSubmitCallbackSuccess:function(e,t){this._onSubmit.apply(this,arguments),this._uploadData.setStatus(e,qq.status.SUBMITTED),this._onSubmitted.apply(this,arguments),this._options.autoUpload?(this._options.callbacks.onSubmitted.apply(this,arguments),this._uploadFile(e)):(this._storeForLater(e),this._options.callbacks.onSubmitted.apply(this,arguments))},_onSubmitDelete:function(e,t,n){var i,o=this.getUuid(e);return t&&(i=qq.bind(t,this,e,o,n)),this._isDeletePossible()?(this._handleCheckedCallback({name:"onSubmitDelete",callback:qq.bind(this._options.callbacks.onSubmitDelete,this,e),onSuccess:i||qq.bind(this._deleteHandler.sendDelete,this,e,o,n),identifier:e}),!0):(this.log("Delete request ignored for ID "+e+", delete feature is disabled or request not possible due to CORS on a user agent that does not support pre-flighting.","warn"),!1)},_onSubmitted:function(e){},_onTotalProgress:function(e,t){this._options.callbacks.onTotalProgress(e,t)},_onUploadPrep:function(e){},_onUpload:function(e,t){this._uploadData.setStatus(e,qq.status.UPLOADING)},_onUploadChunk:function(e,t){},_onUploadStatusChange:function(e,t,n){n===qq.status.PAUSED&&clearTimeout(this._retryTimeouts[e])},_onValidateBatchCallbackFailure:function(e){var t=this;qq.each(e,function(e,n){t._fileOrBlobRejected(n.id)})},_onValidateBatchCallbackSuccess:function(e,t,n,i,o){var r,a=this._currentItemLimit,s=this._netUploadedOrQueued;0===a||s<=a?t.length>0?this._handleCheckedCallback({name:"onValidate",callback:qq.bind(this._options.callbacks.onValidate,this,e[0],o),onSuccess:qq.bind(this._onValidateCallbackSuccess,this,t,0,n,i),onFailure:qq.bind(this._onValidateCallbackFailure,this,t,0,n,i),identifier:"Item '"+t[0].file.name+"', size: "+t[0].file.size}):this._itemError("noFilesError"):(this._onValidateBatchCallbackFailure(t),r=this._options.messages.tooManyItemsError.replace(/\{netItems\}/g,s).replace(/\{itemLimit\}/g,a),this._batchError(r))},_onValidateCallbackFailure:function(e,t,n,i){var o=t+1;this._fileOrBlobRejected(e[t].id,e[t].file.name),this._maybeProcessNextItemAfterOnValidateCallback(!1,e,o,n,i)},_onValidateCallbackSuccess:function(e,t,n,i){var o=this,r=t+1,a=this._getValidationDescriptor(e[t]);this._validateFileOrBlobData(e[t],a).then(function(){o._upload(e[t].id,n,i),o._maybeProcessNextItemAfterOnValidateCallback(!0,e,r,n,i)},function(){o._maybeProcessNextItemAfterOnValidateCallback(!1,e,r,n,i)})},_prepareItemsForUpload:function(e,t,n){if(0===e.length)return void this._itemError("noFilesError");var i=this._getValidationDescriptors(e),o=this._getButtonId(e[0].file),r=this._getButton(o);this._handleCheckedCallback({name:"onValidateBatch",callback:qq.bind(this._options.callbacks.onValidateBatch,this,i,r),onSuccess:qq.bind(this._onValidateBatchCallbackSuccess,this,i,e,t,n,r),onFailure:qq.bind(this._onValidateBatchCallbackFailure,this,e),identifier:"batch validation"})},_preventLeaveInProgress:function(){var e=this;this._disposeSupport.attach(window,"beforeunload",function(t){if(e.getInProgress())return t=t||window.event,t.returnValue=e._options.messages.onLeave,e._options.messages.onLeave})},_refreshSessionData:function(){var e=this,t=this._options.session;qq.Session&&null!=this._options.session.endpoint&&(this._session||(qq.extend(t,{cors:this._options.cors}),t.log=qq.bind(this.log,this),t.addFileRecord=qq.bind(this._addCannedFile,this),this._session=new qq.Session(t)),setTimeout(function(){e._session.refresh().then(function(t,n){e._sessionRequestComplete(),e._options.callbacks.onSessionRequestComplete(t,!0,n)},function(t,n){e._options.callbacks.onSessionRequestComplete(t,!1,n)})},0))},_sessionRequestComplete:function(){},_setSize:function(e,t){this._uploadData.updateSize(e,t),this._totalProgress&&this._totalProgress.onNewSize(e)},_shouldAutoRetry:function(e,t,n){var i=this._uploadData.retrieve({id:e});return!!(!this._preventRetries[e]&&this._options.retry.enableAuto&&i.status!==qq.status.PAUSED&&(void 0===this._autoRetries[e]&&(this._autoRetries[e]=0),this._autoRetries[e]<this._options.retry.maxAutoAttempts))&&(this._autoRetries[e]+=1,!0)},_storeForLater:function(e){this._storedIds.push(e)},_trackButton:function(e){var t;t=qq.supportedFeatures.ajaxUploading?this._handler.getFile(e).qqButtonId:this._getButtonId(this._handler.getInput(e)),t&&(this._buttonIdsForFileIds[e]=t)},_updateFormSupportAndParams:function(e){this._options.form.element=e,this._formSupport=qq.FormSupport&&new qq.FormSupport(this._options.form,qq.bind(this.uploadStoredFiles,this),qq.bind(this.log,this)),this._formSupport&&this._formSupport.attachedToForm&&(this._paramsStore.addReadOnly(null,this._formSupport.getFormInputsAsObject),this._options.autoUpload=this._formSupport.newAutoUpload,this._formSupport.newEndpoint&&this.setEndpoint(this._formSupport.newEndpoint))},_upload:function(e,t,n){var i=this.getName(e);t&&this.setParams(t,e),n&&this.setEndpoint(n,e),this._handleCheckedCallback({name:"onSubmit",callback:qq.bind(this._options.callbacks.onSubmit,this,e,i),onSuccess:qq.bind(this._onSubmitCallbackSuccess,this,e,i),onFailure:qq.bind(this._fileOrBlobRejected,this,e,i),identifier:e})},_uploadFile:function(e){this._handler.upload(e)||this._uploadData.setStatus(e,qq.status.QUEUED)},_uploadStoredFiles:function(){for(var e,t,n=this;this._storedIds.length;)e=this._storedIds.shift(),this._uploadFile(e);t=this.getUploads({status:qq.status.SUBMITTING}).length,t&&(qq.log("Still waiting for "+t+" files to clear submit queue. Will re-parse stored IDs array shortly."),setTimeout(function(){n._uploadStoredFiles()},1e3))},_validateFileOrBlobData:function(e,t){var n=this,i=function(){return e.file instanceof qq.BlobProxy?e.file.referenceBlob:e.file}(),o=t.name,r=t.size,a=this._getButtonId(e.file),s=this._getValidationBase(a),l=new qq.Promise;return l.then(function(){},function(){n._fileOrBlobRejected(e.id,o)}),qq.isFileOrInput(i)&&!this._isAllowedExtension(s.allowedExtensions,o)?(this._itemError("typeError",o,i),l.failure()):0===r?(this._itemError("emptyError",o,i),l.failure()):r>0&&s.sizeLimit&&r>s.sizeLimit?(this._itemError("sizeError",o,i),l.failure()):r>0&&r<s.minSizeLimit?(this._itemError("minSizeError",o,i),l.failure()):(qq.ImageValidation&&qq.supportedFeatures.imagePreviews&&qq.isFile(i)?new qq.ImageValidation(i,qq.bind(n.log,n)).validate(s.image).then(l.success,function(e){n._itemError(e+"ImageError",o,i),l.failure()}):l.success(),l)},_wrapCallbacks:function(){var e,t,n;e=this,t=function(t,n,i){var o;try{return n.apply(e,i)}catch(n){o=n.message||n.toString(),e.log("Caught exception in '"+t+"' callback - "+o,"error")}};for(n in this._options.callbacks)!function(){var i,o;i=n,o=e._options.callbacks[i],e._options.callbacks[i]=function(){return t(i,o,arguments)}}()}}}(),function(){"use strict";qq.FineUploaderBasic=function(e){var t=this;this._options={debug:!1,button:null,multiple:!0,maxConnections:3,disableCancelForFormUploads:!1,autoUpload:!0,request:{customHeaders:{},endpoint:"/server/upload",filenameParam:"qqfilename",forceMultipart:!0,inputName:"qqfile",method:"POST",params:{},paramsInBody:!0,totalFileSizeName:"qqtotalfilesize",uuidName:"qquuid"},validation:{allowedExtensions:[],sizeLimit:0,minSizeLimit:0,itemLimit:0,stopOnFirstInvalidFile:!0,acceptFiles:null,image:{maxHeight:0,maxWidth:0,minHeight:0,minWidth:0}},callbacks:{onSubmit:function(e,t){},onSubmitted:function(e,t){},onComplete:function(e,t,n,i){},onAllComplete:function(e,t){},onCancel:function(e,t){},onUpload:function(e,t){},onUploadChunk:function(e,t,n){},onUploadChunkSuccess:function(e,t,n,i){},onResume:function(e,t,n){},onProgress:function(e,t,n,i){},onTotalProgress:function(e,t){},onError:function(e,t,n,i){},onAutoRetry:function(e,t,n){},onManualRetry:function(e,t){},onValidateBatch:function(e){},onValidate:function(e){},onSubmitDelete:function(e){},onDelete:function(e){},onDeleteComplete:function(e,t,n){},onPasteReceived:function(e){},onStatusChange:function(e,t,n){},onSessionRequestComplete:function(e,t,n){}},messages:{typeError:"{file} has an invalid extension. Valid extension(s): {extensions}.",sizeError:"{file} is too large, maximum file size is {sizeLimit}.",minSizeError:"{file} is too small, minimum file size is {minSizeLimit}.",emptyError:"{file} is empty, please select files again without it.",noFilesError:"No files to upload.",tooManyItemsError:"Too many items ({netItems}) would be uploaded.  Item limit is {itemLimit}.",maxHeightImageError:"Image is too tall.",maxWidthImageError:"Image is too wide.",minHeightImageError:"Image is not tall enough.",minWidthImageError:"Image is not wide enough.",retryFailTooManyItems:"Retry failed - you have reached your file limit.",onLeave:"The files are being uploaded, if you leave now the upload will be canceled.",unsupportedBrowserIos8Safari:"Unrecoverable error - this browser does not permit file uploading of any kind due to serious bugs in iOS8 Safari.  Please use iOS8 Chrome until Apple fixes these issues."},retry:{enableAuto:!1,maxAutoAttempts:3,autoAttemptDelay:5,preventRetryResponseProperty:"preventRetry"},classes:{buttonHover:"qq-upload-button-hover",buttonFocus:"qq-upload-button-focus"},chunking:{enabled:!1,concurrent:{enabled:!1},mandatory:!1,paramNames:{partIndex:"qqpartindex",partByteOffset:"qqpartbyteoffset",chunkSize:"qqchunksize",totalFileSize:"qqtotalfilesize",totalParts:"qqtotalparts"},partSize:2e6,success:{endpoint:null}},resume:{enabled:!1,recordsExpireIn:7,paramNames:{resuming:"qqresume"}},formatFileName:function(e){return e},text:{defaultResponseError:"Upload failure reason unknown",fileInputTitle:"file input",sizeSymbols:["kB","MB","GB","TB","PB","EB"]},deleteFile:{enabled:!1,method:"DELETE",endpoint:"/server/upload",customHeaders:{},params:{}},cors:{expected:!1,sendCredentials:!1,allowXdr:!1},blobs:{defaultName:"misc_data"},paste:{targetElement:null,defaultName:"pasted_image"},camera:{ios:!1,button:null},extraButtons:[],session:{endpoint:null,params:{},customHeaders:{},refreshOnReset:!0},form:{element:"qq-form",autoUpload:!1,interceptSubmit:!0},scaling:{customResizer:null,sendOriginal:!0,orient:!0,defaultType:null,defaultQuality:80,failureText:"Failed to scale",includeExif:!1,sizes:[]},workarounds:{iosEmptyVideos:!0,ios8SafariUploads:!0,ios8BrowserCrash:!1}},qq.extend(this._options,e,!0),this._buttons=[],this._extraButtonSpecs={},this._buttonIdsForFileIds=[],this._wrapCallbacks(),this._disposeSupport=new qq.DisposeSupport,this._storedIds=[],this._autoRetries=[],this._retryTimeouts=[],this._preventRetries=[],this._thumbnailUrls=[],this._netUploadedOrQueued=0,this._netUploaded=0,this._uploadData=this._createUploadDataTracker(),this._initFormSupportAndParams(),this._customHeadersStore=this._createStore(this._options.request.customHeaders),this._deleteFileCustomHeadersStore=this._createStore(this._options.deleteFile.customHeaders),this._deleteFileParamsStore=this._createStore(this._options.deleteFile.params),this._endpointStore=this._createStore(this._options.request.endpoint),this._deleteFileEndpointStore=this._createStore(this._options.deleteFile.endpoint),this._handler=this._createUploadHandler(),this._deleteHandler=qq.DeleteFileAjaxRequester&&this._createDeleteHandler(),this._options.button&&(this._defaultButtonId=this._createUploadButton({element:this._options.button,title:this._options.text.fileInputTitle}).getButtonId()),this._generateExtraButtonSpecs(),this._handleCameraAccess(),this._options.paste.targetElement&&(qq.PasteSupport?this._pasteHandler=this._createPasteHandler():this.log("Paste support module not found","error")),this._preventLeaveInProgress(),this._imageGenerator=qq.ImageGenerator&&new qq.ImageGenerator(qq.bind(this.log,this)),this._refreshSessionData(),this._succeededSinceLastAllComplete=[],this._failedSinceLastAllComplete=[],this._scaler=qq.Scaler&&new qq.Scaler(this._options.scaling,qq.bind(this.log,this))||{},this._scaler.enabled&&(this._customNewFileHandler=qq.bind(this._scaler.handleNewFile,this._scaler)),qq.TotalProgress&&qq.supportedFeatures.progressBar&&(this._totalProgress=new qq.TotalProgress(qq.bind(this._onTotalProgress,this),function(e){var n=t._uploadData.retrieve({id:e});return n&&n.size||0})),this._currentItemLimit=this._options.validation.itemLimit},qq.FineUploaderBasic.prototype=qq.basePublicApi,qq.extend(qq.FineUploaderBasic.prototype,qq.basePrivateApi)}(),qq.AjaxRequester=function(e){"use strict";function t(){return qq.indexOf(["GET","POST","HEAD"],S.method)>=0}function n(e){var t=!1;return qq.each(t,function(e,n){if(qq.indexOf(["Accept","Accept-Language","Content-Language","Content-Type"],n)<0)return t=!0,!1}),t}function i(e){return S.cors.expected&&void 0===e.withCredentials}function o(){var e;return(window.XMLHttpRequest||window.ActiveXObject)&&(e=qq.createXhrInstance(),void 0===e.withCredentials&&(e=new XDomainRequest,e.onload=function(){},e.onerror=function(){},e.ontimeout=function(){},e.onprogress=function(){})),e}function r(e,t){var n=y[e].xhr;return n||(n=t?t:S.cors.expected?o():qq.createXhrInstance(),y[e].xhr=n),n}function a(e){var t,n=qq.indexOf(v,e),i=S.maxConnections;delete y[e],v.splice(n,1),v.length>=i&&n<i&&(t=v[i-1],u(t))}function s(e,t){var n=r(e),o=S.method,s=t===!0;a(e),s?_(o+" request for "+e+" has failed","error"):i(n)||m(n.status)||(s=!0,_(o+" request for "+e+" has failed - response code "+n.status,"error")),S.onComplete(e,n,s)}function l(e){var t,n=y[e].additionalParams,i=S.mandatedParams;return S.paramsStore.get&&(t=S.paramsStore.get(e)),n&&qq.each(n,function(e,n){t=t||{},t[e]=n}),i&&qq.each(i,function(e,n){t=t||{},t[e]=n}),t}function u(e,t){var n,o=r(e,t),a=S.method,s=l(e),u=y[e].payload;return S.onSend(e),n=c(e,s,y[e].additionalQueryParams),i(o)?(o.onload=h(e),o.onerror=q(e)):o.onreadystatechange=d(e),p(e),o.open(a,n,!0),S.cors.expected&&S.cors.sendCredentials&&!i(o)&&(o.withCredentials=!0),f(e),_("Sending "+a+" request for "+e),u?o.send(u):b||!s?o.send():s&&S.contentType&&S.contentType.toLowerCase().indexOf("application/x-www-form-urlencoded")>=0?o.send(qq.obj2url(s,"")):s&&S.contentType&&S.contentType.toLowerCase().indexOf("application/json")>=0?o.send(JSON.stringify(s)):o.send(s),o}function c(e,t,n){var i=S.endpointStore.get(e),o=y[e].addToPath;return void 0!=o&&(i+="/"+o),b&&t&&(i=qq.obj2url(t,i)),n&&(i=qq.obj2url(n,i)),i}function d(e){return function(){4===r(e).readyState&&s(e)}}function p(e){var t=S.onProgress;t&&(r(e).upload.onprogress=function(n){n.lengthComputable&&t(e,n.loaded,n.total)})}function h(e){return function(){s(e)}}function q(e){return function(){s(e,!0)}}function f(e){var o=r(e),a=S.customHeaders,s=y[e].additionalHeaders||{},l=S.method,u={};i(o)||(S.acceptHeader&&o.setRequestHeader("Accept",S.acceptHeader),S.allowXRequestedWithAndCacheControl&&(S.cors.expected&&t()&&!n(a)||(o.setRequestHeader("X-Requested-With","XMLHttpRequest"),o.setRequestHeader("Cache-Control","no-cache"))),!S.contentType||"POST"!==l&&"PUT"!==l||o.setRequestHeader("Content-Type",S.contentType),qq.extend(u,qq.isFunction(a)?a(e):a),qq.extend(u,s),qq.each(u,function(e,t){o.setRequestHeader(e,t)}))}function m(e){return qq.indexOf(S.successfulResponseCodes[S.method],e)>=0}function g(e,t,n,i,o,r,a){y[e]={addToPath:n,additionalParams:i,additionalQueryParams:o,additionalHeaders:r,payload:a};var s=v.push(e);if(s<=S.maxConnections)return u(e,t)}var _,b,v=[],y={},S={acceptHeader:null,validMethods:["PATCH","POST","PUT"],method:"POST",contentType:"application/x-www-form-urlencoded",maxConnections:3,customHeaders:{},endpointStore:{},paramsStore:{},mandatedParams:{},allowXRequestedWithAndCacheControl:!0,successfulResponseCodes:{DELETE:[200,202,204],PATCH:[200,201,202,203,204],POST:[200,201,202,203,204],PUT:[200,201,202,203,204],GET:[200]},cors:{expected:!1,sendCredentials:!1},log:function(e,t){},onSend:function(e){},onComplete:function(e,t,n){},onProgress:null};if(qq.extend(S,e),_=S.log,qq.indexOf(S.validMethods,S.method)<0)throw new Error("'"+S.method+"' is not a supported method for this type of request!");b="GET"===S.method||"DELETE"===S.method,qq.extend(this,{initTransport:function(e){var t,n,i,o,r,a;return{withPath:function(e){return t=e,this},withParams:function(e){return n=e,this},withQueryParams:function(e){return a=e,this},withHeaders:function(e){return i=e,this},withPayload:function(e){return o=e,this},withCacheBuster:function(){return r=!0,this},send:function(s){return r&&qq.indexOf(["GET","DELETE"],S.method)>=0&&(n.qqtimestamp=(new Date).getTime()),g(e,s,t,n,a,i,o)}}},canceled:function(e){a(e)}})},qq.UploadHandler=function(e){"use strict";var t=e.proxy,n={},i=t.onCancel,o=t.getName;qq.extend(this,{add:function(e,t){n[e]=t,n[e].temp={}},cancel:function(e){var t=this,r=new qq.Promise,a=i(e,o(e),r);a.then(function(){t.isValid(e)&&(n[e].canceled=!0,t.expunge(e)),r.success()})},expunge:function(e){delete n[e]},getThirdPartyFileId:function(e){return n[e].key},isValid:function(e){return void 0!==n[e]},reset:function(){n={}},_getFileState:function(e){return n[e]},_setThirdPartyFileId:function(e,t){n[e].key=t},_wasCanceled:function(e){return!!n[e].canceled}})},qq.UploadHandlerController=function(e,t){"use strict";var n,i,o,r=this,a=!1,s=!1,l={paramsStore:{},maxConnections:3,chunking:{enabled:!1,multiple:{enabled:!1}},log:function(e,t){},onProgress:function(e,t,n,i){},onComplete:function(e,t,n,i){},onCancel:function(e,t){},onUploadPrep:function(e){},onUpload:function(e,t){},onUploadChunk:function(e,t,n){},onUploadChunkSuccess:function(e,t,n,i){},onAutoRetry:function(e,t,n,i){},onResume:function(e,t,n){},onUuidChanged:function(e,t){},getName:function(e){},setSize:function(e,t){},isQueued:function(e){},getIdsInProxyGroup:function(e){},getIdsInBatch:function(e){}},u={done:function(e,t,n,i){var r=o._getChunkData(e,t);o._getFileState(e).attemptingResume=!1,delete o._getFileState(e).temp.chunkProgress[t],o._getFileState(e).loaded+=r.size,l.onUploadChunkSuccess(e,o._getChunkDataForCallback(r),n,i)},finalize:function(e){var t=l.getSize(e),n=l.getName(e);i("All chunks have been uploaded for "+e+" - finalizing...."),o.finalizeChunks(e).then(function(r,a){i("Finalize successful for "+e);var s=p.normalizeResponse(r,!0);l.onProgress(e,n,t,t),o._maybeDeletePersistedChunkData(e),p.cleanup(e,s,a)},function(t,o){var r=p.normalizeResponse(t,!1);i("Problem finalizing chunks for file ID "+e+" - "+r.error,"error"),r.reset&&u.reset(e),l.onAutoRetry(e,n,r,o)||p.cleanup(e,r,o)})},hasMoreParts:function(e){return!!o._getFileState(e).chunking.remaining.length},nextPart:function(e){var t=o._getFileState(e).chunking.remaining.shift();return t>=o._getTotalChunks(e)&&(t=null),t},reset:function(e){i("Server or callback has ordered chunking effort to be restarted on next attempt for item ID "+e,"error"),o._maybeDeletePersistedChunkData(e),o.reevaluateChunking(e),o._getFileState(e).loaded=0},sendNext:function(e){var t=l.getSize(e),n=l.getName(e),r=u.nextPart(e),a=o._getChunkData(e,r),d=o._getFileState(e).attemptingResume,h=o._getFileState(e).chunking.inProgress||[];null==o._getFileState(e).loaded&&(o._getFileState(e).loaded=0),d&&l.onResume(e,n,a)===!1&&(u.reset(e),r=u.nextPart(e),a=o._getChunkData(e,r),d=!1),null==r&&0===h.length?u.finalize(e):(i(qq.format("Sending chunked upload request for item {}.{}, bytes {}-{} of {}.",e,r,a.start+1,a.end,t)),l.onUploadChunk(e,n,o._getChunkDataForCallback(a)),h.push(r),o._getFileState(e).chunking.inProgress=h,s&&c.open(e,r),s&&c.available()&&o._getFileState(e).chunking.remaining.length&&u.sendNext(e),o.uploadChunk(e,r,d).then(function(t,n){i("Chunked upload request succeeded for "+e+", chunk "+r),o.clearCachedChunk(e,r);var a=o._getFileState(e).chunking.inProgress||[],s=p.normalizeResponse(t,!0),l=qq.indexOf(a,r);i(qq.format("Chunk {} for file {} uploaded successfully.",r,e)),u.done(e,r,s,n),l>=0&&a.splice(l,1),o._maybePersistChunkedState(e),u.hasMoreParts(e)||0!==a.length?u.hasMoreParts(e)?u.sendNext(e):i(qq.format("File ID {} has no more chunks to send and these chunk indexes are still marked as in-progress: {}",e,JSON.stringify(a))):u.finalize(e)},function(t,a){i("Chunked upload request failed for "+e+", chunk "+r),o.clearCachedChunk(e,r);var d,h=p.normalizeResponse(t,!1);h.reset?u.reset(e):(d=qq.indexOf(o._getFileState(e).chunking.inProgress,r),d>=0&&(o._getFileState(e).chunking.inProgress.splice(d,1),o._getFileState(e).chunking.remaining.unshift(r))),o._getFileState(e).temp.ignoreFailure||(s&&(o._getFileState(e).temp.ignoreFailure=!0,i(qq.format("Going to attempt to abort these chunks: {}. These are currently in-progress: {}.",JSON.stringify(Object.keys(o._getXhrs(e))),JSON.stringify(o._getFileState(e).chunking.inProgress))),qq.each(o._getXhrs(e),function(t,n){i(qq.format("Attempting to abort file {}.{}. XHR readyState {}. ",e,t,n.readyState)),n.abort(),n._cancelled=!0}),o.moveInProgressToRemaining(e),c.free(e,!0)),l.onAutoRetry(e,n,h,a)||p.cleanup(e,h,a))}).done(function(){o.clearXhr(e,r)}))}},c={_open:[],_openChunks:{},_waiting:[],available:function(){var e=l.maxConnections,t=0,n=0;return qq.each(c._openChunks,function(e,i){t++,n+=i.length}),e-(c._open.length-t+n)},free:function(e,t){var n,r=!t,a=qq.indexOf(c._waiting,e),s=qq.indexOf(c._open,e);delete c._openChunks[e],p.getProxyOrBlob(e)instanceof qq.BlobProxy&&(i("Generated blob upload has ended for "+e+", disposing generated blob."),delete o._getFileState(e).file),a>=0?c._waiting.splice(a,1):r&&s>=0&&(c._open.splice(s,1),n=c._waiting.shift(),n>=0&&(c._open.push(n),p.start(n)))},getWaitingOrConnected:function(){var e=[];return qq.each(c._openChunks,function(t,n){n&&n.length&&e.push(parseInt(t))}),qq.each(c._open,function(t,n){c._openChunks[n]||e.push(parseInt(n))}),e=e.concat(c._waiting)},isUsingConnection:function(e){return qq.indexOf(c._open,e)>=0},open:function(e,t){return null==t&&c._waiting.push(e),!!c.available()&&(null==t?(c._waiting.pop(),c._open.push(e)):!function(){var n=c._openChunks[e]||[];n.push(t),c._openChunks[e]=n}(),!0)},reset:function(){c._waiting=[],c._open=[]}},d={send:function(e,t){o._getFileState(e).loaded=0,i("Sending simple upload request for "+e),o.uploadFile(e).then(function(n,o){i("Simple upload request succeeded for "+e);var r=p.normalizeResponse(n,!0),a=l.getSize(e);l.onProgress(e,t,a,a),p.maybeNewUuid(e,r),p.cleanup(e,r,o)},function(n,o){i("Simple upload request failed for "+e);var r=p.normalizeResponse(n,!1);l.onAutoRetry(e,t,r,o)||p.cleanup(e,r,o)})}},p={cancel:function(e){i("Cancelling "+e),l.paramsStore.remove(e),c.free(e)},cleanup:function(e,t,n){var i=l.getName(e);l.onComplete(e,i,t,n),o._getFileState(e)&&o._clearXhrs&&o._clearXhrs(e),c.free(e)},getProxyOrBlob:function(e){return o.getProxy&&o.getProxy(e)||o.getFile&&o.getFile(e)},initHandler:function(){var e=t?qq[t]:qq.traditional,n=qq.supportedFeatures.ajaxUploading?"Xhr":"Form";o=new e[n+"UploadHandler"](l,{getDataByUuid:l.getDataByUuid,getName:l.getName,getSize:l.getSize,getUuid:l.getUuid,log:i,onCancel:l.onCancel,onProgress:l.onProgress,onUuidChanged:l.onUuidChanged}),o._removeExpiredChunkingRecords&&o._removeExpiredChunkingRecords()},isDeferredEligibleForUpload:function(e){return l.isQueued(e)},maybeDefer:function(e,t){return t&&!o.getFile(e)&&t instanceof qq.BlobProxy?(l.onUploadPrep(e),i("Attempting to generate a blob on-demand for "+e),t.create().then(function(t){i("Generated an on-demand blob for "+e),o.updateBlob(e,t),l.setSize(e,t.size),o.reevaluateChunking(e),p.maybeSendDeferredFiles(e)},function(t){var o={};t&&(o.error=t),i(qq.format("Failed to generate blob for ID {}.  Error message: {}.",e,t),"error"),l.onComplete(e,l.getName(e),qq.extend(o,n),null),p.maybeSendDeferredFiles(e),c.free(e)}),!1):p.maybeSendDeferredFiles(e)},maybeSendDeferredFiles:function(e){var t=l.getIdsInProxyGroup(e),n=!1;return t&&t.length?(i("Maybe ready to upload proxy group file "+e),qq.each(t,function(t,i){if(p.isDeferredEligibleForUpload(i)&&o.getFile(i))n=i===e,p.now(i);else if(p.isDeferredEligibleForUpload(i))return!1})):(n=!0,p.now(e)),n},maybeNewUuid:function(e,t){void 0!==t.newUuid&&l.onUuidChanged(e,t.newUuid)},normalizeResponse:function(e,t){var n=e;return qq.isObject(e)||(n={},qq.isString(e)&&!t&&(n.error=e)),n.success=t,n},now:function(e){var t=l.getName(e);if(!r.isValid(e))throw new qq.Error(e+" is not a valid file ID to upload!");l.onUpload(e,t),a&&o._shouldChunkThisFile(e)?u.sendNext(e):d.send(e,t)},start:function(e){var t=p.getProxyOrBlob(e);return t?p.maybeDefer(e,t):(p.now(e),!0)}};qq.extend(this,{add:function(e,t){o.add.apply(this,arguments)},upload:function(e){return!!c.open(e)&&p.start(e)},retry:function(e){return s&&(o._getFileState(e).temp.ignoreFailure=!1),c.isUsingConnection(e)?p.start(e):r.upload(e)},cancel:function(e){var t=o.cancel(e);qq.isGenericPromise(t)?t.then(function(){p.cancel(e)}):t!==!1&&p.cancel(e)},cancelAll:function(){var e,t=c.getWaitingOrConnected();if(t.length)for(e=t.length-1;e>=0;e--)r.cancel(t[e]);c.reset()},getFile:function(e){return o.getProxy&&o.getProxy(e)?o.getProxy(e).referenceBlob:o.getFile&&o.getFile(e)},isProxied:function(e){return!(!o.getProxy||!o.getProxy(e))},getInput:function(e){if(o.getInput)return o.getInput(e)},reset:function(){i("Resetting upload handler"),r.cancelAll(),c.reset(),o.reset()},expunge:function(e){if(r.isValid(e))return o.expunge(e)},isValid:function(e){return o.isValid(e)},getResumableFilesData:function(){
	return o.getResumableFilesData?o.getResumableFilesData():[]},getThirdPartyFileId:function(e){if(r.isValid(e))return o.getThirdPartyFileId(e)},pause:function(e){return!!(r.isResumable(e)&&o.pause&&r.isValid(e)&&o.pause(e))&&(c.free(e),o.moveInProgressToRemaining(e),!0)},isResumable:function(e){return!!o.isResumable&&o.isResumable(e)}}),qq.extend(l,e),i=l.log,a=l.chunking.enabled&&qq.supportedFeatures.chunking,s=a&&l.chunking.concurrent.enabled,n=function(){var e={};return e[l.preventRetryParam]=!0,e}(),p.initHandler()},qq.FormUploadHandler=function(e){"use strict";function t(e){delete c[e],p&&(clearTimeout(d[e]),delete d[e],m.stopReceivingMessages(e));var t=document.getElementById(a._getIframeName(e));t&&(t.setAttribute("src","javascript:false;"),qq(t).remove())}function n(e){return e.split("_")[0]}function i(e){var t=qq.toElement("<iframe src='javascript:false;' name='"+e+"' />");return t.setAttribute("id",e),t.style.display="none",document.body.appendChild(t),t}function o(e,t){var i=e.id,o=n(i),r=q(o);u[r]=t,c[o]=qq(e).attach("load",function(){a.getInput(o)&&(f("Received iframe load event for CORS upload request (iframe name "+i+")"),d[i]=setTimeout(function(){var e="No valid message received from loaded iframe for iframe name "+i;f(e,"error"),t({error:e})},1e3))}),m.receiveMessage(i,function(e){f("Received the following window message: '"+e+"'");var t,o=(n(i),a._parseJsonResponse(e)),r=o.uuid;r&&u[r]?(f("Handling response for iframe name "+i),clearTimeout(d[i]),delete d[i],a._detachLoadEvent(i),t=u[r],delete u[r],m.stopReceivingMessages(i),t(o)):r||f("'"+e+"' does not contain a UUID - ignoring.")})}var r=e.options,a=this,s=e.proxy,l=qq.getUniqueId(),u={},c={},d={},p=r.isCors,h=r.inputName,q=s.getUuid,f=s.log,m=new qq.WindowReceiveMessage({log:f});qq.extend(this,new qq.UploadHandler(e)),qq.override(this,function(e){return{add:function(t,n){e.add(t,{input:n}),n.setAttribute("name",h),n.parentNode&&qq(n).remove()},expunge:function(n){t(n),e.expunge(n)},isValid:function(t){return e.isValid(t)&&void 0!==a._getFileState(t).input}}}),qq.extend(this,{getInput:function(e){return a._getFileState(e).input},_attachLoadEvent:function(e,t){var n;p?o(e,t):c[e.id]=qq(e).attach("load",function(){if(f("Received response for "+e.id),e.parentNode){try{if(e.contentDocument&&e.contentDocument.body&&"false"==e.contentDocument.body.innerHTML)return}catch(e){f("Error when attempting to access iframe during handling of upload response ("+e.message+")","error"),n={success:!1}}t(n)}})},_createIframe:function(e){var t=a._getIframeName(e);return i(t)},_detachLoadEvent:function(e){void 0!==c[e]&&(c[e](),delete c[e])},_getIframeName:function(e){return e+"_"+l},_initFormForUpload:function(e){var t=e.method,n=e.endpoint,i=e.params,o=e.paramsInBody,r=e.targetName,a=qq.toElement("<form method='"+t+"' enctype='multipart/form-data'></form>"),s=n;return o?qq.obj2Inputs(i,a):s=qq.obj2url(i,n),a.setAttribute("action",s),a.setAttribute("target",r),a.style.display="none",document.body.appendChild(a),a},_parseJsonResponse:function(e){var t={};try{t=qq.parseJson(e)}catch(e){f("Error when attempting to parse iframe upload response ("+e.message+")","error")}return t}})},qq.XhrUploadHandler=function(e){"use strict";function t(e){qq.each(n._getXhrs(e),function(t,i){var o=n._getAjaxRequester(e,t);i.onreadystatechange=null,i.upload.onprogress=null,i.abort(),o&&o.canceled&&o.canceled(e)})}var n=this,i=e.options.namespace,o=e.proxy,r=e.options.chunking,a=e.options.resume,s=r&&e.options.chunking.enabled&&qq.supportedFeatures.chunking,l=a&&e.options.resume.enabled&&s&&qq.supportedFeatures.resume,u=o.getName,c=o.getSize,d=o.getUuid,p=o.getEndpoint,h=o.getDataByUuid,q=o.onUuidChanged,f=o.onProgress,m=o.log;qq.extend(this,new qq.UploadHandler(e)),qq.override(this,function(e){return{add:function(t,i){if(qq.isFile(i)||qq.isBlob(i))e.add(t,{file:i});else{if(!(i instanceof qq.BlobProxy))throw new Error("Passed obj is not a File, Blob, or proxy");e.add(t,{proxy:i})}n._initTempState(t),l&&n._maybePrepareForResume(t)},expunge:function(i){t(i),n._maybeDeletePersistedChunkData(i),n._clearXhrs(i),e.expunge(i)}}}),qq.extend(this,{clearCachedChunk:function(e,t){delete n._getFileState(e).temp.cachedChunks[t]},clearXhr:function(e,t){var i=n._getFileState(e).temp;i.xhrs&&delete i.xhrs[t],i.ajaxRequesters&&delete i.ajaxRequesters[t]},finalizeChunks:function(e,t){var i=n._getTotalChunks(e)-1,o=n._getXhr(e,i);return t?(new qq.Promise).success(t(o),o):(new qq.Promise).success({},o)},getFile:function(e){return n.isValid(e)&&n._getFileState(e).file},getProxy:function(e){return n.isValid(e)&&n._getFileState(e).proxy},getResumableFilesData:function(){var e=[];return n._iterateResumeRecords(function(t,i){n.moveInProgressToRemaining(null,i.chunking.inProgress,i.chunking.remaining);var o={name:i.name,remaining:i.chunking.remaining,size:i.size,uuid:i.uuid};i.key&&(o.key=i.key),e.push(o)}),e},isResumable:function(e){return!!r&&n.isValid(e)&&!n._getFileState(e).notResumable},moveInProgressToRemaining:function(e,t,i){var o=t||n._getFileState(e).chunking.inProgress,r=i||n._getFileState(e).chunking.remaining;o&&(m(qq.format("Moving these chunks from in-progress {}, to remaining.",JSON.stringify(o))),o.reverse(),qq.each(o,function(e,t){r.unshift(t)}),o.length=0)},pause:function(e){if(n.isValid(e))return m(qq.format("Aborting XHR upload for {} '{}' due to pause instruction.",e,u(e))),n._getFileState(e).paused=!0,t(e),!0},reevaluateChunking:function(e){if(r&&n.isValid(e)){var t,i,o=n._getFileState(e);if(delete o.chunking,o.chunking={},t=n._getTotalChunks(e),t>1||r.mandatory){for(o.chunking.enabled=!0,o.chunking.parts=t,o.chunking.remaining=[],i=0;i<t;i++)o.chunking.remaining.push(i);n._initTempState(e)}else o.chunking.enabled=!1}},updateBlob:function(e,t){n.isValid(e)&&(n._getFileState(e).file=t)},_clearXhrs:function(e){var t=n._getFileState(e).temp;qq.each(t.ajaxRequesters,function(e){delete t.ajaxRequesters[e]}),qq.each(t.xhrs,function(e){delete t.xhrs[e]})},_createXhr:function(e,t){return n._registerXhr(e,t,qq.createXhrInstance())},_getAjaxRequester:function(e,t){var i=null==t?-1:t;return n._getFileState(e).temp.ajaxRequesters[i]},_getChunkData:function(e,t){var i=r.partSize,o=c(e),a=n.getFile(e),s=i*t,l=s+i>=o?o:s+i,u=n._getTotalChunks(e),d=this._getFileState(e).temp.cachedChunks,p=d[t]||qq.sliceBlob(a,s,l);return d[t]=p,{part:t,start:s,end:l,count:u,blob:p,size:l-s}},_getChunkDataForCallback:function(e){return{partIndex:e.part,startByte:e.start+1,endByte:e.end,totalParts:e.count}},_getLocalStorageId:function(e){var t="5.0",n=u(e),o=c(e),a=r.partSize,s=p(e);return qq.format("qq{}resume{}-{}-{}-{}-{}",i,t,n,o,a,s)},_getMimeType:function(e){return n.getFile(e).type},_getPersistableData:function(e){return n._getFileState(e).chunking},_getTotalChunks:function(e){if(r){var t=c(e),n=r.partSize;return Math.ceil(t/n)}},_getXhr:function(e,t){var i=null==t?-1:t;return n._getFileState(e).temp.xhrs[i]},_getXhrs:function(e){return n._getFileState(e).temp.xhrs},_iterateResumeRecords:function(e){l&&qq.each(localStorage,function(t,n){if(0===t.indexOf(qq.format("qq{}resume",i))){var o=JSON.parse(n);e(t,o)}})},_initTempState:function(e){n._getFileState(e).temp={ajaxRequesters:{},chunkProgress:{},xhrs:{},cachedChunks:{}}},_markNotResumable:function(e){n._getFileState(e).notResumable=!0},_maybeDeletePersistedChunkData:function(e){var t;return!!(l&&n.isResumable(e)&&(t=n._getLocalStorageId(e),t&&localStorage.getItem(t)))&&(localStorage.removeItem(t),!0)},_maybePrepareForResume:function(e){var t,i,o=n._getFileState(e);l&&void 0===o.key&&(t=n._getLocalStorageId(e),i=localStorage.getItem(t),i&&(i=JSON.parse(i),h(i.uuid)?n._markNotResumable(e):(m(qq.format("Identified file with ID {} and name of {} as resumable.",e,u(e))),q(e,i.uuid),o.key=i.key,o.chunking=i.chunking,o.loaded=i.loaded,o.attemptingResume=!0,n.moveInProgressToRemaining(e))))},_maybePersistChunkedState:function(e){var t,i,o=n._getFileState(e);if(l&&n.isResumable(e)){t=n._getLocalStorageId(e),i={name:u(e),size:c(e),uuid:d(e),key:o.key,chunking:o.chunking,loaded:o.loaded,lastUpdated:Date.now()};try{localStorage.setItem(t,JSON.stringify(i))}catch(t){m(qq.format("Unable to save resume data for '{}' due to error: '{}'.",e,t.toString()),"warn")}}},_registerProgressHandler:function(e,t,i){var o=n._getXhr(e,t),r=u(e),a={simple:function(t,n){var i=c(e);t===n?f(e,r,i,i):f(e,r,t>=i?i-1:t,i)},chunked:function(o,a){var s=n._getFileState(e).temp.chunkProgress,l=n._getFileState(e).loaded,u=o,d=a,p=c(e),h=u-(d-i),q=l;s[t]=h,qq.each(s,function(e,t){q+=t}),f(e,r,q,p)}};o.upload.onprogress=function(e){if(e.lengthComputable){var t=null==i?"simple":"chunked";a[t](e.loaded,e.total)}}},_registerXhr:function(e,t,i,o){var r=null==t?-1:t,a=n._getFileState(e).temp;return a.xhrs=a.xhrs||{},a.ajaxRequesters=a.ajaxRequesters||{},a.xhrs[r]=i,o&&(a.ajaxRequesters[r]=o),i},_removeExpiredChunkingRecords:function(){var e=a.recordsExpireIn;n._iterateResumeRecords(function(t,n){var i=new Date(n.lastUpdated);i.setDate(i.getDate()+e),i.getTime()<=Date.now()&&(m("Removing expired resume record with key "+t),localStorage.removeItem(t))})},_shouldChunkThisFile:function(e){var t=n._getFileState(e);return t.chunking||n.reevaluateChunking(e),t.chunking.enabled}})},qq.DeleteFileAjaxRequester=function(e){"use strict";function t(){return"POST"===i.method.toUpperCase()?{_method:"DELETE"}:{}}var n,i={method:"DELETE",uuidParamName:"qquuid",endpointStore:{},maxConnections:3,customHeaders:function(e){return{}},paramsStore:{},cors:{expected:!1,sendCredentials:!1},log:function(e,t){},onDelete:function(e){},onDeleteComplete:function(e,t,n){}};qq.extend(i,e),n=qq.extend(this,new qq.AjaxRequester({acceptHeader:"application/json",validMethods:["POST","DELETE"],method:i.method,endpointStore:i.endpointStore,paramsStore:i.paramsStore,mandatedParams:t(),maxConnections:i.maxConnections,customHeaders:function(e){return i.customHeaders.get(e)},log:i.log,onSend:i.onDelete,onComplete:i.onDeleteComplete,cors:i.cors})),qq.extend(this,{sendDelete:function(e,t,o){var r=o||{};i.log("Submitting delete file request for "+e),"DELETE"===i.method?n.initTransport(e).withPath(t).withParams(r).send():(r[i.uuidParamName]=t,n.initTransport(e).withParams(r).send())}})},function(){function e(e){var t,n=e.naturalWidth,i=e.naturalHeight,o=document.createElement("canvas");return n*i>1048576&&(o.width=o.height=1,t=o.getContext("2d"),t.drawImage(e,-n+1,0),0===t.getImageData(0,0,1,1).data[3])}function t(e,t,n){var i,o,r,a,s=document.createElement("canvas"),l=0,u=n,c=n;for(s.width=1,s.height=n,i=s.getContext("2d"),i.drawImage(e,0,0),o=i.getImageData(0,0,1,n).data;c>l;)r=o[4*(c-1)+3],0===r?u=c:l=c,c=u+l>>1;return a=c/n,0===a?1:a}function n(e,t,n,i){var r=document.createElement("canvas"),a=n.mime||"image/jpeg",s=new qq.Promise;return o(e,t,r,n,i).then(function(){s.success(r.toDataURL(a,n.quality||.8))}),s}function i(e){var t=5241e3;if(!qq.ios())throw new qq.Error("Downsampled dimensions can only be reliably calculated for iOS!");if(e.origHeight*e.origWidth>t)return{newHeight:Math.round(Math.sqrt(t*(e.origHeight/e.origWidth))),newWidth:Math.round(Math.sqrt(t*(e.origWidth/e.origHeight)))}}function o(n,o,s,l,u){var c,d=n.naturalWidth,p=n.naturalHeight,h=l.width,q=l.height,f=s.getContext("2d"),m=new qq.Promise;return f.save(),l.resize?r({blob:o,canvas:s,image:n,imageHeight:p,imageWidth:d,orientation:l.orientation,resize:l.resize,targetHeight:q,targetWidth:h}):(qq.supportedFeatures.unlimitedScaledImageSize||(c=i({origWidth:h,origHeight:q}),c&&(qq.log(qq.format("Had to reduce dimensions due to device limitations from {}w / {}h to {}w / {}h",h,q,c.newWidth,c.newHeight),"warn"),h=c.newWidth,q=c.newHeight)),a(s,h,q,l.orientation),qq.ios()?!function(){e(n)&&(d/=2,p/=2);var i,o,r,a=1024,s=document.createElement("canvas"),l=u?t(n,d,p):1,c=Math.ceil(a*h/d),m=Math.ceil(a*q/p/l),g=0,_=0;for(s.width=s.height=a,i=s.getContext("2d");g<p;){for(o=0,r=0;o<d;)i.clearRect(0,0,a,a),i.drawImage(n,-o,-g),f.drawImage(s,0,0,a,a,r,_,c,m),o+=a,r+=c;g+=a,_+=m}f.restore(),s=i=null}():f.drawImage(n,0,0,h,q),s.qqImageRendered&&s.qqImageRendered(),m.success(),m)}function r(e){var t=e.blob,n=e.image,i=e.imageHeight,o=e.imageWidth,r=e.orientation,s=new qq.Promise,l=e.resize,u=document.createElement("canvas"),c=u.getContext("2d"),d=e.canvas,p=e.targetHeight,h=e.targetWidth;return a(u,o,i,r),d.height=p,d.width=h,c.drawImage(n,0,0),l({blob:t,height:p,image:n,sourceCanvas:u,targetCanvas:d,width:h}).then(function(){d.qqImageRendered&&d.qqImageRendered(),s.success()},s.failure),s}function a(e,t,n,i){switch(i){case 5:case 6:case 7:case 8:e.width=n,e.height=t;break;default:e.width=t,e.height=n}var o=e.getContext("2d");switch(i){case 2:o.translate(t,0),o.scale(-1,1);break;case 3:o.translate(t,n),o.rotate(Math.PI);break;case 4:o.translate(0,n),o.scale(1,-1);break;case 5:o.rotate(.5*Math.PI),o.scale(1,-1);break;case 6:o.rotate(.5*Math.PI),o.translate(0,-n);break;case 7:o.rotate(.5*Math.PI),o.translate(t,-n),o.scale(-1,1);break;case 8:o.rotate(-.5*Math.PI),o.translate(-t,0)}}function s(e,t){var n=this;window.Blob&&e instanceof Blob&&!function(){var t=new Image,i=window.URL&&window.URL.createObjectURL?window.URL:window.webkitURL&&window.webkitURL.createObjectURL?window.webkitURL:null;if(!i)throw Error("No createObjectURL function found to create blob url");t.src=i.createObjectURL(e),n.blob=e,e=t}(),e.naturalWidth||e.naturalHeight||(e.onload=function(){var e=n.imageLoadListeners;e&&(n.imageLoadListeners=null,setTimeout(function(){for(var t=0,n=e.length;t<n;t++)e[t]()},0))},e.onerror=t,this.imageLoadListeners=[]),this.srcImage=e}s.prototype.render=function(e,t){t=t||{};var i,r=this,a=this.srcImage.naturalWidth,s=this.srcImage.naturalHeight,l=t.width,u=t.height,c=t.maxWidth,d=t.maxHeight,p=!this.blob||"image/jpeg"===this.blob.type,h=e.tagName.toLowerCase();return this.imageLoadListeners?void this.imageLoadListeners.push(function(){r.render(e,t)}):(l&&!u?u=s*l/a<<0:u&&!l?l=a*u/s<<0:(l=a,u=s),c&&l>c&&(l=c,u=s*l/a<<0),d&&u>d&&(u=d,l=a*u/s<<0),i={width:l,height:u},qq.each(t,function(e,t){i[e]=t}),"img"===h?!function(){var t=e.src;n(r.srcImage,r.blob,i,p).then(function(n){e.src=n,t===e.src&&e.onload()})}():"canvas"===h&&o(this.srcImage,this.blob,e,i,p),void("function"==typeof this.onrender&&this.onrender(e)))},qq.MegaPixImage=s}(),qq.ImageGenerator=function(e){"use strict";function t(e){return"img"===e.tagName.toLowerCase()}function n(e){return"canvas"===e.tagName.toLowerCase()}function i(){return void 0!==(new Image).crossOrigin}function o(){var e=document.createElement("canvas");return e.getContext&&e.getContext("2d")}function r(e){var t=e.split("/"),n=t[t.length-1].split("?")[0],i=qq.getExtension(n);switch(i=i&&i.toLowerCase()){case"jpeg":case"jpg":return"image/jpeg";case"png":return"image/png";case"bmp":return"image/bmp";case"gif":return"image/gif";case"tiff":case"tif":return"image/tiff"}}function a(e){var t,n,i,o=document.createElement("a");return o.href=e,t=o.protocol,i=o.port,n=o.hostname,t.toLowerCase()!==window.location.protocol.toLowerCase()||(n.toLowerCase()!==window.location.hostname.toLowerCase()||i!==window.location.port&&!qq.ie())}function s(t,n){t.onload=function(){t.onload=null,t.onerror=null,n.success(t)},t.onerror=function(){t.onload=null,t.onerror=null,e("Problem drawing thumbnail!","error"),n.failure(t,"Problem drawing thumbnail!")}}function l(e,t){e.qqImageRendered=function(){t.success(e)}}function u(i,o){var r=t(i)||n(i);return t(i)?s(i,o):n(i)?l(i,o):(o.failure(i),e(qq.format("Element container of type {} is not supported!",i.tagName),"error")),r}function c(t,n,i){var o=new qq.Promise,r=new qq.Identify(t,e),a=i.maxSize,s=null==i.orient||i.orient,l=function(){n.onerror=null,n.onload=null,e("Could not render preview, file may be too large!","error"),o.failure(n,"Browser cannot render image!")};return r.isPreviewable().then(function(r){var c={parse:function(){return(new qq.Promise).success()}},d=s?new qq.Exif(t,e):c,p=new qq.MegaPixImage(t,l);u(n,o)&&d.parse().then(function(e){var t=e&&e.Orientation;p.render(n,{maxWidth:a,maxHeight:a,orientation:t,mime:r,resize:i.customResizeFunction})},function(t){e(qq.format("EXIF data could not be parsed ({}).  Assuming orientation = 1.",t)),p.render(n,{maxWidth:a,maxHeight:a,mime:r,resize:i.customResizeFunction})})},function(){e("Not previewable"),o.failure(n,"Not previewable")}),o}function d(e,t,n,i,o){var s=new Image,l=new qq.Promise;u(s,l),a(e)&&(s.crossOrigin="anonymous"),s.src=e,l.then(function(){u(t,n);var a=new qq.MegaPixImage(s);a.render(t,{maxWidth:i,maxHeight:i,mime:r(e),resize:o})},n.failure)}function p(e,t,n,i){u(t,n),qq(t).css({maxWidth:i+"px",maxHeight:i+"px"}),t.src=e}function h(e,r,s){var l=new qq.Promise,c=s.scale,h=c?s.maxSize:null;return c&&t(r)?o()?a(e)&&!i()?p(e,r,l,h):d(e,r,l,h):p(e,r,l,h):n(r)?d(e,r,l,h):u(r,l)&&(r.src=e),l}qq.extend(this,{generate:function(t,n,i){return qq.isString(t)?(e("Attempting to update thumbnail based on server response."),h(t,n,i||{})):(e("Attempting to draw client-side image preview."),c(t,n,i||{}))}}),this._testing={},this._testing.isImg=t,this._testing.isCanvas=n,this._testing.isCrossOrigin=a,this._testing.determineMimeOfFileName=r},qq.Exif=function(e,t){"use strict";function n(e){for(var t=0,n=0;e.length>0;)t+=parseInt(e.substring(0,2),16)*Math.pow(2,n),e=e.substring(2,e.length),n+=8;return t}function i(t,n){var o=t,r=n;return void 0===o&&(o=2,r=new qq.Promise),qq.readBlobToHex(e,o,4).then(function(e){var t,n=/^ffe([0-9])/.exec(e);n?"1"!==n[1]?(t=parseInt(e.slice(4,8),16),i(o+t+2,r)):r.success(o):r.failure("No EXIF header to be found!")}),r}function o(){var t=new qq.Promise;return qq.readBlobToHex(e,0,6).then(function(e){0!==e.indexOf("ffd8")?t.failure("Not a valid JPEG!"):i().then(function(e){t.success(e)},function(e){t.failure(e)})}),t}function r(t){var n=new qq.Promise;return qq.readBlobToHex(e,t+10,2).then(function(e){n.success("4949"===e)}),n}function a(t,i){var o=new qq.Promise;return qq.readBlobToHex(e,t+18,2).then(function(e){return i?o.success(n(e)):void o.success(parseInt(e,16))}),o}function s(t,n){var i=t+20,o=12*n;return qq.readBlobToHex(e,i,o)}function l(e){for(var t=[],n=0;n+24<=e.length;)t.push(e.slice(n,n+24)),n+=24;return t}function u(e,t){var i=16,o=qq.extend([],c),r={};return qq.each(t,function(t,a){var s,l,u,c=a.slice(0,4),p=e?n(c):parseInt(c,16),h=o.indexOf(p);if(h>=0&&(l=d[p].name,u=d[p].bytes,s=a.slice(i,i+2*u),r[l]=e?n(s):parseInt(s,16),o.splice(h,1)),0===o.length)return!1}),r}var c=[274],d={274:{name:"Orientation",bytes:2}};qq.extend(this,{parse:function(){var n=new qq.Promise,i=function(e){t(qq.format("EXIF header parse failed: '{}' ",e)),n.failure(e)};return o().then(function(o){t(qq.format("Moving forward with EXIF header parsing for '{}'",void 0===e.name?"blob":e.name)),r(o).then(function(e){t(qq.format("EXIF Byte order is {} endian",e?"little":"big")),a(o,e).then(function(r){t(qq.format("Found {} APP1 directory entries",r)),s(o,r).then(function(i){var o=l(i),r=u(e,o);t("Successfully parsed some EXIF tags"),n.success(r)},i)},i)},i)},i),n}}),this._testing={},this._testing.parseLittleEndian=n},qq.Identify=function(e,t){"use strict";function n(e,t){var n=!1,i=[].concat(e);return qq.each(i,function(e,i){if(0===t.indexOf(i))return n=!0,!1}),n}qq.extend(this,{isPreviewable:function(){var i=this,o=new qq.Promise,r=!1,a=void 0===e.name?"blob":e.name;return t(qq.format("Attempting to determine if {} can be rendered in this browser",a)),t("First pass: check type attribute of blob object."),this.isPreviewableSync()?(t("Second pass: check for magic bytes in file header."),qq.readBlobToHex(e,0,4).then(function(e){qq.each(i.PREVIEWABLE_MIME_TYPES,function(t,i){if(n(i,e))return("image/tiff"!==t||qq.supportedFeatures.tiffPreviews)&&(r=!0,o.success(t)),!1}),t(qq.format("'{}' is {} able to be rendered in this browser",a,r?"":"NOT")),r||o.failure()},function(){t("Error reading file w/ name '"+a+"'.  Not able to be rendered in this browser."),o.failure()})):o.failure(),o},isPreviewableSync:function(){var n=e.type,i=qq.indexOf(Object.keys(this.PREVIEWABLE_MIME_TYPES),n)>=0,o=!1,r=void 0===e.name?"blob":e.name;return i&&(o="image/tiff"!==n||qq.supportedFeatures.tiffPreviews),!o&&t(r+" is not previewable in this browser per the blob's type attr"),o}})},qq.Identify.prototype.PREVIEWABLE_MIME_TYPES={"image/jpeg":"ffd8ff","image/gif":"474946","image/png":"89504e","image/bmp":"424d","image/tiff":["49492a00","4d4d002a"]},qq.Identify=function(e,t){"use strict";function n(e,t){var n=!1,i=[].concat(e);return qq.each(i,function(e,i){if(0===t.indexOf(i))return n=!0,!1}),n}qq.extend(this,{isPreviewable:function(){var i=this,o=new qq.Promise,r=!1,a=void 0===e.name?"blob":e.name;return t(qq.format("Attempting to determine if {} can be rendered in this browser",a)),t("First pass: check type attribute of blob object."),this.isPreviewableSync()?(t("Second pass: check for magic bytes in file header."),qq.readBlobToHex(e,0,4).then(function(e){qq.each(i.PREVIEWABLE_MIME_TYPES,function(t,i){if(n(i,e))return("image/tiff"!==t||qq.supportedFeatures.tiffPreviews)&&(r=!0,o.success(t)),!1}),t(qq.format("'{}' is {} able to be rendered in this browser",a,r?"":"NOT")),r||o.failure()},function(){t("Error reading file w/ name '"+a+"'.  Not able to be rendered in this browser."),o.failure()})):o.failure(),o},isPreviewableSync:function(){var n=e.type,i=qq.indexOf(Object.keys(this.PREVIEWABLE_MIME_TYPES),n)>=0,o=!1,r=void 0===e.name?"blob":e.name;return i&&(o="image/tiff"!==n||qq.supportedFeatures.tiffPreviews),!o&&t(r+" is not previewable in this browser per the blob's type attr"),o}})},qq.Identify.prototype.PREVIEWABLE_MIME_TYPES={"image/jpeg":"ffd8ff","image/gif":"474946","image/png":"89504e","image/bmp":"424d","image/tiff":["49492a00","4d4d002a"]},qq.ImageValidation=function(e,t){"use strict";function n(e){var t=!1;return qq.each(e,function(e,n){if(n>0)return t=!0,!1}),t}function i(){var n=new qq.Promise;return new qq.Identify(e,t).isPreviewable().then(function(){var i=new Image,o=window.URL&&window.URL.createObjectURL?window.URL:window.webkitURL&&window.webkitURL.createObjectURL?window.webkitURL:null;o?(i.onerror=function(){t("Cannot determine dimensions for image.  May be too large.","error"),n.failure()},i.onload=function(){n.success({width:this.width,height:this.height})},i.src=o.createObjectURL(e)):(t("No createObjectURL function available to generate image URL!","error"),n.failure())},n.failure),n}function o(e,t){var n;return qq.each(e,function(e,i){if(i>0){var o=/(max|min)(Width|Height)/.exec(e),r=o[2].charAt(0).toLowerCase()+o[2].slice(1),a=t[r];switch(o[1]){case"min":if(a<i)return n=e,!1;break;case"max":if(a>i)return n=e,!1}}}),n}this.validate=function(e){var r=new qq.Promise;return t("Attempting to validate image."),n(e)?i().then(function(t){var n=o(e,t);n?r.failure(n):r.success()},r.success):r.success(),r}},qq.Session=function(e){"use strict";function t(e){return!!qq.isArray(e)||void i.log("Session response is not an array.","error")}function n(e,n,o,r){var a=!1;n=n&&t(e),n&&qq.each(e,function(e,t){if(null==t.uuid)a=!0,i.log(qq.format("Session response item {} did not include a valid UUID - ignoring.",e),"error");else if(null==t.name)a=!0,i.log(qq.format("Session response item {} did not include a valid name - ignoring.",e),"error");else try{return i.addFileRecord(t),!0}catch(e){a=!0,i.log(e.message,"error")}return!1}),r[n&&!a?"success":"failure"](e,o)}var i={endpoint:null,params:{},customHeaders:{},cors:{},addFileRecord:function(e){},log:function(e,t){}};qq.extend(i,e,!0),this.refresh=function(){var e=new qq.Promise,t=function(t,i,o){n(t,i,o,e)},o=qq.extend({},i),r=new qq.SessionAjaxRequester(qq.extend(o,{onComplete:t}));return r.queryServer(),e}},qq.SessionAjaxRequester=function(e){"use strict";function t(e,t,n){var o=null;if(null!=t.responseText)try{o=qq.parseJson(t.responseText)}catch(e){i.log("Problem parsing session response: "+e.message,"error"),n=!0}i.onComplete(o,!n,t)}var n,i={endpoint:null,customHeaders:{},params:{},cors:{expected:!1,sendCredentials:!1},onComplete:function(e,t,n){},log:function(e,t){}};qq.extend(i,e),n=qq.extend(this,new qq.AjaxRequester({acceptHeader:"application/json",validMethods:["GET"],method:"GET",endpointStore:{get:function(){return i.endpoint}},customHeaders:i.customHeaders,log:i.log,onComplete:t,cors:i.cors})),qq.extend(this,{queryServer:function(){var e=qq.extend({},i.params);i.log("Session query request."),n.initTransport("sessionRefresh").withParams(e).withCacheBuster().send()}})},qq.Scaler=function(e,t){"use strict";var n=e.customResizer,i=e.sendOriginal,o=e.orient,r=e.defaultType,a=e.defaultQuality/100,s=e.failureText,l=e.includeExif,u=this._getSortedSizes(e.sizes);qq.extend(this,{enabled:qq.supportedFeatures.scaling&&u.length>0,getFileRecords:function(e,c,d){var p=this,h=[],q=d.blob?d.blob:d,f=new qq.Identify(q,t);return f.isPreviewableSync()?(qq.each(u,function(e,i){var u=p._determineOutputType({defaultType:r,requestedType:i.type,refType:q.type});h.push({uuid:qq.getUniqueId(),name:p._getName(c,{name:i.name,type:u,refType:q.type}),blob:new qq.BlobProxy(q,qq.bind(p._generateScaledImage,p,{customResizeFunction:n,maxSize:i.maxSize,orient:o,type:u,quality:a,failedText:s,includeExif:l,log:t}))})}),h.push({uuid:e,name:c,size:q.size,blob:i?q:null})):h.push({uuid:e,name:c,size:q.size,blob:q}),h},handleNewFile:function(e,t,n,i,o,r,a,s){var l=this,u=(e.qqButtonId||e.blob&&e.blob.qqButtonId,[]),c=null,d=s.addFileToHandler,p=s.uploadData,h=s.paramsStore,q=qq.getUniqueId();qq.each(l.getFileRecords(n,t,e),function(e,t){var n,i=t.size;t.blob instanceof qq.BlobProxy&&(i=-1),n=p.addFile({uuid:t.uuid,name:t.name,size:i,batchId:r,proxyGroupId:q}),t.blob instanceof qq.BlobProxy?u.push(n):c=n,t.blob?(d(n,t.blob),o.push({id:n,file:t.blob})):p.setStatus(n,qq.status.REJECTED)}),null!==c&&(qq.each(u,function(e,t){var n={qqparentuuid:p.retrieve({id:c}).uuid,qqparentsize:p.retrieve({id:c}).size};n[a]=p.retrieve({id:t}).uuid,p.setParentId(t,c),h.addReadOnly(t,n)}),u.length&&!function(){var e={};e[a]=p.retrieve({id:c}).uuid,h.addReadOnly(c,e)}())}})},qq.extend(qq.Scaler.prototype,{scaleImage:function(e,t,n){"use strict";if(!qq.supportedFeatures.scaling)throw new qq.Error("Scaling is not supported in this browser!");var i=new qq.Promise,o=n.log,r=n.getFile(e),a=n.uploadData.retrieve({id:e}),s=a&&a.name,l=a&&a.uuid,u={customResizer:t.customResizer,sendOriginal:!1,orient:t.orient,defaultType:t.type||null,defaultQuality:t.quality,failedToScaleText:"Unable to scale",sizes:[{name:"",maxSize:t.maxSize}]},c=new qq.Scaler(u,o);return qq.Scaler&&qq.supportedFeatures.imagePreviews&&r?qq.bind(function(){var t=c.getFileRecords(l,s,r)[0];t&&t.blob instanceof qq.BlobProxy?t.blob.create().then(i.success,i.failure):(o(e+" is not a scalable image!","error"),i.failure())},this)():(i.failure(),o("Could not generate requested scaled image for "+e+".  Scaling is either not possible in this browser, or the file could not be located.","error")),i},_determineOutputType:function(e){"use strict";var t=e.requestedType,n=e.defaultType,i=e.refType;return n||t?t&&qq.indexOf(Object.keys(qq.Identify.prototype.PREVIEWABLE_MIME_TYPES),t)>=0?"image/tiff"===t?qq.supportedFeatures.tiffPreviews?t:n:t:n:"image/jpeg"!==i?"image/png":i},_getName:function(e,t){"use strict";var n=e.lastIndexOf("."),i=t.type||"image/png",o=t.refType,r="",a=qq.getExtension(e),s="";return t.name&&t.name.trim().length&&(s=" ("+t.name+")"),n>=0?(r=e.substr(0,n),o!==i&&(a=i.split("/")[1]),r+=s+"."+a):r=e+s,r},_getSortedSizes:function(e){"use strict";return e=qq.extend([],e),e.sort(function(e,t){return e.maxSize>t.maxSize?1:e.maxSize<t.maxSize?-1:0})},_generateScaledImage:function(e,t){"use strict";var n=this,i=e.customResizeFunction,o=e.log,r=e.maxSize,a=e.orient,s=e.type,l=e.quality,u=e.failedText,c=e.includeExif&&"image/jpeg"===t.type&&"image/jpeg"===s,d=new qq.Promise,p=new qq.ImageGenerator(o),h=document.createElement("canvas");return o("Attempting to generate scaled version for "+t.name),p.generate(t,h,{maxSize:r,orient:a,customResizeFunction:i}).then(function(){var e=h.toDataURL(s,l),i=function(){o("Success generating scaled version for "+t.name);var n=qq.dataUriToBlob(e);d.success(n)};c?n._insertExifHeader(t,e,o).then(function(t){e=t,i()},function(){o("Problem inserting EXIF header into scaled image.  Using scaled image w/out EXIF data.","error"),i()}):i()},function(){o("Failed attempt to generate scaled version for "+t.name,"error"),d.failure(u)}),d},_insertExifHeader:function(e,t,n){"use strict";var i=new FileReader,o=new qq.Promise,r="";return i.onload=function(){r=i.result,o.success(qq.ExifRestorer.restore(r,t))},i.onerror=function(){n("Problem reading "+e.name+" during attempt to transfer EXIF data to scaled version.","error"),o.failure()},i.readAsDataURL(e),o},_dataUriToBlob:function(e){"use strict";var t,n,i,o;return t=e.split(",")[0].indexOf("base64")>=0?atob(e.split(",")[1]):decodeURI(e.split(",")[1]),n=e.split(",")[0].split(":")[1].split(";")[0],i=new ArrayBuffer(t.length),o=new Uint8Array(i),qq.each(t,function(e,t){o[e]=t.charCodeAt(0)}),this._createBlob(i,n)},_createBlob:function(e,t){"use strict";var n=window.BlobBuilder||window.WebKitBlobBuilder||window.MozBlobBuilder||window.MSBlobBuilder,i=n&&new n;return i?(i.append(e),i.getBlob(t)):new Blob([e],{type:t})}}),qq.ExifRestorer=function(){var e={};return e.KEY_STR="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",e.encode64=function(e){var t,n,i,o,r,a="",s="",l="",u=0;do t=e[u++],n=e[u++],s=e[u++],i=t>>2,o=(3&t)<<4|n>>4,r=(15&n)<<2|s>>6,l=63&s,isNaN(n)?r=l=64:isNaN(s)&&(l=64),a=a+this.KEY_STR.charAt(i)+this.KEY_STR.charAt(o)+this.KEY_STR.charAt(r)+this.KEY_STR.charAt(l),t=n=s="",i=o=r=l="";while(u<e.length);return a},e.restore=function(e,t){var n="data:image/jpeg;base64,";if(!e.match(n))return t;var i=this.decode64(e.replace(n,"")),o=this.slice2Segments(i),r=this.exifManipulation(t,o);return n+this.encode64(r)},e.exifManipulation=function(e,t){var n=this.getExifArray(t),i=this.insertExif(e,n),o=new Uint8Array(i);return o},e.getExifArray=function(e){for(var t,n=0;n<e.length;n++)if(t=e[n],255==t[0]&225==t[1])return t;return[]},e.insertExif=function(e,t){var n=e.replace("data:image/jpeg;base64,",""),i=this.decode64(n),o=i.indexOf(255,3),r=i.slice(0,o),a=i.slice(o),s=r;return s=s.concat(t),s=s.concat(a)},e.slice2Segments=function(e){for(var t=0,n=[];;){if(255==e[t]&218==e[t+1])break;if(255==e[t]&216==e[t+1])t+=2;else{var i=256*e[t+2]+e[t+3],o=t+i+2,r=e.slice(t,o);n.push(r),t=o}if(t>e.length)break}return n},e.decode64=function(e){var t,n,i,o,r,a="",s="",l=0,u=[],c=/[^A-Za-z0-9\+\/\=]/g;if(c.exec(e))throw new Error("There were invalid base64 characters in the input text.  Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='");e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");do i=this.KEY_STR.indexOf(e.charAt(l++)),o=this.KEY_STR.indexOf(e.charAt(l++)),r=this.KEY_STR.indexOf(e.charAt(l++)),s=this.KEY_STR.indexOf(e.charAt(l++)),t=i<<2|o>>4,n=(15&o)<<4|r>>2,a=(3&r)<<6|s,u.push(t),64!=r&&u.push(n),64!=s&&u.push(a),t=n=a="",i=o=r=s="";while(l<e.length);return u},e}(),qq.TotalProgress=function(e,t){"use strict";var n={},i=0,o=0,r=-1,a=-1,s=function(t,n){t===r&&n===a||e(t,n),r=t,a=n},l=function(e,t){var n=!0;return qq.each(e,function(e,i){if(qq.indexOf(t,i)>=0)return n=!1,!1}),n},u=function(e){p(e,-1,-1),delete n[e]},c=function(e,t,n){(0===t.length||l(t,n))&&(s(o,o),this.reset())},d=function(e){var i=t(e);i>0&&(p(e,0,i),n[e]={loaded:0,total:i})},p=function(e,t,r){var a=n[e]?n[e].loaded:0,l=n[e]?n[e].total:0;t===-1&&r===-1?(i-=a,o-=l):(t&&(i+=t-a),r&&(o+=r-l)),s(i,o)};qq.extend(this,{onAllComplete:c,onStatusChange:function(e,t,n){n===qq.status.CANCELED||n===qq.status.REJECTED?u(e):n===qq.status.SUBMITTING&&d(e)},onIndividualProgress:function(e,t,i){p(e,t,i),n[e]={loaded:t,total:i}},onNewSize:function(e){d(e)},reset:function(){n={},i=0,o=0}})},qq.PasteSupport=function(e){"use strict";function t(e){return e.type&&0===e.type.indexOf("image/")}function n(){r=qq(o.targetElement).attach("paste",function(e){
	var n=e.clipboardData;n&&qq.each(n.items,function(e,n){if(t(n)){var i=n.getAsFile();o.callbacks.pasteReceived(i)}})})}function i(){r&&r()}var o,r;o={targetElement:null,callbacks:{log:function(e,t){},pasteReceived:function(e){}}},qq.extend(o,e),n(),qq.extend(this,{reset:function(){i()}})},qq.FormSupport=function(e,t,n){"use strict";function i(e){e.getAttribute("action")&&(s.newEndpoint=e.getAttribute("action"))}function o(e,t){return!(e.checkValidity&&!e.checkValidity())||(n("Form did not pass validation checks - will not upload.","error"),void t())}function r(e){var n=e.submit;qq(e).attach("submit",function(i){i=i||window.event,i.preventDefault?i.preventDefault():i.returnValue=!1,o(e,n)&&t()}),e.submit=function(){o(e,n)&&t()}}function a(e){return e&&(qq.isString(e)&&(e=document.getElementById(e)),e&&(n("Attaching to form element."),i(e),l&&r(e))),e}var s=this,l=e.interceptSubmit,u=e.element,c=e.autoUpload;qq.extend(this,{newEndpoint:null,newAutoUpload:c,attachedToForm:!1,getFormInputsAsObject:function(){return null==u?null:s._form2Obj(u)}}),u=a(u),this.attachedToForm=!!u},qq.extend(qq.FormSupport.prototype,{_form2Obj:function(e){"use strict";var t={},n=function(e){var t=["button","image","reset","submit"];return qq.indexOf(t,e.toLowerCase())<0},i=function(e){return qq.indexOf(["checkbox","radio"],e.toLowerCase())>=0},o=function(e){return!(!i(e.type)||e.checked)||e.disabled&&"hidden"!==e.type.toLowerCase()},r=function(e){var t=null;return qq.each(qq(e).children(),function(e,n){if("option"===n.tagName.toLowerCase()&&n.selected)return t=n.value,!1}),t};return qq.each(e.elements,function(e,i){if(!qq.isInput(i,!0)&&"textarea"!==i.tagName.toLowerCase()||!n(i.type)||o(i)){if("select"===i.tagName.toLowerCase()&&!o(i)){var a=r(i);null!==a&&(t[i.name]=a)}}else t[i.name]=i.value}),t}}),qq.traditional=qq.traditional||{},qq.traditional.FormUploadHandler=function(e,t){"use strict";function n(e,t){var n,i,r;try{i=t.contentDocument||t.contentWindow.document,r=i.body.innerHTML,s("converting iframe's innerHTML to JSON"),s("innerHTML = "+r),r&&r.match(/^<pre/i)&&(r=i.body.firstChild.firstChild.nodeValue),n=o._parseJsonResponse(r)}catch(e){s("Error when attempting to parse form upload response ("+e.message+")","error"),n={success:!1}}return n}function i(t,n){var i=e.paramsStore.get(t),s="get"===e.method.toLowerCase()?"GET":"POST",l=e.endpointStore.get(t),u=r(t);return i[e.uuidName]=a(t),i[e.filenameParam]=u,o._initFormForUpload({method:s,endpoint:l,params:i,paramsInBody:e.paramsInBody,targetName:n.name})}var o=this,r=t.getName,a=t.getUuid,s=t.log;this.uploadFile=function(t){var r,a=o.getInput(t),l=o._createIframe(t),u=new qq.Promise;return r=i(t,l),r.appendChild(a),o._attachLoadEvent(l,function(i){s("iframe loaded");var r=i?i:n(t,l);o._detachLoadEvent(t),e.cors.expected||qq(l).remove(),r.success?u.success(r):u.failure(r)}),s("Sending upload request for "+t),r.submit(),qq(r).remove(),u},qq.extend(this,new qq.FormUploadHandler({options:{isCors:e.cors.expected,inputName:e.inputName},proxy:{onCancel:e.onCancel,getName:r,getUuid:a,log:s}}))},qq.traditional=qq.traditional||{},qq.traditional.XhrUploadHandler=function(e,t){"use strict";var n=this,i=t.getName,o=t.getSize,r=t.getUuid,a=t.log,s=e.forceMultipart||e.paramsInBody,l=function(t,n,r){var a=o(t),l=i(t);n[e.chunking.paramNames.partIndex]=r.part,n[e.chunking.paramNames.partByteOffset]=r.start,n[e.chunking.paramNames.chunkSize]=r.size,n[e.chunking.paramNames.totalParts]=r.count,n[e.totalFileSizeName]=a,s&&(n[e.filenameParam]=l)},u=new qq.traditional.AllChunksDoneAjaxRequester({cors:e.cors,endpoint:e.chunking.success.endpoint,log:a}),c=function(e,t){var n=new qq.Promise;return t.onreadystatechange=function(){if(4===t.readyState){var i=h(e,t);i.success?n.success(i.response,t):n.failure(i.response,t)}},n},d=function(t){var a=e.paramsStore.get(t),s=i(t),l=o(t);return a[e.uuidName]=r(t),a[e.filenameParam]=s,a[e.totalFileSizeName]=l,a[e.chunking.paramNames.totalParts]=n._getTotalChunks(t),a},p=function(e,t){return qq.indexOf([200,201,202,203,204],e.status)<0||!t.success||t.reset},h=function(e,t){var n;return a("xhr - server response received for "+e),a("responseText = "+t.responseText),n=q(!0,t),{success:!p(t,n),response:n}},q=function(e,t){var n={};try{a(qq.format("Received response status {} with body: {}",t.status,t.responseText)),n=qq.parseJson(t.responseText)}catch(t){e&&a("Error when attempting to parse xhr response text ("+t.message+")","error")}return n},f=function(t){var i=new qq.Promise;return u.complete(t,n._createXhr(t),d(t),e.customHeaders.get(t)).then(function(e){i.success(q(!1,e),e)},function(e){i.failure(q(!1,e),e)}),i},m=function(t,n,a,l){var u=new FormData,c=e.method,d=e.endpointStore.get(l),p=i(l),h=o(l);return t[e.uuidName]=r(l),t[e.filenameParam]=p,s&&(t[e.totalFileSizeName]=h),e.paramsInBody||(s||(t[e.inputName]=p),d=qq.obj2url(t,d)),n.open(c,d,!0),e.cors.expected&&e.cors.sendCredentials&&(n.withCredentials=!0),s?(e.paramsInBody&&qq.obj2FormData(t,u),u.append(e.inputName,a),u):a},g=function(t,i){var o=e.customHeaders.get(t),r=n.getFile(t);i.setRequestHeader("Accept","application/json"),i.setRequestHeader("X-Requested-With","XMLHttpRequest"),i.setRequestHeader("Cache-Control","no-cache"),s||(i.setRequestHeader("Content-Type","application/octet-stream"),i.setRequestHeader("X-Mime-Type",r.type)),qq.each(o,function(e,t){i.setRequestHeader(e,t)})};qq.extend(this,{uploadChunk:function(t,i,r){var a,s,u,d=n._getChunkData(t,i),p=n._createXhr(t,i);o(t);return a=c(t,p),n._registerProgressHandler(t,i,d.size),u=e.paramsStore.get(t),l(t,u,d),r&&(u[e.resume.paramNames.resuming]=!0),s=m(u,p,d.blob,t),g(t,p),p.send(s),a},uploadFile:function(t){var i,o,r,a,s=n.getFile(t);return o=n._createXhr(t),n._registerProgressHandler(t),i=c(t,o),r=e.paramsStore.get(t),a=m(r,o,s,t),g(t,o),o.send(a),i}}),qq.extend(this,new qq.XhrUploadHandler({options:qq.extend({namespace:"traditional"},e),proxy:qq.extend({getEndpoint:e.endpointStore.get},t)})),qq.override(this,function(t){return{finalizeChunks:function(n){return e.chunking.success.endpoint?f(n):t.finalizeChunks(n,qq.bind(q,this,!0))}}})},qq.traditional.AllChunksDoneAjaxRequester=function(e){"use strict";var t,n="POST",i={cors:{allowXdr:!1,expected:!1,sendCredentials:!1},endpoint:null,log:function(e,t){}},o={},r={get:function(e){return i.endpoint}};qq.extend(i,e),t=qq.extend(this,new qq.AjaxRequester({acceptHeader:"application/json",validMethods:[n],method:n,endpointStore:r,allowXRequestedWithAndCacheControl:!1,cors:i.cors,log:i.log,onComplete:function(e,t,n){var i=o[e];delete o[e],n?i.failure(t):i.success(t)}})),qq.extend(this,{complete:function(e,n,r,a){var s=new qq.Promise;return i.log("Submitting All Chunks Done request for "+e),o[e]=s,t.initTransport(e).withParams(r).withHeaders(a).send(n),s}})},qq.DragAndDrop=function(e){"use strict";function t(e,t){var n=Array.prototype.slice.call(e);u.callbacks.dropLog("Grabbed "+e.length+" dropped files."),t.dropDisabled(!1),u.callbacks.processingDroppedFilesComplete(n,t.getElement())}function n(e){var t=new qq.Promise;return e.isFile?e.file(function(n){var i=e.name,o=e.fullPath,r=o.indexOf(i);o=o.substr(0,r),"/"===o.charAt(0)&&(o=o.substr(1)),n.qqPath=o,h.push(n),t.success()},function(n){u.callbacks.dropLog("Problem parsing '"+e.fullPath+"'.  FileError code "+n.code+".","error"),t.failure()}):e.isDirectory&&i(e).then(function(e){var i=e.length;qq.each(e,function(e,o){n(o).done(function(){i-=1,0===i&&t.success()})}),e.length||t.success()},function(n){u.callbacks.dropLog("Problem parsing '"+e.fullPath+"'.  FileError code "+n.code+".","error"),t.failure()}),t}function i(e,t,n,o){var r=o||new qq.Promise,a=t||e.createReader();return a.readEntries(function(t){var o=n?n.concat(t):t;t.length?setTimeout(function(){i(e,a,o,r)},0):r.success(o)},r.failure),r}function o(e,t){var i=[],o=new qq.Promise;return u.callbacks.processingDroppedFiles(),t.dropDisabled(!0),e.files.length>1&&!u.allowMultipleItems?(u.callbacks.processingDroppedFilesComplete([]),u.callbacks.dropError("tooManyFilesError",""),t.dropDisabled(!1),o.failure()):(h=[],qq.isFolderDropSupported(e)?qq.each(e.items,function(e,t){var r=t.webkitGetAsEntry();r&&(r.isFile?h.push(t.getAsFile()):i.push(n(r).done(function(){i.pop(),0===i.length&&o.success()})))}):h=e.files,0===i.length&&o.success()),o}function r(e){var n=new qq.UploadDropZone({HIDE_ZONES_EVENT_NAME:c,element:e,onEnter:function(t){qq(e).addClass(u.classes.dropActive),t.stopPropagation()},onLeaveNotDescendants:function(t){qq(e).removeClass(u.classes.dropActive)},onDrop:function(e){o(e.dataTransfer,n).then(function(){t(h,n)},function(){u.callbacks.dropLog("Drop event DataTransfer parsing failed.  No files will be uploaded.","error")})}});return q.addDisposer(function(){n.dispose()}),qq(e).hasAttribute(d)&&qq(e).hide(),p.push(n),n}function a(e){var t;return qq.each(e.dataTransfer.types,function(e,n){if("Files"===n)return t=!0,!1}),t}function s(e){return qq.firefox()?!e.relatedTarget:qq.safari()?e.x<0||e.y<0:0===e.x&&0===e.y}function l(){var e=u.dropZoneElements,t=function(){setTimeout(function(){qq.each(e,function(e,t){qq(t).hasAttribute(d)&&qq(t).hide(),qq(t).removeClass(u.classes.dropActive)})},10)};qq.each(e,function(t,n){var i=r(n);e.length&&qq.supportedFeatures.fileDrop&&q.attach(document,"dragenter",function(t){!i.dropDisabled()&&a(t)&&qq.each(e,function(e,t){t instanceof HTMLElement&&qq(t).hasAttribute(d)&&qq(t).css({display:"block"})})})}),q.attach(document,"dragleave",function(e){s(e)&&t()}),q.attach(qq(document).children()[0],"mouseenter",function(e){t()}),q.attach(document,"drop",function(e){e.preventDefault(),t()}),q.attach(document,c,t)}var u,c="qq-hidezones",d="qq-hide-dropzone",p=[],h=[],q=new qq.DisposeSupport;u={dropZoneElements:[],allowMultipleItems:!0,classes:{dropActive:null},callbacks:new qq.DragAndDrop.callbacks},qq.extend(u,e,!0),l(),qq.extend(this,{setupExtraDropzone:function(e){u.dropZoneElements.push(e),r(e)},removeDropzone:function(e){var t,n=u.dropZoneElements;for(t in n)if(n[t]===e)return n.splice(t,1)},dispose:function(){q.dispose(),qq.each(p,function(e,t){t.dispose()})}})},qq.DragAndDrop.callbacks=function(){"use strict";return{processingDroppedFiles:function(){},processingDroppedFilesComplete:function(e,t){},dropError:function(e,t){qq.log("Drag & drop error code '"+e+" with these specifics: '"+t+"'","error")},dropLog:function(e,t){qq.log(e,t)}}},qq.UploadDropZone=function(e){"use strict";function t(){return qq.safari()||qq.firefox()&&qq.windows()}function n(e){c||(t?d.attach(document,"dragover",function(e){e.preventDefault()}):d.attach(document,"dragover",function(e){e.dataTransfer&&(e.dataTransfer.dropEffect="none",e.preventDefault())}),c=!0)}function i(e){if(!qq.supportedFeatures.fileDrop)return!1;var t,n=e.dataTransfer,i=qq.safari();return t=!(!qq.ie()||!qq.supportedFeatures.fileDrop)||"none"!==n.effectAllowed,n&&t&&(n.files||!i&&n.types.contains&&n.types.contains("Files"))}function o(e){return void 0!==e&&(u=e),u}function r(){function e(){t=document.createEvent("Event"),t.initEvent(s.HIDE_ZONES_EVENT_NAME,!0,!0)}var t;if(window.CustomEvent)try{t=new CustomEvent(s.HIDE_ZONES_EVENT_NAME)}catch(t){e()}else e();document.dispatchEvent(t)}function a(){d.attach(l,"dragover",function(e){if(i(e)){var t=qq.ie()&&qq.supportedFeatures.fileDrop?null:e.dataTransfer.effectAllowed;"move"===t||"linkMove"===t?e.dataTransfer.dropEffect="move":e.dataTransfer.dropEffect="copy",e.stopPropagation(),e.preventDefault()}}),d.attach(l,"dragenter",function(e){if(!o()){if(!i(e))return;s.onEnter(e)}}),d.attach(l,"dragleave",function(e){if(i(e)){s.onLeave(e);var t=document.elementFromPoint(e.clientX,e.clientY);qq(this).contains(t)||s.onLeaveNotDescendants(e)}}),d.attach(l,"drop",function(e){if(!o()){if(!i(e))return;e.preventDefault(),e.stopPropagation(),s.onDrop(e),r()}})}var s,l,u,c,d=new qq.DisposeSupport;s={element:null,onEnter:function(e){},onLeave:function(e){},onLeaveNotDescendants:function(e){},onDrop:function(e){}},qq.extend(s,e),l=s.element,n(),a(),qq.extend(this,{dropDisabled:function(e){return o(e)},dispose:function(){d.dispose()},getElement:function(){return l}})},function(){"use strict";qq.uiPublicApi={addInitialFiles:function(e){this._parent.prototype.addInitialFiles.apply(this,arguments),this._templating.addCacheToDom()},clearStoredFiles:function(){this._parent.prototype.clearStoredFiles.apply(this,arguments),this._templating.clearFiles()},addExtraDropzone:function(e){this._dnd&&this._dnd.setupExtraDropzone(e)},removeExtraDropzone:function(e){if(this._dnd)return this._dnd.removeDropzone(e)},getItemByFileId:function(e){if(!this._templating.isHiddenForever(e))return this._templating.getFileContainer(e)},reset:function(){this._parent.prototype.reset.apply(this,arguments),this._templating.reset(),!this._options.button&&this._templating.getButton()&&(this._defaultButtonId=this._createUploadButton({element:this._templating.getButton(),title:this._options.text.fileInputTitle}).getButtonId()),this._dnd&&(this._dnd.dispose(),this._dnd=this._setupDragAndDrop()),this._totalFilesInBatch=0,this._filesInBatchAddedToUi=0,this._setupClickAndEditEventHandlers()},setName:function(e,t){var n=this._options.formatFileName(t);this._parent.prototype.setName.apply(this,arguments),this._templating.updateFilename(e,n)},pauseUpload:function(e){var t=this._parent.prototype.pauseUpload.apply(this,arguments);return t&&this._templating.uploadPaused(e),t},continueUpload:function(e){var t=this._parent.prototype.continueUpload.apply(this,arguments);return t&&this._templating.uploadContinued(e),t},getId:function(e){return this._templating.getFileId(e)},getDropTarget:function(e){var t=this.getFile(e);return t.qqDropTarget}},qq.uiPrivateApi={_getButton:function(e){var t=this._parent.prototype._getButton.apply(this,arguments);return t||e===this._defaultButtonId&&(t=this._templating.getButton()),t},_removeFileItem:function(e){this._templating.removeFile(e)},_setupClickAndEditEventHandlers:function(){this._fileButtonsClickHandler=qq.FileButtonsClickHandler&&this._bindFileButtonsClickEvent(),this._focusinEventSupported=!qq.firefox(),this._isEditFilenameEnabled()&&(this._filenameClickHandler=this._bindFilenameClickEvent(),this._filenameInputFocusInHandler=this._bindFilenameInputFocusInEvent(),this._filenameInputFocusHandler=this._bindFilenameInputFocusEvent())},_setupDragAndDrop:function(){var e=this,t=this._options.dragAndDrop.extraDropzones,n=this._templating,i=n.getDropZone();return i&&t.push(i),new qq.DragAndDrop({dropZoneElements:t,allowMultipleItems:this._options.multiple,classes:{dropActive:this._options.classes.dropActive},callbacks:{processingDroppedFiles:function(){n.showDropProcessing()},processingDroppedFilesComplete:function(t,i){n.hideDropProcessing(),qq.each(t,function(e,t){t.qqDropTarget=i}),t.length&&e.addFiles(t,null,null)},dropError:function(t,n){e._itemError(t,n)},dropLog:function(t,n){e.log(t,n)}}})},_bindFileButtonsClickEvent:function(){var e=this;return new qq.FileButtonsClickHandler({templating:this._templating,log:function(t,n){e.log(t,n)},onDeleteFile:function(t){e.deleteFile(t)},onCancel:function(t){e.cancel(t)},onRetry:function(t){e.retry(t)},onPause:function(t){e.pauseUpload(t)},onContinue:function(t){e.continueUpload(t)},onGetName:function(t){return e.getName(t)}})},_isEditFilenameEnabled:function(){return this._templating.isEditFilenamePossible()&&!this._options.autoUpload&&qq.FilenameClickHandler&&qq.FilenameInputFocusHandler&&qq.FilenameInputFocusHandler},_filenameEditHandler:function(){var e=this,t=this._templating;return{templating:t,log:function(t,n){e.log(t,n)},onGetUploadStatus:function(t){return e.getUploads({id:t}).status},onGetName:function(t){return e.getName(t)},onSetName:function(t,n){e.setName(t,n)},onEditingStatusChange:function(e,n){var i=qq(t.getEditInput(e)),o=qq(t.getFileContainer(e));n?(i.addClass("qq-editing"),t.hideFilename(e),t.hideEditIcon(e)):(i.removeClass("qq-editing"),t.showFilename(e),t.showEditIcon(e)),o.addClass("qq-temp").removeClass("qq-temp")}}},_onUploadStatusChange:function(e,t,n){this._parent.prototype._onUploadStatusChange.apply(this,arguments),this._isEditFilenameEnabled()&&this._templating.getFileContainer(e)&&n!==qq.status.SUBMITTED&&(this._templating.markFilenameEditable(e),this._templating.hideEditIcon(e)),n===qq.status.UPLOAD_RETRYING?(this._templating.hideRetry(e),this._templating.setStatusText(e),qq(this._templating.getFileContainer(e)).removeClass(this._classes.retrying)):n===qq.status.UPLOAD_FAILED&&this._templating.hidePause(e)},_bindFilenameInputFocusInEvent:function(){var e=qq.extend({},this._filenameEditHandler());return new qq.FilenameInputFocusInHandler(e)},_bindFilenameInputFocusEvent:function(){var e=qq.extend({},this._filenameEditHandler());return new qq.FilenameInputFocusHandler(e)},_bindFilenameClickEvent:function(){var e=qq.extend({},this._filenameEditHandler());return new qq.FilenameClickHandler(e)},_storeForLater:function(e){this._parent.prototype._storeForLater.apply(this,arguments),this._templating.hideSpinner(e)},_onAllComplete:function(e,t){this._parent.prototype._onAllComplete.apply(this,arguments),this._templating.resetTotalProgress()},_onSubmit:function(e,t){var n=this.getFile(e);n&&n.qqPath&&this._options.dragAndDrop.reportDirectoryPaths&&this._paramsStore.addReadOnly(e,{qqpath:n.qqPath}),this._parent.prototype._onSubmit.apply(this,arguments),this._addToList(e,t)},_onSubmitted:function(e){this._isEditFilenameEnabled()&&(this._templating.markFilenameEditable(e),this._templating.showEditIcon(e),this._focusinEventSupported||this._filenameInputFocusHandler.addHandler(this._templating.getEditInput(e)))},_onProgress:function(e,t,n,i){this._parent.prototype._onProgress.apply(this,arguments),this._templating.updateProgress(e,n,i),100===Math.round(n/i*100)?(this._templating.hideCancel(e),this._templating.hidePause(e),this._templating.hideProgress(e),this._templating.setStatusText(e,this._options.text.waitingForResponse),this._displayFileSize(e)):this._displayFileSize(e,n,i)},_onTotalProgress:function(e,t){this._parent.prototype._onTotalProgress.apply(this,arguments),this._templating.updateTotalProgress(e,t)},_onComplete:function(e,t,n,i){function o(t){s&&(a.setStatusText(e),qq(s).removeClass(l._classes.retrying),a.hideProgress(e),l.getUploads({id:e}).status!==qq.status.UPLOAD_FAILED&&a.hideCancel(e),a.hideSpinner(e),t.success?l._markFileAsSuccessful(e):(qq(s).addClass(l._classes.fail),a.showCancel(e),a.isRetryPossible()&&!l._preventRetries[e]&&(qq(s).addClass(l._classes.retryable),a.showRetry(e)),l._controlFailureTextDisplay(e,t)))}var r=this._parent.prototype._onComplete.apply(this,arguments),a=this._templating,s=a.getFileContainer(e),l=this;return r instanceof qq.Promise?r.done(function(e){o(e)}):o(n),r},_markFileAsSuccessful:function(e){var t=this._templating;this._isDeletePossible()&&t.showDeleteButton(e),qq(t.getFileContainer(e)).addClass(this._classes.success),this._maybeUpdateThumbnail(e)},_onUploadPrep:function(e){this._parent.prototype._onUploadPrep.apply(this,arguments),this._templating.showSpinner(e)},_onUpload:function(e,t){var n=this._parent.prototype._onUpload.apply(this,arguments);return this._templating.showSpinner(e),n},_onUploadChunk:function(e,t){this._parent.prototype._onUploadChunk.apply(this,arguments),t.partIndex>0&&this._handler.isResumable(e)&&this._templating.allowPause(e)},_onCancel:function(e,t){this._parent.prototype._onCancel.apply(this,arguments),this._removeFileItem(e),0===this._getNotFinished()&&this._templating.resetTotalProgress()},_onBeforeAutoRetry:function(e){var t,n,i;this._parent.prototype._onBeforeAutoRetry.apply(this,arguments),this._showCancelLink(e),this._options.retry.showAutoRetryNote&&(t=this._autoRetries[e],n=this._options.retry.maxAutoAttempts,i=this._options.retry.autoRetryNote.replace(/\{retryNum\}/g,t),i=i.replace(/\{maxAuto\}/g,n),this._templating.setStatusText(e,i),qq(this._templating.getFileContainer(e)).addClass(this._classes.retrying))},_onBeforeManualRetry:function(e){return this._parent.prototype._onBeforeManualRetry.apply(this,arguments)?(this._templating.resetProgress(e),qq(this._templating.getFileContainer(e)).removeClass(this._classes.fail),this._templating.setStatusText(e),this._templating.showSpinner(e),this._showCancelLink(e),!0):(qq(this._templating.getFileContainer(e)).addClass(this._classes.retryable),this._templating.showRetry(e),!1)},_onSubmitDelete:function(e){var t=qq.bind(this._onSubmitDeleteSuccess,this);this._parent.prototype._onSubmitDelete.call(this,e,t)},_onSubmitDeleteSuccess:function(e,t,n){this._options.deleteFile.forceConfirm?this._showDeleteConfirm.apply(this,arguments):this._sendDeleteRequest.apply(this,arguments)},_onDeleteComplete:function(e,t,n){this._parent.prototype._onDeleteComplete.apply(this,arguments),this._templating.hideSpinner(e),n?(this._templating.setStatusText(e,this._options.deleteFile.deletingFailedText),this._templating.showDeleteButton(e)):this._removeFileItem(e)},_sendDeleteRequest:function(e,t,n){this._templating.hideDeleteButton(e),this._templating.showSpinner(e),this._templating.setStatusText(e,this._options.deleteFile.deletingStatusText),this._deleteHandler.sendDelete.apply(this,arguments)},_showDeleteConfirm:function(e,t,n){var i,o=this.getName(e),r=this._options.deleteFile.confirmMessage.replace(/\{filename\}/g,o),a=(this.getUuid(e),arguments),s=this;i=this._options.showConfirm(r),qq.isGenericPromise(i)?i.then(function(){s._sendDeleteRequest.apply(s,a)}):i!==!1&&s._sendDeleteRequest.apply(s,a)},_addToList:function(e,t,n){var i,o,r=0,a=this._handler.isProxied(e)&&this._options.scaling.hideScaled;this._options.display.prependFiles&&(this._totalFilesInBatch>1&&this._filesInBatchAddedToUi>0&&(r=this._filesInBatchAddedToUi-1),i={index:r}),n||(this._options.disableCancelForFormUploads&&!qq.supportedFeatures.ajaxUploading&&this._templating.disableCancel(),this._options.multiple||(o=this.getUploads({id:e}),this._handledProxyGroup=this._handledProxyGroup||o.proxyGroupId,o.proxyGroupId===this._handledProxyGroup&&o.proxyGroupId||(this._handler.cancelAll(),this._clearList(),this._handledProxyGroup=null))),n?(this._templating.addFileToCache(e,this._options.formatFileName(t),i,a),this._templating.updateThumbnail(e,this._thumbnailUrls[e],!0,this._options.thumbnails.customResizer)):(this._templating.addFile(e,this._options.formatFileName(t),i,a),this._templating.generatePreview(e,this.getFile(e),this._options.thumbnails.customResizer)),this._filesInBatchAddedToUi+=1,(n||this._options.display.fileSizeOnSubmit&&qq.supportedFeatures.ajaxUploading)&&this._displayFileSize(e)},_clearList:function(){this._templating.clearFiles(),this.clearStoredFiles()},_displayFileSize:function(e,t,n){var i=this.getSize(e),o=this._formatSize(i);i>=0&&(void 0!==t&&void 0!==n&&(o=this._formatProgress(t,n)),this._templating.updateSize(e,o))},_formatProgress:function(e,t){function n(e,t){i=i.replace(e,t)}var i=this._options.text.formatProgress;return n("{percent}",Math.round(e/t*100)),n("{total_size}",this._formatSize(t)),i},_controlFailureTextDisplay:function(e,t){var n,i,o;n=this._options.failedUploadTextDisplay.mode,i=this._options.failedUploadTextDisplay.responseProperty,"custom"===n?(o=t[i],o||(o=this._options.text.failUpload),this._templating.setStatusText(e,o),this._options.failedUploadTextDisplay.enableTooltip&&this._showTooltip(e,o)):"default"===n?this._templating.setStatusText(e,this._options.text.failUpload):"none"!==n&&this.log("failedUploadTextDisplay.mode value of '"+n+"' is not valid","warn")},_showTooltip:function(e,t){this._templating.getFileContainer(e).title=t},_showCancelLink:function(e){this._options.disableCancelForFormUploads&&!qq.supportedFeatures.ajaxUploading||this._templating.showCancel(e)},_itemError:function(e,t,n){var i=this._parent.prototype._itemError.apply(this,arguments);this._options.showMessage(i)},_batchError:function(e){this._parent.prototype._batchError.apply(this,arguments),this._options.showMessage(e)},_setupPastePrompt:function(){var e=this;this._options.callbacks.onPasteReceived=function(){var t=e._options.paste.namePromptMessage,n=e._options.paste.defaultName;return e._options.showPrompt(t,n)}},_fileOrBlobRejected:function(e,t){this._totalFilesInBatch-=1,this._parent.prototype._fileOrBlobRejected.apply(this,arguments)},_prepareItemsForUpload:function(e,t,n){this._totalFilesInBatch=e.length,this._filesInBatchAddedToUi=0,this._parent.prototype._prepareItemsForUpload.apply(this,arguments)},_maybeUpdateThumbnail:function(e){var t=this._thumbnailUrls[e],n=this.getUploads({id:e}).status;n===qq.status.DELETED||!t&&!this._options.thumbnails.placeholders.waitUntilResponse&&qq.supportedFeatures.imagePreviews||this._templating.updateThumbnail(e,t,this._options.thumbnails.customResizer)},_addCannedFile:function(e){var t=this._parent.prototype._addCannedFile.apply(this,arguments);return this._addToList(t,this.getName(t),!0),this._templating.hideSpinner(t),this._templating.hideCancel(t),this._markFileAsSuccessful(t),t},_setSize:function(e,t){this._parent.prototype._setSize.apply(this,arguments),this._templating.updateSize(e,this._formatSize(t))},_sessionRequestComplete:function(){this._templating.addCacheToDom(),this._parent.prototype._sessionRequestComplete.apply(this,arguments)}}}(),qq.FineUploader=function(e,t){"use strict";var n=this;this._parent=t?qq[t].FineUploaderBasic:qq.FineUploaderBasic,this._parent.apply(this,arguments),qq.extend(this._options,{element:null,button:null,listElement:null,dragAndDrop:{extraDropzones:[],reportDirectoryPaths:!1},text:{formatProgress:"{percent}% of {total_size}",failUpload:"Upload failed",waitingForResponse:"Processing...",paused:"Paused"},template:"qq-template",classes:{retrying:"qq-upload-retrying",retryable:"qq-upload-retryable",success:"qq-upload-success",fail:"qq-upload-fail",editable:"qq-editable",hide:"qq-hide",dropActive:"qq-upload-drop-area-active"},failedUploadTextDisplay:{mode:"default",responseProperty:"error",enableTooltip:!0},messages:{tooManyFilesError:"You may only drop one file",unsupportedBrowser:"Unrecoverable error - this browser does not permit file uploading of any kind."},retry:{showAutoRetryNote:!0,autoRetryNote:"Retrying {retryNum}/{maxAuto}..."},deleteFile:{forceConfirm:!1,confirmMessage:"Are you sure you want to delete {filename}?",deletingStatusText:"Deleting...",deletingFailedText:"Delete failed"},display:{fileSizeOnSubmit:!1,prependFiles:!1},paste:{promptForName:!1,namePromptMessage:"Please name this image"},thumbnails:{customResizer:null,maxCount:0,placeholders:{waitUntilResponse:!1,notAvailablePath:null,waitingPath:null},timeBetweenThumbs:750},scaling:{hideScaled:!1},showMessage:function(e){return n._templating.hasDialog("alert")?n._templating.showDialog("alert",e):void setTimeout(function(){window.alert(e)},0)},showConfirm:function(e){return n._templating.hasDialog("confirm")?n._templating.showDialog("confirm",e):window.confirm(e)},showPrompt:function(e,t){return n._templating.hasDialog("prompt")?n._templating.showDialog("prompt",e,t):window.prompt(e,t)}},!0),qq.extend(this._options,e,!0),this._templating=new qq.Templating({log:qq.bind(this.log,this),templateIdOrEl:this._options.template,containerEl:this._options.element,fileContainerEl:this._options.listElement,button:this._options.button,imageGenerator:this._imageGenerator,classes:{hide:this._options.classes.hide,editable:this._options.classes.editable},limits:{maxThumbs:this._options.thumbnails.maxCount,timeBetweenThumbs:this._options.thumbnails.timeBetweenThumbs},placeholders:{waitUntilUpdate:this._options.thumbnails.placeholders.waitUntilResponse,thumbnailNotAvailable:this._options.thumbnails.placeholders.notAvailablePath,waitingForThumbnail:this._options.thumbnails.placeholders.waitingPath},text:this._options.text}),this._options.workarounds.ios8SafariUploads&&qq.ios800()&&qq.iosSafari()?this._templating.renderFailure(this._options.messages.unsupportedBrowserIos8Safari):!qq.supportedFeatures.uploading||this._options.cors.expected&&!qq.supportedFeatures.uploadCors?this._templating.renderFailure(this._options.messages.unsupportedBrowser):(this._wrapCallbacks(),this._templating.render(),this._classes=this._options.classes,!this._options.button&&this._templating.getButton()&&(this._defaultButtonId=this._createUploadButton({element:this._templating.getButton(),title:this._options.text.fileInputTitle}).getButtonId()),this._setupClickAndEditEventHandlers(),qq.DragAndDrop&&qq.supportedFeatures.fileDrop&&(this._dnd=this._setupDragAndDrop()),this._options.paste.targetElement&&this._options.paste.promptForName&&(qq.PasteSupport?this._setupPastePrompt():this.log("Paste support module not found.","error")),this._totalFilesInBatch=0,this._filesInBatchAddedToUi=0)},qq.extend(qq.FineUploader.prototype,qq.basePublicApi),qq.extend(qq.FineUploader.prototype,qq.basePrivateApi),qq.extend(qq.FineUploader.prototype,qq.uiPublicApi),qq.extend(qq.FineUploader.prototype,qq.uiPrivateApi),qq.Templating=function(e){"use strict";var t,n,i,o,r,a,s,l,u="qq-file-id",c="qq-file-id-",d="qq-max-size",p="qq-server-scale",h="qq-hide-dropzone",q="qq-drop-area-text",f="qq-in-progress",m="qq-hidden-forever",g={content:document.createDocumentFragment(),map:{}},_=!1,b=0,v=!1,y=[],S=-1,w={log:null,limits:{maxThumbs:0,timeBetweenThumbs:750},templateIdOrEl:"qq-template",containerEl:null,fileContainerEl:null,button:null,imageGenerator:null,classes:{hide:"qq-hide",editable:"qq-editable"},placeholders:{waitUntilUpdate:!1,thumbnailNotAvailable:null,waitingForThumbnail:null},text:{paused:"Paused"}},F={button:"qq-upload-button-selector",alertDialog:"qq-alert-dialog-selector",dialogCancelButton:"qq-cancel-button-selector",confirmDialog:"qq-confirm-dialog-selector",dialogMessage:"qq-dialog-message-selector",dialogOkButton:"qq-ok-button-selector",promptDialog:"qq-prompt-dialog-selector",uploader:"qq-uploader-selector",drop:"qq-upload-drop-area-selector",list:"qq-upload-list-selector",progressBarContainer:"qq-progress-bar-container-selector",progressBar:"qq-progress-bar-selector",totalProgressBarContainer:"qq-total-progress-bar-container-selector",totalProgressBar:"qq-total-progress-bar-selector",file:"qq-upload-file-selector",spinner:"qq-upload-spinner-selector",size:"qq-upload-size-selector",cancel:"qq-upload-cancel-selector",pause:"qq-upload-pause-selector",continueButton:"qq-upload-continue-selector",deleteButton:"qq-upload-delete-selector",retry:"qq-upload-retry-selector",statusText:"qq-upload-status-text-selector",editFilenameInput:"qq-edit-filename-selector",editNameIcon:"qq-edit-filename-icon-selector",dropText:"qq-upload-drop-area-text-selector",dropProcessing:"qq-drop-processing-selector",dropProcessingSpinner:"qq-drop-processing-spinner-selector",thumbnail:"qq-thumbnail-selector"},x={},C=new qq.Promise,E=new qq.Promise,I=function(){var e=w.placeholders.thumbnailNotAvailable,n=w.placeholders.waitingForThumbnail,i={maxSize:S,scale:l};s&&(e?w.imageGenerator.generate(e,new Image,i).then(function(e){C.success(e)},function(){C.failure(),t("Problem loading 'not available' placeholder image at "+e,"error")}):C.failure(),n?w.imageGenerator.generate(n,new Image,i).then(function(e){E.success(e)},function(){E.failure(),t("Problem loading 'waiting for thumbnail' placeholder image at "+n,"error")}):E.failure())},P=function(e){var t=new qq.Promise;return E.then(function(n){Y(n,e),e.src?t.success():(e.src=n.src,e.onload=function(){e.onload=null,te(e),t.success()})},function(){W(e),t.success()}),t},D=function(e,n,i){var o=X(e);return t("Generating new thumbnail for "+e),n.qqThumbnailId=e,w.imageGenerator.generate(n,o,i).then(function(){b++,te(o),x[e].success()},function(){x[e].failure(),w.placeholders.waitUntilUpdate||Q(e,o)})},T=function(){if(y.length){v=!0;var e=y.shift();e.update?$(e):K(e)}else v=!1},U=function(e){return V(L(e),F.cancel)},k=function(e){return V(L(e),F.continueButton)},A=function(e){return V(r,F[e+"Dialog"])},R=function(e){return V(L(e),F.deleteButton)},B=function(){return V(r,F.dropProcessing)},N=function(e){return V(L(e),F.editNameIcon);
	},L=function(e){return g.map[e]||qq(a).getFirstByClass(c+e)},O=function(e){return V(L(e),F.file)},H=function(e){return V(L(e),F.pause)},z=function(e){return null==e?V(r,F.totalProgressBarContainer)||V(r,F.totalProgressBar):V(L(e),F.progressBarContainer)||V(L(e),F.progressBar)},M=function(e){return V(L(e),F.retry)},j=function(e){return V(L(e),F.size)},G=function(e){return V(L(e),F.spinner)},V=function(e,t){return e&&qq(e).getFirstByClass(t)},X=function(e){return s&&V(L(e),F.thumbnail)},W=function(e){e&&qq(e).addClass(w.classes.hide)},Y=function(e,t){var n=e.style.maxWidth,i=e.style.maxHeight;i&&n&&!t.style.maxWidth&&!t.style.maxHeight&&qq(t).css({maxWidth:n,maxHeight:i})},Q=function(e,t){var n=x[e]||(new qq.Promise).failure(),i=new qq.Promise;return C.then(function(e){n.then(function(){i.success()},function(){Y(e,t),t.onload=function(){t.onload=null,i.success()},t.src=e.src,te(t)})}),i},J=function(){var e,o,r,a,u,c,f,m,g,_,b;if(t("Parsing template"),null==w.templateIdOrEl)throw new Error("You MUST specify either a template element or ID!");if(qq.isString(w.templateIdOrEl)){if(e=document.getElementById(w.templateIdOrEl),null===e)throw new Error(qq.format("Cannot find template script at ID '{}'!",w.templateIdOrEl));o=e.innerHTML}else{if(void 0===w.templateIdOrEl.innerHTML)throw new Error("You have specified an invalid value for the template option!  It must be an ID or an Element.");o=w.templateIdOrEl.innerHTML}if(o=qq.trimStr(o),a=document.createElement("div"),a.appendChild(qq.toElement(o)),b=qq(a).getFirstByClass(F.uploader),w.button&&(c=qq(a).getFirstByClass(F.button),c&&qq(c).remove()),qq.DragAndDrop&&qq.supportedFeatures.fileDrop||(g=qq(a).getFirstByClass(F.dropProcessing),g&&qq(g).remove()),f=qq(a).getFirstByClass(F.drop),f&&!qq.DragAndDrop&&(t("DnD module unavailable.","info"),qq(f).remove()),qq.supportedFeatures.fileDrop?qq(b).hasAttribute(q)&&f&&(_=qq(f).getFirstByClass(F.dropText),_&&qq(_).remove()):(b.removeAttribute(q),f&&qq(f).hasAttribute(h)&&qq(f).css({display:"none"})),m=qq(a).getFirstByClass(F.thumbnail),s?m&&(S=parseInt(m.getAttribute(d)),S=S>0?S:null,l=qq(m).hasAttribute(p)):m&&qq(m).remove(),s=s&&m,n=qq(a).getByClass(F.editFilenameInput).length>0,i=qq(a).getByClass(F.retry).length>0,r=qq(a).getFirstByClass(F.list),null==r)throw new Error("Could not find the file list container in the template!");return u=r.innerHTML,r.innerHTML="",a.getElementsByTagName("DIALOG").length&&document.createElement("dialog"),t("Template parsing complete"),{template:qq.trimStr(a.innerHTML),fileTemplate:qq.trimStr(u)}},Z=function(e,t,n){var i=n,o=i.firstChild;t>0&&(o=qq(i).children()[t].nextSibling),i.insertBefore(e,o)},K=function(e){var t=e.id,n=e.optFileOrBlob,i=n&&n.qqThumbnailId,o=X(t),r={customResizeFunction:e.customResizeFunction,maxSize:S,orient:!0,scale:!0};qq.supportedFeatures.imagePreviews?o?w.limits.maxThumbs&&w.limits.maxThumbs<=b?(Q(t,o),T()):P(o).done(function(){x[t]=new qq.Promise,x[t].done(function(){setTimeout(T,w.limits.timeBetweenThumbs)}),null!=i?ne(t,i):D(t,n,r)}):T():o&&(P(o),T())},$=function(e){var t=e.id,n=e.thumbnailUrl,i=e.showWaitingImg,o=X(t),r={customResizeFunction:e.customResizeFunction,scale:l,maxSize:S};if(o)if(n){if(!(w.limits.maxThumbs&&w.limits.maxThumbs<=b))return i&&P(o),w.imageGenerator.generate(n,o,r).then(function(){te(o),b++,setTimeout(T,w.limits.timeBetweenThumbs)},function(){Q(t,o),setTimeout(T,w.limits.timeBetweenThumbs)});Q(t,o),T()}else Q(t,o),T()},ee=function(e,t){var n=z(e),i=null==e?F.totalProgressBar:F.progressBar;n&&!qq(n).hasClass(i)&&(n=qq(n).getFirstByClass(i)),n&&(qq(n).css({width:t+"%"}),n.setAttribute("aria-valuenow",t))},te=function(e){e&&qq(e).removeClass(w.classes.hide)},ne=function(e,n){var i=X(e),o=X(n);t(qq.format("ID {} is the same file as ID {}.  Will use generated thumbnail from ID {} instead.",e,n,n)),x[n].then(function(){b++,x[e].success(),t(qq.format("Now using previously generated thumbnail created for ID {} on ID {}.",n,e)),i.src=o.src,te(i)},function(){x[e].failure(),w.placeholders.waitUntilUpdate||Q(e,i)})};qq.extend(w,e),t=w.log,qq.supportedFeatures.imagePreviews||(w.limits.timeBetweenThumbs=0,w.limits.maxThumbs=0),r=w.containerEl,s=void 0!==w.imageGenerator,o=J(),I(),qq.extend(this,{render:function(){t("Rendering template in DOM."),b=0,r.innerHTML=o.template,W(B()),this.hideTotalProgress(),a=w.fileContainerEl||V(r,F.list),t("Template rendering complete")},renderFailure:function(e){var t=qq.toElement(e);r.innerHTML="",r.appendChild(t)},reset:function(){this.render()},clearFiles:function(){a.innerHTML=""},disableCancel:function(){_=!0},addFile:function(e,t,n,i,s){var l,d=qq.toElement(o.fileTemplate),p=V(d,F.file),h=V(r,F.uploader),f=s?g.content:a;s&&(g.map[e]=d),qq(d).addClass(c+e),h.removeAttribute(q),p&&(qq(p).setText(t),p.setAttribute("title",t)),d.setAttribute(u,e),n?Z(d,n.index,f):f.appendChild(d),i?(d.style.display="none",qq(d).addClass(m)):(W(z(e)),W(j(e)),W(R(e)),W(M(e)),W(H(e)),W(k(e)),_&&this.hideCancel(e),l=X(e),l&&!l.src&&E.then(function(e){l.src=e.src,e.style.maxHeight&&e.style.maxWidth&&qq(l).css({maxHeight:e.style.maxHeight,maxWidth:e.style.maxWidth}),te(l)}))},addFileToCache:function(e,t,n,i){this.addFile(e,t,n,i,!0)},addCacheToDom:function(){a.appendChild(g.content),g.content=document.createDocumentFragment(),g.map={}},removeFile:function(e){qq(L(e)).remove()},getFileId:function(e){var t=e;if(t){for(;null==t.getAttribute(u);)t=t.parentNode;return parseInt(t.getAttribute(u))}},getFileList:function(){return a},markFilenameEditable:function(e){var t=O(e);t&&qq(t).addClass(w.classes.editable)},updateFilename:function(e,t){var n=O(e);n&&(qq(n).setText(t),n.setAttribute("title",t))},hideFilename:function(e){W(O(e))},showFilename:function(e){te(O(e))},isFileName:function(e){return qq(e).hasClass(F.file)},getButton:function(){return w.button||V(r,F.button)},hideDropProcessing:function(){W(B())},showDropProcessing:function(){te(B())},getDropZone:function(){return V(r,F.drop)},isEditFilenamePossible:function(){return n},hideRetry:function(e){W(M(e))},isRetryPossible:function(){return i},showRetry:function(e){te(M(e))},getFileContainer:function(e){return L(e)},showEditIcon:function(e){var t=N(e);t&&qq(t).addClass(w.classes.editable)},isHiddenForever:function(e){return qq(L(e)).hasClass(m)},hideEditIcon:function(e){var t=N(e);t&&qq(t).removeClass(w.classes.editable)},isEditIcon:function(e){return qq(e).hasClass(F.editNameIcon,!0)},getEditInput:function(e){return V(L(e),F.editFilenameInput)},isEditInput:function(e){return qq(e).hasClass(F.editFilenameInput,!0)},updateProgress:function(e,t,n){var i,o=z(e);o&&n>0&&(i=Math.round(t/n*100),100===i?W(o):te(o),ee(e,i))},updateTotalProgress:function(e,t){this.updateProgress(null,e,t)},hideProgress:function(e){var t=z(e);t&&W(t)},hideTotalProgress:function(){this.hideProgress()},resetProgress:function(e){ee(e,0),this.hideTotalProgress(e)},resetTotalProgress:function(){this.resetProgress()},showCancel:function(e){if(!_){var t=U(e);t&&qq(t).removeClass(w.classes.hide)}},hideCancel:function(e){W(U(e))},isCancel:function(e){return qq(e).hasClass(F.cancel,!0)},allowPause:function(e){te(H(e)),W(k(e))},uploadPaused:function(e){this.setStatusText(e,w.text.paused),this.allowContinueButton(e),W(G(e))},hidePause:function(e){W(H(e))},isPause:function(e){return qq(e).hasClass(F.pause,!0)},isContinueButton:function(e){return qq(e).hasClass(F.continueButton,!0)},allowContinueButton:function(e){te(k(e)),W(H(e))},uploadContinued:function(e){this.setStatusText(e,""),this.allowPause(e),te(G(e))},showDeleteButton:function(e){te(R(e))},hideDeleteButton:function(e){W(R(e))},isDeleteButton:function(e){return qq(e).hasClass(F.deleteButton,!0)},isRetry:function(e){return qq(e).hasClass(F.retry,!0)},updateSize:function(e,t){var n=j(e);n&&(te(n),qq(n).setText(t))},setStatusText:function(e,t){var n=V(L(e),F.statusText);n&&(null==t?qq(n).clearText():qq(n).setText(t))},hideSpinner:function(e){qq(L(e)).removeClass(f),W(G(e))},showSpinner:function(e){qq(L(e)).addClass(f),te(G(e))},generatePreview:function(e,t,n){this.isHiddenForever(e)||(y.push({id:e,customResizeFunction:n,optFileOrBlob:t}),!v&&T())},updateThumbnail:function(e,t,n,i){this.isHiddenForever(e)||(y.push({customResizeFunction:i,update:!0,id:e,thumbnailUrl:t,showWaitingImg:n}),!v&&T())},hasDialog:function(e){return qq.supportedFeatures.dialogElement&&!!A(e)},showDialog:function(e,t,n){var i=A(e),o=V(i,F.dialogMessage),r=i.getElementsByTagName("INPUT")[0],a=V(i,F.dialogCancelButton),s=V(i,F.dialogOkButton),l=new qq.Promise,u=function(){a.removeEventListener("click",c),s&&s.removeEventListener("click",d),l.failure()},c=function(){a.removeEventListener("click",c),i.close()},d=function(){i.removeEventListener("close",u),s.removeEventListener("click",d),i.close(),l.success(r&&r.value)};return i.addEventListener("close",u),a.addEventListener("click",c),s&&s.addEventListener("click",d),r&&(r.value=n),o.textContent=t,i.showModal(),l}})},qq.UiEventHandler=function(e,t){"use strict";function n(e){i.attach(e,o.eventType,function(e){e=e||window.event;var t=e.target||e.srcElement;o.onHandled(t,e)})}var i=new qq.DisposeSupport,o={eventType:"click",attachTo:null,onHandled:function(e,t){}};qq.extend(this,{addHandler:function(e){n(e)},dispose:function(){i.dispose()}}),qq.extend(t,{getFileIdFromItem:function(e){return e.qqFileId},getDisposeSupport:function(){return i}}),qq.extend(o,e),o.attachTo&&n(o.attachTo)},qq.FileButtonsClickHandler=function(e){"use strict";function t(e,t){qq.each(o,function(n,o){var r,a=n.charAt(0).toUpperCase()+n.slice(1);if(i.templating["is"+a](e))return r=i.templating.getFileId(e),qq.preventDefault(t),i.log(qq.format("Detected valid file button click event on file '{}', ID: {}.",i.onGetName(r),r)),o(r),!1})}var n={},i={templating:null,log:function(e,t){},onDeleteFile:function(e){},onCancel:function(e){},onRetry:function(e){},onPause:function(e){},onContinue:function(e){},onGetName:function(e){}},o={cancel:function(e){i.onCancel(e)},retry:function(e){i.onRetry(e)},deleteButton:function(e){i.onDeleteFile(e)},pause:function(e){i.onPause(e)},continueButton:function(e){i.onContinue(e)}};qq.extend(i,e),i.eventType="click",i.onHandled=t,i.attachTo=i.templating.getFileList(),qq.extend(this,new qq.UiEventHandler(i,n))},qq.FilenameClickHandler=function(e){"use strict";function t(e,t){if(i.templating.isFileName(e)||i.templating.isEditIcon(e)){var o=i.templating.getFileId(e),r=i.onGetUploadStatus(o);r===qq.status.SUBMITTED&&(i.log(qq.format("Detected valid filename click event on file '{}', ID: {}.",i.onGetName(o),o)),qq.preventDefault(t),n.handleFilenameEdit(o,e,!0))}}var n={},i={templating:null,log:function(e,t){},classes:{file:"qq-upload-file",editNameIcon:"qq-edit-filename-icon"},onGetUploadStatus:function(e){},onGetName:function(e){}};qq.extend(i,e),i.eventType="click",i.onHandled=t,qq.extend(this,new qq.FilenameEditHandler(i,n))},qq.FilenameInputFocusInHandler=function(e,t){"use strict";function n(e,n){if(i.templating.isEditInput(e)){var o=i.templating.getFileId(e),r=i.onGetUploadStatus(o);r===qq.status.SUBMITTED&&(i.log(qq.format("Detected valid filename input focus event on file '{}', ID: {}.",i.onGetName(o),o)),t.handleFilenameEdit(o,e))}}var i={templating:null,onGetUploadStatus:function(e){},log:function(e,t){}};t||(t={}),i.eventType="focusin",i.onHandled=n,qq.extend(i,e),qq.extend(this,new qq.FilenameEditHandler(i,t))},qq.FilenameInputFocusHandler=function(e){"use strict";e.eventType="focus",e.attachTo=null,qq.extend(this,new qq.FilenameInputFocusInHandler(e,{}))},qq.FilenameEditHandler=function(e,t){"use strict";function n(e){var t=s.onGetName(e),n=t.lastIndexOf(".");return n>0&&(t=t.substr(0,n)),t}function i(e){var t=s.onGetName(e);return qq.getExtension(t)}function o(e,t){var n,o=e.value;void 0!==o&&qq.trimStr(o).length>0&&(n=i(t),void 0!==n&&(o=o+"."+n),s.onSetName(t,o)),s.onEditingStatusChange(t,!1)}function r(e,n){t.getDisposeSupport().attach(e,"blur",function(){o(e,n)})}function a(e,n){t.getDisposeSupport().attach(e,"keyup",function(t){var i=t.keyCode||t.which;13===i&&o(e,n)})}var s={templating:null,log:function(e,t){},onGetUploadStatus:function(e){},onGetName:function(e){},onSetName:function(e,t){},onEditingStatusChange:function(e,t){}};qq.extend(s,e),s.attachTo=s.templating.getFileList(),qq.extend(this,new qq.UiEventHandler(s,t)),qq.extend(t,{handleFilenameEdit:function(e,t,i){var o=s.templating.getEditInput(e);s.onEditingStatusChange(e,!0),o.value=n(e),i&&o.focus(),r(o,e),a(o,e)}})}}(window);
	//# sourceMappingURL=fine-uploader.min.js.map

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery UI Widget 1.12.1
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 */
	
	//>>label: Widget
	//>>group: Core
	//>>description: Provides a factory for creating stateful widgets with a common API.
	//>>docs: http://api.jqueryui.com/jQuery.widget/
	//>>demos: http://jqueryui.com/widget/
	
	( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(3), __webpack_require__(10) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	}( function( $ ) {
	
	var widgetUuid = 0;
	var widgetSlice = Array.prototype.slice;
	
	$.cleanData = ( function( orig ) {
		return function( elems ) {
			var events, elem, i;
			for ( i = 0; ( elem = elems[ i ] ) != null; i++ ) {
				try {
	
					// Only trigger remove when necessary to save time
					events = $._data( elem, "events" );
					if ( events && events.remove ) {
						$( elem ).triggerHandler( "remove" );
					}
	
				// Http://bugs.jquery.com/ticket/8235
				} catch ( e ) {}
			}
			orig( elems );
		};
	} )( $.cleanData );
	
	$.widget = function( name, base, prototype ) {
		var existingConstructor, constructor, basePrototype;
	
		// ProxiedPrototype allows the provided prototype to remain unmodified
		// so that it can be used as a mixin for multiple widgets (#8876)
		var proxiedPrototype = {};
	
		var namespace = name.split( "." )[ 0 ];
		name = name.split( "." )[ 1 ];
		var fullName = namespace + "-" + name;
	
		if ( !prototype ) {
			prototype = base;
			base = $.Widget;
		}
	
		if ( $.isArray( prototype ) ) {
			prototype = $.extend.apply( null, [ {} ].concat( prototype ) );
		}
	
		// Create selector for plugin
		$.expr[ ":" ][ fullName.toLowerCase() ] = function( elem ) {
			return !!$.data( elem, fullName );
		};
	
		$[ namespace ] = $[ namespace ] || {};
		existingConstructor = $[ namespace ][ name ];
		constructor = $[ namespace ][ name ] = function( options, element ) {
	
			// Allow instantiation without "new" keyword
			if ( !this._createWidget ) {
				return new constructor( options, element );
			}
	
			// Allow instantiation without initializing for simple inheritance
			// must use "new" keyword (the code above always passes args)
			if ( arguments.length ) {
				this._createWidget( options, element );
			}
		};
	
		// Extend with the existing constructor to carry over any static properties
		$.extend( constructor, existingConstructor, {
			version: prototype.version,
	
			// Copy the object used to create the prototype in case we need to
			// redefine the widget later
			_proto: $.extend( {}, prototype ),
	
			// Track widgets that inherit from this widget in case this widget is
			// redefined after a widget inherits from it
			_childConstructors: []
		} );
	
		basePrototype = new base();
	
		// We need to make the options hash a property directly on the new instance
		// otherwise we'll modify the options hash on the prototype that we're
		// inheriting from
		basePrototype.options = $.widget.extend( {}, basePrototype.options );
		$.each( prototype, function( prop, value ) {
			if ( !$.isFunction( value ) ) {
				proxiedPrototype[ prop ] = value;
				return;
			}
			proxiedPrototype[ prop ] = ( function() {
				function _super() {
					return base.prototype[ prop ].apply( this, arguments );
				}
	
				function _superApply( args ) {
					return base.prototype[ prop ].apply( this, args );
				}
	
				return function() {
					var __super = this._super;
					var __superApply = this._superApply;
					var returnValue;
	
					this._super = _super;
					this._superApply = _superApply;
	
					returnValue = value.apply( this, arguments );
	
					this._super = __super;
					this._superApply = __superApply;
	
					return returnValue;
				};
			} )();
		} );
		constructor.prototype = $.widget.extend( basePrototype, {
	
			// TODO: remove support for widgetEventPrefix
			// always use the name + a colon as the prefix, e.g., draggable:start
			// don't prefix for widgets that aren't DOM-based
			widgetEventPrefix: existingConstructor ? ( basePrototype.widgetEventPrefix || name ) : name
		}, proxiedPrototype, {
			constructor: constructor,
			namespace: namespace,
			widgetName: name,
			widgetFullName: fullName
		} );
	
		// If this widget is being redefined then we need to find all widgets that
		// are inheriting from it and redefine all of them so that they inherit from
		// the new version of this widget. We're essentially trying to replace one
		// level in the prototype chain.
		if ( existingConstructor ) {
			$.each( existingConstructor._childConstructors, function( i, child ) {
				var childPrototype = child.prototype;
	
				// Redefine the child widget using the same prototype that was
				// originally used, but inherit from the new version of the base
				$.widget( childPrototype.namespace + "." + childPrototype.widgetName, constructor,
					child._proto );
			} );
	
			// Remove the list of existing child constructors from the old constructor
			// so the old child constructors can be garbage collected
			delete existingConstructor._childConstructors;
		} else {
			base._childConstructors.push( constructor );
		}
	
		$.widget.bridge( name, constructor );
	
		return constructor;
	};
	
	$.widget.extend = function( target ) {
		var input = widgetSlice.call( arguments, 1 );
		var inputIndex = 0;
		var inputLength = input.length;
		var key;
		var value;
	
		for ( ; inputIndex < inputLength; inputIndex++ ) {
			for ( key in input[ inputIndex ] ) {
				value = input[ inputIndex ][ key ];
				if ( input[ inputIndex ].hasOwnProperty( key ) && value !== undefined ) {
	
					// Clone objects
					if ( $.isPlainObject( value ) ) {
						target[ key ] = $.isPlainObject( target[ key ] ) ?
							$.widget.extend( {}, target[ key ], value ) :
	
							// Don't extend strings, arrays, etc. with objects
							$.widget.extend( {}, value );
	
					// Copy everything else by reference
					} else {
						target[ key ] = value;
					}
				}
			}
		}
		return target;
	};
	
	$.widget.bridge = function( name, object ) {
		var fullName = object.prototype.widgetFullName || name;
		$.fn[ name ] = function( options ) {
			var isMethodCall = typeof options === "string";
			var args = widgetSlice.call( arguments, 1 );
			var returnValue = this;
	
			if ( isMethodCall ) {
	
				// If this is an empty collection, we need to have the instance method
				// return undefined instead of the jQuery instance
				if ( !this.length && options === "instance" ) {
					returnValue = undefined;
				} else {
					this.each( function() {
						var methodValue;
						var instance = $.data( this, fullName );
	
						if ( options === "instance" ) {
							returnValue = instance;
							return false;
						}
	
						if ( !instance ) {
							return $.error( "cannot call methods on " + name +
								" prior to initialization; " +
								"attempted to call method '" + options + "'" );
						}
	
						if ( !$.isFunction( instance[ options ] ) || options.charAt( 0 ) === "_" ) {
							return $.error( "no such method '" + options + "' for " + name +
								" widget instance" );
						}
	
						methodValue = instance[ options ].apply( instance, args );
	
						if ( methodValue !== instance && methodValue !== undefined ) {
							returnValue = methodValue && methodValue.jquery ?
								returnValue.pushStack( methodValue.get() ) :
								methodValue;
							return false;
						}
					} );
				}
			} else {
	
				// Allow multiple hashes to be passed on init
				if ( args.length ) {
					options = $.widget.extend.apply( null, [ options ].concat( args ) );
				}
	
				this.each( function() {
					var instance = $.data( this, fullName );
					if ( instance ) {
						instance.option( options || {} );
						if ( instance._init ) {
							instance._init();
						}
					} else {
						$.data( this, fullName, new object( options, this ) );
					}
				} );
			}
	
			return returnValue;
		};
	};
	
	$.Widget = function( /* options, element */ ) {};
	$.Widget._childConstructors = [];
	
	$.Widget.prototype = {
		widgetName: "widget",
		widgetEventPrefix: "",
		defaultElement: "<div>",
	
		options: {
			classes: {},
			disabled: false,
	
			// Callbacks
			create: null
		},
	
		_createWidget: function( options, element ) {
			element = $( element || this.defaultElement || this )[ 0 ];
			this.element = $( element );
			this.uuid = widgetUuid++;
			this.eventNamespace = "." + this.widgetName + this.uuid;
	
			this.bindings = $();
			this.hoverable = $();
			this.focusable = $();
			this.classesElementLookup = {};
	
			if ( element !== this ) {
				$.data( element, this.widgetFullName, this );
				this._on( true, this.element, {
					remove: function( event ) {
						if ( event.target === element ) {
							this.destroy();
						}
					}
				} );
				this.document = $( element.style ?
	
					// Element within the document
					element.ownerDocument :
	
					// Element is window or document
					element.document || element );
				this.window = $( this.document[ 0 ].defaultView || this.document[ 0 ].parentWindow );
			}
	
			this.options = $.widget.extend( {},
				this.options,
				this._getCreateOptions(),
				options );
	
			this._create();
	
			if ( this.options.disabled ) {
				this._setOptionDisabled( this.options.disabled );
			}
	
			this._trigger( "create", null, this._getCreateEventData() );
			this._init();
		},
	
		_getCreateOptions: function() {
			return {};
		},
	
		_getCreateEventData: $.noop,
	
		_create: $.noop,
	
		_init: $.noop,
	
		destroy: function() {
			var that = this;
	
			this._destroy();
			$.each( this.classesElementLookup, function( key, value ) {
				that._removeClass( value, key );
			} );
	
			// We can probably remove the unbind calls in 2.0
			// all event bindings should go through this._on()
			this.element
				.off( this.eventNamespace )
				.removeData( this.widgetFullName );
			this.widget()
				.off( this.eventNamespace )
				.removeAttr( "aria-disabled" );
	
			// Clean up events and states
			this.bindings.off( this.eventNamespace );
		},
	
		_destroy: $.noop,
	
		widget: function() {
			return this.element;
		},
	
		option: function( key, value ) {
			var options = key;
			var parts;
			var curOption;
			var i;
	
			if ( arguments.length === 0 ) {
	
				// Don't return a reference to the internal hash
				return $.widget.extend( {}, this.options );
			}
	
			if ( typeof key === "string" ) {
	
				// Handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
				options = {};
				parts = key.split( "." );
				key = parts.shift();
				if ( parts.length ) {
					curOption = options[ key ] = $.widget.extend( {}, this.options[ key ] );
					for ( i = 0; i < parts.length - 1; i++ ) {
						curOption[ parts[ i ] ] = curOption[ parts[ i ] ] || {};
						curOption = curOption[ parts[ i ] ];
					}
					key = parts.pop();
					if ( arguments.length === 1 ) {
						return curOption[ key ] === undefined ? null : curOption[ key ];
					}
					curOption[ key ] = value;
				} else {
					if ( arguments.length === 1 ) {
						return this.options[ key ] === undefined ? null : this.options[ key ];
					}
					options[ key ] = value;
				}
			}
	
			this._setOptions( options );
	
			return this;
		},
	
		_setOptions: function( options ) {
			var key;
	
			for ( key in options ) {
				this._setOption( key, options[ key ] );
			}
	
			return this;
		},
	
		_setOption: function( key, value ) {
			if ( key === "classes" ) {
				this._setOptionClasses( value );
			}
	
			this.options[ key ] = value;
	
			if ( key === "disabled" ) {
				this._setOptionDisabled( value );
			}
	
			return this;
		},
	
		_setOptionClasses: function( value ) {
			var classKey, elements, currentElements;
	
			for ( classKey in value ) {
				currentElements = this.classesElementLookup[ classKey ];
				if ( value[ classKey ] === this.options.classes[ classKey ] ||
						!currentElements ||
						!currentElements.length ) {
					continue;
				}
	
				// We are doing this to create a new jQuery object because the _removeClass() call
				// on the next line is going to destroy the reference to the current elements being
				// tracked. We need to save a copy of this collection so that we can add the new classes
				// below.
				elements = $( currentElements.get() );
				this._removeClass( currentElements, classKey );
	
				// We don't use _addClass() here, because that uses this.options.classes
				// for generating the string of classes. We want to use the value passed in from
				// _setOption(), this is the new value of the classes option which was passed to
				// _setOption(). We pass this value directly to _classes().
				elements.addClass( this._classes( {
					element: elements,
					keys: classKey,
					classes: value,
					add: true
				} ) );
			}
		},
	
		_setOptionDisabled: function( value ) {
			this._toggleClass( this.widget(), this.widgetFullName + "-disabled", null, !!value );
	
			// If the widget is becoming disabled, then nothing is interactive
			if ( value ) {
				this._removeClass( this.hoverable, null, "ui-state-hover" );
				this._removeClass( this.focusable, null, "ui-state-focus" );
			}
		},
	
		enable: function() {
			return this._setOptions( { disabled: false } );
		},
	
		disable: function() {
			return this._setOptions( { disabled: true } );
		},
	
		_classes: function( options ) {
			var full = [];
			var that = this;
	
			options = $.extend( {
				element: this.element,
				classes: this.options.classes || {}
			}, options );
	
			function processClassString( classes, checkOption ) {
				var current, i;
				for ( i = 0; i < classes.length; i++ ) {
					current = that.classesElementLookup[ classes[ i ] ] || $();
					if ( options.add ) {
						current = $( $.unique( current.get().concat( options.element.get() ) ) );
					} else {
						current = $( current.not( options.element ).get() );
					}
					that.classesElementLookup[ classes[ i ] ] = current;
					full.push( classes[ i ] );
					if ( checkOption && options.classes[ classes[ i ] ] ) {
						full.push( options.classes[ classes[ i ] ] );
					}
				}
			}
	
			this._on( options.element, {
				"remove": "_untrackClassesElement"
			} );
	
			if ( options.keys ) {
				processClassString( options.keys.match( /\S+/g ) || [], true );
			}
			if ( options.extra ) {
				processClassString( options.extra.match( /\S+/g ) || [] );
			}
	
			return full.join( " " );
		},
	
		_untrackClassesElement: function( event ) {
			var that = this;
			$.each( that.classesElementLookup, function( key, value ) {
				if ( $.inArray( event.target, value ) !== -1 ) {
					that.classesElementLookup[ key ] = $( value.not( event.target ).get() );
				}
			} );
		},
	
		_removeClass: function( element, keys, extra ) {
			return this._toggleClass( element, keys, extra, false );
		},
	
		_addClass: function( element, keys, extra ) {
			return this._toggleClass( element, keys, extra, true );
		},
	
		_toggleClass: function( element, keys, extra, add ) {
			add = ( typeof add === "boolean" ) ? add : extra;
			var shift = ( typeof element === "string" || element === null ),
				options = {
					extra: shift ? keys : extra,
					keys: shift ? element : keys,
					element: shift ? this.element : element,
					add: add
				};
			options.element.toggleClass( this._classes( options ), add );
			return this;
		},
	
		_on: function( suppressDisabledCheck, element, handlers ) {
			var delegateElement;
			var instance = this;
	
			// No suppressDisabledCheck flag, shuffle arguments
			if ( typeof suppressDisabledCheck !== "boolean" ) {
				handlers = element;
				element = suppressDisabledCheck;
				suppressDisabledCheck = false;
			}
	
			// No element argument, shuffle and use this.element
			if ( !handlers ) {
				handlers = element;
				element = this.element;
				delegateElement = this.widget();
			} else {
				element = delegateElement = $( element );
				this.bindings = this.bindings.add( element );
			}
	
			$.each( handlers, function( event, handler ) {
				function handlerProxy() {
	
					// Allow widgets to customize the disabled handling
					// - disabled as an array instead of boolean
					// - disabled class as method for disabling individual parts
					if ( !suppressDisabledCheck &&
							( instance.options.disabled === true ||
							$( this ).hasClass( "ui-state-disabled" ) ) ) {
						return;
					}
					return ( typeof handler === "string" ? instance[ handler ] : handler )
						.apply( instance, arguments );
				}
	
				// Copy the guid so direct unbinding works
				if ( typeof handler !== "string" ) {
					handlerProxy.guid = handler.guid =
						handler.guid || handlerProxy.guid || $.guid++;
				}
	
				var match = event.match( /^([\w:-]*)\s*(.*)$/ );
				var eventName = match[ 1 ] + instance.eventNamespace;
				var selector = match[ 2 ];
	
				if ( selector ) {
					delegateElement.on( eventName, selector, handlerProxy );
				} else {
					element.on( eventName, handlerProxy );
				}
			} );
		},
	
		_off: function( element, eventName ) {
			eventName = ( eventName || "" ).split( " " ).join( this.eventNamespace + " " ) +
				this.eventNamespace;
			element.off( eventName ).off( eventName );
	
			// Clear the stack to avoid memory leaks (#10056)
			this.bindings = $( this.bindings.not( element ).get() );
			this.focusable = $( this.focusable.not( element ).get() );
			this.hoverable = $( this.hoverable.not( element ).get() );
		},
	
		_delay: function( handler, delay ) {
			function handlerProxy() {
				return ( typeof handler === "string" ? instance[ handler ] : handler )
					.apply( instance, arguments );
			}
			var instance = this;
			return setTimeout( handlerProxy, delay || 0 );
		},
	
		_hoverable: function( element ) {
			this.hoverable = this.hoverable.add( element );
			this._on( element, {
				mouseenter: function( event ) {
					this._addClass( $( event.currentTarget ), null, "ui-state-hover" );
				},
				mouseleave: function( event ) {
					this._removeClass( $( event.currentTarget ), null, "ui-state-hover" );
				}
			} );
		},
	
		_focusable: function( element ) {
			this.focusable = this.focusable.add( element );
			this._on( element, {
				focusin: function( event ) {
					this._addClass( $( event.currentTarget ), null, "ui-state-focus" );
				},
				focusout: function( event ) {
					this._removeClass( $( event.currentTarget ), null, "ui-state-focus" );
				}
			} );
		},
	
		_trigger: function( type, event, data ) {
			var prop, orig;
			var callback = this.options[ type ];
	
			data = data || {};
			event = $.Event( event );
			event.type = ( type === this.widgetEventPrefix ?
				type :
				this.widgetEventPrefix + type ).toLowerCase();
	
			// The original event may come from any element
			// so we need to reset the target on the new event
			event.target = this.element[ 0 ];
	
			// Copy original event properties over to the new event
			orig = event.originalEvent;
			if ( orig ) {
				for ( prop in orig ) {
					if ( !( prop in event ) ) {
						event[ prop ] = orig[ prop ];
					}
				}
			}
	
			this.element.trigger( event, data );
			return !( $.isFunction( callback ) &&
				callback.apply( this.element[ 0 ], [ event ].concat( data ) ) === false ||
				event.isDefaultPrevented() );
		}
	};
	
	$.each( { show: "fadeIn", hide: "fadeOut" }, function( method, defaultEffect ) {
		$.Widget.prototype[ "_" + method ] = function( element, options, callback ) {
			if ( typeof options === "string" ) {
				options = { effect: options };
			}
	
			var hasOptions;
			var effectName = !options ?
				method :
				options === true || typeof options === "number" ?
					defaultEffect :
					options.effect || defaultEffect;
	
			options = options || {};
			if ( typeof options === "number" ) {
				options = { duration: options };
			}
	
			hasOptions = !$.isEmptyObject( options );
			options.complete = callback;
	
			if ( options.delay ) {
				element.delay( options.delay );
			}
	
			if ( hasOptions && $.effects && $.effects.effect[ effectName ] ) {
				element[ method ]( options );
			} else if ( effectName !== method && element[ effectName ] ) {
				element[ effectName ]( options.duration, options.easing, callback );
			} else {
				element.queue( function( next ) {
					$( this )[ method ]();
					if ( callback ) {
						callback.call( element[ 0 ] );
					}
					next();
				} );
			}
		};
	} );
	
	return $.widget;
	
	} ) );


/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(3) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	} ( function( $ ) {
	
	$.ui = $.ui || {};
	
	return $.ui.version = "1.12.1";
	
	} ) );


/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery UI :data 1.12.1
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 */
	
	//>>label: :data Selector
	//>>group: Core
	//>>description: Selects elements which have data stored under the specified key.
	//>>docs: http://api.jqueryui.com/data-selector/
	
	( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(3), __webpack_require__(10) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	} ( function( $ ) {
	return $.extend( $.expr[ ":" ], {
		data: $.expr.createPseudo ?
			$.expr.createPseudo( function( dataName ) {
				return function( elem ) {
					return !!$.data( elem, dataName );
				};
			} ) :
	
			// Support: jQuery <1.8
			function( elem, i, match ) {
				return !!$.data( elem, match[ 3 ] );
			}
	} );
	} ) );


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery UI Scroll Parent 1.12.1
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 */
	
	//>>label: scrollParent
	//>>group: Core
	//>>description: Get the closest ancestor element that is scrollable.
	//>>docs: http://api.jqueryui.com/scrollParent/
	
	( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(3), __webpack_require__(10) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	} ( function( $ ) {
	
	return $.fn.scrollParent = function( includeHidden ) {
		var position = this.css( "position" ),
			excludeStaticParent = position === "absolute",
			overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
			scrollParent = this.parents().filter( function() {
				var parent = $( this );
				if ( excludeStaticParent && parent.css( "position" ) === "static" ) {
					return false;
				}
				return overflowRegex.test( parent.css( "overflow" ) + parent.css( "overflow-y" ) +
					parent.css( "overflow-x" ) );
			} ).eq( 0 );
	
		return position === "fixed" || !scrollParent.length ?
			$( this[ 0 ].ownerDocument || document ) :
			scrollParent;
	};
	
	} ) );


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery UI Mouse 1.12.1
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 */
	
	//>>label: Mouse
	//>>group: Widgets
	//>>description: Abstracts mouse-based interactions to assist in creating certain widgets.
	//>>docs: http://api.jqueryui.com/mouse/
	
	( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
				__webpack_require__(3),
				__webpack_require__(14),
				__webpack_require__(10),
				__webpack_require__(9)
			], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	}( function( $ ) {
	
	var mouseHandled = false;
	$( document ).on( "mouseup", function() {
		mouseHandled = false;
	} );
	
	return $.widget( "ui.mouse", {
		version: "1.12.1",
		options: {
			cancel: "input, textarea, button, select, option",
			distance: 1,
			delay: 0
		},
		_mouseInit: function() {
			var that = this;
	
			this.element
				.on( "mousedown." + this.widgetName, function( event ) {
					return that._mouseDown( event );
				} )
				.on( "click." + this.widgetName, function( event ) {
					if ( true === $.data( event.target, that.widgetName + ".preventClickEvent" ) ) {
						$.removeData( event.target, that.widgetName + ".preventClickEvent" );
						event.stopImmediatePropagation();
						return false;
					}
				} );
	
			this.started = false;
		},
	
		// TODO: make sure destroying one instance of mouse doesn't mess with
		// other instances of mouse
		_mouseDestroy: function() {
			this.element.off( "." + this.widgetName );
			if ( this._mouseMoveDelegate ) {
				this.document
					.off( "mousemove." + this.widgetName, this._mouseMoveDelegate )
					.off( "mouseup." + this.widgetName, this._mouseUpDelegate );
			}
		},
	
		_mouseDown: function( event ) {
	
			// don't let more than one widget handle mouseStart
			if ( mouseHandled ) {
				return;
			}
	
			this._mouseMoved = false;
	
			// We may have missed mouseup (out of window)
			( this._mouseStarted && this._mouseUp( event ) );
	
			this._mouseDownEvent = event;
	
			var that = this,
				btnIsLeft = ( event.which === 1 ),
	
				// event.target.nodeName works around a bug in IE 8 with
				// disabled inputs (#7620)
				elIsCancel = ( typeof this.options.cancel === "string" && event.target.nodeName ?
					$( event.target ).closest( this.options.cancel ).length : false );
			if ( !btnIsLeft || elIsCancel || !this._mouseCapture( event ) ) {
				return true;
			}
	
			this.mouseDelayMet = !this.options.delay;
			if ( !this.mouseDelayMet ) {
				this._mouseDelayTimer = setTimeout( function() {
					that.mouseDelayMet = true;
				}, this.options.delay );
			}
	
			if ( this._mouseDistanceMet( event ) && this._mouseDelayMet( event ) ) {
				this._mouseStarted = ( this._mouseStart( event ) !== false );
				if ( !this._mouseStarted ) {
					event.preventDefault();
					return true;
				}
			}
	
			// Click event may never have fired (Gecko & Opera)
			if ( true === $.data( event.target, this.widgetName + ".preventClickEvent" ) ) {
				$.removeData( event.target, this.widgetName + ".preventClickEvent" );
			}
	
			// These delegates are required to keep context
			this._mouseMoveDelegate = function( event ) {
				return that._mouseMove( event );
			};
			this._mouseUpDelegate = function( event ) {
				return that._mouseUp( event );
			};
	
			this.document
				.on( "mousemove." + this.widgetName, this._mouseMoveDelegate )
				.on( "mouseup." + this.widgetName, this._mouseUpDelegate );
	
			event.preventDefault();
	
			mouseHandled = true;
			return true;
		},
	
		_mouseMove: function( event ) {
	
			// Only check for mouseups outside the document if you've moved inside the document
			// at least once. This prevents the firing of mouseup in the case of IE<9, which will
			// fire a mousemove event if content is placed under the cursor. See #7778
			// Support: IE <9
			if ( this._mouseMoved ) {
	
				// IE mouseup check - mouseup happened when mouse was out of window
				if ( $.ui.ie && ( !document.documentMode || document.documentMode < 9 ) &&
						!event.button ) {
					return this._mouseUp( event );
	
				// Iframe mouseup check - mouseup occurred in another document
				} else if ( !event.which ) {
	
					// Support: Safari <=8 - 9
					// Safari sets which to 0 if you press any of the following keys
					// during a drag (#14461)
					if ( event.originalEvent.altKey || event.originalEvent.ctrlKey ||
							event.originalEvent.metaKey || event.originalEvent.shiftKey ) {
						this.ignoreMissingWhich = true;
					} else if ( !this.ignoreMissingWhich ) {
						return this._mouseUp( event );
					}
				}
			}
	
			if ( event.which || event.button ) {
				this._mouseMoved = true;
			}
	
			if ( this._mouseStarted ) {
				this._mouseDrag( event );
				return event.preventDefault();
			}
	
			if ( this._mouseDistanceMet( event ) && this._mouseDelayMet( event ) ) {
				this._mouseStarted =
					( this._mouseStart( this._mouseDownEvent, event ) !== false );
				( this._mouseStarted ? this._mouseDrag( event ) : this._mouseUp( event ) );
			}
	
			return !this._mouseStarted;
		},
	
		_mouseUp: function( event ) {
			this.document
				.off( "mousemove." + this.widgetName, this._mouseMoveDelegate )
				.off( "mouseup." + this.widgetName, this._mouseUpDelegate );
	
			if ( this._mouseStarted ) {
				this._mouseStarted = false;
	
				if ( event.target === this._mouseDownEvent.target ) {
					$.data( event.target, this.widgetName + ".preventClickEvent", true );
				}
	
				this._mouseStop( event );
			}
	
			if ( this._mouseDelayTimer ) {
				clearTimeout( this._mouseDelayTimer );
				delete this._mouseDelayTimer;
			}
	
			this.ignoreMissingWhich = false;
			mouseHandled = false;
			event.preventDefault();
		},
	
		_mouseDistanceMet: function( event ) {
			return ( Math.max(
					Math.abs( this._mouseDownEvent.pageX - event.pageX ),
					Math.abs( this._mouseDownEvent.pageY - event.pageY )
				) >= this.options.distance
			);
		},
	
		_mouseDelayMet: function( /* event */ ) {
			return this.mouseDelayMet;
		},
	
		// These are placeholder methods, to be overriden by extending plugin
		_mouseStart: function( /* event */ ) {},
		_mouseDrag: function( /* event */ ) {},
		_mouseStop: function( /* event */ ) {},
		_mouseCapture: function( /* event */ ) { return true; }
	} );
	
	} ) );


/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;( function( factory ) {
		if ( true ) {
	
			// AMD. Register as an anonymous module.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(3), __webpack_require__(10) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else {
	
			// Browser globals
			factory( jQuery );
		}
	} ( function( $ ) {
	
	// This file is deprecated
	return $.ui.ie = !!/msie [\w.]+/.exec( navigator.userAgent.toLowerCase() );
	} ) );


/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["$"] = __webpack_require__(16);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["jQuery"] = __webpack_require__(3);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ })
/******/ ]);
//# sourceMappingURL=libs.js.map