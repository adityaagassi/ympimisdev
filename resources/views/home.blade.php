@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
        margin-bottom: 5px;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        margin:0;
        padding:0;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(180,180,180);
        font-size: 12px;
        background-color: rgb(240,240,240);
        padding-top: 2px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 3px;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
    .marquee {
        width: 100%;
        overflow: hidden;
        margin: 0px;
        padding: 0px;
        text-align: center;
        height: 35px;
    }
</style>
@stop
@section('header')
<section class="content-header" style="padding: 0; margin:0;">
    <div class="marquee">
        <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span style="font-size:22px;"><b>A</b></span>cquisition of <span style="font-size:22px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 20px;" class="text-purple">
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
            製 造 の リ ア ル タ イ ム 情 報
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
        </span></b>
    </div>
</section>
@endsection

@section('content')

<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Production Support<br/>生産支援モニ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">HR Report (人事報告)</span>
                            <br>
                            <a href="{{ url("index/general/online_transportation") }}">
                                <i class="fa fa-caret-right"></i> Attendance & Transportation Report (出社・移動費のオンライン報告)
                            </a>
                            <br>
                            <a href="{{ url("index/general/surat_dokter") }}">
                                <i class="fa fa-caret-right"></i> Dropbox Surat Dokter (診断書のドロップボックス)
                            </a>
                            <br>
                            <a href="{{ url("index/general/agreement") }}">
                                <i class="fa fa-caret-right"></i> Company Agreement List (会社の契約書)
                            </a>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Overtime Information (残業の情報)</span>
                            <br>
                            <a href="{{ url("index/report/overtime_monthly_fq") }}">
                                <i class="fa fa-caret-right"></i> OT Monitor By CC - Forecast <br> (コストセンターによる残業管理)
                            </a>
                            <br>
                            <a href="{{ url("index/report/overtime_monthly_bdg") }}">
                                <i class="fa fa-caret-right"></i> OT Monitor By CC - Budget <br> (コストセンターによる残業管理)
                            </a>
                            {{--  <br>
                                <a href="http://172.17.128.4/myhris/management/overtime_control">
                                    <i class="fa fa-caret-right"></i> OT Monitor Daily (日次ざんぎぃう管理)
                                </a> --}}
                                <br>
                                <a href="{{ url("index/report/overtime_section")}}">
                                    <i class="fa fa-caret-right"></i> OT By CC (コストセンター別の残業)
                                </a>
                                <br>
                                <a href="{{ url("index/report/overtime_data") }}">
                                    <i class="fa fa-caret-right"></i> OT Data (残業データ)
                                </a>
                                <br>
                                {{--   <a href="{{ url("index/report/overtime_resume") }}">
                                    <i class="fa fa-caret-right"></i> Monthly OT & MP Resume (月次残業・要員まとめ)
                                </a>
                                <br> --}}
                                <a href="{{ url("information_board") }}">
                                    <i class="fa fa-caret-right"></i> Create OT Form (残業申請書)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Manpower Information (人工の情報)</span>
                                <br>
                                <a href="{{ url("index/report/manpower") }}">
                                    <i class="fa fa-caret-right"></i> Manpower Information (人工の情報)
                                </a>
                                {{-- <br>
                                    <a href="{{ url("index/report/stat") }}">
                                        <i class="fa fa-caret-right"></i>By Status (雇用形態別)
                                    </a>
                                    <br>
                                    <a href="{{ url("index/report/department") }}">
                                        <i class="fa fa-caret-right"></i>By Department (部門別)
                                    </a>
                                    <br>
                                    <a href="{{ url("index/report/grade") }}">
                                        <i class="fa fa-caret-right"></i>By Grade (等級別)
                                    </a>
                                    <br>
                                    <a href="{{ url("index/report/jabatan") }}">
                                        <i class="fa fa-caret-right"></i>By Position (役職別)
                                    </a>
                                    <br>
                                    <a href="{{ url("index/report/gender") }}">
                                        <i class="fa fa-caret-right"></i>By Gender (性別)
                                    </a> --}}
                                    <br>
                                    <a href="{{ url("index/report/total_meeting") }}">
                                        <i class="fa fa-caret-right"></i> Total Meeting (トータルミーティング)
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span style="font-weight: bold;">Presence Information (出勤情報)</span>
                                    {{-- <br>
                                        <a href="{{ url("index/general/online_transportation") }}">
                                            <i class="fa fa-caret-right"></i> Online Attendance And Transportation Report (出席・移動のオンライン報告)
                                        </a> --}}
                                        <br>
                                        <a href="{{ url("index/report/employee_resume") }}">
                                            <i class="fa fa-caret-right"></i> Employee Resume (従業員のまとめ)
                                        </a>
                                        <br>
                                        {{--  <a href="{{ url("index/report/daily_attendance")}}">
                                            <i class="fa fa-caret-right"></i>Attendance (出勤)
                                        </a>
                                        <br> --}}
                                        <a href="{{ url("index/report/absence") }}">
                                            <i class="fa fa-caret-right"></i> Absence (欠勤)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/report/attendance_data")}}">
                                            <i class="fa fa-caret-right"></i> Attendance Data (出席データ)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/report/checklog_data")}}">
                                            <i class="fa fa-caret-right"></i> Checklog Data (出退勤登録データ)
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-weight: bold;">General Affair (総務課)</span>
                                        <br>
                                        <a href="{{ url("index/ga_control/driver") }}">
                                            <i class="fa fa-caret-right"></i> Driver Monitoring System (ドライバー管理システム)
                                        </a>
                                        <br>
                                        <a href="#">
                                            <i class="fa fa-caret-right"></i> Live Cooking Order (ライブクッキングの予約)
                                        </a>
                                        <br>
                                        <a href="#">
                                            <i class="fa fa-caret-right"></i> Japanese Food Order (和食弁当の予約)
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-weight: bold;">Quality Assurance (品保)</span>
                                        <br>                            
                                        <a href="{{ url("index/qc_report/grafik_cpar") }}">
                                            <i class="fa fa-caret-right"></i> CPAR & CAR Monitoring (是正予防策・是正策監視)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/qc_report") }}">
                                            <i class="fa fa-caret-right"></i> Corrective & Preventive Action Request (是正予防策依頼)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/qc_car") }}">
                                            <i class="fa fa-caret-right"></i> Corrective Action Report (是正策リポート)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/cpar/resume") }}">
                                            <i class="fa fa-caret-right"></i> Resume CPAR & CAR (是正予防策・是正策のまとめ)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/qc_report/grafik_kategori") }}">
                                            <i class="fa fa-caret-right"></i> Report CPAR By Category (種類別の是正処置報告書)
                                        </a>
                                        <br>                   
                                        <a href="{{ url("index/qa_ymmj") }}">
                                            <i class="fa fa-caret-right"></i> Form Ketidaksesuaian YMMJ (YMMJ不具合リポート)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/qa_ymmj/grafik_ymmj") }}">
                                            <i class="fa fa-caret-right"></i> Report YMMJ (YMMJへの報告)
                                        </a>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>                        
                                        <a href="{{ url("index/qa_ymmj") }}">
                                            <i class="fa fa-caret-right"></i> Form Ketidaksesuaian YMMJ (YMMJ不具合リポート)
                                        </a>
                                        <br>
                                        <a href="{{ url("index/qa_ymmj/grafik_ymmj") }}">
                                            <i class="fa fa-caret-right"></i> Report YMMJ (YMMJへの報告)
                                        </a>
                            <!-- <br>
                            <a href="{{ url("index/request_qa") }}">
                                <i class="fa fa-caret-right"></i> Form Request CPAR QA (品保是正予防策依頼書)
                            </a> -->
                        </td>
                    </tr> --}}
                    {{--  <tr>
                        <td>
                            <span style="font-weight: bold;">Internet of Things (モノのインターネット)</span>
                            <br>
                            <a href="{{ url("index/reedplate/map") }}">
                                <i class="fa fa-caret-right"></i> Smart Tracking Operator ReedPlate <br>(リードプレート作業者のスマートトラッキング)
                            </a>
                            <br>
                            <a href="{{ url("index/reedplate/working_time") }}">
                                <i class="fa fa-caret-right"></i> Working Time Reedplate (リードプレート作業時間)
                            </a>                           
                        </td>
                    </tr> --}}
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Report Form Ketidaksesuaian (不適合報告フォーム)</span>
                            <br>
                            <a href="{{ url("/index/form_ketidaksesuaian") }}">
                                <i class="fa fa-caret-right"></i> Create Form Ketidaksesuaian (不適合報告フォームを作成)
                            </a>
                            <br>
                            <a href="{{ url("/index/form_ketidaksesuaian/monitoring") }}">
                                <!-- <i class="fa fa-caret-right"></i> Incompatible Report Monitoring (不適合報告フォームの管理) -->
                                <i class="fa fa-caret-right"></i> Monitoring Form Ketidaksesuaian (不適合報告フォームの管理)
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span style="font-weight: bold;">Others (他の情報)</span>
                            <br>
                            <a href="{{ url("/index/form_experience") }}">
                                <i class="fa fa-caret-right"></i> (One For All) Form Permasalahan & Kegagalan (問題・失敗のフォーム)
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Audit / Patrol Internal<br>内部監査・パトロール </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Patrol (パトロール)</span>
                            <br>
                            <a href="{{ url("/index/audit_patrol") }}">
                                <i class="fa fa-caret-right"></i> 5S Patrol GM / Presdir (社長パトロール)
                            </a>
                            <br>
                            <a href="{{ url("/index/audit_patrol/monitoring") }}">
                                <i class="fa fa-caret-right"></i> Patrol Monitoring (パトロール監視)
                            </a>

                            
                        </td>                     
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Audit (内部監査)</span>
                            <br>
                            <a href="{{ url("/index/audit_iso/create_audit") }}">
                                <i class="fa fa-caret-right"></i> Audit ISO (ISO内部監査のCPARを作成)
                            </a>
                            <!-- <br>
                            <a href="{{ url("/index/audit_iso") }}">
                                <i class="fa fa-caret-right"></i> Create Audit ISO (ISO内部監査のCPARを作成)
                            </a> -->
                            <br>
                            <a href="{{ url("/index/audit_iso/monitoring2") }}">
                                <i class="fa fa-caret-right"></i> Audit ISO 14001 (ISO内部監査のCPARを報告)
                            </a>
                            <br>
                            <a href="{{ url("/index/audit_iso/monitoring") }}">
                                <i class="fa fa-caret-right"></i> Audit ISO 45001 (ISO内部監査のCPARを報告)
                            </a>
                            <br>
                            <a href="{{ url("/index/audit?category=kanban") }}">
                                <i class="fa fa-caret-right"></i> Audit Kanban (かんばん監査)
                            </a>
                            <br>
                            <a href="{{ url("/index/audit_kanban/monitoring") }}">
                                <i class="fa fa-caret-right"></i> Audit Kanban Monitoring (監査かんばんに対する監視)
                            </a>
                            <br>
                            <a href="{{ url("/index/audit_patrol_mis") }}">
                                <i class="fa fa-caret-right"></i> Audit MIS (MIS課内部監査)
                            </a>
                            <!-- <br>
                            <a href="{{ url("/index/audit_internal_monitoring") }}">
                                <i class="fa fa-caret-right"></i> All Audit Monitoring (全監査の監視)
                            </a> -->
                        </td>
                    </tr>
                    
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Stock Taking<br>棚卸し</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Monthly Stock Taking (月次棚卸)</span>
                            <br>
                            <a href="{{ url("/index/stocktaking/menu") }}">
                                <i class="fa fa-caret-right"></i> Monthly Stock Taking (月次棚卸)
                            </a>
                            <br>
                        </td>                     
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Stock Taking Report (棚卸し報告)</span>
                            <br>
                            <a href="{{ url("/index/stocktaking/silver_report") }}">
                                <i class="fa fa-caret-right"></i> Silver Stock Taking Report (銀材棚卸し報告)
                            </a>
                            <br>
                            <a href="{{ url("/index/stocktaking/daily_report") }}">
                                <i class="fa fa-caret-right"></i> Daily Stock Taking Report (日次棚卸し報告)
                            </a>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
          <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Raw Material<br/>素材</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ url("/index/material/material_monitoring/direct") }}">
                            <i class="fa fa-caret-right"></i> Raw Material Monitoring (Direct) (素材監視「」)
                        </a>
                        <br>
                        <a href="{{ url("/index/material/material_monitoring/indirect") }}">
                            <i class="fa fa-caret-right"></i> Raw Material Monitoring (Indirect) (素材監視「」)
                        </a>
                        <br>
                        <a href="{{ url("/index/material/material_monitoring/subcon") }}">
                            <i class="fa fa-caret-right"></i> Raw Material Monitoring (Subcon) (素材監視「」)
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Other Information<br/>他の情報</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                 <td>
                    {{--     <a href="{{ url("index/report/overtime_outsource") }}">
                        <i class="fa fa-caret-right"></i> Outsource OT (派遣社員の残業管理)
                    </a> --}}
                    {{-- <br>
                        <a href="http://172.17.128.114/clinic-new/chart.php">
                            <i class="fa fa-caret-right"></i> Clinic Visit ()
                        </a> 
                        <br>--}}
                        {{--  <a href="{{ url("index/general/mosaic") }}">
                            <i class="fa fa-caret-right"></i> Yamaha Day Mosaic Art Project
                        </a>
                        <br> --}}
                        <a href="{{ url("index/mirai_mobile/index") }}">
                            <i class="fa fa-caret-right"></i> MIRAI Mobile Report(モバイルMIRAIの記録)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_monitoring") }}">
                            <i class="fa fa-caret-right"></i> Clinic Monitoring (クリニック監視)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_visit?datefrom=&dateto=") }}">
                            <i class="fa fa-caret-right"></i> Clinic Visit (クリニック訪問)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_disease?month=") }}">
                            <i class="fa fa-caret-right"></i> Clinic Diagnostic Data (クリニック見立てデータ)
                        </a>
                        {{--  <br>
                            <a href="{{ url("index/emergency_response") }}">
                                <i class="fa fa-caret-right"></i> Emergency Condition (緊急事態)
                            </a> --}}
                            <br>
                            <a href="{{ url("index/toilet") }}">
                                <i class="fa fa-caret-right"></i> Toilet Availability (トイレステイタス(空席・使用中))
                            </a>
                            <br>
                            <a href="{{ url("index/display/ip?location=server") }}">
                                <i class="fa fa-caret-right"></i> Internet Protocol Monitoring (IP管理)
                            </a>
                            <br>
                            <!-- <a href="{{ url("visitor_confirmation_manager") }}">
                                <i class="fa fa-caret-right"></i> Visitor Confirmation (来客の確認)
                            </a>
                            <br> -->
                            <a href="{{ url("visitor_display") }}">
                                <i class="fa fa-caret-right"></i> Visitor Monitoring (来客の管理)
                            </a>
                            <br>
                            <a href="{{ url("index/display/pantry_visit?tanggal=") }}">
                                <i class="fa fa-caret-right"></i> Pantry Visitor Monitoring (給湯室の来室者監視)
                            </a>
                            <br>
                            <a href="{{ url("index/temperature") }}">
                                <i class="fa fa-caret-right"></i> Body Temperature (体温)
                            </a>
                            <br>
                            <a href="{{ url("index/temperature/room_temperature") }}">
                                <i class="fa fa-caret-right"></i> Room Temperature (室内温度)
                            </a>
                            <br>
                            <a href="{{ url("index/meeting") }}">
                                <i class="fa fa-caret-right"></i> Meeting List (会議リスト)
                            </a>
                            <br>
                            <a href="{{ secure_url("index/std_control/safety_shoes") }}">
                                <i class="fa fa-caret-right"></i> Safety Shoes Control (安全靴管理システム)
                            </a>
                            <br>
                            <a href="{{ url("index/survey") }}">
                                <i class="fa fa-caret-right"></i> Emergency Survey (エマージェンシーサーベイ)
                            </a>
                            <!-- <br>
                            <a href="{{ url("index/display/office_clock") }}">
                                <i class="fa fa-caret-right"></i> Office Clock (??)
                            </a> -->
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>HRqu & e-Kaizen<br>Hrqu&e-改善</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php $tahun = date('Y'); ?>
                            <a href="{{ route('emp_service', ['id' =>'1', 'tahun' => $tahun]) }}">
                                <i class="fa fa-caret-right"></i> HRqu (従業員の情報サービス)
                            </a>
                            <br>
                            <a href="{{ url("/index/resume_pajak") }}">
                                <i class="fa fa-caret-right"></i> Resume Pengisian NPWP (納税義務者番号データのまとめ)
                            </a>
                            <br>

                            <a href="{{ route('emp_service', ['id' => '2', 'tahun' => $tahun]) }}">
                                <i class="fa fa-caret-right"></i> e-Kaizen (E-改善)
                            </a>
                            <br>
                            <a href="{{ url("/index/kaizen/aproval/resume") }}">
                                <i class="fa fa-caret-right"></i> Resume e-Kaizen Progress (E-改善進捗のまとめ)
                            </a>
                            <br>
                            <a href="{{ url("/index/kaizen") }}">
                                <i class="fa fa-caret-right"></i> List Unverified e-Kaizen (未承認E-改善のリスト)
                            </a>
                            <br>
                            {{-- <a href="#">
                                <i class="fa fa-caret-right"></i> List Verified e-Kaizen (承認済E-改善のリスト)
                            </a>
                            <br> --}}
                            {{-- <a href="{{ url("/index/kaizen/applied") }}">
                                <i class="fa fa-caret-right"></i> List Applied e-Kaizen (適用済E-改善のリスト)
                            </a>
                            <br> --}}
                            <a href="{{ url("/index/kaizen2/resume") }}">
                                <i class="fa fa-caret-right"></i> Report All Kaizen (全改善のリポート)
                            </a>
                            <br>
                            <a href="{{ url("index/kaizen2/report") }}">
                                <i class="fa fa-caret-right"></i> Report Kaizen Excellent (エクセレント改善のリポート)
                            </a>
                            <br>
                            <a href="{{ url("index/kaizen2/value") }}">
                                <i class="fa fa-caret-right"></i> Report Kaizen Reward (改善リポートのリワード)
                            </a>
                            <br>
                        </td>
                    </tr>
                    {{--  <tr>
                        <td>
                            <a href="{{ url("index/upload_kaizen") }}">
                                <i class="fa fa-caret-right"></i> Upload Kaizen Images (改善写真のアップロード)
                            </a>
                            <br>
                        </td>
                    </tr> --}}
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>YMPI Supporting System<br> YMPI サポートシステム</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="http://10.109.52.8/sf6/" target="_blank">
                                <i class="fa fa-caret-right"></i> Sunfish (サンフィッシュ)
                            </a>
                            <br>
                            <a href="https://a01.yamaha.co.jp/fw/dfw/CERT/Portal.php" target="_blank">
                                <i class="fa fa-caret-right"></i> IDM Portal (IDMポータル)
                            </a>
                            <br>
                            <a href="https://yamahagroup.sharepoint.com/sites/prj00220" target="_blank">
                                <i class="fa fa-caret-right"></i> Sharepoint;
                            </a>
                            <a href="https://a01.yamaha.co.jp/fw/dfw/SAP2/Citrix/XenApp/site/default.aspx" target="_blank">
                               SAP;
                           </a>
                           <a href="https://adagio.yamaha.co.jp/imart/default.portal" target="_blank">
                               Adagio;
                           </a>
                           <a href="https://a01.yamaha.co.jp/fw/dfw/MA5/ma5/EntranceServlet" target="_blank">
                               MA5;
                           </a>
                       </td>     
                   </tr>                                                                             
               </tbody>
           </table>
           <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Workshop<br>ワークショップ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ url("index/workshop/create_wjo") }}">
                            <i class="fa fa-caret-right"></i> Create WJO (作業依頼書の作成)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/wjo_monitoring") }}">
                            <i class="fa fa-caret-right"></i> WJO Monitoring (作業依頼書の監視)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/productivity") }}">
                            <i class="fa fa-caret-right"></i> Workshop Productivity (作業依頼書の実現力)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/workload") }}">
                            <i class="fa fa-caret-right"></i> Workshop Workload (作業依頼書一覧)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/operatorload") }}">
                            <i class="fa fa-caret-right"></i> Workshop Operator Work Schedule (ワークショップ作業者の作業予定)
                        </a>
                        <br>
                    </td>                        
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Plant Maintenance<br>工場保全管理</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Machine Monitoring (マシン監視)</span>
                        <br>
                        <a href="http://10.109.52.7/zed/dashboard/awal" target="_blank">
                            <i class="fa fa-caret-right"></i> Overall Equipment Efficiency (稼働率) (OEE)
                        </a>
                        <br>
                        <a href="{{ url("/machinery_monitoring?mesin=") }}">
                            <i class="fa fa-caret-right"></i> Machinery Monitoring (機械監視)
                        </a>
                        <br>
                        <a href="http://10.109.52.7/mtnc/login/log" target="_blank">
                            <i class="fa fa-caret-right"></i> Planned Maintenance Activity Finding
                        </a>

                    </td>                     
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">SPK (メンテナンス作業依頼書)</span>
                        <br>
                        <a href="{{ url("index/maintenance/list/user") }}">
                            <i class="fa fa-caret-right"></i> Create SPK (作業依頼書を作成)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/spk/weekly") }}">
                            <i class="fa fa-caret-right"></i> SPK Weekly Report (??)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/spk/grafik") }}">
                            <i class="fa fa-caret-right"></i> SPK Monitoring (作業依頼書の管理)
                        </a>
                        <br>
                        <a href="{{ url("index/report/urgent_monitoring") }}">
                            <i class="fa fa-caret-right"></i> Urgent SPK Monitoring (??)
                        </a>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Utility (ユーティリティー)</span>
                        <br>
                        <a href="{{ url("index/maintenance/apar") }}">
                            <i class="fa fa-caret-right"></i> APAR Check Schedule (消火器・消火栓の点検日程)
                        </a>
                        <br>
                        <a href="{{ secure_url("/index/maintenance/apar/expire") }}">
                            <i class="fa fa-caret-right"></i> APAR Expired List 消火器・消火栓の使用期限一覧)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/apar/resume") }}">
                            <i class="fa fa-caret-right"></i> APAR Resume (消火栓・消火器の点検進捗のまとめ)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/pic/Utility") }}">
                            <i class="fa fa-caret-right"></i> PIC List (担当者リスト)
                        </a>
                        <br>
                        <a href="{{ url("/index/production_report/index/10") }}">
                            <i class="fa fa-caret-right"></i> Maintenance Report (メンテナンスリポート)
                        </a>
                        <!-- <br>
                        <a href="{{ url('/index/production_report/index/13') }}">
                            <i class="fa fa-caret-right"></i> PE Field Report (??)
                        </a> -->
                    </td>
                </tr>
                       <!--  <tr>
                            <td>
                                <span style="font-weight: bold;">Planned Maintenance (予定保全)</span>
                                <br>
                                <a href="{{ url("index/maintenance/pm/schedule") }}">
                                    <i class="fa fa-caret-right"></i> Plan Maintenance Schedule (予定保全の計画)
                                </a>
                                <br>
                                <a href="{{ url("index/maintenance/pm/monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Plan Maintenance Monitoring (保全計画の監視)
                                </a>
                                <br>
                                <a href="{{ url("index/maintenance/apar") }}">
                                    <i class="fa fa-caret-right"></i> PIC List (担当者リスト)
                                </a>
                                <br>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Work In Process<br/>仕掛品</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">KITTO (きっと)</span>
                                <br>
                                <a href="http://10.109.52.4/kitto/public">
                                    <i class="fa fa-caret-right"></i> Kanban Monitoring (かんばん監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">INJECTION Process (成形プロセス)</span>

                                <br>
                                <a href="{{ url("/index/injeksi") }}">
                                    <i class="fa fa-caret-right"></i> Injection Recorder (RC成形)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">MATERIAL Process (イニシアル工程)</span>
                                <br>
                                <a href="{{ url("/index/initial", "press") }}">
                                    <i class="fa fa-caret-right"></i> Press (プレス)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial", "lotting") }}">
                                    <i class="fa fa-caret-right"></i> Lotting (ロッティング)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/12") }}">
                                    <i class="fa fa-caret-right"></i> Parts Process (WI-PP) Report (部品加工)
                                </a>
                                <br>
                                <a href="{{ url("/index/press/monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Press Machine Monitoring (プレス機管理)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial/stock_monitoring", "mpro") }}">
                                    <i class="fa fa-caret-right"></i> M-PRO Stock Monitoring (部品加工の仕掛品監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial/stock_trend", "mpro") }}">
                                    <i class="fa fa-caret-right"></i> M-PRO Stock Trend (部品加工の在庫トレンド)
                                </a>
                                <br>
                                <a href="http://10.109.52.7/tpro/" target="_blank">
                                    <i class="fa fa-caret-right"></i> M-Pro Kanban Monitoring (Mプロかんばんの監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">BODY Process (イニシアル工程)</span>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_cl") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (ロッティング)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/12") }}">
                                    <i class="fa fa-caret-right"></i> Parts Process (WI-PP) Report (部品加工)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">WELDING Process (溶接工程)</span>
                                <br>
                                <a href="{{ url("/index/process_welding_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート溶接)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_stamp_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス溶接)
                                </a>
                                <br>
                                <a href="{{ url("/index/welding_jig") }}">
                                    <i class="fa fa-caret-right"></i> Digital Jig Handling (冶具デジタル管理)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/welding_sax?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/welding_cl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking Monitor (クラリネットピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/15") }}">
                                    <i class="fa fa-caret-right"></i> Welding Process Report (溶接プロセスリポート)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">MIDDLE Process (中間工程)</span>
                                <br>
                                <a href="{{ url("/index/process_middle_cl") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_middle_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート表面処理)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_middle_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス表面処理)
                                </a>
                                <br>
                                <a href="{{ url("/index/middle/stock_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Middle Stock Monitoring (中間工程の仕掛品監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">FINAL Process (最終工程)</span>
                                <br>
                                <a href="{{ url("/index/process_assy_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート仮組~組立)
                                </a>
                                <br>
                                <a href="{{ url("/index/Pianica") }}">
                                    <i class="fa fa-caret-right"></i> Pianica (ピアニカ組立)
                                </a><br>
                                <a href="{{ url("index/process_stamp_sx_assy") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス仮組～組立)
                                </a>
                                <br>
                                <a href="{{ url("index/recorder_process") }}">
                                    <i class="fa fa-caret-right"></i> Recorder (リコーダー)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/assy_sax?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Sax Key Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/body/sax_body?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Sax Body Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/assy_cl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking Monitor (クラリネットピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/8") }}">
                                    <i class="fa fa-caret-right"></i> Assembly (WI-A) Report (アセンブリ（WI-A）レポート)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Check Material Dimensions (寸法測定結果)</span>
                                <br>
                                <a href="http://10.109.52.11/digital-ik-cdm/">
                                    <i class="fa fa-caret-right"></i> Work Instruction Digital System (作業手順書デジタル化)
                                </a>
                                <br>
                                <a href="http://10.109.52.11/cdm-new/">
                                    <i class="fa fa-caret-right"></i> T-Pro CDM Charts (T-ProのCDMチャート)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">NG Jelas (明らか不良)</span>
                                <br>
                                <a href="{{ url("/index/audit_ng_jelas_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Audit NG Jelas Monitoring (明らか不良監査の監視)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Finished Goods & KD Parts<br/>完成品・KD部品</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Finished Goods Control (完成品管理)</span>
                                <br>
                                <a href="{{ url("/index/fg_production_schedule") }}">
                                    <i class="fa fa-caret-right"></i> Production Schedule Data (生産スケジュールデータ)
                                </a>
                                {{-- <br>
                                    <a href="{{ url("/index/fg_production_monitoring") }}">
                                        <i class="fa fa-caret-right"></i> Production Schedule Monitoring (生産予定監視)
                                    </a> --}}
                                    <br>
                                    <a href="{{ url("/index/dp_production_result") }}">
                                        <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/dp_fg_accuracy") }}">
                                        <i class="fa fa-caret-right"></i> FG Accuracy (FG週次出荷)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/fg_production") }}">
                                        <i class="fa fa-caret-right"></i> Production Result (生産実績)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/fg_stock") }}">
                                        <i class="fa fa-caret-right"></i> Finished Goods Stock (完成品在庫)
                                    </a>
                                    {{--  <br>
                                        <a href="{{ url("/index/display/shipment_progress") }}">
                                            <i class="fa fa-caret-right"></i> FG Shipment Progress (出荷結果)
                                        </a> --}}
                                        <br>
                                        <a href="{{ url("/index/fg_traceability") }}">
                                            <i class="fa fa-caret-right"></i> FG Traceability (FG完成品追跡)
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-weight: bold;">KD Parts Control (KD部品管理)</span>
                                        {{-- <br>
                                            <a href="{{ url("/index/kd_production_schedule_data") }}">
                                                <i class="fa fa-caret-right"></i> Production Schedule Data (生産スケジュールデータ)
                                            </a> --}}
                                            {{--   <br>
                                                <a href="{{ url("/index/kd_daily_production_result") }}">
                                                    <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                                                </a> --}}
                                                <br>
                                                <a href="{{ url("/index/kd_stock") }}">
                                                    <i class="fa fa-caret-right"></i> KD Parts Stock (KD部品在庫)
                                                </a>
                                                <br>
                                                <a href="{{ url("/index/kd_traceability") }}">
                                                    <i class="fa fa-caret-right"></i> KD Traceability (KD完成品追跡)
                                                </a>
                                                {{--  <br>
                                                    <a href="{{ url("/index/kd_shipment_progress") }}">
                                                        <i class="fa fa-caret-right"></i> KD Shipment Progress (出荷結果)
                                                    </a> --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Shipment Control (出荷管理)</span>
                                                    <br>
                                                    <a href="{{ url("/index/display/all_stock") }}">
                                                        <i class="fa fa-caret-right"></i> All Stock (全在庫)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("index/resume_shipping_order") }}">
                                                        <i class="fa fa-caret-right"></i> Shipping Booking Management List (船便予約管理リスト)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_shipment_schedule") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Schedule Data (出荷スケジュールデータ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_shipment_result") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Result (出荷結果)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/display/shipment_progress_all") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Progress (出荷結果)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/display/shipment_report") }}">
                                                        <i class="fa fa-caret-right"></i> Weekly Shipment ETD SUB (週次出荷　スラバヤ着荷)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_container_departure") }}">
                                                        <i class="fa fa-caret-right"></i> Container Departure (コンテナー出発)
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Shipment Performance (出荷管理)</span>
                                                    <br>
                                                    <a href="{{ url("/index/display/stuffing_monitoring") }}">
                                                        <i class="fa fa-caret-right"></i> Stuffing Monitoring (荷積み監視)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_weekly_summary") }}">
                                                        <i class="fa fa-caret-right"></i> Weekly Summary (週次まとめ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_monthly_summary") }}">
                                                        <i class="fa fa-caret-right"></i> Monthly Summary (月次まとめ)
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Chorei (朝礼)</span>
                                                    <br>
                                                    <a href="{{ url("/index/ch_daily_production_result") }}">
                                                        <i class="fa fa-caret-right"></i> FG Production Summary (FG生産まとめ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/ch_daily_production_result_kd") }}">
                                                        <i class="fa fa-caret-right"></i> KD Production Summary (KD生産まとめ)
                                                    </a>
                                                    {{--  <br>
                                                        <a href="{{ url("/index/display/efficiency_monitoring") }}">
                                                            <i class="fa fa-caret-right"></i> Daily Efficiency Monitoring (日次効率の監視)
                                                        </a> --}}
                                                        <br>
                                                        <a href="{{ url("/index/display/efficiency_monitoring_monthly") }}">
                                                            <i class="fa fa-caret-right"></i> Monthly Efficiency Monitoring (月次効率の監視)
                                                        </a>
                                                        <br>
                                                        <a href="{{ url("/index/display/eff_scrap") }}">
                                                            <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                                        </a>
                                                        <br>
                                                        <a href="{{ url("/index/general/pointing_call/japanese") }}">
                                                            <i class="fa fa-caret-right"></i> Japanese Pointing Call (駐在員指差し呼称)
                                                        </a>
                                                        {{-- <br>
                                                            <a href="{{ url("/index/production_achievement") }}">
                                                                <i class="fa fa-caret-right"></i> Production Achievement ()
                                                            </a> --}}
                                                        </td>
                                                    </tr>
                                                    {{-- <tr>
                                                        <td>
                                                            <span style="font-weight: bold;">Additional</span>
                                                            <br>
                                                            <a href="{{ url("flute_repair") }}">
                                                                <i class="fa fa-caret-right"></i> Flute Repair
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("recorder_repair") }}">
                                                                <i class="fa fa-caret-right"></i> Recorder Repair
                                                            </a>
                                                        </td>
                                                    </tr> --}}
                                                    {{-- <tr>
                                                        <td>
                                                            <span style="font-weight: bold;">Display (表示)</span>
                                                            <br>
                                                            <a href="{{ url("/index/display/stuffing_progress") }}">
                                                                <i class="fa fa-caret-right"></i> Stuffing Progress (荷積み進捗)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("/index/display/stuffing_time") }}">
                                                                <i class="fa fa-caret-right"></i> Stuffing Time (荷積み時間)
                                                            </a>
                                                        </td>
                                                    </tr> --}}
                                                </tbody>
                                            </table>

                                            {{--       <table class="table table-bordered">
                                                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                    <tr>
                                                        <th>Chorei<br/> 朝礼</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url("/index/ch_daily_production_result") }}">
                                                                <i class="fa fa-caret-right"></i> Production Summary (生産まとめ)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("/index/display/eff_scrap") }}">
                                                                <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("/index/general/pointing_call/japanese") }}">
                                                                <i class="fa fa-caret-right"></i> Japanese Pointing Call (駐在員指差し呼称)
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table> --}}


                                            <table class="table table-bordered">
                                                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                    <tr>
                                                        <th>PR, PO, Investment & Budget Control<br/> 購入依頼書・投資申請</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url("purchase_requisition/monitoring") }}">
                                                                <i class="fa fa-caret-right"></i> PR Monitoring & Control (PR監視・管理)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("investment/control") }}">
                                                                <i class="fa fa-caret-right"></i> Investment Monitoring & Control (投資管理)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("purchase_order/monitoring") }}">
                                                                <i class="fa fa-caret-right"></i> PO Monitoring & Control (PO管理)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("budget/info") }}">
                                                                <i class="fa fa-caret-right"></i> Budget Information (予算情報)
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- <table class="table table-bordered">
                                                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                    <tr>
                                                        <th>
                                                            <span style="font-weight: bold;">Internet of Things<br>モノのインターネット</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url("index/driver_manager") }}">
                                                                <i class="fa fa-caret-right"></i> Report Driver IN / OUT <span style="color:red"> *Beacon </span>
                                                            </a>                           
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table> --}}

                           <!--  <table class="table table-bordered">
                                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                    <tr>
                                        <th>Sakurentsu<br/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="{{ url("index/sakurentsu/monitoring") }}">
                                                <i class="fa fa-caret-right"></i> Sakurentsu Monitoring ()
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                </section>

                @stop
                @section('scripts')
                <script src="{{ url("js/jquery.marquee.min.js")}}"></script>
                <script>
                    jQuery(document).ready(function() {
                        $('.marquee').marquee({
                            duration: 4000,
                            gap: 1,
                            delayBeforeStart: 0,
                            direction: 'up',
                            duplicated: true
                        });
                    });

                </script>
                @endsection