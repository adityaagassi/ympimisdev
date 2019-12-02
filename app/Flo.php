<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flo extends Model
{
	use SoftDeletes;
    //
    protected $fillable = [
        'flo_number', 'invoice_number', 'container_id', 'bl_date', 'shipment_schedule_id', 'material_number', 'quantity', 'actual', 'status', 'created_by', 'destination_code'
    ];
    
    public function user()
    {
    	return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

    public function shipmentschedule()
    {
    	return $this->belongsTo('App\ShipmentSchedule', 'shipment_schedule_id')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo('App\Status', 'status', 'status_code')->withTrashed();
    }
}
