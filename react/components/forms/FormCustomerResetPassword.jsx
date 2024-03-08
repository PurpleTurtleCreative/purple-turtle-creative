/**
 * FormCustomerResetPassword component
 *
 * Resets a customer's password.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerResetPassword({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormCustomerResetPassword">
			{/* ... content goes here ... */}
		</div>
	);
}
