import { NavItem } from '@/types';
import { BookOpen, Folder, KeyRound, LayoutGrid, Rocket, UserRoundCog, UsersRound } from 'lucide-react';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
        permission: ['dashboard.index'],
    },
    {
        title: 'Permission',
        href: '/permissions',
        icon: KeyRound,
        permission: ['permissions.index'],
    },
    {
        title: 'Role',
        href: '/roles',
        icon: UserRoundCog,
        permission: ['roles.index'],
    },
    {
        title: 'User',
        href: '/users',
        icon: UsersRound,
        permission: ['users.index'],
    },
    {
        title: 'Resources',
        href: '#',
        icon: Rocket,
        permission: ['users.index'],
        items: [
            {
                title: 'Repository',
                href: 'https://github.com/laravel/react-starter-kit',
            },
            {
                title: 'Documentation',
                href: 'https://laravel.com/docs/starter-kits',
            },
        ],
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];

export const settingsNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: '/settings/profile',
        icon: null,
    },
    {
        title: 'Password',
        href: '/settings/password',
        icon: null,
    },
    {
        title: 'Appearance',
        href: '/settings/appearance',
        icon: null,
    },
];
