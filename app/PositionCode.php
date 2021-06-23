<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionCode extends Model
{
    protected $table = "position_code";

    protected $fillable = [
    	'position_code', 'division', 'department', 'section', 'group', 'sub_group', 'position'
	];
}
