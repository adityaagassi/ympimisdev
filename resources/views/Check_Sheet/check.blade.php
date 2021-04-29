@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
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
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(211,211,211);
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
</style>
@stop
@section('stylesheets')
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>
@stop
@section('header')
<section class="content-header">
    <h1>
        {{ $page }}
        <small>it all starts here</small>
    </h1>
    <ol class="breadcrumb">
        {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">Blank page</li> --}}
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    @if ($errors->has('password'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Alert!</h4>
        {{ $errors->first() }}
    </div>   
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa  fa-paper-plane-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">CONSIGNEE & ADDRESS</span>
                    <span class="info-box-number">{{$time->destination}}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 ">
            <div class="info-box">
                <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">SHIPPED FROM  </span>
                    <span class="info-box-number">{{$time->shipped_from}}</span>
                    <span class="info-box-text">SHIPPED TO  </span>
                    <span class="info-box-number">{{$time->shipped_to}}</span>
                    <span class="info-box-text">CARRIER  </span>
                    <span class="info-box-number">@if(isset($time->shipmentcondition->shipment_condition_name))
                        {{$time->shipmentcondition->shipment_condition_name}}
                        @else
                        -
                    @endif</span>
                    <span class="info-box-text">ON OR ABOUT  </span>
                    <span class="info-box-number">{{date('d-M-Y', strtotime($time->etd_sub))}}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-envelope-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">INVOICE NO  </span>
                    <span class="info-box-number">{{$time->invoice}}</span>
                    <span class="info-box-text">DATE   </span>
                    <span class="info-box-number">{{date('d-M-Y', strtotime($time->Stuffing_date))}}</span>
                    <span class="info-box-text">PAYMENT  </span>
                    <span class="info-box-number">{{$time->payment}}</span>
                    <span class="info-box-text">SHIPPER  </span>
                    <span class="info-box-number">PT YMPI</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 ">
            <div class="info-box">
                <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-user"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">INPUTOR  </span>
                    <span class="info-box-number">
                        @if(isset($time->user2->name))
                        {{$time->user2->name}}
                        @else
                        Not registered
                        @endif
                    </span>
                    <span class="info-box-text">DATE   </span>
                    <span class="info-box-number">{{date('d-M-Y')}}</span><br>

                    <form method="post" action="{{ url('save/CheckSheet') }}" name="kirim" id="kirim">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" />
                        <input type="text" name="id" value="{{$time->id_checkSheet}}" hidden>
                        <input type="text" name="status" value="1" hidden>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <P class="pull-right">
                <a href="{{ url("index/CheckSheet")}}">
                    <button class="btn btn-warning btn-lg" style="color:white"><i class=" fa fa-backward "></i> Back</button> &nbsp;
                </a>
                <button class="btn btn-success btn-lg" style="color:white" onclick="save()"><i class="fa fa-save "></i> Save</button>&nbsp;&nbsp;&nbsp;
            </P>
        </div>
    </div>

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-left">
            <li class="active">
                <a href="#seal" data-toggle="tab">
                    <b>1. SEAL INFORMATION</b>
                </a>
            </li>
            <li>
                <a href="#container" data-toggle="tab">
                    <b>2. CONDITION OF CONTAINER </b>
                </a>
            </li>
            <li>
                <a href="#cargo" data-toggle="tab">
                    <b>3. CONDITION OF CARGO</b>
                </a>
            </li>
            <li>
                <a href="#closure" data-toggle="tab">
                    <b>4. CONTAINER CLOSURE </b>
                </a>
            </li>
        </ul>

        <p id="id_checkSheet_master" hidden>{{$time->id_checkSheet}}</p>
        <p id="id_checkSheet_master_id" hidden>{{$time->id}}</p>
        <input type="hidden" id="driver_photo_hidden" value="{{ $photo }}">
        <input type="hidden" id="seal_photo_hidden" value="{{ $seal_photo }}">
        <input type="hidden" id="seal_number_hidden" value="{{ $time->seal_number }}">
        <input type="hidden" id="countainer_number_hidden" value="{{ $time->countainer_number }}">
        <input type="hidden" id="shipment_condition" value="{{ $time->carier }}">

        <div class="tab-content no-padding">

            <div class="chart tab-pane active" id="seal" style="position: relative;">
                <div class="box-body">
                    <div class="col-xs-8 col-xs-offset-2" id="driver_sea">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th colspan="2">SEAL INSPECTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th style="vertical-align: middle;">DRIVER NAME</th>
                                        <th>
                                            <input type="text" name="driver_name" id="driver_name" class="form-control" value="{{$time->driver_name}}" onchange="nomor('driver_name',this.value)">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">DRIVER PHOTO</th>
                                        <th>
                                            <input type="file" class="file" style="display:none" onchange="readURL(this);" id="input_photo">
                                            <button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)" style="font-size: 1.5vw; width: 300px; height: 200px;"><i class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo Driver</button>
                                            <img width="150px" id="driver_photo" src="" onclick="buttonImage(this)" style="display: none; width: 300px; height: 200px;" alt="your image"/>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">SEAL NO.</th>
                                        <th>
                                            <input type="text" name="seal_number" id="seal_number" class="form-control" value="{{$time->seal_number}}" onchange="nomor('seal_number',this.value)">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">CONTAINER NO.</th>
                                        <th>
                                            <input type="text" name="countainer_number" id="countainer_number" class="form-control" value="{{$time->countainer_number}}" onchange="nomor('countainer_number',this.value)">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">NO POL</th>
                                        <th>
                                            <input type="text" name="no_pol" id="no_pol" class="form-control" value="{{$time->no_pol}}" onchange="nomor('no_pol',this.value)">
                                        </th>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="col-xs-8 col-xs-offset-2" id="driver_non_sea" style="margin-bottom: 100px; margin-top: 100px;">
                        <center>
                            <h1 style="text-transform: uppercase;">Seal information is only for shipping condition by Sea</h1>
                        </center>
                    </div>
                </div>
            </div>

            <div class="chart tab-pane" id="cargo" style="position: relative; ">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <div class="input-group margin">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info btn-lg"><i class="fa fa-search"></i></button>
                                </div>
                                <input type="text" class="form-control input-lg" name="myInput" id="myInput" onkeyup="cari()" placeholder="Search ...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table no-margin table-bordered table-striped" id="tabel1">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th>DEST</th>
                                    <th>INVOICE</th>
                                    <th>GMC</th>
                                    <th>DESCRIPTION OF GOODS</th>
                                    <th>FSTK</th>
                                    <th>MARKING NO.</th>
                                    <th colspan="2">PACKAGE</th>
                                    <th colspan="2">QUANTITY</th>
                                    <th colspan="2">Check</th>
                                    <th>Total</th>
                                    <th>Confirm</th>
                                    <th colspan="3">Diff</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach($detail as $nomor => $detail)
                                    <input type="text" id="countdetail" value="{{$loop->count}}" hidden></input>
                                    <td width="5%">{{$detail->destination}}</td>
                                    <td width="8%">{{$detail->invoice}}</td>
                                    <td width="5%">{{$detail->gmc}}</td>
                                    <td>{{$detail->goods}}</td>
                                    <td>{{$detail->stock}}</td>
                                    <td width="2%">{{$detail->marking}}</td>
                                    @if($detail->package_set =="PL")
                                    <td class="PLT" width="5%">{{$detail->package_qty}}</td>
                                    <td class="PLTT" hidden>{{$detail->confirm}}</td>
                                    @elseif($detail->package_set =="C/T")
                                    <td class="CTN" width="5%">{{$detail->package_qty}}</td>
                                    <td class="CTNT" hidden>{{$detail->confirm}}</td>
                                    @else
                                    <td class="{{$detail->package_qty}}" width="5%">{{$detail->package_qty}}</td>
                                    @endif
                                    <td width="2%">{{$detail->package_set}}</td>
                                    <td class="{{$detail->qty_set}}" width="5%">{{$detail->qty_qty}}</td>
                                    <td width="2%">{{$detail->qty_set}} </td>
                                    <td width="8%">
                                        @if( $detail->package_set == "-")
                                        <button class="btn btn-block btn-primary btn-sm" id="like{{$nomor + 1}}" onclick="okbara({{$nomor + 1}}); masuk('0','{{$detail->id}}');totalconfirm()" style="display: block;"> Check</button>
                                        @else
                                        <button class="btn btn-block btn-primary btn-sm" id="like{{$nomor + 1}}" onclick="add({{$nomor + 1}}); minusdata({{$nomor + 1}}); hide({{$nomor + 1}}); update({{$nomor + 1}},{{$detail->id}}); totalconfirm()" style="display: block;"> Check</button>
                                        @endif
                                    </td>
                                    <td width="8%">
                                        @if( $detail->package_set == "-")
                                        <button class="btn btn-block btn-warning btn-sm" id="like{{$nomor + 1}}" onclick="ngbara({{$nomor + 1}});  masuk('1','{{$detail->id}}');totalconfirm()" style="display: block;"> Uncheck</button>
                                        @else
                                        <button class="btn btn-block btn-warning btn-sm" id="like{{$nomor + 1}}" onclick="minus({{$nomor + 1}}); minusdata({{$nomor + 1}}); hide({{$nomor + 1}}); update({{$nomor + 1}},{{$detail->id}}); totalconfirm();" style="display: block;"> Uncheck</button>
                                        @endif
                                    </td>
                                    <td width="2%">
                                        <p id="total{{$nomor + 1}}" >{{$detail->package_qty}}</p>
                                    </td>
                                    @if($detail->package_set =="PL")
                                    <td width="2%" class="PLTTT"><p id="inc{{$nomor + 1}}">{{$detail->confirm}}</p></td>
                                    @elseif($detail->package_set =="C/T")
                                    <td width="2%" class="CTNTT"><p id="inc{{$nomor + 1}}">{{$detail->confirm}}</p></td>
                                    @else
                                    <td width="2%"><p id="inc{{$nomor + 1}}">{{$detail->confirm}}</p></td>
                                    @endif
                                    <td width="5%"><p id="diff{{$nomor + 1}}">{{$detail->diff}}</p></td>
                                    @if( $detail->package_set == "-" )
                                    <td width="5%">
                                        @if( $detail->bara == "1" )
                                        <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw fa-check"></i></span>
                                        <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: block;"><i class="fa fa-fw  fa-close"></i></span> 
                                        @elseif( $detail->bara == "0" )
                                        <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: block;"><i class="fa fa-fw fa-check"></i></span>
                                        <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw  fa-close"></i></span> 
                                        @endif
                                    </td>
                                    @else
                                    @if( $detail->diff == "0" )
                                    <td width="5%">
                                        <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: block;">
                                            <i class="fa fa-fw fa-check"></i>
                                        </span>
                                        <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: none;">
                                            <i class="fa fa-fw  fa-close"></i>
                                        </span>
                                    </td>
                                    @else
                                    <td width="5%">
                                        <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: block;">
                                            <i class="fa fa-fw  fa-close"></i>
                                        </span>
                                        <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: none;">
                                            <i class="fa fa-fw fa-check"></i>
                                        </span>
                                    </td>
                                    @endif
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background-color: RGB(252, 248, 227);">
                                <tr>
                                    <th colspan="6" rowspan="2"> <CENTER>REMAIN PALLET & CTN</CENTER></th>                    
                                    <th><p id="plte"></p></th>
                                    <th>PL</th>
                                    <th><p id="sete"></p></th>
                                    <th>SET</th>
                                    <th colspan="2" rowspan="2">Confirm</th>
                                    <th>PL</th>
                                    <th><p id="pltet"></p></th>                   
                                    <th rowspan="2">Diff</th>
                                    <th><p id="pltem"></p></th>   
                                </tr>
                                <tr>
                                    <th><p id="ctne"></p></th>
                                    <th>C/T</th>
                                    <th><p id="pcse"></p></th>
                                    <th>PC</th>
                                    <th>C/T</th>
                                    <th><p id="ctnet"></p></th>                    
                                    <th><p id="ctntem"></p></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="chart tab-pane" id="container" style="position: relative;">
                <div class="box-body">
                    <div class="col-xs-8">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th colspan="2">AREA OF INSPECTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($container as $nomor => $container)
                                    <tr>
                                        <input type="text" id="count" value="{{$loop->count}}" hidden></input>
                                        <td id="rows{{$nomor + 1}}" hidden>
                                            @php
                                            $p = 'images/7poin.png';
                                            @endphp
                                            <img src="{{ url($p) }}" class="user-image" alt="7 Poin" align="middle" width="300">
                                        </td>
                                        <td height="100">
                                            <br>{{$container->area}}<br>&nbsp;
                                        </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <th>ACCEPTABLE</th>
                                    <th>REMARK</th>
                                </thead>
                                @foreach($inspection as $nomor => $inspection)
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection1 == 1)
                                        <button type="button" id="good1" class="btn btn-block btn-success btn-sm" onclick="good(1)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng1" class="btn btn-block btn-danger btn-sm" onclick="ng(1)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng1" class="btn btn-block btn-danger btn-sm" onclick="ng(1)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good1" class="btn btn-block btn-success btn-sm" onclick="good(1)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection1" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark1 != '')
                                        <TEXTAREA id="remark1" onchange="addInspection2(1)">{{$inspection->remark1}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark1" onchange="addInspection2(1)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection2 == 1)
                                        <button type="button" id="good2" class="btn btn-block btn-success btn-sm" onclick="good(2)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm" onclick="ng(2)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm" onclick="ng(2)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good2" class="btn btn-block btn-success btn-sm" onclick="good(2)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection2" hidden></p>
                                    </td>
                                    <td> 
                                        @if ($inspection->remark2 != '')
                                        <TEXTAREA id="remark2" onchange="addInspection2(2)">{{$inspection->remark2}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark2" onchange="addInspection2(2)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection3 == 1)
                                        <button type="button" id="good3" class="btn btn-block btn-success btn-sm" onclick="good(3)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm" onclick="ng(3)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm" onclick="ng(3)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good3" class="btn btn-block btn-success btn-sm" onclick="good(3)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection3" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark3 != '')
                                        <TEXTAREA id="remark3" onchange="addInspection2(3)">{{$inspection->remark3}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark3" onchange="addInspection2(3)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection4 == 1)
                                        <button type="button" id="good4" class="btn btn-block btn-success btn-sm" onclick="good(4)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm" onclick="ng(4)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm" onclick="ng(4)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good4" class="btn btn-block btn-success btn-sm" onclick="good(4)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection4" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark4 != '')
                                        <TEXTAREA id="remark4" onchange="addInspection2(4)">{{$inspection->remark4}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark4" onchange="addInspection2(4)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection5 == 1)
                                        <button type="button" id="good5" class="btn btn-block btn-success btn-sm" onclick="good(5)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm" onclick="ng(5)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm" onclick="ng(5)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good5" class="btn btn-block btn-success btn-sm" onclick="good(5)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection5" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark5 != '')
                                        <TEXTAREA id="remark5" onchange="addInspection2(5)">{{$inspection->remark5}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark5" onchange="addInspection2(5)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection6 == 1)
                                        <button type="button" id="good6" class="btn btn-block btn-success btn-sm" onclick="good(6)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm" onclick="ng(6)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm" onclick="ng(6)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good6" class="btn btn-block btn-success btn-sm" onclick="good(6)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection6" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark6 != '')
                                        <TEXTAREA id="remark6" onchange="addInspection2(6)">{{$inspection->remark6}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark6" onchange="addInspection2(6)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection7 == 1)
                                        <button type="button" id="good7" class="btn btn-block btn-success btn-sm" onclick="good(7)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm" onclick="ng(7)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm" onclick="ng(7)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good7" class="btn btn-block btn-success btn-sm" onclick="good(7)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection7" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark7 != '')
                                        <TEXTAREA id="remark7" onchange="addInspection2(7)">{{$inspection->remark7}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark7" onchange="addInspection2(7)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection8 == 1)
                                        <button type="button" id="good8" class="btn btn-block btn-success btn-sm" onclick="good(8)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm" onclick="ng(8)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm" onclick="ng(8)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good8" class="btn btn-block btn-success btn-sm" onclick="good(8)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection8" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark8 != '')
                                        <TEXTAREA id="remark8" onchange="addInspection2(8)">{{$inspection->remark8}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark8" onchange="addInspection2(8)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td height="100">
                                        @if ($inspection->inspection9 == 1)
                                        <button type="button" id="good9" class="btn btn-block btn-success btn-sm" onclick="good(9)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm" onclick="ng(9)" style="display: none;">NOT GOOD</button>
                                        @else
                                        <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm" onclick="ng(9)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good9" class="btn btn-block btn-success btn-sm" onclick="good(9)" style="display: none;">GOOD</button>
                                        @endif
                                        <p id="inspection9" hidden></p>
                                    </td>
                                    <td>
                                        @if ($inspection->remark9 != '')
                                        <TEXTAREA id="remark9" onchange="addInspection2(9)">{{$inspection->remark9}}</TEXTAREA>
                                        @else
                                        <TEXTAREA id="remark9" onchange="addInspection2(9)"></TEXTAREA>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart tab-pane" id="closure" style="position: relative;">
                <div class="box-body">
                    <div class="col-xs-8 col-xs-offset-2" id="seal_sea">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th colspan="2">CLOSURE CONTAINER INSPECTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th style="vertical-align: middle;">CONTAINER NO.</th>
                                        <th>
                                            <input type="text" name="closure_countainer_number" id="closure_countainer_number" class="form-control" onchange="closureNomor('closure_countainer_number',this.value)">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">SEAL NO.</th>
                                        <th>
                                            <input type="text" name="closure_seal_number" id="closure_seal_number" class="form-control" onchange="closureNomor('closure_seal_number',this.value)">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle;">SEAL PHOTO</th>
                                        <th>
                                            <input type="file" class="file" style="display:none" onchange="readSeal(this);" id="input_seal">
                                            <button class="btn btn-primary btn-lg" id="btnSeal" onclick="buttonImage(this)" style="font-size: 1.5vw; width: 300px; height: 200px;"><i class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo Seal</button>
                                            <img width="150px" id="seal_photo" src="" onclick="buttonImage(this)" style="display: none; width: 300px; height: 200px;" alt="your image"/>
                                        </th>
                                    </tr>

                                </tbody>
                                <tfoot id="closure_foot">
                                    <tr style="background-color: rgb(252, 248, 227);">
                                        <th colspan="2">
                                            <button class="btn btn-success btn-lg" id="btnClosure" onclick="closure()">Submit</button>

                                        </th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                    <div class="col-xs-8 col-xs-offset-2" id="seal_non_sea" style="margin-bottom: 100px; margin-top: 100px;">
                        <center>
                            <h1 style="text-transform: uppercase;">Closure information is only for shipping condition by Sea</h1>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</section>

<div class="modal modal-warning fade" id="ALERT">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Warning</h4>
                </div>
                <div class="modal-body">
                    <p>Data Not Match</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        showDriverPhoto();
        showSealPhoto();
        shipmentCondition();

        $('#rows1').removeAttr('hidden');
        var count = document.getElementById("count").value;
        document.getElementById("rows1").rowSpan = count;
        var plt = 0;
        var ctn = 0;
        var set = 0;
        var pcs = 0;
        var pltt = 0;
        var ctnt = 0;
        $(".PLT").each(function() {
            plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#plte').html("" + plt);

        $(".CTN").each(function() {
            ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#ctne').html("" + ctn);

        $(".PLTT").each(function() {
            pltt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#pltet').html("" + pltt);

        $(".CTNT").each(function() {
            ctnt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#ctnet').html("" + ctnt);

        var pltem = pltt-plt ;
        var ctntem =ctnt- ctn ;
        $('#pltem').html("" + pltem);
        $('#ctntem').html("" + ctntem);

        $(".SET").each(function() {
            set += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#sete').html("" + set);

        $(".PC").each(function() {
            pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#pcse').html("" + pcs);

    });

    function shipmentCondition() {
        var shipment_condition = $("#shipment_condition").val();
        console.log(shipment_condition);
        if(shipment_condition == 'C1'){
            $('#driver_sea').show();
            $('#driver_non_sea').hide();

            $('#seal_sea').show();
            $('#seal_non_sea').hide();
        }else{
            $('#driver_sea').hide();
            $('#driver_non_sea').show();

            $('#seal_sea').hide();
            $('#seal_non_sea').show();
        }
    }

    function showSealPhoto() {
        var photo = $("#seal_photo_hidden").val();
        var seal = $("#seal_number_hidden").val();
        var countainer = $("#countainer_number_hidden").val();

        if(photo != ''){
            $("#closure_foot").hide();

            $("#seal_photo").show();
            $("#btnSeal").hide();
            $("#seal_photo").attr('src', photo);

            $("#driver_name").prop("disabled", true);
            $("#seal_number").prop("disabled", true);
            $("#countainer_number").prop("disabled", true);
            $("#no_pol").prop("disabled", true);

            $("#closure_seal_number").prop("disabled", true);
            $("#closure_countainer_number").prop("disabled", true);

            $("#closure_seal_number").val(seal);
            $("#closure_countainer_number").val(countainer);
        }else{
            $("#closure_seal_number").prop("disabled", false);
            $("#closure_countainer_number").prop("disabled", false);

            $("#driver_name").prop("disabled", false);
            $("#seal_number").prop("disabled", false);
            $("#countainer_number").prop("disabled", false);
            $("#no_pol").prop("disabled", false);

            $("#closure_foot").show();
            $("#seal_photo").hide();
            $("#btnSeal").show();

        }

    }

    function showDriverPhoto() {
        var photo = $("#driver_photo_hidden").val();

        if(photo != ''){
            $("#driver_photo").show();
            $("#btnImage").hide();
            $("#driver_photo").attr('src', photo);
        }else{
            $("#driver_photo").hide();
            $("#btnImage").show();
        }

    }

    function closure(){
        var insert = true;

        if($('#input_seal').prop('files')[0] == undefined){
            insert = false;
        }

        if($('#closure_seal_number').val() == ''){
            insert = false;
        }

        if($('#closure_countainer_number').val() == ''){
            insert = false;
        }

        if (insert) {
            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

            var formData = new FormData();

            formData.append('id_checkSheet', id_checkSheet);    


            formData.append('file_datas', $('#input_seal').prop('files')[0]);
            var file = $('#input_seal').val().replace(/C:\\fakepath\\/i, '').split(".");
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);

            $.ajax({
                url:"{{ url('import/seal_photo') }}",
                method:"POST",
                data:formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function (result, status, xhr) {
                    $('#closure_foot').hide();
                    $("#seal_photo_hidden").val(result.photo);
                    openSuccessGritter("Success", "Closure Photo Saved Successfully");
                },
                error: function (result, status, xhr) {
                    console.log(result.message);
                },
            })
        }else{
            openErrorGritter("Error", "Complete Field Before Submit");
        }
    }

    function readSeal(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var img = $(input).closest("th").find("img");
                $(img).show();
                $(img).attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);

        }

        $(input).closest("th").find("button").hide();
    }

    function buttonImage(elem) {
        $(elem).closest("th").find("input").click();
    }

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var img = $(input).closest("th").find("img");
                $(img).show();
                $(img).attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);

        }

        $(input).closest("th").find("button").hide();
        saveImage(input);
    }


    function saveImage(input) {
        var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

        var formData = new FormData();
        formData.append('file_datas', $(input).prop('files')[0]);
        var file = $(input).val().replace(/C:\\fakepath\\/i, '').split(".");

        formData.append('id_checkSheet', id_checkSheet);    
        formData.append('extension', file[1]);
        formData.append('photo_name', file[0]);


        $.ajax({
            url:"{{ url('import/driver_photo') }}",
            method:"POST",
            data:formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (result, status, xhr) { 
                openSuccessGritter("Success", "Driver's Photo Saved Successfully");
            },
            error: function (result, status, xhr) {
                console.log(result.message);
            },
        })
    }

    function cari() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("tabel1");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    function add(id){
        var a = id;
        var total =parseInt(document.getElementById("total"+a).innerHTML);
        var i =parseInt(document.getElementById("inc"+a).innerHTML);
        var aa = i+1;
        if (aa <= total){
            document.getElementById('inc'+a).innerHTML = aa;
        }
    }

    function minus(id){
        var a = id;
        var i =parseInt(document.getElementById("inc"+a).innerHTML);
        var aa = i-1;
        if (i > 0){
            document.getElementById('inc'+a).innerHTML = aa;
        }

    }

    function minusdata(id){
        var a = id;
        var total =parseInt(document.getElementById("total"+a).innerHTML);
        var confirm =parseInt(document.getElementById("inc"+a).innerHTML);
        var aa = confirm - total ;
        var aaa= 0;
        if(aa > 0){
            aaa = " + " + aa;
        }else{
            aaa = " " + aa;
        }
        document.getElementById('diff'+a).innerHTML = aaa;
    }

    function hide(id){
        var a = id;
        var confirm =parseInt(document.getElementById("diff"+a).innerHTML);
        var y = document.getElementById("y"+a);
        var n = document.getElementById("n"+a);
        if (confirm == 0) {
            y.style.display = "block";
            n.style.display = "none";
        } else {
            y.style.display = "none";
            n.style.display = "block";
        }
    }

    function okbara(id){
        var a = id;
        var confirm =parseInt(document.getElementById("diff"+a).innerHTML);
        var y = document.getElementById("y"+a);
        var n = document.getElementById("n"+a);

        y.style.display = "block";
        n.style.display = "none";

    }

    function ngbara(id){
        var a = id;
        var confirm =parseInt(document.getElementById("diff"+a).innerHTML);
        var y = document.getElementById("y"+a);
        var n = document.getElementById("n"+a);

        y.style.display = "none";
        n.style.display = "block";

    }

    function update(id, id2){
        var a = id;
        var id_detail = id2;
        var confirm =parseInt(document.getElementById("inc"+a).innerHTML);
        var diff =document.getElementById("diff"+a).innerHTML;
        var data = {
            id_detail:id_detail,
            confirm:confirm,
            diff:diff,
        }

        $.post('{{ url("update/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });
    }

    function good(id){
        var a = id;
        document.getElementById('inspection'+a).innerHTML = "0";
        document.getElementById("good"+a).style.display = "none";
        document.getElementById("ng"+a).style.display = "block";
        var confirm =parseInt(document.getElementById("inspection"+a).innerHTML);
        var inspection = "inspection"+a;
        var id =document.getElementById("id_checkSheet_master").innerHTML;
        var data = {
            confirm:confirm,
            inspection:inspection,
            id:id,
        }

        $.post('{{ url("addDetail/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });
    }


    function ng(id){
        var a = id;
        document.getElementById('inspection'+a).innerHTML = "1";
        document.getElementById("good"+a).style.display = "block";
        document.getElementById("ng"+a).style.display = "none";
        var confirm =parseInt(document.getElementById("inspection"+a).innerHTML);
        var inspection = "inspection"+a;
        var id =document.getElementById("id_checkSheet_master").innerHTML;
        var data = {
            confirm:confirm,
            inspection:inspection,
            id:id,
        }

        $.post('{{ url("addDetail/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });
    }

    function addInspection(){
        var id =document.getElementById("id_checkSheet_master").innerHTML;
        var data = {
            id:id,
        }

        $.post('{{ url("add/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });
    }


    function addInspection2(id){
        var a = id;
        var text = document.getElementById("remark"+a).value;
        var id =document.getElementById("id_checkSheet_master").innerHTML;
        var remark = "remark"+a;
        var data = {
            remark:remark,
            text:text,
            id:id,
        }
        $.post('{{ url("addDetail2/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });

    }

    function nomor(id,nama){
        var kolom = id;
        var isi =nama;
        var id =document.getElementById("id_checkSheet_master").innerHTML;

        var data = {
            kolom:kolom,
            isi:isi,
            id:id,
        }

        $.post('{{ url("nomor/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
            if(result.status){
                $('#'+result.id).val(result.value);
            }
        });

    }

    function masuk(isi,id){

        var isi =isi;
        var id =id;
        var id_master =document.getElementById("id_checkSheet_master").innerHTML;

        var data = {
            isi:isi,
            id:id,
            id_master:id_master,
        }

        $.post('{{ url("bara/CheckSheet") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
        });

    }

    function save(){
        var count = document.getElementById("countdetail").value;
        var ctn = 0;
        var plt = 0;

        $(".CTN").each(function() {
            ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $(".PLT").each(function() {
            plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        var jumlah = ctn + plt;
        var semua = 0;

        for (var i = 1; i <= count; i++) {
            var idt= "inc"+i;        
            var a = document.getElementById("inc"+i).innerHTML;
            semua += parseInt(a);
        }

        if(jumlah != semua){
            $('#ALERT').modal('show');
            return false;
        }

        var shipment_condition = $("#shipment_condition").val();
        if(shipment_condition == 'C1'){
            var photo = $("#seal_photo_hidden").val();

            if(photo == ''){
                $('#ALERT').modal('show');
                return false;
            }
        }


        document.getElementById("kirim").submit(); 

    }

    function totalconfirm(){
        var pltt = 0;
        var ctnt = 0;
        var plt = 0;
        var ctn = 0;

        $(".PLTTT").each(function() {
            pltt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#pltet').html("" + pltt);

        $(".CTNTT").each(function() {
            ctnt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $('#ctnet').html("" + ctnt);

        $(".PLT").each(function() {
            plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        $(".CTN").each(function() {
            ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });

        var pltem = pltt-plt ;
        var ctntem =ctnt- ctn ;
        $('#pltem').html("" + pltem);
        $('#ctntem').html("" + ctntem);

    }

    function closureNomor(id,nama){
        var kolom = id;
        var isi =nama;
        var id =document.getElementById("id_checkSheet_master").innerHTML;

        var data = {
            kolom:kolom,
            isi:isi,
            id:id,
        }

        $.post('{{ url("closure/check_checksheet") }}', data, function(result, status, xhr){
            if(result.status){
                openSuccessGritter("Success", '');
            }else{
                $('#'+kolom).val('');
                openErrorGritter("Error", result.message);
            }
        });

    }

    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '3000'
        });
    }

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '3000'
        });
    }


</script>
@stop