<?php

namespace PerfexSaaSApi;

defined('BASEPATH') or exit('No direct script access allowed');

use OpenApi\Generator;

require_once __DIR__ . '/../Tenant_trait.php';

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * )
 * @OA\Server(url=APP_BASE_URL_DEFAULT)
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     securityScheme="api_key"
 * )
 * @OA\Schema(
 *      schema="StringList",
 *      @OA\Property(property="value", type="array", @OA\Items(anyOf={@OA\Schema(type="string")}))
 * )
 * @OA\Schema(
 *      schema="String",
 *      @OA\Property(property="value", type="string")
 * )
 * @OA\Schema(
 *      schema="Object",
 *      @OA\Property(property="value", type="object")
 * )
 * @OA\Schema(
 *     schema="MixedList",
 *     @OA\Property(property="fields", type="array", @OA\Items(oneOf={
 *         @OA\Schema(ref="#/components/schemas/StringList"),
 *         @OA\Schema(ref="#/components/schemas/String"),
 *         @OA\Schema(ref="#/components/schemas/Object")
 *     }))
 * )
 *
 * @OA\Schema(
 *     schema="PricingPlan",
 *     type="object",
 *     description="Pricing Plan details",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the pricing plan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the pricing plan",
 *         example="Basic Plan"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="A brief description of the pricing plan",
 *         example="This is a basic plan for beginners."
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The slug for the pricing plan",
 *         example="basic-plan"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the pricing plan",
 *         example=19.99
 *     ),
 *     @OA\Property(
 *         property="trial_period",
 *         type="integer",
 *         description="The trial period in days",
 *         example=14
 *     ),
 *     @OA\Property(
 *         property="is_default",
 *         type="boolean",
 *         description="Indicates if this is the default pricing plan",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="is_private",
 *         type="boolean",
 *         description="Indicates if this pricing plan is private",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="db_scheme",
 *         type="string",
 *         description="The database scheme associated with the pricing plan",
 *         example="default_scheme"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="The status of the pricing plan",
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="modules",
 *         type="array",
 *         description="List of modules included in the pricing plan",
 *         @OA\Items(type="string"),
 *         example={"module1", "module2", "module3"}
 *     ),
 *     @OA\Property(
 *         property="metadata",
 *         type="object",
 *         description="Additional metadata for the pricing plan",
 *         @OA\Property(
 *             property="invoice",
 *             type="string",
 *             description="Invoice details",
 *             example="monthly"
 *         ),
 *         @OA\Property(
 *             property="max_instance_limit",
 *             type="integer",
 *             description="Maximum instance limit",
 *             example=10
 *         ),
 *         @OA\Property(
 *             property="limitations",
 *             type="object",
 *             description="Limitations of the pricing plan",
 *             example={"invoice": "10", "estimate": "20"}
 *         ),
 *         @OA\Property(
 *             property="enable_subdomain",
 *             type="boolean",
 *             description="Indicates if subdomains are enabled",
 *             example=true
 *         ),
 *         @OA\Property(
 *             property="enable_custom_domain",
 *             type="boolean",
 *             description="Indicates if custom domains are enabled",
 *             example=false
 *         ),
 *         @OA\Property(
 *             property="shared_settings",
 *             type="object",
 *             additionalProperties=true,
 *             description="Shared settings for the pricing plan",
 *             example={"setting1": "value1", "setting2": "value2"}
 *         )
 *     )
 * )
 * @OA\Schema(
 *     schema="Tenant",
 *     title="Tenant",
 *     description="Details of a tenant",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the tenant",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="clientid",
 *         type="integer",
 *         description="Client ID of the tenant",
 *         example=101
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the tenant",
 *         example="Example Corp"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Slug of the tenant",
 *         example="example-corp"
 *     ),
 *     @OA\Property(
 *         property="custom_domain",
 *         type="string",
 *         description="Custom domain of the tenant",
 *         example="example.com"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status of the tenant",
 *         enum={"active", "inactive", "disabled", "banned", "pending", "deploying", "pending-delete"},
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="status_note",
 *         type="string",
 *         description="Status note of the tenant",
 *         example="Ready for deployment"
 *     ),
 *     @OA\Property(
 *         property="dsn",
 *         type="string",
 *         description="DSN of the tenant",
 *         example="mysql://username:password@hostname:3306/database"
 *     ),
 *     @OA\Property(
 *         property="accessible_url_list",
 *         type="array",
 *         description="List of accessible URLs for the tenant",
 *         @OA\Items(type="string", example="https://example.com")
 *     ),
 *     @OA\Property(
 *         property="metadata",
 *         type="object",
 *         description="Additional metadata of the tenant",
 *         @OA\Property(property="key", type="string", example="value")
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the tenant was created",
 *         example="2024-06-24 15:30:00"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the tenant was last updated",
 *         example="2024-06-25 09:45:00"
 *     )
 * )
 * @OA\Schema(
 *     schema="TenantCreateRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The company name"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The tenant slug or subdomain id"
 *     ),
 *     @OA\Property(
 *         property="custom_domain",
 *         type="string",
 *         description="The tenant custom domain"
 *     )
 * )
 * @OA\Schema(
 *     schema="TenantUpdateRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The company name"
 *     ),
 *     @OA\Property(
 *         property="custom_domain",
 *         type="string",
 *         description="The tenant custom domain"
 *     ),
 *     @OA\Property(
 *         property="disabled_modules",
 *         type="array",
 *         description="The tenant disabled modules",
 *         @OA\Items(type="string")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SubscriptionUpdateRequest",
 *     type="object",
 *     @OA\Property(
 *         property="custom_limits",
 *         type="object",
 *         description="The new limit structure. Object containing limit name and new value. See the my account form in client portal.
 *         When sending using application/x-www-form-urlencoded, ensure your custom_limits is rightly encode i.e custom_limits[staff]=30&custom_limits[storage]=50 ...
 *         See the client package customization form for better understanding of the structure. Both the form and this endpoint use same request format.
 *         Patching is allowed for this property. Set the resources value to zero to remove from the invoice extra units.",
 *         @OA\Property(
 *           property="staff",
 *           type="string",
 *           example="30"
 *         ),
 *         @OA\Property(
 *           property="storage",
 *           type="string",
 *           description="New storage value in MB",
 *           example="500"
 *         )
 *     ),
 *     @OA\Property(
 *         property="purchased_modules",
 *         type="array",
 *         description="The tenant purchased modules. 
 *         Patching is not allowed. Send the complete list of purchased modules for the tenant. (i.e you might need to merge with older purchased module if buying new extra module)",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="purchased_services",
 *         type="array",
 *         description="The tenant purchased services. 
 *         Patching is not allowed. Send the complete list of purchased services for the tenant. (i.e you need to merge with old service if buying new extra services)",
 *         @OA\Items(type="string")
 *     )
 * )
 *
 *
 * @OA\Response(
 *    description="Success",
 *    response="Success",
 *    @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
 * )
 *  
 * @OA\Response(
 *    description="default",
 *    response="ServerError",
 *    @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 * )
 *
 * @OA\Schema(
 *    schema="SuccessResponse",
 *    type="object",
 *    description="General response structure for non list success endpoint",
 *    @OA\Property(
 *         property="success",
 *         type="string",
 *         description="The success message",
 *         example="Created successfully"
 *     ),
 *     @OA\Property(
 *         property="redirect",
 *         type="string",
 *         description="Url to be redirected to if any (optional)",
 *         example="https://demo.com/compolete/some/action"
 *     )
 * )
 * 
 * @OA\Schema(
 *    schema="ErrorResponse",
 *    type="object",
 *    description="General response structure for common errors (non 200 status).",
 *    @OA\Property(
 *         property="error",
 *         type="string",
 *         description="The error message.",
 *         example="An error occurred"
 *     ),
 *     @OA\Property(
 *         property="redirect",
 *         type="string",
 *         description="Url to be redirected to if any (optional) i.e https://demo.com/compolete/some/action",
 *         example=""
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SubscriptionSuccessResponse",
 *     type="object",
 *     description="Subscription response structure for success calls. redirect and success property can be empty. package and invoice will always be set.",
 *     @OA\Property(
 *         property="success",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="redirect",
 *         type="string",
 *         example=""
 *     ),
 *     @OA\Property(
 *         property="action_url",
 *         type="string",
 *         example="",
 *         description="(Optional) Action url to complete the subscription. i.e stripe checkout. You can direct user to this URL in webview client or use stripe_session_id instead."
 *     ),
 *     @OA\Property(
 *         property="stripe_session_id",
 *         type="string",
 *         example="",
 *         description="(Optional) Stripe session id when using stripe subscription on the package. You can redirect the user from stripe SDK client."
 *     ),
 *     @OA\Property(
 *         property="package",
 *         ref="#/components/schemas/PricingPlan"
 *     ),
 *     @OA\Property(
 *         property="invoice",
 *         ref="#/components/schemas/MixedList"
 *     )
 * )
 */
trait Main
{
    use \Tenant_trait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\Get(
     *     tags={"Health Check"},
     *     path="/saas/api/index",
     *     summary="Health Check",
     *     description="Returns OK if the API is reachable",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index()
    {
        exit("OK");
    }

    /**
     * @OA\Get(
     *     tags={"Utility"},
     *     path="/saas/api/caddy_domain_check",
     *     summary="Check if a domain is recognized by the system",
     *     description="Returns 404 if no match, 200 (OK) if same as base domain, and 200 (Matched) when a match is found (subdomain or custom domain)",
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="The domain to check"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Match status",
     *         @OA\JsonContent(
     *             type="string",
     *             example="OK or Matched"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No domain match"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request: Missing domain parameter"
     *     )
     * )
     */
    public function caddy_domain_check()
    {
        // Get the domain or subdomain and validate
        $domain = $this->input->get("domain", true);
        if (empty($domain)) {
            set_status_header(400);
            echo 'No domain provided';
            return;
        }

        if (perfex_saas_get_saas_default_host() === $domain) {
            set_status_header(200);
            echo "OK";
            return;
        }

        // Detect info if using subdomain or custom domain. Will return non empty 'slug' if subdomain other 'custom_domain' or false
        $tenant_info = perfex_saas_get_tenant_info_by_host($domain);
        if ($tenant_info) {
            $identified_by_slug = !empty($tenant_info['slug']);
            $field = $identified_by_slug ? 'slug' : 'custom_domain';
            $value = $identified_by_slug ? $tenant_info['slug'] : $tenant_info['custom_domain'];
            $tenant = perfex_saas_search_tenant_by_field($field, $value);
            if ($tenant) {
                set_status_header(200);
                echo "Matched";
                return;
            }
        }

        // Set 404
        set_status_header(404);
    }

    /**
     * @OA\Get(
     *     tags={"Utility"},
     *     path="/saas/api/is_slug_available/{slug}",
     *     summary="Check for slug availability",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="The slug to check"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Slug availability result",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="available",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     )
     * )
     */
    public function is_slug_available($slug)
    {
        $is_available = true;

        if ($is_available) {
            $validated_slug = perfex_saas_generate_unique_slug($slug, 'companies');
            $is_available = $validated_slug === $slug ||
                $validated_slug === perfex_saas_clean_slug($slug); // allow dash in slug
        }

        return $this->response_json(['available' => $is_available]);
    }

    /**
     * @OA\Get(
     *     tags={"Utility"},
     *     path="/saas/api/is_custom_domain_available/{domain}",
     *     summary="Check if a custom domain is available for use by the client.",
     *     @OA\Parameter(
     *         name="domain",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="The domain to check"
     *     ),
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID. Should be provided when not using session (i.e outside client portal). Provide to check is relative for a specific user for cases of updating."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain availability status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="available",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function is_custom_domain_available($domain, $client_id = '')
    {
        $is_available = false;

        $client_id = (int)(empty($client_id) ? $this->input->get('client_id', true) : $client_id);
        if (!empty($client_id))
            $this->api_key_middleware();

        if (perfex_saas_is_valid_custom_domain($domain)) {
            $this->perfex_saas_model->db->group_start();
            $this->perfex_saas_model->db->where('custom_domain', $domain, true);
            $this->perfex_saas_model->db->or_like('metadata', '"pending_custom_domain":"' . $domain . '"', true);
            $this->perfex_saas_model->db->or_like('metadata', "'pending_custom_domain':'" . $domain . "'", true);
            $this->perfex_saas_model->db->group_end();

            if (empty($client_id)) {
                $client_id = is_client_logged_in() ? get_client_user_id() : $client_id;
            }

            $client_id = (int)$client_id;
            if ($client_id) {
                $this->perfex_saas_model->db->group_start()->where('`clientid` !=', $client_id)->group_end();
            }
            $_companies = $this->perfex_saas_model->get(perfex_saas_table('companies'));
            $is_available = count((array)$_companies) === 0;
        }

        return $this->response_json(['available' => $is_available]);
    }




    /**************************************** Manage Settings ***************************************************/

    /**
     * @OA\Get(
     *     tags={"Config"},
     *     path="/saas/api/settings",
     *     summary="Get the list of essential settings.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             description="Object containing settings key and value. Value will be string or json string",
     *             @OA\Property(property="settings_key", type="string"),
     *             example={
     *                 "perfex_saas_enable_single_package_mode": "0",
     *                 "perfex_saas_enable_auto_trial": "0",
     *                 "perfex_saas_require_invoice_payment_status": "[""4""]"
     *             }
     *        )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function settings($group = '')
    {
        $this->api_key_middleware();

        if (!empty($group) && in_array($group, ['modules', 'services'])) {
            return $this->$group();
        }

        $keys = [
            "perfex_saas_enable_single_package_mode",
            "perfex_saas_enable_auto_trial",
            "perfex_saas_autocreate_first_company",
            "perfex_saas_allow_customer_cancel_subscription",
            "perfex_saas_require_invoice_payment_status",
            "perfex_saas_autolaunch_instance",
            "perfex_saas_alternative_base_host",
            "perfex_saas_route_id",
            "perfex_saas_reserved_slugs",
            "perfex_saas_enable_subdomain_input_on_signup_form",
            "perfex_saas_enable_customdomain_input_on_signup_form",
            "perfex_saas_landing_page_url",
            "perfex_saas_landing_page_url_mode",
            "perfex_saas_clients_default_theme_whitelabel_name",
            "perfex_saas_instance_delete_pending_days",
            "perfex_saas_tenant_seeding_source",
            "perfex_saas_seeding_tenant",
            "perfex_saas_enable_custom_module_request",
        ];

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = get_option($key);
        }

        return $this->response_json($result);
    }


    /**
     * @OA\Get(
     *     tags={"Config"},
     *     path="/saas/api/settings/modules",
     *     summary="Get the modules list with marketplace information",
     *     @OA\Response(
     *         response=200,
     *         description="A list of modules with marketplace information",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="system_name",
     *                 type="object",
     *                 @OA\Property(
     *                     property="custom_name",
     *                     type="string",
     *                     description="The custom name of the module"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     format="float",
     *                     description="The default price of the module. Get package specific from the package list endpoint."
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="url",
     *                     description="The image URL of the module"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function modules()
    {
        $this->api_key_middleware();

        $modules = $this->perfex_saas_model->modules();
        $result = [];
        foreach ($modules as $key => $module) {
            $system_name = $module['system_name'];
            $custom_name = $module['custom_name'] ?? $system_name;
            $custom_name = _l($custom_name, '', false);
            $price = $module['price'] ?? 0.0;
            $img = $module['image'] ?? '';
            $result[$system_name] = [
                'system_name' => $system_name,
                'custom_name' => $custom_name,
                'price' => $price,
                'image' => $img
            ];
        }
        return $this->response_json($result);
    }


    /**
     * @OA\Get(
     *     tags={"Config"},
     *     path="/saas/api/settings/services",
     *     summary="Get the service list with marketplace information",
     *     @OA\Response(
     *         response=200,
     *         description="A list of services with marketplace information",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="service_id",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the service"
     *                 ),
     *                 @OA\Property(
     *                     property="billing_mode",
     *                     type="string",
     *                     description="The billing mode of the service"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     format="float",
     *                     description="The price of the service. Get package specific from the package list endpoint."
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="url",
     *                     description="The image URL of the service"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function services()
    {
        $this->api_key_middleware();

        $services = $this->perfex_saas_model->services();
        $result = [];
        foreach ($services as $service_id => $service) {
            if (empty($service_id)) continue;
            $name = $service['name'];
            $billing_mode = $service['billing_mode'];
            $price = $service['price'] ?? null; // Changed to null for better handling
            $img = $service['image'] ?? '';
            $result[$service_id] = [
                'name' => $name,
                'billing_mode' => $billing_mode,
                'price' => $price,
                'image' => $img,
            ];
        }
        return $this->response_json($result);
    }






    /**************************************** Manage Plans ***************************************************/

    /**
     * @OA\Get(
     *     tags={"Plan"},
     *     path="/saas/api/plans",
     *     summary="Retrieve packages list. Filterable by package id as query",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Optional package/plan id"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of pricing plans",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PricingPlan")
     *         )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function plans($id = '')
    {
        $this->api_key_middleware();

        $id = (int)(empty($id) ? $this->input->get('id', true) : $id);

        $_packages = $this->perfex_saas_model->packages($id);
        if (!is_array($_packages)) {
            $_packages = [$_packages];
        }

        $package_list = [];
        foreach ($_packages as $key => $package) {
            $package_list[] = $this->package_transformer($package);
        }

        return $this->response_json($package_list);
    }


    /**************************************** Manage tenants ***************************************************/

    /**
     * @OA\Get(
     *     tags={"Tenants"},
     *     path="/saas/api/tenants",
     *     summary="Retrieve tenants information. Filterable by tenant id as query",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Optional tenant ID"
     *     ),
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Optional client ID to limit list to a particular client"
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *             default=1000
     *         ),
     *         description="Limit"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *             default=1
     *         ),
     *         description=""
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tenants",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tenant")
     *         )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function tenants($id = '', $client_id = '')
    {
        $this->api_key_middleware();

        try {
            $id = (int)(empty($id) ? $this->input->get('id', true) : $id);
            $client_id = (int)(empty($client_id) ? $this->input->get('client_id', true) : $client_id);

            $limit = (int)($this->input->get('limit', true) ?? 1000);
            $page = (int)($this->input->get('page', true) ?? 1);
            $offset = ($page - 1) * $limit;

            $this->perfex_saas_model->db->limit($limit);
            $this->perfex_saas_model->db->offset($offset);
            if ($client_id)
                $this->perfex_saas_model->db->where('clientid', $client_id);
            $_tenants = $this->perfex_saas_model->companies($id);

            if (!is_array($_tenants)) {
                $_tenants = [$_tenants];
            }

            $tenant_list = [];
            foreach ($_tenants as $key => $tenant) {
                $tenant_list[] = [
                    "id" => $tenant->id,
                    "clientid" => $tenant->clientid,
                    "name" => $tenant->name,
                    "slug" => $tenant->slug,
                    "custom_domain" => $tenant->slug,
                    "status" => $tenant->status, // enum('active','inactive','disabled','banned','pending','deploying','pending-delete')
                    "status_note" => $tenant->status_note,
                    "dsn" => perfex_saas_dsn_to_string(perfex_saas_parse_dsn($tenant->dsn, ['host', 'dbname']), false),
                    "accessible_url_list" => perfex_saas_tenant_base_url($tenant, '', 'all'),
                    "metadata" => $tenant->metadata,
                    "created_at" => $tenant->created_at,
                    "updated_at" => $tenant->updated_at
                ];
            }
            return $this->response_json($tenant_list);
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Tenants"},
     *     path="/saas/api/create_tenant/{client_id}",
     *     summary="Create a new tenant instance",
     *     @OA\Parameter(
     *         name="client_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/TenantCreateRequest")
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/TenantCreateRequest"),
     *             example={
     *                 "name": "Ulutfa Tech",
     *                 "slug": "ulutfacrm",
     *                 "custom_domain": "ulutfacrm.com"
     *             }
     *         )
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Created tenant details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="string",
     *                 example="Tenant created successfully"
     *             )
     *         )
     *     ),
     *    @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *    security={{"api_key": {}}}
     * )
     */
    public function create_tenant($client_id)
    {
        $this->api_key_middleware('post');
        try {
            return $this->create_or_edit_company($client_id);
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Tenants"},
     *     path="/saas/api/update_tenant/{client_id}/{tenant_id}",
     *     summary="Update tenant details",
     *     @OA\Parameter(
     *         name="client_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\Parameter(
     *         name="tenant_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Tenant ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/TenantUpdateRequest")
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/TenantUpdateRequest"),
     *             example={
     *                 "name": "Ulutfa Tech",
     *                 "custom_domain": "ulutfacrm.com",
     *                 "disabled_modules": {"surveys","theme_style"}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated tenant details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="string",
     *                 example="Tenant updated successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function update_tenant($client_id, $tenant_id)
    {
        $this->api_key_middleware('post');
        try {
            return $this->create_or_edit_company($client_id, $tenant_id);
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Tenants"},
     *     path="/saas/api/delete_tenant/{tenant_id}",
     *     summary="Delete a tenant",
     *     @OA\Parameter(
     *         name="tenant_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *         description="ID of the tenant to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success message with details of deletion",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="string",
     *                 example="Company deleted"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request: Missing tenant ID"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found: Tenant not found"
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function delete_tenant($tenant_id)
    {
        $this->api_key_middleware('delete');
        try {
            $id = (int)$tenant_id;
            if ($id) {
                $removed = $this->perfex_saas_model->delete_company($id);
                if ($removed) {
                    $message = _l('deleted', _l('perfex_saas_company')) . ($removed !== true ? ' ' . _l('perfex_saas_with_error') . ': ' . $removed : '');
                    return $this->response_json(['success' => $message]);
                }
                $message =  _l('perfex_saas_error_completing_action') . (is_string($removed) ? ': ' . $removed : '');
                throw new \Exception($message, 1);
            }

            throw new \Exception(_l('perfex_saas_error_completing_action'));
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }




    /**************************************** Manage tenant subscriptions ***************************************************/

    /**
     * @OA\Post(
     *     tags={"Subscription"},
     *     path="/saas/api/subscribe/{clientid}/{packageslug}",
     *     summary="Subscribe a client to a plan/package",
     *     @OA\Parameter(
     *         name="clientid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\Parameter(
     *         name="packageslug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Package slug"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription added successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubscriptionSuccessResponse")
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function subscribe($clientid, $packageslug)
    {
        $this->api_key_middleware('post');
        try {
            $service = $this->subscribeToPackage((int)$clientid, $packageslug);
            return $this->response_json($service);
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Subscription"},
     *     path="/saas/api/update_subscription/{clientid}",
     *     summary="Update subscription details for a client",
     *     @OA\Parameter(
     *         name="clientid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/SubscriptionUpdateRequest"),
     *             example={
     *                 "custom_limits": {"storage": "30", "staff": "2"},
     *                 "purchased_modules": {"surveys", "theme_styles"},
     *                 "purchased_services": {"serv38843392914","serv3880789877"}
     *             }
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/SubscriptionUpdateRequest"),
     *             example={
     *                 "custom_limits": {"storage": "30", "staff": "2"},
     *                 "purchased_modules": {"surveys", "theme_styles"},
     *                 "purchased_services": {"serv38843392914","serv3880789877"}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubscriptionSuccessResponse")
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function update_subscription($clientid)
    {
        $this->api_key_middleware('post');
        try {
            $service = $this->updateSubscription((int)$clientid);
            return $this->response_json($service);
        } catch (\Throwable $th) {
            return $this->response_json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Subscription"},
     *     path="/saas/api/cancel_subscription/{clientid}",
     *     summary="Cancel subscription for a client",
     *     @OA\Parameter(
     *         name="clientid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription cancelled successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubscriptionSuccessResponse")
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function cancel_subscription($clientid)
    {
        $this->api_key_middleware('post');

        if (!(int)get_option('perfex_saas_allow_customer_cancel_subscription')) {
            return $this->response_json(['error' => _l('perfex_saas_allow_customer_cancel_subscription')], 423);
        }

        $clientid = (int)$clientid;
        $service = $this->cancelSubscription((int)$clientid);
        return $this->response_json($service);
    }

    /**
     * @OA\Post(
     *     tags={"Subscription"},
     *     path="/saas/api/resume_subscription/{clientid}",
     *     summary="Resume cancelled subscription for a client",
     *     @OA\Parameter(
     *         name="clientid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="Client ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription resumed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubscriptionSuccessResponse")
     *     ),
     *     @OA\Response(response="default", ref="#/components/responses/ServerError"),
     *     security={{"api_key": {}}}
     * )
     */
    public function resume_subscription($clientid)
    {
        $this->api_key_middleware('post');

        if (!(int)get_option('perfex_saas_allow_customer_cancel_subscription')) {
            return $this->response_json(['error' => _l('perfex_saas_allow_customer_cancel_subscription')], 423);
        }

        $clientid = (int)$clientid;
        $service = $this->resumeSubscription((int)$clientid);
        return $this->response_json($service);
    }




    /**
     * Common method to handle create or edit form submission.
     * Client company form validation and execution are summarized in this method.
     *
     * @param string $clientid
     * @param string $tenantid ID of the company to edit (optional)
     * @return void
     */
    private function create_or_edit_company($clientid, $tenantid = '')
    {
        $service = $this->createOrUpdateCompany((int)$clientid, $tenantid);
        return $this->response_json($service);
    }



    /**************************************** Common Helpers ***************************************************/
    /**
     * echo response as json
     *
     * @param array $data
     * @return mixed
     */
    private function response_json(array $data, int $status = 200)
    {
        if (isset($data['error']) && !empty($data['error']))
            $status = 422;

        if (isset($data['package']))
            $data['package'] = $this->package_transformer((object)$data['package']);

        header('Content-Type: application/json');
        set_status_header($status);
        echo json_encode($data);
        exit();
    }

    /**
     * Check and authorize api token key
     *
     * @return mixed
     */
    private function api_key_middleware($method = 'GET')
    {
        $this->api_enabled_middleware();

        $apiMethod = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? null;
        if (empty($apiMethod) || empty($method)) {
            return $this->response_json(['error' => 'TRACE_ERROR' . _l('perfex_saas_permission_denied')], 405);
        }

        $authorization = $this->input->get_request_header("Authorization", true);
        if (empty($authorization) || strlen($authorization) < 32) {

            return $this->response_json(['error' => _l('perfex_saas_api_invalid_credential')], 401);
        }

        $apiUser = perfex_saas_api_users_by_token($authorization);

        $token = $apiUser->token;
        if (empty($token) || strlen($token) < 32) {
            return $this->response_json(['error' => _l('perfex_saas_api_key_invalid')], 402);
        }

        if ($token !== $authorization) {
            return $this->response_json(['error' => _l('perfex_saas_api_invalid_credential')], 403);
        }


        if ($this->input->server('REQUEST_METHOD', true) !== strtoupper($method)) {
            return $this->response_json(['error' => _l('perfex_saas_invalid_method')], 405);
        }


        $access = (int)($apiUser->permissions->{$apiMethod}->{strtolower($method)} ?? 0);
        if ($access !== 1) {
            return $this->response_json(['error' => _l('perfex_saas_permission_denied')], 405);
        }

        return true;
    }


    /**
     * Ensure the api functionality is enabled.
     *
     * @return mixed True when successfull
     */
    private function api_enabled_middleware()
    {
        if (get_option('perfex_saas_enable_api') != 1) {
            return $this->response_json(['error' => _l('perfex_saas_api_not_enabled')], 401);
        }
        return true;
    }

    private function package_transformer($package)
    {
        return [
            "id" => $package->id,
            "name" => $package->name,
            "description" => $package->description,
            "slug" => $package->slug,
            "price" => $package->price,
            "trial_period" => $package->trial_period,
            "is_default" => $package->is_default,
            "is_private" => $package->is_private,
            "db_scheme" => $package->db_scheme,
            "status" => $package->status,
            "modules" => $package->modules,
            "metadata" => [
                "invoice" => $package->metadata->invoice,
                "max_instance_limit" => $package->metadata->max_instance_limit,
                "limitations" => $package->metadata->limitations,
                "enable_subdomain" => $package->metadata->enable_subdomain,
                "enable_custom_domain" => $package->metadata->enable_custom_domain,
                "shared_settings" => $package->metadata->shared_settings ?? []
            ],
        ];
    }
}