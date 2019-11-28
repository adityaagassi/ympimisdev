<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionAudit extends Model
{
    use SoftDeletes;

    protected $table = 'production_audits';

	protected $fillable = [
		'activity_list_id','point_check_audit_id', 'date', 'week_name', 'foto_kondisi_aktual', 'kondisi', 'pic','auditor','created_by'
	];

	public function point_check_audit()
    {
        return $this->belongsTo('App\PointCheckAudit', 'point_check_audit_id', 'id')->withTrashed();
    }

    public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function employee_pic()
    {
        return $this->belongsTo('App\Employee', 'pic', 'employee_id')->withTrashed();
    }

    public function employee_auditor()
    {
        return $this->belongsTo('App\Employee', 'auditor', 'employee_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
