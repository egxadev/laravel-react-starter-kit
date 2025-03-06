import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Permission } from '@/types/permission';
import { Head, usePage } from '@inertiajs/react';
import {
    ColumnFiltersState,
    SortingState,
    VisibilityState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { ChevronDown } from 'lucide-react';
import * as React from 'react';
import { columns } from './partials/data-table';

export default function PermissionIndex() {
    const { breadcrumbs, permissions } = usePage<{
        breadcrumbs: BreadcrumbItem[];
        permissions: Permission[];
    }>().props;
    const [sorting, setSorting] = React.useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([]);
    const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>({});
    const [rowSelection, setRowSelection] = React.useState({});

    const table = useReactTable({
        data: permissions,
        columns,
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            rowSelection,
        },
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={breadcrumbs[0].title} />

            <div className="w-full px-4">
                <div className="flex items-center py-4">
                    <Input
                        placeholder="Filter names..."
                        value={(table.getColumn('name')?.getFilterValue() as string) ?? ''}
                        onChange={(event) => table.getColumn('name')?.setFilterValue(event.target.value)}
                        className="max-w-sm"
                    />
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" className="ml-auto">
                                Columns <ChevronDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {table
                                .getAllColumns()
                                .filter((column) => column.getCanHide())
                                .map((column) => {
                                    return (
                                        <DropdownMenuCheckboxItem
                                            key={column.id}
                                            className="capitalize"
                                            checked={column.getIsVisible()}
                                            onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                        >
                                            {column.id}
                                        </DropdownMenuCheckboxItem>
                                    );
                                })}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            {table.getHeaderGroups().map((headerGroup) => (
                                <TableRow key={headerGroup.id}>
                                    {headerGroup.headers.map((header) => {
                                        return (
                                            <TableHead key={header.id}>
                                                {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                                            </TableHead>
                                        );
                                    })}
                                </TableRow>
                            ))}
                        </TableHeader>
                        <TableBody>
                            {table.getRowModel().rows?.length ? (
                                table.getRowModel().rows.map((row) => (
                                    <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                                        {row.getVisibleCells().map((cell) => (
                                            <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                        ))}
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell colSpan={columns.length} className="h-24 text-center">
                                        No results.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>

                <div className="flex items-center justify-end space-x-2 py-4">
                    <div className="text-muted-foreground flex-1 text-sm">
                        Showing {table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1} to{' '}
                        {Math.min(
                            (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
                            table.getFilteredRowModel().rows.length,
                        )}{' '}
                        of {table.getFilteredRowModel().rows.length} entries.
                    </div>
                    <div className="space-x-2">
                        {/* <Button variant="outline" size="sm" onClick={() => table.previousPage()} disabled={!table.getCanPreviousPage()}>
                            Previous
                        </Button>
                        <Button variant="outline" size="sm" onClick={() => table.nextPage()} disabled={!table.getCanNextPage()}>
                            Next
                        </Button> */}
                        <Pagination>
                            <PaginationContent>
                                <PaginationItem>
                                    <PaginationPrevious
                                        href="#"
                                        onClick={() => {
                                            if (table.getCanPreviousPage()) {
                                                table.previousPage();
                                            }
                                        }}
                                        className={table.getCanPreviousPage() ? '' : 'cursor-default opacity-50'}
                                    />
                                </PaginationItem>
                                {table.getPageCount() > 5 ? (
                                    <>
                                        <PaginationItem>
                                            <PaginationLink
                                                href="#"
                                                isActive={table.getState().pagination.pageIndex === 0}
                                                onClick={() => table.setPageIndex(0)}
                                            >
                                                1
                                            </PaginationLink>
                                        </PaginationItem>
                                        {table.getState().pagination.pageIndex > 2 && <PaginationEllipsis />}
                                        {[
                                            table.getState().pagination.pageIndex - 1,
                                            table.getState().pagination.pageIndex,
                                            table.getState().pagination.pageIndex + 1,
                                        ]
                                            .filter((page) => page > 0 && page < table.getPageCount() - 1)
                                            .map((page) => (
                                                <PaginationItem key={page}>
                                                    <PaginationLink
                                                        href="#"
                                                        isActive={table.getState().pagination.pageIndex === page}
                                                        onClick={() => table.setPageIndex(page)}
                                                    >
                                                        {page + 1}
                                                    </PaginationLink>
                                                </PaginationItem>
                                            ))}
                                        {table.getState().pagination.pageIndex < table.getPageCount() - 3 && <PaginationEllipsis />}
                                        <PaginationItem>
                                            <PaginationLink
                                                href="#"
                                                isActive={table.getState().pagination.pageIndex === table.getPageCount() - 1}
                                                onClick={() => table.setPageIndex(table.getPageCount() - 1)}
                                            >
                                                {table.getPageCount()}
                                            </PaginationLink>
                                        </PaginationItem>
                                    </>
                                ) : (
                                    Array.from({
                                        length: table.getPageCount(),
                                    }).map((_, index) => (
                                        <PaginationItem key={index}>
                                            <PaginationLink
                                                href="#"
                                                isActive={table.getState().pagination.pageIndex === index}
                                                onClick={() => table.setPageIndex(index)}
                                            >
                                                {index + 1}
                                            </PaginationLink>
                                        </PaginationItem>
                                    ))
                                )}
                                <PaginationItem>
                                    <PaginationNext
                                        href="#"
                                        onClick={() => {
                                            if (table.getCanNextPage()) {
                                                table.nextPage();
                                            }
                                        }}
                                        className={!table.getCanNextPage() ? 'cursor-default opacity-50' : ''}
                                    />
                                </PaginationItem>
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
