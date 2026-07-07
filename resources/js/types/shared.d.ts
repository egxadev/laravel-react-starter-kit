import type { Auth } from './auth';

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    ssoEnabled: boolean;
    passkeysEnabled: boolean;
    [key: string]: unknown;
}
