<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewDetail extends Model
{
    use SoftDeletes;

    protected $table = 'interview_details';

	protected $fillable = [
        'interview_id', 'nik','filosofi_yamaha','aturan_k3','komitmen_berkendara','kebijakan_mutu','dasar_tindakan_bekerja','enam_pasal_keselamatan','budaya_kerja','budaya_5s','komitmen_hotel_konsep','janji_tindakan_dasar', 'created_by'
    ];
    
    public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function participants()
    {
        return $this->belongsTo('App\Employee', 'nik', 'employee_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
