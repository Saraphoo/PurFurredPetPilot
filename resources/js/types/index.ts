import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    avatar?: string;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Pet {
    id: number;
    name: string;
    type: string;
    species?: string;
    breed?: string;
    sex: string;
    DOB: string;
    neutered?: boolean;
    color?: string;
    weight?: string;
    height?: string;
    length?: string;
    created_at: string;
    updated_at: string;
}

export interface PetInfo {
    id: number;
    pet_id: number;
    key: string;
    value: string;
    created_at: string;
    updated_at: string;
}

export interface Meal {
    id: number;
    pet_id: number;
    name: string;
    type: string;
    amount: string;
    frequency: string;
    notes?: string;
    created_at: string;
    updated_at: string;
}

export interface Housing {
    id: number;
    pet_id: number;
    type: string;
    size: string;
    location: string;
    temperature?: string;
    humidity?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
}

export interface Medical {
    id: number;
    pet_id: number;
    condition: string;
    diagnosis_date: string;
    treatment?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
}

export interface Behavior {
    id: number;
    pet_id: number;
    behavior: string;
    frequency: string;
    triggers?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
}

export interface Medication {
    id: number;
    pet_id: number;
    name: string;
    dosage: string;
    frequency: string;
    time_of_day?: string;
    notes?: string;
    prescribing_vet?: string;
    pharmacy?: string;
    refill_date?: string;
    expiration_date?: string;
    created_at: string;
    updated_at: string;
}
