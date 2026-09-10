<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'specialization'];

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }
}
