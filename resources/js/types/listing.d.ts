export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface ListingFilters {
    search: string;
    sort_by: string;
    sort_dir: 'asc' | 'desc';
    trashed?: boolean;
}

export type Listing<T> = {
    data: T[];
    meta: PaginationMeta;
    filters: ListingFilters;
};
