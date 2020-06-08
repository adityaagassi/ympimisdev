<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyDetail extends Model
{
	protected $fillable = [
		'tag', 'serial_number', 'model', 'location','operator_id','sedang_start_date','sedang_finish_date','origin_group_code','created_by', 'is_send_log'
	];
}
