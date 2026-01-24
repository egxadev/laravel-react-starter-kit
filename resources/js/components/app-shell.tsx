import { SidebarProvider } from '@/components/ui/sidebar';
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import type { ReactNode } from 'react';

type Props = {
    children: ReactNode;
    variant?: 'header' | 'sidebar';
};

const SIDEBAR_COOKIE_NAME = 'sidebar_state';

/**
 * Reads sidebar state from cookie
 */
function getSidebarStateFromCookie(): boolean {
    if (typeof document === 'undefined') return true;
    
    const cookies = document.cookie.split(';');
    const sidebarCookie = cookies.find((cookie) => cookie.trim().startsWith(`${SIDEBAR_COOKIE_NAME}=`));
    
    if (!sidebarCookie) return true;
    
    const value = sidebarCookie.split('=')[1]?.trim();
    return value === 'true';
}

/**
 * AppShell component that provides layout structure
 * Handles sidebar state persistence across Inertia.js navigation
 * 
 * Best practices:
 * - Uses controlled state to persist sidebar state across navigation
 * - Syncs with server value on initial load (SSR/hydration)
 * - Cookie is the source of truth for user preference
 * - Prevents state reset on navigation
 */
export function AppShell({ children, variant = 'header' }: Props) {
    const serverSidebarOpen = usePage<SharedData>().props.sidebarOpen;
    
    // Initialize state: prefer cookie (user's last preference), fallback to server value
    // This ensures state persists across Inertia navigation
    const [sidebarOpen, setSidebarOpen] = useState<boolean>(() => {
        // On client-side, read from cookie (user's preference)
        // On SSR, use server value (from PHP middleware)
        if (typeof document !== 'undefined') {
            return getSidebarStateFromCookie();
        }
        return serverSidebarOpen;
    });

    // Sync with cookie on client-side mount (handles hydration)
    // This ensures client-side state matches cookie after SSR
    useEffect(() => {
        const cookieValue = getSidebarStateFromCookie();
        if (cookieValue !== sidebarOpen) {
            setSidebarOpen(cookieValue);
        }
    }, []); // Only run once on mount

    if (variant === 'header') {
        return (
            <div className="flex min-h-screen w-full flex-col">{children}</div>
        );
    }

    // Use controlled state to persist sidebar state across Inertia navigation
    // SidebarProvider will automatically update the cookie when state changes
    // This prevents state reset when navigating between pages
    return (
        <SidebarProvider open={sidebarOpen} onOpenChange={setSidebarOpen}>
            {children}
        </SidebarProvider>
    );
}
