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
	
	__webpack_require__(78);

/***/ }),
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var $at = __webpack_require__(7)(true);
	
	// 21.1.3.27 String.prototype[@@iterator]()
	__webpack_require__(10)(String, 'String', function (iterated) {
	  this._t = String(iterated); // target
	  this._i = 0;                // next index
	// 21.1.5.2.1 %StringIteratorPrototype%.next()
	}, function () {
	  var O = this._t;
	  var index = this._i;
	  var point;
	  if (index >= O.length) return { value: undefined, done: true };
	  point = $at(O, index);
	  this._i += point.length;
	  return { value: point, done: false };
	});


/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

	var toInteger = __webpack_require__(8);
	var defined = __webpack_require__(9);
	// true  -> String#at
	// false -> String#codePointAt
	module.exports = function (TO_STRING) {
	  return function (that, pos) {
	    var s = String(defined(that));
	    var i = toInteger(pos);
	    var l = s.length;
	    var a, b;
	    if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
	    a = s.charCodeAt(i);
	    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
	      ? TO_STRING ? s.charAt(i) : a
	      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
	  };
	};


/***/ }),
/* 8 */
/***/ (function(module, exports) {

	// 7.1.4 ToInteger
	var ceil = Math.ceil;
	var floor = Math.floor;
	module.exports = function (it) {
	  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
	};


/***/ }),
/* 9 */
/***/ (function(module, exports) {

	// 7.2.1 RequireObjectCoercible(argument)
	module.exports = function (it) {
	  if (it == undefined) throw TypeError("Can't call method on  " + it);
	  return it;
	};


/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var LIBRARY = __webpack_require__(11);
	var $export = __webpack_require__(12);
	var redefine = __webpack_require__(27);
	var hide = __webpack_require__(17);
	var has = __webpack_require__(28);
	var Iterators = __webpack_require__(29);
	var $iterCreate = __webpack_require__(30);
	var setToStringTag = __webpack_require__(46);
	var getPrototypeOf = __webpack_require__(48);
	var ITERATOR = __webpack_require__(47)('iterator');
	var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
	var FF_ITERATOR = '@@iterator';
	var KEYS = 'keys';
	var VALUES = 'values';
	
	var returnThis = function () { return this; };
	
	module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
	  $iterCreate(Constructor, NAME, next);
	  var getMethod = function (kind) {
	    if (!BUGGY && kind in proto) return proto[kind];
	    switch (kind) {
	      case KEYS: return function keys() { return new Constructor(this, kind); };
	      case VALUES: return function values() { return new Constructor(this, kind); };
	    } return function entries() { return new Constructor(this, kind); };
	  };
	  var TAG = NAME + ' Iterator';
	  var DEF_VALUES = DEFAULT == VALUES;
	  var VALUES_BUG = false;
	  var proto = Base.prototype;
	  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
	  var $default = $native || getMethod(DEFAULT);
	  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
	  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
	  var methods, key, IteratorPrototype;
	  // Fix native
	  if ($anyNative) {
	    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
	    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
	      // Set @@toStringTag to native iterators
	      setToStringTag(IteratorPrototype, TAG, true);
	      // fix for some old engines
	      if (!LIBRARY && !has(IteratorPrototype, ITERATOR)) hide(IteratorPrototype, ITERATOR, returnThis);
	    }
	  }
	  // fix Array#{values, @@iterator}.name in V8 / FF
	  if (DEF_VALUES && $native && $native.name !== VALUES) {
	    VALUES_BUG = true;
	    $default = function values() { return $native.call(this); };
	  }
	  // Define iterator
	  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
	    hide(proto, ITERATOR, $default);
	  }
	  // Plug for library
	  Iterators[NAME] = $default;
	  Iterators[TAG] = returnThis;
	  if (DEFAULT) {
	    methods = {
	      values: DEF_VALUES ? $default : getMethod(VALUES),
	      keys: IS_SET ? $default : getMethod(KEYS),
	      entries: $entries
	    };
	    if (FORCED) for (key in methods) {
	      if (!(key in proto)) redefine(proto, key, methods[key]);
	    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
	  }
	  return methods;
	};


/***/ }),
/* 11 */
/***/ (function(module, exports) {

	module.exports = true;


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

	var global = __webpack_require__(13);
	var core = __webpack_require__(14);
	var ctx = __webpack_require__(15);
	var hide = __webpack_require__(17);
	var PROTOTYPE = 'prototype';
	
	var $export = function (type, name, source) {
	  var IS_FORCED = type & $export.F;
	  var IS_GLOBAL = type & $export.G;
	  var IS_STATIC = type & $export.S;
	  var IS_PROTO = type & $export.P;
	  var IS_BIND = type & $export.B;
	  var IS_WRAP = type & $export.W;
	  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
	  var expProto = exports[PROTOTYPE];
	  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE];
	  var key, own, out;
	  if (IS_GLOBAL) source = name;
	  for (key in source) {
	    // contains in native
	    own = !IS_FORCED && target && target[key] !== undefined;
	    if (own && key in exports) continue;
	    // export native or passed
	    out = own ? target[key] : source[key];
	    // prevent global pollution for namespaces
	    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
	    // bind timers to global for call from export context
	    : IS_BIND && own ? ctx(out, global)
	    // wrap global constructors for prevent change them in library
	    : IS_WRAP && target[key] == out ? (function (C) {
	      var F = function (a, b, c) {
	        if (this instanceof C) {
	          switch (arguments.length) {
	            case 0: return new C();
	            case 1: return new C(a);
	            case 2: return new C(a, b);
	          } return new C(a, b, c);
	        } return C.apply(this, arguments);
	      };
	      F[PROTOTYPE] = C[PROTOTYPE];
	      return F;
	    // make static versions for prototype methods
	    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
	    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
	    if (IS_PROTO) {
	      (exports.virtual || (exports.virtual = {}))[key] = out;
	      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
	      if (type & $export.R && expProto && !expProto[key]) hide(expProto, key, out);
	    }
	  }
	};
	// type bitmap
	$export.F = 1;   // forced
	$export.G = 2;   // global
	$export.S = 4;   // static
	$export.P = 8;   // proto
	$export.B = 16;  // bind
	$export.W = 32;  // wrap
	$export.U = 64;  // safe
	$export.R = 128; // real proto method for `library`
	module.exports = $export;


/***/ }),
/* 13 */
/***/ (function(module, exports) {

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global = module.exports = typeof window != 'undefined' && window.Math == Math
	  ? window : typeof self != 'undefined' && self.Math == Math ? self
	  // eslint-disable-next-line no-new-func
	  : Function('return this')();
	if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),
/* 14 */
/***/ (function(module, exports) {

	var core = module.exports = { version: '2.5.1' };
	if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

	// optional / simple context binding
	var aFunction = __webpack_require__(16);
	module.exports = function (fn, that, length) {
	  aFunction(fn);
	  if (that === undefined) return fn;
	  switch (length) {
	    case 1: return function (a) {
	      return fn.call(that, a);
	    };
	    case 2: return function (a, b) {
	      return fn.call(that, a, b);
	    };
	    case 3: return function (a, b, c) {
	      return fn.call(that, a, b, c);
	    };
	  }
	  return function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};


/***/ }),
/* 16 */
/***/ (function(module, exports) {

	module.exports = function (it) {
	  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
	  return it;
	};


/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

	var dP = __webpack_require__(18);
	var createDesc = __webpack_require__(26);
	module.exports = __webpack_require__(22) ? function (object, key, value) {
	  return dP.f(object, key, createDesc(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};


/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

	var anObject = __webpack_require__(19);
	var IE8_DOM_DEFINE = __webpack_require__(21);
	var toPrimitive = __webpack_require__(25);
	var dP = Object.defineProperty;
	
	exports.f = __webpack_require__(22) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
	  anObject(O);
	  P = toPrimitive(P, true);
	  anObject(Attributes);
	  if (IE8_DOM_DEFINE) try {
	    return dP(O, P, Attributes);
	  } catch (e) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};


/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(20);
	module.exports = function (it) {
	  if (!isObject(it)) throw TypeError(it + ' is not an object!');
	  return it;
	};


/***/ }),
/* 20 */
/***/ (function(module, exports) {

	module.exports = function (it) {
	  return typeof it === 'object' ? it !== null : typeof it === 'function';
	};


/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = !__webpack_require__(22) && !__webpack_require__(23)(function () {
	  return Object.defineProperty(__webpack_require__(24)('div'), 'a', { get: function () { return 7; } }).a != 7;
	});


/***/ }),
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

	// Thank's IE8 for his funny defineProperty
	module.exports = !__webpack_require__(23)(function () {
	  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
	});


/***/ }),
/* 23 */
/***/ (function(module, exports) {

	module.exports = function (exec) {
	  try {
	    return !!exec();
	  } catch (e) {
	    return true;
	  }
	};


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(20);
	var document = __webpack_require__(13).document;
	// typeof document.createElement is 'object' in old IE
	var is = isObject(document) && isObject(document.createElement);
	module.exports = function (it) {
	  return is ? document.createElement(it) : {};
	};


/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

	// 7.1.1 ToPrimitive(input [, PreferredType])
	var isObject = __webpack_require__(20);
	// instead of the ES6 spec version, we didn't implement @@toPrimitive case
	// and the second argument - flag - preferred type is a string
	module.exports = function (it, S) {
	  if (!isObject(it)) return it;
	  var fn, val;
	  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
	  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
	  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
	  throw TypeError("Can't convert object to primitive value");
	};


/***/ }),
/* 26 */
/***/ (function(module, exports) {

	module.exports = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};


/***/ }),
/* 27 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(17);


/***/ }),
/* 28 */
/***/ (function(module, exports) {

	var hasOwnProperty = {}.hasOwnProperty;
	module.exports = function (it, key) {
	  return hasOwnProperty.call(it, key);
	};


/***/ }),
/* 29 */
/***/ (function(module, exports) {

	module.exports = {};


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var create = __webpack_require__(31);
	var descriptor = __webpack_require__(26);
	var setToStringTag = __webpack_require__(46);
	var IteratorPrototype = {};
	
	// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
	__webpack_require__(17)(IteratorPrototype, __webpack_require__(47)('iterator'), function () { return this; });
	
	module.exports = function (Constructor, NAME, next) {
	  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
	  setToStringTag(Constructor, NAME + ' Iterator');
	};


/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

	// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
	var anObject = __webpack_require__(19);
	var dPs = __webpack_require__(32);
	var enumBugKeys = __webpack_require__(44);
	var IE_PROTO = __webpack_require__(41)('IE_PROTO');
	var Empty = function () { /* empty */ };
	var PROTOTYPE = 'prototype';
	
	// Create object with fake `null` prototype: use iframe Object with cleared prototype
	var createDict = function () {
	  // Thrash, waste and sodomy: IE GC bug
	  var iframe = __webpack_require__(24)('iframe');
	  var i = enumBugKeys.length;
	  var lt = '<';
	  var gt = '>';
	  var iframeDocument;
	  iframe.style.display = 'none';
	  __webpack_require__(45).appendChild(iframe);
	  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
	  // createDict = iframe.contentWindow.Object;
	  // html.removeChild(iframe);
	  iframeDocument = iframe.contentWindow.document;
	  iframeDocument.open();
	  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
	  iframeDocument.close();
	  createDict = iframeDocument.F;
	  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
	  return createDict();
	};
	
	module.exports = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    Empty[PROTOTYPE] = anObject(O);
	    result = new Empty();
	    Empty[PROTOTYPE] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO] = O;
	  } else result = createDict();
	  return Properties === undefined ? result : dPs(result, Properties);
	};


/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

	var dP = __webpack_require__(18);
	var anObject = __webpack_require__(19);
	var getKeys = __webpack_require__(33);
	
	module.exports = __webpack_require__(22) ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject(O);
	  var keys = getKeys(Properties);
	  var length = keys.length;
	  var i = 0;
	  var P;
	  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
	  return O;
	};


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

	// 19.1.2.14 / 15.2.3.14 Object.keys(O)
	var $keys = __webpack_require__(34);
	var enumBugKeys = __webpack_require__(44);
	
	module.exports = Object.keys || function keys(O) {
	  return $keys(O, enumBugKeys);
	};


/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

	var has = __webpack_require__(28);
	var toIObject = __webpack_require__(35);
	var arrayIndexOf = __webpack_require__(38)(false);
	var IE_PROTO = __webpack_require__(41)('IE_PROTO');
	
	module.exports = function (object, names) {
	  var O = toIObject(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (has(O, key = names[i++])) {
	    ~arrayIndexOf(result, key) || result.push(key);
	  }
	  return result;
	};


/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

	// to indexed object, toObject with fallback for non-array-like ES3 strings
	var IObject = __webpack_require__(36);
	var defined = __webpack_require__(9);
	module.exports = function (it) {
	  return IObject(defined(it));
	};


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var cof = __webpack_require__(37);
	// eslint-disable-next-line no-prototype-builtins
	module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
	  return cof(it) == 'String' ? it.split('') : Object(it);
	};


/***/ }),
/* 37 */
/***/ (function(module, exports) {

	var toString = {}.toString;
	
	module.exports = function (it) {
	  return toString.call(it).slice(8, -1);
	};


/***/ }),
/* 38 */
/***/ (function(module, exports, __webpack_require__) {

	// false -> Array#indexOf
	// true  -> Array#includes
	var toIObject = __webpack_require__(35);
	var toLength = __webpack_require__(39);
	var toAbsoluteIndex = __webpack_require__(40);
	module.exports = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIObject($this);
	    var length = toLength(O.length);
	    var index = toAbsoluteIndex(fromIndex, length);
	    var value;
	    // Array#includes uses SameValueZero equality algorithm
	    // eslint-disable-next-line no-self-compare
	    if (IS_INCLUDES && el != el) while (length > index) {
	      value = O[index++];
	      // eslint-disable-next-line no-self-compare
	      if (value != value) return true;
	    // Array#indexOf ignores holes, Array#includes - not
	    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
	      if (O[index] === el) return IS_INCLUDES || index || 0;
	    } return !IS_INCLUDES && -1;
	  };
	};


/***/ }),
/* 39 */
/***/ (function(module, exports, __webpack_require__) {

	// 7.1.15 ToLength
	var toInteger = __webpack_require__(8);
	var min = Math.min;
	module.exports = function (it) {
	  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
	};


/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

	var toInteger = __webpack_require__(8);
	var max = Math.max;
	var min = Math.min;
	module.exports = function (index, length) {
	  index = toInteger(index);
	  return index < 0 ? max(index + length, 0) : min(index, length);
	};


/***/ }),
/* 41 */
/***/ (function(module, exports, __webpack_require__) {

	var shared = __webpack_require__(42)('keys');
	var uid = __webpack_require__(43);
	module.exports = function (key) {
	  return shared[key] || (shared[key] = uid(key));
	};


/***/ }),
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

	var global = __webpack_require__(13);
	var SHARED = '__core-js_shared__';
	var store = global[SHARED] || (global[SHARED] = {});
	module.exports = function (key) {
	  return store[key] || (store[key] = {});
	};


/***/ }),
/* 43 */
/***/ (function(module, exports) {

	var id = 0;
	var px = Math.random();
	module.exports = function (key) {
	  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
	};


/***/ }),
/* 44 */
/***/ (function(module, exports) {

	// IE 8- don't enum bug keys
	module.exports = (
	  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
	).split(',');


/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

	var document = __webpack_require__(13).document;
	module.exports = document && document.documentElement;


/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

	var def = __webpack_require__(18).f;
	var has = __webpack_require__(28);
	var TAG = __webpack_require__(47)('toStringTag');
	
	module.exports = function (it, tag, stat) {
	  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
	};


/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

	var store = __webpack_require__(42)('wks');
	var uid = __webpack_require__(43);
	var Symbol = __webpack_require__(13).Symbol;
	var USE_SYMBOL = typeof Symbol == 'function';
	
	var $exports = module.exports = function (name) {
	  return store[name] || (store[name] =
	    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
	};
	
	$exports.store = store;


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

	// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
	var has = __webpack_require__(28);
	var toObject = __webpack_require__(49);
	var IE_PROTO = __webpack_require__(41)('IE_PROTO');
	var ObjectProto = Object.prototype;
	
	module.exports = Object.getPrototypeOf || function (O) {
	  O = toObject(O);
	  if (has(O, IE_PROTO)) return O[IE_PROTO];
	  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
	    return O.constructor.prototype;
	  } return O instanceof Object ? ObjectProto : null;
	};


/***/ }),
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

	// 7.1.13 ToObject(argument)
	var defined = __webpack_require__(9);
	module.exports = function (it) {
	  return Object(defined(it));
	};


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

	__webpack_require__(51);
	var global = __webpack_require__(13);
	var hide = __webpack_require__(17);
	var Iterators = __webpack_require__(29);
	var TO_STRING_TAG = __webpack_require__(47)('toStringTag');
	
	var DOMIterables = ('CSSRuleList,CSSStyleDeclaration,CSSValueList,ClientRectList,DOMRectList,DOMStringList,' +
	  'DOMTokenList,DataTransferItemList,FileList,HTMLAllCollection,HTMLCollection,HTMLFormElement,HTMLSelectElement,' +
	  'MediaList,MimeTypeArray,NamedNodeMap,NodeList,PaintRequestList,Plugin,PluginArray,SVGLengthList,SVGNumberList,' +
	  'SVGPathSegList,SVGPointList,SVGStringList,SVGTransformList,SourceBufferList,StyleSheetList,TextTrackCueList,' +
	  'TextTrackList,TouchList').split(',');
	
	for (var i = 0; i < DOMIterables.length; i++) {
	  var NAME = DOMIterables[i];
	  var Collection = global[NAME];
	  var proto = Collection && Collection.prototype;
	  if (proto && !proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
	  Iterators[NAME] = Iterators.Array;
	}


/***/ }),
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var addToUnscopables = __webpack_require__(52);
	var step = __webpack_require__(53);
	var Iterators = __webpack_require__(29);
	var toIObject = __webpack_require__(35);
	
	// 22.1.3.4 Array.prototype.entries()
	// 22.1.3.13 Array.prototype.keys()
	// 22.1.3.29 Array.prototype.values()
	// 22.1.3.30 Array.prototype[@@iterator]()
	module.exports = __webpack_require__(10)(Array, 'Array', function (iterated, kind) {
	  this._t = toIObject(iterated); // target
	  this._i = 0;                   // next index
	  this._k = kind;                // kind
	// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
	}, function () {
	  var O = this._t;
	  var kind = this._k;
	  var index = this._i++;
	  if (!O || index >= O.length) {
	    this._t = undefined;
	    return step(1);
	  }
	  if (kind == 'keys') return step(0, index);
	  if (kind == 'values') return step(0, O[index]);
	  return step(0, [index, O[index]]);
	}, 'values');
	
	// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
	Iterators.Arguments = Iterators.Array;
	
	addToUnscopables('keys');
	addToUnscopables('values');
	addToUnscopables('entries');


/***/ }),
/* 52 */
/***/ (function(module, exports) {

	module.exports = function () { /* empty */ };


/***/ }),
/* 53 */
/***/ (function(module, exports) {

	module.exports = function (done, value) {
	  return { value: value, done: !!done };
	};


/***/ }),
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */
/***/ (function(module, exports) {

	exports.f = Object.getOwnPropertySymbols;


/***/ }),
/* 62 */
/***/ (function(module, exports) {

	exports.f = {}.propertyIsEnumerable;


/***/ }),
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */,
/* 74 */,
/* 75 */,
/* 76 */,
/* 77 */,
/* 78 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	var _classCallCheck2 = __webpack_require__(79);
	
	var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);
	
	var _createClass2 = __webpack_require__(80);
	
	var _createClass3 = _interopRequireDefault(_createClass2);
	
	var _sendform = __webpack_require__(84);
	
	var _getToken = __webpack_require__(104);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	$(document).ready(function () {
	    var projectApp = new App();
	    projectApp.init();
	});
	
	var App = function () {
	    function App() {
	        (0, _classCallCheck3.default)(this, App);
	    }
	
	    (0, _createClass3.default)(App, [{
	        key: 'init',
	        value: function init() {
	            if ($('#modal-digital-package').length) {
	                $.magnificPopup.open({
	                    showCloseBtn: false,
	                    type: 'inline',
	                    alignTop: true,
	                    tLoading: 'Loading...',
	                    items: {
	                        src: '#modal-digital-package'
	                    }
	                });
	                $('body').on('click', '.js_mfpopup-popup-close', function (e) {
	                    e.preventDefault();
	                    $.magnificPopup.close();
	                });
	            }
	            var $updateInformer = $('.js-get-info-zip');
	            if ($updateInformer.length) {
	                var action = $updateInformer.attr('data-action');
	                var method = $updateInformer.attr('data-method');
	                var data = { _token: (0, _getToken.getToken)() };
	                var intervalSendAjax = setInterval(function () {
	                    $.ajax({
	                        url: action,
	                        method: method,
	                        data: data,
	                        success: function success(data) {
	                            if (data.redirect !== undefined) {
	                                window.location.replace(data.redirect);
	                                return;
	                            }
	                            if (data.link && data.link !== '') {
	                                var $cntUser = $('.js-get-info-zip-cnt');
	                                $cntUser.fadeIn(200);
	                                $updateInformer.remove();
	                                $cntUser.each(function (el) {
	                                    el.find('.js_zip-download').attr('href', data.link);
	                                });
	                                clearInterval(intervalSendAjax);
	                            }
	                        },
	                        error: function error(data) {}
	                    });
	                }, 3000);
	            }
	            /**
	             * Send form enter code
	             * @type {Form}
	             */
	            var formCodeLogin = new _sendform.Sendform('.js_form-code', {
	                success: function success() {},
	                error: function error(request) {
	                    $('.js_form-code .form-status').text(JSON.parse(request.response).message).addClass('with_error');
	                }
	            });
	            /**
	             * Send form on order payment page
	             * @type {Form}
	             */
	            var formOrderPay = new _sendform.Sendform('.js_form-pay', {
	                success: function success(data) {
	                    var url = '/order/get/' + JSON.parse(data.response).order_id;
	                    window.location.href = url;
	                },
	                error: function error(request) {
	                    $('.js_form-pay .form-status').html(JSON.parse(request.response).message).addClass('with_error');
	                }
	            });
	            /**
	             * Send form on order status page
	             * @type {Form}
	             */
	            var formWithReview = new _sendform.Sendform('.js_submit-review', {
	                success: function success() {},
	                error: function error(request) {
	                    $('.js_submit-review .form-status').text(JSON.parse(request.response).message).addClass('with_error');
	                }
	            });
	            $('body').on('click', '.js_review-add', function () {
	                var link = $(this).attr('data-href');
	                window.open(link, '_blank');
	            });
	
	            /**
	             * Send form on order status page
	             * @type {Form}
	             */
	            var formWithReviewEmail = new _sendform.Sendform('.js_submit-review-email', {
	                success: function success() {}
	            });
	
	            if ($('.js_review-add-email').length) {
	                var links = $('.js_review-add-email').attr('data-href');
	                localStorage.setItem('link', links);
	                var linkGgl = localStorage.getItem('link');
	                window.open(linkGgl, '_blank');
	            }
	
	            if ($('.js_submit-review-email').length) {
	                $('.js_submit-review-email').trigger('submit');
	            }
	
	            /**
	             * accordion for cart product's on order status page
	             */
	            var accordion = function accordion(accordionTtl, accordionCnt) {
	                var activeClassTtl = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "__active";
	                var activeClassCnt = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '__show';
	
	                $('body').on('click', accordionTtl, function (event) {
	                    event.preventDefault();
	                    if ($(this).hasClass(activeClassTtl) && !$(this).hasClass(activeClassTtl)) {
	                        $(this).removeClass(activeClassTtl);
	                        $(this).closest('.order-product').find(accordionCnt).slideUp(400).removeClass(activeClassCnt);
	                    }
	                    $(this).toggleClass(activeClassTtl);
	                    $(this).closest('.order-product').find(accordionCnt).slideToggle(400).toggleClass(activeClassCnt);
	                });
	            };
	            accordion('.js_ui-accordion-ttl', '.js_ui-accordion-cnt');
	
	            /**
	             * Mask for order credit card
	             */
	            if ($('.js_date').length) {
	                var cleaveDate = new Cleave('.js_date', {
	                    date: true,
	                    datePattern: ['m', 'y']
	                });
	
	                var cleaveNumber = new Cleave('.js_number', {
	                    creditCard: true,
	                    onCreditCardTypeChanged: function onCreditCardTypeChanged(type) {}
	                });
	
	                var cleaveCvv = new Cleave('.js_cvv', {
	                    blocks: [3, 3, 3],
	                    numeral: true,
	                    delimiter: '',
	                    delimiterLazyShow: true
	                });
	            }
	            function detectMob() {
	                if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i)) {
	                    return true;
	                } else {
	                    return false;
	                }
	            }
	
	            if (detectMob()) {
	                // $('.js_mob-preview-images').fadeIn(150);
	                $('.js_zip-download').hide();
	            }
	        }
	    }]);
	    return App;
	}();
	
	;

/***/ }),
/* 79 */
/***/ (function(module, exports) {

	"use strict";
	
	exports.__esModule = true;
	
	exports.default = function (instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	};

/***/ }),
/* 80 */
/***/ (function(module, exports, __webpack_require__) {

	"use strict";
	
	exports.__esModule = true;
	
	var _defineProperty = __webpack_require__(81);
	
	var _defineProperty2 = _interopRequireDefault(_defineProperty);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	exports.default = function () {
	  function defineProperties(target, props) {
	    for (var i = 0; i < props.length; i++) {
	      var descriptor = props[i];
	      descriptor.enumerable = descriptor.enumerable || false;
	      descriptor.configurable = true;
	      if ("value" in descriptor) descriptor.writable = true;
	      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
	    }
	  }
	
	  return function (Constructor, protoProps, staticProps) {
	    if (protoProps) defineProperties(Constructor.prototype, protoProps);
	    if (staticProps) defineProperties(Constructor, staticProps);
	    return Constructor;
	  };
	}();

/***/ }),
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(82), __esModule: true };

/***/ }),
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

	__webpack_require__(83);
	var $Object = __webpack_require__(14).Object;
	module.exports = function defineProperty(it, key, desc) {
	  return $Object.defineProperty(it, key, desc);
	};


/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

	var $export = __webpack_require__(12);
	// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
	$export($export.S + $export.F * !__webpack_require__(22), 'Object', { defineProperty: __webpack_require__(18).f });


/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	Object.defineProperty(exports, "__esModule", {
	  value: true
	});
	
	var _form = __webpack_require__(85);
	
	Object.defineProperty(exports, 'Sendform', {
	  enumerable: true,
	  get: function get() {
	    return _interopRequireDefault(_form).default;
	  }
	});

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	
	var _getIterator2 = __webpack_require__(86);
	
	var _getIterator3 = _interopRequireDefault(_getIterator2);
	
	var _assign = __webpack_require__(91);
	
	var _assign2 = _interopRequireDefault(_assign);
	
	var _from = __webpack_require__(95);
	
	var _from2 = _interopRequireDefault(_from);
	
	var _classCallCheck2 = __webpack_require__(79);
	
	var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);
	
	var _createClass2 = __webpack_require__(80);
	
	var _createClass3 = _interopRequireDefault(_createClass2);
	
	var _field = __webpack_require__(102);
	
	var _field2 = _interopRequireDefault(_field);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	var Form = function () {
	    /**
	     * @param formElement {Element} - class of form.
	     * @param settings {Object} - settings object.
	     * @param reference {Object} - reference for validation.
	     */
	    function Form(formElement, settings, reference) {
	        var _this2 = this;
	
	        (0, _classCallCheck3.default)(this, Form);
	
	        this.form = this.form = document.querySelector(formElement);
	        if (this.form == null || undefined) return;
	        this.inputs = (0, _from2.default)(this.form.querySelectorAll('input:not([type="hidden"]), select, textarea'));
	        /**
	         * Set action(url for request)
	         */
	        var action = this.form.getAttribute('action');
	
	        this.action = action != null ? action : '/';
	
	        this.dispalyStatus = true;
	
	        // Form state if it contain errors
	        this.state = true;
	        //determine if there are any mistakes now.
	        this.error = false;
	        // Show spunner activity
	        this.isSpinnerActive = false;
	        // Text of status field.
	        this.statusText = null;
	        // Contain all field of this form
	        this.items = [];
	        // Contain errors field with position
	        this.errorItems = {};
	        // settings
	        var customSettings = {
	            resetAfterSubmit: true,
	            onlyValidate: false,
	            statusClass: 'form-status',
	            statusErrorClass: 'with_error',
	            statusSuccessClass: 'with_success',
	            errorClass: 'error',
	            successClass: 'success-valid',
	            validateClass: '.js_sendform-validate',
	            requiredClass: 'form-required',
	            modalOpen: true,
	            modalId: '#thanks',
	            msgSend: '',
	            msgDone: 'Done',
	            msgError: 'Sending error',
	            msgValError: 'One of required field is empty',
	            spinnerColor: '#000',
	            formPosition: 'relative',
	            resetClass: '.js_senform-reset',
	            method: 'POST',
	            sendAllCheckbox: false,
	            success: function success(data) {
	                _this2.successSubmit();
	            },
	            error: function error(data) {
	                _this2.errorSubmit();
	            },
	            validationSuccess: function validationSuccess() {},
	            validationError: function validationError() {
	                _this2.validationErrorCallback();
	            }
	            // validation rules
	        };var customReference = {
	            email: ['isEmail', 'isEmpty'],
	            text: ['isEmpty'],
	            textarea: ['isEmpty'],
	            phone: ['minLength'],
	            required: ['isEmpty'],
	            checkbox: ['isChecked'],
	            radio: ['isCheckedRadio']
	
	        };
	
	        this.settings = (0, _assign2.default)({}, customSettings, settings);
	        this.reference = (0, _assign2.default)({}, customReference, reference);
	
	        this.onInit();
	    }
	
	    /**
	     * On initialize class.
	     * Creating all inputs of this form.
	     * if setting for only validate true init this func.
	     * else init function on submitting.
	     * creating status text field.
	     * init function for reset field.
	     */
	
	
	    (0, _createClass3.default)(Form, [{
	        key: 'onInit',
	        value: function onInit() {
	            var _this3 = this;
	
	            this.createInputsValidate();
	
	            if (this.settings.onlyValidate) {
	                this.onValidate();
	            } else {
	                this.form.addEventListener('submit', function (event) {
	                    event.preventDefault();
	                    _this3.preSubmit();
	                });
	            }
	            this.dispalyStatus = $(this.form).data('status') === undefined ? true : $(this.form).data('status');
	
	            if (this.dispalyStatus) {
	                this.createStatusField();
	            }
	            this.onReset();
	        }
	
	        /**
	         * Creating for each input, select, checkboxes own class.
	         * And pushing this classes into array.
	         */
	
	    }, {
	        key: 'createInputsValidate',
	        value: function createInputsValidate() {
	            var _this4 = this;
	
	            this.inputs.forEach(function (el, i) {
	                var item = new _field2.default(el, _this4.state, _this4.reference, _this4.settings, i);
	                _this4.items.push(item);
	            });
	        }
	
	        /**
	         * Create hidden field for status text.
	         */
	
	    }, {
	        key: 'createStatusField',
	        value: function createStatusField() {
	            if (this.form.querySelector('.' + this.settings.statusClass) !== null) {
	                this.statusText = this.form.querySelector('.' + this.settings.statusClass);
	                return;
	            }
	            var div = document.createElement('div');
	            div.innerHTML = '';
	            div.classList.add(this.settings.statusClass);
	            this.form.appendChild(div);
	            this.statusText = this.form.querySelector('.' + this.settings.statusClass);
	        }
	
	        /**
	         * checking on error.
	         * prepare for submitting:
	         * add spinner, add status text.
	         * call submit function.
	         */
	
	    }, {
	        key: 'preSubmit',
	        value: function preSubmit() {
	            var _this5 = this;
	
	            this.validateField();
	            if (!this.state) {
	                this.errorOnForm();
	                return;
	            }
	            var checkbox = (0, _from2.default)(this.form.querySelectorAll('input[type="checkbox"]'));
	            checkbox.forEach(function (item) {
	                if (item.checked) {
	                    item.value = 1;
	                }
	                if (_this5.settings.sendAllCheckbox && !item.checked) {
	                    item.value = 0;
	                }
	            });
	
	            this.error = false;
	            if (!this.isSpinnerActive) this.addSpinner();
	            if (this.dispalyStatus) this.statusText.innerHTML = this.settings.msgSend;
	            this.submitData();
	        }
	
	        /**
	         * Foreach in all items call validation function.
	         * @param result {object} - variable keep return from
	         * validation function.Object contain 2 attr
	         * result.valid {boolean} -show is field pass validation.
	         * result.position {string} - position of field.
	         *
	         */
	
	    }, {
	        key: 'validateField',
	        value: function validateField() {
	            var _this6 = this;
	
	            var localState = true;
	            this.items.forEach(function (item) {
	                var result = item.validate();
	                if (result == undefined) return;
	                localState = localState * result.valid;
	
	                if (localState) {
	                    delete _this6.errorItems[result.position];
	                } else {
	                    _this6.errorItems[result.position] = false;
	                }
	            });
	
	            this.state = localState;
	            if (this.state) {
	                this.removeStatusText();
	            }
	        }
	
	        /**
	         * Call reset method on all items.
	         */
	
	    }, {
	        key: 'resetField',
	        value: function resetField() {
	            this.items.forEach(function (item) {
	                item.resetSelf();
	            });
	        }
	
	        /**
	         * Adding spinner.
	         */
	
	    }, {
	        key: 'addSpinner',
	        value: function addSpinner() {
	            var div = document.createElement('div');
	            div.innerHTML = '<div class="form-loading"></div>';
	            div.id = 'formsendHover';
	            //this.form.appendChild(div)
	            document.body.appendChild(div);
	            this.isSpinnerActive = true;
	        }
	        /**
	         * Removing spinner.
	         */
	
	    }, {
	        key: 'removeSpinner',
	        value: function removeSpinner() {
	            if (!document.querySelector('#formsendHover')) return;
	            document.querySelector('#formsendHover').remove();
	            this.isSpinnerActive = false;
	        }
	
	        /**
	         * init validation by press on btn.
	         */
	
	    }, {
	        key: 'onValidate',
	        value: function onValidate() {
	            var _this7 = this;
	
	            var validateBtn = this.form.querySelector(this.settings.validateClass);
	            validateBtn.addEventListener('click', function (event) {
	                event.preventDefault();
	                _this7.validateField();
	                if (_this7.state) {
	                    _this7.settings.validationSuccess();
	                    return;
	                }
	                _this7.settings.validationError();
	            });
	        }
	
	        /**
	         * init function reseting by press btn.
	         */
	
	    }, {
	        key: 'onReset',
	        value: function onReset() {
	            var _this8 = this;
	
	            var resetClass = this.form.querySelector(this.settings.resetClass);
	            if (resetClass == null || undefined) return;
	            resetClass.addEventListener('click', function () {
	                _this8.resetField();
	            });
	        }
	
	        /**
	         * Add text and set error on true.
	         * And add text error.
	         */
	
	    }, {
	        key: 'errorOnForm',
	        value: function errorOnForm() {
	            if (this.error) return;
	            this.error = true;
	            if (this.dispalyStatus) {
	                this.statusText.innerHTML = this.settings.msgValError;
	                this.statusText.classList.add('with_error');
	            }
	        }
	
	        /**
	         * On error validation
	         */
	
	    }, {
	        key: 'validationErrorCallback',
	        value: function validationErrorCallback() {
	            if (this.dispalyStatus) {
	                this.errorStatusClass();
	                this.printText(this.settings.msgValError);
	            }
	        }
	
	        /**
	         * Set text in status in form.
	         * @param text{string}
	         */
	
	    }, {
	        key: 'printText',
	        value: function printText(text) {
	            this.statusText.innerHTML = text;
	        }
	
	        /**
	         * Clean status text
	         */
	
	    }, {
	        key: 'removeStatusText',
	        value: function removeStatusText() {
	            if (this.dispalyStatus) {
	                this.statusText.innerHTML = '';
	                this.statusText.classList = this.settings.statusClass;
	            }
	        }
	
	        /**
	         * Set error class on status text in form
	         */
	
	    }, {
	        key: 'errorStatusClass',
	        value: function errorStatusClass() {
	            this.statusText.classList.add(this.settings.statusErrorClass);
	        }
	
	        /**
	         * Set success class on status text in form
	         */
	
	    }, {
	        key: 'successStatusClass',
	        value: function successStatusClass() {
	            this.statusText.classList.add(this.settings.statusSuccessClass);
	        }
	
	        /**
	         * Submitting data
	         * @param event
	         */
	
	    }, {
	        key: 'submitData',
	        value: function submitData(event) {
	            var _this9 = this;
	
	            var request = new XMLHttpRequest();
	
	            var data = new FormData(this.form);
	
	            if (this.settings.method == 'GET') {
	                var firstRun = true;
	                var _this = this;
	                var _iteratorNormalCompletion = true;
	                var _didIteratorError = false;
	                var _iteratorError = undefined;
	
	                try {
	                    for (var _iterator = (0, _getIterator3.default)(data.keys()), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
	                        var key = _step.value;
	
	                        if (firstRun) {
	                            _this.action += '?';
	                            firstRun = false;
	                        } else _this.action += '&';
	                        _this.action += key + '=' + data.get(key);
	                    }
	                } catch (err) {
	                    _didIteratorError = true;
	                    _iteratorError = err;
	                } finally {
	                    try {
	                        if (!_iteratorNormalCompletion && _iterator.return) {
	                            _iterator.return();
	                        }
	                    } finally {
	                        if (_didIteratorError) {
	                            throw _iteratorError;
	                        }
	                    }
	                }
	            }
	
	            request.open(this.settings.method, this.action, true);
	            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	            var filesInput = this.form.querySelectorAll('input[type="file"]');
	            if (filesInput.length) {
	                filesInput = (0, _from2.default)(filesInput);
	                data = this.prepareFiles(filesInput, data);
	            }
	
	            request.onload = function (data) {
	                // Success!
	                if (request.status >= 200 && request.status < 400) {
	                    if (request.getResponseHeader('Content-Type') === 'application/json') {
	                        var _data = JSON.parse(request.response);
	                        //redirect
	                        if (_data.redirect !== undefined) {
	                            window.location.replace(_data.redirect);
	                            return;
	                        }
	                    }
	                    _this9.settings.success(request);
	                } else {
	                    // We reached our target server, but it returned an error
	                    _this9.settings.error(request);
	                }
	                _this9.removeSpinner();
	            };
	            request.send(data);
	        }
	
	        /**
	         * Check input with files on existing files
	         *  run function to prepare files
	         *
	         * @param inputsWithFile
	         * @param data
	         * @returns {*}
	         */
	
	    }, {
	        key: 'prepareFiles',
	        value: function prepareFiles(inputsWithFile, data) {
	            var _this10 = this;
	
	            inputsWithFile.forEach(function (input) {
	                if (!input.files.length) return;
	
	                data = _this10.appendFilesIntoData(input, data);
	            });
	
	            return data;
	        }
	
	        /**
	         * Add files into data with new names
	         * @param input
	         * @param data
	         * @returns {*}
	         */
	
	    }, {
	        key: 'appendFilesIntoData',
	        value: function appendFilesIntoData(input, data) {
	
	            var files = (0, _from2.default)(input.files);
	
	            files.forEach(function (file, i) {
	                data.append('' + input.name + i, file);
	            });
	
	            return data;
	        }
	
	        /**
	         * On error submit
	         */
	
	    }, {
	        key: 'errorSubmit',
	        value: function errorSubmit() {
	            if (this.dispalyStatus) {
	                this.errorStatusClass();
	                this.printText(this.settings.msgError);
	            }
	        }
	
	        /**
	         * On success submit
	         */
	
	    }, {
	        key: 'successSubmit',
	        value: function successSubmit() {
	            if (this.dispalyStatus) {
	                this.successStatusClass();
	                this.printText(this.settings.msgDone);
	            }
	        }
	    }]);
	    return Form;
	}();
	
	exports.default = Form;

/***/ }),
/* 86 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(87), __esModule: true };

/***/ }),
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

	__webpack_require__(50);
	__webpack_require__(6);
	module.exports = __webpack_require__(88);


/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

	var anObject = __webpack_require__(19);
	var get = __webpack_require__(89);
	module.exports = __webpack_require__(14).getIterator = function (it) {
	  var iterFn = get(it);
	  if (typeof iterFn != 'function') throw TypeError(it + ' is not iterable!');
	  return anObject(iterFn.call(it));
	};


/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {

	var classof = __webpack_require__(90);
	var ITERATOR = __webpack_require__(47)('iterator');
	var Iterators = __webpack_require__(29);
	module.exports = __webpack_require__(14).getIteratorMethod = function (it) {
	  if (it != undefined) return it[ITERATOR]
	    || it['@@iterator']
	    || Iterators[classof(it)];
	};


/***/ }),
/* 90 */
/***/ (function(module, exports, __webpack_require__) {

	// getting tag from 19.1.3.6 Object.prototype.toString()
	var cof = __webpack_require__(37);
	var TAG = __webpack_require__(47)('toStringTag');
	// ES3 wrong here
	var ARG = cof(function () { return arguments; }()) == 'Arguments';
	
	// fallback for IE11 Script Access Denied error
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (e) { /* empty */ }
	};
	
	module.exports = function (it) {
	  var O, T, B;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
	    // builtinTag case
	    : ARG ? cof(O)
	    // ES3 arguments fallback
	    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
	};


/***/ }),
/* 91 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(92), __esModule: true };

/***/ }),
/* 92 */
/***/ (function(module, exports, __webpack_require__) {

	__webpack_require__(93);
	module.exports = __webpack_require__(14).Object.assign;


/***/ }),
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

	// 19.1.3.1 Object.assign(target, source)
	var $export = __webpack_require__(12);
	
	$export($export.S + $export.F, 'Object', { assign: __webpack_require__(94) });


/***/ }),
/* 94 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	// 19.1.2.1 Object.assign(target, source, ...)
	var getKeys = __webpack_require__(33);
	var gOPS = __webpack_require__(61);
	var pIE = __webpack_require__(62);
	var toObject = __webpack_require__(49);
	var IObject = __webpack_require__(36);
	var $assign = Object.assign;
	
	// should work with symbols and should have deterministic property order (V8 bug)
	module.exports = !$assign || __webpack_require__(23)(function () {
	  var A = {};
	  var B = {};
	  // eslint-disable-next-line no-undef
	  var S = Symbol();
	  var K = 'abcdefghijklmnopqrst';
	  A[S] = 7;
	  K.split('').forEach(function (k) { B[k] = k; });
	  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
	}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
	  var T = toObject(target);
	  var aLen = arguments.length;
	  var index = 1;
	  var getSymbols = gOPS.f;
	  var isEnum = pIE.f;
	  while (aLen > index) {
	    var S = IObject(arguments[index++]);
	    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
	    var length = keys.length;
	    var j = 0;
	    var key;
	    while (length > j) if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
	  } return T;
	} : $assign;


/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(96), __esModule: true };

/***/ }),
/* 96 */
/***/ (function(module, exports, __webpack_require__) {

	__webpack_require__(6);
	__webpack_require__(97);
	module.exports = __webpack_require__(14).Array.from;


/***/ }),
/* 97 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var ctx = __webpack_require__(15);
	var $export = __webpack_require__(12);
	var toObject = __webpack_require__(49);
	var call = __webpack_require__(98);
	var isArrayIter = __webpack_require__(99);
	var toLength = __webpack_require__(39);
	var createProperty = __webpack_require__(100);
	var getIterFn = __webpack_require__(89);
	
	$export($export.S + $export.F * !__webpack_require__(101)(function (iter) { Array.from(iter); }), 'Array', {
	  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
	  from: function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	    var O = toObject(arrayLike);
	    var C = typeof this == 'function' ? this : Array;
	    var aLen = arguments.length;
	    var mapfn = aLen > 1 ? arguments[1] : undefined;
	    var mapping = mapfn !== undefined;
	    var index = 0;
	    var iterFn = getIterFn(O);
	    var length, result, step, iterator;
	    if (mapping) mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
	    // if object isn't iterable or it's array with default iterator - use simple case
	    if (iterFn != undefined && !(C == Array && isArrayIter(iterFn))) {
	      for (iterator = iterFn.call(O), result = new C(); !(step = iterator.next()).done; index++) {
	        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
	      }
	    } else {
	      length = toLength(O.length);
	      for (result = new C(length); length > index; index++) {
	        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
	      }
	    }
	    result.length = index;
	    return result;
	  }
	});


/***/ }),
/* 98 */
/***/ (function(module, exports, __webpack_require__) {

	// call something on iterator step with safe closing on error
	var anObject = __webpack_require__(19);
	module.exports = function (iterator, fn, value, entries) {
	  try {
	    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
	  // 7.4.6 IteratorClose(iterator, completion)
	  } catch (e) {
	    var ret = iterator['return'];
	    if (ret !== undefined) anObject(ret.call(iterator));
	    throw e;
	  }
	};


/***/ }),
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

	// check on default Array iterator
	var Iterators = __webpack_require__(29);
	var ITERATOR = __webpack_require__(47)('iterator');
	var ArrayProto = Array.prototype;
	
	module.exports = function (it) {
	  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
	};


/***/ }),
/* 100 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	var $defineProperty = __webpack_require__(18);
	var createDesc = __webpack_require__(26);
	
	module.exports = function (object, index, value) {
	  if (index in object) $defineProperty.f(object, index, createDesc(0, value));
	  else object[index] = value;
	};


/***/ }),
/* 101 */
/***/ (function(module, exports, __webpack_require__) {

	var ITERATOR = __webpack_require__(47)('iterator');
	var SAFE_CLOSING = false;
	
	try {
	  var riter = [7][ITERATOR]();
	  riter['return'] = function () { SAFE_CLOSING = true; };
	  // eslint-disable-next-line no-throw-literal
	  Array.from(riter, function () { throw 2; });
	} catch (e) { /* empty */ }
	
	module.exports = function (exec, skipClosing) {
	  if (!skipClosing && !SAFE_CLOSING) return false;
	  var safe = false;
	  try {
	    var arr = [7];
	    var iter = arr[ITERATOR]();
	    iter.next = function () { return { done: safe = true }; };
	    arr[ITERATOR] = function () { return iter; };
	    exec(arr);
	  } catch (e) { /* empty */ }
	  return safe;
	};


/***/ }),
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

	'use strict';
	
	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	
	var _toConsumableArray2 = __webpack_require__(103);
	
	var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);
	
	var _assign = __webpack_require__(91);
	
	var _assign2 = _interopRequireDefault(_assign);
	
	var _classCallCheck2 = __webpack_require__(79);
	
	var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);
	
	var _createClass2 = __webpack_require__(80);
	
	var _createClass3 = _interopRequireDefault(_createClass2);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	/**
	 * Class Field.
	 */
	var Field = function () {
	    function Field(field, state, reference, settings, position) {
	        (0, _classCallCheck3.default)(this, Field);
	
	        this.field = field;
	        this.reference = (0, _assign2.default)({}, reference);
	        this.isValid = true;
	        this.type = this.field.getAttribute('type');
	        this.isRequired = this.field.hasAttribute('required');
	        this.firstCheck = true;
	        this.settings = settings;
	        this.regularExp = this.field.getAttribute('pattern');
	        this.position = position;
	
	        this.removeRequired();
	        this.onKeyUp();
	        this.onChange();
	        if (this.regularExp != null) {
	            this.createValidation();
	        }
	    }
	
	    (0, _createClass3.default)(Field, [{
	        key: 'createValidation',
	        value: function createValidation() {
	            this.field.removeAttribute('pattern');
	            if (this.reference[this.type] == undefined || null) {
	                this.reference[this.type] = ['regExp'];
	                return;
	            }
	            this.reference[this.type] = [].concat((0, _toConsumableArray3.default)(this.reference[this.type]), ['regExp']);
	        }
	    }, {
	        key: 'removeRequired',
	        value: function removeRequired() {
	            if (!this.isRequired) return;
	
	            this.field.removeAttribute("required");
	            this.field.classList.add(this.settings.requiredClass);
	        }
	    }, {
	        key: 'onKeyUp',
	        value: function onKeyUp() {
	            var _this = this;
	
	            this.field.addEventListener('keyup', function (el) {
	                if (_this.firstCheck) return;
	                var value = el.target.value;
	                _this.validate(value);
	            });
	        }
	    }, {
	        key: 'onChange',
	        value: function onChange() {
	            var _this2 = this;
	
	            this.field.addEventListener('change', function (el) {
	                var value = el.target.value;
	                _this2.validate(value);
	                _this2.firstCheck = false;
	            });
	        }
	    }, {
	        key: 'validate',
	        value: function validate() {
	            var _this3 = this;
	
	            if (!this.needValidate()) {
	                this.removeError();
	                return;
	            }
	
	            var value = this.field.value;
	            var validationFunc = this.reference[this.type];
	            var valid = true;
	
	            if (validationFunc == undefined) {
	                validationFunc = this.reference['required'];
	            }
	            validationFunc.forEach(function (func) {
	                var result = _this3[func](value);
	                valid = valid * result;
	            });
	
	            if (valid) {
	                this.removeError();
	            } else {
	                this.addError();
	            }
	            this.isValid = valid;
	            return {
	                valid: this.isValid,
	                position: this.position
	            };
	        }
	    }, {
	        key: 'addError',
	        value: function addError() {
	            this.field.classList.add(this.settings.errorClass);
	        }
	    }, {
	        key: 'removeError',
	        value: function removeError() {
	            this.field.classList.remove(this.settings.errorClass);
	        }
	    }, {
	        key: 'needValidate',
	        value: function needValidate() {
	            var noEmpty = this.field.value != 0;
	
	            if (this.isRequired) {
	                return true;
	            }
	
	            if (noEmpty && this.type == 'email') {
	                return true;
	            }
	
	            if (noEmpty && this.regularExp != null) {
	                return true;
	            }
	
	            return false;
	        }
	    }, {
	        key: 'resetSelf',
	        value: function resetSelf() {
	            this.field.value = '';
	            this.field.checked = false;
	        }
	
	        //Is on no empty value testing
	
	    }, {
	        key: 'isEmpty',
	        value: function isEmpty(val) {
	            if (val == '') {
	                return false;
	            } else {
	                return true;
	            }
	        }
	
	        //Is email testing
	
	    }, {
	        key: 'isEmail',
	        value: function isEmail(val) {
	            var email = /^[-\w.]+@([A-z0-9]+\.)+[A-z]{2,4}$/;
	            return email.test(val);
	        }
	
	        //Is url testing
	
	    }, {
	        key: 'isUrl',
	        value: function isUrl(element) {
	            var url = /[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,}/;
	            return url.test($(element).val());
	        }
	
	        //Is min 5 charachter
	
	    }, {
	        key: 'minLength',
	        value: function minLength(val) {
	            if (val.length > 5) return true;
	        }
	    }, {
	        key: 'isChecked',
	        value: function isChecked() {
	            if (this.field.checked) {
	                return true;
	            }
	            return false;
	        }
	
	        //Is cyrillic testing
	
	    }, {
	        key: 'isCyr',
	        value: function isCyr(val) {
	            var cyr = /[\u0400-\u04FF]/gi;
	            return cyr.test(val);
	        }
	    }, {
	        key: 'isCheckedRadio',
	        value: function isCheckedRadio() {
	            var collection = document.querySelectorAll('input[name=' + this.field.name + ']');
	            var isSomeChecked = false;
	            collection.forEach(function (el) {
	                if (el.checked) isSomeChecked = true;
	            });
	            return isSomeChecked;
	        }
	    }, {
	        key: 'regExp',
	        value: function regExp(val) {
	            var reqular = new RegExp(this.regularExp);
	            return reqular.test(val);
	        }
	    }]);
	    return Field;
	}();
	
	exports.default = Field;

/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

	"use strict";
	
	exports.__esModule = true;
	
	var _from = __webpack_require__(95);
	
	var _from2 = _interopRequireDefault(_from);
	
	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
	
	exports.default = function (arr) {
	  if (Array.isArray(arr)) {
	    for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
	      arr2[i] = arr[i];
	    }
	
	    return arr2;
	  } else {
	    return (0, _from2.default)(arr);
	  }
	};

/***/ }),
/* 104 */
/***/ (function(module, exports) {

	'use strict';
	
	Object.defineProperty(exports, "__esModule", {
	    value: true
	});
	exports.getToken = getToken;
	function getToken() {
	    return $('#_token-csrf').html();
	}

/***/ })
/******/ ]);
//# sourceMappingURL=script.js.map