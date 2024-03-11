/**
 * FormCustomerAuthentication component
 *
 * Authenticates a customer session.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerAuthentication({ onSuccess }) {
	const [ status, setStatus ] = useState('idle');

	// @todo - Make tabbed component to elegantly switch between login and signup forms.

	return (
		<div className="ptc-FormCustomerAuthentication">
			<h2>Hello, cruel world!</h2>
		</div>
	);
}
