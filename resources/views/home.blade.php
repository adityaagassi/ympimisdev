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
        height: 50px;
    }
</style>
@stop
@section('header')
<section class="content-header" style="padding: 0; margin:0;">
    <div class="marquee">
        <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span style="font-size:22px;"><b>A</b></span>cquisition of <span style="font-size:22px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 20px;" class="text-purple">
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="26px">
            製 造 の リ ア ル タ イ ム 情 報
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="26px">
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
                            <span style="font-weight: bold;">Overtime Information (残業の情報)</span>
                            <br>
                            <a href="{{ url("index/report/overtime_monthly") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> OT Monitor By CC (??)
                            </a>
                            <br>
                            <a href="http://172.17.128.4/myhris/management/overtime_control" target="_blank">
                                <i class="fa fa-caret-right"></i> OT Monitor Daily (??)
                            </a>
                            <br>
                            <a href="{{ url("index/report/overtime_section")}}" target="_blank">
                                <i class="fa fa-caret-right"></i> OT By CC (??)
                            </a>
                            <br>
                            <a href="{{ url("index/report/overtime_data") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> OT Data (残業データ)
                            </a>
                            <br>
                            <a href="{{ url("index/report/overtime_resume") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Monthly OT & MP Resume (??)
                            </a>
                            <br>
                            <a href="http://172.17.128.4/myhris/home/overtime_form" target="_blank">
                                <i class="fa fa-caret-right"></i> Create OT Form (残業申請書)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Manpower Information (人工の情報)</span>
                            <br>
                            <a href="{{ url("index/report/stat") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>By Status (雇用形態別)
                            </a>
                            <br>
                            <a href="{{ url("index/report/department") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>By Department (部門別)
                            </a>
                            <br>
                            <a href="{{ url("index/report/grade") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>By Grade (等級別)
                            </a>
                            <br>
                            <a href="{{ url("index/report/jabatan") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>By Position (役職別)
                            </a>
                            <br>
                            <a href="{{ url("index/report/gender") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>By Gender (??)
                            </a>
                            <br>
                            <a href="{{ url("index/report/total_meeting") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Total Meeting (トータルミーティング)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Presence Information (??)</span>
                            <br>
                            <a href="{{ url("index/report/daily_attendance")}}" target="_blank">
                                <i class="fa fa-caret-right"></i>Attendance (??)
                            </a>
                            <br>
                            <a href="{{ url("index/report/gender") }}" target="_blank">
                                <i class="fa fa-caret-right"></i>Absence (??)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Other Information (??)</span>
                            <br>
                            <a href="{{ url("index/report/overtime_outsource") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Outsource OT (派遣社員の残業管理)
                            </a>
                            <br>
                            <a href="{{ url("index/report/overtime_outsource_data") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Outsource OT Data (派遣社員の残業データ)
                            </a>
                            <br>
                            <a href="{{ url("index/employee/service") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> HRqu - Employee Self Services (従業員の情報サービス)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Plant Maintenance (工場保全管理)</span>
                            <br>
                            <a href="http://172.17.129.99/zed/dashboard/awal" target="_blank">
                                <i class="fa fa-caret-right"></i> Overall Equipment Efficiency (稼働率)
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
                        <th>Raw Material<br/>素材</th>
                    </tr>
                </thead>
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
                            <a href="http://172.17.128.4/kitto/public" target="_blank">
                                <i class="fa fa-caret-right"></i> Kanban Monitoring (かんばん監視)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">INITIAL Process (イニシアル工程)</span>
                            <br>
                            <a href="{{ url("/index/initial/stock_monitoring", "mpro") }}">
                                <i class="fa fa-caret-right"></i> M-PRO Stock Monitoring (部品加工の仕掛品監視)
                            </a>
                            <br>
                            <a href="{{ url("/index/initial/stock_trend", "mpro") }}">
                                <i class="fa fa-caret-right"></i> M-PRO Stock Trend (部品加工の在庫トレンド)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">WELDING Process (溶接工程)</span>
                            <br>
                            <a href="{{ url("/index/process_stamp_sx") }}">
                                <i class="fa fa-caret-right"></i> Saxophone (サックス溶接)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">MIDDLE Process (中間工程)</span>
                            <br>
                            <a href="{{ url("/index/process_middle_sx") }}">
                                <i class="fa fa-caret-right"></i> Saxophone (サックス表面処理)
                            </a>
                            <br>
                            <a href="{{ url("/index/middle/stock_monitoring") }}">
                                <i class="fa fa-caret-right"></i>Middle Stock Monitoring (中間工程の仕掛品監視)
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
                                <i class="fa fa-caret-right"></i> Saxophone (???)
                            </a>
                            <br>
                            <a href="{{ url("/index/display/sub_assy?date=&surface=&key2=&model2=") }}">
                                <i class="fa fa-caret-right"></i> Saxophone Picking Monitor (サックスのピッキング監視)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Check Material Dimensions (寸法測定結果)</span>
                            <br>
                            <a href="http://172.17.128.114/digital-ik-cdm/" target="blank">
                                <i class="fa fa-caret-right"></i> Work Instruction Digital System<br>(作業手順書デジタル化)
                            </a>
                            <br>
                            <a href="http://172.17.128.114/cdm-new/" target="blank">
                                <i class="fa fa-caret-right"></i> T-Pro CDM Charts<br>(T-ProのCDMチャート)
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
                        <th>Finished Goods<br/>完成品</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Finished Goods Control (完成品管理)</span>
                            <br>
                            <a href="{{ url("/index/fg_production_schedule") }}">
                                <i class="fa fa-caret-right"></i> Production Schedule Data (??)
                            </a>
                            <br>
                            <a href="{{ url("/index/dp_production_result") }}">
                                <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                            </a>
                            <br>
                            <a href="{{ url("/index/dp_fg_accuracy") }}" target="_blank">
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
                            <br>
                            <a href="{{ url("/index/fg_traceability") }}">
                                <i class="fa fa-caret-right"></i> Traceability (完成品追跡)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Shipment Control (出荷管理)</span>
                            <br>
                            <a href="{{ url("/index/fg_shipment_schedule") }}">
                                <i class="fa fa-caret-right"></i> Shipment Schedule Data (出荷スケジュール)
                            </a>
                            <br>
                            <a href="{{ url("/index/fg_shipment_result") }}">
                                <i class="fa fa-caret-right"></i> Shipment Result (出荷結果)
                            </a>
                            <br>
                            <a href="{{ url("/index/display/shipment_progress") }}">
                                <i class="fa fa-caret-right"></i> Shipment Progress (出荷結果)
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
                            <a href="{{ url("/index/display/stuffing_monitoring") }}" target="_blank">
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
                            <a href="{{ url("/index/ch_daily_production_result") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Production Summary (生産まとめ)
                            </a>
                        </td>
                    </tr>
                    {{-- <tr>
                        <td>
                            <span style="font-weight: bold;">Display (表示)</span>
                            <br>
                            <a href="{{ url("/index/display/stuffing_progress") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Stuffing Progress (荷積み進捗)
                            </a>
                            <br>
                            <a href="{{ url("/index/display/stuffing_time") }}" target="_blank">
                                <i class="fa fa-caret-right"></i> Stuffing Time (荷積み時間)
                            </a>
                        </td>
                    </tr> --}}
                </tbody>
            </table>
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