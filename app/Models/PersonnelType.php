<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD

class PersonnelType extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];
=======
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonnelType extends Model
{
    protected $fillable = ['name', 'description'];

    public function personnels(): HasMany
    {
        return $this->hasMany(Personnel::class);
    }
>>>>>>> 76a4a5d0d367ab21a6f6c631fa175b4609069bb5
}
