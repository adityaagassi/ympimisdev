<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewPicture extends Model
{
    use SoftDeletes;

    protected $table = 'interview_pictures';

	protected $fillable = [
        'interview_id', 'picture','extension','created_by'
    ];
    
    public function interview()
    {
        return $this->belongsTo('App\Interview', 'interview_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
