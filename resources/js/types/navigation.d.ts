import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import type { PermissionName } from '@/constants/permissions';

export interface BreadcrumbItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
    permission?: PermissionName[];
    items?: NavItem[];
}
