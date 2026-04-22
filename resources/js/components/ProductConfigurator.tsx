import React, { useState, useMemo, useEffect, useCallback } from 'react';

interface Product {
    id: number;
    name: string;
    sku: string;
    is_cut_to_length: boolean;
    standard_length_mm: number | null;
    weight_per_meter_kg: number;
    weight_per_piece_kg: number;
    price_per_kg_eur: number;
    dimensions: Record<string, number>;
}

interface AnarbeitungOption {
    code: string;
    name_de: string;
    price_per_cut_eur: number | null;
    price_per_kg_eur: number | null;
}

interface CertificateOption {
    code: string;
    name_de: string;
    surcharge_eur: number;
}

interface Props {
    product: Product;
    anarbeitungOptions: AnarbeitungOption[];
    certificates: CertificateOption[];
    csrfToken: string;
}

function formatEur(value: number): string {
    return value.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '\u00A0€';
}

function formatKg(value: number): string {
    return value.toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' kg';
}

const LENGTH_PRESETS = [500, 1000, 2000, 3000, 6000];

export default function ProductConfigurator({ product, anarbeitungOptions, certificates, csrfToken }: Props) {
    const [quantity, setQuantity] = useState(1);
    const [lengthMm, setLengthMm] = useState(product.standard_length_mm || 1000);
    const [selectedAnarbeitung, setSelectedAnarbeitung] = useState<string[]>([]);
    const [selectedCert, setSelectedCert] = useState(certificates[0]?.code || '2.2');
    const [adding, setAdding] = useState(false);
    const [added, setAdded] = useState(false);

    const weight = useMemo(() => {
        if (product.is_cut_to_length) {
            return (lengthMm / 1000) * product.weight_per_meter_kg * quantity;
        }
        return product.weight_per_piece_kg * quantity;
    }, [quantity, lengthMm, product]);

    const basePrice = useMemo(() => weight * product.price_per_kg_eur, [weight, product]);

    const anarbeitungCost = useMemo(() => {
        let cost = 0;
        for (const code of selectedAnarbeitung) {
            const opt = anarbeitungOptions.find(o => o.code === code);
            if (!opt) continue;
            if (opt.price_per_cut_eur) {
                cost += opt.price_per_cut_eur * quantity;
            } else if (opt.price_per_kg_eur) {
                cost += opt.price_per_kg_eur * weight;
            }
        }
        return cost;
    }, [selectedAnarbeitung, quantity, weight, anarbeitungOptions]);

    const certCost = useMemo(() => {
        const cert = certificates.find(c => c.code === selectedCert);
        return cert?.surcharge_eur || 0;
    }, [selectedCert, certificates]);

    const totalPrice = basePrice + anarbeitungCost + certCost;

    // Dispatch price/weight updates to the sticky mobile bar (Alpine.js)
    useEffect(() => {
        const formattedTotal = totalPrice.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        window.dispatchEvent(new CustomEvent('configurator-price-update', {
            detail: { totalPrice, formattedTotal }
        }));
    }, [totalPrice]);

    const toggleAnarbeitung = (code: string) => {
        setSelectedAnarbeitung(prev =>
            prev.includes(code) ? prev.filter(c => c !== code) : [...prev, code]
        );
    };

    const handleAddToCart = useCallback(async () => {
        setAdding(true);
        try {
            const resp = await fetch('/api/cart/items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: product.id,
                    quantity,
                    length_mm: product.is_cut_to_length ? lengthMm : null,
                    anarbeitung: selectedAnarbeitung,
                    certificate_code: selectedCert,
                }),
            });
            if (resp.ok) {
                const data = await resp.json();
                setAdded(true);
                setTimeout(() => setAdded(false), 3000);
                // Dispatch event for CartSummary to pick up
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                // Toast notification
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { message: `${product.name} wurde zum Warenkorb hinzugefügt`, type: 'success' }
                }));
            }
        } catch (e) {
            console.error('Failed to add to cart', e);
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { message: 'Fehler beim Hinzufügen zum Warenkorb', type: 'error' }
            }));
        } finally {
            setAdding(false);
        }
    }, [product, quantity, lengthMm, selectedAnarbeitung, selectedCert, csrfToken]);

    // Listen for external add-to-cart trigger (e.g. from sticky mobile bar)
    useEffect(() => {
        const handler = () => handleAddToCart();
        window.addEventListener('trigger-add-to-cart', handler);
        return () => window.removeEventListener('trigger-add-to-cart', handler);
    }, [handleAddToCart]);

    return (
        <div className="bg-gray-50 rounded-xl border border-gray-200 p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Konfigurieren & bestellen</h3>

            {/* Quantity */}
            <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Menge ({product.is_cut_to_length ? 'Stück' : 'Stück'})
                </label>
                <div className="flex items-center gap-2">
                    <button
                        onClick={() => setQuantity(Math.max(1, quantity - 1))}
                        className="w-10 h-10 rounded-lg border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50"
                    >−</button>
                    <input
                        type="number"
                        min="1"
                        value={quantity}
                        onChange={e => setQuantity(Math.max(1, parseInt(e.target.value) || 1))}
                        className="w-20 h-10 text-center rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900"
                    />
                    <button
                        onClick={() => setQuantity(quantity + 1)}
                        className="w-10 h-10 rounded-lg border border-gray-300 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50"
                    >+</button>
                </div>
            </div>

            {/* Length (cut-to-length only) */}
            {product.is_cut_to_length && (
                <div className="mb-4">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Länge (mm)</label>
                    <input
                        type="number"
                        min="1"
                        max={product.standard_length_mm || 12000}
                        value={lengthMm}
                        onChange={e => setLengthMm(Math.max(1, parseInt(e.target.value) || 1))}
                        className="w-full h-10 rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900 mb-2"
                    />
                    <div className="flex flex-wrap gap-1.5">
                        {LENGTH_PRESETS.filter(l => !product.standard_length_mm || l <= product.standard_length_mm).map(preset => (
                            <button
                                key={preset}
                                onClick={() => setLengthMm(preset)}
                                className={`px-3 py-1 text-xs rounded-full border transition-colors ${
                                    lengthMm === preset
                                        ? 'bg-blue-900 text-white border-blue-900'
                                        : 'bg-white text-gray-600 border-gray-300 hover:border-blue-900'
                                }`}
                            >
                                {preset.toLocaleString('de-DE')} mm
                            </button>
                        ))}
                    </div>
                </div>
            )}

            {/* Anarbeitung */}
            {anarbeitungOptions.length > 0 && (
                <div className="mb-4">
                    <label className="block text-sm font-medium text-gray-700 mb-2">Anarbeitung</label>
                    <div className="space-y-2">
                        {anarbeitungOptions.map(opt => {
                            const optCost = opt.price_per_cut_eur
                                ? opt.price_per_cut_eur * quantity
                                : (opt.price_per_kg_eur || 0) * weight;
                            return (
                                <label key={opt.code} className="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={selectedAnarbeitung.includes(opt.code)}
                                        onChange={() => toggleAnarbeitung(opt.code)}
                                        className="rounded border-gray-300 text-blue-900 focus:ring-blue-900"
                                    />
                                    <span className="text-sm flex-1">{opt.name_de}</span>
                                    <span className="text-xs text-gray-500">+{formatEur(optCost)}</span>
                                </label>
                            );
                        })}
                    </div>
                </div>
            )}

            {/* Certificate */}
            {certificates.length > 0 && (
                <div className="mb-6">
                    <label className="block text-sm font-medium text-gray-700 mb-2">Werkzeugnis</label>
                    <div className="space-y-2">
                        {certificates.map(cert => (
                            <label key={cert.code} className="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                <input
                                    type="radio"
                                    name="certificate"
                                    value={cert.code}
                                    checked={selectedCert === cert.code}
                                    onChange={() => setSelectedCert(cert.code)}
                                    className="border-gray-300 text-blue-900 focus:ring-blue-900"
                                />
                                <span className="text-sm flex-1">{cert.name_de}</span>
                                <span className="text-xs text-gray-500">
                                    {cert.surcharge_eur > 0 ? `+${formatEur(cert.surcharge_eur)}` : 'kostenlos'}
                                </span>
                            </label>
                        ))}
                    </div>
                </div>
            )}

            {/* Price summary */}
            <div className="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <div className="space-y-1 text-sm">
                    <div className="flex justify-between text-gray-500">
                        <span>Gewicht</span>
                        <span>{formatKg(weight)}</span>
                    </div>
                    <div className="flex justify-between text-gray-500">
                        <span>Grundpreis ({formatEur(product.price_per_kg_eur)}/kg)</span>
                        <span>{formatEur(basePrice)}</span>
                    </div>
                    {anarbeitungCost > 0 && (
                        <div className="flex justify-between text-gray-500">
                            <span>Anarbeitung</span>
                            <span>{formatEur(anarbeitungCost)}</span>
                        </div>
                    )}
                    {certCost > 0 && (
                        <div className="flex justify-between text-gray-500">
                            <span>Zeugnis</span>
                            <span>{formatEur(certCost)}</span>
                        </div>
                    )}
                    <div className="border-t border-gray-100 pt-2 flex justify-between text-lg font-bold text-gray-900">
                        <span>Gesamtpreis</span>
                        <span>{formatEur(totalPrice)}</span>
                    </div>
                    <div className="text-right text-xs text-gray-400">zzgl. MwSt. und Versand</div>
                </div>
            </div>

            {/* Add to cart button */}
            <button
                onClick={handleAddToCart}
                disabled={adding}
                className={`w-full py-3 px-4 rounded-lg font-semibold text-white transition-colors ${
                    added
                        ? 'bg-green-600'
                        : adding
                            ? 'bg-gray-400 cursor-wait'
                            : 'bg-orange-500 hover:bg-orange-600'
                }`}
            >
                {added ? '✓ Zum Warenkorb hinzugefügt' : adding ? 'Wird hinzugefügt...' : 'In den Warenkorb'}
            </button>
        </div>
    );
}
