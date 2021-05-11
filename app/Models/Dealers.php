<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicles;
use App\Traits\Uuid;

class Dealers extends Model
{
    use Uuid;

    protected $table = 'dealers';

    protected $fillable = ['name', 'phone_number'];
    protected $dates = ['created_at', 'updated_at'];

    public function vehicles()
    {
        return $this->hasMany(Vehicles::class,'vehicle_dealer_id');
    }
}
