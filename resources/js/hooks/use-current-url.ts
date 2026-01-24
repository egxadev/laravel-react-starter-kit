import { toUrl } from '@/lib/utils';
import type { InertiaLinkProps } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';

export type IsCurrentUrlFn = (
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl?: string,
) => boolean;

export type WhenCurrentUrlFn = <TIfTrue, TIfFalse = null>(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    ifTrue: TIfTrue,
    ifFalse?: TIfFalse,
) => TIfTrue | TIfFalse;

export type IsActiveOrChildFn = (
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl?: string,
) => boolean;

export type UseCurrentUrlReturn = {
    currentUrl: string;
    isCurrentUrl: IsCurrentUrlFn;
    whenCurrentUrl: WhenCurrentUrlFn;
    isActiveOrChild: IsActiveOrChildFn;
};

export function useCurrentUrl(): UseCurrentUrlReturn {
    const page = usePage();
    const currentUrlPath = new URL(page.url, window?.location.origin).pathname;

    const isCurrentUrl: IsCurrentUrlFn = (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) => {
        const urlToCompare = currentUrl ?? currentUrlPath;
        const urlString = toUrl(urlToCheck);

        if (!urlString.startsWith('http')) {
            return urlString === urlToCompare;
        }

        try {
            const absoluteUrl = new URL(urlString);
            return absoluteUrl.pathname === urlToCompare;
        } catch {
            return false;
        }
    };

    const whenCurrentUrl: WhenCurrentUrlFn = <TIfTrue, TIfFalse = null>(
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        ifTrue: TIfTrue,
        ifFalse: TIfFalse = null as TIfFalse,
    ): TIfTrue | TIfFalse => {
        return isCurrentUrl(urlToCheck) ? ifTrue : ifFalse;
    };

    // Check if current URL matches or is a child of the given URL
    // This handles cases like /users matching /users/create, /users/1/edit, etc.
    const isActiveOrChild: IsActiveOrChildFn = (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) => {
        const urlToCompare = currentUrl ?? currentUrlPath;
        const urlString = toUrl(urlToCheck);

        // Normalize paths by removing trailing slashes (except root)
        const normalizePath = (path: string): string => {
            return path === '/' ? path : path.replace(/\/$/, '');
        };

        const normalizedBase = normalizePath(urlString);
        const normalizedCurrent = normalizePath(urlToCompare);

        // Skip hash links and invalid URLs
        if (urlString === '#' || !normalizedBase || !normalizedCurrent) {
            return false;
        }

        // Handle absolute URLs
        if (urlString.startsWith('http')) {
            try {
                const absoluteUrl = new URL(urlString);
                const absoluteBase = normalizePath(absoluteUrl.pathname);
                if (absoluteBase === normalizedCurrent) {
                    return true;
                }
                const baseWithSlash = absoluteBase + '/';
                return normalizedCurrent.startsWith(baseWithSlash);
            } catch {
                return false;
            }
        }

        // Exact match
        if (normalizedBase === normalizedCurrent) {
            return true;
        }

        // Check if current path is a child of the base path
        // e.g., /users should match /users/create, /users/1/edit, etc.
        // But NOT /users-settings or /usersettings
        // We ensure the base path is followed by '/' or end of string
        const baseWithSlash = normalizedBase + '/';
        if (normalizedCurrent.startsWith(baseWithSlash)) {
            return true;
        }

        return false;
    };

    return {
        currentUrl: currentUrlPath,
        isCurrentUrl,
        whenCurrentUrl,
        isActiveOrChild,
    };
}
