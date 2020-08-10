<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CostCenterHistory;
use Illuminate\Support\Facades\DB;

class CostCenterHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costcenter:history';

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
    public function handle()
    {
        $insert = array();
        $cost_center = db::connection('sunfish')->select("SELECT cost_center_name,cost_center_code,COUNT(cost_center_code) as jumlah FROM dbo.VIEW_YMPI_Emp_OrgUnit where cost_center_code != 'SunFishCC' and end_date is null GROUP BY cost_center_code,cost_center_name");
        $datas = json_decode(json_encode($cost_center), true);

        foreach ($datas as $data) {
            CostCenterHistory::create([
                'cost_center_code' => $data['cost_center_code'],
                'cost_center_name' => $data['cost_center_name'],
                'count' => $data['jumlah'],
                'created_by' => 1
            ]);
        }
    }
}
