/**
 * Customer context
 *
 * Manages customer authentication and state data.
 */

import { createContext, useState } from '@wordpress/element';

export const CustomerContext = createContext(false);

export function CustomerContextProvider({ children }) {
	// Only one customer allowed due to persisting global.
	const [ authToken, setAuthToken ] = useState(
		sessionStorage.getItem('ptcCustomer') || ''
	);
	// Form input controls.
	const [ emailInput, setEmailInput ] = useState('');
	const [ passwordInput, setPasswordInput ] = useState('');
	// State signals.
	const [ processingStatus, setProcessingStatus ] = useState('idle'); // idle, loading.

	useEffect(() => {
		// Persist state when updates occur.
		sessionStorage.setItem('ptcCustomer', authToken);
	}, [ authToken ]);

	const authenticate = async ( action ) => {
		if ( 'idle' === processingStatus ) {
			setProcessingStatus('loading');
			return await window.fetch(
				/* api endpoint url */,
				{
	        method: 'POST',
	        headers: {
	          'Content-Type': 'application/json',
	        },
	        body: JSON.stringify({
	        	action,
	          email: context.emailInput,
	          password: context.passwordInput,
	        }),
	      })
				.then(res => {

					if ( ! res.ok ) {
						throw 'Signin failed: ' + res.statusText;
					}

					body = await res.json();
					setAuthToken(body.data.authToken);

					return body;
				})
				.catch(err => {
					return {
						status: 'error',
						code: 500,
						message: err,
						data: null,
					};
				})
				.finally(() => {
					setProcessingStatus('idle');
				});
		} else {
			return {
	      status: 'error',
	      code: 400,
	      message: 'Already processing another request.',
	      data: null,
	    };
		}
	};

	// Public context.
	const context = {

		emailInput,
		setEmailInput,

		passwordInput,
		setPasswordInput,

		isLoggedIn: () => {
			return ( !! authToken );
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

		resetPassword: async () => {
			// use context.passwordInput.
			return await window.fetch()
				.then(res => res.json())
				.then(res => {
					return res.data;
				})
				.catch(err => {
					return null;
				});
		},

		fetchCustomerData: async () => {
			// requires authToken.
			return await window.fetch()
				.then(res => res.json())
				.then(res => {
					return res.data;
				})
				.catch(err => {
					return null;
				});
		},

		goToCheckout: ( productId, planId ) => {
			// requires authToken.
			// redirect to checkout session URL.
		},

		goToBillingPortal: () => {
			// requires authToken.
			// redirect to customer billing portal URL.
		}
	};

	return (
		<CustomerContext.Provider value={context}>
			{children}
		</CustomerContext.Provider>
	);
}
