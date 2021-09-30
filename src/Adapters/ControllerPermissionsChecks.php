<?php

namespace Wordless\Adapters;

use Wordless\Exception\WordPressFailedToCreateRole;
use Wordless\Helpers\Str;
use WP_Error;

trait ControllerPermissionsChecks
{
    /**
     * @return bool|WP_Error
     */
    public function create_item_permissions_check()
    {
        return $this->resolvePermission($this->createPermissionName());
    }

    /**
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check()
    {
        return $this->resolvePermission($this->deletePermissionName());
    }

    /**
     * @return bool|WP_Error
     */
    public function get_item_permissions_check()
    {
        return $this->resolvePermission($this->getItemPermissionName());
    }

    /**
     * @return bool|WP_Error
     */
    public function get_items_permissions_check()
    {
        return $this->resolvePermission($this->getItemsPermissionName());
    }

    /**
     * @return bool|WP_Error
     */
    public function update_item_permissions_check()
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