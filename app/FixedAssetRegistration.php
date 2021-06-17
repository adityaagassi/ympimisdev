<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetRegistration extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_number','asset_name', 'invoice_number', 'invoice_name', 'clasification_id', 'vendor', 'currency', 'amount', 'amount_usd', 'pic', 'location', 'investment_number', 'budget_number', 'usage_term', 'usage_estimation', 'invoice_file', 'status', 'request_date', 'category_code', 'category', 'sap_id', 'depreciation_key', 'remark', 'manager_app', 'manager_app_date', 'manager_fa_date', 'update_fa_at', 'created_by'
	];
}
