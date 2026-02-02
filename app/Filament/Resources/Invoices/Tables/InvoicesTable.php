<?php

namespace App\Filament\Resources\Invoices\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        \App\InvoiceStatus::Unpaid => 'gray',
                        \App\InvoiceStatus::Paid => 'success',
                        \App\InvoiceStatus::Overdue => 'warning',
                        \App\InvoiceStatus::Cancelled => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // ->defaultSort('invoice_number', 'desc') 

            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        $record->load(['project.client.user']);
                        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $record]);
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $record->invoice_number . '.pdf');
                    }),
                Action::make('preview_pdf')
                    ->label('Preview PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('invoice.preview', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
