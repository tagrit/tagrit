"use strict";
/**
 * Rmove module upgrade needed badge in tenan admin.
 * The non assigned module can often need upgrade which we do not want to show to the tenants
 */
document
	.querySelectorAll("#setup-menu-item span.badge")
	.forEach(function (element) {
		if (element) {
			element.remove();
		}
	});
document.querySelectorAll(".menu-item-modules").forEach(function (element) {
	if (element) {
		element.remove();
	}
});
