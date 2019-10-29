<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingReport extends Model
{
    use SoftDeletes;

    protected $table = 'training_reports';

	protected $fillable = [
        'activity_list_id', 'department', 'section', 'product', 'periode', 'date', 'time', 'trainer', 'theme'
        ,'isi_training','tujuan','standard','leader','foreman','notes','created_by'
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
