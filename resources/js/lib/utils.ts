import { usePage } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export default function hasAnyPermission(permissions: any) {
    //destruct auth from props
    const { auth } = usePage().props;

    //get permissions from props
    let allPermissions = auth.permissions;

    let hasPermission = false;

    permissions.forEach(function (item: any) {
        if (allPermissions[item]) hasPermission = true;
    });

    return hasPermission;
}
