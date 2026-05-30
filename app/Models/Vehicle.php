<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'brand_model_id',
        'vehicle_type_id',
        'vehicle_color_id',
        'plate',
        'year',
        'engine_number',
        'chassis_number',
        'mileage',
        'status'
    ];

    public function model()
    {
        return $this->belongsTo(BrandModel::class, 'brand_model_id');
    }

    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function color()
    {
        return $this->belongsTo(VehicleColor::class, 'vehicle_color_id');
    }
}
