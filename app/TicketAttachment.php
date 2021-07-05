<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketAttachment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'ticket_id', 'file_name', 'remark', 'created_by', 'file_extension'
	];
}
