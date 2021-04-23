<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SapTransactions extends Model
{
    protected $fillable = [
    	'entry_date', 'posting_date', 'movement_type', 'material_number', 'quantity', 'storage_location', 'receive_location', 'reference', 'remark', 'created_by'
	];
}
