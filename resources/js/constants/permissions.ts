export const Permission = {
    DashboardIndex: 'dashboard.index',
    DashboardStatistics: 'dashboard.statistics',
    DashboardChart: 'dashboard.chart',
    UsersIndex: 'users.index',
    UsersCreate: 'users.create',
    UsersEdit: 'users.edit',
    UsersDelete: 'users.delete',
    RolesIndex: 'roles.index',
    RolesCreate: 'roles.create',
    RolesEdit: 'roles.edit',
    RolesDelete: 'roles.delete',
    PermissionsIndex: 'permissions.index',
} as const;

export type PermissionName = (typeof Permission)[keyof typeof Permission];
