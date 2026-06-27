<?php

return [
    'modules' => [
        'users' => [
            'name' => 'Gestión de usuarios',
            'permissions' => [
                'users.view' => 'Ver usuarios',
                'users.create' => 'Crear usuarios',
                'users.edit' => 'Editar usuarios',
                'users.delete' => 'Eliminar usuarios',
                'users.activate' => 'Activar usuarios',
                'users.deactivate' => 'Desactivar usuarios',
            ],
        ],
        'businesses' => [
            'name' => 'Administrar negocios',
            'super_admin_only' => true,
            'permissions' => [
                'businesses.view' => 'Ver negocios',
                'businesses.create' => 'Crear negocios',
                'businesses.edit' => 'Editar negocios',
                'businesses.delete' => 'Eliminar negocios',
            ],
        ],
        'education' => [
            'name' => 'Educación',
            'permissions' => [
                'education.view' => 'Ver educación',
                'education.create' => 'Crear registros',
                'education.edit' => 'Editar registros',
                'education.delete' => 'Eliminar registros',
            ],
        ],
        'members' => [
            'name' => 'Miembros',
            'permissions' => [
                'members.view' => 'Ver miembros',
                'members.create' => 'Crear miembros',
                'members.edit' => 'Editar miembros',
                'members.delete' => 'Eliminar miembros',
            ],
        ],
        'reports' => [
            'name' => 'Reportes',
            'permissions' => [
                'reports.view' => 'Ver reportes',
                'reports.export' => 'Exportar reportes',
            ],
        ],
        'settings' => [
            'name' => 'Configuración',
            'permissions' => [
                'settings.view' => 'Ver configuración',
                'settings.edit' => 'Editar configuración',
            ],
        ],
        'roles' => [
            'name' => 'Roles y permisos',
            'permissions' => [
                'roles.view' => 'Ver roles',
                'roles.create' => 'Crear roles',
                'roles.edit' => 'Editar roles',
                'roles.delete' => 'Eliminar roles',
                'permissions.view' => 'Ver permisos',
                'permissions.assign' => 'Asignar permisos',
            ],
        ],
        'zones' => [
            'name' => 'Zonas administrativas',
            'permissions' => [
                'zones.view' => 'Ver zonas',
                'zones.create' => 'Crear zonas',
                'zones.edit' => 'Editar zonas',
                'zones.delete' => 'Eliminar zonas',
            ],
        ],
        'churches' => [
            'name' => 'Iglesias',
            'permissions' => [
                'churches.view' => 'Ver iglesias',
                'churches.create' => 'Crear iglesias',
                'churches.edit' => 'Editar iglesias',
                'churches.delete' => 'Eliminar iglesias',
            ],
        ],
    ],

    'roles' => [
        'superAdmin' => [
            'name' => 'Super administrador',
            'description' => 'Acceso completo al sistema',
            'level' => 1,
        ],
        'Administrador' => [
            'name' => 'Administrador',
            'description' => 'Administración general de la iglesia',
            'level' => 2,
        ],
        'Pastor' => [
            'name' => 'Pastor',
            'description' => 'Liderazgo pastoral',
            'level' => 3,
        ],
        'Presbitero' => [
            'name' => 'Presbítero',
            'description' => 'Apoyo pastoral y administrativo',
            'level' => 3,
        ],
        'Co-pastor' => [
            'name' => 'Co-pastor',
            'description' => 'Gestión de usuarios del negocio',
            'level' => 4,
        ],
        'Lider' => [
            'name' => 'Líder',
            'description' => 'Liderazgo de grupo o ministerio',
            'level' => 4,
        ],
        'Asistente' => [
            'name' => 'Asistente',
            'description' => 'Apoyo operativo',
            'level' => 5,
        ],
        'Maestro' => [
            'name' => 'Maestro',
            'description' => 'Educación y formación',
            'level' => 5,
        ],
        'Coordinador educativo' => [
            'name' => 'Coordinador educativo',
            'description' => 'Coordinación del área educativa',
            'level' => 5,
        ],
    ],
];
