/**
 * FormInputCustomerEmail component
 *
 * Controlled input to collect a customer's email address.
 */

import { CustomerContext } from '../customer/CustomerContext.jsx';

import { useContext } from '@wordpress/element';

export default function FormInputCustomerEmail() {
	const { emailInput, setEmailInput } = useContext(CustomerContext);

	return (
		<div className="ptc-FormInputCustomerEmail">
			<label htmlFor="customer-email">Email</label>
			<input
				type="email"
				id="customer-email"
				name="email"
				value={emailInput}
				onChange={(event) => { setEmailInput(event.target.value) }}
				required
			/>
		</div>
	);
}
