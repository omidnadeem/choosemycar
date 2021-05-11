<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Dealers;
use App\Traits\Uuid;

class Vehicles extends Model
{
    use Uuid;
    
    protected $table = 'vehicles';

    protected $fillable = ['mark', 'colour', 'fuel', 'status'];
    protected $dates = ['created_at', 'updated_at'];

    public function dealer()
    {
        return $this->belongsTo(Dealers::class, 'vehicle_dealer_id');
    }
}
