"use strict";

/**
 * Initializes the form script for the package form.
 * Enhancements include handling of pool additions and removals,
 * database scheme changes, live filtering of shared settings,
 * and form validation.
 */
function saasPackageFormScript() {
	let $pools = $("#pools");
	let $dbSchemeSelect = $('select[name="db_scheme"]');
	let $dbPools = $(".db_pools");
	let $intervalSelection = $("select[name='metadata[invoice][recurring]']");

	// Handle pool addition
	$dbPools.on("click", "#add-pool", function () {
		let template = $(".pool-template").clone();
		template.append(
			'<div class="tw-mb-4"><button type="button" class="btn pull-right btn-danger remove-pool"><i class="fa fa-times"></i></button></div>'
		);
		template.removeClass("pool-template");
		$pools.append(template);
		$("#pools label").remove(); // Remove label from the list
	});

	// Handle pool removal
	$pools.on("click", ".remove-pool", function () {
		$(this).closest(".tw-flex").remove();
	});

	// Handle database scheme change
	$dbSchemeSelect.on("change", function () {
		let showDbPools = ["single_pool", "shard"].includes($(this).val());
		$dbPools.toggleClass("hidden", !showDbPools);
	});

	// Handle interval selection change and show custom interval if neccessary
	$intervalSelection.on("change", function () {
		if ($(this).val() === "custom") {
			$(".recurring_custom").removeClass("hide");
			$("[name='metadata[invoice][repeat_type_custom]']").attr(
				"required",
				true
			);
			$("[name='metadata[invoice][repeat_every_custom]']").attr(
				"required",
				true
			);
		} else {
			$(".recurring_custom").addClass("hide");
			$("[name='metadata[invoice][repeat_type_custom]']").removeAttr(
				"required"
			);
			$("[name='metadata[invoice][repeat_every_custom]']").removeAttr(
				"required"
			);
		}
	});

	// Lifetime deal switch
	$("#is_liftetime_deal").on("change", function () {
		if ($(this).prop("checked")) {
			$("[name='metadata[invoice][recurring]']")
				.val("custom")
				.trigger("change");
			$("[name='metadata[invoice][repeat_type_custom]']")
				.val("year")
				.trigger("change");
			$("[name='metadata[invoice][repeat_every_custom]']")
				.val("100")
				.trigger("change");
		} else {
			$("[name='metadata[invoice][recurring]']")
				.val("")
				.trigger("change");
			$("[name='metadata[invoice][repeat_type_custom]']")
				.val("")
				.trigger("change");
			$("[name='metadata[invoice][repeat_every_custom]']")
				.val("")
				.trigger("change");
		}
	});

	// Live filtering of shared settings
	saasFilterItems("#sharedfilter", ".shared_settings .item");

	// Initialize editor and form validation
	init_editor("#description", _simple_editor_config());
	appValidateForm($("#packages_form"), {
		name: "required",
		price: "required",
		"metadata[invoice][recurring]": "required",
	});

	$("#advance-settings-toggle").on("click", function () {
		$(".advance-settings").toggleClass("hidden");
		return false;
	});

	// Shared settings input group control and helper
	$(".mask-checkbox input,.enforce-checkbox input").on("change", function () {
		if ($(this).prop("checked"))
			$(this)
				.closest(".share-row")
				.find(".share-checkbox input")
				.prop("checked", true);
	});

	$(".share-checkbox input").on("change", function () {
		if (!$(this).prop("checked")) {
			$(this)
				.closest(".share-row")
				.find(".mask-checkbox input,.enforce-checkbox input")
				.prop("checked", false);
		}
	});

	$("[name='metadata[auto_remove_inactive_instance]']")
		.on("change", function () {
			let $deps = $(".auto_remove_inactive_instance.deps");
			if ($(this).val() === "yes") {
				$deps.show();
			} else {
				$deps.hide();
			}
		})
		.trigger("change");
}

/**
 * Search list of items
 * @param {string} inputSelector The search input field selector
 * @param {string} itemsSelector The items list class selector
 */
function saasFilterItems(
	inputSelector = ".perfex_saas_filter",
	itemsSelector = ".perfex_saas_filterables .item"
) {
	// Live filtering of tables
	let $saasFilter = $(inputSelector);
	if ($saasFilter.length) {
		$saasFilter.each((index, filterInput) => {
			let $filterInput = $(filterInput);
			let $filterableItems = $filterInput.parent().find(itemsSelector);
			let searchTimeout;
			$filterInput.off("input").on("input", function () {
				clearTimeout(searchTimeout); // Clear any existing timeout
				searchTimeout = setTimeout(function () {
					let value = $filterInput.val().toLowerCase();
					$filterableItems.filter(function () {
						$(this).toggle(
							$(this).text().toLowerCase().indexOf(value) > -1
						);
					});
				}, 500);
			});
		});
	}
}

/**
 * Initializes the form script for the company form.
 * Enhancements include handling of database scheme changes,
 * testing database connections, and form validation.
 */
function saasCompanyFormScript() {
	// Handle database scheme change
	let $dbSchemeSelect = $('select[name="db_scheme"]');
	let $dbPools = $(".db_pools");
	$dbSchemeSelect.on("change", function () {
		let showDbPools = ["single_pool", "shard"].includes($(this).val());
		$dbPools.toggleClass("hidden", !showDbPools);
	});

	// Form validation
	appValidateForm($("#companies_form"), {
		name: "required",
		clientid: "required",
		db_scheme: "required",
		"db_pools[host]": {
			required: {
				depends: function (element) {
					return $dbSchemeSelect.val() === "shard";
				},
			},
		},
		"db_pools[user]": {
			required: {
				depends: function (element) {
					return $dbSchemeSelect.val() === "shard";
				},
			},
		},
		"db_pools[dbname]": {
			required: {
				depends: function (element) {
					return $dbSchemeSelect.val() === "shard";
				},
			},
		},
	});
}

/**
 * Sets the active menu item in the master admin sidebar for SaaS menu.
 * It makes saas endpoint parent active .i.e make packages menu active when viewing the create package form.
 * Create package form link is not on the menu and thus prefex will not highlight is as active.
 *
 */
function saasAdminActiveMenu() {
	// Check for active class in sidebar links
	let $activeSaasLink = side_bar.find('li > a[href="' + location + '"]');
	if (!$activeSaasLink.length) {
		let saasMenuSelector = ".menu-item-" + SAAS_MODULE_NAME;

		let $saasDropdownMenu = side_bar.find(saasMenuSelector);

		$(saasMenuSelector + " .collapse").addClass("in");

		let saasSubMenus = $saasDropdownMenu.find("li");
		for (let index = 0; index < saasSubMenus.length; index++) {
			let $link = $(saasSubMenus[index]).find("a");
			let linkPath = $link.attr("href").split("/").pop(); // i.e "packages" in "packages/create"
			let currentLocationBase = location.href
				.split(SAAS_MODULE_NAME + "/")
				.pop();

			if (linkPath?.length && currentLocationBase.startsWith(linkPath)) {
				$activeSaasLink = $link;
				break;
			}
		}

		if ($activeSaasLink.length) {
			$activeSaasLink.parent("li").not(".quick-links").addClass("active");
			$saasDropdownMenu.addClass("active");
			// Set aria expanded to true
			$saasDropdownMenu.prop("aria-expanded", true);
			$activeSaasLink
				.parents("ul.nav-second-level")
				.prop("aria-expanded", true);
		}
	}
}

/**
 * Handles the package limit toggle functionality.
 */
function saasAdminHandlePackageLimitToggle() {
	// Add necessary classes for styling
	$("#package-qouta label").addClass(
		"tw-flex tw-justify-between tw-items-center"
	);

	let $packageQuota = $("#package-qouta");

	// Handle click event on anchor tags inside package-qouta
	$packageQuota.on("click", "a", function () {
		let $group = $(this).closest(".input-group");
		let $input = $group.find("input");

		// Enable the input and switch between finite and infinite values
		$input.removeAttr("readonly");

		if ($input.val() == "-1") {
			// Switch from infinite to finite value
			$input.val($input.data("finite-value") || "1");
			$(this).hide();
			$group.find("a.mark_infinity").show();
		} else {
			// Switch from finite to infinite value
			if ($input.val() != "-1") {
				$input.data("finite-value", $input.val());
			}
			$input.val("-1");
			$input.attr("readonly", "readonly");
			$(this).hide();
			$group.find("a.mark_metered").show();
		}
	});
}

/**
 * Deploys companies using AJAX request.
 */
function saasAdminDeployService() {
	$.getJSON(
		admin_url +
			SAAS_MODULE_NAME +
			"/companies/deploy/" +
			(typeof perfex_saas_company_id == "undefined"
				? ""
				: perfex_saas_company_id),
		function (data) {
			if (data?.total_success > 0) {
				// Trigger cron to each deployed instance url
				try {
					if (data?.cron_urls?.length) {
						for (let i = 0; i < data.cron_urls.length; i++) {
							const c_url = data.cron_urls[i];
							$.getJSON(c_url);
						}
					} else {
						$.getJSON(site_url + "cron/index");
					}
				} catch (error) {
					console.log(error);
				}
			}
			setTimeout(function () {
				$(".tenants-table .btn-dt-reload").click();
			}, 1000);
		}
	);
}

/**
 * Function to copy text to clipboard
 * @param {string} text The string to copy to clipboard
 * @returns String
 */
function SaaSCopyToClipboard(text) {
	// Create a temporary input element to hold the link text
	var tempInput = document.createElement("input");
	tempInput.value = text;
	document.body.appendChild(tempInput);

	// Select the link text
	tempInput.select();
	tempInput.setSelectionRange(0, text.length);

	// Copy the selected text to the clipboard
	document.execCommand("copy");

	// Remove the temporary input element
	document.body.removeChild(tempInput);

	// Optionally, provide some visual feedback to indicate the link is copied
	return true;
}

/**
 * Initializes NProgress for page loading progress bar.
 */
function initNProgress() {
	if (typeof NProgress === "undefined") return;

	// Increase randomly
	let interval = setInterval(function () {
		NProgress.inc();
	}, 1000);

	const pageLoaded = () => {
		clearInterval(interval);
		NProgress.done();
	};

	const startNgProgress = () => {
		if (NProgress.status <= 0) NProgress.start();
	};

	// Trigger finish when page fully loaded
	addEventListener("load", (event) => {
		pageLoaded();
	});

	addEventListener("pageshow", (event) => {
		pageLoaded();
	});

	// Trigger bar when exiting the page
	addEventListener("pagehide", (event) => {
		startNgProgress();
	});
	addEventListener("beforeunload", (event) => {
		startNgProgress();
	});

	// Show the progress bar
	startNgProgress();
}

function saasFormRepeat() {
	let $repeatGroup = $("[data-repeat-list]");
	$repeatGroup.each((index, group) => {
		let $group = $(group);
		let $groupInputs = $group.find("[data-repeat-list-input]");
		let $groupTarget = $($group.attr("data-repeat-list"));

		$group.on("click", "button", function () {
			let template = "";

			let repeatId = $group.find("[data-repeat-list-id]").val() ?? 0;

			let hasEmptyField = false;
			$groupInputs.each((i, input) => {
				if (!$(input).val().length) {
					hasEmptyField = true;
					$(input).parent().addClass("has-error");
				}
			});

			if (
				!hasEmptyField &&
				!$group.find('[aria-invalid="true"]').length
			) {
				$groupInputs.each((i, input) => {
					let $input = $(input);
					let $inputWrapper = $input.parent();
					$inputWrapper.removeClass("has-error");
					let wrapperClassName = $inputWrapper
						.attr("class")
						.replace(
							"input-group",
							$inputWrapper.data("class") ?? ""
						);

					let name = $input.attr("data-repeat-list-input");
					if (repeatId) {
						name = name.replace("$data-repeat-list-id", repeatId);
						$groupTarget
							.find(`[data-repeat-id="${repeatId}"]`)
							.remove();
					}

					let value = $input.val();
					let text = $input.find(`[value="${value}"]`).text() ?? "";
					text = text.length
						? text
						: value +
						  " " +
						  ($inputWrapper.find(".input-group-addon").text() ??
								"");
					template += `<div class="${wrapperClassName}"><input type="hidden" name="${name}" value="${value}" />${text}</div>`;
				});

				$groupTarget.append(
					`<div class="${$group.attr(
						"class"
					)}" data-repeat-id="${repeatId}">${template} <button class="btn btn-danger remove-parent tw-ml-2" type="button">-</button></div>`
				);
			}
		});
	});

	$(document).on("click", ".btn.remove-parent", function () {
		$(this).parent().remove();
	});
}

$(function () {
	// Insert the top statistics to Saas stats container for improved data presentation on the dashboard
	if (
		$("#widget-saas_top_stats").length &&
		$('[data-container="top-12"]').length
	) {
		$('[data-container="top-12"]').prepend($("#widget-saas_top_stats"));
	}

	if (!SAAS_IS_TENANT) {
		// Handle Saas menu styling for imporeved UX
		if (location.pathname.includes(SAAS_MODULE_NAME)) {
			saasAdminActiveMenu(SAAS_MODULE_NAME);
		}

		// Perfex saas table filter. This script enables auto-filtering for Saas invoice when the invoice page is visited with ?ps search by triggering the table filter
		if (window.location.search.replace("?", "") == SAAS_FILTER_TAG) {
			$(".dataTables_filter input[type=search]")
				.val(SAAS_FILTER_TAG)
				.trigger("keyup");
		}

		// Run deploy service. This is a common helper for sharing instance deployment load
		setTimeout(() => {
			saasAdminDeployService();
		}, 1000);

		// Package quota
		if ($("#package-qouta")) {
			saasAdminHandlePackageLimitToggle();
		}

		// Copy to clipboard
		$(".copy-to-clipboard").on("click", function () {
			SaaSCopyToClipboard($(this).data("text"));
			alert_float("success", $(this).data("success-text"), "");
		});

		// Detect and bind any list search using default parameters
		saasFilterItems();
	}

	if (SAAS_IS_TENANT) {
		if ($("#settings-form")) {
			// Remove the upgrade button and system info from the instance panel.
			// This is a fallback method as its expected to be removed in the backend.
			let c_to_remove = document.querySelectorAll(
				"#settings-form > div > div.col-md-3 > a"
			);
			c_to_remove.forEach((e) => {
				e.remove();
			});
		}

		// Remove enforced shared fields from UI
		for (let i = 0; i < SAAS_ENFORCED_SHARED_FIELDS.length; i++) {
			const field = SAAS_ENFORCED_SHARED_FIELDS[i];
			$(`[app-field-wrapper="settings[${field}]"]`).remove();
			$(`[name="settings[${field}]"]`)?.closest(".form-group")?.remove();
			$(`[name="${field}"]`)?.closest(".form-group")?.remove();
		}

		// Remove show help settings fallback
		let showHelpSettingsLabel = $("label[for=show_help_on_setup_menu]");
		if (showHelpSettingsLabel.length) {
			// First remove the next underline to the form group
			showHelpSettingsLabel.parent("div.form-group").next("hr").remove();
			// Then remove the div
			showHelpSettingsLabel.parent("div.form-group").remove();
		}

		initNProgress();
	}
});
