/**
 * FormInputCodePuncher component
 *
 * Controlled input to collect an array of digits.
 */

import { useRef } from '@wordpress/element';

export default function FormInputCodePuncher({ slots, onChange }) {
	const inputRefs = Array.from({ length: slots.length }, () => useRef());

	const handleInputChange = (index, value) => {
		if ( 1 === value.length && /^\d$/.test(value) ) {
			const updatedSlots = [ ...slots ];
			updatedSlots[index] = value;
			onChange(updatedSlots);
			if ( index < slots.length - 1 ) {
				inputRefs[index + 1].current.focus(); // Focus next slot.
			}
		}
	};

	const handleKeyDown = (index, event) => {
		if ( 'Backspace' === event.key && index > 0 ) {
			const updatedSlots = [ ...slots ];
			updatedSlots[index - 1] = ''; // Clear the previous slot.
			inputRefs[index - 1].current.focus(); // Focus previous slot.
		}
	};

	return (
		<div className="ptc-FormInputCodePuncher">
			<fieldset>
				<legend>Verification Code</legend>
				<div className="code-puncher">
					{
						slots.map((digit, index) => (
							<input
								key={index}
								ref={inputRefs[index]}
								name={`code_punch_digits[${index}]`}
								type="text"
								inputMode="numeric"
								maxLength="1"
								value={digit}
								onChange={(e) => handleInputChange(index, e.target.value)}
								onKeyDown={(e) => handleKeyDown(index, e)}
								aria-label={`Digit ${index + 1}`}
							/>
						))
					}
				</div>
			</fieldset>
		</div>
	);
}
