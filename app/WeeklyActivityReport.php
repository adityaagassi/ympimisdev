<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyActivityReport extends Model
{
    use SoftDeletes;

    protected $table = 'weekly_activity_reports';

	protected $fillable = [
        'activity_list_id', 'department','subsection', 'date', 'week_name', 'report_type','action','problem', 'foto_aktual', 'leader','foreman', 'created_by'
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
