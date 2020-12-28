<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RcKensa extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'serial_number',
		'operator_kensa',
		'tag',
		'product',
		'material_number',
		'cavity',
		'start_time',
		'end_time',
		'ng_name',
		'ng_count',
		'status',
		'remark',
		'created_by'];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
