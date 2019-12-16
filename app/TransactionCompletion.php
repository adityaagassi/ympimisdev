<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionCompletion extends Model{

	protected $fillable = [
		'serial_number', 'material_number', 'issue_plant', 'issue_location', 'reference_number', 'movement_type', 'quantity', 'reference_file', 'created_by'
	];
}
