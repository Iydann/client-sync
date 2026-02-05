<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Invoice Information')
                    ->columns(2)
                    ->schema([
                        Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Project')
                            ->disabled(fn ($context) => $context === 'edit' || request()->routeIs('filament.*.resources.projects.edit'))
                            ->hidden(fn () => request()->routeIs('filament.*.resources.projects.edit'))
                            ->live()
                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                $projectId = $state ?? $record?->project_id;
                                self::setInvoiceNumberAndSequence($set, $projectId, $record);
                            })
                            ->afterStateUpdated(function ($state, callable $set, $record) {
                                self::setInvoiceNumberAndSequence($set, $state, $record);
                            })
                            ->columnSpan('full'),
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->dehydrated()
                            ->default('-')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        TextInput::make('invoice_sequence')
                            ->label('Invoice Sequence')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                    ]),
                Section::make('Project Contract Information')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('grand_total')
                            ->label('Total Payment Required')
                            ->columnSpan('full')
                            ->content(fn (callable $get) => self::formatGrandTotal($get('project_id'))),
                        Placeholder::make('total_invoiced')
                            ->label('Total Invoiced')
                            ->content(fn (callable $get) => self::formatTotalInvoiced($get('project_id'))),
                        Placeholder::make('remaining')
                            ->label('Remaining')
                            ->content(fn (callable $get) => self::formatRemaining($get('project_id'))),
                    ]),

                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Select::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('unpaid'),
                DatePicker::make('due_date')
                    ->required(),
            ]);
    }

    private static function setInvoiceNumberAndSequence(callable $set, ?int $projectId, $record): void
    {
        if (!$projectId) {
            $set('invoice_number', '-');
            $set('invoice_sequence', '-');
            return;
        }

        $sequence = self::computeSequence($projectId, $record);
        $set('invoice_sequence', $sequence ? '#' . $sequence : '-');
        $set('invoice_number', $sequence ? Invoice::previewInvoiceNumber() : '-');
    }

    private static function computeSequence(int $projectId, $record): ?int
    {
        $ids = Invoice::where('project_id', $projectId)
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        if ($record && in_array($record->id, $ids, true)) {
            return array_search($record->id, $ids, true) + 1;
        }

        return count($ids) + 1;
    }

    private static function formatGrandTotal(?int $projectId): string
    {
        if (!$projectId) {
            return 'IDR 0';
        }
        $project = Project::find($projectId);
        if (!$project) {
            return 'IDR 0';
        }
        $grandTotal = (float) ($project->grand_total ?? 0);
        if ($grandTotal <= 0) {
            // Fallback calculation if grand_total not set
            $contractValue = (float) ($project->contract_value ?? 0);
            if ($project->include_tax) {
                $grandTotal = $contractValue;
            } else {
                $grandTotal = $contractValue + (float) ($project->ppn_amount ?? 0) + (float) ($project->pph_amount ?? 0);
            }
        }
        return 'IDR ' . number_format($grandTotal, 0, ',', '.');
    }

    private static function formatTotalInvoiced(?int $projectId): string
    {
        if (!$projectId) {
            return 'IDR 0';
        }
        $project = Project::find($projectId);
        if (!$project) {
            return 'IDR 0';
        }
        $totalInvoiced = $project->invoices()
            ->where('status', '!=', 'cancelled')
            ->sum('amount') ?? 0;
        return 'IDR ' . number_format($totalInvoiced, 0, ',', '.');
    }

    private static function formatRemaining(?int $projectId): string
    {
        if (!$projectId) {
            return 'IDR 0';
        }
        $project = Project::find($projectId);
        if (!$project) {
            return 'IDR 0';
        }
        
        // Get grand total
        $grandTotal = (float) ($project->grand_total ?? 0);
        if ($grandTotal <= 0) {
            // Fallback calculation if grand_total not set
            $contractValue = (float) ($project->contract_value ?? 0);
            if ($project->include_tax) {
                $grandTotal = $contractValue;
            } else {
                $grandTotal = $contractValue + (float) ($project->ppn_amount ?? 0) + (float) ($project->pph_amount ?? 0);
            }
        }
        
        $totalInvoiced = $project->invoices()
            ->where('status', '!=', 'cancelled')
            ->sum('amount') ?? 0;
        $remaining = $grandTotal - $totalInvoiced;
        return 'IDR ' . number_format($remaining, 0, ',', '.');
    }

}
