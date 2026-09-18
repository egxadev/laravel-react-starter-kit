import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store as storeUsers, update as updateUsers } from '@/routes/users';
import type { Role } from '@/types/role';
import type { User } from '@/types/user';

interface UserFormProps {
    mode: 'create' | 'edit';
    roles: Role[];
    user?: User;
    className?: string;
}

export function UserForm({ mode, roles, user, className }: UserFormProps) {
    const { data, setData, post, put, errors, processing } = useForm({
        name: user?.name ?? '',
        email: user?.email ?? '',
        roles: user?.roles?.map((r) => r.id) ?? [] as number[],
        password: '',
        password_confirmation: '',
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (mode === 'create') {
            post(storeUsers().url);
        } else {
            put(updateUsers({ user: user!.id }).url);
        }
    }

    function toggleRole(id: number, checked: boolean) {
        setData('roles', checked ? [...data.roles, id] : data.roles.filter((r) => r !== id));
    }

    return (
        <form onSubmit={handleSubmit} className={`${className ?? ''} space-y-6`}>
            <div className="space-y-2">
                <Label htmlFor="name">User Name</Label>
                <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
            </div>

            <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                {errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
            </div>

            <div className="space-y-3">
                <Label className="text-base">Role</Label>
                <p className="text-muted-foreground text-sm">Select roles for this user.</p>
                {roles.map((role) => (
                    <div key={role.id} className="flex items-center space-x-3">
                        <Checkbox
                            id={`role-${role.id}`}
                            checked={data.roles.includes(role.id)}
                            onCheckedChange={(checked) => toggleRole(role.id, !!checked)}
                        />
                        <Label htmlFor={`role-${role.id}`} className="text-sm font-normal">
                            {role.name}
                        </Label>
                    </div>
                ))}
                {errors.roles && <p className="text-sm text-red-500">{errors.roles}</p>}
            </div>

            <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
                {errors.password && <p className="text-sm text-red-500">{errors.password}</p>}
            </div>

            <div className="space-y-2">
                <Label htmlFor="password_confirmation">Confirm Password</Label>
                <Input
                    id="password_confirmation"
                    type="password"
                    value={data.password_confirmation}
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                />
                {errors.password_confirmation && <p className="text-sm text-red-500">{errors.password_confirmation}</p>}
            </div>

            <Button type="submit" disabled={processing}>
                Submit
            </Button>
        </form>
    );
}
