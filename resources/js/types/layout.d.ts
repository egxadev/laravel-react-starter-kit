import type { ReactNode } from 'react';
import type { BreadcrumbItem } from '@/types/navigation';

export interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export interface AppHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export interface AuthLayoutProps {
    children?: ReactNode;
    name?: string;
    title?: string;
    description?: string;
}
