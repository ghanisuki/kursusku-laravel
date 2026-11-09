<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'fee', 'quota', 'start_date', 'status'];

    protected function casts(): array
    {
        return ['start_date' => 'date'];
    }
}
