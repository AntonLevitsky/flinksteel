import React, { useState } from 'react';

interface CartItem {
    id: number;
    product_id: number;
    product_name: string;
    product_sku: string;
    material_grade: string;
    form_slug: string;
    quantity: number;
    length_mm: number | null;
    is_cut_to_length: boolean;
    anarbeitung: string[];
    certificate_code: string | null;
    unit_price_eur: number;
    line_total_eur: number;
    is_bestellware: boolean;
    delivery_days: [number, number];
}

interface Props {
    items: CartItem[];
    checkoutUrl: string;
    homeUrl: string;
    angebotUrl: string;
    csrfToken: string;
}

function formatEur(value: number): string {
    return value.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '\u00A0€';
}

const ANARBEITUNG_LABELS: Record<string, string> = {
    saw_cut: 'Zuschnitt',
    deburr: 'Entgraten',
    sandblast: 'Sandstrahlen',
    galvanize: 'Verzinken',
    prime: 'Grundieren',
};

export default function CartTable({ items: initialItems, checkoutUrl, homeUrl, angebotUrl, csrfToken }: Props) {
    const [items, setItems] = useState(initialItems);
    const [updating, setUpdating] = useState<number | null>(null);

    const subtotal = items.reduce((sum, item) => sum + item.line_total_eur, 0);
    // Shipping is calculated at checkout based on weight, PLZ, and selected option.
    // Cart shows subtotal only — no shipping estimate.
    const vat = subtotal * 0.19;
    const grossSubtotal = subtotal + vat;

    const updateQuantity = async (id: number, newQty: number) => {
        if (newQty < 1) return;
        setUpdating(id);
        try {
            const resp = await fetch(`/api/cart/items/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity: newQty }),
            });
            if (resp.ok) {
                const data = await resp.json();
                setItems(prev => prev.map(item =>
                    item.id === id ? { ...item, quantity: newQty, line_total_eur: data.item.line_total_eur, unit_price_eur: data.item.unit_price_eur } : item
                ));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Menge aktualisiert', type: 'success' } }));
            }
        } catch (e) {
            console.error('Failed to update', e);
            window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Ein Fehler ist aufgetreten', type: 'error' } }));
        } finally {
            setUpdating(null);
        }
    };

    const removeItem = async (id: number) => {
        setUpdating(id);
        try {
            const resp = await fetch(`/api/cart/items/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            if (resp.ok) {
                const data = await resp.json();
                setItems(prev => prev.filter(item => item.id !== id));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Artikel entfernt', type: 'info' } }));
            }
        } catch (e) {
            console.error('Failed to remove', e);
            window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Ein Fehler ist aufgetreten', type: 'error' } }));
        } finally {
            setUpdating(null);
        }
    };

    if (items.length === 0) {
        return (
            <div className="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg className="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <p className="text-gray-500 mb-4">Ihr Warenkorb ist leer.</p>
                <a href={homeUrl} className="inline-flex items-center px-6 py-2.5 bg-blue-900 text-white rounded-lg hover:bg-blue-800 font-medium text-sm transition-colors">
                    Zum Sortiment
                </a>
            </div>
        );
    }

    const hasBestellware = items.some(i => i.is_bestellware);
    const hasLagerware = items.some(i => !i.is_bestellware);
    const isMixedOrder = hasBestellware && hasLagerware;

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Items */}
            <div className="lg:col-span-2 space-y-4">
                {/* Teillieferung notice for mixed orders */}
                {isMixedOrder && (
                    <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                        <svg className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p className="text-sm font-medium text-amber-800">Hinweis: Ihre Bestellung enthält Lagerware und Bestellware</p>
                            <p className="text-xs text-amber-700 mt-1">
                                Lagerartikel werden in 1–3 Werktagen versandt. Bestellware wird vom Hersteller bezogen (5–10 Werktage) und separat geliefert.
                                Es können daher <strong>zwei Teillieferungen</strong> erfolgen. Zusätzliche Versandkosten entstehen Ihnen dadurch nicht.
                            </p>
                        </div>
                    </div>
                )}
                {hasBestellware && !hasLagerware && (
                    <div className="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
                        <svg className="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p className="text-sm font-medium text-blue-800">Bestellware — Lieferfrist ca. 5–10 Werktage</p>
                            <p className="text-xs text-blue-700 mt-1">
                                Alle Artikel in Ihrem Warenkorb werden vom Hersteller bezogen und nach Eingang in unserem Lager an Sie versandt.
                            </p>
                        </div>
                    </div>
                )}

                <div className="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                    {items.map(item => (
                        <div key={item.id} className="p-4 sm:p-6 flex gap-4">
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <h3 className="text-sm font-semibold text-gray-900 truncate">{item.product_name}</h3>
                                    {item.is_bestellware ? (
                                        <span className="shrink-0 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">
                                            Bestellware
                                        </span>
                                    ) : (
                                        <span className="shrink-0 text-[10px] font-medium text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">
                                            Ab Lager
                                        </span>
                                    )}
                                </div>
                                <p className="text-xs text-gray-500 mt-0.5">
                                    {item.product_sku} · {item.material_grade}
                                    {item.length_mm && ` · ${item.length_mm.toLocaleString('de-DE')} mm`}
                                    {' · '}{item.delivery_days[0]}–{item.delivery_days[1]} Werktage
                                </p>
                                {item.anarbeitung.length > 0 && (
                                    <p className="text-xs text-blue-700 mt-1">
                                        {item.anarbeitung.map(code => ANARBEITUNG_LABELS[code] || code).join(', ')}
                                    </p>
                                )}
                                {item.certificate_code && (
                                    <p className="text-xs text-green-700 mt-0.5">Zeugnis {item.certificate_code}</p>
                                )}

                                <div className="flex items-center gap-2 mt-3">
                                    <button
                                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                        disabled={updating === item.id || item.quantity <= 1}
                                        className="w-8 h-8 rounded border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50 disabled:opacity-50 text-sm"
                                    >−</button>
                                    <span className="w-12 text-center text-sm font-medium">
                                        {updating === item.id ? '...' : item.quantity}
                                    </span>
                                    <button
                                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                        disabled={updating === item.id}
                                        className="w-8 h-8 rounded border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50 disabled:opacity-50 text-sm"
                                    >+</button>
                                    <button
                                        onClick={() => removeItem(item.id)}
                                        disabled={updating === item.id}
                                        className="ml-4 text-xs text-red-500 hover:text-red-700"
                                    >Entfernen</button>
                                </div>
                            </div>

                            <div className="text-right shrink-0">
                                <p className="text-sm font-bold text-gray-900">{formatEur(item.line_total_eur)}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Summary sidebar */}
            <div>
                <div className="bg-white rounded-xl border border-gray-200 p-6 sticky top-20">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Zusammenfassung</h2>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-gray-500">Warenwert ({items.length} Pos.)</span>
                            <span>{formatEur(subtotal)}</span>
                        </div>
                        <div className="flex justify-between text-gray-400">
                            <span>Versand</span>
                            <span>wird an der Kasse berechnet</span>
                        </div>
                        <div className="border-t border-gray-200 pt-2 flex justify-between">
                            <span className="text-gray-500">zzgl. MwSt. (19 %)</span>
                            <span>{formatEur(vat)}</span>
                        </div>
                        <div className="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold">
                            <span>Warenwert brutto</span>
                            <span>{formatEur(grossSubtotal)}</span>
                        </div>
                        <p className="text-xs text-gray-400 pt-1">Versandkosten werden an der Kasse nach Gewicht, PLZ und gewählter Versandart berechnet. Frei Haus für PLZ 88xxx.</p>
                    </div>

                    <a
                        href={checkoutUrl}
                        className="block w-full mt-6 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition-colors"
                    >
                        Weiter zur Kasse
                    </a>
                    <a
                        href={angebotUrl}
                        target="_blank"
                        className="block w-full mt-2 border border-blue-900 text-blue-900 hover:bg-blue-50 font-medium py-2.5 px-4 rounded-lg text-center text-sm transition-colors"
                    >
                        Angebot als PDF erstellen
                    </a>
                </div>
            </div>
        </div>
    );
}
