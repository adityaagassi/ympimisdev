<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloDetail extends Model
{
	use SoftDeletes;
    //
    protected $fillable = [
        'serial_number', 'material_number', 'origin_group_code', 'flo_number', 'quantity', 'created_by'
    ];

     public function user()
    {
    	return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

    public function shipmentschedule()
    {
    	return $this->belongsTo('App\ShipmentSchedule', 'flo_number')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
    }

    public function volume()
    {
        return $this->belongsTo('App\MaterialVolume', 'material_number', 'material_number')->withTrashed();
    }
    //
}
