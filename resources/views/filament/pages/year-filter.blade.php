<div class="mr-3"
    x-data="{
        year: @js($currentYear),
        changeYear(value) {
            const url = new URL(window.location.href)
            url.searchParams.set('year', value)

            if (window.Livewire?.dispatch) {
                window.history.replaceState({}, '', url.toString())
                window.Livewire.dispatch('yearChanged', { year: value })
                return
            }

            if (window.Livewire?.emit) {
                window.history.replaceState({}, '', url.toString())
                window.Livewire.emit('yearChanged', value)
                return
            }

            window.location.href = url.toString()
        }
    }">
    <select name="year"
        x-model="year"
        x-on:change="changeYear($event.target.value)"
        class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-6 py-4 text-base"
        style="min-width: 200px; min-height: 40px;">
        <option value="all" @selected($currentYear === 'all')>All Years</option>
        @foreach ($years as $year)
        <option value="{{ $year }}" @selected((string) $currentYear === (string) $year)>
            {{ $year }}
        </option>
        @endforeach
    </select>
</div>