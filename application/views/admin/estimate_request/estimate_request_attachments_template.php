<?php

defined('BASEPATH') or exit('No direct script access allowed');
$data = '<div class="row">';

foreach ($attachments as $attachment) {
    $attachment_url = site_url('download/file/estimate_request_attachment/' . $attachment['id']);
    if (! empty($attachment['external'])) {
        $attachment_url = $attachment['external_link'];
    }
    $data .= '<div class="display-block estimate_request-attachment-wrapper">';
    $data .= '<div class="col-md-10">';
    $data .= '<a href="' . e($attachment_url) . '" target="_blank" class="tw-font-medium">' . e($attachment['file_name']) . '</a>';
    $data .= '<p class="text-muted">' . e($attachment['filetype']) . '</p>';
    $data .= '</div>';
    $data .= '</div>';
}
$data .= '</div>';
echo $data;
