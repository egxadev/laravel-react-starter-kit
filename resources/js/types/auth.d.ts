import type { User } from './user';

export interface Auth {
    user: User;
}

export interface PageProps<T extends Record<string, unknown> = Record<string, unknown>> extends T {
    auth?: {
        user: User;
        permissions: string[];
    };
}
