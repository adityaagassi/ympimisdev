<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditGuidance extends Model
{
    use SoftDeletes;

    protected $table = 'audit_guidances';

	protected $fillable = [
		'activity_list_id','nama_dokumen','no_dokumen', 'date', 'month','periode', 'status','leader', 'foreman','created_by'
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
