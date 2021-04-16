<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpKanagata extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'material_number','material_name','material_description','process','process_1','process_2','process_3','process_4','process_5','product','part','punch_die_number','using','spare','need_kanban','remark','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
