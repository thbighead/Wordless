<?php

use Wordless\Adapters\Role;

return [
    'custom-admin' => [
        'custom_cap_1' => true,
        'custom_cap_2' => true,
    ],
    Role::EDITOR => [
        'moderate_comments' => true,
        'upload_files' => false,
        'custom_capability' => true,
        'another_custom_capability' => false,
    ],
];
