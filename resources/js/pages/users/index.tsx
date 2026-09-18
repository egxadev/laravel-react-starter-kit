import { Head, router, usePage } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ChevronDown } from 'lucide-react';
import { index as indexUsers, create as createUsers } from '@/routes/users';
import type { BreadcrumbItem } from '@/types';
import type { User } from '@/types/user';
import { columns } from './partials/data-table';

export default function UserIndex() {
    const { breadcrumbs, data, meta, filters } = usePage<{
        breadcrumbs: BreadcrumbItem[];
        data: User[];
        meta: { current_page: number; last_page: number; per_page: number; total: number; from: number; to: number };
        filters: { search: string; sort_by: string; sort_dir: string; trashed: boolean };
    }>().props;

    function setTrashed(value: boolean) {
        router.get(indexUsers().url, { ...filters, trashed: value, page: 1 }, { preserveState: true, replace: true });
    }

    return (
        <>
            <Head title={breadcrumbs[0].title} />
            <DataTable
                columns={columns}
                data={data}
                meta={meta}
                filters={filters}
                routeUrl={indexUsers()}
                tableMeta={{ isTrashed: filters.trashed }}
                createHref={createUsers()}
                createLabel="Add User"
                extraFilters={
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" className="w-full sm:w-auto">
                                {filters.trashed ? 'Trashed' : 'Active'} <ChevronDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                onClick={() => setTrashed(false)}
                                className={!filters.trashed ? 'bg-accent' : ''}
                            >
                                Active
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                onClick={() => setTrashed(true)}
                                className={filters.trashed ? 'bg-accent' : ''}
                            >
                                Trashed
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                }
            />
        </>
    );
}
