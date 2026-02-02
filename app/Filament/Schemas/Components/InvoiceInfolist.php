<?php

namespace App\Filament\Schemas\Components;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Invoice Tabs')
                ->tabs([
                    Tab::make('Invoice Details')
                        ->icon('heroicon-m-document')
                        ->schema([
                            Section::make()
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('invoice_number')
                                        ->label('Invoice Number')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->columnSpan('full'),
                                    TextEntry::make('id')
                                        ->label('Invoice Sequence')
                                        ->formatStateUsing(function ($record) {
                                            $position = $record->project->invoices()
                                                ->orderBy('created_at', 'asc')
                                                ->pluck('id')
                                                ->search($record->id) + 1;
                                            return "#{$position}";
                                        }),
                                    TextEntry::make('amount')
                                        ->label('Amount')
                                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->size('lg')
                                        ->color(fn ($state) => $state->getColor()),
                                    TextEntry::make('due_date')
                                        ->label('Due Date')
                                        ->date('d F Y'),
                                ]),
                            Actions::make([
                                Action::make('download_pdf')
                                    ->label('Download PDF')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->action(function ($record) {
                                        $record->load(['project.client.user']);
                                        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $record]);
                                        return response()->streamDownload(function () use ($pdf) {
                                            echo $pdf->output();
                                        }, $record->invoice_number . '.pdf');
                                    }),
                                Action::make('preview_pdf')
                                    ->label('View PDF')
                                    ->icon('heroicon-m-eye')
                                    ->url(fn ($record) => route('invoice.preview', $record->id))
                                    ->openUrlInNewTab(),
                            ]),
                        ]),
                    Tab::make('Client Info')
                        ->icon('heroicon-m-user')
                        ->schema([
                            Section::make()
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('project.client.client_name')
                                        ->label('Client Name')
                                        ->weight('bold')
                                        ->columnSpan('full')
                                        ->size('lg'),
                                    TextEntry::make('project.client.user.email')
                                        ->label('Email')
                                        ->copyable()
                                        ->icon('heroicon-m-envelope'),
                                    TextEntry::make('project.client.phone')
                                        ->label('Phone')
                                        ->copyable()
                                        ->icon('heroicon-m-phone'),
                                    TextEntry::make('project.client.address')
                                        ->label('Address')
                                        ->hidden(fn ($record) => !$record->project->client->address),
                                    TextEntry::make('project.client.client_type')
                                        ->label('Client Type')
                                        ->badge()
                                        ->color(fn ($state) => $state?->value === 'corporate' ? 'info' : 'success'),
                                    TextEntry::make('project.client.user.created_at')
                                        ->label('Registration Date')
                                        ->date('d F Y'),
                                ]),
                        ]),

                    Tab::make('Project Info')
                        ->icon('heroicon-m-briefcase')
                        ->schema([
                            Section::make()
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('project.title')
                                        ->label('Project Name')
                                        ->weight('bold')
                                        ->columnSpan('full')
                                        ->size('lg'),
                                    TextEntry::make('project.description')
                                        ->label('Project Description')
                                        ->columnSpan('full')
                                        ->hidden(fn ($record) => !$record->project->description),
                                    TextEntry::make('project.contract_number')
                                        ->label('Contract Number'),
                                    TextEntry::make('project.contract_value')
                                        ->label('Contract Value')
                                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                    TextEntry::make('project.progress')
                                        ->label('Project Progress')
                                        ->suffix('%'),
                                    TextEntry::make('project.status')
                                        ->label('Project Status')
                                        ->badge()
                                        ->color(fn ($state) => $state->getColor()),
                                    TextEntry::make('project.contract_date')
                                        ->label('Contract Date')
                                        ->date('d F Y'),
                                    TextEntry::make('project.payment_progress')
                                        ->label('Payment Progress')
                                        ->suffix('%'),
                                ]),
                        ]),
                ])->columnSpanFull(),
        ]);
    }
}
