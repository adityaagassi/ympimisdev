<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PlanMesinInjection;
use App\PlanMesinInjectionTmp;
use App\StockPartInjection;
use App\TransactionPartInjection;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InjectionSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:injections';

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

      if(date('D')=='Thu' ){
        $day2 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
      }else if(date('D')=='Fri' ){
        $day2 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
      }else if(date('D')=='Sat' ){
        $day2 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
      }   
      else{
        $day2 = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
      }

      $yesterday = date('Y-m-d', strtotime(Carbon::yesterday()));
      $today = date('Y-m-d');



      $MESIN1 = [];
      $MESIN2 = [];
      $MESIN3 = [];
      $MESIN4 = [];
      $MESIN5 = [];
      $MESIN6 = [];
      $MESIN7 = [];
      $MESIN8 = [];
      $MESIN9 = [];
      $MESIN11 = [];
      $MESIN = [];


      $queryStock ="
      SELECT gmc,part,SUM(in_part) in_part ,SUM(out_part) out_part ,(SUM(in_part) - SUM(out_part)) as qty from (
      SELECT gmc, part,total as in_part, 0 as out_part from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='".$today."' and `status` ='IN'

      UNION ALL

      SELECT gmc, part,total as in_part, 0 as out_part from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='".$today."' AND DATE_FORMAT(created_at,'%H:%i:%s')<='08:00:00' and `status` ='IN'

      UNION ALL

      SELECT gmc, part,0 as in_part, total as out_part from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='".$today."' and `status` ='OUT'

      UNION ALL

      SELECT gmc, part,0 as in_part, total as out_part from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d')='".$today."' AND DATE_FORMAT(created_at,'%H:%i:%s')<='08:00:00' and `status` ='OUT'

      UNION all

      SELECT gmc,part,0 as in_part, 0 as out_part from detail_part_injections GROUP BY part, gmc
      ) a GROUP BY part, gmc
      ";


      $stock = DB::select($queryStock);


      foreach ($stock as $row) {
        $gmc = $row->gmc;
        $part = $row->part;
        $akhir = $row->qty;

        $StockPartInjection = new StockPartInjection([
          'gmc' => $gmc,
          'part' => $part, 
          'stock_akhir' =>  $akhir, 
          'created_by' => '123',
        ]);
        $StockPartInjection->save();

        $TransactionPartInjection = new TransactionPartInjection([
          'gmc' => $gmc,
          'part' => $part, 
          'total' =>  $akhir,
          'created_at' => $yesterday.' 08:08:08',
          'status' => 'IN',
          'created_by' => '123',
        ]);
        $TransactionPartInjection->save();
      }

      $query = "
      SELECT due_date, DATE_FORMAT(stock.created_at,'%Y-%m-%d') as injek, quantity, target.model,target.part,
      target.part_code,target.color,target,COALESCE(total,0) as stock, (target - COALESCE(total,0) ) as qty_injek, ROUND((82800  / cycle_time_mesin_injections.cycle  )*cycle_time_mesin_injections.shoot,0) as max_day, cycle, shoot, mesin.mesin, working from (
      SELECT target.*, (sum(target.quantity)*3) as target from (
      SELECT target.due_date,target.quantity, materials.model,detail_part_injections.part,detail_part_injections.part_code,
      detail_part_injections.color  from (
      SELECT material_number,due_date,quantity from production_schedules WHERE 
      material_number in (SELECT material_number from materials WHERE category ='fg' and hpl='RC') and 
      DATE_FORMAT(due_date,'%Y-%m-%d') ='".$day2."' 
      ) target
      LEFT JOIN materials on target.material_number = materials.material_number               
      CROSS join  detail_part_injections on materials.model = detail_part_injections.model 

      ) as target GROUP BY part, due_date ORDER BY due_date               
      ) target 

      LEFT JOIN (

      SELECT * from transaction_part_injections WHERE DATE_FORMAT(created_at,'%Y-%m-%d') in (
      SELECT MIN(week_date) week_date from (
      SELECT  week_date from ympimis.weekly_calendars WHERE 
      week_date not in ( SELECT tanggal from  ftm.kalender) 
      and week_date <'".$day2."' ORDER BY week_date desc limit 2
      ) a
      )
      ) as stock on target.part = stock.part

      LEFT JOIN (
      SELECT part,color, SUM(qty) as mesin, GROUP_CONCAT(working_mesin_injections.mesin) as working 
      from working_mesin_injections
      LEFT JOIN status_mesin_injections on working_mesin_injections.mesin = status_mesin_injections.mesin
      where status_mesin_injections.`status` !='OFF'
      GROUP BY part,color ORDER BY mesin
      ) as mesin on target.part_code = mesin.part and target.color = mesin.color



      LEFT JOIN cycle_time_mesin_injections 
      on target.part_code = cycle_time_mesin_injections.part 
      and target.color = cycle_time_mesin_injections.color

      WHERE (target - COALESCE(total,0) ) > 0 
      ";

      $tglQ = "
      SELECT  week_date from (
      SELECT  week_date from ympimis.weekly_calendars WHERE 
      week_date not in ( SELECT tanggal from  ftm.kalender) 
      and week_date <'".$day2."' ORDER BY week_date desc limit 2
      ) a ORDER BY week_date asc 
      ";


      $plan = DB::select($query);
      $tgl = DB::select($tglQ);
      $plan2 = count($plan);
      $tglAll = [];
      $all = 0;

      foreach ($tgl as $tgls ) {
        array_push($tglAll, $tgls->week_date);  
      }


   //      foreach ($plan as $row2) {
   //          if ( str_contains ($row2->working,',') ) {
   //           $m = explode(',', $row2->working);
   //           for ($y = 0; $y < count($m); $y++) {

   //              if ($m[$y] =="MESIN1") {
   //                  array_push($MESIN1, $row2->part.','.$row2->qty_injek.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$row2->injek.'#' );                  

   //              }

   //          }
   //      }else{
   //          $m = $row2->working;
   //          if ($m =="MESIN1") {
   //             array_push($MESIN1, $row2->part.','.$row2->qty_injek.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$row2->injek.'#' );                   
   //         }         
   //     }
   // }

      foreach ($plan as $row2) {
        if ( str_contains ($row2->working,',') ) {
         $m = explode(',', $row2->working);
         $qty2 = $row2->qty_injek;


         for ($y = 0; $y < count($m); $y++) {
          if ($all >= count($tglAll) ) {
            $all = count($tglAll) - 1;
          }

                // if ($all = 0) {
                //     $all = 0;
                // }

          if ($qty2 <= 0) {
           $qty2 = 0;
         }

         if ( $row2->max_day > $qty2   ) {
           array_push($MESIN, $m[$y].','.$row2->part.','.$qty2.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$all].','.'1211'.','.'#' );
           $qty2 -=  $row2->max_day; 
           if ($qty2 <= 0) {
             $qty2 = 0;
           }

         }else{

          $qty3 = $qty2;
          $i = 0;
          $mesin = count($m);

          while ( $qty3 > $row2->max_day) {
           if ($qty3 > $row2->max_day) {
            if ($i >= count($m)) {
              $i -= count($m);
            }



            for ($aa=0; $aa < $mesin ; $aa++) {
              if (($row2->max_day *  $mesin) >= $row2->qty_injek ) {
               array_push($MESIN, $m[$aa].','.$row2->part.','.$row2->max_day.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.$i.'922,'.'#' );
               $qty2 -=  $row2->max_day;
               $qty3 -=  $row2->max_day;
               break;
             } else{
              if ($row2->max_day > $qty3) {
                array_push($MESIN, $m[$aa].','.$row2->part.','.$qty3.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.$i.'9331,'.'#' );
                $qty2 -=  $row2->max_day;
                $qty3 -=  $row2->max_day;

              }else{
               array_push($MESIN, $m[$aa].','.$row2->part.','.$row2->max_day.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.$i.'933,'.'#' );
               $qty2 -=  $row2->max_day;
               $qty3 -=  $row2->max_day;
             }

           }

           if ($qty3 <= 0) {
             $qty3 = 0;

           }

         }

       }else{
        array_push($MESIN, $m[$y].','.$row2->part.','.$qty3.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.'21212121'.','.'#' );
      }
      $i++;
      $all = $i;
    }



  } 
}
}else{
  $m = $row2->working;
  $qty2 = $row2->qty_injek;
  if ($qty2 <= 0) {
   $qty2 = 0;
 }

 if ($all >= 1 ) {
  $all = 1;
}

    // array_push($MESIN, $row2->working.','.$row2->part.','.$row2->qty_injek.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$row2->injek.','.'111'.','.'#' ); 

if ( $row2->max_day > $qty2   ) {
  array_push($MESIN, $m.','.$row2->part.','.$qty2.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$all].','.'121122'.','.'#' );
  $qty2 -=  $row2->max_day; 
  if ($qty2 <= 0) {
   $qty2 = 0;
 }

}else{

  $qty3 = $qty2;
  $i = 0;
  $mesin = count($m);

  while ( $qty3 != 0  ) { 
    if ($i > 1) {
      $i = 1;
    }              

    if ($row2->max_day > $qty3) {
      array_push($MESIN, $m.','.$row2->part.','.$qty3.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.$i.'933122,'.'#' );
      $qty2 -=  $row2->max_day;
      $qty3 -=  $row2->max_day;

    }else{
     array_push($MESIN, $m.','.$row2->part.','.$row2->max_day.','.$row2->part_code.' - '.$row2->color.','.$row2->due_date.','.$tglAll[$i].','.$i.'93322,'.'#' );
     $qty2 -=  $row2->max_day;
     $qty3 -=  $row2->max_day;

   } 


   if ($qty3 <= 0) {
     $qty3 = 0;

   }        

   $i++;
   $all = $i;
 }
}                  

}
}

foreach ($MESIN as $row ) {
  $rows = explode("#", $row);
  $row3 = explode(",", $rows[0]);
  $plan_injections = new PlanMesinInjection([
    'mesin' => $row3[0],
    'part' => $row3[1], 
    'qty' =>  $row3[2], 
    'color' =>  $row3[3],
    'due_date' =>  $row3[4], 
    'working_date' =>  $row3[5],   
    'created_by' => $row3[6],
  ]);
  $plan_injections->save(); 
}

// foreach ($plan as $row) {
//     $mesin = $row->working;
//     $due_date = $row->due_date;            
//     $working_date = $row->injek;
//     $part = $row->part;
//     $qty_injek = $row->qty_injek;
//     $color = $row->part_code.' - '.$row->color;

//     $plan_injections = new PlanMesinInjection([
//         'mesin' => $mesin,
//         'part' => $part, 
//         'qty' =>  $qty_injek, 
//         'color' =>  $color,
//         'due_date' =>  $due_date, 
//         'working_date' =>  $working_date,   
//         'created_by' => $plan2,
//     ]);
//     $plan_injections->save();
// }

// echo $day2;
}
}
