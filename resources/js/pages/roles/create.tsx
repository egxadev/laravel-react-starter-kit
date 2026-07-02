import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import type {BreadcrumbItem, PageProps} from '@/types';
import type {Permission} from '@/types/permission';
import { RoleForm } from './partials/form';

export default function RoleCreate({
    breadcrumbs,
    permissions,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    permissions: Permission[];
}>) {
    return (
        <>
            <Head title={breadcrumbs[0].title} />

            <div className="w-full p-4">
                <div className="mx-auto space-y-6">
                    <Heading title="Role" description="Create a new role" />

                    <RoleForm mode="create" permissions={permissions} className="max-w-xl" />
                </div>
            </div>
        </>
    );
}
