<?php

namespace App\Filament\Schemas\Components;

use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Project Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('client.client_name')
                            ->label('Client'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('progress')
                            ->label('Progress')
                            ->formatStateUsing(fn ($state) => $state . '%'),
                        TextEntry::make('contract_number')
                            ->label('Contract Number')
                            ->placeholder('-'),
                        TextEntry::make('contract_date')
                            ->date(),
                        TextEntry::make('contract_value')
                            ->label('Contract Value')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                        TextEntry::make('payment_progress')
                            ->label('Payment Progress')
                            ->formatStateUsing(fn ($state) => $state . '%'),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('deadline')
                            ->date(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Section::make('Project Assets')
                    ->schema([
                        TextEntry::make('display_assets')
                            ->label('Uploaded Files')
                            ->getStateUsing(function (Project $record) {
                                $files = ($record->getMedia('project-assets') ?? collect())->reverse()->values();
                                
                                if ($files->isEmpty()) {
                                    return '<span style="color: #6b7280; font-style: italic; font-size: 0.875rem;">No assets uploaded yet.</span>';
                                }
                                
                                return $files->map(function ($media) {
                                    $url = $media->getUrl();
                                    $fileName = $media->name ?? 'Unknown';
                                    $size = $media->size ? round($media->size / 1024, 2) . ' KB' : 'Unknown';
                                    
                                    return "
                                        <div class='flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 mb-2'>
                                            <svg class='w-4 h-4 text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'></path>
                                            </svg>
                                            <div class='flex flex-col'>
                                                <a href='{$url}' target='_blank' style='color: #2563eb; text-decoration: underline; font-size: 0.875rem; font-weight: 600;'>
                                                    {$fileName}
                                                </a>
                                                <span style='font-size: 10px; color: #6b7280; text-transform: uppercase;'>
                                                    {$size}
                                                </span>
                                            </div>
                                        </div>
                                    ";
                                })->implode('');
                            })
                            ->html()
                            ->columnSpanFull(),
                    ]),
                Section::make('Contract Value Details')
                    ->description(fn ($record) => $record->include_tax ? 'Contract value includes tax' : 'Contract value excludes tax')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('project_payment')
                            ->label('Project Payment')
                            ->getStateUsing(function ($record) {
                                $contractValue = (float) ($record->contract_value ?? 0);
                                $includeTax = (bool) ($record->include_tax ?? false);
                                $ppnAmount = (float) ($record->ppn_amount ?? 0);
                                $pphAmount = (float) ($record->pph_amount ?? 0);
                                $subtotal = $includeTax
                                    ? ($contractValue - $ppnAmount - $pphAmount)
                                    : $contractValue;

                                return 'Rp ' . number_format($subtotal, 0, ',', '.');
                            }),
                        TextEntry::make('ppn_amount')
                            ->label(fn ($record) => 'PPN (' . number_format($record->ppn_rate ?? 0, 2) . '%)')
                            ->getStateUsing(function ($record) {
                                $ppnAmount = (float) ($record->ppn_amount ?? 0);
                                return 'Rp ' . number_format($ppnAmount, 0, ',', '.');
                            })
                            ->hidden(fn ($record) => $record->ppn_rate == 0),
                        TextEntry::make('pph_amount')
                            ->label(fn ($record) => 'PPH (' . number_format($record->pph_rate ?? 0, 2) . '%)')
                            ->getStateUsing(function ($record) {
                                $pphAmount = (float) ($record->pph_amount ?? 0);
                                return 'Rp ' . number_format($pphAmount, 0, ',', '.');
                            })
                            ->hidden(fn ($record) => $record->pph_rate == 0),
                        TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->getStateUsing(function ($record) {
                                $contractValue = (float) ($record->contract_value ?? 0);
                                $includeTax = (bool) ($record->include_tax ?? false);
                                $ppnAmount = (float) ($record->ppn_amount ?? 0);
                                $pphAmount = (float) ($record->pph_amount ?? 0);
                                $grandTotal = $includeTax
                                    ? $contractValue
                                    : ($contractValue + $ppnAmount + $pphAmount);

                                return 'Rp ' . number_format($grandTotal, 0, ',', '.');
                            })
                            ->weight('bold'),
                    ]),
            ]);
    }
}