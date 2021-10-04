<?php

namespace Wordless\Contracts;

use Symfony\Component\HttpFoundation\JsonResponse;
use WP_Error;

trait ControllerErrorHandling
{
    private function buildInvalidMethodError(string $method_name): WP_Error
    {
        return new WP_Error(
            self::INVALID_METHOD_CODE,
            /* translators: %s: Method name. */
            sprintf(__('Method \'%s\' not implemented. Must be overridden in subclass.'), $method_name),
            ['status' => JsonResponse::HTTP_METHOD_NOT_ALLOWED]
        );
    }

    private function buildForbiddenContextError(string $method_name): WP_Error
    {
        return new WP_Error(
            self::FORBIDDEN_CONTEXT_CODE,
            __('Sorry, you are not allowed to edit posts in this post type.'),
            ['status' => rest_authorization_required_code()]
        );
    }
}