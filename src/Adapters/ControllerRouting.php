<?php

namespace Wordless\Adapters;

trait ControllerRouting
{
    protected bool $public_routes = false;

    public function register_routes()
    {
        $this->registerDestroyRoute();
        $this->registerIndexRoute();
        $this->registerShowRoute();
        $this->registerStoreRoute();
        $this->registerUpdateRoute();
    }

    public function registerDestroyRoute()
    {
        $this->routeBaseRegistration([
            'methods' => Request::METHOD_DELETE,
            'callback' => [$this, self::METHOD_NAME_TO_REST_DESTROY_ITEMS],
            'permission_callback' => [$this, self::PERMISSION_METHOD_NAME_TO_REST_DESTROY_ITEMS],
            'args' => [
                'force' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => __('Whether to bypass Trash and force deletion.'),
                ],
            ],
        ], $this->defineCustomRestBaseWithIdRouteParameter());
    }

    public function registerIndexRoute()
    {
        $this->routeBaseRegistration([
            'methods' => Request::METHOD_GET,
            'callback' => [$this, self::METHOD_NAME_TO_REST_INDEX_ITEMS],
            'permission_callback' => [$this, self::PERMISSION_METHOD_NAME_TO_REST_INDEX_ITEMS],
            'args' => $this->get_collection_params(),
        ]);
    }

    public function registerShowRoute()
    {
        $this->routeBaseRegistration([
            'methods' => Request::METHOD_GET,
            'callback' => [$this, self::METHOD_NAME_TO_REST_SHOW_ITEMS],
            'permission_callback' => [$this, self::PERMISSION_METHOD_NAME_TO_REST_SHOW_ITEMS],
            'args' => ['context' => $this->get_context_param(['default' => 'view'])],
        ], $this->defineCustomRestBaseWithIdRouteParameter());
    }

    public function registerStoreRoute()
    {
        $this->routeBaseRegistration([
            'methods' => Request::METHOD_POST,
            'callback' => [$this, self::METHOD_NAME_TO_REST_INDEX_ITEMS],
            'permission_callback' => [$this, self::PERMISSION_METHOD_NAME_TO_REST_INDEX_ITEMS],
            'args' => $this->get_collection_params(),
        ]);
    }

    public function registerUpdateRoute()
    {
        $this->routeBaseRegistration([
            'methods' => Request::EDITABLE,
            'callback' => [$this, self::METHOD_NAME_TO_REST_UPDATE_ITEMS],
            'permission_callback' => [$this, self::PERMISSION_METHOD_NAME_TO_REST_UPDATE_ITEMS],
            'args' => $this->get_endpoint_args_for_item_schema(Request::EDITABLE),
        ], $this->defineCustomRestBaseWithIdRouteParameter());
    }

    private function defineCustomRestBaseWithIdRouteParameter(): string
    {
        return "/$this->rest_base/(?P<id>[\d]+)";
    }

    private function defineSchemaMethod(): string
    {
        if ($this->public_routes) {
            return self::FULL_SCHEMA_METHOD;
        }

        return !is_null($this->user) ? self::FULL_SCHEMA_METHOD : self::PUBLIC_SCHEMA_METHOD;
    }

    private function mountRouteBaseRegistrationArgs(array $route_details): array
    {
        return [
            // Here we register the readable endpoint for collections.
            $route_details,
            // Register our schema callback.
            'schema' => [$this, $this->defineSchemaMethod()],
        ];
    }

    private function routeBaseRegistration(
        array   $route_details,
        ?string $custom_rest_base = null,
        ?string $custom_namespace = null
    )
    {
        register_rest_route(
            $custom_namespace ?? $this->namespace,
            $custom_rest_base ?? "/$this->rest_base",
            $this->mountRouteBaseRegistrationArgs($route_details)
        );
    }
}