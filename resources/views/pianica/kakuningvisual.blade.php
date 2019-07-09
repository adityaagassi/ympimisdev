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
  tr>th{
    text-align:center;
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

  b{
    font-size: 25px;
  }
  table{
    font-size: 25px;
    font-weight: bold;
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
   <span class="text-purple"> 外観確認詳細</span>
  </h1>

  <ol class="breadcrumb">
    {{-- <a href="javascript:void(0)"  data-toggle="modal" data-target="#edit" class="btn btn-warning btn-sm">Input</a> --}}
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
    <div class="col-xs-6 ">
     <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
        <b>Tag RFID</b> 
        {{-- <b>RFID</b> --}} <b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b><br>
        <input type="text" name="rfid" id="rfid" class="form-control"  autofocus style="text-align: center; font-size: 30px; height: 45px" placeholder="RFID"><br>

        <input type="text" name="rfid2" id="rfid2" hidden="">
        
        <center><button class="btn btn-lg btn-primary" onclick="rf()">Change</button></center> <br>
      </div>
    </div>

  </div>
  <div class="col-xs-6">
    <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
       <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()">Change Operator Kakuning Visual</button>         
       <span class="info-box-text" style="font-size: 25px">OPERATOR Kakuning Visual</span>
       <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b> 
       <div class="table-responsive">
        <table width="100%"  class="table table-bordered table-striped" border="0" style="margin :0px">
          <tr>
            <td colspan="2" style="background-color: rgba(126,86,134,.7); color: white;" >Total</td>
            <td  style="background-color: rgba(126,86,134,.7); color: white;" >OK</td>
            <td  style="background-color: rgba(126,86,134,.7); color: white;" >NG</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 45px; background-color: #F0FFF0;" valign="middle" id="total" >0</td>
            <td  style="font-size: 45px; background-color: #F0FFF0;" valign="middle" id="bagus_total" >0</td>
            <td  style="font-size: 45px; background-color: pink;" valign="middle" id="ng_total" >0</td>
          </tr>
        </table>
      </div>
    </div>
  </div>   
</div>
</div>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-solid">
      <div class="box box-body"> 
       <!-- Custom Tabs -->
       <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="font-size: 25px">
          <li class="active" style="width: 14%"><a href="#tab_1" data-toggle="tab">Frame Assy  </a></li>
          <li style="width: 14%"><a href="#tab_2" data-toggle="tab">Cover R/L</a></li>
          <li style="width: 14%"><a href="#tab_3" data-toggle="tab">Cover Lower</a></li>
          <li style="width: 14%"><a href="#tab_4" data-toggle="tab">Handle</a></li>
          <li style="width: 14%"><a href="#tab_5" data-toggle="tab">Button</a></li>
          <li style="width: 14%"><a href="#tab_6" data-toggle="tab">Pianica</a></li>

          <li ><button class="btn btn-lg btn-warning pull-right" onclick="simpan()"><i class="fa fa-save"></i>&nbsp;Save</button></a></li>


        </ul>
        <div class="tab-content">

          <div class="tab-pane active" id="tab_1">
            <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span> -->
            <input id="ng" hidden></input><br>
           <div class="table-responsive">
            <table class="table no-margin table-bordered table-striped" border="0" id="Frame1">
             <tr style="background-color: rgba(126,86,134,.7); color: white;" >
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index <=9)                
              <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$nomor+1}}</th>
              @endif
              @endforeach                                  
            </tr> 
            <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index <=9)                
              <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
              @endif
              @endforeach                                  
            </tr>              
            <tr style="background-color: rgba(126,86,134,.7); color: white;" >
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index <=9)                
              <th  align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
              @endif
              @endforeach                                  
            </tr>
            <tr style="background-color: #F0FFF0">
              @foreach($ng_list as $nomor => $ng)
              @if($ng->location =="Frame Assy" && $loop->index <=9)
              <td Style="font-size: 45px" valign="middle" class="f1">0</td>
              @endif
              @endforeach                                  
            </tr>                
          </table> <BR>

          <table class="table no-margin table-bordered table-striped" border="0" id="Frame2">
           <tr style="background-color: rgba(126,86,134,.7); color: white;" >
            @foreach($ng_list as $nomor => $ng)
            @if($ng->location =="Frame Assy" && $loop->index >9)                
            <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$nomor+1}}</th>
            @endif
            @endforeach                                  
          </tr>  
          <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
            @foreach($ng_list as $nomor => $ng)
            @if($ng->location =="Frame Assy" && $loop->index >9)                
            <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
            @endif
            @endforeach                                  
          </tr>                 
          <tr style="background-color: rgba(126,86,134,.7); color: white;" >
            @foreach($ng_list as $nomor => $ng)
            @if($ng->location =="Frame Assy" && $loop->index >9)                
            <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
            @endif
            @endforeach                                  
          </tr>
          <tr style="background-color: #F0FFF0">
            @foreach($ng_list as $nomor => $ng)
            @if($ng->location =="Frame Assy" && $loop->index >9)
            <td Style="font-size: 45px" valign="middle" class="f1">0</td>
            @endif
            @endforeach                                  
          </tr>                
        </table> <BR> 

      </div>
    </div>

    <!-- /.tab-pane -->
    <div class="tab-pane" id="tab_2">
     <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
     <div class="table-responsive">
      <table class="table no-margin table-bordered table-striped" border="0" id="r/l1">
       <tr style="background-color: rgba(126,86,134,.7); color: white;" >
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index <=28)                
        <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{($nomor-19)+1}}</th>
        @endif
        @endforeach                                  
      </tr>
      <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index <=28)                
        <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
        @endif
        @endforeach                                  
      </tr>               
      <tr style="background-color: rgba(126,86,134,.7); color: white;" >
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index <=28)                
        <th align="center" width="{{(100/10)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
        @endif
        @endforeach                                  
      </tr>
      <tr style="background-color: #F0FFF0">
        @foreach($ng_list as $nomor => $ng)
        @if($ng->location =="Cover R/L" && $loop->index <=28)
        <td Style="font-size: 45px" valign="middle" class="f1">0</td>
        @endif
        @endforeach                                  
      </tr>                
    </table> <BR>

    <table class="table no-margin table-bordered table-striped" border="0" id="r/l2">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover R/L" && $loop->index >28)                
      <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{($nomor-19)+1}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover R/L" && $loop->index >28)                
      <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>                   
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover R/L" && $loop->index >28)                
      <th align="center" width="{{(100/9)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover R/L" && $loop->index >28)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR> 

</div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_3">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="lower1">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{($nomor-39)+2}}</th>
      @endif
      @endforeach                                  
    </tr> 
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>              
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)                
      <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Cover Lower" && $loop->index <=45)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>

  <table class="table no-margin table-bordered table-striped" border="0" id="lower2">
   <tr style="background-color: rgba(126,86,134,.7); color: white;" >
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{($nomor-39)+2}}</th>
    @endif
    @endforeach                                  
  </tr>
  <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
    @endif
    @endforeach                                  
  </tr>                   
  <tr style="background-color: rgba(126,86,134,.7); color: white;" >
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)                
    <th align="center" width="{{(100/8)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
    @endif
    @endforeach                                  
  </tr>
  <tr style="background-color: #F0FFF0">
    @foreach($ng_list as $nomor => $ng)
    @if($ng->location =="Cover Lower" && $loop->index >45)
    <td Style="font-size: 45px" valign="middle" class="f1">0</td>
    @endif
    @endforeach                                  
  </tr>                
</table> <BR> 

</div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_4">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Handle">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{($nomor-55)+2}}</th>
      @endif
      @endforeach                                  
    </tr>   
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>            
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Handle" && $loop->index <=60)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>

<!-- /.tab-pane -->
<div class="tab-pane" id="tab_5">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Button">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{($nomor-61)+2}}</th>
      @endif
      @endforeach                                  
    </tr>    
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>           
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)                
      <th align="center" width="{{(100/6)}}%" style="vertical-align: middle;">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Button" && $loop->index <=66)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>


<!-- /.tab-pane -->
<div class="tab-pane" id="tab_6">
  <!-- <span class="info-box-text" style="font-size: 25px">NG LIST</span></input><br> -->
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Pianica">
     <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle;">{{($nomor-67)+2}}</th>
      @endif
      @endforeach                                  
    </tr>     
    <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle; ">{{$ng->id}}</th>
      @endif
      @endforeach                                  
    </tr>          
    <tr style="background-color: rgba(126,86,134,.7); color: white;" >
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)                
      <th align="center" width="{{(100/3)}}%" style="vertical-align: middle; ">{{$ng->ng_name}}</th>
      @endif
      @endforeach                                  
    </tr>
    <tr style="background-color: #F0FFF0">
      @foreach($ng_list as $nomor => $ng)
      @if($ng->location =="Pianica" && $loop->index <=70)
      <td Style="font-size: 45px" valign="middle" class="f1">0</td>
      @endif
      @endforeach                                  
    </tr>                
  </table> <BR>
</div>
</div>
<!-- /.tab-pane -->
</div>
<!-- /.tab-content -->
</div>
<!-- nav-tabs-custom -->
</div>            
</div>
</div>
</div>

<div class="modal modal-default fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Your RFID </h4>
      </div>
      <div class="modal-body" >
        <span>RFID</span>
        <input type="text" name="oppureto" id="oppureto"  class="form-control" autofocus style="text-align: center;  font-size: 30px; height: 45px" placeholder="RFID">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
        <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()" >Change</button>

        {{-- <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a> --}}
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >

  jQuery(document).ready(function() {
    gettotalng();
            // $('#oppureto').focus();
            $('#oppureto').val("");
            $('#rfid').val("");
            $('#ubahpureto').css({'display' : 'none'})
            $('#edit').modal({backdrop: 'static', keyboard: false});
            $('#edit').modal('show');
            $('#edit').on('shown.bs.modal', function() {
              $('#oppureto').focus();
            })
            $('#entrydate').datepicker({
              autoclose: true,
              format: 'yyyy-mm-dd',
            });
            $('body').toggleClass("sidebar-collapse");
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
          });

  
  var ngar = new Array();
  var Frame1 = document.getElementById("Frame1");   
  if (Frame1 != null) {              
    for (var j = 0; j < 10; j++) {
      Frame1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Frame1); };  

    }
  } 

  var Frame2 = document.getElementById("Frame2");   
  if (Frame2 != null) {              
    for (var j = 0; j < 9; j++) {
      Frame2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Frame2); };  

    }
  }

  var rl1 = document.getElementById("r/l1");   
  if (rl1 != null) {              
    for (var j = 0; j < 10; j++) {
      rl1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,rl1); };  

    }
  }
  var rl2 = document.getElementById("r/l2");   
  if (rl2 != null) {              
    for (var j = 0; j < 9; j++) {
      rl2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,rl2); };  

    }
  }

  var lower1 = document.getElementById("lower1");   
  if (lower1 != null) {              
    for (var j = 0; j < 8; j++) {
      lower1.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,lower1); };  

    }
  }

  var lower2 = document.getElementById("lower2");   
  if (lower2 != null) {              
    for (var j = 0; j < 8; j++) {
      lower2.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,lower2); };  

    }
  }
  var Handle = document.getElementById("Handle");   
  if (Handle != null) {              
    for (var j = 0; j < 6; j++) {
      Handle.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Handle); };  

    }
  }
  var Button = document.getElementById("Button");   
  if (Button != null) {              
    for (var j = 0; j < 6; j++) {
      Button.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Button); };  

    }
  }
  var Pianica = document.getElementById("Pianica");   
  if (Pianica != null) {              
    for (var j = 0; j < 3; j++) {
      Pianica.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Pianica); };  

    }
  }

  function low(data,nomor,row) {

    var tbl=row;
    
    var row = $(data).closest("tr").index();

    var Akhir = parseInt(data.innerHTML);
    if(Akhir =="0"){
      tbl.rows[3].cells[nomor].innerHTML ="1";
      tbl.rows[3].cells[nomor].style.backgroundColor = "pink";
      var ng = (tbl.rows[1].cells[nomor].innerHTML);                
      ngar.push(ng);
      $('#ng').val(ngar);

    }else{
      tbl.rows[3].cells[nomor].innerHTML ="0";
      tbl.rows[3].cells[nomor].style.backgroundColor = "#F0FFF0";
      var abc = document.getElementById('ng').value.split(",");
      var ng = tbl.rows[1].cells[nomor].innerHTML; 
      var filteredAry = abc.filter(function(e) { return e !== ng })
      ngar = filteredAry;
      $('#ng').val(ngar);

    }           

  }  




  $('#oppureto').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#oppureto").val().length == 10){
        pureto(); 
        getpureto();              
        return false;
      }
      else{
        $("#oppureto").val("");
        alert('Error!', 'RFID number invalid.');
      }
    }
  }); 

  $('#rfid').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#rfid").val().length == 10){
        $('#rfid').prop('disabled', true);
        var id = $('#rfid').val();           
        $('#p_rfid').text(id);
        $('#rfid2').val(id);
        getmodel();
               // alert("aa");            
               return false;
             }
             else{
              $("#rfid").val("");
              alert('Error!', 'RFID number invalid.');
            }
          }
        });

  function rf() {            
    $('#rfid').val("");
    $('#rfid').removeAttr('disabled');
    $('#rfid').focus();
    $('#p_rfid').text("[ ]");
  } 


  function openmodal() {
    $('#ubahpureto2').css({'display' : 'block'})
    $('#ubahpureto').css({'display' : 'block'})
    $('#edit').modal('show');
    $('#oppureto').prop('disabled', true);
  }         

  function pureto() {
    var pureto = $('#oppureto').val();
    $('#p_pureto').text(pureto);
  }       

  function openpureto() {
    $('#oppureto').val("");
    $('#oppureto').removeAttr('disabled');
    $('#oppureto').focus();
  }


  function simpan() {
    var tag = $('#rfid').val();
    var model = $('#modelb').text();
    var op = $('#p_pureto_nik').text();
              // var bensuki = $('#nikbensuki').text();
              var a = "{{Auth::user()->name}}";
              var line = a.substr(a.length - 1);
              var location ="PN_Kakuning_Visual";
              var qty = 1;
              var status = 1;
              var ng = $('#ng').val();

              if(tag == ''){
                alert('All field must be filled'); 
                $('#rfid').focus(); 
              }else{
                var data = {
                  tag:tag,
                  model:model,
                  op:op,        
                  line:line,
                  location:location,
                  qty:qty,
                  status:status,
                  ng:ng,
                }
                $.post('{{ url("index/SaveKakuningVisual") }}', data, function(result, status, xhr){
                  console.log(status);
                  console.log(result);
                  console.log(xhr);
                  if(xhr.status == 200){
                    if(result.status){
                     $('#rfid').val("");
                     $('#rfid').removeAttr('disabled');
                     $('#rfid').focus();
                     $('#ng').val('');
                     ngar = [];
                     var x = document.getElementsByClassName("f1");
                     

                     for (var i = 0; i < x.length; i++) {
                       x[i].innerHTML = "0";
                       x[i].style.backgroundColor = "#F0FFF0";
                      // Frame1.rows[3].cells[i].innerHTML ="0";
                      // Frame1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Frame2.rows[3].cells[i].innerHTML ="0";
                      // Frame2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // rl1.rows[3].cells[i].innerHTML ="0";
                      // rl1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // rl2.rows[3].cells[i].innerHTML ="0";
                      // rl2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // lower1.rows[3].cells[i].innerHTML ="0";
                      // lower1.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // lower2.rows[3].cells[i].innerHTML ="0";
                      // lower2.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Handle.rows[3].cells[i].innerHTML ="0";
                      // Handle.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Button.rows[3].cells[i].innerHTML ="0";
                      // Button.rows[3].cells[i].style.backgroundColor = "#F0FFF0";

                      // Pianica.rows[3].cells[i].innerHTML ="0";
                      // Pianica.rows[3].cells[i].style.backgroundColor = "#F0FFF0";
                    }
                    openSuccessGritter('Success!', result.message);
                    gettotalng();
                    deleteInv();
                  }
                  else{
                    openErrorGritter('Error!', result.message);
                  }
                }
                else{

                  alert("Disconnected from server");
                }
              });
              }        
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


            function getpureto() {
             var pureto = $('#oppureto').val();
             var data ={
              pureto:pureto,
              op:'kakuning visual',
            }
            $.get('{{ url("index/op_Pureto") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#p_pureto_nama').text(result.nama);
                  $('#p_pureto_nik').text(result.nik);
                  $('#edit').modal('hide');
                  $('#rfid').focus();
            // $('#tag_material').val(result.tag);
            openSuccessGritter('Success!', result.message);
          }
          else{
           $('#oppureto').val("");
            // $('#oppureto').removeAttr('disabled');
            $('#oppureto').focus();
            openErrorGritter('Error!', result.message);
          }
        }
        else{

          alert("Disconnected from server");
        }
      });

          }

          function getmodel() {
           var tag = $('#rfid').val();
           var data ={
            tag:tag,            
          }
          $.get('{{ url("index/model") }}', data, function(result, status, xhr){
            console.log(status);
            console.log(result);
            console.log(xhr);
            if(xhr.status == 200){
              if(result.status){
                $('#modelb').text(result.model);
                $('#p_model').text(result.model);
                $('#textmodel').css({'color':'black'})        
                openSuccessGritter('Success!', result.message);
              }
              else{
                $('#rfid').val("");
                $('#rfid').removeAttr('disabled');
                $('#rfid').focus();
                openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });

        }

        function gettotalng() {
         var tag = $('#rfid').val();
         var a = "{{Auth::user()->name}}";
        var line = a.substr(a.length - 1);
         var data ={
          location:'PN_Kakuning_Visual',
          line:line

        }
        $.get('{{ url("index/TotalNg") }}', data, function(result, status, xhr){
          console.log(status);
          console.log(result);
          console.log(xhr);
          if(xhr.status == 200){
            if(result.status){
              $('#total').text(result.total[0].total);
              $('#bagus_total').text(result.total[0].total - result.total[0].ng);
              $('#ng_total').text(result.total[0].ng);
              // $('#biri').text(result.model[0].total);
              // $('#oktaf').text(result.model[1].total);
              // $('#rendah').text(result.model[2].total);
              // $('#tinggi').text(result.model[3].total);
                // alert(result.model[0].total)
                // $('#textmodel').css({'color':'black'})        
                openSuccessGritter('Success!', result.message);

              }
              else{                
                openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });

      }

      function deleteInv() {
         var tag = $('#rfid2').val();
          var data = {
                  tag:tag,
                }
         $.post('{{ url("index/deleteInv") }}', data, function(result, status, xhr){
                  console.log(status);
                  console.log(result);
                  console.log(xhr);
                  if(xhr.status == 200){
                    if(result.status){
                    

                    // openSuccessGritter('Success!', result.message);
                  }
                  else{
                    // openErrorGritter('Error!', result.message);
                  }
                }
                else{

                  alert("Disconnected from server");
                }
              });
              }
      




    </script>
    @stop