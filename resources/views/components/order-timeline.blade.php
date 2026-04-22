@props(['order'])

@php
    $steps = [
        ['key' => 'bestaetigt', 'label' => 'Bestätigt', 'date' => $order->placed_at],
        ['key' => 'in_bearbeitung', 'label' => 'In Bearbeitung', 'date' => null],
        ['key' => 'versandt', 'label' => 'Versandt', 'date' => null],
        ['key' => 'zugestellt', 'label' => 'Zugestellt', 'date' => null],
    ];
    $statusOrder = ['bestaetigt', 'in_bearbeitung', 'versandt', 'zugestellt'];
    $currentIndex = array_search($order->status, $statusOrder);
    if ($currentIndex === false) $currentIndex = 0;
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div class="flex items-center justify-between">
        @foreach($steps as $index => $step)
            {{-- Step circle + label --}}
            <div class="flex flex-col items-center relative z-10">
                @if($index < $currentIndex)
                    {{-- Past step: green with checkmark --}}
                    <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="mt-2 text-xs font-medium text-green-700 hidden sm:block">{{ $step['label'] }}</span>
                @elseif($index === $currentIndex)
                    {{-- Current step: blue with icon --}}
                    <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-blue-900 ring-2 ring-blue-300 flex items-center justify-center">
                        @if($step['key'] === 'bestaetigt')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @elseif($step['key'] === 'in_bearbeitung')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif($step['key'] === 'versandt')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        @elseif($step['key'] === 'zugestellt')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-bold text-blue-900 hidden sm:block">{{ $step['label'] }}</span>
                @else
                    {{-- Future step: gray with icon --}}
                    <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        @if($step['key'] === 'in_bearbeitung')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif($step['key'] === 'versandt')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        @elseif($step['key'] === 'zugestellt')
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        @endif
                    </div>
                    <span class="mt-2 text-xs text-gray-400 hidden sm:block">{{ $step['label'] }}</span>
                @endif

                @if($step['date'])
                    <span class="text-[10px] text-gray-400 hidden sm:block">{{ $step['date']->format('d.m.Y') }}</span>
                @endif
            </div>

            {{-- Connecting line (not after last step) --}}
            @if($index < count($steps) - 1)
                <div class="flex-1 mx-2 h-0.5
                    @if($index < $currentIndex)
                        bg-green-500
                    @else
                        bg-gray-200 border-t border-dashed border-gray-300
                    @endif
                " style="margin-top: -1.25rem;"></div>
            @endif
        @endforeach
    </div>
</div>
