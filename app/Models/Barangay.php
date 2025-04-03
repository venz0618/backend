<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = ['barangay_name', 'city_id', 'barangay_status'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // public function clusteredPrecincts()
    // {
    //     return $this->hasMany(ClusteredPrecinct::class);
    // }
}
