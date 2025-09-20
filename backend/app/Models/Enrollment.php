<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id','course_id','progress_percentage',
        'enrollment_date','completion_date'
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function course()  { return $this->belongsTo(Course::class); }
}

