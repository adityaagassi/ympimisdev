<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoldingInjectionLog extends Model
{
	use SoftDeletes;

    protected $fillable = [
		'rfid','mesin', 'part', 'color','start_time','end_time','running_shot','total_running_shot','ng_name','ng_count','status','status_maintenance','created_by'
	];
}
