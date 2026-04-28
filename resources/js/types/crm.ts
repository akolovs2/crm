export type Tag = {
    id: number;
    name: string;
    color: string;
};

export type Company = {
    id: number;
    name: string;
    industry: string | null;
    website: string | null;
    phone: string | null;
    email: string | null;
    address: string | null;
    city: string | null;
    country: string | null;
    owner?: { id: number; name: string };
    contacts?: Contact[];
    deals?: Deal[];
    tags?: Tag[];
};

export type DealStage = 'lead' | 'qualified' | 'proposal' | 'negotiation' | 'won' | 'lost';

export type Deal = {
    id: number;
    name: string;
    value: string;
    currency: string;
    stage: DealStage;
    expected_close_date: string | null;
    contact_id: number | null;
    company_id: number | null;
    owner_id: number;
    created_at: string;
    updated_at: string;
};

export type ActivityType = 'call' | 'email' | 'meeting' | 'task' | 'note';

export type Activity = {
    id: number;
    type: ActivityType;
    subject: string;
    description: string | null;
    due_at: string | null;
    completed_at: string | null;
    owner_id: number;
    created_at: string;
    updated_at: string;
};

export type Contact = {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string | null;
    phone: string | null;
    job_title: string | null;
    company_id: number | null;
    owner_id: number;
    company?: Company;
    owner?: { id: number; name: string };
    deals?: Deal[];
    activities?: Activity[];
    tags?: Tag[];
    created_at: string;
    updated_at: string;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
};
