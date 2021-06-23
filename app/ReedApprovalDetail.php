<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedApprovalDetail extends Model{

	protected $fillable = [	
		'approval_id',
		'shot',
		'length',
		'diameter',
		'thickness',
		'weight',
		'remark',
		'created_by'
	];
}
