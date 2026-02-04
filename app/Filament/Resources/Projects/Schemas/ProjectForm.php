<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('client_id')
                    ->relationship('client', 'client_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Client')
                    ->live(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('start_date'),
                DatePicker::make('deadline'),
                DatePicker::make('contract_date'),
                TextInput::make('contract_number')
                    ->maxLength(100),
                TextInput::make('contract_value')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('IDR')
                    ->required()
                    ->live(),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->required()
                    ->default('pending'),

                // Tax Configuration Section
                Section::make('Tax Configuration')
                    ->description('Configure PPN and PPH rates for this project')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('include_tax')
                            ->label('Contract Value Includes Tax')
                            ->helperText('Enable if contract value already includes taxes. Disable if taxes should be added.')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('ppn_rate')
                            ->label('PPN Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->default(11.00)
                            ->live(),
                        TextInput::make('pph_rate')
                            ->label('PPH Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->default(2.50)
                            ->live(),

                        // Display calculated amounts
                        Placeholder::make('ppn_amount')
                            ->label('PPN Amount')
                            ->columnSpan(1)
                            ->content(fn ($get) => self::formatPpnAmount(
                                $get('contract_value'),
                                $get('ppn_rate'),
                                $get('pph_rate'),
                                $get('include_tax')
                            )),

                        Placeholder::make('pph_amount')
                            ->label('PPH Amount')
                            ->columnSpan(1)
                            ->content(fn ($get) => self::formatPphAmount(
                                $get('contract_value'),
                                $get('ppn_rate'),
                                $get('pph_rate'),
                                $get('include_tax')
                            )),

                        Placeholder::make('grand_total')
                            ->label('Grand Total')
                            ->columnSpanFull()
                            ->content(fn ($get) => self::formatGrandTotal(
                                $get('contract_value'),
                                $get('ppn_rate'),
                                $get('pph_rate'),
                                $get('include_tax')
                            )),
                    ]),
            ]);
    }

    private static function formatPpnAmount($contractValue, $ppnRate, $pphRate, $includeTax): string
    {
        $contractValue = (float) $contractValue ?? 0;
        $ppnRate = (float) $ppnRate ?? 11.00;
        $pphRate = (float) $pphRate ?? 2.50;

        if ($contractValue <= 0) {
            return 'IDR 0';
        }

        if ($includeTax) {
            $totalTaxPercent = ($ppnRate + $pphRate) / 100;
            $subtotal = $contractValue / (1 + $totalTaxPercent);
            $ppnAmount = $subtotal * $ppnRate / 100;
        } else {
            $ppnAmount = $contractValue * $ppnRate / 100;
        }

        return 'IDR ' . number_format($ppnAmount, 0, ',', '.');
    }

    private static function formatPphAmount($contractValue, $ppnRate, $pphRate, $includeTax): string
    {
        $contractValue = (float) $contractValue ?? 0;
        $ppnRate = (float) $ppnRate ?? 11.00;
        $pphRate = (float) $pphRate ?? 2.50;

        if ($contractValue <= 0) {
            return 'IDR 0';
        }

        if ($includeTax) {
            $totalTaxPercent = ($ppnRate + $pphRate) / 100;
            $subtotal = $contractValue / (1 + $totalTaxPercent);
            $pphAmount = $subtotal * $pphRate / 100;
        } else {
            $pphAmount = $contractValue * $pphRate / 100;
        }

        return 'IDR ' . number_format($pphAmount, 0, ',', '.');
    }

    private static function formatGrandTotal($contractValue, $ppnRate, $pphRate, $includeTax): string
    {
        $contractValue = (float) $contractValue ?? 0;
        $ppnRate = (float) $ppnRate ?? 11.00;
        $pphRate = (float) $pphRate ?? 2.50;

        if ($contractValue <= 0) {
            return 'IDR 0';
        }

        if ($includeTax) {
            $grandTotal = $contractValue;
        } else {
            $ppnAmount = $contractValue * $ppnRate / 100;
            $pphAmount = $contractValue * $pphRate / 100;
            $grandTotal = $contractValue + $ppnAmount + $pphAmount;
        }

        return 'IDR ' . number_format($grandTotal, 0, ',', '.');
    }
}



