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


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
