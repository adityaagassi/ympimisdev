<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiddleMaterialRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'material_number', 'quantity', 'item','created_by', 'created_at', 'updated_at', 'deleted_at'
	];
}
