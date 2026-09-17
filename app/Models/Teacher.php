<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\TeacherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([TeacherObserver::class])]
class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
        'attachment',
    ];

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }
}