<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetInvoice extends Model
{
	Use SoftDeletes;
    
    protected $fillable = [
		'form_id', 'investment_number', 'invoice_number', 'invoice_name', 'invoice_name', 'fixed_asset_name', 'att', 'remark', 'status', 'created_by'
	];
}
