<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flo extends Model
{
	use SoftDeletes;
    //
    protected $fillable = [
        'flo_number', 'invoice_number', 'container_number', 'bl_date', 'shipment_schedule_id', 'quantity', 'actual', 'status', 'created_by'
    ];
    
    public function user()
    {
    	return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

    public function shipmentschedule()
    {
    	return $this->belongsTo('App\ShipmentSchedule', 'shipment_schedule_id')->withTrashed();
    }
    //
}
