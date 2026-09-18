import type { ColumnDef } from '@tanstack/react-table';
import { ChevronsUpDown } from 'lucide-react';
import {
    DataTableRowActions,
    useDeleteAction,
} from '@/components/data-table-row-actions';
import { Button } from '@/components/ui/button';
import { useHasAnyPermission } from '@/lib/utils';
import { destroy as destroyRoles, edit as editRoles } from '@/routes/roles';
import type { Role } from '@/types/role';

export const columns: ColumnDef<Role>[] = [
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
        id: 'actions',
        cell: ({ row }) => {
            const data = row.original;
            return <ActionCell data={data} />;
        },
    },
];

const ActionCell = ({ data }: { data: Role }) => {
    const hasAnyPermission = useHasAnyPermission();
    const handleDelete = useDeleteAction(destroyRoles({ role: data.id }).url);

    return (
        <DataTableRowActions
            actions={[
                {
                    label: 'Edit',
                    href: editRoles({ role: data.id }).url,
                    permission: hasAnyPermission(['roles.edit']),
                },
                {
                    label: 'Delete',
                    permission: hasAnyPermission(['roles.delete']),
                    confirm: {
                        title: 'Are you absolutely sure?',
                        description:
                            'This action cannot be undone. This will permanently delete your data from our servers.',
                    },
                    onConfirm: handleDelete,
                },
            ]}
        />
    );
};
