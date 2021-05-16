<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GreatdayAttendance extends Model
{
    protected $fillable = [
		'employee_id', 'name', 'task','date_in','department','section','group','latitude','longitude','mock','time_in','village','state_district','state', 'created_by', 
	];
}
