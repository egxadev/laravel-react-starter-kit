import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Toaster } from "@/components/ui/sonner"
import type { AppLayoutProps } from '@/types';

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        {children}
        <Toaster />
    </AppLayoutTemplate>
);
