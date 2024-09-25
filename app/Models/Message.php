<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'emetteur_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'recepteur_id');
    }
}
