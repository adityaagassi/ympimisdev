<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataMaterials extends Model
{
    protected $fillable = [
        'id', 'store', 'category', 'material_number', 'material_description', 'ideal', 'actual', 'location', 'remark', 'print', 'status', 'quantity', 'created_by', 'created_at', 'deleted_at', 'updated_at'
    ];
}
