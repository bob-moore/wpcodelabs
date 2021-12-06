/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/scripts/public.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/scripts/includes/jquery.jumpscroll.js":
/*!***************************************************!*\
  !*** ./src/scripts/includes/jquery.jumpscroll.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function ($) {\n  'use strict';\n\n  $.fn.JumpScroll = function (options) {\n    var defaults = {\n      scrollSpeed: 'progressive',\n      target: 'body',\n      topOffset: 0\n    };\n    options = $.extend({}, defaults, options);\n    return $.map(this, function (el) {\n      return new JumpScroll(el);\n    });\n\n    function JumpScroll(el) {\n      var $el,\n          $target,\n          settings = {};\n      /**\n       * cache DOM elements and options\n       * Set variables and assign DOM elements to reusable vars\n       * @return (null) blank return to maintain consitancy\n       */\n\n      var cacheDom = function cacheDom() {\n        $el = $(el);\n        $target = el.hash !== '' ? $(el.hash) : $(options.target);\n        $target = $target.length !== 0 ? $target : false;\n        settings = {\n          topOffset: $el.data('topOffset') || options.topOffset || 0,\n          scrollSpeed: getScrollSpeed()\n        };\n        return;\n      };\n      /**\n       * Binds events\n       * @return (null) blank return to maintain consistancy\n       */\n\n\n      var bindEvents = function bindEvents() {\n        $el.on('click', scroll);\n        return;\n      };\n      /**\n       * Get scroll speed option in this order:\n       * 1. If able calculate distance and use progressive speed\n       * 2. If elements has data-scrollSpeed set, use that.\n       * 3. Else, if scrollspeed option is set globally, use that.\n       * 4. Final fallback, just use 'slow'\n       * @return (int or string) Speed at which to scroll\n       */\n\n\n      var getScrollSpeed = function getScrollSpeed() {\n        if (options.speed === 'progressive' && $target !== false) {\n          return Math.abs(window.pageYOffset - $target.offset().top) / 2;\n        } else {\n          return $el.data('scrollSpeed') || options.speed || 'slow';\n        }\n      };\n      /**\n       * How to handle scroll events\n       * First, capture event to prevent page reloading\n       * Then, scroll page to specified point using options\n       * Last, blur the link focus and return self\n       * @param  (object) event : Event object\n       * @return ( object ) $el : return self for chaining\n       */\n\n\n      var scroll = function scroll(event) {\n        event.preventDefault();\n        $el.trigger('jumpscroll:start');\n        $('body, html').stop().animate({\n          scrollTop: $target.offset().top - parseInt(settings.topOffset)\n        }, settings.ScrollSpeed, function () {\n          $el.trigger('jumpscroll:done');\n          $el.blur();\n        });\n        return $el;\n      };\n      /**\n       * Anonomous init function\n       * Organize execution of module\n       * @type self invoking function to kickoff the module\n       */\n\n\n      (function () {\n        cacheDom();\n\n        if (settings.$target !== false) {\n          bindEvents();\n        }\n      })();\n\n      return $el;\n    }\n  };\n})(jQuery);\n\n//# sourceURL=webpack:///./src/scripts/includes/jquery.jumpscroll.js?");

/***/ }),

/***/ "./src/scripts/public.js":
/*!*******************************!*\
  !*** ./src/scripts/public.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// require( './vendors/jquery.waypoints.js' );\n__webpack_require__(/*! ./includes/jquery.jumpscroll.js */ \"./src/scripts/includes/jquery.jumpscroll.js\"); // require( './includes/jquery.scrollview.js' );\n// require( './includes/jquery.scrolltoggle.js' );\n\n\n(function ($) {\n  'use strict';\n\n  $.fn.WpImageAlignment = function (options) {\n    // Set the font size\n    var fontsize = parseFloat($('body').css('font-size'));\n    return $.map(this, function (el) {\n      return new ResponsiveAlignedImage($(el));\n    });\n\n    function ResponsiveAlignedImage($img) {\n      var $container, classList;\n\n      var _init = function _init() {\n        // Get the parent element that contains our image\n        $container = $img.closest('div'); // Save the original list of classes\n\n        classList = $img.attr('class'); // Bind some events\n\n        if ($container.length) {\n          // Check alignment and adjust if necessary\n          _alignImg(); // Check alignment on image resize\n\n\n          $(window).on('resize', _alignImg);\n        } // Return the image\n\n\n        return $img;\n      };\n\n      var _isTextSquashed = function _isTextSquashed() {\n        // Is there less than 10x font size available for text to wrap?\n        if ($container.width() - $img.width() < 10 * fontsize) {\n          return true;\n        }\n\n        return false;\n      };\n\n      var _isImageSquashed = function _isImageSquashed() {\n        // Is the image at least 1/2 of the container width?\n        if ($img.width() >= $container.width() / 2) {\n          return true;\n        }\n\n        return false;\n      };\n\n      var _alignImg = function _alignImg(event, data) {\n        // If squashed, let's unsquash it\n        if (_isImageSquashed() && _isTextSquashed()) {\n          $img.removeClass('alignleft alignright').addClass('aligncenter');\n        } // Else, let's restore it to it's default state\n        else {\n            $img.removeClass('aligncenter').addClass(classList);\n          }\n\n        return;\n      };\n\n      return _init();\n    }\n  };\n})(jQuery);\n\njQuery(function ($) {\n  'use strict'; // $( '.scrolltoggle' ).ScrollToggle();\n  // $( '.inview' ).ScrollView();\n\n  $('.jumpscroll').JumpScroll();\n  $('.entry-content img.alignleft, .entry-content img.alignright').WpImageAlignment();\n  $('.menu-item.noclick > a').on('click', function (e) {\n    e.preventDefault();\n  });\n  $.map($('a[href*=\"location=newtab\"]'), function (el) {\n    $(el).attr('target', '_blank').attr('rel', 'noreferrer noopener');\n  });\n});\n\n//# sourceURL=webpack:///./src/scripts/public.js?");

/***/ })

/******/ });
//# sourceMappingURL=public.js.map
