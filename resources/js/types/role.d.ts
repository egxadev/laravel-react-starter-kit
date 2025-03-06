import { Permission } from './permission';

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions?: Permission[];
}
