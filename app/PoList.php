<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoList extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'porg', 'pgr', 'vendor', 'name', 'country', 'material', 'description', 'plnt', 'sloc', 'sc_vendor', 'cost_ctr', 'purchdoc', 'item', 'acctassigcat', 'order_date', 'deliv_date', 'order_qty', 'deliv_qty', 'base_unit_of_measure', 'price', 'curr', 'order_no', 'reply_date', 'create_date', 'delay', 'reply_qty', 'comment', 'del', 'incomplete', 'compl', 'ctr', 'spt', 'stock', 'lt', 'dsf', 'die_end', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo(App\User, created_by)->withTrashed();
	}
}