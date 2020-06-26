<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccSupplier extends Model
{
    protected $fillable = [
		'vendor_code','supplier_name', 'supplier_address', 'supplier_city', 'supplier_phone', 'supplier_fax', 'contact_name','supplier_npwp','supplier_duration','position','supplier_status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
