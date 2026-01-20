<?php

namespace App\Filament\Schemas\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Invoice Details')
                ->columns(2)
                // ->columnSpan('full')
                ->schema([
                    TextEntry::make('invoice_number')->label('Invoice Number'),
                    TextEntry::make('project.title')->label('Project'),
                    TextEntry::make('amount')->label('Amount')
                        ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('created_at')->date(),
                    TextEntry::make('due_date')->date(),
                ])
                
        ]);
    }
}