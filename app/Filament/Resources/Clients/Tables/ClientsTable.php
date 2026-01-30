<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action; // SAYA TIDAK UBAH INI
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // PENTING: Pakai Str untuk token manual
use App\Mail\ClientInvitationMail;
use Filament\Notifications\Notification;

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

                TextColumn::make('user.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ready' => 'success',  
                        'invited' => 'warning',  
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone')->searchable(),
                TextColumn::make('address')->limit(50)->toggleable(),
            ])
            ->recordActions([
                ViewAction::make(),
                
                Action::make('resend_invite')
                    ->label('Resend Invite')
                    ->icon('heroicon-m-envelope')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->user->status, ['invited', 'pending']))
                    ->requiresConfirmation()
                    ->modalHeading('Resend Invitation')
                    ->modalDescription('A new token will be generated and a Setup Password link will be sent.')
                    ->action(function ($record) {
                        $user = $record->user;

                        // 1. Generate Token Manual (Agar terbaca di InvitationController)
                        $newToken = Str::random(64);

                        // 2. Update User (Simpan token di tabel users, BUKAN password_reset_tokens)
                        $user->update([
                            'invitation_token' => $newToken,
                            'status' => 'invited',
                        ]);

                        // 3. Kirim Email Tipe 'invite'
                        // Parameter ke-3 'invite' akan memicu route('invitation.show')
                        Mail::to($user->email)
                            ->send(new ClientInvitationMail($user, $newToken, 'invite'));

                        Notification::make()
                            ->title('Invitation resent')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}