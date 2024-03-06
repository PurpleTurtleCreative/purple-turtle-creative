/**
 * FormCustomerLogin component
 *
 * Logs in an existing customer.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerLogin({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormCustomerLogin">
			{/* ... content goes here ... */}
		</div>
	);
}
