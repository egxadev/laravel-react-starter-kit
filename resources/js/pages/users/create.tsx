import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Role } from '@/types/role';
import { Head } from '@inertiajs/react';
import { UserForm } from './partials/form';

export default function UserCreate({
    breadcrumbs,
    roles,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    roles: Role[];
}>) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="p-4 shadow sm:rounded-lg sm:p-8">
                        <UserForm mode="create" roles={roles} className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
