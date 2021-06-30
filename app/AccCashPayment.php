<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccCashPayment extends Model
{
    protected $fillable = [
        'submission_date','category','remark','currency','amount','no_pr','file','pdf','posisi','status','manager','manager_name','status_manager','direktur','status_direktur','presdir','status_presdir','alasan','datereject','created_by','created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
