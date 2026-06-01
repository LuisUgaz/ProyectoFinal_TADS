<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonnelType extends Model
{
    protected $fillable = ['name', 'description'];

    public function personnels(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }
}
