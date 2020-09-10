<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\IndirectMaterialCostCenter;
use App\IndirectMaterialLog;
use App\IndirectMaterialOut;
use App\IndirectMaterialSchedule;
use App\ChemicalSolution;
use App\ChemicalSolutionComposer;
use App\ChemicalControlLog;
use App\WeeklyCalendar;

class SchedulingChemical extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduling:chemical';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $now = date('Y-m-d');

        $calendar = WeeklyCalendar::where('week_date', $now)
        ->where('remark', '<>', 'H')
        ->first();

        if($calendar){

            $pelayanan = ['07:30:00', '13:30:00', '16:30:00', '21:30:00'];
            $shift = [1, 1, 2, 2];
            // $pelayanan = '10:30:00';

            $material = ChemicalSolution::where('category', 'SCHEDULLING')->get();

            // dd(count($material));

            for ($i=0; $i < count($material); $i++) {
                $chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
                ->where('solution_id', $material[$i]->id)
                ->select(
                    'chemical_solution_composers.solution_name',
                    'chemical_solution_composers.solution_id',
                    'chemical_solution_composers.material_number',
                    'chemical_solution_composers.storage_location',
                    'chemical_solution_composers.quantity',
                    'chemical_solution_composers.bun',
                    'chemical_solutions.cost_center_id'
                )
                ->get();

                // dd(count($chm_composer));

                for ($j=0; $j < count($chm_composer); $j++) {

                    $schedule = IndirectMaterialSchedule::where(db::raw('date_format(indirect_material_schedules.schedule_date,"%Y-%m-%d")'), $now)
                    ->where('solution_id', $chm_composer[$j]->solution_id)
                    ->where('material_number', $chm_composer[$j]->material_number)
                    ->where('category', 'Pembuatan Baru')
                    ->get();

                    if($material[$i]->note > count($schedule)){
                        try {
                            $schedule = new IndirectMaterialSchedule([
                                'schedule_date' => date('Y-m-d H:i:s', strtotime($now.' '.$pelayanan[count($schedule)])),
                                'schedule_shift' => $shift[count($schedule)],
                                'category' => 'Pembuatan Baru',
                                'solution_id' => $chm_composer[$j]->solution_id,
                                'material_number' => $chm_composer[$j]->material_number,
                                'cost_center_id' => $chm_composer[$j]->cost_center_id,
                                'storage_location' => $chm_composer[$j]->storage_location,
                                'quantity' => $chm_composer[$j]->quantity,
                                'bun' => $chm_composer[$j]->bun,
                                'created_by' => 1
                            ]);
                            $schedule->save();

                        } catch (Exception $e) {
                            $error_log = new ErrorLog([
                                'error_message' => $e->getMessage(),
                                'created_by' => 1
                            ]);
                            $error_log->save();
                        }
                    }
                }
            } 
        }
    }
}

