<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditKanban extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_list_id',
		'point_check_id',
		'department',
		'point_check',
		'check_date',
		'condition',
		'leader',
		'foreman',
		'send_status',
		'send_date',
		'approval_leader',
		'approval_date_leader',
		'approval',
		'approval_date',
		'created_by',

	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
