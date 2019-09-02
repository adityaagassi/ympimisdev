<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class EmailConfirmationOvertimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:confirmation_overtime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email not confirmed overtime > x days';

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
        $date = carbon::now()->subDays(3);

        $query = "select a.section, count(b.id) as unconfirmed from
        ( SELECT
        over.tanggal,
        over.nik,
        karyawan.namaKaryawan as nama_karyawan,
        section.nama as section,
        over.jam,
        presensi.masuk,
        presensi.keluar
        FROM
        over
        LEFT JOIN posisi ON posisi.nik = over.nik
        LEFT JOIN karyawan on karyawan.nik = over.nik
        left join section on section.id = posisi.id_sec
        left join presensi on presensi.nik = over.nik and presensi.tanggal = over.tanggal
        WHERE
        status_final = 0
        ) AS a
        LEFT JOIN (
        SELECT
        over_time.id,
        over_time.tanggal,
        over_time_member.nik,
        sum( over_time_member.jam ) AS plan_ot,
        dari,
        sampai,
        over_time.hari
        FROM
        over_time
        LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
        WHERE
        over_time_member.nik IS NOT NULL 
        AND deleted_at IS NULL 
        AND over_time_member.STATUS = 0
        AND over_time_member.jam_aktual = 0
        GROUP BY
        over_time.id,
        over_time.tanggal,
        over_time.hari,
        over_time_member.nik,
        over_time_member.dari,
        over_time_member.sampai
        ) AS b ON a.nik = b.nik 
        AND a.tanggal = b.tanggal
        where b.id IS NOT NULL and date_format(a.tanggal,'%Y-%m-%d') < '".$date."' group by a.section order by a.tanggal asc, a.nik asc";

        $unconfirmed = db::connection('mysql3')->select($query);

        $section = "";
        for($x = 0; $x < count($unconfirmed); $x++) {
            $section = $section."'".$unconfirmed[$x]->section."'";
            if($x != count($unconfirmed)-1){
                $section = $section.",";
            }
        }

        $query2 = "SELECT email FROM users
        left join (
        select s.employee_id, position, section from
        (select employee_id, position from promotion_logs where position NOT IN ('Leader','Sub Leader','-') and valid_to is null) s
        left join (select employee_id, section from mutation_logs where valid_to is null) d on s.employee_id = d.employee_id
        where section in (".$section.")
        ) d on users.username = d.employee_id
        where position is not null";

        $mail_to = db::table('send_emails')
        ->where('remark', '=', 'overtime_confirmation')
        ->whereNull('deleted_at')
        ->orWhere('remark', '=', 'superman')
        ->whereNull('deleted_at')
        ->select('email')
        ->distinct()
        ->get();

        $mail_to_2 = db::select($query2);

        $mail = array();

        foreach ($mail_to as $row) {
            if(!in_array($row->email, $mail)){
                array_push($mail, $row->email);
            }
        }

        foreach ($mail_to_2 as $row) {
            if(!in_array($row->email, $mail)){
                array_push($mail, $row->email);
            }
        }

        $data = $unconfirmed;

        if(count($unconfirmed) > 0){
            Mail::to($mail)->send(new SendEmail($data, 'confirmation_overtime'));
        }
    }
}