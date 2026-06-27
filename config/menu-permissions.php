<?php

/**
 * Respaldo de permisos por código de ítem cuando menu_items.permission es null.
 * Preferir definir permission en MenuSeeder.
 */
return [
    'users.list' => 'users.view',
    'users.roles-permissions' => 'roles.view',
    'business-admin.businesses' => 'businesses.view',
    'church-admin.zones' => 'zones.view',
    'church-admin.churches' => 'churches.view',
];
