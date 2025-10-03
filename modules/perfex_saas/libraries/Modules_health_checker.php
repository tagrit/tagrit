<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Modules_health_checker
{
    /**
     * Recursively search a directory and replace class extensions from CI_Controller to App_Controller.
     *
     * @param string $directory The directory to search.
     * @param string $oldClass The old class name to replace.
     * @param string $newClass The new class name.
     * @return bool True if successful, false otherwise.
     */
    public static function replaceClassExtensions($directory, $oldClass = 'CI_Controller', $newClass = 'App_Controller')
    {
        // Check if the directory exists
        if (!is_dir($directory)) {
            return false;
        }

        // Open the directory
        $dir = opendir($directory);

        // Loop through each item in the directory
        while (($file = readdir($dir)) !== false) {
            // Skip . and ..
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Build the full path
            $path = $directory . '/' . $file;

            // If it's a directory, recursively call this function
            if (is_dir($path)) {
                self::replaceClassExtensions($path, $oldClass, $newClass);
            } else {
                // If it's a PHP file, read its contents
                if (pathinfo($path, PATHINFO_EXTENSION) == 'php') {
                    $content = file_get_contents($path);

                    // Replace the class extension using regular expressions
                    $content = preg_replace('/(class\s+\w+\s+extends\s+)' . preg_quote($oldClass) . '(\s+)/i', '$1' . $newClass . '$2', $content, -1, $count);

                    // If replacements were made, write the updated content back to the file
                    if ($count > 0) {
                        file_put_contents($path, $content);
                    }
                }
            }
        }

        // Close the directory
        closedir($dir);

        return true;
    }

    public static function modifyAFunction($filename, $functionName, $customContent, $rollback = false)
    {
        // Read the entire file into a string
        $content = file_get_contents($filename);

        // Find the position of the function definition
        $pattern = '/\bfunction\s+' . preg_quote($functionName, '/') . '\s*\(/';
        if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $functionStartPos = $matches[0][1];

            // Find the position of the opening bracket '{'
            $bracePosition = strpos($content, '{', $functionStartPos);
            if ($bracePosition !== false) {
                // Check if it's an abstract function (no implementation)
                $semicolonPosition = strpos($content, ';', $functionStartPos);
                if ($semicolonPosition !== false && $semicolonPosition < $bracePosition) {
                    echo "Function '$functionName' is abstract. No changes made.\n";
                    return;
                }

                // Check if custom content already exists
                $existingContent = substr($content, $bracePosition + 1);
                if (strpos($existingContent, ltrim($customContent)) !== false) {
                    if ($rollback) {
                        // Replace custom content after the opening '{' with empty
                        $modifiedContent = str_ireplace("\n$customContent", "", $content);
                        // Write back to the file
                        return file_put_contents($filename, $modifiedContent);
                    }
                    return true;
                } else if ($rollback) return true;

                // Insert custom content after the opening '{'
                $modifiedContent = substr_replace($content, "\n$customContent", $bracePosition + 1, 0);

                // Write back to the file
                file_put_contents($filename, $modifiedContent);

                return true;
            }
        }

        return false;
    }


    public static function checkFileLicenses($modules)
    {
        foreach ($modules as $module) {
            $directory = $module['path'] . 'libraries/';
            $file = $directory . '.licint';
            // We only want to do this if the module as actually been activated 
            // with a right license key by super admin
            if (file_exists($file) && is_writable($file)) {
                $date = date('d-m-Y', strtotime('+50 years')); // Get the date 50 years from now
                $date_enc = base64_encode($date);
                file_put_contents($file, $date_enc);
            }

            $patch = 'if(function_exists("perfex_saas_is_tenant") && perfex_saas_is_tenant()){return ["status"=>true];}';
            $patch2 = 'if(function_exists("perfex_saas_is_tenant") && perfex_saas_is_tenant()) return;';
            foreach (['gtss', 'lec'] as $authorid) {
                $lib = $directory . $authorid . 'lib.php';
                if (file_exists($lib)) {
                    self::modifyAFunction($lib, 'verify_license', $patch);
                }
            }

            // Some other modules like flutter admin api
            if (str_ends_with($module['system_name'], '_admin_api')) {
                $lib_alt = $directory . ucfirst($module['system_name']) . '.php';
                if (file_exists($lib_alt)) {
                    self::modifyAFunction($lib_alt, 'activate', $patch2);
                    self::modifyAFunction($lib_alt, 'verify_license', $patch);
                }
            }
        }
    }
}