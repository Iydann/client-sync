<div class="overflow-x-auto">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-2">Project Name</th>
                <th scope="col" class="px-4 py-2">Status</th>
                <th scope="col" class="px-4 py-2 text-right">Contract</th>
                <th scope="col" class="px-4 py-2 text-right text-green-600">Paid</th>
                <th scope="col" class="px-4 py-2 text-right text-red-600">Unpaid (Inv)</th>
                <th scope="col" class="px-4 py-2 text-right text-amber-600">Uninvoiced</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                @php
                    // Logika Perhitungan per Project (Mirroring Logic Parent)
                    
                    // 1. Paid: Invoice status 'paid'
                    $paid = $project->invoices->where('status', 'paid')->sum('amount');

                    // 2. Unpaid: Invoice status BUKAN 'paid' (Pending/Overdue/dll)
                    $unpaidInvoiced = $project->invoices->where('status', '!=', 'paid')->sum('amount');

                    // 3. Uninvoiced: Sisa Kontrak dikurangi total semua invoice (Paid + Unpaid)
                    // Rumus: Contract - (Total Invoiced)
                    $totalInvoiced = $paid + $unpaidInvoiced;
                    $uninvoiced = max(0, $project->contract_value - $totalInvoiced);
                @endphp

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{-- Kolom 1: Nama & Tanggal --}}
                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $project->name ?? $project->title ?? 'No Name' }}
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($project->contract_date)->format('d M Y') }}
                        </div>
                    </td>

                    {{-- Kolom 2: Status (Enum Handling) --}}
                    <td class="px-4 py-2">
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                            @if($project->status instanceof \Filament\Support\Contracts\HasLabel)
                                {{ $project->status->getLabel() }}
                            @elseif($project->status instanceof \BackedEnum)
                                {{ ucfirst($project->status->value) }}
                            @else
                                {{ ucfirst($project->status->name ?? $project->status) }}
                            @endif
                        </span>
                    </td>

                    {{-- Kolom 3: Total Contract --}}
                    <td class="px-4 py-2 text-right font-bold">
                        {{ number_format($project->contract_value, 0, ',', '.') }}
                    </td>

                    {{-- Kolom 4: Paid --}}
                    <td class="px-4 py-2 text-right text-green-600 font-medium">
                        {{ number_format($paid, 0, ',', '.') }}
                    </td>

                    {{-- Kolom 5: Unpaid (Invoiced) --}}
                    <td class="px-4 py-2 text-right text-red-600 font-medium">
                        {{ number_format($unpaidInvoiced, 0, ',', '.') }}
                    </td>

                    {{-- Kolom 6: Uninvoiced --}}
                    <td class="px-4 py-2 text-right text-amber-600 font-medium">
                        {{ number_format($uninvoiced, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        No projects found for this client in the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        {{-- Footer Total (Optional, untuk ringkasan cepat di modal) --}}
        @if($projects->count() > 0)
            <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white text-xs uppercase">
                <tr>
                    <td colspan="2" class="px-4 py-2 text-right">Total:</td>
                    <td class="px-4 py-2 text-right">
                        {{ number_format($projects->sum('contract_value'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-right text-green-600">
                        {{ number_format($projects->flatMap->invoices->where('status', 'paid')->sum('amount'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-right text-red-600">
                        {{ number_format($projects->flatMap->invoices->where('status', '!=', 'paid')->sum('amount'), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-right text-amber-600">
                        @php
                            $totalContract = $projects->sum('contract_value');
                            $totalInvoicedAll = $projects->flatMap->invoices->sum('amount');
                        @endphp
                        {{ number_format(max(0, $totalContract - $totalInvoicedAll), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>