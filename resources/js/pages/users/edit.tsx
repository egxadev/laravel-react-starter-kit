import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, PageProps } from '@/types';
import { Role } from '@/types/role';
import { User } from '@/types/user';
import { Head } from '@inertiajs/react';
import { UserForm } from './partials/form';

export default function UserEdit({
    breadcrumbs,
    user,
    roles,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    user: User;
    roles: Role[];
}>) {

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="w-full p-4">
                <div className="mx-auto space-y-6">
                    <Heading title="User" description="Update a user" />

                    <UserForm mode="edit" user={user} roles={roles} className="max-w-xl" />
                </div>
            </div>
        </AppLayout>
    );
}
