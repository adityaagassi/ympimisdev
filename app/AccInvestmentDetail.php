<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccInvestmentDetail extends Model
{
    protected $fillable = [
		'reff_number','no_item','detail','uom','qty','price','amount','dollar','vat_status','sudah_po','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function detail_inv()
    {
        return $this->belongsTo('App\AccInvestment', 'reff_number', 'reff_number')->withTrashed();
    }
}
