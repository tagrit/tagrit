<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['file_sharing/file_sharing_client'] = 'file_sharing_client/index';
$route['file_sharing/gtsverify/activate'] = 'gtsverify/activate';
$route['file_sharing/download_directory/(:any)'] = 'file_sharing_public/download_directory/$1';
$route['file_sharing/download_file/(:any)'] = 'file_sharing_public/download_file/$1';
$route['file_sharing/file_sharing_client'] = 'file_sharing_client/index';
$route['file_sharing/file_sharing_client/(:any)'] = 'file_sharing_client/$1';
$route['file_sharing/file_sharing_public/download'] = 'file_sharing_public/download';
$route['file_sharing/file_sharing_public/check_download'] = 'file_sharing_public/check_download';
$route['file_sharing/file_sharing_public/share_downloaded'] = 'file_sharing_public/share_downloaded';
$route['file_sharing/file_sharing_public/get_url_by_hash'] = 'file_sharing_public/get_url_by_hash';
$route['file_sharing/(:any)'] = 'file_sharing_public/index/$1';