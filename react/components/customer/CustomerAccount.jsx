/**
 * CustomerAccount component
 *
 * Displays customer account information.
 */

import { useEffect, useState } from '@wordpress/element';

export default function CustomerAccount({ formData, setFormData }) {
	const [ status, setStatus ] = useState('idle');

	useEffect(() => {

		// do something...

		return () => {
			// cleanup something...
		};
	}, []);

	return (
		<div className="ptc-CustomerAccount">
			{/* ... content goes here ... */}
		</div>
	);
}
