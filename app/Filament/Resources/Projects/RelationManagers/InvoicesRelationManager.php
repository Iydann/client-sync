<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    public function table(Table $table): Table
    {
        return InvoicesTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => Invoice::previewInvoiceNumber())
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
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
                            ->prefix('Rp'),
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
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => Invoice::previewInvoiceNumber())
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
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