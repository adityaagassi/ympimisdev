@extends('layouts.master')
@section('header')
<section class="content-header" style="text-align: center;">
    <span style="font-weight: bold; font-size: 30px">YMPI Information System <span class="text-purple">YMPI 情報システム</span></span>
    <ol class="breadcrumb">
    </ol>
</section>
@endsection

@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-default box-solid">
                <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
                    <h5 class="box-title" style="font-weight: bold;">Raw Material <span class="text-purple">素材</span></h5>
                </div>
                <div class="box-body">

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Vendor To YMPI (ベンダー⇒YMPI)</span>

                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Weekly MilkRun Parts <span class="text-purple">週次ミルクラン部品納入 </span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Weekly JIT Parts <span class="text-purple">週次JIT部品納入 </span> 
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Warehouse To Production (YMPI 部品倉庫⇒生産職場)</span>

                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Weekly Picking Status <span class="text-purple">週次ピッキング状況 </span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Budomari Material Monitor <span class="text-purple">材料歩留モニター </span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Picking Trouble By Vendor <span class="text-purple">ベンダー別のピッキング問題 </span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Picking Trouble by Model Material Number <span class="text-purple">(???)</span> 
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

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> WIP Monitoring (WIP 生産工程モニタリング)</span>

                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Production Result <span class="text-purple">生産実績</span>
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> WIP Monitor<span class="text-purple">???</span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> WIP Flow Process <span class="text-purple">???</span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Stock WIP <span class="text-purple">???</span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> WIP Data Record <span class="text-purple">???</span> 
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> WIP Performance (WIPパフォーマンス)</span>

                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Material Number Efficiency Data <span class="text-purple">???</span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Assembly Efficiency & Loss Time <span class="text-purple">???</span> 
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/404") }}">
                        <span class="fa fa-bar-chart"></span> Assembly Efficiency & Loss Time Monthly <span class="text-purple">???</span> 
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

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Sales Control (売上管理)</span>

                    <a class="list-group-item disabled" href="{{ url("/index/sl_budget") }}">
                        <span class="fa fa-bar-chart"></span> Sales Budget/Forecast/Actual <span class="text-purple">売上予算・見通し・実績</span>
                    </a>
                    <a class="list-group-item disabled" href="{{ url("/index/sl_current") }}">
                        <span class="fa fa-bar-chart"></span> Current Sales Progress <span class="text-purple">現在売上進捗</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Finished Goods Control (完成品管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_production") }}">
                        <span class="fa fa-bar-chart"></span> Production Result <span class="text-purple">生産実績</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_stock") }}">
                        <span class="fa fa-bar-chart"></span> Finsihed Goods Stock <span class="text-purple">完成品在庫</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Shipment Control (出荷管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_container_departure") }}">
                        <span class="fa fa-bar-chart"></span> Container Departure <span class="text-purple">コンテナー出発</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_shipment_schedule") }}">
                        <span class="fa fa-table"></span> Shipment Schedule Data <span class="text-purple">出荷スケジュール</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_traceability") }}">
                        <span class="fa fa-table"></span> Traceability <span class="text-purple">完成品追跡</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Shipment Performance (出荷管理)</span>

                    <a class="list-group-item" href="{{ url("/index/fg_weekly_summary") }}">
                        <span class="fa fa-table"></span> Weekly Summary <span class="text-purple">週次まとめ</span>
                    </a>
                    <a class="list-group-item" href="{{ url("/index/fg_monthly_summary") }}">
                        <span class="fa fa-table"></span> Monthly Summary <span class="text-purple">月次まとめ</span>
                    </a>

                    <span style="font-weight: bold; color: green;"><i class="fa fa-caret-right"></i> Chorei (朝礼)</span>

                    <a class="list-group-item" href="{{ url("/index/ch_daily_production_result") }}">
                        <span class="fa fa-bar-chart"></span> Production Summary <span class="text-purple">生産まとめ</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</section>

@stop