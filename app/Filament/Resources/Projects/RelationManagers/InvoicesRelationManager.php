<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) ($ownerRecord->invoices_count ?? $ownerRecord->invoices()->count());
    }

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        Section::make('Invoice Information')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->label('Invoice Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn () => Invoice::previewInvoiceNumber())
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('invoice_sequence')
                                    ->label('Invoice Sequence')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(function () {
                                        $project = $this->ownerRecord;
                                        $sequence = $project->invoices()->count() + 1;
                                        return '#' . $sequence;
                                    })
                                    ->columnSpan(1),
                            ]),
                        Section::make('Project Contract Information')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Placeholder::make('grand_total')
                                    ->label('Total Payment Required')
                                    ->columnSpan('full')
                                    ->content(function () {
                                        $project = $this->ownerRecord;
                                        $grandTotal = (float) ($project->grand_total ?? 0);
                                        if ($grandTotal <= 0) {
                                            $contractValue = (float) ($project->contract_value ?? 0);
                                            if ($project->include_tax) {
                                                $grandTotal = $contractValue;
                                            } else {
                                                $grandTotal = $contractValue + (float) ($project->ppn_amount ?? 0) + (float) ($project->pph_amount ?? 0);
                                            }
                                        }
                                        return 'IDR ' . number_format($grandTotal, 0, ',', '.');
                                    }),
                                Forms\Components\Placeholder::make('total_invoiced')
                                    ->label('Total Invoiced')
                                    ->content(function () {
                                        $totalInvoiced = $this->ownerRecord->invoices()
                                            ->where('status', '!=', 'cancelled')
                                            ->sum('amount') ?? 0;
                                        return 'IDR ' . number_format($totalInvoiced, 0, ',', '.');
                                    }),
                                Forms\Components\Placeholder::make('remaining')
                                    ->label('Remaining')
                                    ->content(function () {
                                        $project = $this->ownerRecord;
                                        $grandTotal = (float) ($project->grand_total ?? 0);
                                        if ($grandTotal <= 0) {
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
                                    }),
                            ]),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('unpaid'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status !== 'paid')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                    ]),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status !== 'paid'),
            ])
            ->emptyStateHeading('No invoices')
            ->emptyStateDescription('Create an invoice for this project.')
            ->emptyStateActions([
                CreateAction::make()
                    ->schema([
                        Section::make('Invoice Information')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->label('Invoice Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn () => Invoice::previewInvoiceNumber())
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('invoice_sequence')
                                    ->label('Invoice Sequence')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(function () {
                                        $project = $this->ownerRecord;
                                        $sequence = $project->invoices()->count() + 1;
                                        return '#' . $sequence;
                                    })
                                    ->columnSpan(1),
                            ]),
                        Section::make('Project Contract Information')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Placeholder::make('grand_total')
                                    ->label('Total Payment Required')
                                    ->columnSpan('full')
                                    ->content(function () {
                                        $project = $this->ownerRecord;
                                        $grandTotal = (float) ($project->grand_total ?? 0);
                                        if ($grandTotal <= 0) {
                                            $contractValue = (float) ($project->contract_value ?? 0);
                                            if ($project->include_tax) {
                                                $grandTotal = $contractValue;
                                            } else {
                                                $grandTotal = $contractValue + (float) ($project->ppn_amount ?? 0) + (float) ($project->pph_amount ?? 0);
                                            }
                                        }
                                        return 'IDR ' . number_format($grandTotal, 0, ',', '.');
                                    }),
                                Forms\Components\Placeholder::make('total_invoiced')
                                    ->label('Total Invoiced')
                                    ->content(function () {
                                        $totalInvoiced = $this->ownerRecord->invoices()
                                            ->where('status', '!=', 'cancelled')
                                            ->sum('amount') ?? 0;
                                        return 'IDR ' . number_format($totalInvoiced, 0, ',', '.');
                                    }),
                                Forms\Components\Placeholder::make('remaining')
                                    ->label('Remaining')
                                    ->content(function () {
                                        $project = $this->ownerRecord;
                                        $grandTotal = (float) ($project->grand_total ?? 0);
                                        if ($grandTotal <= 0) {
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
                                    }),
                            ]),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('unpaid'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                    ]),
            ]);
    }
}