<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChemicalControlLog extends Model{
    
    protected $fillable = [
		'date', 'solution_name', 'cost_center_id', 'target_max', 'target_warning', 'note', 'quantity', 'accumulative', 'created_by'
	];

}
