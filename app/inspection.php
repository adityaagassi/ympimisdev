<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Inspection extends Model
{
	use SoftDeletes;
    protected $fillable = [
		'id_checksheet','inspection1','inspection2','inspection3', 'inspection4', 'inspection5', 'inspection6', 'inspection7', 'inspection8', 'inspection9', 'remark1', 'remark2', 'remark3', 'remark4', 'remark5','remark6','remark7','remark8','remark9','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
