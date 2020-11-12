<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StocktakingErrorList extends Model{
    protected $fillable = [
		'file_name', 'location', 'store', 'sub_store', 'material_number', 'material_description', 'category', 'error_message', 'created_by' 
	];
}
