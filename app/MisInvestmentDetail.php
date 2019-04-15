<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisInvestmentDetail extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'project', 'description', 'item_code', 'uom', 'qty', 'price', 'type', 'category', 'remark'
	];
}
