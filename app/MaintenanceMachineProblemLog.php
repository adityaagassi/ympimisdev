<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceMachineProblemLog extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'id','machine_id', 'machine_name', 'location', 'defect', 'handling', 'prevention', 'part', 'remark', 'started_time', 'finished_time', 'created_by'
	];
}
