/**
 * FormCustomerAuthentication component
 *
 * Authenticates a customer session.
 */

import FormCustomerCreateAccount from './FormCustomerCreateAccount.jsx';

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerAuthentication({ onSuccess }) {
	const [ status, setStatus ] = useState('idle');

	// @todo - Make tabbed component to elegantly switch between login and signup forms.

	return (
		<div className="ptc-FormCustomerAuthentication">
			<h2>Create or Log In to Your Account</h2>
			<p>Please create an account or sign in to manage your software licenses and billing information.</p>
			<FormCustomerCreateAccount />
		</div>
	);
}
