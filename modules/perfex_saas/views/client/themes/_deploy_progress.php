<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
"use strict";
const splash_theme = "<?= get_option('perfex_saas_deploy_splash_screen_theme'); ?>";
const statusUrl = site_url + "clients/companies/deploy?status=1"; // Replace with your actual URL
const progressChecks = new Set();
let template = `
        <div id="progress-container" class="panel-body col-md-6 col-md-offset-3 tw-bordered tw-flex tw-flex-col tw-justify-center">
            <h2 class="tw-mb-8"><?= _l('perfex_saas_deploying_page_title'); ?></h2>
            <div id="progress-checks"></div>
            <div id="loading" class="loading tw-w-full tw-text-center tw-mt-5"><i class="fa fa-spin fa-spinner fa-3x"></i></div>
            <div id="progress-checks-single" class="tw-w-full tw-text-center tw-text-lg tw-mt-4" style="display:none;"></div>
        </div>`;

document.getElementById('wrapper').style.display = 'none';
document.body.innerHTML += template

function displayProgressCheck(status) {
    if (progressChecks.has(status)) return;
    const progressContainer = document.getElementById("progress-container");
    const progressChecksDiv = document.getElementById("progress-checks");
    const loadingDiv = document.getElementById("loading");

    const fontSize = 14 + progressChecks.size * 2;
    const progressCheck = document.createElement("div");
    progressCheck.className = "progress-check tw-mb-4";
    progressCheck.innerHTML =
        `<div style="font-size: ${fontSize}px"><i class="fa fa-check text-success"></i> ${status} ...</div>`;
    progressChecksDiv.appendChild(progressCheck);

    progressChecks.add(status);

    progressContainer.style.display = "block";
}

let processInterval;
let checkInProgress = false;

async function checkProcessStatus() {

    if (checkInProgress) return;
    checkInProgress = true;

    // Make request
    const response = await fetch(statusUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    checkInProgress = false;

    if (response.redirected) {
        checkInProgress = true;
        clearInterval(processInterval);
        return window.location.reload();
    }

    if (response.ok) {
        const data = await response.json();
        const statuses = data.status;
        if (!statuses.length) {
            checkInProgress = true;
            clearInterval(processInterval);
            return window.location.reload();
        }

        if (splash_theme == 'simple') {
            let progressChecksDiv = document.getElementById("progress-checks-single");
            progressChecksDiv.style.display = "block";
            progressChecksDiv.innerText = statuses.pop();
            return;
        }

        statuses.forEach(status => {
            if (status.trim().length)
                displayProgressCheck(status);
        });
    }
}

// Initialize and start checking the process status periodically
const checkInterval = 1500; // Adjust the interval as needed (in milliseconds)
setTimeout(() => {
    checkProcessStatus();
}, 500);
processInterval = setInterval(checkProcessStatus, checkInterval);
</script>