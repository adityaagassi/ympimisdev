<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NgFinding extends Model
{
    use SoftDeletes;

    protected $table = 'ng_findings';

	protected $fillable = [
        'activity_list_id', 'department', 'date', 'material_number','quantity','finder', 'picture','defect','checked_qa', 'leader','foreman', 'created_by'
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
