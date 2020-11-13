<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EfficiencyUpload extends Model{
    protected $fillable = [
		'cost_center_name', 'total_date', 'total_input', 'total_output', 'remark', 'created_by'
	];

}
