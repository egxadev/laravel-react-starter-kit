import { useHasAnyPermission } from '@/lib/utils';
import type { NavItem } from '@/types';

export function useFilteredNavItems(items: NavItem[]): NavItem[] {
    const hasAnyPermission = useHasAnyPermission();

    return items.filter(
        (item) => !item.permission || hasAnyPermission(item.permission),
    );
}
