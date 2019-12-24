<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JishuHozenPoint extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_list_id', 'nama_pengecekan','leader','foreman','created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
