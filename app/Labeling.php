<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labeling extends Model
{
    use SoftDeletes;

    protected $table = 'labelings';

	protected $fillable = [
		'activity_list_id','department','section', 'product', 'periode', 'date', 'nama_mesin','foto_arah_putaran','foto_sisa_putaran','keterangan','leader','foreman','created_by'
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
