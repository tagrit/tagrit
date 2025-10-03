"use strict";
async function handleCompanyDeploymentSuccess(data) {
	const reloadPage = () => {
		// Reload page after either requests complete or timeout
		try {
			window.parent.location.reload();
		} catch (error) {
			console.log(error);
			window.location.reload();
		}
	};

	const fetchWithRetry = async (url, maxAttempts = 2) => {
		for (let attempt = 1; attempt <= maxAttempts; attempt++) {
			try {
				const response = await $.getJSON(url);
				if (response) {
					return response; // Success: return the response
				} else {
					throw new Error("Empty response");
				}
			} catch (error) {
				console.log(
					`Attempt ${attempt} failed for ${url}: ${error.message}`
				);
				if (attempt === maxAttempts) {
					throw error; // Throw the error after reaching max attempts
				}
			}
		}
	};

	const executeCronRequests = async (data) => {
		const cronUrls = data?.cron_urls;

		if (!cronUrls?.length) return reloadPage();

		try {
			for (let i = 0; i < cronUrls.length; i++) {
				const c_url = cronUrls[i];

				// Fetch with retry mechanism
				await fetchWithRetry(c_url, data?.cron_urls?.length ? 2 : 1);

				console.log(`Successfully fetched ${c_url}`);
			}
		} catch (error) {
			console.log("Error fetching cron URLs: ", error);
		}
	};

	try {
		// Add a timeout of 15 seconds for all requests
		const timeout = new Promise((_, reject) =>
			setTimeout(
				() => reject(new Error("Request timeout exceeded 15 seconds")),
				15000
			)
		);

		// Fetch cron URLs with retries and timeout
		const fetchCronTask = executeCronRequests(data);

		await Promise.race([fetchCronTask, timeout]);

		console.log("All cron requests completed or timed out.");
	} catch (error) {
		console.log("An error occurred: ", error);
	} finally {
		reloadPage();
	}
}

/**
 * Handles the company deployment response.
 * @param {Object} data - The response data.
 */
function handleCompanyDeployment(data) {
	if (data?.total_success > 0) {
		// Trigger cron to each deployed instance url
		handleCompanyDeploymentSuccess(data);
	}

	if (data.errors?.length) {
		data.errors.forEach(function (error) {
			alert_float("danger", error, 10000);
		});

		$(".company-status .fa-spin").removeClass("fa-spin");

		setTimeout(function () {
			try {
				window.parent.location.reload();
			} catch (error) {
				console.log(error);
			}
			window.location.reload();
		}, 8000);
	}
}

/**
 * Removes submenu items from the DOM.
 * It removes some menu/nav from the client side.
 */
function removeSubmenuItems() {
	let selectors =
		".section-client-dashboard>dl:first-of-type, .projects-summary-heading,.submenu.customer-top-submenu";
	document.querySelectorAll(selectors).forEach(function ($element) {
		$element.remove();
	});
	$(selectors)?.remove();
}

/**
 * Handles the company modal view.
 */
function handleCompanyModalView() {
	let slug = $(this).data("slug");

	let viewPane = $("#view-company-modal");
	if (viewPane.hasClass("modal")) viewPane.modal("show");
	else {
		viewPane.slideDown();
		viewPane.find(".close,.close-btn").click(function () {
			viewPane.slideUp();
		});
	}

	$('select[name="view-company"]')
		.selectpicker("val", slug)
		.trigger("change");

	try {
		let iframe = getCompanyViewerFrame();
		iframe.contentWindow.set_body_small();
	} catch (error) {
		console.log(error);
	}
}

/**
 * Handles the modal company change event.
 */
function handleModalCompanyChange() {
	let slug = $(this).val();
	if (!slug.length) $("#view-company-modal").modal("hide");
	magicAuth(slug);
}

/**
 * Get the company preview iframe
 * @returns object awokumadivad
 */
function getCompanyViewerFrame() {
	return document.querySelector("#company-viewer");
}

/**
 * Loads a company into the modal viewer.
 * @param {string} slug - The company slug.
 */
function magicAuth(slug) {
	let iframe = getCompanyViewerFrame();
	iframe.src = SAAS_MAGIC_AUTH_BASE_URL + slug;
	iframe.onload = function () {
		$(".first-loader").hide();
	};
	iframe.contentWindow?.NProgress?.start() || $(".first-loader").show();
}

/*
 * Debounce function to limit the frequency of function execution
 * @param {Function} func - The function to be debounced
 * @param {number} wait - The debounce wait time in milliseconds
 * @param {boolean} immediate - Whether to execute the function immediately
 * @returns {Function} - The debounced function
 */
function debounce(func, wait, immediate) {
	var timeout;
	return function () {
		var context = this,
			args = arguments;
		var later = function () {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
}

function slugifyTenantId(text) {
	return text
		.trim()
		.toLowerCase()
		.split(" ")[0]
		.substring(0, SAAS_MAX_SLUG_LENGTH)
		.replace(/[^a-z0-9]+/g, "-");
}
/*
 * Function to generate slug and check its availability
 */
function generateSlugAndCheckAvailability() {
	// Generate the slug from the input value
	let slug = slugifyTenantId($("input[name=slug]").val());

	let $statusLabel = $("#slug-check-label");

	if (!slug.length || !slug.replaceAll("-", "").length) {
		$statusLabel.html("");
		return;
	}

	let domain = slug + "." + SAAS_DEFAULT_HOST;

	// Set the generated slug as the input value
	$("input[name=slug]").val(slug);

	// Display a message indicating that availability is being checked
	$statusLabel.html("<i class='fa fa-spinner fa-spin tw-mr-1'></i>" + domain);

	const handleCheckResult = (data) => {
		let isAvailable = data?.available;

		// Update the label with the slug availability status
		$statusLabel.html(
			`<span class='text-${
				isAvailable ? "success" : "danger"
			}'>${domain}</span>`
		);
	};

	// Send an AJAX request to check the slug availability on the server
	$.getJSON(
		`${site_url}${SAAS_MODULE_NAME}/api/is_slug_available/${slug}`,
		handleCheckResult
	).fail((error, status, statusText) => {
		alert_float("danger", statusText, 5000);
		handleCheckResult({});
	});
}

/**
 * Function to bind and listen to the slug input field
 *
 * @param {string} formSelector The parent element selector for the inputs
 * @param {string} slugSourceInputSelector Optional element that should be used for auto generating the slug
 * @returns
 */
function bindAndListenToSlugInput(
	formSelector = "#add-company-form",
	slugSourceInputSelector = "#add-company-form input[name='name']"
) {
	if (!$(formSelector).length) {
		console.warn("provided select not exist:", formSelector);
		return;
	}

	let slugInputSelector = "input[name=slug]";

	// Inject the result placeholder HTML
	$(
		'<small id="slug-check-label" class="text-right tw-w-full tw-block tw-text-xs"></small>'
	).insertAfter(slugInputSelector);

	// Debounced event handler for company name input changes
	let debouncedGenerateSlugAndCheckAvailability = debounce(
		generateSlugAndCheckAvailability,
		500
	);

	// Generate slug from company name input
	if ($(slugSourceInputSelector).length)
		$(slugSourceInputSelector)
			.unbind("input")
			.on("input", function () {
				var companyName = $(slugSourceInputSelector).val();
				var slug = slugifyTenantId(companyName);
				$(slugInputSelector).val(slug).trigger("input");
			});

	// Check for availability of the slug
	$(formSelector + " " + slugInputSelector)
		.unbind("input")
		.on("input", debouncedGenerateSlugAndCheckAvailability);
}

/*
 * Function to check if the provided custom domain is available
 */
function checkCustomDomainAvailability(domainInput) {
	let domain = domainInput.val();

	let $statusLabel = $("#custom-domain-check-label");
	if (!$statusLabel.length) {
		// Inject the result placeholder HTML
		$(
			'<small id="custom-domain-check-label" class="text-right tw-w-full tw-block tw-text-xs"></small>'
		).insertAfter(domainInput);
		$statusLabel = $("#custom-domain-check-label");
	}

	if (!domain.length) {
		$statusLabel.html("");
		return;
	}

	// Display a message indicating that availability is being checked
	$statusLabel.html("<i class='fa fa-spinner fa-spin tw-mr-1'></i>" + domain);

	const handleCheckResult = (data) => {
		let isAvailable = data?.available;

		// Update the label with the slug availability status
		$statusLabel.html(
			`<span class='text-${
				isAvailable ? "success" : "danger"
			}'>${domain}</span>`
		);
	};

	const isValidDomainName = (supposedDomainName) => {
		return /^(?!-)[A-Za-z0-9-]+([\-\.]{1}[a-z0-9]+)*\.[A-Za-z]{2,6}$/i.test(
			supposedDomainName
		);
	};

	if (!isValidDomainName(domain)) {
		return handleCheckResult({availability: false});
	}

	// Send an AJAX request to check the domain availability on the server
	$.getJSON(
		`${site_url}${SAAS_MODULE_NAME}/api/is_custom_domain_available/${domain}`,
		handleCheckResult
	).fail((error, status, statusText) => {
		alert_float("danger", statusText, 5000);
		handleCheckResult({});
	});
}

function bindAndListenToCustomDomainInput() {
	$("input[name=custom_domain]")
		.off("change")
		.on("change", function () {
			let domain = $(this).val();
			if (domain.indexOf("://") !== -1) {
				domain = domain.split("://")[1] ?? "";
				$(this).val(domain);
			}
			checkCustomDomainAvailability($(this));
		});
}

// Funciton to show simple loading indicator when iframe content will leave
function SaaSShowLoadingIndicator() {
	// Check if the spinner already exists
	if (document.getElementById("saas-loading-spinner")) {
		return; // Exit if the spinner is already present
	}

	// Create the FontAwesome icon element
	const loadingIcon = document.createElement("i");
	loadingIcon.className = "fa fa-spinner fa-spin fa-2x"; // FontAwesome classes for spinner
	loadingIcon.id = "saas-loading-spinner"; // Assign an ID to the spinner

	// Set up styles to center the spinner
	loadingIcon.style.position = "fixed";
	loadingIcon.style.top = "50%";
	loadingIcon.style.left = "50%";
	loadingIcon.style.transform = "translate(-50%, -50%)";
	loadingIcon.style.zIndex = "9999"; // Ensure it's on top of other content

	// Add the spinner to the document body
	document.body.appendChild(loadingIcon);
}

$(document).ready(function () {
	$(".ps-container").insertAfter("#greeting");

	// If alert container, rearrange to show immediately after greetings i.e before ps-container
	if ($("#alerts").parent(".row").length) {
		$("#alerts").parent(".row").insertAfter("#greeting");
	}

	// Remove submenu (e.g., calendar and files)
	if (SAAS_CONTROL_CLIENT_MENU) removeSubmenuItems();

	// Hide the form initially
	if ($("#add-company-form").length) {
		$("#add-company-form").hide();

		// Show the form when the add button is clicked
		$(".add-company-btn").click(function () {
			$("#add-company-trigger").slideUp();
			$("#add-company-form").slideDown();
			$("#add-company-form [name='name']").trigger("input");
		});

		// Cancel button closes the form and shows the early UI
		$("#cancel-add-company").click(function () {
			$("#add-company-form").slideUp();
			$("#add-company-trigger").slideDown();
		});
	}

	// Show the edit form
	$(".company .dropdown-menu .edit-company-nav").click(function () {
		let $company = $(this).parents(".company");
		$company
			.find(".panel_footer, .info, .dropdown, .custom-domain-form")
			.slideUp();
		$company.find(".edit-form").slideDown();
		$company.find(".bootstrap-select").slideDown();
	});

	// Show the custom domain form
	$(".company .dropdown-menu .edit-custom-domain-nav").click(function () {
		let $company = $(this).parents(".company");
		$company.find(".panel_footer, .info, .dropdown, .edit-form").slideUp();
		$company.find(".custom-domain-form").slideDown();
	});

	// Cancel button closes the edit form and shows the early UI
	$(
		".company .edit-form .btn.close-btn, .company .custom-domain-form .btn.close-btn"
	).click(function () {
		let $company = $(this).parents(".company");
		$company.find(".edit-form, .custom-domain-form").slideUp();
		$company.find(".info, .panel_footer, .dropdown").slideDown();
	});

	// Render Saas view
	let view = SAAS_ACTIVE_SEGMENT;
	if (view) {
		$(".ps-view").hide();
		showSaasView(view);
	}

	// Function to show the specified Saas view
	function showSaasView(view) {
		$(view.replace("?", "#")).show();

		if (
			window.location.href.includes(view) ||
			window.location.pathname.replaceAll("/", "") == "clients"
		)
			$(".customers-nav-item-" + view.replace("?", "")).addClass(
				"active"
			);
	}

	// Worker helper for instant deployment of a company
	$.getJSON(site_url + "clients/companies/deploy", handleCompanyDeployment);

	// Company modal view
	$(".view-company").click(handleCompanyModalView);

	// Detect change in modal company list selector and react
	$(document).on("change", '[name="view-company"]', handleModalCompanyChange);

	// Click the first company by default if client is having only one.
	setTimeout(() => {
		let companyList = $("#companies:visible .company.active.autolaunch");

		// Ensure not in iframe while and magic auth i.e client bridge
		if (
			typeof SAAS_IS_MAGIC_AUTH !== "undefined" &&
			SAAS_IS_MAGIC_AUTH === true &&
			window.self !== window.top
		)
			return;

		if (
			companyList.length > 0 &&
			sessionStorage.getItem("autolaunched") !== "1"
		) {
			sessionStorage.setItem("autolaunched", "1");
			$(companyList[0]).find(".view-company").click();
		}
	}, 500);

	/** Subdomain checking for improved UX */
	bindAndListenToSlugInput();

	/** Custom domain checking */
	bindAndListenToCustomDomainInput();

	if (window.location.search.startsWith("?request_custom_")) {
		let searchParams = new URLSearchParams(window.location.search);
		$("[name=subject]").val(searchParams.get("title"));
		$("[name=message]").val(searchParams.get("message"));
	}

	// Show modals passed throug ?view-modal=modal-id query
	if (window.location.search.indexOf("view-modal") != -1) {
		let searchParams = new URLSearchParams(window.location.search);
		let modalSelector = "#" + searchParams.get("view-modal");
		let $modal = $(modalSelector);
		if (!$modal.length) $modal = $(modalSelector + "Modal");
		if ($modal.length) $modal.modal("show");
	}
});

/**
 * Class simulating the app_format_money method in PHP.
 *
 * Example usage:
 *
 * const currency = {
 *    symbol: '$',
 *    decimal_separator: '.',
 *    thousand_separator: ',',
 *    placement: 'before'
 * };
 *
 * const options = {
 *    removeDecimalsOnZero: 1, // Example option values
 *    decimalPlaces: 2,
 *    currency: currency
 * };
 *
 * const formatter = new AppFormatMoney(options);
 * const formattedAmount = formatter.format(1000, currency);
 * const formattedAmount = formatter.format(1000);
 * console.log(formattedAmount); // Output: "$ 1,000.00"
 */
class AppFormatMoney {
	constructor(options) {
		this.options = options;
	}

	format(amount, currency = null, excludeSymbol = false) {
		// Check whether the amount is numeric and valid
		if (!this.isNumeric(amount) && amount !== 0) {
			return amount;
		}

		if (currency === null) currency = this.options?.currency || {};

		if (amount === null) {
			amount = 0;
		}

		// Determine the symbol
		const symbol = !excludeSymbol ? currency.symbol : "";

		// Check decimal places
		const removeDecimalsOnZero = this.options.removeDecimalsOnZero || 0;
		const decimalPlaces = this.options.decimalPlaces || 2;
		const d =
			removeDecimalsOnZero === 1 && !this.isDecimal(amount)
				? 0
				: decimalPlaces;

		// Format the amount
		let amountFormatted = amount.toLocaleString(undefined, {
			minimumFractionDigits: d,
			maximumFractionDigits: d,
			useGrouping: true,
			decimalSeparator: currency.decimal_separator,
			groupSeparator: currency.thousand_separator,
		});

		// Maybe add the currency symbol
		const formattedWithCurrency =
			currency.placement === "after"
				? amountFormatted + "" + symbol
				: symbol + "" + amountFormatted;

		return formattedWithCurrency;
	}

	// Check if passed number is decimal
	isDecimal(val) {
		return typeof val === "number" && Math.floor(val) !== val;
	}

	// Check if passed value is numeric
	isNumeric(value) {
		return !isNaN(parseFloat(value)) && isFinite(value);
	}
}
