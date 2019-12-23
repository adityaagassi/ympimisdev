<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reedplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id', 'employee_id', 'major', 'minor'
    ];

}
