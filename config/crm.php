<?php

return [
    'lookups' => [
        'cache_key' => env('CRM_LOOKUPS_CACHE_KEY', 'crm:lookups:all:v1'),
        'cache_ttl' => (int) env('CRM_LOOKUPS_CACHE_TTL', 900),
    ],

    'security' => [
        'hide_existence' => filter_var(env('CRM_HIDE_EXISTENCE', true), FILTER_VALIDATE_BOOL),
    ],

    'meetings' => [
        'allow_lead_owner_edit' => filter_var(env('CRM_MEETINGS_ALLOW_LEAD_OWNER_EDIT', true), FILTER_VALIDATE_BOOL),
    ],

    'pagination' => [
        'use_cursor' => filter_var(env('CRM_USE_CURSOR_PAGINATION', false), FILTER_VALIDATE_BOOL),
    ],
];
