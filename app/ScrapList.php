<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapList extends Model
{
	protected $fillable = [
		'slip', 'material_number', 'material_description', 'spt', 'valcl', 'category', 'issue_location', 'receive_location', 'remark', 'quantity', 'created_by', 'created_at', 'reason', 'summary', 'category_reason'
	];
}
