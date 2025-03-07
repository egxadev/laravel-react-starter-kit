import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Permission } from '@/types/permission';
import { Role } from '@/types/role';
import { User } from '@/types/user';
import { Head } from '@inertiajs/react';
import { RoleForm } from './partials/form';

export default function RoleEdit({
    auth,
    breadcrumbs,
    role,
    permissions,
}: PageProps<{
    auth: { user: User; permissions: string[] };
    breadcrumbs: BreadcrumbItem[];
    role: Role;
    permissions: Permission[];
}>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <RoleForm auth={auth} role={role} permissions={permissions} mode="edit" className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
