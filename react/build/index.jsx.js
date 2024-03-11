/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./react/components/customer/CustomerContext.jsx":
/*!*******************************************************!*\
  !*** ./react/components/customer/CustomerContext.jsx ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomerContext: () => (/* binding */ CustomerContext),
/* harmony export */   CustomerContextProvider: () => (/* binding */ CustomerContextProvider)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);

/**
 * Customer context
 *
 * Manages customer authentication and state data.
 */


const CustomerContext = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createContext)(false);
function CustomerContextProvider({
  children
}) {
  // Only one customer allowed due to persisting global.
  const [authToken, setAuthToken] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(sessionStorage.getItem('ptcCustomer') || '');
  // Form input controls.
  const [emailInput, setEmailInput] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)('');
  const [passwordInput, setPasswordInput] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)('');
  // State signals.
  const [processingStatus, setProcessingStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)('idle'); // idle, loading.

  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    // Persist state when updates occur.
    sessionStorage.setItem('ptcCustomer', authToken);
  }, [authToken]);
  const resErrorAlreadyProcessing = {
    status: 'error',
    code: 400,
    message: 'Already processing another request.',
    data: null
  };
  const authenticate = async action => {
    if ('loading' !== processingStatus) {
      setProcessingStatus('loading');
      return await window.fetch(`${window.ptcTheme.api.v1}/customer/authenticate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.ptcTheme.api.auth_nonce
        },
        body: JSON.stringify({
          action,
          email: context.emailInput,
          password: context.passwordInput,
          nonce: window.ptcTheme.api.nonce
        })
      }).then(async res => {
        if (!res.ok) {
          throw `Failed to log in. Error: ${res.statusText}`;
        }
        const body = await res.json();
        setAuthToken(body.data.authToken);
        return body;
      }).catch(err => {
        return {
          status: 'error',
          code: 500,
          message: err,
          data: null
        };
      }).finally(() => {
        setProcessingStatus('idle');
      });
    } else {
      return resErrorAlreadyProcessing;
    }
  };

  // Public context.
  const context = {
    emailInput,
    setEmailInput,
    passwordInput,
    setPasswordInput,
    isLoggedIn: () => {
      return !!authToken;
    },
    login: async () => {
      return await authenticate('login');
    },
    signup: async () => {
      return await authenticate('signup');
    },
    logout: () => {
      setAuthToken(''); // reset state.
    },
    verifyEmail: async () => {
      if ('loading' !== processingStatus) {
        setProcessingStatus('loading');
        return await window.fetch(`${window.ptcTheme.api.v1}/mailing-lists/subscribe`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.ptcTheme.api.auth_nonce
          },
          body: JSON.stringify({
            email: context.emailInput,
            list_id: 'a2tBf2KrKnxBF66s',
            // Hard-coded. Sorry, Mom.
            nonce: window.ptcTheme.api.nonce
          })
        }).then(async res => {
          if (!res.ok) {
            throw `Failed to verify email. Error: ${res.statusText}`;
          }
          const body = await res.json();
          return body;
        }).catch(err => {
          return {
            status: 'error',
            code: 500,
            message: err,
            data: null
          };
        }).finally(() => {
          setProcessingStatus('idle');
        });
      } else {
        return resErrorAlreadyProcessing;
      }
    },
    resetPassword: async () => {
      // use context.passwordInput.
      return await window.fetch().then(res => res.json()).then(res => {
        return res.data;
      }).catch(err => {
        return null;
      });
    },
    fetchCustomerData: async () => {
      // requires authToken.
      return await window.fetch().then(res => res.json()).then(res => {
        return res.data;
      }).catch(err => {
        return null;
      });
    },
    goToCheckout: (productId, planId) => {
      // requires authToken.
      // redirect to checkout session URL.
    },
    goToBillingPortal: () => {
      // requires authToken.
      // redirect to customer billing portal URL.
    }
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(CustomerContext.Provider, {
    value: context
  }, children);
}

/***/ }),

/***/ "./react/components/forms/FormCustomerAuthentication.jsx":
/*!***************************************************************!*\
  !*** ./react/components/forms/FormCustomerAuthentication.jsx ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormCustomerAuthentication)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);

/**
 * FormCustomerAuthentication component
 *
 * Authenticates a customer session.
 */


function FormCustomerAuthentication({
  onSuccess
}) {
  const [status, setStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)('idle');

  // @todo - Make tabbed component to elegantly switch between login and signup forms.

  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormCustomerAuthentication"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, "Hello, cruel world!"));
}

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*************************!*\
  !*** ./react/index.jsx ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/customer/CustomerContext.jsx */ "./react/components/customer/CustomerContext.jsx");
/* harmony import */ var _components_forms_FormCustomerAuthentication_jsx__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/forms/FormCustomerAuthentication.jsx */ "./react/components/forms/FormCustomerAuthentication.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Renders customer components.
 */




document.addEventListener('DOMContentLoaded', () => {
  const rootNode = document.querySelector('#ptc-react-customer-authentication');
  if (rootNode) {
    const handleSuccess = res => {
      window.console.log('Successfully authenticated!', res);
    };
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.createRoot)(rootNode).render((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__.CustomerContextProvider, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_forms_FormCustomerAuthentication_jsx__WEBPACK_IMPORTED_MODULE_2__["default"], {
      onSuccess: handleSuccess
    })));
  }
});
})();

/******/ })()
;
//# sourceMappingURL=index.jsx.js.map