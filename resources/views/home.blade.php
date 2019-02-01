@extends('layouts.master')
@section('header')
<section class="content-header" style="text-align: center; padding-top: 0;">
    <span style="font-size: 30px;" class="text-purple"><span style="font-size:36px;"><b>M</b></span>anufactur<span style="font-size:37px;"><b>i</b></span>ng <span style="font-size:36px;"><b>R</b></span>ealtime <span style="font-size:36px;"><b>A</b></span>cquisition of <span style="font-size:36px;"><b>I</b></span>nformation</span>
    <ol class="breadcrumb">
    </ol>
</section>
@endsection

@section('content')

<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Raw Material <span class="text-purple">素材</span></h5>
                </div>
                <div class="box-body">

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Vendor To YMPI (ベンダー⇒YMPI)</span>

                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Weekly MilkRun Parts<br><span class="text-purple">週次ミルクラン部品納入 </span>
                    </a>
                    
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Weekly JIT Parts<br><span class="text-purple">週次JIT部品納入 </span> 
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Warehouse To Production (YMPI 部品倉庫⇒生産職場)</span>

                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Weekly Picking Status<br><span class="text-purple">週次ピッキング状況 </span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Budomari Material Monitor<br><span class="text-purple">材料歩留モニター </span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Picking Trouble By Vendor<br><span class="text-purple">ベンダー別のピッキング問題 </span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Picking Trouble by Model Material Number<br><span class="text-purple">製品ＧＭＣ別のピッキング問題</span> 
                    </a>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Work In Process <span class="text-purple">仕掛品</span></h5>
                </div>
                <div class="box-body">

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> WIP Monitoring (仕掛品監視)</span>

                    <a class="list-group-item" href="{{ url("/index/process_assy_fl") }}" style="text-align: center;">
                        FL Subassy-Assembly<br><span class="text-purple">フルート仮組~組立</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> CDM Cek Dimensi Material (???)</span>

                    <a class="list-group-item" href="http://172.17.128.114/digital-ik-cdm/" style="text-align: center;" target="blank">
                        Work Instruction Digital System<br><span class="text-purple">作業手順書デジタル化</span>
                    </a>

                    <a class="list-group-item" href="http://172.17.128.114/cdm-new/" style="text-align: center;" target="blank">
                        T-Pro CDM Charts<br><span class="text-purple">T-ProのCDMチャート</span>
                    </a>



                  {{--   <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Production Result<br><span class="text-purple">生産実績</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> WIP Monitor<br><span class="text-purple">仕掛品監視</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> WIP Flow Process<br><span class="text-purple">仕掛品流れ</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Stock WIP<br><span class="text-purple">仕掛品在庫</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> WIP Data Record<br><span class="text-purple">仕掛品データ記録</span> 
                    </a> --}}
{{-- 
                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> WIP Performance (仕掛品業績)</span>

                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Material Number Efficiency Data<br><span class="text-purple">GMC別能率データ</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Assembly Efficiency & Loss Time<br><span class="text-purple">組立の能率・ロス時間</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Assembly Efficiency & Loss Time Monthly<br><span class="text-purple">月次　組立の能率・ロス時間</span> 
                    </a> --}}

                </div>
            </div>

            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Production Support Monitoring<br><span class="text-purple">生産支援モニタリング</span></h5>
                </div>
                <div class="box-body">
                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Plant Maintenance (工場保全管理)</span>
                    <a class="list-group-item" href="http://172.17.129.99/zed/dashboard/awal" style="text-align: center;" target="blank">
                        OEE (Overall Equipment Efficiency)<br><span class="text-purple">稼働率</span>
                    </a>
                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> HR Management (人材管理)</span>
                    <a class="list-group-item" href="http://172.17.128.4/myhris/home/presensi" style="text-align: center;" target="blank">
                        Manpower Attendance<br><span class="text-purple">勤怠管理</span>
                    </a>
                    <a class="list-group-item" href="http://172.17.128.4/myhris/home/karyawan_graph" style="text-align: center;" target="blank">
                        Manpower Database<br><span class="text-purple">社員構成</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Finished Goods <span class="text-purple">完成品</span></h5>
                </div>
                <div class="box-body">

                  {{--   <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Sales Control (売上管理)</span>

                    <a class="list-group-item" href="{{ url("/index/sl_budget") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Sales Budget/Forecast/Actual<br><span class="text-purple">売上予算・見通し・実績</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/sl_current") }}" style="text-align: center; color: rgb(190,190,190);">
                        <span class="fa fa-bar-chart"></span> Current Sales Progress<br><span class="text-purple">現在売上進捗</span>
                    </a> --}}

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Finished Goods Control (完成品管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_production") }}" style="text-align: center;">
                        <span class="fa fa-bar-chart"></span> Production Result<br><span class="text-purple">生産実績</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_stock") }}" style="text-align: center;">
                        <span class="fa fa-bar-chart"></span> Finished Goods Stock<br><span class="text-purple">完成品在庫</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Shipment Control (出荷管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_container_departure") }}" style="text-align: center;">
                        <span class="fa fa-bar-chart"></span> Container Departure<br><span class="text-purple">コンテナー出発</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_shipment_schedule") }}" style="text-align: center;">
                        <span class="fa fa-table"></span> Shipment Schedule Data<br><span class="text-purple">出荷スケジュール</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_traceability") }}" style="text-align: center;">
                        <span class="fa fa-table"></span> Traceability<br><span class="text-purple">完成品追跡</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Shipment Performance (出荷管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_weekly_summary") }}" style="text-align: center;">
                        <span class="fa fa-table"></span> Weekly Summary<br><span class="text-purple">週次まとめ</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_monthly_summary") }}" style="text-align: center;">
                        <span class="fa fa-table"></span> Monthly Summary<br><span class="text-purple">月次まとめ</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Chorei (朝礼)</span>

                    <a class="list-group-item" href="{{ url("/index/ch_daily_production_result") }}" style="text-align: center;">
                        <span class="fa fa-bar-chart"></span> Production Summary<br><span class="text-purple">生産まとめ</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Display (表示)</span>

                    <a class="list-group-item" href="{{ url("/index/dp_production_result") }}" style="text-align: center;">
                        <span class="fa fa-bar-chart"></span> Daily Production Result<br><span class="text-purple">日常生産実績</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</section>

@stop