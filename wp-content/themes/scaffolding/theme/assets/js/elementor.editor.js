/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/arrive/src/arrive.js":
/*!*******************************************!*\
  !*** ./node_modules/arrive/src/arrive.js ***!
  \*******************************************/
/***/ (function() {

eval("/*globals jQuery,Window,HTMLElement,HTMLDocument,HTMLCollection,NodeList,MutationObserver */\n/*exported Arrive*/\n/*jshint latedef:false */\n\n/*\n * arrive.js\n * v2.4.1\n * https://github.com/uzairfarooq/arrive\n * MIT licensed\n *\n * Copyright (c) 2014-2017 Uzair Farooq\n */\nvar Arrive = (function(window, $, undefined) {\n\n  \"use strict\";\n\n  if(!window.MutationObserver || typeof HTMLElement === 'undefined'){\n    return; //for unsupported browsers\n  }\n\n  var arriveUniqueId = 0;\n\n  var utils = (function() {\n    var matches = HTMLElement.prototype.matches || HTMLElement.prototype.webkitMatchesSelector || HTMLElement.prototype.mozMatchesSelector\n                  || HTMLElement.prototype.msMatchesSelector;\n\n    return {\n      matchesSelector: function(elem, selector) {\n        return elem instanceof HTMLElement && matches.call(elem, selector);\n      },\n      // to enable function overloading - By John Resig (MIT Licensed)\n      addMethod: function (object, name, fn) {\n        var old = object[ name ];\n        object[ name ] = function(){\n          if ( fn.length == arguments.length ) {\n            return fn.apply( this, arguments );\n          }\n          else if ( typeof old == 'function' ) {\n            return old.apply( this, arguments );\n          }\n        };\n      },\n      callCallbacks: function(callbacksToBeCalled, registrationData) {\n        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {\n          // as onlyOnce param is true, make sure we fire the event for only one item\n          callbacksToBeCalled = [callbacksToBeCalled[0]];\n        }\n\n        for (var i = 0, cb; (cb = callbacksToBeCalled[i]); i++) {\n          if (cb && cb.callback) {\n            cb.callback.call(cb.elem, cb.elem);\n          }\n        }\n\n        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {\n          // unbind event after first callback as onceOnly is true.\n          registrationData.me.unbindEventWithSelectorAndCallback.call(\n            registrationData.target, registrationData.selector, registrationData.callback);\n        }\n      },\n      // traverse through all descendants of a node to check if event should be fired for any descendant\n      checkChildNodesRecursively: function(nodes, registrationData, matchFunc, callbacksToBeCalled) {\n        // check each new node if it matches the selector\n        for (var i=0, node; (node = nodes[i]); i++) {\n          if (matchFunc(node, registrationData, callbacksToBeCalled)) {\n            callbacksToBeCalled.push({ callback: registrationData.callback, elem: node });\n          }\n\n          if (node.childNodes.length > 0) {\n            utils.checkChildNodesRecursively(node.childNodes, registrationData, matchFunc, callbacksToBeCalled);\n          }\n        }\n      },\n      mergeArrays: function(firstArr, secondArr){\n        // Overwrites default options with user-defined options.\n        var options = {},\n            attrName;\n        for (attrName in firstArr) {\n          if (firstArr.hasOwnProperty(attrName)) {\n            options[attrName] = firstArr[attrName];\n          }\n        }\n        for (attrName in secondArr) {\n          if (secondArr.hasOwnProperty(attrName)) {\n            options[attrName] = secondArr[attrName];\n          }\n        }\n        return options;\n      },\n      toElementsArray: function (elements) {\n        // check if object is an array (or array like object)\n        // Note: window object has .length property but it's not array of elements so don't consider it an array\n        if (typeof elements !== \"undefined\" && (typeof elements.length !== \"number\" || elements === window)) {\n          elements = [elements];\n        }\n        return elements;\n      }\n    };\n  })();\n\n\n  // Class to maintain state of all registered events of a single type\n  var EventsBucket = (function() {\n    var EventsBucket = function() {\n      // holds all the events\n\n      this._eventsBucket    = [];\n      // function to be called while adding an event, the function should do the event initialization/registration\n      this._beforeAdding    = null;\n      // function to be called while removing an event, the function should do the event destruction\n      this._beforeRemoving  = null;\n    };\n\n    EventsBucket.prototype.addEvent = function(target, selector, options, callback) {\n      var newEvent = {\n        target:             target,\n        selector:           selector,\n        options:            options,\n        callback:           callback,\n        firedElems:         []\n      };\n\n      if (this._beforeAdding) {\n        this._beforeAdding(newEvent);\n      }\n\n      this._eventsBucket.push(newEvent);\n      return newEvent;\n    };\n\n    EventsBucket.prototype.removeEvent = function(compareFunction) {\n      for (var i=this._eventsBucket.length - 1, registeredEvent; (registeredEvent = this._eventsBucket[i]); i--) {\n        if (compareFunction(registeredEvent)) {\n          if (this._beforeRemoving) {\n              this._beforeRemoving(registeredEvent);\n          }\n\n          // mark callback as null so that even if an event mutation was already triggered it does not call callback\n          var removedEvents = this._eventsBucket.splice(i, 1);\n          if (removedEvents && removedEvents.length) {\n            removedEvents[0].callback = null;\n          }\n        }\n      }\n    };\n\n    EventsBucket.prototype.beforeAdding = function(beforeAdding) {\n      this._beforeAdding = beforeAdding;\n    };\n\n    EventsBucket.prototype.beforeRemoving = function(beforeRemoving) {\n      this._beforeRemoving = beforeRemoving;\n    };\n\n    return EventsBucket;\n  })();\n\n\n  /**\n   * @constructor\n   * General class for binding/unbinding arrive and leave events\n   */\n  var MutationEvents = function(getObserverConfig, onMutation) {\n    var eventsBucket    = new EventsBucket(),\n        me              = this;\n\n    var defaultOptions = {\n      fireOnAttributesModification: false\n    };\n\n    // actual event registration before adding it to bucket\n    eventsBucket.beforeAdding(function(registrationData) {\n      var\n        target    = registrationData.target,\n        observer;\n\n      // mutation observer does not work on window or document\n      if (target === window.document || target === window) {\n        target = document.getElementsByTagName(\"html\")[0];\n      }\n\n      // Create an observer instance\n      observer = new MutationObserver(function(e) {\n        onMutation.call(this, e, registrationData);\n      });\n\n      var config = getObserverConfig(registrationData.options);\n\n      observer.observe(target, config);\n\n      registrationData.observer = observer;\n      registrationData.me = me;\n    });\n\n    // cleanup/unregister before removing an event\n    eventsBucket.beforeRemoving(function (eventData) {\n      eventData.observer.disconnect();\n    });\n\n    this.bindEvent = function(selector, options, callback) {\n      options = utils.mergeArrays(defaultOptions, options);\n\n      var elements = utils.toElementsArray(this);\n\n      for (var i = 0; i < elements.length; i++) {\n        eventsBucket.addEvent(elements[i], selector, options, callback);\n      }\n    };\n\n    this.unbindEvent = function() {\n      var elements = utils.toElementsArray(this);\n      eventsBucket.removeEvent(function(eventObj) {\n        for (var i = 0; i < elements.length; i++) {\n          if (this === undefined || eventObj.target === elements[i]) {\n            return true;\n          }\n        }\n        return false;\n      });\n    };\n\n    this.unbindEventWithSelectorOrCallback = function(selector) {\n      var elements = utils.toElementsArray(this),\n          callback = selector,\n          compareFunction;\n\n      if (typeof selector === \"function\") {\n        compareFunction = function(eventObj) {\n          for (var i = 0; i < elements.length; i++) {\n            if ((this === undefined || eventObj.target === elements[i]) && eventObj.callback === callback) {\n              return true;\n            }\n          }\n          return false;\n        };\n      }\n      else {\n        compareFunction = function(eventObj) {\n          for (var i = 0; i < elements.length; i++) {\n            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector) {\n              return true;\n            }\n          }\n          return false;\n        };\n      }\n      eventsBucket.removeEvent(compareFunction);\n    };\n\n    this.unbindEventWithSelectorAndCallback = function(selector, callback) {\n      var elements = utils.toElementsArray(this);\n      eventsBucket.removeEvent(function(eventObj) {\n          for (var i = 0; i < elements.length; i++) {\n            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector && eventObj.callback === callback) {\n              return true;\n            }\n          }\n          return false;\n      });\n    };\n\n    return this;\n  };\n\n\n  /**\n   * @constructor\n   * Processes 'arrive' events\n   */\n  var ArriveEvents = function() {\n    // Default options for 'arrive' event\n    var arriveDefaultOptions = {\n      fireOnAttributesModification: false,\n      onceOnly: false,\n      existing: false\n    };\n\n    function getArriveObserverConfig(options) {\n      var config = {\n        attributes: false,\n        childList: true,\n        subtree: true\n      };\n\n      if (options.fireOnAttributesModification) {\n        config.attributes = true;\n      }\n\n      return config;\n    }\n\n    function onArriveMutation(mutations, registrationData) {\n      mutations.forEach(function( mutation ) {\n        var newNodes    = mutation.addedNodes,\n            targetNode = mutation.target,\n            callbacksToBeCalled = [],\n            node;\n\n        // If new nodes are added\n        if( newNodes !== null && newNodes.length > 0 ) {\n          utils.checkChildNodesRecursively(newNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);\n        }\n        else if (mutation.type === \"attributes\") {\n          if (nodeMatchFunc(targetNode, registrationData, callbacksToBeCalled)) {\n            callbacksToBeCalled.push({ callback: registrationData.callback, elem: targetNode });\n          }\n        }\n\n        utils.callCallbacks(callbacksToBeCalled, registrationData);\n      });\n    }\n\n    function nodeMatchFunc(node, registrationData, callbacksToBeCalled) {\n      // check a single node to see if it matches the selector\n      if (utils.matchesSelector(node, registrationData.selector)) {\n        if(node._id === undefined) {\n          node._id = arriveUniqueId++;\n        }\n        // make sure the arrive event is not already fired for the element\n        if (registrationData.firedElems.indexOf(node._id) == -1) {\n          registrationData.firedElems.push(node._id);\n\n          return true;\n        }\n      }\n\n      return false;\n    }\n\n    arriveEvents = new MutationEvents(getArriveObserverConfig, onArriveMutation);\n\n    var mutationBindEvent = arriveEvents.bindEvent;\n\n    // override bindEvent function\n    arriveEvents.bindEvent = function(selector, options, callback) {\n\n      if (typeof callback === \"undefined\") {\n        callback = options;\n        options = arriveDefaultOptions;\n      } else {\n        options = utils.mergeArrays(arriveDefaultOptions, options);\n      }\n\n      var elements = utils.toElementsArray(this);\n\n      if (options.existing) {\n        var existing = [];\n\n        for (var i = 0; i < elements.length; i++) {\n          var nodes = elements[i].querySelectorAll(selector);\n          for (var j = 0; j < nodes.length; j++) {\n            existing.push({ callback: callback, elem: nodes[j] });\n          }\n        }\n\n        // no need to bind event if the callback has to be fired only once and we have already found the element\n        if (options.onceOnly && existing.length) {\n          return callback.call(existing[0].elem, existing[0].elem);\n        }\n\n        setTimeout(utils.callCallbacks, 1, existing);\n      }\n\n      mutationBindEvent.call(this, selector, options, callback);\n    };\n\n    return arriveEvents;\n  };\n\n\n  /**\n   * @constructor\n   * Processes 'leave' events\n   */\n  var LeaveEvents = function() {\n    // Default options for 'leave' event\n    var leaveDefaultOptions = {};\n\n    function getLeaveObserverConfig() {\n      var config = {\n        childList: true,\n        subtree: true\n      };\n\n      return config;\n    }\n\n    function onLeaveMutation(mutations, registrationData) {\n      mutations.forEach(function( mutation ) {\n        var removedNodes  = mutation.removedNodes,\n            callbacksToBeCalled = [];\n\n        if( removedNodes !== null && removedNodes.length > 0 ) {\n          utils.checkChildNodesRecursively(removedNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);\n        }\n\n        utils.callCallbacks(callbacksToBeCalled, registrationData);\n      });\n    }\n\n    function nodeMatchFunc(node, registrationData) {\n      return utils.matchesSelector(node, registrationData.selector);\n    }\n\n    leaveEvents = new MutationEvents(getLeaveObserverConfig, onLeaveMutation);\n\n    var mutationBindEvent = leaveEvents.bindEvent;\n\n    // override bindEvent function\n    leaveEvents.bindEvent = function(selector, options, callback) {\n\n      if (typeof callback === \"undefined\") {\n        callback = options;\n        options = leaveDefaultOptions;\n      } else {\n        options = utils.mergeArrays(leaveDefaultOptions, options);\n      }\n\n      mutationBindEvent.call(this, selector, options, callback);\n    };\n\n    return leaveEvents;\n  };\n\n\n  var arriveEvents = new ArriveEvents(),\n      leaveEvents  = new LeaveEvents();\n\n  function exposeUnbindApi(eventObj, exposeTo, funcName) {\n    // expose unbind function with function overriding\n    utils.addMethod(exposeTo, funcName, eventObj.unbindEvent);\n    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorOrCallback);\n    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorAndCallback);\n  }\n\n  /*** expose APIs ***/\n  function exposeApi(exposeTo) {\n    exposeTo.arrive = arriveEvents.bindEvent;\n    exposeUnbindApi(arriveEvents, exposeTo, \"unbindArrive\");\n\n    exposeTo.leave = leaveEvents.bindEvent;\n    exposeUnbindApi(leaveEvents, exposeTo, \"unbindLeave\");\n  }\n\n  if ($) {\n    exposeApi($.fn);\n  }\n  exposeApi(HTMLElement.prototype);\n  exposeApi(NodeList.prototype);\n  exposeApi(HTMLCollection.prototype);\n  exposeApi(HTMLDocument.prototype);\n  exposeApi(Window.prototype);\n\n  var Arrive = {};\n  // expose functions to unbind all arrive/leave events\n  exposeUnbindApi(arriveEvents, Arrive, \"unbindAllArrive\");\n  exposeUnbindApi(leaveEvents, Arrive, \"unbindAllLeave\");\n\n  return Arrive;\n\n})(window, typeof jQuery === 'undefined' ? null : jQuery, undefined);\n\n//# sourceURL=webpack://scaffolding/./node_modules/arrive/src/arrive.js?");

/***/ }),

/***/ "./src/scripts/elementor.editor.js":
/*!*****************************************!*\
  !*** ./src/scripts/elementor.editor.js ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

eval("jQuery(function ($) {\n  'use strict';\n\n  __webpack_require__(/*! arrive */ \"./node_modules/arrive/src/arrive.js\");\n\n  var _removeFatalError = function _removeFatalError(dialog) {\n    /**\n     * Hide the dialog\n     */\n    $(dialog).hide();\n    /**\n     * Remove the listener, so we don't catch all errors\n     */\n\n    $(document).unbindArrive('#elementor-fatal-error-dialog', _removeFatalError);\n  };\n\n  var _refreshPreview = function _refreshPreview(event) {\n    /**\n     * Make sure elementor exists\n     */\n    if (typeof elementor === 'undefined') {\n      return false;\n    }\n    /**\n     * Make sure it's the correct post message\n     */\n\n\n    if (event.originalEvent.data.caller !== 'theme_hook' || event.originalEvent.data.action !== 'error') {\n      return false;\n    }\n    /**\n     * Attach event listener\n     */\n\n\n    $(document).arrive('#elementor-fatal-error-dialog', _removeFatalError);\n    /**\n     * Reload the preview\n     */\n\n    elementor.channels.editor.trigger('elementorThemeBuilder:ApplyPreview');\n  };\n\n  $(window).on('message', _refreshPreview);\n});\n\n//# sourceURL=webpack://scaffolding/./src/scripts/elementor.editor.js?");

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
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./src/scripts/elementor.editor.js");
/******/ 	
/******/ })()
;