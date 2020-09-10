<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChemicalSolutionComposer extends Model{
    
     protected $fillable = [
		'solution_name', 'solution_id', 'material_number', 'material_description', 'storage_location', 'expired', 'quantity', 'bun', 'addition', 'created_by'
	];
}
