<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMDocument extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'sakurentsu_number', 'form_id', 'document_name', 'document_description','target_date','finish_date','pic','remark', 'file_name','created_by'
	];
}
