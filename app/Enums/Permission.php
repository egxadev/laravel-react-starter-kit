<?php

namespace App\Enums;

enum Permission: string
{
    case DashboardIndex = 'dashboard.index';
    case DashboardStatistics = 'dashboard.statistics';
    case DashboardChart = 'dashboard.chart';

    case UsersIndex = 'users.index';
    case UsersCreate = 'users.create';
    case UsersEdit = 'users.edit';
    case UsersDelete = 'users.delete';

    case RolesIndex = 'roles.index';
    case RolesCreate = 'roles.create';
    case RolesEdit = 'roles.edit';
    case RolesDelete = 'roles.delete';

    case PermissionsIndex = 'permissions.index';
}
