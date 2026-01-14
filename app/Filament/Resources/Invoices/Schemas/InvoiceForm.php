<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;

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
                    ->label('Project')
                    ->disabled(fn ($context) => $context === 'edit' || request()->routeIs('filament.*.resources.projects.edit'))
                    ->hidden(fn () => request()->routeIs('filament.*.resources.projects.edit')),
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
}
