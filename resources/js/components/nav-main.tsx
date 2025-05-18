import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { useIsMobile } from '@/hooks/use-mobile';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import hasAnyPermission from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from './ui/collapsible';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from './ui/dropdown-menu';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const { state } = useSidebar();
    const isMobile = useIsMobile();
    const cleanup = useMobileNavigation();
    const path = page.url.split('?')[0];

    return (
        <SidebarGroup>
            <SidebarGroupLabel>Dashboard</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) =>
                    item.permission && hasAnyPermission(item.permission) ? (
                        item.items && item.items.length > 0 ? (
                            state === 'collapsed' ? (
                                <SidebarMenuItem>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <SidebarMenuButton
                                                isActive={items.some(({ items }) => items?.some(({ href }) => page.url.startsWith(href)))}
                                            >
                                                {item.icon && <item.icon />}
                                                <span>{item.title}</span>
                                            </SidebarMenuButton>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent
                                            className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                                            align={isMobile ? 'end' : state === 'collapsed' ? 'start' : 'end'}
                                            side={isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'}
                                        >
                                            {item.items?.map((subItem) => (
                                                <DropdownMenuItem asChild>
                                                    <Link className="block w-full" href={subItem.href} as="button" onClick={cleanup}>
                                                        <span>{subItem.title}</span>
                                                    </Link>
                                                </DropdownMenuItem>
                                            ))}
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </SidebarMenuItem>
                            ) : (
                                <Collapsible
                                    key={item.title}
                                    asChild
                                    defaultOpen={item.items?.some(subItem => subItem.href === path)}
                                    className="group/collapsible"
                                >
                                    <SidebarMenuItem>
                                        <CollapsibleTrigger asChild>
                                            <SidebarMenuButton>
                                                {item.icon && <item.icon />}
                                                <span>{item.title}</span>
                                                <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                            </SidebarMenuButton>
                                        </CollapsibleTrigger>
                                        <CollapsibleContent>
                                            <SidebarMenuSub>
                                                {item.items?.map((subItem) => (
                                                    <SidebarMenuSubItem key={subItem.title}>
                                                        <SidebarMenuSubButton asChild isActive={page.url.startsWith(subItem.href)}>
                                                            <Link href={subItem.href} prefetch>
                                                                <span>{subItem.title}</span>
                                                            </Link>
                                                        </SidebarMenuSubButton>
                                                    </SidebarMenuSubItem>
                                                ))}
                                            </SidebarMenuSub>
                                        </CollapsibleContent>
                                    </SidebarMenuItem>
                                </Collapsible>
                            )
                        ) : (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton asChild isActive={page.url.startsWith(item.href)}>
                                    <Link href={item.href} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        )
                    ) : null,
                )}
            </SidebarMenu>
        </SidebarGroup>
    );
}
