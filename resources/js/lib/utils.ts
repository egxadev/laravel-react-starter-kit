import { usePage } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

type AuthProps = {
    auth: {
        permissions: Record<string, boolean>;
    };
};

export default function hasAnyPermission(permissions: string[]): boolean {
    const { auth } = usePage<AuthProps>().props;

    return permissions.some((permission) => auth.permissions[permission]);
}
