<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangSimpati extends Model
{
    protected $fillable = [
        'employee', 'sub_group', 'group', 'seksi', 'department', 'jabatan', 'permohonan', 'lampiran','created_by', 'created_at', 'updated_at', 'deleted_at'
    ];
}
