<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyInventory extends Model
{
    protected $fillable = [
		'tag', 'serial_number', 'model', 'location','location_next','remark','origin_group_code','created_by'
	];
}
