"use strict";

// Define the MpesaPay object with various properties and methods
const mpesaPay = {
	modal: null, // Reference to the modal element
	submitCallback: null, // Callback function for form submission
	timeoutMinutes: 2.0, // Timeout duration in minutes
	notificationCallback: alert, // Callback function for displaying notifications
	timer: null, // Reference to the timer element
	timerInterval: null, // Interval ID for the timer
	phoneInput: null, // Reference to the phone number input field
	submitButton: null, // Reference to the submit button
	conversionBlock: null, // Reference to the conversion block element
	amount: 0, // Amount for payment
	amountInUSD: 0, // Amount in USD
	currency: "KES", // Currency code
	usd: "USD", // USD currency code

	// Method to close the modal
	closeModal() {
		this.modal.classList.remove("d-flex", "show");
		this.modal.classList.add("fade");
	},

	// Method to open the modal
	openModal() {
		this.modal.classList.remove("fade");
		this.modal.classList.add("show", "d-flex");
	},

	// Initialization method
	init(config) {
		this.modal = document.getElementById("mpesa-modal");
		this.submitCallback = config.submitCallback;
		this.notificationCallback = config.notificationCallback;
		this.timeoutMinutes = config.timeoutMinutes;
		this.timer = document.getElementById("mpesa-timer");
		this.phoneInput = document.querySelector(
			"#mpesa-modal input[name=mpesa_phone_number]"
		);
		this.submitButton = document.getElementById("pay-with-stk-push");
		this.conversionBlock = document.getElementById("conversion-block");
		this.amount = config.amount;
		this.amountInUSD = config.amountInUSD ?? 0;

		// Set the amount text and enable/disable submit button based on the amount
		if (this.amount > 0) {
			let amountText =
				this.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,") +
				" " +
				this.currency;
			document.getElementById("mpesa-amount").innerText = amountText;
			document.getElementById("mpesa-amount-2").innerText = amountText;

			this.submitButton.removeAttribute("disabled");

			// Display conversion rate if amount is in USD
			if (this.amountInUSD > 0) {
				this.conversionBlock.innerText = `1 ${this.usd} = ${(
					this.amount / this.amountInUSD
				).toFixed(2)} ${this.currency}`;
			}
		}
	},

	// Initialize the timer for payment timeout
	initTimer(timeoutCallback) {
		if (!this.timer) return;

		let currentTime = this.timeoutMinutes;

		this.timerInterval = setInterval(() => {
			currentTime = currentTime - 0.0166667;

			if (currentTime <= 0) {
				this.timer.classList.add("d-none");
				clearInterval(this.timerInterval);

				// Invoke the timeout callback function
				if (timeoutCallback) timeoutCallback();

				return;
			}

			// Update the timer display
			this.timer.querySelector("#time").innerText =
				(currentTime < 1
					? parseInt(currentTime * 60).toString()
					: parseFloat(currentTime).toFixed(2)) +
				(currentTime < 1 ? " seconds" : " minutes");
		}, 1000);

		// Show the timer element
		this.timer.classList.remove("d-none");
		this.submitButton.classList.add("d-none");
	},

	// Enable user actions (submit button, phone input)
	enableActions() {
		this.submitButton.removeAttribute("disabled");
		this.phoneInput.removeAttribute("disabled");
		this.submitButton.classList.remove("loading", "hourglass", "d-none");
		this.conversionBlock.classList.remove("d-none");
		if (this.timer) this.timer.classList.add("d-none");
	},

	// Disable user actions (submit button, phone input)
	disableActions() {
		this.submitButton.setAttribute("disabled", "disabled");
		this.phoneInput.setAttribute("disabled", "disabled");
		this.submitButton.classList.add("loading", "hourglass");
		this.conversionBlock.classList.add("d-none");
	},

	// Reset the payment process
	reset() {
		this.enableActions();
		this.currentTime = this.timeoutMinutes;
		if (this.timerInterval) clearInterval(this.timerInterval);
	},

	// Method to handle payment with STK push
	pay_with_stk_push() {
		this.phoneInput.value = this.phoneInput.value
			.replaceAll(" ", "")
			.replaceAll("+", "");

		let phoneNumber = this.phoneInput.value;

		if (!phoneNumber) {
			// Display a warning notification for invalid phone number
			this.notificationCallback("Invalid phone number", "warning");
			return;
		}

		if (!this.submitCallback) {
			// Display a warning notification for invalid submit callback
			this.notificationCallback("Invalid submit callback", "warning");
			return;
		}

		// Invoke the submit callback function with the phone number
		this.submitCallback(phoneNumber);
	},
};

/**
 * Function to initiate Mpesa STK push payment
 *
 * @param {string} formSelector Form selector i.e "#pay-form"
 * @param {string} verifyEndpoint Endpoint to verify the status of the payment
 * @param {string} currencyCode The currency code for payment
 * @param {float} oneUsdInKes Custom USD value of one KES
 * @returns
 */
function pay_with_mpesa_stk_push(
	formSelector,
	verifyEndpoint,
	currencyCode,
	oneUsdInKes,
	totalAmountLeftToPay
) {
	let amount = $("[name=amount]");
	amount = $("[name=amount]").length ? amount.val() : totalAmountLeftToPay;
	amount = parseFloat(amount);

	let checkInterval;

	// Function to check the payment status periodically
	const checkMpesaStatus = (txnId) => {
		let poolInterval = setInterval(async () => {
			let resp = await fetch(verifyEndpoint + txnId);
			if (resp.status) {
				resp = await resp.json();
				if (resp.id) {
					// Notify the user with the response message
					notify(resp.message, resp.success ? "success" : "");

					if (resp.success)
						setTimeout(() => {
							window.location.reload();
						}, 5000);
					else {
						clearInterval(checkInterval);
						mpesaPay.reset();
					}
				}
			}
		}, 15000);

		return poolInterval;
	};

	// Initialize the MpesaPay object
	mpesaPay.init({
		timeoutMinutes: 5,
		amount: currencyCode == "USD" ? amount * oneUsdInKes : amount,
		amountInUSD: currencyCode == "USD" ? amount : 0,
		notificationCallback: notify,
		submitCallback: (phoneNumber) => {
			mpesaPay.disableActions();

			// Insert the phone number into the form
			if (!$(formSelector + " [name=phone_number]").length) {
				$(formSelector).append(
					`<input type="hidden" name="phone_number" value="${phoneNumber}" />
           <input type="hidden" name="make_payment" value="Pay now" />`
				);
			}

			// Update the phone number
			$("[name=phone_number]").val(phoneNumber);

			ajaxSubmitForm(
				formSelector,
				(json, statusText) => {
					// Show the message only when there is an error (to maintain a smooth process)
					if (!json.success) notify(json.message);

					if (json.success && json.ref_id) {
						checkInterval = checkMpesaStatus(json.ref_id);

						// Define the timeout callback function
						let timeoutCallback = () => {
							clearInterval(checkInterval);
							alert(
								"We can't verify your payment at this time. Please check your invoice after a few minutes in case you have completed the payment."
							);
							window.location.reload();
						};

						// Initialize the timer for payment timeout
						mpesaPay.initTimer(timeoutCallback);
					} else {
						// Enable user actions
						mpesaPay.enableActions();
					}
				},
				(xhr, textStatus, errorThrown) => {
					notify(errorThrown);

					// Enable user actions
					mpesaPay.enableActions();
				}
			);
		},
	});

	// Open the Mpesa modal
	mpesaPay.openModal();
	return;
}

/**
 * Function to submit a form using AJAX
 *
 * @param {string} formSelector Form selector i.e "#pay-form"
 * @param {function} successCallback Method to call on success
 * @param {function} errorCallback Callback method on error
 */
function ajaxSubmitForm(formSelector, successCallback, errorCallback) {
	$.post(
		$(formSelector).attr("action"),
		$(formSelector).serialize(),
		successCallback,
		"json"
	).fail(function (xhr, textStatus, errorThrown) {
		if (errorCallback) errorCallback(xhr, textStatus, errorThrown);
	});
}

/**
 * Function to display notifications
 *
 * @param {string} message The message or notice
 * @param {string} type Type of the message i.e success , danger, warning
 */
function notify(message, type) {
	alert_float(type == "success" ? type : "danger", message, 20000);
}
