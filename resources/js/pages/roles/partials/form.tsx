import { Permission } from "@/types/permission";
import { Role } from "@/types/role";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { router, usePage } from "@inertiajs/react";
import { PageProps } from "@/types";
import { User } from "@/types/user";
import { toast } from "sonner";

const formSchema = z.object({
    name: z.string().min(2, {
        message: "Role name must be at least 2 characters.",
    }),
    permissions: z.array(z.number()),
});

export function RoleForm({
    auth,
    role,
    permissions,
    mode,
    className,
}: PageProps<{
    auth: { user: User; permissions: string[] };
    role?: Role;
    permissions: Permission[];
    mode: "create" | "edit";
    className: string;
}>) {
    const { errors } = usePage().props;

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            name: role?.name,
            permissions: (role?.permissions ?? []).map(
                (permission) => permission.id
            ),
        },
    });

    function onSubmit(values: z.infer<typeof formSchema>) {
        const isCreateMode = mode === "create";
        const url = isCreateMode ? "/roles" : `/roles/${role?.id}`;
        const message = isCreateMode
            ? "Create role successfully"
            : "Update role successfully";

        if (isCreateMode) {
            router.post(url, values, {
                onSuccess: () => {
                    console.log(message);
                    toast.success(message);
                },
            });
        } else {
            router.put(url, values, {
                onSuccess: () => {
                    console.log(message);
                    toast.success(message);
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
                            <FormLabel>Role Name</FormLabel>
                            <FormControl>
                                <Input {...field} />
                            </FormControl>
                            <FormMessage>{errors.name}</FormMessage>
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="permissions"
                    render={() => (
                        <FormItem>
                            <div className="mb-4">
                                <FormLabel className="text-base">
                                    Role
                                </FormLabel>
                                <FormDescription>
                                    Select the role items you want to display in
                                    the sidebar.
                                </FormDescription>
                            </div>
                            {permissions.map((permission) => (
                                <FormField
                                    key={permission.id}
                                    control={form.control}
                                    name="permissions"
                                    render={({ field }) => {
                                        return (
                                            <FormItem
                                                key={permission.id}
                                                className="flex flex-row items-start space-x-3 space-y-0"
                                            >
                                                <FormControl>
                                                    <Checkbox
                                                        checked={field.value?.includes(
                                                            permission.id
                                                        )}
                                                        onCheckedChange={(
                                                            checked
                                                        ) => {
                                                            return checked
                                                                ? field.onChange(
                                                                      [
                                                                          ...field.value,
                                                                          permission.id,
                                                                      ]
                                                                  )
                                                                : field.onChange(
                                                                      field.value?.filter(
                                                                          (
                                                                              value
                                                                          ) =>
                                                                              value !==
                                                                              permission.id
                                                                      )
                                                                  );
                                                        }}
                                                    />
                                                </FormControl>
                                                <FormLabel className="text-sm font-normal">
                                                    {permission.name}
                                                </FormLabel>
                                            </FormItem>
                                        );
                                    }}
                                />
                            ))}
                            <FormMessage>{errors.permissions}</FormMessage>
                        </FormItem>
                    )}
                />
                <Button type="submit">Submit</Button>
            </form>
        </Form>
    );
}
