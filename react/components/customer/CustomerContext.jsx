/**
 * Customer context
 *
 * Manages customer authentication and state data.
 */

import { createContext, useEffect, useState } from '@wordpress/element';

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

	const resErrorAlreadyProcessing = {
		status: 'error',
		code: 400,
		message: 'Already processing another request.',
		data: null,
	};

	const authenticate = async ( action, params = {} ) => {
		if ( 'loading' !== processingStatus ) {
			setProcessingStatus('loading');
			return await window.fetch(
				`${window.ptcTheme.api.v1}/customer/authenticate`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.ptcTheme.api.auth_nonce,
					},
					body: JSON.stringify({
						action,
						email: context.emailInput,
						password: context.passwordInput,
						nonce: window.ptcTheme.api.nonce,
						...params,
					}),
				})
				.then(async (res) => {

					const body = await res.json();

					if ( res.ok && body?.data?.authToken ) {
						setAuthToken(body.data.authToken);
					}

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
			return ( !! authToken );
		},

		login: async ( params = {} ) => {
			return await authenticate('login', params);
		},

		signup: async ( params = {} ) => {
			return await authenticate('signup', params);
		},

		logout: () => {
			setAuthToken(''); // reset state.
		},

		verifyEmail: async () => {
			if ( 'loading' !== processingStatus ) {
				setProcessingStatus('loading');
				return await window.fetch(
					`${window.ptcTheme.api.v1}/mailing-lists/subscribe`,
					{
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': window.ptcTheme.api.auth_nonce,
						},
						body: JSON.stringify({
							email: context.emailInput,
							list_id: 'a2tBf2KrKnxBF66s', // Hard-coded. Sorry, Mom.
							nonce: window.ptcTheme.api.nonce,
						}),
					})
					.then(async (res) => {

						if ( ! res.ok ) {
							throw `Failed to verify email. Error: ${res.statusText}`;
						}

						const body = await res.json();
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
				return resErrorAlreadyProcessing;
			}
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
