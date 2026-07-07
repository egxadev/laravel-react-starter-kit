'use client';

import type { ColumnDef } from '@tanstack/react-table';
import { ChevronsUpDown } from 'lucide-react';

import { Button } from '@/components/ui/button';
import type {Permission} from '@/types/permission';

export const columns: ColumnDef<Permission>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return (
                <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                    Name
                    <ChevronsUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
    },
];
