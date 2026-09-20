import { Head, usePage } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { index as indexRoles, create as createRoles } from '@/routes/roles';
import type { BreadcrumbItem, Listing } from '@/types';
import type { Role } from '@/types/role';
import { columns } from './partials/data-table';

export default function RoleIndex() {
    const { breadcrumbs, data, meta, filters } = usePage<
        { breadcrumbs: BreadcrumbItem[] } & Listing<Role>
    >().props;

    return (
        <>
            <Head title={breadcrumbs[0].title} />
            <DataTable
                columns={columns}
                data={data}
                meta={meta}
                filters={filters}
                routeUrl={indexRoles()}
                createHref={createRoles()}
                createLabel="Add Role"
            />
        </>
    );
}
