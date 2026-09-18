import type { ColumnDef } from '@tanstack/react-table';
import { ChevronsUpDown } from 'lucide-react';
import {
    DataTableRowActions,
    useDeleteAction,
    usePatchAction,
} from '@/components/data-table-row-actions';
import { Button } from '@/components/ui/button';
import { useHasAnyPermission } from '@/lib/utils';
import {
    destroy as destroyUsers,
    edit as editUsers,
    forceDelete as forceDeleteUsers,
    restore as restoreUsers,
} from '@/routes/users';
import type { User } from '@/types/user';

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() =>
                    column.toggleSorting(column.getIsSorted() === 'asc')
                }
            >
                Name
                <ChevronsUpDown className="ml-2 h-4 w-4" />
            </Button>
        ),
    },
    {
        accessorKey: 'email',
        header: ({ column }) => (
            <Button
                variant="ghost"
                onClick={() =>
                    column.toggleSorting(column.getIsSorted() === 'asc')
                }
            >
                Email
                <ChevronsUpDown />
            </Button>
        ),
        cell: ({ row }) => <div>{row.getValue('email')}</div>,
    },
    {
        id: 'actions',
        cell: ({ row, table }) => {
            const data = row.original;
            const isTrashed =
                (table.options.meta as { isTrashed?: boolean })?.isTrashed ||
                false;

            return <ActionCell data={data} isTrashed={isTrashed} />;
        },
    },
];

const ActionCell = ({
    data,
    isTrashed = false,
}: {
    data: User;
    isTrashed?: boolean;
}) => {
    const hasAnyPermission = useHasAnyPermission();
    const handleDelete = useDeleteAction(destroyUsers({ user: data.id }).url);
    const handleRestore = usePatchAction(restoreUsers({ user: data.id }).url);
    const handleForceDelete = useDeleteAction(
        forceDeleteUsers({ user: data.id }).url,
    );

    const activeActions = [
        {
            label: 'Edit',
            href: editUsers({ user: data.id }).url,
            permission: hasAnyPermission(['users.edit']),
        },
        {
            label: 'Delete',
            permission: hasAnyPermission(['users.delete']),
            confirm: {
                title: 'Are you absolutely sure?',
                description:
                    'This will move the user to trash. You can restore it later.',
            },
            onConfirm: handleDelete,
        },
    ];

    const trashedActions = [
        {
            label: 'Restore',
            permission: hasAnyPermission(['users.delete']),
            confirm: {
                title: 'Restore User',
                description:
                    'This will restore the user and make it available again.',
                confirmLabel: 'Restore',
            },
            onConfirm: handleRestore,
        },
        {
            label: 'Permanently Delete',
            variant: 'destructive' as const,
            permission: hasAnyPermission(['users.delete']),
            confirm: {
                title: 'Permanently Delete User',
                description:
                    'This action cannot be undone. This will permanently delete the user from our servers.',
                confirmLabel: 'Permanently Delete',
            },
            onConfirm: handleForceDelete,
        },
    ];

    return (
        <DataTableRowActions
            actions={isTrashed ? trashedActions : activeActions}
        />
    );
};
