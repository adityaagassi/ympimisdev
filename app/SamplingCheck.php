<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SamplingCheck extends Model
{
    use SoftDeletes;

    protected $table = 'sampling_checks';

	protected $fillable = [
        'activity_list_id', 'department', 'section','subsection', 'month', 'date', 'product', 'no_seri_part', 'jumlah_cek', 'leader', 'created_by'
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
