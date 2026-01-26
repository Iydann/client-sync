<div class="mr-3"
    x-data="{
        year: @js($currentYear),
        init() {
            this.$watch('year', value => {
                Livewire.dispatch('yearChanged', value)
            })
        }
    }">
    <select name="year"
        x-model="year"
        class="fi-input block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
        <option value="all">All Years</option>
        @foreach ($years as $year)
        <option value="{{ $year }}">
            {{ $year }}
        </option>
        @endforeach
    </select>
</div>