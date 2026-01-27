<x-filament-panels::page>
    <div 
        class="space-y-6"
        x-data="{
            chartData: @js($ganttData),
            selectedYear: @js($currentFilterYear),
            
            init() {
                if (typeof gantt === 'undefined') {
                    console.error('Library Gantt belum dimuat.');
                    return;
                }
                this.renderGantt();
                this.$watch('chartData', (value) => {
                    this.renderGantt();
                });
            },

            renderGantt() {
                const container = document.getElementById('gantt_here');
                if (!container) return;

                gantt.clearAll();

                // 1. AKTIFKAN PLUGIN
                gantt.plugins({ 
                    tooltip: true, 
                    marker: true 
                });
                
                // 2. KONFIGURASI TAMPILAN
                gantt.config.date_format = '%Y-%m-%d';
                
                // Matikan interaksi edit/geser
                gantt.config.readonly = true;            
                gantt.config.select_task = false;        
                gantt.config.details_on_dblclick = false; 

                // --- PENGATURAN UKURAN AGAR KONSISTEN ---
                gantt.config.bar_height = 20; 
                gantt.config.row_height = 35;
                
                // KUNCI: Lebar kolom bulan tetap 50px meski datanya 10 tahun
                // Ini akan memaksa scrollbar muncul panjang ke samping
                gantt.config.min_column_width = 50; 
                gantt.config.scale_height = 50;
                
                // Cegah chart mengecilkan diri agar muat di layar
                gantt.config.autosize = false; 

                // --- PENGATURAN GRID (KIRI) AGAR TIDAK TERPOTONG ---
                // Total Width = 220 (Name) + 80 (Status) + Padding
                gantt.config.grid_width = 360; 

                gantt.config.scales = [
                    { unit: 'year', step: 1, format: '%Y' }, // Baris Atas: Tahun (2026)
                    { unit: 'month', step: 1, format: '%M' }, // Baris Bawah: Bulan (Jan, Feb, ...)
                ];

                gantt.config.columns = [
                    { name: 'text', label: 'Project Name', width: 220, tree: true, resize: true },
                    { name: 'status', label: 'Status', width: 120, align: 'center' },
                ];

                // 3. TOOLTIP
                gantt.templates.tooltip_text = (start, end, task) => {
                    return `<b>${task.text}</b><br/>
                            Status: ${task.status}<br/>
                            Start: ${gantt.templates.tooltip_date_format(start)}<br/>
                            End: ${gantt.templates.tooltip_date_format(end)}<br/>
                            Progress: ${Math.round(task.progress * 100)}%`;
                };

                // 4. LOGIKA FILTER TAHUN (UPDATED)
                const today = new Date();
                const currentRealYear = today.getFullYear();
                
                if (this.selectedYear === 'all') {
                    // --- LOGIKA BARU UNTUK ALL YEARS ---
                    // Kita cari tanggal paling awal dan paling akhir dari data
                    // agar chart merentang sesuai durasi asli, bukan auto-fit.
                    
                    if (this.chartData.data && this.chartData.data.length > 0) {
                        let minDate = null;
                        let maxDate = null;

                        this.chartData.data.forEach(task => {
                            let start = new Date(task.start_date);
                            // Estimasi end date berdasarkan durasi (hari)
                            let end = new Date(start);
                            end.setDate(start.getDate() + (task.duration || 1));

                            if (!minDate || start < minDate) minDate = start;
                            if (!maxDate || end > maxDate) maxDate = end;
                        });

                        if (minDate && maxDate) {
                            // Tambahkan buffer 1 bulan sebelum & sesudah agar tidak mepet
                            minDate.setMonth(minDate.getMonth() - 1);
                            maxDate.setMonth(maxDate.getMonth() + 1);

                            gantt.config.start_date = minDate;
                            gantt.config.end_date = maxDate;
                        } else {
                            // Fallback jika data kosong
                            gantt.config.start_date = undefined;
                            gantt.config.end_date = undefined;
                        }
                    } else {
                        gantt.config.start_date = undefined;
                        gantt.config.end_date = undefined;
                    }
                    
                } else {
                    // Jika Tahun Tertentu: Tetap 1 Jan - 31 Des
                    const yearInt = parseInt(this.selectedYear);
                    gantt.config.start_date = new Date(yearInt, 0, 1);
                    gantt.config.end_date = new Date(yearInt, 11, 31);
                }

                // Inisialisasi & Load Data
                gantt.init('gantt_here');
                gantt.parse(this.chartData);

                // 5. LOGIKA SCROLL
                if (this.selectedYear == currentRealYear) {
                    gantt.addMarker({ start_date: today, css: 'today', text: 'Today' });
                    gantt.scrollTo(today); 
                } else {
                    gantt.deleteMarker('today'); 
                    // Scroll ke data paling awal
                    gantt.scrollTo(gantt.config.start_date);
                }
            }
        }"
    >
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <span>
                        Project Timeline 
                        <span class="text-gray-500 font-normal">
                            {{ $currentFilterYear === 'all' ? '(All Time)' : "($currentFilterYear)" }}
                        </span>
                    </span>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        @svg('heroicon-o-cursor-arrow-rays', 'w-4 h-4')
                        <span>Hover for details</span>
                    </div>
                </div>
            </x-slot>

            <div class="w-full relative min-h-[400px]">
                <div wire:ignore
                     id="gantt_here" 
                     class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 w-full"
                     style="height: {{ isset($ganttData['data']) ? max(450, (count($ganttData['data']) * 35) + 80) : 450 }}px;">
                </div>
                
                @if (!isset($ganttData['data']) || count($ganttData['data']) === 0)
                     <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 z-10 text-gray-500">
                        @svg('heroicon-o-calendar', 'w-16 h-16 text-gray-300')
                        <h3 class="text-lg font-medium mt-2">No Projects Found for {{ $currentFilterYear }}</h3>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Status Bar</x-slot>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-2">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #9ca3af;"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                </div>
                <div class="flex items-center gap-2">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #3b82f6;"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">In Progress</span>
                </div>
                <div class="flex items-center gap-2">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #10b981;"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                </div>
                <div class="flex items-center gap-2">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444;"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Cancelled</span>
                </div>
            </div>
        </x-filament::section>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
        <style>
            .gantt_marker.today { background-color: #EF4444 !important; opacity: 0.8; }
            .gantt_marker.today .gantt_marker_content { 
                background-color: #EF4444 !important; color: white !important; 
                font-size: 11px; padding: 2px 6px; border-radius: 4px; 
            }
            .gantt_layout_cell::-webkit-scrollbar { width: 8px; height: 8px; }
            .gantt_layout_cell::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            
            .dark .gantt_container, .dark .gantt_grid, .dark .gantt_task, .dark .gantt_task_bg {
                background-color: #111827 !important; color: #d1d5db !important;
            }
            .dark .gantt_grid_scale, .dark .gantt_scale_line, .dark .gantt_grid_head_cell {
                background-color: #1f2937 !important; color: #e5e7eb !important; border-color: #374151 !important;
            }
            .dark .gantt_row, .dark .gantt_task_row {
                background-color: #111827 !important; border-color: #374151 !important;
            }
            .gantt_task_row, .gantt_row {
                cursor: default !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    @endpush
</x-filament-panels::page>