export interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'merchant';
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
}

export interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}
