<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('project_id')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Project'),
                TextInput::make('invoice_number')
                    ->label('Invoice Number')
                    ->disabled()
                    ->dehydrated()
                    ->default(fn () => Invoice::previewInvoiceNumber())
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step('0.01'),
                Select::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('unpaid'),
                DatePicker::make('due_date')
                    ->required(),
            ]);
    }
}
