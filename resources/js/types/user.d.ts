import { Role } from "./role";

export interface User {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
}
