import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import hasAnyPermission from '@/lib/utils';
import { User } from '@/types/user';
import { Link, router } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { ArrowUpDown, MoreHorizontal } from 'lucide-react';
import { useState } from 'react';

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return (
                <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                    Name
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return (
                <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                    Email
                    <ArrowUpDown />
                </Button>
            );
        },
        cell: ({ row }) => <div>{row.getValue('email')}</div>,
    },
    {
        id: 'actions',
        cell: ({ row, table }) => {
            const data = row.original;
            const isTrashed = (table.options.meta as { isTrashed?: boolean })?.isTrashed || false;

            return <ActionCell data={data} isTrashed={isTrashed} />;
        },
    },
];

const ActionCell = ({ data, isTrashed = false }: { data: User; isTrashed?: boolean }) => {
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);

    function handleDelete() {
        router.delete(route('users.destroy', data.id), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                setIsDropdownOpen(false);
                console.log('User deleted successfully.');
            },
            onError: () => {
                setIsDropdownOpen(false);
                console.error('Failed to delete user.');
            },
        });
    }

    function handleRestore() {
        router.patch(
            route('users.restore', data.id),
            {},
            {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    setIsDropdownOpen(false);
                    console.log('User restored successfully.');
                },
                onError: () => {
                    setIsDropdownOpen(false);
                    console.error('Failed to restore user.');
                },
            },
        );
    }

    function handleForceDelete() {
        router.delete(route('users.force-delete', data.id), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                setIsDropdownOpen(false);
                console.log('User permanently deleted successfully.');
            },
            onError: () => {
                setIsDropdownOpen(false);
                console.error('Failed to permanently delete user.');
            },
        });
    }

    return (
        <DropdownMenu open={isDropdownOpen} onOpenChange={setIsDropdownOpen}>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="h-8 w-8 p-0">
                    <span className="sr-only">Open menu</span>
                    <MoreHorizontal className="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuSeparator />

                {!isTrashed ? (
                    <>
                        {hasAnyPermission(['users.edit']) && (
                            <Link href={route('users.edit', data.id)}>
                                <DropdownMenuItem>Edit</DropdownMenuItem>
                            </Link>
                        )}

                        {hasAnyPermission(['users.delete']) && (
                            <AlertDialog>
                                <AlertDialogTrigger className="w-full rounded-sm px-2 py-1.5 text-left text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800">
                                    Delete
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                        <AlertDialogDescription>This will move the user to trash. You can restore it later.</AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction onClick={() => handleDelete()}>Continue</AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}
                    </>
                ) : (
                    <>
                        {hasAnyPermission(['users.delete']) && (
                            <AlertDialog>
                                <AlertDialogTrigger className="w-full rounded-sm px-2 py-1.5 text-left text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800">
                                    Restore
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Restore User</AlertDialogTitle>
                                        <AlertDialogDescription>This will restore the user and make it available again.</AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction onClick={() => handleRestore()}>Restore</AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}

                        {hasAnyPermission(['users.delete']) && (
                            <AlertDialog>
                                <AlertDialogTrigger className="w-full rounded-sm px-2 py-1.5 text-left text-sm text-red-600 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                                    Permanently Delete
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Permanently Delete User</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. This will permanently delete the user from our servers.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction onClick={() => handleForceDelete()} className="bg-red-600 hover:bg-red-700">
                                            Permanently Delete
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        )}
                    </>
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
};
