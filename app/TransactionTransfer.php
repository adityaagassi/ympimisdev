<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTransfer extends Model
{
	protected $fillable = [
		'serial_number', 'plant', 'material_number', 'issue_plant', 'issue_location', 'receive_plant', 'receive_location', 'cost_center', 'gl_account', 'transaction_code', 'movement_type', 'reason_code', 'quantity', 'reference_file', 'created_by'
	];

}
