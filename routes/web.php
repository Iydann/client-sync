<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    // return view('welcome');
    return redirect('/portal');
});
Route::get('/invitation/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}', [InvitationController::class, 'store'])->name('invitation.store');

Route::get('/invoice/{invoice}/preview', function (Invoice $invoice) {
    $invoice->load(['project.client.user']);
    $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);
    return $pdf->stream($invoice->invoice_number . '.pdf');
})->name('invoice.preview')->middleware('auth');
