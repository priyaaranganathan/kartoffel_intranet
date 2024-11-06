<?php
namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Filament\Resources\InvoiceResource;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build()
    {
        return $this->view('emails.invoice')
            ->subject("Invoice #{$this->invoice->invoice_number}")
            ->attachData(
                InvoiceResource::generatePdf($this->invoice)->output(),
                "{$this->invoice->invoice_number}.pdf"
            );
    }
}