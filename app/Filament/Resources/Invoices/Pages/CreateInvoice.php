<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Schemas\InvoiceFormInfolist;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate invoice number at creation time with lock to prevent duplicates
        $data['invoice_number'] = Invoice::generateInvoiceNumber();
        return $data;
    }
}

