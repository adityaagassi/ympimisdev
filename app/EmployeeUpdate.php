<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeUpdate extends Model{

	protected $fillable = [
		'employee_id',
		'name',
		'nik',
		'npwp',
		'birth_place',
		'birth_date',
		'religion',
		'mariage_status',
		'address',
		'current_address',
		'telephone',
		'handphone',
		'email',
		'bpjskes',
		'faskes',
		'bpjstk',
		'f_ayah',
		'f_ibu',
		'f_saudara1',
		'f_saudara2',
		'f_saudara3',
		'f_saudara4',
		'f_saudara5',
		'f_saudara6',
		'f_saudara7',
		'f_saudara8',
		'f_saudara9',
		'f_saudara10',
		'f_saudara11',
		'f_saudara12',
		'm_pasangan',
		'm_anak1',
		'm_anak2',
		'm_anak3',
		'm_anak4',
		'm_anak5',
		'm_anak6',
		'm_anak7',
		'sd',
		'smp',
		'sma',
		's1',
		's2',
		's3',
		'emergency1',
		'emergency2',
		'emergency3',
		'attachment',
		'count_attachment',
		'created_by'
	];

}
