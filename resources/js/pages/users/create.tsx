import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import type {BreadcrumbItem, PageProps} from '@/types';
import type {Role} from '@/types/role';
import { UserForm } from './partials/form';

export default function UserCreate({
    breadcrumbs,
    roles,
}: PageProps<{
    breadcrumbs: BreadcrumbItem[];
    roles: Role[];
}>) {
    return (
        <>
            <Head title={breadcrumbs[0].title} />

            <div className="w-full p-4">
                <div className="mx-auto space-y-6">
                    <Heading title="User" description="Create a new user" />

                    <UserForm mode="create" roles={roles} className="max-w-xl" />
                </div>
            </div>
        </>
    );
}
