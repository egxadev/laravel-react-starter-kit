import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PER_PAGE_OPTIONS } from '@/constants/pagination';

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface DataTablePaginationProps {
    meta: PaginationMeta;
    onPageChange: (page: number) => void;
    onPerPageChange?: (perPage: number) => void;
}

export function DataTablePagination({ meta, onPageChange, onPerPageChange }: DataTablePaginationProps) {
    return (
        <div className="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:gap-2">
            {onPerPageChange && (
                <div className="flex items-center gap-2 text-sm justify-center">
                    <span className="text-muted-foreground">Show:</span>
                    <Select
                        value={meta.per_page.toString()}
                        onValueChange={(value) => onPerPageChange(parseInt(value))}
                    >
                        <SelectTrigger className="w-20">
                            <SelectValue />
                        </SelectTrigger>
                            <SelectContent>
                                {PER_PAGE_OPTIONS.map((option) => (
                                    <SelectItem key={option} value={option.toString()}>
                                        {option}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                    </Select>
                    <span className="text-muted-foreground">entries</span>
                </div>
            )}
            <div className="text-center">
                <div className="text-sm text-muted-foreground">
                    Showing {meta.from} to {meta.to} of {meta.total} entries.
                </div>
            </div>

            <div className="flex justify-center sm:justify-end">
                <Pagination>
                    <PaginationContent>
                        <PaginationItem>
                            <PaginationPrevious
                                href="#"
                                onClick={() => {
                                    if (meta.current_page > 1) {
                                        onPageChange(meta.current_page - 1);
                                    }
                                }}
                                className={meta.current_page > 1 ? '' : 'cursor-default opacity-50'}
                            />
                        </PaginationItem>
                        {meta.last_page > 5 ? (
                            <>
                                <PaginationItem>
                                    <PaginationLink href="#" isActive={meta.current_page === 1} onClick={() => onPageChange(1)}>
                                        1
                                    </PaginationLink>
                                </PaginationItem>
                                {meta.current_page > 3 && <PaginationEllipsis />}
                                {[meta.current_page - 1, meta.current_page, meta.current_page + 1]
                                    .filter((page) => page > 1 && page < meta.last_page)
                                    .map((page) => (
                                        <PaginationItem key={page}>
                                            <PaginationLink href="#" isActive={meta.current_page === page} onClick={() => onPageChange(page)}>
                                                {page}
                                            </PaginationLink>
                                        </PaginationItem>
                                    ))}
                                {meta.current_page < meta.last_page - 2 && <PaginationEllipsis />}
                                <PaginationItem>
                                    <PaginationLink
                                        href="#"
                                        isActive={meta.current_page === meta.last_page}
                                        onClick={() => onPageChange(meta.last_page)}
                                    >
                                        {meta.last_page}
                                    </PaginationLink>
                                </PaginationItem>
                            </>
                        ) : (
                            Array.from({ length: meta.last_page }, (_, i) => i + 1).map((page) => (
                                <PaginationItem key={page}>
                                    <PaginationLink href="#" isActive={meta.current_page === page} onClick={() => onPageChange(page)}>
                                        {page}
                                    </PaginationLink>
                                </PaginationItem>
                            ))
                        )}
                        <PaginationItem>
                            <PaginationNext
                                href="#"
                                onClick={() => {
                                    if (meta.current_page < meta.last_page) {
                                        onPageChange(meta.current_page + 1);
                                    }
                                }}
                                className={meta.current_page < meta.last_page ? '' : 'cursor-default opacity-50'}
                            />
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    );
}
