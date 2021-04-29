<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScrapLog extends Model
{
    use SoftDeletes;
	protected $fillable = [
		'scrap_id',
		'slip',
		'material_number',
		'material_description',
		'spt',
		'valcl',
		'category',
		'issue_location',
		'receive_location',
		'remark',
		'quantity',
		'category_reason',
		'reason',
		'summary',
		'scraped_by',
		'slip_created',
		'created_by',
		'created_at', 
		'deleted_at',	
		'updated_at'
	];
}
