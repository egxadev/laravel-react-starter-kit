import { NavItem } from '@/types';
import { BookOpen, Folder, KeyRound, LayoutGrid, UserRoundCog, UsersRound } from 'lucide-react';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
        permission: ['dashboard.index'],
    },
    {
        title: 'Permission',
        url: '/permissions',
        icon: KeyRound,
        permission: ['permissions.index'],
    },
    {
        title: 'Role',
        url: '/roles',
        icon: UserRoundCog,
        permission: ['roles.index'],
    },
    {
        title: 'Customers',
        url: '/customers',
        icon: UsersRound,
        permission: ['customers.index'],
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        url: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        url: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];

export const settingsNavItems: NavItem[] = [
    {
        title: 'Profile',
        url: '/settings/profile',
        icon: null,
    },
    {
        title: 'Password',
        url: '/settings/password',
        icon: null,
    },
    {
        title: 'Appearance',
        url: '/settings/appearance',
        icon: null,
    },
];
