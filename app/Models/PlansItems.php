<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlansItems extends Model
{
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'plan_id'
    ];

    protected $table = 'plans_items';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function plans(): HasOne {
        return $this->hasOne(Plans::class, 'plan_id', "id");
    }
}
