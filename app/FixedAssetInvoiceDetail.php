<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetInvoiceDetail extends Model
{
	Use SoftDeletes;
    
    protected $fillable = [
    	'invoice_id', 'vendor', 'currency', 'amount', 'amount_usd', 'remark', 'created_by'
    ];
}
