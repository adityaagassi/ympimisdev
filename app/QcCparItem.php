<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCparItem extends Model
{
	use SoftDeletes;

    protected $fillable = [
		'cpar_no','part_item','no_invoice','lot_qty','sample_qty','detail_problem','defect_qty','defect_presentase'
	];
}
