/**
 * FormCustomerAuthentication component
 *
 * Authenticates a customer session.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerAuthentication({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormCustomerAuthentication">
			{/* ... content goes here ... */}
		</div>
	);
}
