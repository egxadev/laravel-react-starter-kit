import Heading from '@/components/heading';
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

            <div className="w-full p-4">
                <div className="mx-auto space-y-6">
                    <Heading title="Role" description="Create a new role" />

                    <RoleForm mode="create" permissions={permissions} className="max-w-xl" />
                </div>
            </div>
        </AppLayout>
    );
}
