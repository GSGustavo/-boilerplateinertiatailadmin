<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRenewals extends Model
{
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'status',
        'plan_id',
        'start_on',
        'end_on',
        'subscription_id',
        'price'
    ];

    protected $table = 'subscription_renewals';


    protected function casts(): array
    {
        return [
            'start_on' => 'datetime:Y-m-d\TH:i:s-04:00',
            'end_on' => 'datetime:Y-m-d\TH:i:s-04:00',
            'paid_at' => 'datetime:Y-m-d\TH:i:s-04:00',
            'paid_at' => 'datetime',
        ];
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plans::class, 'plan_id', 'id');
    }

    public function getPriceAttribute($value)
    {
        // Format with a comma as decimal separator and optionally a dot for thousands
        return $value == 0 ? "0" : number_format($value, 2, ',', '.');
    }
}
