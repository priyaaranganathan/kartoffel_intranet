<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Dear {{ $invoice->client->name }},</p>
    
    <p>Please find attached the invoice #{{ $invoice->invoice_number }} for project {{ $invoice->project->name }}.</p>
    
    <p>Invoice Details:</p>
    <ul>
        <li>Invoice Date: {{ $invoice->issue_date->format('d/m/Y') }}</li>
        <li>Due Date: {{ $invoice->due_date->format('d/m/Y') }}</li>
        <li>Amount: {{ number_format($invoice->total_amount, 2) }}</li>
    </ul>
    
    <p>Please process the payment at your earliest convenience.</p>
    
    <p>Thank you for your business!</p>
    
    <p>Best regards,<br>Kartoffel Team</p>
</body>
</html>