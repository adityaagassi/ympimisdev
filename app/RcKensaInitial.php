<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RcKensaInitial extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'tag', 'product','material_number','part_name','part_type','color','cavity','location','status','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
