<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kaizenNote extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'id_kaizen', 'foreman_note', 'manager_note', 'created_by'
	];
}
