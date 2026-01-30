<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\ClientType;
use Filament\Schemas\Components\View;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                // PERBAIKAN: Menampilkan Status User (Active/Pending)
                TextColumn::make('user.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',   // Hijau (Sudah verifikasi)
                        'pending' => 'warning',  // Kuning (Belum set password)
                        'banned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client_type')
                    ->label('Client Type')
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? $state?->value)
                    ->sortable()
                    ->searchable()
                    ->badge(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('address')
                    ->limit(50)
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                // Opsional: Tombol Resend Invite jika masih pending
                \Filament\Actions\Action::make('resend_invite')
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->visible(fn ($record) => $record->user->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\Mail::to($record->user->email)
                            ->send(new \App\Mail\ClientInvitationMail($record->user, $record->user->invitation_token));
                        \Filament\Notifications\Notification::make()->title('Undangan dikirim ulang')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}