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
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
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
  <!-- SELECT2 EXAMPLE -->
  <div class="row">
    <div class="col-md-3">
      <div class="info-box">
        <span class="info-box-icon " style="background-color: rgba(126,86,134,.7);"><i class="fa  fa-paper-plane-o"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">CONSIGNEE & ADDRESS</span>
          <span class="info-box-number">{{$time->destination}}</span>
          <span class="info-box-text">STATUS</span>
          @if($time->status == 1)            
            <span class=" label label-success ">Checked</span>
            @else
            <span class="label label-warning">Unchecked</span>
            @endif
             @if($time->status == 1)
            <span class="info-box-text">INSPECTOR  </span>
          <span class="info-box-number">@if(isset($time->user3->name))
                       <!--  {{$time->created_by}} - --> {{$time->user3->name}}
                        @else
                       <!--  {{$time->created_by}} - --> Not registered
                        @endif
          </span>
          @endif
        </div>
      </div>
    </div>


    <div class="col-md-3 ">
      <div class="info-box">
        <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">SIHHPED FROM  </span>
          <span class="info-box-number">{{$time->shipped_from}}</span>
          <span class="info-box-text">SIHHPED TO  </span>
          <span class="info-box-number">{{$time->shipped_to}}</span>
          <span class="info-box-text">cARRIER  </span>
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

    <div class="col-md-3 ">
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

    <div class="col-md-1 ">
     <a href="{{ url("print/CheckSheet/{$time->id}")}}" class="btn btn-primary btn-lg" style="color:white"><i class="fa fa-print"></i> Print {{ $page }}</a><br><br>
     <a href="{{ url("printsurat/CheckSheet/{$time->id}")}}" class="btn btn-warning btn-lg" style="color:white"><i class="fa fa-print"></i> Print Surat Jalan</a><br><br>
     <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-lg" style="color:white"><i class="fa fa-folder-open-o"></i> Upload {{ $page }}</a>
    </div>

     

  </div>

  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-left "style="background-color: rgba(126,86,134,.7);">
      <li class="active"><a href="#cargo" data-toggle="tab"><i class="fa fa-folder"></i><b> CONDITION OF CARGO</b></a></li>
      <li><a href="#container" data-toggle="tab"><i class="fa fa-folder"></i><b> CONDITION OF CONTAINER </b></a></li>
      <p id="id_checkSheet_master" hidden>{{$time->id_checkSheet}}</p>
      <p id="id_checkSheet_master_id" hidden>{{$time->id}}</p>
    </ul>
    <div class="tab-content no-padding">

      <div class="chart tab-pane active" id="cargo" style="position: relative; ">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin table-bordered table-striped">
              <thead>
                <tr style="background-color: rgba(126,86,134,.7);">
                  <th>DEST</th>
                  <th>INVOICE</th>
                  <th>GMC</th>
                  <th>DESCRIPTION OF GOODS</th>
                  <th>MARKING NO.</th>
                  <th colspan="2">PACKAGE</th>
                  <th colspan="2">QUANTITY</th>
                  <th>Total</th>
                  <th>Confirm</th>
                  <th colspan="2">Diff</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                 @foreach($detail as $nomor => $detail)

                 <td>{{$detail->destination}}</td>
                 <td>{{$detail->invoice}}</td>
                 <td>{{$detail->gmc}}</td>
                 <td>{{$detail->goods}}</td>
                 <td>{{$detail->marking}}</td>
                 <td class="{{$detail->package_set}}">{{$detail->package_qty}}</td>
                 <td>{{$detail->package_set}}</td>
                 <td class="{{$detail->qty_set}}">{{$detail->qty_qty}}</td>
                 <td>{{$detail->qty_set}} </td>
                
                <td><p id="total{{$nomor + 1}}" >{{$detail->package_qty}}</p></td>
                <td><p id="inc{{$nomor + 1}}">{{$detail->confirm}}</p></td>
                <td><p id="diff{{$nomor + 1}}">{{$detail->diff}}</p></td>
                @if( $detail->package_set == "-")
                <td>
                  <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw  fa-close"></i></span> </td>
                  @else
                @if( $detail->diff == "0")
                <td><span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: block;"><i class="fa fa-fw fa-check"></i></span>
                  <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw  fa-close"></i></span> </td>
                  @else
                  <td><span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: block;"><i class="fa fa-fw  fa-close"></i></span>
                    <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw fa-check"></i></span> </td>
                    @endif
                    @endif
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="5" rowspan="2"> <CENTER>REMAIN PALLET & CTN</CENTER></th>                    
                    <th><p id="plte"></p></th>
                    <th>PLT</th>
                    <th><p id="sete"></p></th>
                    <th>SET</th>
                  </tr>
                  <tr>

                    <th><p id="ctne"></p></th>
                    <th>CTN</th>
                    <th><p id="pcse"></p></th>
                    <th>PCS</th>
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
                       $p = 'images/7poin.jpg';
                       @endphp
                       <img src="{{ url($p) }}" class="user-image" alt="7 Poin" align="middle" width="300"></td>
                    <td height="100" ><br>{{$container->area}}<br>&nbsp;</td>
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
                   </thead >
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
                      <p id="inspection1" hidden></p></td>
                      <td> 
                        @if ($inspection->remark1 != '')
                        <TEXTAREA id="remark1" onchange="addInspection2(1)"readonly>{{$inspection->remark1}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark1" onchange="addInspection2(1)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 2 -->
                      <tr>
                      <td height="100">
                        @if ($inspection->inspection2 == 1)
                      <button type="button" id="good2" class="btn btn-block btn-success btn-sm" onclick="good(2)" style="display: block;">GOOD</button>
                       <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm" onclick="ng(2)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm" onclick="ng(2)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good2" class="btn btn-block btn-success btn-sm" onclick="good(2)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection2" hidden></p></td>
                      <td> 
                        @if ($inspection->remark2 != '')
                        <TEXTAREA id="remark2" onchange="addInspection2(2)"readonly>{{$inspection->remark2}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark2" onchange="addInspection2(2)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 3 -->
                      <tr>
                      <td height="100">
                        @if ($inspection->inspection3 == 1)
                      <button type="button" id="good3" class="btn btn-block btn-success btn-sm" onclick="good(3)" style="display: block;">GOOD</button>
                       <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm" onclick="ng(3)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm" onclick="ng(3)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good3" class="btn btn-block btn-success btn-sm" onclick="good(3)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection3" hidden></p></td>
                      <td> 
                        @if ($inspection->remark3 != '')
                        <TEXTAREA id="remark3" onchange="addInspection2(3)"readonly>{{$inspection->remark3}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark3" onchange="addInspection2(3)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 4 -->

                       <tr>
                      <td height="100">
                        @if ($inspection->inspection4 == 1)
                      <button type="button" id="good4" class="btn btn-block btn-success btn-sm" onclick="good(4)" style="display: block;">GOOD</button>
                       <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm" onclick="ng(4)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm" onclick="ng(4)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good4" class="btn btn-block btn-success btn-sm" onclick="good(4)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection4" hidden></p></td>
                      <td> 
                        @if ($inspection->remark4 != '')
                        <TEXTAREA id="remark4" onchange="addInspection2(4)"readonly>{{$inspection->remark4}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark4" onchange="addInspection2(4)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 5 -->
                       <tr>
                      <td height="100">
                        @if ($inspection->inspection5 == 1)
                      <button type="button" id="good5" class="btn btn-block btn-success btn-sm" onclick="good(5)" style="display: block;">GOOD</button>
                       <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm" onclick="ng(5)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm" onclick="ng(5)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good5" class="btn btn-block btn-success btn-sm" onclick="good(5)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection5" hidden></p></td>
                      <td> 
                        @if ($inspection->remark5 != '')
                        <TEXTAREA id="remark5" onchange="addInspection2(5)"readonly>{{$inspection->remark5}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark5" onchange="addInspection2(5)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 6 -->
                       <tr>
                      <td height="100">
                        @if ($inspection->inspection6 == 1)
                      <button type="button" id="good6" class="btn btn-block btn-success btn-sm" onclick="good(6)" style="display: block;">GOOD</button>
                       <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm" onclick="ng(6)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm" onclick="ng(6)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good6" class="btn btn-block btn-success btn-sm" onclick="good(6)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection6" hidden></p></td>
                      <td> 
                        @if ($inspection->remark6 != '')
                        <TEXTAREA id="remark6" onchange="addInspection2(6)"readonly>{{$inspection->remark6}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark6" onchange="addInspection2(6)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 7 -->

                      <tr>
                      <td height="100">
                        @if ($inspection->inspection7 == 1)
                      <button type="button" id="good7" class="btn btn-block btn-success btn-sm" onclick="good(7)" style="display: block;">GOOD</button>
                       <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm" onclick="ng(7)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm" onclick="ng(7)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good7" class="btn btn-block btn-success btn-sm" onclick="good(7)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection7" hidden></p></td>
                      <td> 
                        @if ($inspection->remark7 != '')
                        <TEXTAREA id="remark7" onchange="addInspection2(7)"readonly>{{$inspection->remark7}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark7" onchange="addInspection2(7)"readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 8 -->
                      <tr>
                      <td height="100">
                        @if ($inspection->inspection8 == 1)
                      <button type="button" id="good8" class="btn btn-block btn-success btn-sm" onclick="good(8)" style="display: block;">GOOD</button>
                       <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm" onclick="ng(8)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm" onclick="ng(8)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good8" class="btn btn-block btn-success btn-sm" onclick="good(8)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection8" hidden></p></td>
                      <td> 
                        @if ($inspection->remark8 != '')
                        <TEXTAREA id="remark8" onchange="addInspection2(8)" readonly>{{$inspection->remark8}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark8" onchange="addInspection2(8)" readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>

                      <!-- 9 -->
                      <tr>
                      <td height="100">
                        @if ($inspection->inspection9 == 1)
                      <button type="button" id="good9" class="btn btn-block btn-success btn-sm" onclick="good(9)" style="display: block;">GOOD</button>
                       <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm" onclick="ng(9)" style="display: none;">NOT GOOD</button>
                      @else
                      <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm" onclick="ng(9)" style="display: block;">NOT GOOD</button>
                      <button type="button" id="good9" class="btn btn-block btn-success btn-sm" onclick="good(9)" style="display: none;">GOOD</button>
                      @endif
                      <p id="inspection9" hidden></p></td>
                      <td> 
                        @if ($inspection->remark9 != '')
                        <TEXTAREA id="remark9" onchange="addInspection2(9)" readonly>{{$inspection->remark9}}</TEXTAREA>
                        @else
                        <TEXTAREA id="remark9" onchange="addInspection2(9)" readonly></TEXTAREA>
                         @endif
                      </td>
                      </tr>
                    @endforeach
                   
                 </table>
               </div>
               </div>
             </div>
           </div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id ="importForm" method="post" action="{{ url('importDetail/CheckSheet') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [Destination][Invoice][Container Number][GMC][Goods][Marking No][Package Qty][Package Set][Qty Qty][Qty Set]<br>
          Sample: <a href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a> Code: #Add
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="check_sheet_import" id="InputFile" accept="text/plain"></center>
            <input type="text" name="master_id" value="{{$time->id_checkSheet}}" hidden>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @endsection

@section('scripts')
<script >
  
jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $('#rows1').removeAttr('hidden');
        var count = document.getElementById("count").value;
        document.getElementById("rows1").rowSpan = count;
        var plt = 0;
        var ctn = 0;
        var set = 0;
        var pcs = 0;
        $(".PLT").each(function() {
          plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });
        $('#plte').html("" + plt);

        $(".CTN").each(function() {
          ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });
        $('#ctne').html("" + ctn);

        $(".SET").each(function() {
          set += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });
        $('#sete').html("" + set);

        $(".PCS").each(function() {
          pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
        });
        $('#pcse').html("" + pcs);

    
});
</script>
@stop