/**
 * FormStepVerificationCode component
 *
 * Sends a verification code to a recipient's email and validates
 * the user's response.
 */

import { useEffect, useState } from '@wordpress/element';

export default function FormStepVerificationCode({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-FormStepVerificationCode">
			{/* ... content goes here ... */}
		</div>
	);
}
