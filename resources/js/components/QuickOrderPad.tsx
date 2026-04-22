import React, { useState, useRef, useCallback, useEffect } from 'react';

interface Product {
    id: number;
    sku: string;
    name: string;
    material_grade: string;
    price_per_kg: number;
    stock_status: string;
    stock_quantity_kg: number;
    is_cut_to_length: boolean;
    weight_per_piece_kg: number;
    weight_per_meter_kg: number;
    standard_length_mm: number | null;
}

interface Row {
    id: number;
    skuInput: string;
    product: Product | null;
    quantity: number;
    suggestions: Product[];
    showSuggestions: boolean;
    loading: boolean;
    error: string | null;
    added: boolean;
}

interface Props {
    lookupUrl: string;
    addToCartUrl: string;
    cartUrl: string;
    csrfToken: string;
}

function formatPrice(value: number): string {
    return value.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function stockDot(status: string): string {
    if (status === 'Ab Lager') return 'bg-green-500';
    if (status === 'Geringe Menge') return 'bg-yellow-500';
    return 'bg-gray-400';
}

function stockBadgeClasses(status: string): string {
    if (status === 'Ab Lager') return 'bg-green-50 text-green-700';
    if (status === 'Geringe Menge') return 'bg-yellow-50 text-yellow-700';
    return 'bg-gray-100 text-gray-600';
}

let nextRowId = 1;

function createEmptyRow(): Row {
    return {
        id: nextRowId++,
        skuInput: '',
        product: null,
        quantity: 1,
        suggestions: [],
        showSuggestions: false,
        loading: false,
        error: null,
        added: false,
    };
}

function createInitialRows(count: number): Row[] {
    return Array.from({ length: count }, () => createEmptyRow());
}

export default function QuickOrderPad({ lookupUrl, addToCartUrl, cartUrl, csrfToken }: Props) {
    const [rows, setRows] = useState<Row[]>(() => createInitialRows(5));
    const [submitting, setSubmitting] = useState(false);
    const debounceTimers = useRef<Map<number, ReturnType<typeof setTimeout>>>(new Map());
    const quantityRefs = useRef<Map<number, HTMLInputElement>>(new Map());

    // Cleanup timers on unmount
    useEffect(() => {
        return () => {
            debounceTimers.current.forEach(timer => clearTimeout(timer));
        };
    }, []);

    const updateRow = useCallback((rowId: number, updates: Partial<Row>) => {
        setRows(prev => prev.map(r => r.id === rowId ? { ...r, ...updates } : r));
    }, []);

    const handleSkuChange = useCallback((rowId: number, value: string) => {
        updateRow(rowId, { skuInput: value, product: null, error: null, added: false });

        // Clear existing debounce timer
        const existingTimer = debounceTimers.current.get(rowId);
        if (existingTimer) clearTimeout(existingTimer);

        if (value.length < 2) {
            updateRow(rowId, { suggestions: [], showSuggestions: false, loading: false });
            return;
        }

        updateRow(rowId, { loading: true });

        const timer = setTimeout(async () => {
            try {
                const resp = await fetch(`${lookupUrl}?q=${encodeURIComponent(value)}`);
                const data: Product[] = await resp.json();
                updateRow(rowId, { suggestions: data, showSuggestions: true, loading: false });
            } catch {
                updateRow(rowId, { suggestions: [], showSuggestions: false, loading: false, error: 'Suche fehlgeschlagen' });
            }
        }, 300);

        debounceTimers.current.set(rowId, timer);
    }, [lookupUrl, updateRow]);

    const selectProduct = useCallback((rowId: number, product: Product) => {
        updateRow(rowId, {
            product,
            skuInput: product.sku,
            suggestions: [],
            showSuggestions: false,
            error: null,
        });
        // Focus quantity input
        setTimeout(() => {
            const qInput = quantityRefs.current.get(rowId);
            if (qInput) {
                qInput.focus();
                qInput.select();
            }
        }, 50);
    }, [updateRow]);

    const removeRow = useCallback((rowId: number) => {
        setRows(prev => {
            const filtered = prev.filter(r => r.id !== rowId);
            // Always keep at least 1 row
            return filtered.length > 0 ? filtered : [createEmptyRow()];
        });
    }, []);

    const addMoreRows = useCallback(() => {
        setRows(prev => [...prev, ...createInitialRows(5)]);
    }, []);

    const clearAll = useCallback(() => {
        setRows(createInitialRows(5));
    }, []);

    const addAllToCart = useCallback(async () => {
        const validRows = rows.filter(r => r.product !== null);
        if (validRows.length === 0) return;

        setSubmitting(true);
        let successCount = 0;

        for (const row of validRows) {
            try {
                const resp = await fetch(addToCartUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: row.product!.id,
                        quantity: row.quantity,
                    }),
                });

                if (resp.ok) {
                    successCount++;
                    updateRow(row.id, { added: true });
                } else {
                    updateRow(row.id, { error: 'Fehler beim Hinzufügen' });
                }
            } catch {
                updateRow(row.id, { error: 'Netzwerkfehler' });
            }
        }

        setSubmitting(false);

        // Dispatch cart-updated event
        window.dispatchEvent(new CustomEvent('cart-updated'));

        // Show success toast
        if (successCount > 0) {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    message: `${successCount} ${successCount === 1 ? 'Artikel' : 'Artikel'} zum Warenkorb hinzugefügt`,
                    type: 'success',
                },
            }));
        }

        // Clear added highlight after 2s
        setTimeout(() => {
            setRows(prev => prev.map(r => r.added ? { ...r, added: false } : r));
        }, 2000);
    }, [rows, addToCartUrl, csrfToken, updateRow]);

    const validCount = rows.filter(r => r.product !== null).length;

    return (
        <div className="bg-white rounded-xl border border-gray-200">
            {/* Desktop header */}
            <div className="hidden lg:grid lg:grid-cols-12 gap-4 px-6 py-3 bg-gray-50 rounded-t-xl border-b border-gray-200 text-xs font-medium text-gray-500 uppercase tracking-wider">
                <div className="col-span-3">Artikelnr.</div>
                <div className="col-span-1">Menge</div>
                <div className="col-span-3">Produkt</div>
                <div className="col-span-1">Werkstoff</div>
                <div className="col-span-2 text-right">Preis/kg</div>
                <div className="col-span-1">Verfügbarkeit</div>
                <div className="col-span-1"></div>
            </div>

            {/* Rows */}
            <div className="divide-y divide-gray-100">
                {rows.map((row) => (
                    <div
                        key={row.id}
                        className={`px-4 sm:px-6 py-4 transition-colors duration-500 ${
                            row.added ? 'bg-green-50' : ''
                        }`}
                    >
                        {/* Desktop layout */}
                        <div className="hidden lg:grid lg:grid-cols-12 gap-4 items-center">
                            {/* SKU input with autocomplete */}
                            <div className="col-span-3 relative">
                                <input
                                    type="text"
                                    value={row.skuInput}
                                    onChange={(e) => handleSkuChange(row.id, e.target.value)}
                                    onFocus={() => {
                                        if (row.suggestions.length > 0) {
                                            updateRow(row.id, { showSuggestions: true });
                                        }
                                    }}
                                    onBlur={() => {
                                        // Delay to allow click on suggestion
                                        setTimeout(() => updateRow(row.id, { showSuggestions: false }), 200);
                                    }}
                                    placeholder="SKU oder Produktname..."
                                    className={`w-full text-sm border rounded-lg px-3 py-2 focus:border-blue-900 focus:ring-blue-900 ${
                                        row.error ? 'border-red-300' : 'border-gray-300'
                                    }`}
                                />
                                {row.loading && (
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg className="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                )}
                                {row.showSuggestions && row.suggestions.length > 0 && (
                                    <div className="absolute z-20 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        {row.suggestions.map(p => (
                                            <button
                                                key={p.id}
                                                type="button"
                                                onMouseDown={(e) => e.preventDefault()}
                                                onClick={() => selectProduct(row.id, p)}
                                                className="w-full text-left px-3 py-2 hover:bg-blue-50 flex items-center justify-between gap-2 text-sm"
                                            >
                                                <div className="min-w-0">
                                                    <span className="font-medium text-gray-900">{p.sku}</span>
                                                    <span className="text-gray-500 ml-2 truncate">{p.name}</span>
                                                </div>
                                                <div className="flex items-center gap-2 shrink-0">
                                                    <span className={`w-2 h-2 rounded-full ${stockDot(p.stock_status)}`}></span>
                                                    <span className="text-gray-400 text-xs">{formatPrice(p.price_per_kg)} EUR/kg</span>
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                )}
                                {row.error && (
                                    <p className="text-xs text-red-500 mt-1">{row.error}</p>
                                )}
                            </div>

                            {/* Quantity */}
                            <div className="col-span-1">
                                <input
                                    ref={(el) => {
                                        if (el) quantityRefs.current.set(row.id, el);
                                    }}
                                    type="number"
                                    min="1"
                                    value={row.quantity}
                                    onChange={(e) => updateRow(row.id, { quantity: Math.max(1, parseInt(e.target.value) || 1) })}
                                    className="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-900 focus:ring-blue-900"
                                />
                            </div>

                            {/* Product name */}
                            <div className="col-span-3">
                                {row.product ? (
                                    <span className="text-sm text-gray-900 truncate block">{row.product.name}</span>
                                ) : (
                                    <span className="text-sm text-gray-300">&mdash;</span>
                                )}
                            </div>

                            {/* Material */}
                            <div className="col-span-1">
                                {row.product ? (
                                    <span className="text-xs font-medium text-blue-900 bg-blue-50 px-2 py-0.5 rounded">{row.product.material_grade}</span>
                                ) : (
                                    <span className="text-sm text-gray-300">&mdash;</span>
                                )}
                            </div>

                            {/* Price/kg */}
                            <div className="col-span-2 text-right">
                                {row.product ? (
                                    <span className="text-sm font-medium text-gray-900">{formatPrice(row.product.price_per_kg)}&nbsp;EUR/kg</span>
                                ) : (
                                    <span className="text-sm text-gray-300">&mdash;</span>
                                )}
                            </div>

                            {/* Availability */}
                            <div className="col-span-1">
                                {row.product ? (
                                    <span className={`inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full ${stockBadgeClasses(row.product.stock_status)}`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${stockDot(row.product.stock_status)}`}></span>
                                        {row.product.stock_status}
                                    </span>
                                ) : (
                                    <span className="text-sm text-gray-300">&mdash;</span>
                                )}
                            </div>

                            {/* Remove */}
                            <div className="col-span-1 text-right">
                                <button
                                    onClick={() => removeRow(row.id)}
                                    className="text-gray-400 hover:text-red-500 transition-colors p-1"
                                    title="Zeile entfernen"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {/* Mobile layout */}
                        <div className="lg:hidden space-y-3">
                            <div className="flex items-start gap-3">
                                <div className="flex-1 relative">
                                    <label className="block text-xs font-medium text-gray-500 mb-1">Artikelnr.</label>
                                    <input
                                        type="text"
                                        value={row.skuInput}
                                        onChange={(e) => handleSkuChange(row.id, e.target.value)}
                                        onFocus={() => {
                                            if (row.suggestions.length > 0) {
                                                updateRow(row.id, { showSuggestions: true });
                                            }
                                        }}
                                        onBlur={() => {
                                            setTimeout(() => updateRow(row.id, { showSuggestions: false }), 200);
                                        }}
                                        placeholder="SKU oder Produktname..."
                                        className={`w-full text-sm border rounded-lg px-3 py-2 focus:border-blue-900 focus:ring-blue-900 ${
                                            row.error ? 'border-red-300' : 'border-gray-300'
                                        }`}
                                    />
                                    {row.loading && (
                                        <div className="absolute right-3 top-8">
                                            <svg className="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    )}
                                    {row.showSuggestions && row.suggestions.length > 0 && (
                                        <div className="absolute z-20 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            {row.suggestions.map(p => (
                                                <button
                                                    key={p.id}
                                                    type="button"
                                                    onMouseDown={(e) => e.preventDefault()}
                                                    onClick={() => selectProduct(row.id, p)}
                                                    className="w-full text-left px-3 py-2 hover:bg-blue-50 text-sm"
                                                >
                                                    <div className="font-medium text-gray-900">{p.sku}</div>
                                                    <div className="text-gray-500 text-xs truncate">{p.name}</div>
                                                </button>
                                            ))}
                                        </div>
                                    )}
                                    {row.error && (
                                        <p className="text-xs text-red-500 mt-1">{row.error}</p>
                                    )}
                                </div>
                                <div className="w-20">
                                    <label className="block text-xs font-medium text-gray-500 mb-1">Menge</label>
                                    <input
                                        ref={(el) => {
                                            if (el) quantityRefs.current.set(row.id, el);
                                        }}
                                        type="number"
                                        min="1"
                                        value={row.quantity}
                                        onChange={(e) => updateRow(row.id, { quantity: Math.max(1, parseInt(e.target.value) || 1) })}
                                        className="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-900 focus:ring-blue-900"
                                    />
                                </div>
                                <button
                                    onClick={() => removeRow(row.id)}
                                    className="text-gray-400 hover:text-red-500 transition-colors p-1 mt-6"
                                    title="Zeile entfernen"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {row.product && (
                                <div className="bg-gray-50 rounded-lg p-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                                    <span className="text-gray-900 font-medium truncate">{row.product.name}</span>
                                    <span className="text-xs font-medium text-blue-900 bg-blue-50 px-2 py-0.5 rounded">{row.product.material_grade}</span>
                                    <span className="text-gray-700">{formatPrice(row.product.price_per_kg)}&nbsp;EUR/kg</span>
                                    <span className={`inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full ${stockBadgeClasses(row.product.stock_status)}`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${stockDot(row.product.stock_status)}`}></span>
                                        {row.product.stock_status}
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>
                ))}
            </div>

            {/* Actions */}
            <div className="px-4 sm:px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <button
                    onClick={addMoreRows}
                    className="inline-flex items-center justify-center gap-2 px-4 py-2 border border-blue-900 text-blue-900 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Weitere Zeilen hinzufügen
                </button>
                <button
                    onClick={clearAll}
                    className="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors"
                >
                    Leeren
                </button>

                <div className="sm:ml-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    {validCount > 0 && (
                        <span className="text-sm text-gray-500 text-center">
                            {validCount} {validCount === 1 ? 'Artikel' : 'Artikel'} ausgewählt
                        </span>
                    )}
                    <button
                        onClick={addAllToCart}
                        disabled={validCount === 0 || submitting}
                        className="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-orange-500 text-white rounded-lg text-sm font-semibold hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {submitting ? (
                            <>
                                <svg className="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Wird hinzugefügt...
                            </>
                        ) : (
                            <>
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                                </svg>
                                Alle zum Warenkorb hinzufügen
                            </>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
}
