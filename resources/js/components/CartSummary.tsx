import React, { useState, useEffect } from 'react';

interface Props {
    initialCount: number;
}

export default function CartSummary({ initialCount }: Props) {
    const [count, setCount] = useState(initialCount);

    useEffect(() => {
        const handleCartUpdate = (e: CustomEvent) => {
            if (e.detail?.cart_count !== undefined) {
                setCount(e.detail.cart_count);
            }
        };

        window.addEventListener('cart-updated', handleCartUpdate as EventListener);

        // Poll for updates every 30s as fallback
        const interval = setInterval(async () => {
            try {
                const resp = await fetch('/api/cart/summary');
                if (resp.ok) {
                    const data = await resp.json();
                    setCount(data.count);
                }
            } catch {}
        }, 30000);

        return () => {
            window.removeEventListener('cart-updated', handleCartUpdate as EventListener);
            clearInterval(interval);
        };
    }, []);

    if (count === 0) return null;

    return (
        <span className="absolute -top-1 -right-1 w-5 h-5 bg-orange-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
            {count > 99 ? '99+' : count}
        </span>
    );
}
