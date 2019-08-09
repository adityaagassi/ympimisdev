<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use SoftDeletes;
    //
	protected $fillable = [
		'id', 'cost_center', 'cost_center_name', 'section', 'sub_sec', 'group', 'created_by'
	];
}
