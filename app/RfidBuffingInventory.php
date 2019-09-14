<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class RfidBuffingInventory extends Model
{
	// use SoftDeletes;
	protected $connection = 'digital_kanban';
	protected $table = 'buffing_inventories';

	protected $fillable = [
		'material_number', 'operator_id', 'lokasi', 'material_qty', 'material_tg_id', 'created_by', 'updated_at'
	];

}
