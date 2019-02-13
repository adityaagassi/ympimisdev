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
    border:1px solid rgb(211,211,211);
}
table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
.marquee {
    height: 50px;
    overflow: hidden;
}
</style>
@stop
@section('header')
<section class="content-header" style="padding: 0; margin:0;">
    <div class="marquee" data-duplicated='true' data-direction='up' style="text-align: center;">
        <span style="font-size: 22px;" class="text-purple"><span style="font-size:26px;"><b>M</b></span>anufactur<span style="font-size:27px;"><b>i</b></span>ng <span style="font-size:26px;"><b>R</b></span>ealtime <span style="font-size:26px;"><b>A</b></span>cquisition of <span style="font-size:26px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 26px;" class="text-purple">製 造 の リ ア ル タ イ ム 情 報</span></b>
    </p>
</div>
</section>
@endsection

@section('content')

<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-3">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 18px;">
                    <tr>
                        <th>Production Support<br/>生産支援モニ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">HR Management (人材管理)</span>
                            <br>
                            <a href="http://172.17.128.4/myhris/home/presensi" target="blank">
                                <i class="fa fa-caret-right"></i> Manpower Attendance<br>(勤怠管理)
                            </a><br>
                            <a href="http://172.17.128.4/myhris/home/karyawan_graph" target="blank">
                                <i class="fa fa-caret-right"></i> Manpower Database<br>(社員構成)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Plant Maintenance (工場保全管理)</span>
                            <br>
                            <a href="http://172.17.129.99/zed/dashboard/awal" target="blank">
                                <i class="fa fa-caret-right"></i> Overall Equipment Efficiency<br>(稼働率)
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-3">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 18px;">
                    <tr>
                        <th>Raw Material<br/>素材</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-3">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 18px;">
                    <tr>
                        <th>Work In Process<br/>仕掛品</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">WIP Monitoring (仕掛品監視)</span>
                            <br>
                            <a href="{{ url("/index/process_assy_fl") }}" target="blank">
                                <i class="fa fa-caret-right"></i> FL Subassy-Assembly<br>(フルート仮組~組立)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Cek Dimensi Material (寸法測定結果)</span>
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
        <div class="col-md-3">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 18px;">
                    <tr>
                        <th>Finished Goods<br/>完成品</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Finished Goods Control (完成品管理)</span>
                            <br>
                            <a href="{{ url("/index/fg_production") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Production Result<br>(生産実績)
                            </a>
                            <br>
                            <a href="{{ url("/index/fg_stock") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Finished Goods Stock<br>(完成品在庫)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Shipment Control (出荷管理)</span>
                            <br>
                            <a href="{{ url("/index/fg_container_departure") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Container Departure<br>(コンテナー出発)
                            </a>
                            <br>
                            <a href="{{ url("/index/fg_shipment_schedule") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Shipment Schedule Data<br>(出荷スケジュール)
                            </a>
                            <br>
                            <a href="{{ url("/index/fg_traceability") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Traceability<br>(完成品追跡)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Shipment Performance (出荷管理)</span>
                            <br>
                            <a href="{{ url("/index/fg_weekly_summary") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Weekly Summary<br>(週次まとめ)
                            </a>
                            <br>
                            <a href="{{ url("/index/fg_monthly_summary") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Monthly Summary<br>(月次まとめ)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Chorei (朝礼)</span>
                            <br>
                            <a href="{{ url("/index/ch_daily_production_result") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Production Summary<br>(生産まとめ)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Display (表示)</span>
                            <br>
                            <a href="{{ url("/index/dp_production_result") }}" target="blank">
                                <i class="fa fa-caret-right"></i> Daily Production Result<br>(日常生産実績)
                            </a>
                        </td>
                    </tr>
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
            direction: 'up'
        });
    });

</script>
@endsection