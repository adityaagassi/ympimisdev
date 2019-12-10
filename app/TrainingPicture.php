<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingPicture extends Model
{
    use SoftDeletes;

    protected $table = 'training_pictures';

	protected $fillable = [
        'training_id', 'picture','extension','created_by'
    ];
    
    public function training_reports()
    {
        return $this->belongsTo('App\TrainingReport', 'training_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
