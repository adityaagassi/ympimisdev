<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\Libraries\ActMLEasyIf;
use App\LogProcess;
use App\PlcCounter;
use App\CodeGenerator;
use App\StampInventory;
use App\StampSchedule;

class ProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexProcessAssyFL(){
		return view('processes.assy_fl.index', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL1(){
		return view('processes.assy_fl.stamp', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL2(){
		return view('processes.assy_fl.tanpoawase', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL3(){
		return view('processes.assy_fl.seasoning', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL4(){
		return view('processes.assy_fl.choseikanggo', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchProcessAssyFLDisplayStockChart(){
		$now = date('Y-m-d');

		$inventory = StampInventory::where('origin_group_code', '=', '041')
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 1)
		->select('model', db::raw('sum(quantity) as stock'))
		->groupBy('model')
		->get();

		$query2 = "SELECT hasil.model,sum(hasil.plan) as plan,sum(hasil.quantity) as quantity from (
		select model, 0 as plan, sum(quantity) as quantity from stamp_inventories where process_code='1'  GROUP BY model 
		union all
		select model, sum(plan) as plan, 0 as quantity from
        (
        select materials.model, sum(plan) as plan from
        (
        select material_number, quantity as plan
        from production_schedules 
       	union all
        select material_number, -(quantity) as plan
        from flo_details
        ) as plan
        left join materials on materials.material_number = plan.material_number
        group by materials.model
        union all
        select model, -(quantity) as plan
        from stamp_inventories
        ) as result
        group by model
        having plan > 0
       ) as hasil GROUP BY hasil.model HAVING hasil.model like 'YFL%'";

		$table = DB::select($query2);

		$response = array(
			'status' => true,
			'stockData' => $inventory,
			'stockTable' => $table,
		);
		return Response::json($response);
	}


	public function indexResumes(){
		return view('processes.assy_fl.resumes', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}
	
	public function indexDisplay(){
		return view('processes.assy_fl.display', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexDisplayWipFL(){
		
	}

	public function fetchProcessAssyFL2ActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '3' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);

		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '3' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL3ActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '4' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);

		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '4' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL4ActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '5' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);

		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '5' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLDisplayActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);

		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}
	public function fetchProcessAssyFL2StockChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');
		$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));

		$inventory = StampInventory::where('origin_group_code', '=', '041')
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 2)
		->select('model', db::raw('sum(quantity) as stock'))
		->groupBy('model')
		->get();

		$query2 = "SELECT hasil.model,sum(hasil.plan) as plan,sum(hasil.quantity) as quantity from (
		select model, 0 as plan, sum(quantity) as quantity from stamp_inventories where process_code='2'  GROUP BY model 
		union all
		select model, sum(plan) as plan, 0 as quantity from
        (
        select materials.model, sum(plan) as plan from
        (
        select material_number, quantity as plan
        from production_schedules 
       	union all
        select material_number, -(quantity) as plan
        from flo_details
        ) as plan
        left join materials on materials.material_number = plan.material_number
        group by materials.model
        union all
        select model, -(quantity) as plan
        from stamp_inventories
        ) as result
        group by model
        having plan > 0
       ) as hasil GROUP BY hasil.model HAVING hasil.model like 'YFL%'";

		$table = DB::select($query2);

		$response = array(
			'status' => true,
			'stockData' => $inventory,
			'stockTable' => $table,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL3StockChart(){
		$now = date('Y-m-d');

		$inventory = StampInventory::where('origin_group_code', '=', '041')
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 3)
		->select('model', db::raw('sum(quantity) as stock'))
		->groupBy('model')
		->get();

		$query2 = "SELECT hasil.model,sum(hasil.plan) as plan,sum(hasil.quantity) as quantity from (
		select model, 0 as plan, sum(quantity) as quantity from stamp_inventories where process_code='3'  GROUP BY model 
		union all
		select model, sum(plan) as plan, 0 as quantity from
        (
        select materials.model, sum(plan) as plan from
        (
        select material_number, quantity as plan
        from production_schedules 
       	union all
        select material_number, -(quantity) as plan
        from flo_details
        ) as plan
        left join materials on materials.material_number = plan.material_number
        group by materials.model
        union all
        select model, -(quantity) as plan
        from stamp_inventories
        ) as result
        group by model
        having plan > 0
       ) as hasil GROUP BY hasil.model HAVING hasil.model like 'YFL%'";

		$table = DB::select($query2);

		$response = array(
			'status' => true,
			'stockData' => $inventory,
			'stockTable' => $table,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL4StockChart(){
		$now = date('Y-m-d');

		$inventory = StampInventory::where('origin_group_code', '=', '041')
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 4)
		->select('model', db::raw('sum(quantity) as stock'))
		->groupBy('model')
		->get();

		$query2 = "SELECT hasil.model,sum(hasil.plan) as plan,sum(hasil.quantity) as quantity from (
		select model, 0 as plan, sum(quantity) as quantity from stamp_inventories where process_code='4'  GROUP BY model 
		union all
		select model, sum(plan) as plan, 0 as quantity from
        (
        select materials.model, sum(plan) as plan from
        (
        select material_number, quantity as plan
        from production_schedules 
       	union all
        select material_number, -(quantity) as plan
        from flo_details
        ) as plan
        left join materials on materials.material_number = plan.material_number
        group by materials.model
        union all
        select model, -(quantity) as plan
        from stamp_inventories
        ) as result
        group by model
        having plan > 0
       ) as hasil GROUP BY hasil.model HAVING hasil.model like 'YFL%'";

		$table = DB::select($query2);

		$response = array(
			'status' => true,
			'stockData' => $inventory,
			'stockTable' => $table,
		);
		return Response::json($response);
	}


	public function fetchProcessAssyFL3EfficiencyChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 3 and st_assemblies.process_code = 4 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 3

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 4
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date
		having std_time > 0 and actual_time > 0";

		$query2 = "select due_date, model, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 3 and st_assemblies.process_code = 4 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 3

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 4
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date, model
		having std_time > 0 and actual_time > 0";

		$efficiencyData = DB::select($query);
		$efficiencyTable = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'efficiencyTable' => $efficiencyTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL4EfficiencyChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 4 and st_assemblies.process_code = 5 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 4

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 5
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date
		having std_time > 0 and actual_time > 0";

		$query2 = "select due_date, model, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 4 and st_assemblies.process_code = 5 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 4

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 5
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date, model
		having std_time > 0 and actual_time > 0";

		$efficiencyData = DB::select($query);
		$efficiencyTable = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'efficiencyTable' => $efficiencyTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFL2EfficiencyChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 2 and st_assemblies.process_code = 3 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 2

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 3
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date
		having std_time > 0 and actual_time > 0";

		$query2 = "select due_date, model, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 2 and st_assemblies.process_code = 3 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 2

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 3
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date, model
		having std_time > 0 and actual_time > 0";

		$efficiencyData = DB::select($query);
		$efficiencyTable = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'efficiencyTable' => $efficiencyTable,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLDisplayEfficiencyChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 1 and st_assemblies.process_code = 2 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 1

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 2
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date
		having std_time > 0 and actual_time > 0";

		$query2 = "select due_date, model, sum(std_time) as std_time, sum(actual_time) as actual_time, sum(std_time)/sum(actual_time) as efficiency from
		(
		select date(log_processes.created_at) as due_date, log_processes.model, sum(log_processes.manpower)*st_assemblies.st as std_time, 0 as actual_time from log_processes left join st_assemblies on st_assemblies.model = log_processes.model where log_processes.process_code = 1 and st_assemblies.process_code = 2 group by log_processes.model, st_assemblies.st, date(log_processes.created_at)

		union all

		select due_date, model, 0 as std_time, sum(actual_time) as actual_time from
		(
		select date(max(start_time)) as due_date, model, quantity*if(timestampdiff(minute, max(start_time), max(end_time))>480, 0, timestampdiff(minute, max(start_time), max(end_time))) as actual_time from
		(
		select log_processes.serial_number, log_processes.model, log_processes.quantity, log_processes.created_at as start_time, '0000-00-00 00:00:00' as end_time from log_processes where log_processes.process_code = 1

		union all

		select log_processes.serial_number, log_processes.model, log_processes.quantity, '0000-00-00 00:00:00' as start_time, log_processes.created_at as end_time from log_processes where log_processes.process_code = 2
		) as result1
		group by serial_number, model, quantity
		) as result2
		group by due_date, model
		) as result3
		where due_date >= '".$first."' and due_date <= '".$now."'
		group by due_date, model
		having std_time > 0 and actual_time > 0";

		$efficiencyData = DB::select($query);
		$efficiencyTable = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'efficiencyTable' => $efficiencyTable,
		);
		return Response::json($response);
	}
	function inputProcessAssyFL2(Request $request){

		$stamp = LogProcess::where('serial_number', '=', $request->get('serialNumber'))
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 1)
		->first();

		if($stamp != ""){
			try{
				$id = Auth::id();

				$log_process = new LogProcess([
					'process_code' => $request->get('processCode'),
					'serial_number' => $request->get('serialNumber'),
					'model' => $stamp->model,
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id
				]);

				$inventory = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
				->where('model', 'like', 'YFL%')
				->where('process_code', '=', 1)
				->first();

				$inventory->process_code = $request->get('processCode');

				$inventory->save();
				$log_process->save();
				$stamp->forceDelete();

				$response = array(
					'status' => true,
					'message' => 'Input success',
				);
				return Response::json($response);
			}
			catch (QueryException $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Process invalid',
			);
			return Response::json($response);
		}
		
	}


	function inputProcessAssyFL3(Request $request){

		$stamp = LogProcess::where('serial_number', '=', $request->get('serialNumber'))
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 2)
		->first();

		if($stamp != ""){
			try{
				$id = Auth::id();

				$log_process = new LogProcess([
					'process_code' => $request->get('processCode'),
					'serial_number' => $request->get('serialNumber'),
					'model' => $stamp->model,
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id
				]);

				$inventory = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
				->where('model', 'like', 'YFL%')
				->where('process_code', '=', 2)
				->first();

				$inventory->process_code = $request->get('processCode');

				$inventory->save();
				$log_process->save();
				$stamp->forceDelete();

				$response = array(
					'status' => true,
					'message' => 'Input success',
				);
				return Response::json($response);
			}
			catch (QueryException $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Process invalid',
			);
			return Response::json($response);
		}
		
	}

	function inputProcessAssyFL4(Request $request){

		$stamp = LogProcess::where('serial_number', '=', $request->get('serialNumber'))
		->where('model', 'like', 'YFL%')
		->where('process_code', '=', 3)
		->first();

		if($stamp != ""){
			try{
				$id = Auth::id();

				$log_process = new LogProcess([
					'process_code' => $request->get('processCode'),
					'serial_number' => $request->get('serialNumber'),
					'model' => $stamp->model,
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id
				]);

				$inventory = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
				->where('model', 'like', 'YFL%')
				->where('process_code', '=', 3)
				->first();

				$inventory->process_code = $request->get('processCode');

				$inventory->save();
				$log_process->save();
				$stamp->forceDelete();

				$response = array(
					'status' => true,
					'message' => 'Input success',
				);
				return Response::json($response);
			}
			catch (QueryException $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Process invalid',
			);
			return Response::json($response);
		}
		
	}


	public function fetchStampPlan(){

		$now = date('Y-m-d');

		$query = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planData = DB::select($query);
		$materials = DB::table('materials')->where('model', 'like', 'YFL%')->select('model')->distinct()->get();

		$response = array(
			'status' => true,
			'planData' => $planData,
			'model' => $materials,
		);
		return Response::json($response);
	}

	public function fetchSerialNumber(Request $request){
		$code_generator = DB::table('code_generators')->where('note', '=', $request->get('originGroupCode'))->first();
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index);
		$number2 = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

		$lastCounter = $code_generator->prefix.$number;
		$nextCounter = $code_generator->prefix.$number2;

		$response = array(
			'status' => true,
			'lastCounter' => $lastCounter,
			'nextCounter' => $nextCounter,
		);
		return Response::json($response);
	}

	public function fetchResult(){
		$now = date('Y-m-d');
		$log_processes = db::table('log_processes')
		->where('process_code', '=', '1')
		->where('model', 'like', 'YFL%')
		->where(db::raw('date(created_at)'), '=', $now)
		->orderBy('created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'resultData' => $log_processes,
		);
		return Response::json($response);
	}

	public function adjust(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$prefix = $code_generator->prefix;
		$lastIndex = $code_generator->index;

		$response = array(
			'status' => true,
			'prefix' => $prefix,
			'lastIndex' => $lastIndex,
		);
		return Response::json($response);
	}

	public function adjustUpdate(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$code_generator->index = $request->get('lastIndex');
		$code_generator->prefix = $request->get('prefix');
		$code_generator->save();

		$response = array(
			'status' => true,
			'message' => 'Serial number adjustment success',
		);
		return Response::json($response);
	}

	public function adjustSerial(Request $request){
		if($request->get('adjust') == 'minus'){
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index-1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted minus',
			);
			return Response::json($response);
		}
		else{
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted plus',
			);
			return Response::json($response);
		}
	}

	public function editStamp(Request $request){
		$log_process = LogProcess::find($request->get('id'));

		$response = array(
			'status' => true,
			'logProcess' => $log_process,
		);
		return Response::json($response);
	}

	public function destroyStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model);

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model);

		$log_process->forceDelete();
		$stamp_inventory->forceDelete();

		$response = array(
			'status' => true,
			'message' => 'Delete Success',
		);
		return Response::json($response);
	}

	public function updateStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model)
		->first();
		$log_process->model = $request->get('model');

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model)
		->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

		$stamp_inventory->update(['model' => $request->get('model')]);
		$log_process->save();

		$response = array(
			'status' => true,
			'message' => 'Update Success',
		);
		return Response::json($response);
	}

	public function stamp(Request $request){
		try{
			$plc = new ActMLEasyIf(0);
			$datas = $plc->read_data('D0', 16);
			$data = $datas[0];
			$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();

			if($plc_counter->plc_counter <> $data){

				$id = Auth::id();

				$plc_counter->plc_counter = $data;

				$log_process = new LogProcess([
					'process_code' => $request->get('processCode'),
					'serial_number' => $request->get('serialNumber'),
					'model' => $request->get('model'),
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id
				]);

				$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
				$code_generator->index = $code_generator->index+1;

				if ($request->get('category')=='fg'){
					$stamp_inventory = new StampInventory([
						'origin_group_code' => $request->get('originGroupCode'),
						'model' => $request->get('model'),
						'quantity' => 1,
						'process_code' => $request->get('processCode'),
						'serial_number' => $request->get('serialNumber')
					]);
					$stamp_inventory->save();
				}
				
				$plc_counter->save();
				$code_generator->save();
				$log_process->save();

				$printer_name = 'SUPERMAN';

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setBarcodeWidth(2);
				$printer->setBarcodeHeight(64);
				$printer->barcode($request->get('serialNumber'), Printer::BARCODE_CODE39);
				// $printer->qrCode($request->get('serialNumber'));
				$printer->setTextSize(3, 1);
				$printer->text($request->get('serialNumber')."\n\n");
				$printer->feed(1);
				$printer->text($request->get('model')."\n\n");
				$printer->cut();
				$printer->close();

				$response = array(
					'status' => true,
					'statusCode' => 'stamp',
					'message' => 'Stamp success',
					'data' => $plc_counter->plc_counter
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => true,
					'statusCode' => 'noStamp',
				);
				return Response::json($response);
			}
		}
		catch (\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function filter_stamp_detail(Request $request){
		$flo_detailsTable = DB::table('log_processes')

		->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->get();

		return DataTables::of($stamp_detail)
		->addColumn('action', function($stamp_detail){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

}
