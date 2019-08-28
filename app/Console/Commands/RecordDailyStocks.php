<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\DailyStock;
use App\ErrorLog;

class RecordDailyStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:daily_stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record daily stock from KITTO';

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
    public function handle()
    {
        $query = "select material_number, issue_location, sum(lot) as quantity from kitto.inventories group by material_number, issue_location";

        $inventories = db::select($query);

        foreach ($inventories as $inventory) {
            $data = [
                'material_number' => $inventory->material_number,
                'location' => $inventory->issue_location,
                'quantity' => $inventory->quantity,
                'created_by' => 1
            ];
            try{
                $daily_stock = new DailyStock($data);
                $daily_stock->save(); 
            }
            catch(\Exception $e){
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id
                ]);
                $error_log->save();                
            }
        }
    }
}
