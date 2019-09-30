<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RfidLogEfficiency extends Model
{
	protected $connection = 'digital_kanban';
	protected $table = 'log_efficiencies';

	protected $fillable = [
		'operator_id', 'status', 'grup', 'time_filled', 'remark', 'created_at', 'updated_at'
	];
}
