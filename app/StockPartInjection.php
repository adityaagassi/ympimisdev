<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockPartInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'gmc', 'part', 'stock_awal', 'stock_akhir', 'created_by'
	];
}
