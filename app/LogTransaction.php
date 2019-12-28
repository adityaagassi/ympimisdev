<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogTransaction extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'material_number', 'issue_plant', 'issue_storage_location', 'receive_plant', 'receive_storage_location', 'issue_plant', 'cost_center', 'gl_account', 'transaction_code', 'mvt', 'reason_code', 'reference_number', 'transaction_date', 'qty', 'created_by', 'reference_file'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
    //
}