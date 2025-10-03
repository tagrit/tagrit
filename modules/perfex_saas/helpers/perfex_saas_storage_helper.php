<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Calculate the size of a directory.
 *
 * @param string $path The path of the directory.
 * @return int The total size of the directory in bytes.
 */
function perfex_saas_get_directory_size($path)
{
    $total_size = 0;

    if (!is_dir($path)) return $total_size;

    // Recursively iterate through the directory and its subdirectories.
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            // Add the size of each file to the total size.
            $total_size += $file->getSize();
        }
    }

    return $total_size;
}


/**
 * Get a linear array of storage size units.
 *
 * @return array An array containing storage size units (B, KB, MB, GB, TB).
 */
function perfex_saas_storage_size_units()
{
    // Define an array containing storage size units.
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    // Return the array of storage size units with increase in size.
    return $units;
}

/**
 * Format a storage size in human-readable format.
 *
 * @param int $size The size in bytes.
 * @return string The formatted size with appropriate unit (B, KB, MB, GB, TB).
 */
function perfex_saas_format_storage_size($size)
{
    $units = perfex_saas_storage_size_units();
    $i = 0;
    while ($size >= 1024 && $i < 4) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

/**
 * Convert a human-readable storage size to bytes.
 *
 * @param string $formatted_size The formatted size with unit (e.g., '10 MB').
 * @return int The size in bytes.
 */
function perfex_saas_convert_formatted_size_to_bytes($formatted_size)
{
    $units = perfex_saas_storage_size_units();
    $size_parts = explode(' ', $formatted_size);

    if (count($size_parts) === 2) {
        $size_value = (float) $size_parts[0];
        $size_unit = trim(strtoupper($size_parts[1]));

        $unit_index = array_search($size_unit, $units);

        if ($unit_index !== false) {
            return $size_value * (1024 ** $unit_index);
        }
    }

    // If conversion fails, return -1 to indicate an error.
    return -1;
}

/**
 * Custom wrapper over get_upload_path_by_type with ability to get fullpath or just the upload folder path
 *
 * @param string $type
 * @param boolean $fullpath
 * @return string
 */
function perfex_saas_get_upload_path_by_type($type, $fullpath = true)
{
    if ($type == 'module:backup') {
        $path = PERFEX_SAAS_TENANT_UPLOAD_BASE_FOLDER . 'backups' . '/';
        if (!is_dir($path)) {
            perfex_saas_create_dir($path, FCPATH . 'backups/', true);
        }
        return $path;
    }

    $path = get_upload_path_by_type($type);
    if (!$fullpath)
        $path = str_ireplace(FCPATH, '', $path);
    return $path;
}

/**
 * Return tenant upload directory with trailing slash
 *
 * @param object $tenant
 * @return string
 */
function perfex_saas_tenant_upload_base_path($tenant)
{
    return FCPATH . 'uploads/tenants/' . $tenant->slug . '/';
}

/**
 * Get the base path for tenant media.
 *
 * @param object $tenant The tenant object.
 * @return string The base path for tenant media.
 * @throws Exception When no instance found i.e CI not initiated
 */
function perfex_saas_tenant_media_base_path($tenant)
{
    // Get the master media folder and slug.
    $master_media_folder = get_instance()->app->get_media_folder();
    $master_slug = perfex_saas_master_tenant_slug();

    // Use the master media folder by default.
    $media_folder = $master_media_folder;

    // Check if the master media folder ends with the master slug.
    if (str_ends_with($master_media_folder, $master_slug)) {
        // Remove the master slug from the media folder.
        $media_folder = str_replace_last($master_slug, '', $media_folder);
    }

    if (str_ends_with($media_folder, $tenant->slug) && $media_folder !== $tenant->slug) {
        return FCPATH . $media_folder;
    }

    // Construct and return the full media path for the tenant.
    return FCPATH . $media_folder . '/' . $tenant->slug;
}


/**
 * Get tenant's storage information including upload and media sizes.
 *
 * @param object $tenant The tenant object.
 * @return object Storage information object including upload and media sizes.
 */
function perfex_saas_calculate_tenant_storage_size($tenant)
{
    // Initialize variables.
    $upload_size_in_bytes = 0;
    $media_size_in_bytes = 0;

    // Calculate upload storage size.
    $upload_base_path = perfex_saas_tenant_upload_base_path($tenant);
    $upload_size_in_bytes = perfex_saas_get_directory_size($upload_base_path);

    // Calculate media storage size.
    $media_base_path = perfex_saas_tenant_media_base_path($tenant);
    $media_size_in_bytes = perfex_saas_get_directory_size($media_base_path);

    // Calculate total storage size.
    $total_size_in_bytes = $upload_size_in_bytes + $media_size_in_bytes;

    // Create a storage information object.
    $storage_info = (object)[
        'upload_size_in_bytes' => $upload_size_in_bytes,
        'media_size_in_bytes' => $media_size_in_bytes,
        'total' => $total_size_in_bytes,
    ];

    // Return the storage information object.
    return $storage_info;
}

/**
 * Get tenant used storage.
 * This relies on the stored/cache usage info from cron. 
 * Use perfex_saas_calculate_tenant_storage_size() for active calculation .Note that can often take longer
 *
 * @param object $tenant
 * @param bool $format
 * @return string The size in byte if not format or in human readable formatted when $format is true
 */
function perfex_saas_tenant_used_storage($tenant, $format = false)
{
    // Get tenant's storage information.
    $storage_info = (object)($tenant->metadata->storage ?? []);
    $size = $storage_info->total ?? 0;
    return $format ? perfex_saas_format_storage_size($size) : $size;
}

/**
 * Get the tenant human readable limit from the package
 *
 * @param object $tenant
 * @return string
 */
function perfex_saas_tenant_storage_limit($tenant)
{
    $size = $tenant->package_invoice->metadata->storage_limit->size ?? 0;
    $unit = $tenant->package_invoice->metadata->storage_limit->unit ?? perfex_saas_storage_size_units()[0];

    // Add extra purchased space
    $extra_size = (int)($tenant->package_invoice->custom_limits->{'storage'} ?? 0);
    $size = $size + $extra_size;

    $limit = $size . ' ' . $unit;
    return $limit;
}

/**
 * Check is the tenant storage is unlimited
 *
 * @param ojbect $tenant
 * @return bool
 */
function perfex_saas_tenant_storage_is_unlimited($tenant)
{
    $quota = perfex_saas_tenant_storage_limit($tenant);
    $unlimited = str_starts_with(trim($quota), -1);
    return $unlimited;
}

/**
 * Update tenant storage size in the metadata.
 *
 * @param object $tenant The tenant object.
 * @return object The tenant object with updated storage info
 */
function perfex_saas_update_tenant_storage_size($tenant)
{
    // Calculate the current storage size for the tenant.
    $storage = perfex_saas_calculate_tenant_storage_size($tenant);

    // Check if the calculated total storage size is different from the stored value.
    if ($storage->total != ($tenant->metadata->storage->total ?? 0)) {
        // Get the table name for the companies.
        $table = perfex_saas_table('companies');

        // Update the metadata with the new storage size information.
        $tenant->metadata = (object)array_merge((array)$tenant->metadata, ['storage' => $storage]);
        $metadata = json_encode((array)$tenant->metadata);
        $query = "UPDATE `$table` SET `metadata` = '$metadata' WHERE slug='$tenant->slug';";

        // Execute the raw SQL query to update the metadata.
        perfex_saas_raw_query($query);
    }
    return $tenant;
}

/**
 * Validate tenant upload based on remaining space including currently uploading files.
 *
 * @param object $tenant The tenant object.
 * @param array $uploaded_files The $_FILES array containing information about the uploaded files. Make empty to estimate just the left space.
 * @return bool Whether the upload is allowed based on space availability.
 */
function perfex_saas_tenant_has_enough_storage($tenant, $uploaded_files = [])
{
    // Calculate total used space including the currently uploading files.
    $total_used_space = perfex_saas_tenant_used_storage($tenant, false);

    $total_file_size = 0;

    if (!empty($uploaded_files)) {
        foreach ($uploaded_files as $upload) {
            $sizes = $upload['size'];
            $sizes = is_array($sizes) ? $sizes : [$sizes];
            foreach ($sizes as $key => $size) {
                $total_file_size += $size;
            }
        }
    }

    if ($total_file_size === 0) return true;

    // Maximum allowed space in bytes
    $max_allowed_space =  perfex_saas_convert_formatted_size_to_bytes(perfex_saas_tenant_storage_limit($tenant));

    // Calculate remaining space.
    $remaining_space = $max_allowed_space - ($total_used_space + $total_file_size);

    // All files can fit in the remaining space, upload is allowed.
    return $remaining_space >= 0;
}


/**
 * Handle 404 static file request for tenants.
 * The method assist in handling files upload by tenants before proceeding with 404.
 * This method is need until Perfex author stop using hard coded upload/ string in file uploads url.
 *
 * @param object $tenant
 * @param string $upload_folder The base upload folder.
 * @param string $tenants_upload_folder The tenant-specific upload folder.
 * @return void
 */
function perfex_saas_handle_tenant_404_static_file_request($tenant, $upload_folder, $tenants_upload_folder)
{
    // Get the requested URI.
    $requested_uri = $_SERVER['REQUEST_URI'] ?? '';

    // If no file extension is found in the URI, return.
    if (stripos($requested_uri, '.') === false) {
        return;
    }

    // Check if the requested file is not a PHP file.
    $tenant_slug = $tenant->slug;

    // Get the full url for the current request
    $_url = perfex_saas_url_origin($_SERVER, true, true); // full url with query
    $url = $_url;
    $base_url = perfex_saas_url_origin($_SERVER);

    // Ensure the requested file path does not include the base url (incase the crm is installed in folder)
    $requested_file = str_ireplace($base_url, '', $url);
    $requested_file = ltrim($requested_file, '/');

    // Early return for invalid files
    $extension = pathinfo($requested_file, PATHINFO_EXTENSION);
    if (stripos($url, './') !== false || empty($extension) || in_array($extension, ['php', 'env', 'htaccess'])) return;

    // Remove tenant path id if present from url and requested file 
    $path_signature = perfex_saas_tenant_url_signature($tenant_slug);
    if (stripos($requested_file, $path_signature) !== false) {

        $requested_file = ltrim(str_ireplace($path_signature, "", $requested_file), '/');
        $url = str_ireplace('/' . $path_signature, '', $url);

        $requested_file_tenant_equiv = str_replace_first($upload_folder, $tenants_upload_folder . $tenant_slug . '/', $requested_file);
        if (file_exists(FCPATH . urldecode($requested_file_tenant_equiv))) {
            $url = str_ireplace($upload_folder, $tenants_upload_folder . $tenant_slug . '/', $url);
            // Redirect to the tenant-specific file URL.
            header("Location: $url");
            exit;
        }

        // Redirect if file exist
        if (file_exists(FCPATH . urldecode($requested_file))) {
            $is_tenant_upload_path = stripos($requested_file, $tenants_upload_folder) === 0;
            $can_serve_file = $is_tenant_upload_path;

            if (!$is_tenant_upload_path) {

                // Check if file in share list and has match value, then sever from master
                $shared_fields = (array)$tenant->package_invoice->metadata->shared_settings->shared ?? [];
                if (!empty($shared_fields)) {
                    // @todo Avoid DB calls here
                    $values = (array)perfex_saas_get_options($shared_fields, false);
                    $values = array_column($values, 'value');
                    $can_serve_file = in_array(basename($requested_file), $values) || in_array($requested_file, $values);
                }
            }

            if ($can_serve_file) {
                header("Location: $url");
                exit;
            }
        }
    }


    if (
        stripos($requested_file, $tenants_upload_folder) === false &&
        stripos($requested_file, $upload_folder) === 0
    ) {
        $requested_file_alt = str_replace_first($upload_folder, $tenants_upload_folder . $tenant_slug . '/', $requested_file);
        if (file_exists(FCPATH . $requested_file_alt))
            $url = str_replace_first($upload_folder, $tenants_upload_folder . $tenant_slug . '/', $url);

        // Redirect to the tenant-specific file URL.
        if ($_url !== $url) {
            header("Location: $url");
            exit;
        }
    }
}

/**
 * Create a directory and set permissions.
 *
 * @param string $path The path of the directory to create.
 * @param string $blueprint The path to a blueprint directory for copying files (optional).
 * @return void
 */
function perfex_saas_create_dir($path, $blueprint = '', $force_htaccess = false)
{
    // Create the directory with the specified permissions.
    mkdir($path, DIR_READ_MODE);

    // Create an index.html file within the directory to prevent directory listing.
    fopen(rtrim($path, '/') . '/' . 'index.html', 'w');

    // Add .htaccess file if a blueprint path is provided.
    if ($blueprint) {
        // Check if the .htaccess file exists in the blueprint directory.
        if (file_exists($blueprint . '.htaccess')) {
            // Copy the .htaccess file to the new directory.
            copy($blueprint . '.htaccess', rtrim($path, '/') . '/' . '.htaccess');
        } else if ($force_htaccess) {
            fopen($path . '.htaccess', 'w');
            $fp = fopen($path . '.htaccess', 'a+');
            if ($fp) {
                fwrite($fp, 'Order Deny,Allow' . PHP_EOL . 'Deny from all');
                fclose($fp);
            }
        }
    }
}