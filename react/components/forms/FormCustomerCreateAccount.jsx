/**
 * FormCustomerCreateAccount component
 *
 * Creates a new customer account.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormCustomerCreateAccount({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormCustomerCreateAccount">
			{/* ... content goes here ... */}
		</div>
	);
}
