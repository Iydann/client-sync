<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Mail\InvoiceMail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

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
            ->actions([
                ViewAction::make(),
                self::sendInvoiceAction(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function sendInvoiceAction(): Action
    {
        return Action::make('send_invoice')
            ->label(fn ($record) => $record->sent_at ? 'Resend' : 'Send')
            ->icon('heroicon-o-paper-airplane')
            ->color(fn ($record) => $record->sent_at ? 'gray' : 'primary')
            ->requiresConfirmation()
            ->modalHeading(fn ($record) => $record->sent_at ? 'Resend Invoice?' : 'Send Invoice?')
            ->modalDescription(fn ($record) => $record->sent_at 
                ? 'This invoice was already sent on ' . $record->sent_at->format('d M Y H:i') . '. Are you sure you want to resend it?'
                : 'Are you sure you want to send this invoice to the client\'s email?')
            ->modalSubmitActionLabel(fn ($record) => $record->sent_at ? 'Resend' : 'Send')
            ->action(function ($record) {
                $record->load(['project.client.user']);
                
                $clientEmail = $record->project->client->user->email ?? null;
                
                if (!$clientEmail) {
                    Notification::make()
                        ->title('Failed to send invoice')
                        ->body('Client email not found.')
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    Mail::to($clientEmail)->send(new InvoiceMail($record));
                    
                    $record->update(['sent_at' => now()]);

                    Notification::make()
                        ->title('Invoice sent successfully')
                        ->body('Invoice has been sent to ' . $clientEmail)
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to send invoice')
                        ->body('Error: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
