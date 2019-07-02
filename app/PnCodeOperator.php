<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PnCodeOperator extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'kode','nik','bagian','remark','id_number','created_by'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
