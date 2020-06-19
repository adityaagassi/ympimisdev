<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assembly extends Model
{
    protected $fillable = [
		'ip_address', 'location', 'location_number', 'origin_group_code','port','online_time','operator_id','sedang_tag','sedang_serial_number','sedang_model','sedang_time','allowance','remark','created_by'
	];
}
