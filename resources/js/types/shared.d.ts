export type * from './auth';
export type * from './navigation';
export type * from './ui';

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}
