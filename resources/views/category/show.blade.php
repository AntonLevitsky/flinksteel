<x-app-layout>
    <x-slot:title>{{ $category->name }}</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
        {{-- Breadcrumbs --}}
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-900">Startseite</a>
            @foreach($breadcrumbs as $bc)
                <span class="mx-1">/</span>
                @if($loop->last)
                    <span class="text-gray-900 font-medium">{{ $bc->name }}</span>
                @else
                    <a href="{{ route('category.show', $bc->slug) }}" class="hover:text-blue-900">{{ $bc->name }}</a>
                @endif
            @endforeach
        </nav>

        @php
            $intro = $category->getIntroText();
            $related = $category->getRelatedCategories();
        @endphp

        {{-- Title + intro + cross-links --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
            @if($intro)
                <p class="text-sm text-gray-600 max-w-3xl leading-relaxed">{{ $intro }}</p>
            @endif
            @if(!empty($related))
                <p class="text-sm text-gray-500 mt-2">
                    <span class="text-gray-400">Andere Ausführung gesucht?</span>
                    @foreach($related as $i => $rel)
                        <a href="{{ route('category.show', $rel['category']->slug) }}" class="text-blue-900 hover:underline font-medium">{{ $rel['label'] }}</a>@if($i < count($related) - 1)<span class="text-gray-300"> · </span>@endif
                    @endforeach
                </p>
            @endif
        </div>

        {{-- Mobile filter trigger --}}
        <div class="flex items-center justify-between mb-4 lg:hidden">
            <span class="text-sm text-gray-500">{{ $products->total() }} Produkte</span>
            <button @click="filterOpen = true" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
        </div>

        {{-- Mobile filter overlay --}}
        <div x-show="filterOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="filterOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"
             x-cloak></div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar filters --}}
            <aside :class="filterOpen
                    ? 'fixed inset-x-0 bottom-0 z-50 bg-white rounded-t-2xl max-h-[85vh] overflow-y-auto shadow-2xl p-4'
                    : 'hidden lg:block lg:w-64 lg:shrink-0 lg:sticky lg:top-20'"
                x-cloak>
                {{-- Mobile drawer header --}}
                <div class="flex items-center justify-between mb-4 lg:hidden">
                    <h2 class="text-lg font-semibold text-gray-900">Filter</h2>
                    <button @click="filterOpen = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @php
                    $filterProps = [
                        'materials' => $availableMaterials->map(function($m) { return ['id' => $m->id, 'grade' => $m->grade]; })->values(),
                        'forms' => $availableForms->map(function($f) { return ['id' => $f->id, 'name' => $f->name]; })->values(),
                        'selectedMaterials' => request('materials', []),
                        'selectedForms' => request('forms', []),
                        'inStock' => request()->boolean('in_stock'),
                        'restlaengen' => request()->boolean('restlaengen'),
                        'sort' => $sort,
                        'baseUrl' => route('category.show', $category->slug),
                    ];
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <h2 class="font-semibold text-gray-900 mb-4">Filter</h2>
                    <div id="react-category-filter" data-props='@json($filterProps)'></div>
                </div>

                @if($children->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 p-4 mt-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Unterkategorien</h3>
                        <ul class="space-y-2">
                            @foreach($children as $child)
                                <li>
                                    <a href="{{ route('category.show', $child->slug) }}" class="text-sm text-gray-700 hover:text-blue-900 transition-colors">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>

            {{-- Product Grid --}}
            <div class="flex-1">
                <div id="product-grid-container">
                    @include('category._product-grid')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/islands/category-filter.tsx', 'resources/js/category-ajax.ts'])
    @endpush
</x-app-layout>
