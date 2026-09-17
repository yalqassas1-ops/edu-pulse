<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
    ];

    /**
     * العلاقة مع الشُعب الدراسية
     */
    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class, 'class_room_id');
    }
}