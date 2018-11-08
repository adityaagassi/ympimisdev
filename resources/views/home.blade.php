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
          {{--   <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a> --}}
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
            <h5 class="box-title" style="font-weight: bold;">Work In Process <span class="text-purple">仕掛品</span></h5>
        </div>
        <div class="box-body">
           {{--  <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a>
            <a class="list-group-item" href=""></a> --}}
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="box box-default box-solid">
        <div class="box-header with-border text-center" style="background-color: rgba(144, 238, 126, 0.60);">
            <h5 class="box-title" style="font-weight: bold;">Finished Goods <span class="text-purple">完成品</span></h5>
        </div>
        <div class="box-body">
            <span style="font-weight: bold; color: red;"><i class="fa fa-caret-right"></i> Finished Goods Control (??????)</span>
            <a class="list-group-item" href="{{ url("/index/fg_production") }}"><span class="fa fa-bar-chart"></span> Production Result <span class="text-purple">生産実績</span></a>
            <a class="list-group-item" style="font-weight: bold;" href="{{ url("/index/fg_stock") }}"><span class="fa fa-bar-chart"></span> Finsihed Goods Stock <span class="text-purple">完成品在庫</span></a>
            <span style="font-weight: bold; color: red;"><i class="fa fa-caret-right"></i> Shipment Control (??????)</span>
            <a class="list-group-item" href="{{ url("/index/fg_container_departure") }}"><span class="fa fa-bar-chart"></span> Container Departure <span class="text-purple">コンテナー出発</span></a>
            <a class="list-group-item" href="{{ url("/index/fg_shipment_schedule") }}"><span class="fa fa-table"></span> Shipment Schedule Data <span class="text-purple">出荷スケジュール</span></a>
            <a class="list-group-item" href="{{ url("/index/fg_traceability") }}"><span class="fa fa-table"></span> Traceability <span class="text-purple">完成品追跡</span></a>
            <span style="font-weight: bold; color: red;"><i class="fa fa-caret-right"></i> Shipment Performance (??????)</span>
            <a class="list-group-item" href="{{ url("/index/fg_weekly_summary") }}"><span class="fa fa-table"></span> Weekly Summary <span class="text-purple">週次まとめ</span></a>
            <a class="list-group-item" href="{{ url("/index/fg_monthly_summary") }}"><span class="fa fa-table"></span> Monthly Summary <span class="text-purple">月次まとめ</span></a>
            <span style="font-weight: bold; color: red;"><i class="fa fa-caret-right"></i> Chorei (朝礼)</span>
            <a class="list-group-item" href="{{ url("/index/ch_daily_production_result") }}"><span class="fa fa-bar-chart"></span> Production Summary <span class="text-purple">??????</span></a>
        </div>
    </div>
</div>
</div>
</section>

@stop