<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusteredPrecinct extends Model
{
    use HasFactory;

    protected $fillable = ['clustered_precinct_num', 'barangay_id', 'clustered_status'];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function precinctNumbers()
    {
        return $this->hasMany(PrecinctNumber::class, 'clustered_precinct_id');
    }
}
