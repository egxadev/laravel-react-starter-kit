import { router } from '@inertiajs/react';
import type { RouteDefinition } from '@/wayfinder';
import type { ColumnDef } from '@tanstack/react-table';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import type { SortingState, VisibilityState } from '@tanstack/react-table';
import { ChevronDown } from 'lucide-react';
import * as React from 'react';
import { DataTablePagination } from '@/components/data-table-pagination';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface DataTableProps<TData> {
    columns: ColumnDef<TData>[];
    data: TData[];
    meta: PaginationMeta;
    filters: Record<string, unknown>;
    routeUrl: RouteDefinition<'get'> | string;
    extraFilters?: React.ReactNode;
    createHref?: RouteDefinition<'get'> | string;
    createLabel?: string;
    tableMeta?: Record<string, unknown>;
}

export function DataTable<TData>({
    columns,
    data,
    meta,
    filters,
    routeUrl,
    extraFilters,
    createHref,
    createLabel = 'Add',
    tableMeta,
}: DataTableProps<TData>) {
    const toUrl = (href: RouteDefinition<'get'> | string): string =>
        typeof href === 'string' ? href : href.url;
    const [search, setSearch] = React.useState(
        (filters.search as string) ?? '',
    );
    const [sorting, setSorting] = React.useState<SortingState>([
        {
            id: (filters.sort_by as string) ?? 'created_at',
            desc: filters.sort_dir === 'desc',
        },
    ]);
    const [columnVisibility, setColumnVisibility] =
        React.useState<VisibilityState>({});
    const [rowSelection, setRowSelection] = React.useState({});
    const [isInitialRender, setIsInitialRender] = React.useState(true);

    React.useEffect(() => {
        setIsInitialRender(false);
    }, []);

    const handleServerOperation = React.useCallback(
        (params: {
            page?: number;
            per_page?: number;
            sort_by?: string;
            sort_dir?: string;
            search?: string;
            [key: string]: unknown;
        }) => {
            router.get(
                toUrl(routeUrl),
                {
                    ...filters,
                    ...params,
                    page: params.page ?? meta.current_page,
                    per_page: params.per_page ?? meta.per_page,
                },
                { preserveState: true, replace: true },
            );
        },
        [routeUrl, filters, meta.current_page, meta.per_page],
    );

    React.useEffect(() => {
        if (isInitialRender) return;
        const timer = setTimeout(
            () => handleServerOperation({ search, page: 1 }),
            500,
        );
        return () => clearTimeout(timer);
    }, [search]);

    React.useEffect(() => {
        if (isInitialRender) return;
        if (sorting.length > 0) {
            handleServerOperation({
                sort_by: sorting[0].id,
                sort_dir: sorting[0].desc ? 'desc' : 'asc',
                page: 1,
            });
        }
    }, [sorting]);

    const table = useReactTable({
        data,
        columns,
        manualPagination: true,
        manualSorting: true,
        manualFiltering: true,
        pageCount: meta.last_page,
        onSortingChange: setSorting,
        onColumnVisibilityChange: setColumnVisibility,
        onRowSelectionChange: setRowSelection,
        getCoreRowModel: getCoreRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        meta: tableMeta,
        state: {
            sorting,
            pagination: {
                pageIndex: meta.current_page - 1,
                pageSize: meta.per_page,
            },
            columnVisibility,
            rowSelection,
        },
    });

    return (
        <div className="w-full px-2 sm:px-4">
            <div className="flex flex-col gap-2 py-4 sm:flex-row sm:items-center sm:gap-4">
                <div className="flex w-full flex-col gap-2 sm:flex-row sm:items-center sm:gap-2">
                    <Input
                        placeholder="Search..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="max-w-full sm:max-w-sm"
                    />
                </div>
                <div className="flex w-full flex-row gap-1 sm:w-auto">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                variant="outline"
                                className="w-full sm:w-auto"
                            >
                                Columns <ChevronDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {table
                                .getAllColumns()
                                .filter((col) => col.getCanHide())
                                .map((col) => (
                                    <DropdownMenuCheckboxItem
                                        key={col.id}
                                        className="capitalize"
                                        checked={col.getIsVisible()}
                                        onCheckedChange={(value) =>
                                            col.toggleVisibility(!!value)
                                        }
                                    >
                                        {col.id}
                                    </DropdownMenuCheckboxItem>
                                ))}
                        </DropdownMenuContent>
                    </DropdownMenu>

                    {extraFilters}

                    {createHref && (
                        <a
                            href={toUrl(createHref)}
                            className="w-full sm:w-auto"
                        >
                            <Button className="w-full sm:w-auto">
                                {createLabel}
                            </Button>
                        </a>
                    )}
                </div>
            </div>

            <div className="overflow-x-auto rounded-md border">
                <Table className="text-sm sm:text-base">
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => (
                                    <TableHead
                                        key={header.id}
                                        className="px-2 py-2 text-center text-xs whitespace-nowrap sm:text-sm"
                                    >
                                        {header.isPlaceholder
                                            ? null
                                            : flexRender(
                                                  header.column.columnDef
                                                      .header,
                                                  header.getContext(),
                                              )}
                                    </TableHead>
                                ))}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow
                                    key={row.id}
                                    data-state={
                                        row.getIsSelected() && 'selected'
                                    }
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell
                                            key={cell.id}
                                            className="px-2 py-2 text-xs whitespace-nowrap sm:text-sm"
                                        >
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center"
                                >
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>

            <DataTablePagination
                meta={meta}
                onPageChange={(page) => handleServerOperation({ page })}
                onPerPageChange={(perPage) =>
                    handleServerOperation({ per_page: perPage, page: 1 })
                }
            />
        </div>
    );
}
