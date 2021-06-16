<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogDataMaterials extends Model
{
    protected $fillable = [
        'id_before', 'store', 'category', 'material_number', 'material_description', 'ideal', 'actual', 'location', 'remark', 'print', 'status', 'quantity', 'created_by', 'created_at', 'deleted_at', 'updated_at'
    ];
}
