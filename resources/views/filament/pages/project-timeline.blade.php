<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <span>Project Timeline</span>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        @svg('heroicon-o-cursor-arrow-rays', 'w-4 h-4')
                        <span>Click for information</span>
                    </div>
                </div>
            </x-slot>

            <div class="w-full">
                @if (isset($ganttData['data']) && count($ganttData['data']) > 0)
                    @php
                        $count = count($ganttData['data']);
                        $height = max(450, ($count * 35) + 80);
                        $jsonString = json_encode($ganttData);
                    @endphp
                    
                    <div id="gantt_here" 
                         data-gantt-json="{{ $jsonString }}"
                         class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 w-full"
                         style="height: {{ $height }}px;">
                    </div>

                @else
                    <div class="flex flex-col items-center justify-center h-64 text-gray-500 gap-4">
                        @svg('heroicon-o-calendar', 'w-16 h-16 text-gray-300')
                        <h3 class="text-lg font-medium">No Project Data Available</h3>
                        <p class="text-sm">Please ensure projects have Contract Date and Deadline set.</p>
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
            .gantt_marker.today .gantt_marker_content { background-color: #EF4444 !important; color: white !important; font-size: 11px; padding: 2px 6px; border-radius: 4px; }
            .gantt_layout_cell::-webkit-scrollbar { width: 8px; height: 8px; }
            .gantt_layout_cell::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            
            /* Dark Mode Fixes */
            .dark .gantt_container, .dark .gantt_grid, .dark .gantt_task, .dark .gantt_task_bg {
                background-color: #111827 !important; color: #d1d5db !important;
            }
            .dark .gantt_grid_scale, .dark .gantt_scale_line, .dark .gantt_grid_head_cell {
                background-color: #1f2937 !important; color: #e5e7eb !important; border-color: #374151 !important;
            }
            .dark .gantt_cell, .dark .gantt_task_cell {
                background-color: #111827 !important; color: #d1d5db !important; border-color: #374151 !important;
            }
            .dark .gantt_row, .dark .gantt_task_row {
                background-color: #111827 !important; border-color: #374151 !important;
            }
            .dark .gantt_row:hover, .dark .gantt_task_row:hover {
                background-color: #1f2937 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => initGantt());
            document.addEventListener('livewire:navigated', () => { 
                if(typeof gantt !== 'undefined') gantt.clearAll(); 
                initGantt(); 
            });

            function initGantt() {
                const container = document.getElementById("gantt_here");
                if (!container) return;

                const rawJson = container.getAttribute('data-gantt-json');
                if (!rawJson) return;

                let rawData;
                try { rawData = JSON.parse(rawJson); } catch (e) { console.error("JSON Error:", e); return; }

                if (!rawData || !rawData.data || rawData.data.length === 0) return;

                gantt.plugins({ marker: true, tooltip: true });

                // --- LOGIKA RENTANG WAKTU (1 Bulan Belakang - 3 Bulan Depan) ---
                const today = new Date();
                const startDate = new Date(today);
                startDate.setMonth(today.getMonth() - 1);
                startDate.setDate(1);

                const endDate = new Date(today);
                endDate.setMonth(today.getMonth() + 3); 
                endDate.setDate(1);

                gantt.config.start_date = startDate;
                gantt.config.end_date = endDate;
                // ----------------------------------------------------------------

                gantt.config.date_format = "%Y-%m-%d"; 
                gantt.config.readonly = true;
                gantt.config.bar_height = 20; 
                gantt.config.row_height = 35;
                
                gantt.config.scales = [
                    { unit: "month", step: 1, format: "%F, %Y" },
                ];
                
                gantt.config.min_column_width = 100; 

                gantt.config.columns = [
                    { name: "text", label: "Project Name", width: 220, tree: true, resize: true },
                    { name: "status", label: "Status", width: 120, align: "center" },
                ];

                gantt.templates.tooltip_text = function(start, end, task) {
                    return `<b>${task.text}</b><br/>
                            Status: ${task.status}<br/>
                            Start: ${gantt.templates.tooltip_date_format(start)}<br/>
                            End: ${gantt.templates.tooltip_date_format(end)}<br/>
                            Progress: ${Math.round(task.progress * 100)}%`;
                };

                gantt.init("gantt_here");
                gantt.parse(rawData);
                gantt.addMarker({ start_date: new Date(), css: "today", text: "Today" });
            }
        </script>
    @endpush
</x-filament-panels::page>