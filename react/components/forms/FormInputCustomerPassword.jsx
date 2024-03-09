/**
 * FormInputCustomerPassword component
 *
 * Controlled input to collect a customer's password.
 */

import { CustomerContext } from '../customer/CustomerContext.jsx';

import { useContext } from '@wordpress/element';

export default function FormInputCustomerPassword({ extraClassNames = '' }) {
	const { passwordInput, setPasswordInput } = useContext(CustomerContext);

	return (
		<div className={"ptc-FormInputCustomerPassword"+extraClassNames}>
			<label htmlFor="customer-password">Password</label>
			<input
				type="password"
				id="customer-password"
				name="password"
				value={passwordInput}
				onChange={(event) => { setPasswordInput(event.target.value) }}
				required
			/>
		</div>
	);
}
