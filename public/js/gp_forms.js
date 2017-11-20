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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _form = __webpack_require__(1);

var _form2 = _interopRequireDefault(_form);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function ready(func) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
        func();
    } else {
        document.addEventListener("DOMContentLoaded", func);
    }
}

function load() {
    var form = new _form2.default();
    document.addEventListener("pageLoad", function () {
        form = new _form2.default();
    });
}

ready(load);

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _ajax = __webpack_require__(2);

var _ajax2 = _interopRequireDefault(_ajax);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

Math.easeInOutQuint = function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return c / 2 * t * t * t * t * t + b;
    t -= 2;
    return c / 2 * (t * t * t * t * t + 2) + b;
};

function Form() {
    this.form = document.querySelector(".gp_form");
    var that = this;

    if (this.form) {
        this.inputs = this.form.querySelectorAll("input");
        this.textareas = this.form.querySelectorAll("textarea");
        this.selects = this.form.querySelectorAll("select");
        this.formName = this.form.getAttribute("name");
        this.form.addEventListener("submit", this.submit.bind(this), false);

        for (var i = 0; i < this.inputs.length; i++) {
            this.inputs[i].addEventListener("blur", function () {
                that.textChange(this);
            });
        }
        for (var i = 0; i < this.textareas.length; i++) {
            this.textareas[i].addEventListener("blur", function () {
                that.textChange(this);
            });
        }
    }
}

Form.prototype.textChange = function (el) {
    if (el.value == "") {
        el.classList.remove("is-filled");
    } else {
        el.classList.add("is-filled");
    }
};

Form.prototype.submit = function (e) {
    e.preventDefault();
    var that = this;
    this.data = new FormData(this.form);
    // this.processFields();
    (0, _ajax2.default)({
        headers: {
            "content-type": null
        }
    }).post("/wp-json/" + nonce[0] + "/submit/" + this.formName + "?_wpnonce=" + nonce[1], this.data).always(function (response) {
        var alertZone = that.form.querySelector(".form__alert-zone");
        if (response.hasErrors) {
            alertZone.innerHTML = "";
            that.createAlert("danger", response.mainError);
            that.scroll(window.scrollY, that.form.offsetTop, 2);

            for (var obj in response.errors) {
                if (response.errors[obj] !== "") {
                    var tmp = document.querySelector("[name='" + obj + "']");
                    tmp.parentElement.classList.add("is-error");
                }
            }

            // for (var i = 0; i < response.errors.length; i++) {
            //     that.createAlert("danger", response.errors[i]);
            //     that.scroll(window.scrollY, that.form.offsetTop, 2);
            // }
        } else {
            alertZone.innerHTML = "";

            if (document.querySelector(".alert--fancy")) {
                var fancy = document.querySelector(".alert--fancy");
                fancy.classList.add("is-visible");
                that.form.style.display = "none";
            } else {
                that.createAlert("success", "Form submitted successfully");
                that.scroll(window.scrollY, that.form.offsetTop, 2);
            }
        }
    });
};

Form.prototype.createAlert = function (type, message) {
    var alertZone = this.form.querySelector(".form__alert-zone");
    var alert = document.createElement("div");
    alert.setAttribute("class", "alert alert--" + type);
    alert.innerHTML = message;
    alertZone.appendChild(alert);
};

Form.prototype.scroll = function (from, to, duration) {
    var change = to - from;
    var currentIteration = 0;
    var totalIterations = duration * 60;

    function animate() {
        currentIteration++;
        window.scrollTo(0, Math.easeInOutQuint(currentIteration, from, change, totalIterations));

        if (currentIteration < totalIterations) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
};

Form.prototype.processFields = function () {
    var i = 0;

    for (i = 0; i < this.inputs.length; i++) {
        this.data.append(this.inputs[i].getAttribute("name"), this.inputs[i].getAttribute("value"));
    }
};

exports.default = Form;

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/**!
 * ajax - v2.3.0
 * Ajax module in Vanilla JS
 * https://github.com/fdaciuk/ajax

 * Sun Jul 23 2017 10:55:09 GMT-0300 (BRT)
 * MIT (c) Fernando Daciuk
*/
!function (e, t) {
  "use strict";
   true ? !(__WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? exports = module.exports = t() : e.ajax = t();
}(undefined, function () {
  "use strict";
  function e(e) {
    var r = ["get", "post", "put", "delete"];return e = e || {}, e.baseUrl = e.baseUrl || "", e.method && e.url ? n(e.method, e.baseUrl + e.url, t(e.data), e) : r.reduce(function (r, o) {
      return r[o] = function (r, u) {
        return n(o, e.baseUrl + r, t(u), e);
      }, r;
    }, {});
  }function t(e) {
    return e || null;
  }function n(e, t, n, u) {
    var c = ["then", "catch", "always"],
        i = c.reduce(function (e, t) {
      return e[t] = function (n) {
        return e[t] = n, e;
      }, e;
    }, {}),
        f = new XMLHttpRequest(),
        d = r(t, n, e);return f.open(e, d, !0), f.withCredentials = u.hasOwnProperty("withCredentials"), o(f, u.headers), f.addEventListener("readystatechange", a(i, f), !1), f.send(s(n)), i.abort = function () {
      return f.abort();
    }, i;
  }function r(e, t, n) {
    if ("get" !== n.toLowerCase() || !t) return e;var r = s(t),
        o = e.indexOf("?") > -1 ? "&" : "?";return e + o + r;
  }function o(e, t) {
    t = t || {}, u(t) || (t["Content-Type"] = "application/x-www-form-urlencoded"), Object.keys(t).forEach(function (n) {
      t[n] && e.setRequestHeader(n, t[n]);
    });
  }function u(e) {
    return Object.keys(e).some(function (e) {
      return "content-type" === e.toLowerCase();
    });
  }function a(e, t) {
    return function n() {
      t.readyState === t.DONE && (t.removeEventListener("readystatechange", n, !1), e.always.apply(e, c(t)), t.status >= 200 && t.status < 300 ? e.then.apply(e, c(t)) : e["catch"].apply(e, c(t)));
    };
  }function c(e) {
    var t;try {
      t = JSON.parse(e.responseText);
    } catch (n) {
      t = e.responseText;
    }return [t, e];
  }function s(e) {
    return i(e) ? f(e) : e;
  }function i(e) {
    return "[object Object]" === Object.prototype.toString.call(e);
  }function f(e) {
    return Object.keys(e).reduce(function (t, n) {
      var r = t ? t + "&" : "";return r + d(n) + "=" + d(e[n]);
    }, "");
  }function d(e) {
    return encodeURIComponent(e);
  }return e;
});

/***/ })
/******/ ]);