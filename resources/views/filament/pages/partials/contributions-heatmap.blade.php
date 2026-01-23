<x-filament::card>
    <div class="flex items-center gap-4 mb-6">
        
        <x-filament::avatar 
            :src="\Filament\Facades\Filament::getUserAvatarUrl($user)"
            :alt="$user->name"
            size="lg" 
            class="ring-1 ring-gray-200 dark:ring-gray-700"
        />
        
        <div class="flex flex-col">
            <span class="text-lg font-bold text-gray-900" style="color: #0f172a;">
                {{ $user->name }}
            </span>
            
            <span class="text-sm text-gray-500">
                {{ number_format($stats['total'] ?? 0) }} contributions in the last 3 months
            </span>
        </div>
    </div>


    <div>
        {{-- Legend --}}
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="text-sm font-medium text-gray-700">
                Contribution Activity
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span>Less</span>
                <span class="w-3.5 h-3.5 rounded-[2px] bg-gray-200 border border-gray-300"></span>
                <span class="w-3.5 h-3.5 rounded-[2px] bg-green-300 border border-green-400"></span>
                <span class="w-3.5 h-3.5 rounded-[2px] bg-green-400 border border-green-500"></span>
                <span class="w-3.5 h-3.5 rounded-[2px] bg-green-500 border border-green-600"></span>
                <span class="w-3.5 h-3.5 rounded-[2px] bg-green-700 border border-green-800"></span>
                <span>More</span>
            </div>
        </div>

        @php
            $heatmap = $stats['heatmap'] ?? [];
            
            // Logika Tanggal
            $firstDay = $heatmap[0]['date'] ?? null;
            $startDayOfWeek = $firstDay ? \Carbon\Carbon::parse($firstDay)->dayOfWeek : 0; 
            $padStart = $startDayOfWeek; 
            
            $totalCells = count($heatmap) + $padStart;
            $padEnd = (7 - ($totalCells % 7)) % 7;
            
            $cells = collect(array_fill(0, $padStart, null))
                ->merge($heatmap)
                ->merge(array_fill(0, $padEnd, null))
                ->values();
            
            $weeks = $cells->chunk(7);
            
            $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        @endphp

        <div class="flex items-start">
            <div class="flex flex-col gap-1 mr-2"> 
                @foreach($dayLabels as $label)
                    <div class="h-4 flex items-center">
                        <span class="text-[10px] font-medium leading-none text-gray-400 uppercase tracking-wide">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-1 overflow-x-auto pb-2 scrollbar-hide">
                @foreach ($weeks as $week)
                    <div class="flex flex-col gap-1">
                        @foreach ($week as $day)
                            <div 
                                @if(is_array($day))
                                    x-data 
                                    x-tooltip="{
                                        content: '{{ $day['count'] }} contributions on {{ \Carbon\Carbon::parse($day['date'])->format('D, M j, Y') }}',
                                        theme: $store.theme
                                    }"
                                @endif
                                @class([
                                    'w-4 h-4 rounded-[3px]',
                                    'border',
                                    
                                    'bg-transparent border-transparent' => is_null($day),
                                    'bg-gray-200 border-gray-300' => is_array($day) && $day['level'] === 0,
                                    
                                    'bg-green-300 border-green-400' => is_array($day) && $day['level'] === 1,
                                    'bg-green-400 border-green-500' => is_array($day) && $day['level'] === 2,
                                    'bg-green-500 border-green-600' => is_array($day) && $day['level'] === 3,
                                    'bg-green-700 border-green-800' => is_array($day) && $day['level'] === 4,
                                ])
                            ></div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament::card>