<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_numbers', 'building_id'];

    protected $casts = [
        'phone_numbers' => 'array',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_organization');
    }

}
