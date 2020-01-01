<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WorkshopJobOrder extends Model{

	use SoftDeletes;
	protected $fillable = [
		'order_no', 'sub_section', 'priority', 'type', 'item_number', 'item_name', 'quantity', 'request_date', 'material', 'problem_description', 'remark', 'attachment', 'target_date', 'finish_date', 'difficulty', 'main_process', 'rating', 'note', 'drawing_number', 'approved_by', 'operator', 'created_by', 
	];

}
