<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GeneralShoesStock extends Model{

	use SoftDeletes;

    protected $fillable = [
		'gender', 'size', 'temp_stock', 'quantity', 'created_by'
	];

}
