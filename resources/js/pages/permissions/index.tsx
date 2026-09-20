import { Head, usePage } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { index as indexPermissions } from '@/routes/permissions';
import type { BreadcrumbItem, Listing } from '@/types';
import type { Permission } from '@/types/permission';
import { columns } from './partials/data-table';

export default function PermissionIndex() {
    const { breadcrumbs, data, meta, filters } = usePage<
        { breadcrumbs: BreadcrumbItem[] } & Listing<Permission>
    >().props;

    return (
        <>
            <Head title={breadcrumbs[0].title} />
            <DataTable
                columns={columns}
                data={data}
                meta={meta}
                filters={filters}
                routeUrl={indexPermissions()}
            />
        </>
    );
}
