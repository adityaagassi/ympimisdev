<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SkillUnfulfilledLog;
use Illuminate\Support\Facades\DB;

class SkillUnfulfilledLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skill:unfulfilled_log';

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
        $loc = DB::SELECT("SELECT DISTINCT(location) from skills");
        foreach ($loc as $key) {
            $emp = DB::SELECT("SELECT * FROM skill_employees where location = '".$key->location."'");
            if (count($emp) > 0) {
                foreach ($emp as $val) {
                    $skills = DB::SELECT("SELECT
                        skills2.skill_code,
                        skills2.skill,
                        skills2.process,
                        skills2.location,
                        skills2.nilai,
                        COALESCE(skills2.skill_now,skills2.skill) as skill_now,
                        COALESCE(skills2.nilai_now,0) as nilai_now,
                        COALESCE(skills2.id_skill_now,0) as id_skill_now 
                    FROM
                        (
                        SELECT
                            skill_code,
                            skill,
                            process,
                            location,
                        value
                            AS nilai,(
                            SELECT
                                skill 
                            FROM
                                skill_maps 
                            WHERE
                                skill_code = skills.skill_code 
                                AND employee_id = '".$val->employee_id."' 
                                AND skill_maps.deleted_at IS NULL 
                                ) AS skill_now,(
                            SELECT 
                            value
                            FROM
                                skill_maps 
                            WHERE
                                skill_code = skills.skill_code 
                                AND employee_id = '".$val->employee_id."' 
                                AND skill_maps.deleted_at IS NULL 
                                ) AS nilai_now,(
                            SELECT
                                id 
                            FROM
                                skill_maps 
                            WHERE
                                skill_code = skills.skill_code 
                                AND employee_id = '".$val->employee_id."' 
                                AND skill_maps.deleted_at IS NULL 
                            ) AS id_skill_now 
                        FROM
                            skills 
                        WHERE
                            skills.location = '".$key->location."' 
                            AND skills.deleted_at IS NULL 
                        ) skills2
                    WHERE
                        (skills2.nilai_now IS NULL 
                        and skills2.process = (SELECT process from skill_employees where employee_id = '".$val->employee_id."' and location = '".$key->location."'))
                        OR (skills2.nilai_now < skills2.nilai
                        and skills2.process = (SELECT process from skill_employees where employee_id = '".$val->employee_id."' and location = '".$key->location."'))");

                    if (count($skills) > 0) {
                        foreach ($skills as $vul) {
                            if ($vul->nilai_now == 0) {
                                $remark = 'Tidak Ada Skill';
                            }else{
                                $remark = 'Nilai Skill Kurang';
                            }
                            SkillUnfulfilledLog::create([
                                'employee_id' => $val->employee_id,
                                'process' => $vul->process,
                                'location' => $vul->location,
                                'skill_code' => $vul->skill_code,
                                'value' => $vul->nilai_now,
                                'required' => 3,
                                'remark' => $remark,
                                'created_by' => 1
                            ]);
                        }
                    }
                }
            }
        }
    }
}
