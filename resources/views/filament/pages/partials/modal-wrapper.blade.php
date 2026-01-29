{{-- resources/views/filament/pages/partials/modal-wrapper.blade.php --}}

@livewire(\App\Livewire\ClientProjectsList::class, [
    'clientId' => $clientId,
    'year' => $year ?? null
])