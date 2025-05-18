<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriptions extends Model
{
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'status',
        'user_id',
    ];

    protected $table = 'subscriptions';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function renewals(): HasMany {
        return $this->hasMany(SubscriptionRenewals::class, 'subscription_id', "id");
    }

   

}
