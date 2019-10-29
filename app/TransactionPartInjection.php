<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPartInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'gmc', 'part', 'total', 'status', 'created_by'
	];
}
