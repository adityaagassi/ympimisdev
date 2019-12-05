<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditProcess extends Model
{
    use SoftDeletes;

    protected $table = 'audit_processes';

	protected $fillable = [
		'activity_list_id','department','section', 'product', 'periode', 'date','week_name', 'proses','operator','auditor','cara_proses','kondisi_cara_proses','pemahaman','kondisi_pemahaman','keterangan','leader','foreman','created_by'
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
