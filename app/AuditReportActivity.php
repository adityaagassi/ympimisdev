<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditReportActivity extends Model
{
    use SoftDeletes;

    protected $table = 'audit_report_activities';

	protected $fillable = [
        'activity_list_id', 'department', 'section','subsection', 'date', 'nama_dokumen', 'no_dokumen','kesesuaian_aktual_proses','tindakan_perbaikan','target','kelengkapan_point_safety','kesesuaian_qc_kouteihyo','operator', 'leader','foreman', 'created_by'
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
