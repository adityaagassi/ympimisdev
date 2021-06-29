<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangPekerjaan extends Model
{
    protected $fillable = [
        'department', 'seksi', 'bulan', 'employee', 'in_out', 'tanggal', 'keterangan', 'created_by', 'created_at', 'updated_at', 'deleted_at'
    ];
}
