import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Permission } from '@/types/permission';
import { Role } from '@/types/role';
import { Head } from '@inertiajs/react';
import { RoleForm } from './partials/form';

export default function RoleEdit({
    breadcrumbs,
    role,
    permissions,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    role: Role;
    permissions: Permission[];
}>) {

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="w-full p-4">
                <div className="mx-auto space-y-6">
                    <Heading title="Role" description="Update a role" />

                    <RoleForm mode="edit" role={role} permissions={permissions} className="max-w-xl" />
                </div>
            </div>
        </AppLayout>
    );
}
