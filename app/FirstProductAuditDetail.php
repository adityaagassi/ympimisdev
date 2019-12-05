<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirstProductAuditDetail extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'activity_list_id','first_product_audit_id','date','no_seri', 'judgement','pic', 'leader', 'foreman', 'created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function first_product_audit()
    {
        return $this->belongsTo('App\FirstProductAudit', 'first_product_audit_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
