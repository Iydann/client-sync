<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    
    <h2>Hello, {{ $invoice->project->client->user->name }}</h2>

    <p>Please find attached your invoice <strong>{{ $invoice->invoice_number }}</strong> for the project <strong>{{ $invoice->project->title }}</strong>.</p>

    <table style="margin: 20px 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Invoice Number</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Project</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">{{ $invoice->project->title }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Invoice Amount</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">IDR {{ number_format($invoice->amount, 0, ',', '.') }}</td>
        </tr>
        @if($invoice->ppn_rate > 0)
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">PPN ({{ number_format($invoice->ppn_rate, 2) }}%)</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">IDR {{ number_format($invoice->ppn_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($invoice->pph_rate > 0)
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">PPH ({{ number_format($invoice->pph_rate, 2) }}%)</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">IDR {{ number_format($invoice->pph_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Due Date</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">{{ $invoice->due_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Status</td>
            <td style="padding: 8px 12px; border: 1px solid #ddd;">{{ $invoice->status->getLabel() }}</td>
        </tr>
    </table>

    <p>Please review the attached PDF for complete details.</p>

    <p>If you have any questions regarding this invoice, please don't hesitate to contact us.</p>

    <p style="margin-top: 30px;">
        Best regards,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #777;">
        This is an automated email. Please do not reply directly to this email.
    </p>
</body>
</html>
