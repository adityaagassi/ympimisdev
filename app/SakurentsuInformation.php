<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuInformation extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
		'sakurentsu_number', 'approver_id', 'approver_name', 'department','remark','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
