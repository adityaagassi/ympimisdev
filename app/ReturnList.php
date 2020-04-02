<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnList extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'material_number', 'issue_location', 'receive_location', 'created_by', 'material_description', 'quantity'
	];
}
