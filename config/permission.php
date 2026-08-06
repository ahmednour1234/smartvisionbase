<?php

return [

    'models' => [

        'permission' => Spatie\Permission\Models\Permission::class,

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        'roles' => 'roles',

        'permissions' => 'permissions',

        'model_has_permissions' => 'model_has_permissions',

        'model_has_roles' => 'model_has_roles',

        'role_has_permissions' => 'role_has_permissions',

    ],

    'column_names' => [

        /*
         * Change this if you want to name the related model primary key other than `model_id`.
         *
         * For example, this would be nice if your primary keys are UUIDs.
         */
        'model_morph_key' => 'model_id',

    ],

    /*
     * When set to true, Spatie Permission will register a Gate check method,
     * so you can call: $user->can('permission-name')
     */
    'register_permission_check_method' => true,

    /*
     * When set to true, a role/permission exception message will include the role/permission names.
     */
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,

    /*
     * Enables wildcard permissions like: `leads.*`
     */
    'enable_wildcard_permission' => false,

    /*
     * Cache settings
     */
    'cache' => [

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        'key' => 'spatie.permission.cache',

        'store' => 'default',
    ],

    /*
     * If you use multiple guards, you can set the default guard name here.
     * Filament typically uses the `web` guard.
     */
    'default_guard_name' => 'web',

    /*
     * Teams support (disabled)
     */
    'teams' => false,

    /*
     * Passport settings (unused)
     */
    'use_passport_client_credentials' => false,
    'use_passport_token_guard' => false,
];
