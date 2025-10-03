<?php
defined('BASEPATH') or exit('No direct script access allowed');

use OpenApi\Generator;

function perfex_saas_api_classes()
{
    return [module_dir_path(PERFEX_SAAS_MODULE_NAME, 'controllers/api/Main.php')];
}

/**
 * Get the Open Api instance
 *
 * @param array $paths_to_scan Optional
 * @return null|\OpenApi\Annotations\OpenApi
 */
function perfex_saas_api_openapi_instance($paths_to_scan = [])
{
    $paths_to_scan = !empty($paths_to_scan) ? $paths_to_scan : perfex_saas_api_classes();
    $openapi = Generator::scan($paths_to_scan);
    return $openapi;
}

function perfex_saas_api_endpoints_specs()
{
    $openapi = perfex_saas_api_openapi_instance();
    $CI = &get_instance();

    $session_key = 'perfex_saas_api_endpoints_specs';

    if (!empty($cache = $CI->session->userdata($session_key))) {
        return $cache;
    }

    $methodPaths = [];
    foreach ($openapi->paths as $path => $pathItem) {
        // Check each HTTP method in the path item
        foreach (['get', 'post', 'put', 'delete', 'patch', 'options', 'head'] as $method) {
            if (isset($pathItem->$method)) {

                foreach (perfex_saas_api_classes() as $class_file) {

                    $class_name = str_ireplace('.php', '', basename($class_file));
                    $context = $pathItem->_context;
                    $className = $context->fullyQualifiedName($class_name);

                    $operation = $pathItem->$method;
                    if (!isset($operation->path) || !is_array($operation->security)) continue;

                    $reflection = new ReflectionClass($className);
                    foreach ($reflection->getMethods() as $methodReflection) {
                        $comment = $methodReflection->getDocComment();
                        $feature = $methodReflection->getName();

                        //@todo Do not check for class and always affix
                        if ($class_name !== 'Main')
                            $feature = $class_name . '.' . $feature;

                        if (!empty($comment) && strpos($comment, $operation->summary) !== false) {
                            $methodPaths[$feature]['summary'] = $operation->summary;
                            $methodPaths[$feature]['methods'][$method] = $operation->path;
                            break;
                        }
                    }
                }
            }
        }
    }

    $CI->session->set_userdata($session_key, $methodPaths);

    return $methodPaths;
}

/**
 * Get list of api users or specific one as identified by $id
 *
 * @param string $id
 * @return array|object
 */
function perfex_saas_api_users($id = '')
{
    $CI = get_instance();
    $table = perfex_saas_table('api_users');
    $users = $CI->perfex_saas_model->get($table, $id);
    if ($id && $users) {
        $users->permissions = json_decode($users->permissions ?? '');
    }

    if (empty($id) && is_array($users)) {
        for ($i = 0; $i < count($users); $i++) {
            $users[$i]->permissions = json_decode($users[$i]->permissions ?? '');
        }
    }
    return $users;
}

/**
 * Get api user details identified by token
 *
 * @param string $token
 * @return array|object|null
 */
function perfex_saas_api_users_by_token($token = '')
{
    $CI = get_instance();
    $table = perfex_saas_table('api_users');
    $CI->perfex_saas_model->db->where('token', $token);
    $user = $CI->perfex_saas_model->get($table);

    if (empty($user) || ($user && count($user) !== 1)) return null;

    $user = $user[0];

    $user->permissions = (object)json_decode($user->permissions ?? '');
    return $user;
}