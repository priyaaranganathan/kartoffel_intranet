<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'project_id',
        // 'milestone_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'status', // draft, sent, paid
        'payment_received_date',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'issue_date' => 'date',
        'payment_received_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // protected static function booted()
    // {
    //     static::creating(function ($invoice) {
    //         // Set issue_date to current date
    //         if (!$invoice->issue_date) {
    //             $invoice->issue_date = Carbon::today(); // or Carbon::now()->toDateString()
    //         }

    //         // Set due_date to 7 days after issue_date
    //         if (!$invoice->due_date) {
    //             $invoice->due_date = Carbon::today()->addDays(7)->toDateString();
    //         }
    //     });
    // }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}