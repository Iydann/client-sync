<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class IdleDevelopersWidget extends TableWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    
    // Judul saya ganti agar lebih akurat secara konteks
    protected static ?string $heading = 'Unassigned Developers (Bench)';

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('roles', fn (Builder $query) => $query->where('name', 'developer'))
                
                    ->whereDoesntHave('projects', function (Builder $query) {
                        $query->whereIn('status', ['in_progress', 'pending']);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Developer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined Since')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->default('Bench / Idle')
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                // Aksi diarahkan ke Edit User agar bisa menambah Project di sana
                Action::make('assign_project')
                    ->label('Assign Project')
                    ->icon('heroicon-m-briefcase')
                    ->url(fn (User $record): string => route('filament.admin.resources.users.edit', $record))
                    ->button(),
            ])
            ->paginated(false);
    }
}