<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyCheck extends Model
{
    use SoftDeletes;

    protected $table = 'daily_checks';

	protected $fillable = [
        'activity_list_id', 'department', 'product', 'production_date','check_date', 'serial_number', 'condition', 'keterangan','leader','foreman','created_by','created_by'
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
