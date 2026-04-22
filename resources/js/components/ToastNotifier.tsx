import React, { useState, useEffect, useCallback, useRef } from 'react';

interface Toast {
    id: number;
    message: string;
    type: 'success' | 'error' | 'info';
    exiting: boolean;
}

const TOAST_COLORS: Record<string, string> = {
    success: 'bg-green-50 border-green-200 text-green-800',
    error: 'bg-red-50 border-red-200 text-red-800',
    info: 'bg-blue-50 border-blue-200 text-blue-800',
};

function ToastIcon({ type }: { type: string }) {
    if (type === 'success') {
        return (
            <svg className="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        );
    }
    if (type === 'error') {
        return (
            <svg className="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        );
    }
    // info
    return (
        <svg className="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    );
}

export default function ToastNotifier() {
    const [toasts, setToasts] = useState<Toast[]>([]);
    const counterRef = useRef(0);

    const removeToast = useCallback((id: number) => {
        setToasts(prev => prev.filter(t => t.id !== id));
    }, []);

    const startExit = useCallback((id: number) => {
        setToasts(prev => prev.map(t => t.id === id ? { ...t, exiting: true } : t));
        setTimeout(() => removeToast(id), 300);
    }, [removeToast]);

    const addToast = useCallback((message: string, type: 'success' | 'error' | 'info') => {
        const id = ++counterRef.current;
        const newToast: Toast = { id, message, type, exiting: false };

        setToasts(prev => {
            const updated = [...prev, newToast];
            // Max 3 visible - remove oldest if exceeded
            if (updated.length > 3) {
                const oldest = updated[0];
                setTimeout(() => startExit(oldest.id), 0);
            }
            return updated;
        });

        // Auto-dismiss after 4 seconds
        setTimeout(() => startExit(id), 4000);
    }, [startExit]);

    useEffect(() => {
        const handler = (e: Event) => {
            const detail = (e as CustomEvent).detail;
            if (detail && detail.message && detail.type) {
                addToast(detail.message, detail.type);
            }
        };
        window.addEventListener('show-toast', handler);
        return () => window.removeEventListener('show-toast', handler);
    }, [addToast]);

    if (toasts.length === 0) return null;

    return (
        <div className="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none">
            {toasts.map(toast => (
                <div
                    key={toast.id}
                    className={`pointer-events-auto rounded-lg shadow-lg border px-4 py-3 flex items-center gap-3 text-sm max-w-sm ${TOAST_COLORS[toast.type]}`}
                    style={{
                        transform: toast.exiting ? 'translateX(100%)' : 'translateX(0)',
                        opacity: toast.exiting ? 0 : 1,
                        transition: 'transform 300ms ease, opacity 300ms ease',
                    }}
                >
                    <ToastIcon type={toast.type} />
                    <span className="flex-1">{toast.message}</span>
                    <button
                        onClick={() => startExit(toast.id)}
                        className="shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            ))}
        </div>
    );
}
