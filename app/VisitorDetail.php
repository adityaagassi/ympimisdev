<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VisitorDetail extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'id_visitor','id_number','full_name','in_time','out_time','status','tag','remark','telp'
	];
}

