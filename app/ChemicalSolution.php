<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChemicalSolution extends Model{
    
     protected $fillable = [
		'solution_name', 'cost_center_id', 'category', 'note', 'is_add_schedule', 'target_uom', 'target_warning', 'target_max', 'actual_quantity', 'created_by'
	];

}
