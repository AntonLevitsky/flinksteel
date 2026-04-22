import React, { useState, useRef, useEffect, useCallback } from 'react';

interface Suggestion {
    id: number;
    name: string;
    sku: string;
    material_grade: string;
    category_name: string;
    form_slug: string;
    price_per_kg_eur: number;
    stock_status: string;
    url: string;
}

interface Props {
    searchUrl: string;
    suggestUrl: string;
    initialQuery: string;
}

function stockDotColor(status: string): string {
    switch (status) {
        case 'Ab Lager':
            return 'bg-green-500';
        case 'Geringe Menge':
            return 'bg-yellow-500';
        default:
            return 'bg-gray-400';
    }
}

function formatPrice(price: number): string {
    return price.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' \u20AC/kg';
}

export default function SearchAutocomplete({ searchUrl, suggestUrl, initialQuery }: Props) {
    const [query, setQuery] = useState(initialQuery);
    const [suggestions, setSuggestions] = useState<Suggestion[]>([]);
    const [isOpen, setIsOpen] = useState(false);
    const [activeIndex, setActiveIndex] = useState(-1);
    const [loading, setLoading] = useState(false);

    const containerRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const fetchSuggestions = useCallback(async (q: string) => {
        if (q.length < 2) {
            setSuggestions([]);
            setIsOpen(false);
            return;
        }

        setLoading(true);
        try {
            const resp = await fetch(`${suggestUrl}?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (resp.ok) {
                const data: Suggestion[] = await resp.json();
                setSuggestions(data);
                setIsOpen(data.length > 0);
                setActiveIndex(-1);
            }
        } catch (e) {
            console.error('Search suggest failed', e);
        } finally {
            setLoading(false);
        }
    }, [suggestUrl]);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setQuery(value);

        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }

        if (value.length < 2) {
            setSuggestions([]);
            setIsOpen(false);
            return;
        }

        timerRef.current = setTimeout(() => {
            fetchSuggestions(value);
        }, 300);
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (!isOpen) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActiveIndex(prev => (prev < suggestions.length - 1 ? prev + 1 : prev));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActiveIndex(prev => (prev > 0 ? prev - 1 : -1));
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && suggestions[activeIndex]) {
                e.preventDefault();
                window.location.href = suggestions[activeIndex].url;
            }
            // If no active index, let the form submit naturally
        } else if (e.key === 'Escape') {
            setIsOpen(false);
            setActiveIndex(-1);
        }
    };

    // Close dropdown on click outside
    useEffect(() => {
        const handleMouseDown = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setIsOpen(false);
                setActiveIndex(-1);
            }
        };
        document.addEventListener('mousedown', handleMouseDown);
        return () => document.removeEventListener('mousedown', handleMouseDown);
    }, []);

    // Cleanup timer on unmount
    useEffect(() => {
        return () => {
            if (timerRef.current) {
                clearTimeout(timerRef.current);
            }
        };
    }, []);

    return (
        <div ref={containerRef} className="relative">
            <form action={searchUrl} method="GET">
                <div className="relative">
                    <input
                        ref={inputRef}
                        type="text"
                        name="q"
                        value={query}
                        onChange={handleInputChange}
                        onKeyDown={handleKeyDown}
                        onFocus={() => {
                            if (suggestions.length > 0) setIsOpen(true);
                        }}
                        placeholder="Produkt, Werkstoff oder Artikelnummer suchen..."
                        className="w-full rounded-lg border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-blue-900 focus:ring-blue-900"
                        autoComplete="off"
                    />
                    <div className="absolute inset-y-0 left-0 flex items-center pl-3">
                        {loading ? (
                            <svg className="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        ) : (
                            <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        )}
                    </div>
                </div>
            </form>

            {isOpen && suggestions.length > 0 && (
                <div className="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                    {suggestions.map((suggestion, index) => (
                        <div
                            key={suggestion.id}
                            className={`px-4 py-3 cursor-pointer flex items-center gap-3 ${
                                index === activeIndex ? 'bg-blue-50' : 'hover:bg-gray-50'
                            } ${index > 0 ? 'border-t border-gray-100' : ''}`}
                            onMouseEnter={() => setActiveIndex(index)}
                            onClick={() => {
                                window.location.href = suggestion.url;
                            }}
                        >
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="font-medium text-gray-900 truncate">{suggestion.name}</span>
                                    <span className="text-xs font-mono text-gray-400 shrink-0">{suggestion.sku}</span>
                                </div>
                                <div className="flex items-center gap-2 mt-0.5">
                                    <span className="bg-blue-50 text-blue-700 text-xs px-1.5 py-0.5 rounded">{suggestion.material_grade}</span>
                                    <span className="text-xs text-gray-400">{suggestion.category_name}</span>
                                </div>
                            </div>
                            <div className="flex items-center gap-2 shrink-0">
                                <span className="text-sm text-gray-600">{formatPrice(suggestion.price_per_kg_eur)}</span>
                                <span className={`w-2 h-2 rounded-full ${stockDotColor(suggestion.stock_status)}`} title={suggestion.stock_status}></span>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
