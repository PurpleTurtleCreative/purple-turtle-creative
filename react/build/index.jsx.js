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
  const authenticate = async (action, params = {}) => {
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
          nonce: window.ptcTheme.api.nonce,
          ...params
        })
      }).then(async res => {
        const body = await res.json();
        if (res.ok && body?.data?.authToken) {
          setAuthToken(body.data.authToken);
        }
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
    processingStatus,
    emailInput,
    setEmailInput,
    passwordInput,
    setPasswordInput,
    isLoggedIn: () => {
      return !!authToken;
    },
    login: async (params = {}) => {
      return await authenticate('login', params);
    },
    signup: async (params = {}) => {
      return await authenticate('signup', params);
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
/* harmony import */ var _FormCustomerCreateAccount_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormCustomerCreateAccount.jsx */ "./react/components/forms/FormCustomerCreateAccount.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);

/**
 * FormCustomerAuthentication component
 *
 * Authenticates a customer session.
 */



function FormCustomerAuthentication({
  onSuccess
}) {
  const [status, setStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('idle');

  // @todo - Make tabbed component to elegantly switch between login and signup forms.

  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormCustomerAuthentication"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, "Create or Log In to Your Account"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Please create an account or sign in to manage your software licenses and billing information."), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormCustomerCreateAccount_jsx__WEBPACK_IMPORTED_MODULE_1__["default"], null));
}

/***/ }),

/***/ "./react/components/forms/FormCustomerCreateAccount.jsx":
/*!**************************************************************!*\
  !*** ./react/components/forms/FormCustomerCreateAccount.jsx ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormCustomerCreateAccount)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../customer/CustomerContext.jsx */ "./react/components/customer/CustomerContext.jsx");
/* harmony import */ var _FormInputCustomerEmail_jsx__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FormInputCustomerEmail.jsx */ "./react/components/forms/FormInputCustomerEmail.jsx");
/* harmony import */ var _FormInputCustomerPassword_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./FormInputCustomerPassword.jsx */ "./react/components/forms/FormInputCustomerPassword.jsx");
/* harmony import */ var _FormInputCaptcha_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./FormInputCaptcha.jsx */ "./react/components/forms/FormInputCaptcha.jsx");
/* harmony import */ var _FormStepVerificationCode_jsx__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./FormStepVerificationCode.jsx */ "./react/components/forms/FormStepVerificationCode.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);

/**
 * FormCustomerCreateAccount component
 *
 * Creates a new customer account.
 */







function FormCustomerCreateAccount(onSuccess) {
  const {
    emailInput,
    isLoggedIn,
    passwordInput,
    processingStatus,
    signup
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useContext)(_customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__.CustomerContext);
  const [error, setError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)('');
  const [confirmPasswordInput, setConfirmPasswordInput] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)('');
  const handleSubmit = async event => {
    event.preventDefault();
    if (confirmPasswordInput !== passwordInput) {
      setError('password_mismatch');
      return;
    } else {
      setError('');
    }
    const formData = new FormData(event.target);
    signup({
      "cf-turnstile-action": formData.get('cf-turnstile-action'),
      "cf-turnstile-response": formData.get('cf-turnstile-response')
    }).then(res => {
      if ('success' === res?.status) {
        onSuccess(res);
        setError('');
      } else {
        if (403 === res?.code) {
          // Signup could not be authorized due to missing
          // email verification. Show email verification step.
          setError('unverified_email');
        } else if (res?.message) {
          setError(res.message);
        } else {
          setError('Failed to create new account. Please try again.');
        }
      }
    });
  };
  const handleEmailVerificationSuccess = res => {
    window.console.trace(res);
    setError('');
  };
  let errorText = error;
  if ('password_mismatch' === error) {
    errorText = 'Passwords do not match. Please ensure your password was entered correctly into both fields and try again.';
  } else if ('unverified_email' === error) {
    errorText = '';
  }
  let innerContent = null;
  if (isLoggedIn()) {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "You are already logged in.");
  } else if ('loading' === processingStatus) {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Loading...");
  } else if ('unverified_email' === error) {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormStepVerificationCode_jsx__WEBPACK_IMPORTED_MODULE_5__["default"], {
      email: emailInput,
      codeLength: 6,
      onSuccess: handleEmailVerificationSuccess
    });
  } else {
    let passwordExtraClassNames = '';
    if ('password_mismatch' === error) {
      passwordExtraClassNames += ' input-error';
    }
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
      onSubmit: handleSubmit
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormInputCustomerEmail_jsx__WEBPACK_IMPORTED_MODULE_2__["default"], null), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormInputCustomerPassword_jsx__WEBPACK_IMPORTED_MODULE_3__["default"], {
      extraClassNames: passwordExtraClassNames
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "customer-confirm-password" + passwordExtraClassNames
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      htmlFor: "customer-confirm-password"
    }, "Confirm Password"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      type: "password",
      id: "customer-confirm-password",
      name: "confirm_password",
      value: confirmPasswordInput,
      onChange: event => {
        setConfirmPasswordInput(event.target.value);
      },
      required: true
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormInputCaptcha_jsx__WEBPACK_IMPORTED_MODULE_4__["default"], {
      action: "ptc-customer-create-account"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      type: "submit"
    }, "Create Account"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("small", null, "By submitting this form, you agree to our ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
      href: "https://purpleturtlecreative.com/privacy-policy/"
    }, "Privacy Policy"), " and to receiving important email messages from Purple Turtle Creative about your purchases. A verification email will be sent to confirm your account creation.")));
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormCustomerCreateAccount"
  }, errorText && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, errorText), innerContent);
}

/***/ }),

/***/ "./react/components/forms/FormInputCaptcha.jsx":
/*!*****************************************************!*\
  !*** ./react/components/forms/FormInputCaptcha.jsx ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormInputCaptcha)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

/**
 * FormInputCaptcha component
 *
 * Form input to render a captcha.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#explicitly-render-the-turnstile-widget
 */

function FormInputCaptcha({
  action
}) {
  let innerContent = null;
  if (!window?.ptcTheme?.cf_turnstile?.site_key) {
    window.console.error('Failed to render FormInputCaptcha without configured Cloudflare Turnstile site key.');
  } else if (!action) {
    window.console.error('Failed to render FormInputCaptcha without specified action.');
  } else {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      type: "hidden",
      name: "cf-turnstile-action",
      value: action
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      class: "cf-turnstile",
      "data-language": "en-us",
      "data-theme": "light",
      "data-size": "normal",
      "data-appearance": "always",
      "data-sitekey": window.ptcTheme.cf_turnstile.site_key,
      "data-action": action
    }));
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormInputCaptcha"
  }, innerContent);
}

/***/ }),

/***/ "./react/components/forms/FormInputCodePuncher.jsx":
/*!*********************************************************!*\
  !*** ./react/components/forms/FormInputCodePuncher.jsx ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormInputCodePuncher)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);

/**
 * FormInputCodePuncher component
 *
 * Controlled input to collect an array of digits.
 */


function FormInputCodePuncher({
  slots,
  onChange
}) {
  const inputRefs = Array.from({
    length: slots.length
  }, () => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useRef)());
  const handleInputChange = (index, value) => {
    if (1 === value.length && /^\d$/.test(value)) {
      const updatedSlots = [...slots];
      updatedSlots[index] = value;
      onChange(updatedSlots);
      if (index < slots.length - 1) {
        inputRefs[index + 1].current.focus(); // Focus next slot.
      }
    }
  };
  const handleKeyDown = (index, event) => {
    if ('Backspace' === event.key && index > 0) {
      const updatedSlots = [...slots];
      let slotToClearIndex = index - 1;
      if (inputRefs[index].current.value) {
        // Clear current slot instead if it has a value.
        slotToClearIndex = index;
      }
      updatedSlots[slotToClearIndex] = '';
      onChange(updatedSlots);
      inputRefs[slotToClearIndex].current.focus(); // Focus previous slot.
    }
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormInputCodePuncher"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("fieldset", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("legend", null, "Verification Code"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "code-puncher"
  }, slots.map((digit, index) => {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      key: index,
      ref: inputRefs[index],
      name: `code_punch_digits[${index}]`,
      type: "text",
      inputMode: "numeric",
      maxLength: "1",
      value: digit,
      onChange: e => handleInputChange(index, e.target.value),
      onKeyDown: e => handleKeyDown(index, e),
      "aria-label": `Digit ${index + 1}`
    });
  }))));
}

/***/ }),

/***/ "./react/components/forms/FormInputCustomerEmail.jsx":
/*!***********************************************************!*\
  !*** ./react/components/forms/FormInputCustomerEmail.jsx ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormInputCustomerEmail)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../customer/CustomerContext.jsx */ "./react/components/customer/CustomerContext.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);

/**
 * FormInputCustomerEmail component
 *
 * Controlled input to collect a customer's email address.
 */



function FormInputCustomerEmail() {
  const {
    emailInput,
    setEmailInput
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useContext)(_customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__.CustomerContext);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormInputCustomerEmail"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "customer-email"
  }, "Email"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "email",
    id: "customer-email",
    name: "email",
    value: emailInput,
    onChange: event => {
      setEmailInput(event.target.value);
    },
    required: true
  }));
}

/***/ }),

/***/ "./react/components/forms/FormInputCustomerPassword.jsx":
/*!**************************************************************!*\
  !*** ./react/components/forms/FormInputCustomerPassword.jsx ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormInputCustomerPassword)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../customer/CustomerContext.jsx */ "./react/components/customer/CustomerContext.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);

/**
 * FormInputCustomerPassword component
 *
 * Controlled input to collect a customer's password.
 */



function FormInputCustomerPassword({
  extraClassNames = ''
}) {
  const {
    passwordInput,
    setPasswordInput
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useContext)(_customer_CustomerContext_jsx__WEBPACK_IMPORTED_MODULE_1__.CustomerContext);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormInputCustomerPassword" + extraClassNames
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "customer-password"
  }, "Password"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "password",
    id: "customer-password",
    name: "password",
    value: passwordInput,
    onChange: event => {
      setPasswordInput(event.target.value);
    },
    required: true
  }));
}

/***/ }),

/***/ "./react/components/forms/FormStepVerificationCode.jsx":
/*!*************************************************************!*\
  !*** ./react/components/forms/FormStepVerificationCode.jsx ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormStepVerificationCode)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _FormInputCodePuncher_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormInputCodePuncher.jsx */ "./react/components/forms/FormInputCodePuncher.jsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);

/**
 * FormStepVerificationCode component
 *
 * Sends a verification code to a recipient's email and validates
 * the user's response.
 */



function FormStepVerificationCode({
  email,
  codeLength,
  onSuccess
}) {
  const [codeDigits, setCodeDigits] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(Array.from({
    length: codeLength
  }, () => ''));
  const [status, setStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('idle');
  const [error, setError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('');
  const formRef = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useRef)();
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useEffect)(() => {
    if (codeLength === codeDigits.join('').length) {
      // Automatically submit when all digits are entered.
      formRef.current.requestSubmit();
    }
  }, [codeDigits]);
  const handleSubmit = async event => {
    event.preventDefault();
    // Check verification code length.
    const verificationCodeString = codeDigits.join('');
    if (verificationCodeString.length !== codeLength) {
      setError(`Please enter the ${codeLength}-digit verification code.`);
    }
    // Check the verification code against the server.
    if ('loading' !== status) {
      setStatus('loading');
      await window.fetch(`${window.ptcTheme.api.v1}/mailing-lists/subscribe`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.ptcTheme.api.auth_nonce
        },
        body: JSON.stringify({
          email: email,
          verification_code: codeDigits.join(''),
          list_id: 'a2tBf2KrKnxBF66s',
          // Hard-coded. Sorry, Mom.
          nonce: window.ptcTheme.api.nonce
        })
      }).then(async res => {
        const body = await res.json();
        if (res.ok && 'success' === body?.status) {
          onSuccess(body);
        } else if (body?.message) {
          window.console.error(body.message);
          setError(body.message);
        } else {
          setError('Failed to verify email. Please try again.');
        }
      }).catch(err => {
        window.console.error(err);
        setError(err.message);
      }).finally(() => {
        setStatus('idle');
      });
    }
  };
  let innerContent = null;
  if ('loading' === status) {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Loading...");
  } else {
    innerContent = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
      ref: formRef,
      onSubmit: handleSubmit
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Confirm your email address"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, `To continue, please enter the ${length}-digit verification code sent to your ${email} email.`), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_FormInputCodePuncher_jsx__WEBPACK_IMPORTED_MODULE_1__["default"], {
      slots: codeDigits,
      onChange: setCodeDigits
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
      type: "submit"
    }, "Continue"));
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ptc-FormStepVerificationCode"
  }, error && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, error), innerContent);
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