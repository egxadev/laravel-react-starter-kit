import { usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { toast } from 'sonner';

interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

/**
 * Creates a stable hash from flash messages object
 * Uses sorted keys to ensure consistent hashing regardless of property order
 */
function createFlashHash(flash: FlashMessages): string {
    const sortedEntries = Object.entries(flash)
        .filter(([, value]) => value !== undefined && value !== null && value !== '')
        .sort(([keyA], [keyB]) => keyA.localeCompare(keyB));
    
    return JSON.stringify(sortedEntries);
}

/**
 * Custom hook to handle flash messages from Inertia.js
 * Prevents duplicate toast notifications by tracking previous flash messages
 * 
 * Best practices implemented:
 * - Uses stable hash comparison (sorted keys) to prevent false positives
 * - Only processes non-empty flash messages
 * - Handles back navigation by tracking unique flash message hashes
 * 
 * @example
 * ```tsx
 * export default function MyPage() {
 *   useFlashMessages();
 *   // ... rest of component
 * }
 * ```
 */
export function useFlashMessages() {
    const { flash } = usePage<{ flash: FlashMessages }>().props;
    const previousFlashHashRef = useRef<string>('');
    const processedHashesRef = useRef<Set<string>>(new Set());

    useEffect(() => {
        const currentHash = createFlashHash(flash);
        const hasFlashMessage = flash.success || flash.error || flash.warning || flash.info;

        // Skip if no flash messages or if hash hasn't changed
        if (!hasFlashMessage) {
            // Reset tracking when flash is cleared
            if (previousFlashHashRef.current) {
                previousFlashHashRef.current = '';
                processedHashesRef.current.clear();
            }
            return;
        }

        // Only show toast if this is a new flash message (not seen before)
        if (currentHash !== previousFlashHashRef.current && !processedHashesRef.current.has(currentHash)) {
            if (flash.success) toast.success(flash.success);
            if (flash.error) toast.error(flash.error);
            if (flash.warning) toast.warning(flash.warning);
            if (flash.info) toast.info(flash.info);

            // Track this hash to prevent duplicates
            previousFlashHashRef.current = currentHash;
            processedHashesRef.current.add(currentHash);

            // Clean up old hashes to prevent memory leak (keep last 10)
            if (processedHashesRef.current.size > 10) {
                const hashesArray = Array.from(processedHashesRef.current);
                processedHashesRef.current.clear();
                // Keep the most recent hash and a few others
                processedHashesRef.current.add(hashesArray[hashesArray.length - 1]);
            }
        }
    }, [flash]);
}
