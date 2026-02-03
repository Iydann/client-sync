<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Schemas\InvoiceFormInfolist;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
