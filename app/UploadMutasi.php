<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadMutasi extends Model
{
    protected $table = "upload_mutasi";

    protected $fillable = [
    	'nik', 'position_code', 'created_at'
	];
}
