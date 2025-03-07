import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Permission } from '@/types/permission';
import { User } from '@/types/user';
import { Head } from '@inertiajs/react';
import { RoleForm } from './partials/form';

export default function RoleCreate({
    auth,
    breadcrumbs,
    permissions,
}: PageProps<{
    auth: { user: User; permissions: string[] };
    breadcrumbs: BreadcrumbItem[];
    permissions: Permission[];
}>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <RoleForm auth={auth} permissions={permissions} mode="create" className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
