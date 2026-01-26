<x-filament-panels::page>
    <div class="space-y-6">
        @foreach ($users as $user)
            @include('filament.pages.partials.contributions-heatmap', [
                'user' => $user,
                'stats' => $stats[$user->id] ?? null,
            ])
        @endforeach
    </div>
</x-filament-panels::page>