import React, { useState } from 'react';

interface MaterialOption {
    id: number;
    grade: string;
}

interface FormOption {
    id: number;
    name: string;
}

interface Props {
    materials: MaterialOption[];
    forms: FormOption[];
    selectedMaterials: number[];
    selectedForms: number[];
    inStock: boolean;
    restlaengen: boolean;
    sort: string;
    baseUrl: string;
}

export default function CategoryFilter({
    materials,
    forms,
    selectedMaterials: initialMaterials,
    selectedForms: initialForms,
    inStock: initialInStock,
    restlaengen: initialRestlaengen,
    sort,
    baseUrl,
}: Props) {
    const [selMaterials, setSelMaterials] = useState<number[]>(
        (initialMaterials || []).map(Number)
    );
    const [selForms, setSelForms] = useState<number[]>(
        (initialForms || []).map(Number)
    );
    const [inStock, setInStock] = useState(initialInStock);
    const [restlaengen, setRestlaengen] = useState(initialRestlaengen);

    const toggleMaterial = (id: number) => {
        setSelMaterials(prev =>
            prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]
        );
    };

    const toggleForm = (id: number) => {
        setSelForms(prev =>
            prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]
        );
    };

    const applyFilters = () => {
        const params = new URLSearchParams();
        selMaterials.forEach(id => params.append('materials[]', String(id)));
        selForms.forEach(id => params.append('forms[]', String(id)));
        if (inStock) params.set('in_stock', '1');
        if (restlaengen) params.set('restlaengen', '1');
        if (sort) params.set('sort', sort);
        const url = `${baseUrl}?${params.toString()}`;
        window.dispatchEvent(new CustomEvent('filter-apply', { detail: { url } }));
    };

    const resetFilters = () => {
        setSelMaterials([]);
        setSelForms([]);
        setInStock(false);
        setRestlaengen(false);
        window.dispatchEvent(new CustomEvent('filter-apply', { detail: { url: baseUrl } }));
    };

    const hasFilters = selMaterials.length > 0 || selForms.length > 0 || inStock || restlaengen;

    return (
        <div className="space-y-5">
            {/* Materials */}
            {materials.length > 0 && (
                <div>
                    <h4 className="text-sm font-medium text-gray-900 mb-2">Werkstoff</h4>
                    <div className="space-y-1.5 max-h-48 overflow-y-auto">
                        {materials.map(m => (
                            <label key={m.id} className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={selMaterials.includes(m.id)}
                                    onChange={() => toggleMaterial(m.id)}
                                    className="rounded border-gray-300 text-blue-900 focus:ring-blue-900"
                                />
                                <span className="text-sm text-gray-700">{m.grade}</span>
                            </label>
                        ))}
                    </div>
                </div>
            )}

            {/* Forms */}
            {forms.length > 0 && (
                <div>
                    <h4 className="text-sm font-medium text-gray-900 mb-2">Form</h4>
                    <div className="space-y-1.5 max-h-48 overflow-y-auto">
                        {forms.map(f => (
                            <label key={f.id} className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={selForms.includes(f.id)}
                                    onChange={() => toggleForm(f.id)}
                                    className="rounded border-gray-300 text-blue-900 focus:ring-blue-900"
                                />
                                <span className="text-sm text-gray-700">{f.name}</span>
                            </label>
                        ))}
                    </div>
                </div>
            )}

            {/* Toggles */}
            <div className="space-y-2">
                <label className="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        checked={inStock}
                        onChange={() => setInStock(!inStock)}
                        className="rounded border-gray-300 text-blue-900 focus:ring-blue-900"
                    />
                    <span className="text-sm text-gray-700">Nur Lagerware</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        checked={restlaengen}
                        onChange={() => setRestlaengen(!restlaengen)}
                        className="rounded border-gray-300 text-blue-900 focus:ring-blue-900"
                    />
                    <span className="text-sm text-gray-700">Restlängen verfügbar</span>
                </label>
            </div>

            {/* Buttons */}
            <div className="flex flex-col gap-2 pt-2">
                <button
                    onClick={applyFilters}
                    className="w-full py-2 px-4 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition-colors"
                >
                    Filter anwenden
                </button>
                {hasFilters && (
                    <button
                        onClick={resetFilters}
                        className="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors"
                    >
                        Filter zurücksetzen
                    </button>
                )}
            </div>
        </div>
    );
}
