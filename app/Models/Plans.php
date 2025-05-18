<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plans extends Model
{
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'status',
        'name',
        'price',
        'days',
        'internal'
    ];

    protected $table = 'plans';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function plan_items(): HasMany {
        return $this->hasMany(PlansItems::class, 'plan_id', "id");
    }

    public function getPriceAttribute($value)
    {
        // Format with a comma as decimal separator and optionally a dot for thousands
        return $value == null ? "0" : number_format($value, 2, ',', '.');
    }
}
