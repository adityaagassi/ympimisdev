<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCostdown extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'ticket_id', 'category', 'cost_name', 'cost_description', 'cost_amount', 'remark'
	];
}
