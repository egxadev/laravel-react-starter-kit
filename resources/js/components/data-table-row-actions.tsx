import { Link, router } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { useState } from 'react';
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
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export interface RowAction {
    label: string;
    href?: string;
    permission?: boolean;
    variant?: 'default' | 'destructive';
    confirm?: {
        title: string;
        description: string;
        confirmLabel?: string;
    };
    onConfirm?: () => void;
}

interface DataTableRowActionsProps {
    actions: RowAction[];
}

export function DataTableRowActions({ actions }: DataTableRowActionsProps) {
    const [isOpen, setIsOpen] = useState(false);
    const visible = actions.filter((a) => a.permission !== false);

    return (
        <DropdownMenu open={isOpen} onOpenChange={setIsOpen}>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="h-8 w-8 p-0">
                    <span className="sr-only">Open menu</span>
                    <MoreHorizontal className="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuSeparator />

                {visible.map((action, i) => {
                    const isDestructive = action.variant === 'destructive';
                    const triggerClass = `w-full rounded-sm px-2 py-1.5 text-left text-sm hover:bg-neutral-100 dark:hover:bg-neutral-800${isDestructive ? ' text-red-600' : ''}`;

                    if (action.confirm && action.onConfirm) {
                        return (
                            <AlertDialog key={i}>
                                <AlertDialogTrigger className={triggerClass}>
                                    {action.label}
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>{action.confirm.title}</AlertDialogTitle>
                                        <AlertDialogDescription>{action.confirm.description}</AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction
                                            onClick={() => {
                                                action.onConfirm!();
                                                setIsOpen(false);
                                            }}
                                            className={isDestructive ? 'bg-red-600 hover:bg-red-700' : undefined}
                                        >
                                            {action.confirm.confirmLabel ?? 'Continue'}
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        );
                    }

                    if (action.href) {
                        return (
                            <Link key={i} href={action.href} className={triggerClass}>
                                {action.label}
                            </Link>
                        );
                    }

                    return null;
                })}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

export function useDeleteAction(url: string, onDone?: () => void) {
    return () =>
        router.delete(url, {
            preserveState: false,
            preserveScroll: true,
            onFinish: onDone,
        });
}

export function usePatchAction(url: string, onDone?: () => void) {
    return () =>
        router.patch(
            url,
            {},
            {
                preserveState: false,
                preserveScroll: true,
                onFinish: onDone,
            },
        );
}
