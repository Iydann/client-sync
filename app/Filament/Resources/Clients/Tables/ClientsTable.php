<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                        $newToken = Str::random(64);
                        $user->update([
                            'invitation_token' => $newToken,
                            'status' => 'invited',
                        ]);
                        Mail::to($user->email)
                            ->send(new ClientInvitationMail($user, $newToken, 'invite'));

                        Notification::make()
                            ->title('Invitation resent')
                            ->body("An invitation email has been sent to {$user->email}")
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