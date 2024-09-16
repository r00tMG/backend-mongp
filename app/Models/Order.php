<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'payment_intent_id', 'total', 'status', 'payment_status', 'paid_at',
    ];

    /**
     * Relation avec l'utilisateur qui a passé la commande
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les transactions liées à cette commande
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
