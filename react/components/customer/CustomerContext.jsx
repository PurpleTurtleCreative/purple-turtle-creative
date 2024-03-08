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

	useEffect(() => {
		// Persist state when updates occur.
		sessionStorage.setItem('ptcCustomer', authToken);
	}, [ authToken ]);

	const context = {

		authToken,

		emailInput,
		setEmailInput,

		passwordInput,
		setPasswordInput,

		isSignedIn: () => {
			return ( !! context.authToken );
		},

		signin: async () => {
			// use context.emailInput and context.passwordInput.
			return await window.fetch()
				.then(res => res.json())
				.then(res => {
					// request JWT auth token from server.
					// on success, set auth token to authenticate future requests.
					setAuthToken(res.data.authToken);
					return true;
				})
				.catch(err => {
					return false;
				});
		},

		signup: async () => {
			// use context.emailInput and context.passwordInput.
			return await window.fetch()
				.then(res => res.json())
				.then(res => {
					// request JWT auth token from server.
					// on success, set auth token to authenticate future requests.
					setAuthToken(res.data.authToken);
					return true;
				})
				.catch(err => {
					return false;
				});
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
			// check if signed in.
			// redirect to checkout session URL.
		},

		goToBillingPortal: () => {
			// check if signed in.
			// redirect to customer billing portal URL.
		}
	};

	return (
		<CustomerContext.Provider value={context}>
			{children}
		</CustomerContext.Provider>
	);
}
