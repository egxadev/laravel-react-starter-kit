import { Head, usePage } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { index as indexPermissions } from '@/routes/permissions';
import type { BreadcrumbItem } from '@/types';
import type { Permission } from '@/types/permission';
import { columns } from './partials/data-table';

export default function PermissionIndex() {
    const { breadcrumbs, data, meta, filters } = usePage<{
        breadcrumbs: BreadcrumbItem[];
        data: Permission[];
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            from: number;
            to: number;
        };
        filters: { search: string; sort_by: string; sort_dir: string };
    }>().props;

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
