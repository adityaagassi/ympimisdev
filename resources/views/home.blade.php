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

                    <a class="list-group-item" href="{{ url("/index/displayWipFl") }}" style="text-align: center;">
                        FL Subassy-Assy Resume<br><span class="text-purple">???</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/404") }}" style="text-align: center; color: rgb(190,190,190);">
                        FL Kariawase<br><span class="text-purple">仕掛品監視</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("index/process_assy_fl_2") }}" style="text-align: center;">
                        FL Tanpoawase<br><span class="text-purple">FLタンポ合わせ作業</span>
                    </a>
                    <a class="list-group-item" href="{{ url("index/process_assy_fl_3") }}" style="text-align: center;">
                        FL Seasoning-Kanggou<br><span class="text-purple">FLシーズニング作業・篏合作業</span> 
                    </a>
                    <a class="list-group-item" href="{{ url("index/process_assy_fl_4") }}" style="text-align: center;">
                        FL Chousei<br><span class="text-purple">FL調整作業</span> 
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