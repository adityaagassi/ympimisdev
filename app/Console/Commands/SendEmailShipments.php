<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class SendEmailShipments extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'email:shipment';

/**
* The console command description.
*
* @var string
*/
protected $description = 'Command description';

/**
* Create a new command instance.
*
* @return void
*/
public function __construct()
{
	parent::__construct();
}

/**
* Execute the console command.
*
* @return mixed
*/
public function handle(){
	$mail_to = db::table('send_emails')
	->where('remark', '=', 'shipment')
	->WhereNull('deleted_at')
	->orWhere('remark', '=', 'superman')
	->WhereNull('deleted_at')
	->select('email')
	->get();

	$st_date = date('Y-m-d', strtotime(Carbon::now()->addDays(2)));

	$query = "select a.hpl, a.st_date, a.material_number, a.material_description, a.destination_shortname, a.plan, coalesce(b.actual,0) as actual, coalesce(b.actual,0)-a.plan as diff from
	(
	select materials.category, shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, materials.hpl, shipment_schedules.destination_code, destinations.destination_shortname, sum(shipment_schedules.quantity) as plan from shipment_schedules
	left join materials on materials.material_number = shipment_schedules.material_number
	left join destinations on destinations.destination_code = shipment_schedules.destination_code
	where materials.category = 'FG' and shipment_schedules.st_date = '" .$st_date . "'
	group by shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname, materials.hpl, materials.category
	) as a
	left join
	(
	select shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code, sum(flos.actual) as actual from flos
	left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id
	group by shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code
	) as b 
	on a.st_date = b.st_date and a.material_number = b.material_number and a.destination_code = b.destination_code
	having diff > 0 order by hpl asc, diff asc";

	$data = db::select($query);

	if(count($data) > 0){
		Mail::to($mail_to)->send(new SendEmail($data, 'shipment'));
	}
}
}