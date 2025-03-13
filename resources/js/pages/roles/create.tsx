import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Permission } from '@/types/permission';
import { Head } from '@inertiajs/react';
import { RoleForm } from './partials/form';

export default function RoleCreate({
    breadcrumbs,
    permissions,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    permissions: Permission[];
}>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="p-4 shadow sm:rounded-lg sm:p-8">
                        <RoleForm mode="create" permissions={permissions} className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
