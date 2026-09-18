import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store as storeRoles, update as updateRoles } from '@/routes/roles';
import type { Permission } from '@/types/permission';
import type { Role } from '@/types/role';

interface RoleFormProps {
    mode: 'create' | 'edit';
    permissions: Permission[];
    role?: Role;
    className?: string;
}

export function RoleForm({ mode, permissions, role, className }: RoleFormProps) {
    const { data, setData, post, put, errors, processing } = useForm({
        name: role?.name ?? '',
        permissions: (role?.permissions ?? []).map((p) => p.id) as number[],
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (mode === 'create') {
            post(storeRoles().url);
        } else {
            put(updateRoles({ role: role!.id }).url);
        }
    }

    function togglePermission(id: number, checked: boolean) {
        setData('permissions', checked ? [...data.permissions, id] : data.permissions.filter((p) => p !== id));
    }

    return (
        <form onSubmit={handleSubmit} className={`${className ?? ''} space-y-6`}>
            <div className="space-y-2">
                <Label htmlFor="name">Role Name</Label>
                <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
            </div>

            <div className="space-y-3">
                <Label className="text-base">Permission</Label>
                <p className="text-muted-foreground text-sm">Select permissions for this role.</p>
                {permissions.map((permission) => (
                    <div key={permission.id} className="flex items-center space-x-3">
                        <Checkbox
                            id={`permission-${permission.id}`}
                            checked={data.permissions.includes(permission.id)}
                            onCheckedChange={(checked) => togglePermission(permission.id, !!checked)}
                        />
                        <Label htmlFor={`permission-${permission.id}`} className="text-sm font-normal">
                            {permission.name}
                        </Label>
                    </div>
                ))}
                {errors.permissions && <p className="text-sm text-red-500">{errors.permissions}</p>}
            </div>

            <Button type="submit" disabled={processing}>
                Submit
            </Button>
        </form>
    );
}
