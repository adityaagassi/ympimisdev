<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaMaterial extends Model
{
    protected $fillable = [
		'material_number', 'material_description','vendor','vendor_shortname','created_by'
	];
}
