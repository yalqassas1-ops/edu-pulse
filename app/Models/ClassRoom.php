<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity'];

    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class, 'class_room_id');
    }
}