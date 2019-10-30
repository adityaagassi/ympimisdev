<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SamplingCheckDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sampling_check_details';

	protected $fillable = [
        'sampling_check_id', 'point_check', 'hasil_check','picture_check', 'pic_check', 'sampling_by', 'created_by'
    ];
    
    public function sampling_checks()
    {
        return $this->belongsTo('App\SamplingCheck', 'sampling_check_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
