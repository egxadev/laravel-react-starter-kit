import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { PageProps } from '@/types';
import { Role } from '@/types/role';
import { User } from '@/types/user';
import { zodResolver } from '@hookform/resolvers/zod';
import { router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { z } from 'zod';

const formSchema = z
    .object({
        name: z.string().min(2, { message: 'Role name must be at least 2 characters.' }),
        email: z.string().email({ message: 'Invalid email address.' }),
        roles: z.array(z.number()),
        password: z.string().min(8, { message: 'Password must be at least 8 characters.' }).optional(),
        password_confirmation: z.string().min(8, { message: 'Confirm Password must be at least 8 characters.' }).optional(),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: "Passwords don't match",
        path: ['password_confirmation'],
    });

export function UserForm({
    mode,
    roles,
    user,
    className,
}: PageProps<{
    mode: 'create' | 'edit';
    roles: Role[];
    user?: User;
    className: string;
}>) {
    const { errors } = usePage().props;

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            name: user?.name,
            email: user?.email,
            roles: user?.roles?.map((role) => role.id) || [],
        },
    });

    function onSubmit(values: z.infer<typeof formSchema>) {
        const isCreateMode = mode === 'create';
        const url = isCreateMode ? '/users' : `/users/${user?.id}`;

        if (isCreateMode) {
            router.post(url, values, {
                onSuccess: () => {
                    console.log('User created successfully.');
                },
                onError: () => {
                    console.error('Failed to create user.');
                },
            });
        } else {
            router.put(url, values, {
                onSuccess: () => {
                    console.log('User updated successfully.');
                },
                onError: () => {
                    console.error('Failed to update user.');
                },
            });
        }
    }

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className={`${className} space-y-8`}>
                <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>User Name</FormLabel>
                            <FormControl>
                                <Input {...field} />
                            </FormControl>
                            <FormMessage>{errors.name}</FormMessage>
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input {...field} />
                            </FormControl>
                            <FormMessage>{errors.email}</FormMessage>
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="roles"
                    render={({ field }) => (
                        <FormItem>
                            <div className="mb-4">
                                <FormLabel className="text-base">Role</FormLabel>
                                <FormDescription>Select roles for this user.</FormDescription>
                            </div>
                            {roles.map((role) => (
                                <FormItem key={role.id} className="flex flex-row items-start space-y-0 space-x-3">
                                    <FormControl>
                                        <Checkbox
                                            checked={(field.value ?? []).includes(role.id)}
                                            onCheckedChange={(checked) => {
                                                const currentValues = Array.isArray(field.value) ? field.value : [];
                                                field.onChange(
                                                    checked ? [...currentValues, role.id] : currentValues.filter((value) => value !== role.id),
                                                );
                                            }}
                                        />
                                    </FormControl>
                                    <FormLabel className="text-sm font-normal">{role.name}</FormLabel>
                                </FormItem>
                            ))}
                            <FormMessage>{errors.roles}</FormMessage>
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="password"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Password</FormLabel>
                            <FormControl>
                                <Input type="password" {...field} />
                            </FormControl>
                            <FormMessage>{errors.password}</FormMessage>
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="password_confirmation"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Confirm Password</FormLabel>
                            <FormControl>
                                <Input type="password" {...field} />
                            </FormControl>
                            <FormMessage>{errors.password_confirmation}</FormMessage>
                        </FormItem>
                    )}
                />

                <Button type="submit">Submit</Button>
            </form>
        </Form>
    );
}
