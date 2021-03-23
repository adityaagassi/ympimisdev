<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialInOut extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'movement_type', 'issue_location', 'receive_location', 'quantity', 'entry_date', 'posting_date', 'created_by'
	];
}
