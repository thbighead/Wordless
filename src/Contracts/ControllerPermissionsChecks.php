<?php

namespace Wordless\Contracts;

use Wordless\Exception\WordPressFailedToCreateRole;
use Wordless\Helpers\Str;
use WP_Error;
use WP_REST_Request;

trait ControllerPermissionsChecks
{
    /**
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function create_item_permissions_check($request)
    {
        return $this->resolvePermission($this->createPermissionName());
    }

    /**
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check($request)
    {
        return $this->resolvePermission($this->deletePermissionName());
    }

    /**
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_item_permissions_check($request)
    {
        return $this->resolvePermission($this->getItemPermissionName());
    }

    /**
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function get_items_permissions_check($request)
    {
        return $this->resolvePermission($this->getItemsPermissionName());
    }

    /**
     * @param WP_REST_Request $request
     * @return bool|WP_Error
     */
    public function update_item_permissions_check($request)
    {
        return $this->resolvePermission($this->updatePermissionName());
    }

    /**
     * @throws WordPressFailedToCreateRole
     */
    public function registerCapabilitiesToRoles()
    {
        $capabilities = [
            $this->deletePermissionName(),
            $this->getItemsPermissionName(),
            $this->getItemPermissionName(),
            $this->createPermissionName(),
            $this->updatePermissionName(),
        ];

        foreach ($this->allowed_roles_names as $string_role) {
            if (($role = get_role($string_role)) === null) {
                $role = add_role($string_role, Str::titleCase($string_role), $capabilities);
            }

            if ($role === null) {
                throw new WordPressFailedToCreateRole($string_role);
            }
        }
    }

    private function createPermissionName(): string
    {
        return "store_{$this->resourceName()}";
    }

    private function deletePermissionName(): string
    {
        return "destroy_{$this->resourceName()}";
    }

    private function getItemPermissionName(): string
    {
        return "show_{$this->resourceName()}";
    }

    private function getItemsPermissionName(): string
    {
        return "index_{$this->resourceName()}";
    }

    private function updatePermissionName(): string
    {
        return "update_{$this->resourceName()}";
    }

    /**
     * @param string $capability
     * @return bool|WP_Error
     */
    private function resolvePermission(string $capability)
    {
        if (!$this->getCurrentUser()->can($capability)) {
            return $this->buildForbiddenContextError(__METHOD__);
        }

        return true;
    }
}