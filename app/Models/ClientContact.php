<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientContact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email','status','client_id','contact','is_primary_contact'];

    protected $casts = [
        'status' =>  RecordStatus::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($contact) {
            if ($contact->is_primary) {
                // Ensure only one primary contact exists
                static::where('client_id', $contact->client_id)
                    ->where('id', '!=', $contact->id)
                    ->update(['is_primary_contact' => false]);
            }
        });

        static::deleting(function ($contact) {
            if ($contact->is_primary) {
                // Prevent deletion if contact is primary
                throw new \Exception('Cannot delete a primary contact.');
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
