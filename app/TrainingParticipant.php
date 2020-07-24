<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingParticipant extends Model
{
    use SoftDeletes;

    protected $table = 'training_participants';

	protected $fillable = [
        'training_id', 'participant_id','file','created_by'
    ];
    
    public function training_reports()
    {
        return $this->belongsTo('App\TrainingReport', 'training_id', 'id')->withTrashed();
    }

    public function participant_name()
    {
        return $this->belongsTo('App\EmployeeSync', 'participant_id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
