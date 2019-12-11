<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RfidBuffingDataLog extends Model
{
    protected $connection = 'digital_kanban';
	protected $table = 'data_log';

	protected $fillable = [
		'dev_ip_address', 'operator_id', 'material_number', 'akan_start_time', 'sedang_start_time', 'selesai_start_time', 'status', 'material_qty', 'material_tag_id', 'rack', 'check', 'check_time'
	];

}
