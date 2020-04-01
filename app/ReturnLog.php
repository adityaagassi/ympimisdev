<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnLog extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'return_id', 'material_number', 'issue_location', 'receive_location', 'created_by', 'material_description', 'quantity', 'returned_by', 'remark', 'slip_created'
	];
}
