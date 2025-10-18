import { type NavItem } from '@/types';
import { BookOpen, Folder, KeyRound, LayoutGrid, Rocket, UserRoundCog, UsersRound } from 'lucide-react';
import { index as indexDashboard } from '@/routes/dashboard';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editPassword } from '@/routes/password';
import { edit } from '@/routes/profile';
import { show } from '@/routes/two-factor';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: indexDashboard(),
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
        href: edit(),
        icon: null,
    },
    {
        title: 'Password',
        href: editPassword(),
        icon: null,
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
        icon: null,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: null,
    },
];
