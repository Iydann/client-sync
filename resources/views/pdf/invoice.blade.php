<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 25px 35px;
            background: #fff;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        /* Logo Style */
        .logo {
            font-size: 36px;
            font-weight: bold;
            color: #5BA4E6;
            letter-spacing: -1px;
            margin-bottom: 5px;
        }
        
        .logo-accent {
            color: #3B82F6;
            font-style: italic;
        }
        
        /* Invoice Title */
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e3a5f;
            text-align: right;
        }
        
        /* Header Info Table */
        .header-info {
            font-size: 10px;
            text-align: right;
        }
        
        .header-info td {
            padding: 2px 0;
        }
        
        .header-info .label {
            color: #666;
            padding-right: 15px;
        }
        
        .header-info .value {
            color: #333;
            text-align: right;
        }
        
        /* Section Headers */
        .section-header {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a5f;
            border-bottom: 2px solid #5BA4E6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 20px;
        }
        
        /* Company/Client Info */
        .info-section {
            vertical-align: top;
            padding-right: 20px;
        }
        
        .info-company-name {
            font-size: 12px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 5px;
        }
        
        .info-text {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }
        
        /* Items Table */
        .items-table {
            margin-top: 20px;
            border: 1px solid #ddd;
        }
        
        .items-table th {
            background: #1e3a5f;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #1e3a5f;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 10px;
            vertical-align: top;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        /* Notes & Totals Section */
        .bottom-section {
            margin-top: 20px;
        }
        
        .notes-box {
            vertical-align: top;
            padding-right: 20px;
        }
        
        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a5f;
            text-decoration: underline;
            margin-bottom: 8px;
        }
        
        .notes-content {
            font-size: 9px;
            color: #555;
            line-height: 1.6;
        }
        
        /* Totals */
        .totals-box {
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
        }
        
        .totals-table td {
            padding: 4px 8px;
            font-size: 10px;
        }
        
        .totals-table .label {
            text-align: right;
            color: #666;
            font-weight: bold;
        }
        
        .totals-table .value {
            text-align: right;
            color: #333;
            width: 120px;
        }
        
        .totals-table .total-final {
            background: #1e3a5f;
            color: white;
        }
        
        .totals-table .total-final td {
            padding: 8px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .totals-table .total-due {
            background: #5BA4E6;
            color: white;
        }
        
        .totals-table .total-due td {
            padding: 10px 8px;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-overdue {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-cancelled {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .footer-date {
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .footer-logo {
            font-size: 20px;
            font-weight: bold;
            color: #5BA4E6;
            letter-spacing: -1px;
        }
        
        .footer-tagline {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="logo">DEK<span class="logo-accent" style="font-style: normal;">A</span></div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="invoice-title">Invoice</div>
                <table class="header-info">
                    <tr>
                        <td class="label">Reference</td>
                        <td class="value">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="value">{{ $invoice->created_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Due Date</td>
                        <td class="value">{{ $invoice->due_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">
                            <span class="status-badge status-{{ strtolower($invoice->status->value) }}">
                                {{ $invoice->status->getLabel() }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    {{-- Company & Client Info --}}
    <table style="margin-top: 0px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                <div class="section-header">Company Information</div>
                <div class="info-company-name">DEKA</div>
                <div class="info-text">
                    Jl. Keledang No. 48 Samarinda, Indonesia<br>
                    Phone: (0541) 7807079<br>
                    Website: www.deka.co.id<br>
                    Email: hello@deka.co.id
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                <div class="section-header">Bill To</div>
                <div class="info-company-name">{{ $invoice->project->client->client_name }}</div>
                <div class="info-text">
                    @if($invoice->project->client->address)
                        {{ Str::limit($invoice->project->client->address, 80) }}<br>
                    @endif
                    @if($invoice->project->client->phone)
                        Phone: {{ $invoice->project->client->phone }}<br>
                    @endif
                    @if($invoice->project->client->user && $invoice->project->client->user->email)
                        Email: {{ $invoice->project->client->user->email }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
    
    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25%;">Project</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 15%;" class="text-center">Date</th>
                <th style="width: 25%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ Str::limit($invoice->project->title, 30) }}</td>
                <td>
                    @if($invoice->project->description)
                        {{-- {{ $invoice->project->description }} --}}
                        {{ Str::limit($invoice->project->description, 90) }}
                    @else
                        Project service fee
                    @endif
                    {{-- @if($invoice->project->contract_number)
                        <br><small style="color: #888;">Contract: {{ $invoice->project->contract_number }}</small>
                    @endif --}}
                </td>
                <td class="text-center">{{ $invoice->due_date->format('d/m/Y') }}</td>
                <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    {{-- Notes & Totals --}}
    <table class="bottom-section">
        <tr>
            <td style="width: 50%;" class="notes-box">
                <div class="notes-title">Notes</div>
                <div class="notes-content">
                    Please make payment before the due date.<br>
                    Include invoice number as payment reference.<br>
                    Thank you for your business.
                </div>
                
                <div class="notes-title" style="margin-top: 15px;">Payment Information</div>
                <div class="notes-content">
                    <strong>Bank Mandiri</strong><br>
                    Account: 1234xxxxx<br>
                    Name: CV Deka
                </div>
            </td>
            <td style="width: 50%;" class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax (0%)</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="label">Discount</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px;"></td>
                    </tr>
                    <tr class="total-final">
                        <td class="label" style="color: white;">Total</td>
                        <td class="value" style="color: white;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    {{-- Footer --}}
    <div class="footer">
        <div class="footer-date">{{ now()->format('d M, Y') }}</div>
        <div class="logo">DEK<span class="logo-accent" style="font-style: normal;">A</span></div>
        <div class="footer-tagline">Web Application Agency</div>
    </div>
</body>
</html>
