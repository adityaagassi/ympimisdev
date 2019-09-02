<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class EmailMiddleKanban extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:middle_kanban';

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
        ->where('remark', '=', 'middle')
        ->WhereNull('deleted_at')
        ->orWhere('remark', '=', 'superman')
        ->WhereNull('deleted_at')
        ->select('email')
        ->get();

        $queryKanban = "select middle_inventories.tag, middle_inventories.material_number, materials.model, materials.`key`, materials.surface, middle_inventories.quantity, middle_inventories.created_at, middle_inventories.remark, DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) AS diff
        from middle_inventories left join materials
        on middle_inventories.material_number = materials.material_number
        WHERE DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) > 4";

        $queryJml = "select count(middle_inventories.material_number) as jml
        from middle_inventories left join materials
        on middle_inventories.material_number = materials.material_number
        WHERE DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) > 4";

        $dataKanban = db::select($queryKanban);
        $dataJml = db::select($queryJml);

        $data = [
            'kanban' => $dataKanban,
            'jml' => $dataJml
        ];

        if(count($dataKanban) > 0){
            Mail::to($mail_to)->send(new SendEmail($data, 'middle_kanban'));
        }
    }
}
