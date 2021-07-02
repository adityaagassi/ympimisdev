<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PchCatalog extends Model
{
	 Use SoftDeletes;

    protected $fillable = [
		'gmc','desc','tanggal','uom','supplier', 'nama_vendor','code_vendor', 'size','foto', 'created_by'
		];

		public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

}