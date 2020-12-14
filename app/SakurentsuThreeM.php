<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeM extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'sakurentsu_number', 'form_number', 'date', 'title', 'product_name', 'process_name', 'unit', 'category', 'reason', 'benefit', 'check_before', 'started_date', 'special_items', 'bom_change', 'related_department', 'att', 'remark', 'created_by'
	];
}
