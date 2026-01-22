<x-filament::card>
    <div class="flex items-center gap-4 mb-4">
        <x-filament::avatar :label="$user->name" />
        <div>
            <div class="font-semibold">{{ $user->name }}</div>
            <div class="text-sm text-gray-500">
                {{ $stats['total'] ?? 0 }} contributions (last 3 months)
            </div>
        </div>
    </div>

    {{-- HEATMAP --}}
    <div class="mt-4">
        <div class="text-sm font-medium mb-2">Daily Contributions</div>

        <div class="flex gap-1">
            @foreach ($stats['heatmap'] ?? [] as $day)
                <div
                    class="w-3 h-3 rounded"
                    title="{{ $day['date'] }} — {{ $day['count'] }} contributions"
                    @class([
                        'bg-gray-200' => $day['level'] === 0,
                        'bg-green-200' => $day['level'] === 1,
                        'bg-green-400' => $day['level'] === 2,
                        'bg-green-600' => $day['level'] === 3,
                        'bg-green-800' => $day['level'] === 4,
                    ])
                ></div>
            @endforeach
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-500 mt-2">
            <span>Less</span>
            <span class="w-3 h-3 bg-gray-200 rounded"></span>
            <span class="w-3 h-3 bg-green-200 rounded"></span>
            <span class="w-3 h-3 bg-green-400 rounded"></span>
            <span class="w-3 h-3 bg-green-600 rounded"></span>
            <span class="w-3 h-3 bg-green-800 rounded"></span>
            <span>More</span>
        </div>
    </div>
</x-filament::card>
