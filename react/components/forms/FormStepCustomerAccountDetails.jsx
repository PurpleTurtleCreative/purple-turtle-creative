/**
 * FormStepCustomerAccountDetails component
 *
 * Collects a customer's account details for login or account
 * creation.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormStepCustomerAccountDetails({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormStepCustomerAccountDetails">
			{/* ... content goes here ... */}
		</div>
	);
}
