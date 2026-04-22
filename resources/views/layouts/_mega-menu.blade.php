<div class="relative" x-data="{ open: false, activeCategory: 0, closeTimeout: null }"
     @mouseenter="clearTimeout(closeTimeout); open = true"
     @mouseleave="closeTimeout = setTimeout(() => { open = false }, 200)">
    {{-- Trigger Button --}}
    <button @click="open = !open"
            class="flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-blue-900 transition-colors px-3 py-2 rounded-lg hover:bg-gray-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="hidden sm:inline">Sortiment</span>
        <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Desktop Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         @keydown.escape.window="open = false"
         class="hidden lg:block absolute left-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-gray-200 z-50 w-[640px]"
         x-cloak>
        <div class="flex">
            {{-- Left: Category list --}}
            <div class="w-56 border-r border-gray-100 py-2">
                @foreach($megaMenuCategories as $index => $cat)
                    <a href="{{ route('category.show', $cat->slug) }}"
                       @mouseenter="activeCategory = {{ $index }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                       :class="activeCategory === {{ $index }} ? 'bg-blue-50 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-50'">
                        <x-category-icon :icon="$cat->icon" class="w-5 h-5" />
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
            {{-- Right: Subcategories --}}
            <div class="flex-1 p-4">
                @foreach($megaMenuCategories as $index => $cat)
                    <div x-show="activeCategory === {{ $index }}" x-cloak>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ $cat->name }}</h3>
                        <div class="grid grid-cols-2 gap-1">
                            @foreach($cat->children as $child)
                                <a href="{{ route('category.show', $child->slug) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-900 transition-colors">
                                    <span>{{ $child->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $child->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('category.show', $cat->slug) }}"
                           class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-900 hover:underline">
                            Alle {{ $cat->name }} anzeigen
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile Panel (accordion) --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         class="lg:hidden fixed inset-0 z-50 bg-black/50"
         @click.self="open = false"
         x-cloak>
        <div class="absolute inset-x-0 top-16 bottom-0 bg-white overflow-y-auto"
             @click.stop>
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Sortiment</h2>
                    <button @click="open = false" class="p-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-1" x-data="{ openSection: null }">
                    @foreach($megaMenuCategories as $index => $cat)
                        <div class="border-b border-gray-100 last:border-0">
                            <button @click="openSection = openSection === {{ $index }} ? null : {{ $index }}"
                                    class="flex items-center justify-between w-full px-3 py-3 text-left">
                                <div class="flex items-center gap-3">
                                    <x-category-icon :icon="$cat->icon" class="w-5 h-5" />
                                    <span class="text-sm font-medium text-gray-900">{{ $cat->name }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openSection === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openSection === {{ $index }}" x-collapse x-cloak class="pb-2">
                                <a href="{{ route('category.show', $cat->slug) }}"
                                   class="block px-3 py-2 ml-8 text-sm font-medium text-blue-900">
                                    Alle {{ $cat->name }}
                                </a>
                                @foreach($cat->children as $child)
                                    <a href="{{ route('category.show', $child->slug) }}"
                                       class="flex items-center justify-between px-3 py-2 ml-8 text-sm text-gray-600 hover:text-blue-900">
                                        <span>{{ $child->name }}</span>
                                        <span class="text-xs text-gray-400">{{ $child->products_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
