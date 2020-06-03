<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblyNgLog extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'employee_id', 'tag', 'serial_number', 'model', 'location', 'ng_name','ongko','value_atas','value_bawah','remark','sedang_start_date','sedang_finish_date','operator_id','origin_group_code','created_by', 'remark', 'started_at'
	];
}
