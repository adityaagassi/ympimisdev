<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyNgTemp extends Model
{
	protected $fillable = [
		'employee_id', 'tag', 'serial_number', 'model', 'location', 'ng_name','ongko','value_atas','value_bawah','value_lokasi','remark','operator_id','origin_group_code','created_by', 'remark', 'started_at'
	];
}
