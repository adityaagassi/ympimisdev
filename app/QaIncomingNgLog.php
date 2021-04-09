<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaIncomingNgLog extends Model
{
    protected $fillable = [
		'incoming_check_code','lot_number','location','inspector_id','material_number', 'material_description','vendor','qty_rec','qty_check','qty_ng','invoice','inspection_level','ng_name','status_ng','note_ng','created_by'
	];
}
