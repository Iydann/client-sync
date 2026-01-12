<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => Invoice::previewInvoiceNumber())
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step('0.01'),
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
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step('0.01'),
                        Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        DatePicker::make('due_date')
                            ->required(),
                    ]),
            ]);
    }
}