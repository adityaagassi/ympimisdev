<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloDetail extends Model
{
	use SoftDeletes;
    //
    protected $fillable = [
        'serial_number', 'flo_number', 'quantity', 'created_by'
    ];

     public function user()
    {
    	return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

    public function shipmentschedule()
    {
    	return $this->belongsTo('App\ShipmentSchedule', 'flo_number')->withTrashed();
    }
    //
}
