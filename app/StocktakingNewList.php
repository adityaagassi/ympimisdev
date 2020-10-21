<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StocktakingNewList extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id_list','location','store','sub_store', 'material_number','category','print_status','quantity','inputed_by','remark','created_by' 
	];
}
