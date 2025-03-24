<?php

function run_module_migrations() {
    $CI =& get_instance();
    $CI->load->database();

    $module_name = 'imprest';
    $current_version = get_option($module_name . '_migration_version') ?: 0;
    log_message('debug', "Current migration version: " . $current_version);

    $migrations_path = APP_MODULES_PATH . $module_name . '/migrations/';
    $files = glob($migrations_path . '*.php');

    if (empty($files)) {
        log_message('error', "No migration files found in: " . $migrations_path);
        dd("No migration files found in: " . $migrations_path);
    }

    sort($files);

    foreach ($files as $file) {
        $filename = basename($file, '.php');
        $version = (int) explode('_', $filename)[0];

        if ($version > $current_version) {
            log_message('debug', "Running migration: " . $filename);

            try {
                require_once $file;
                $migration_class = 'Migration_' . implode('_', array_slice(explode('_', $filename), 1));  // ✅ Add 'Migration_' prefix

                if (!class_exists($migration_class)) {
                    log_message('error', "Migration class '$migration_class' does not exist in file '$file'");
                    dd("Migration class '$migration_class' does not exist in file '$file'");
                }

                $migration = new $migration_class();

                if (!method_exists($migration, 'up')) {
                    log_message('error', "Method 'up' missing in '$migration_class'");
                    dd("Method 'up' missing in '$migration_class'");
                }

                $migration->up();
                update_option($module_name . '_migration_version', $version);
                log_message('info', "Migration applied: " . $filename);

            } catch (Exception $e) {
                log_message('error', "Error running migration '$filename': " . $e->getMessage());
                dd("Error running migration '$filename': " . $e->getMessage());
            }
        }
    }
}
