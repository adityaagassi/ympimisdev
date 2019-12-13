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
    <span class="text-purple"> 最終検査詳細</span>
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
    <div class="col-xs-6">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
         <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()">Change Operator Kensa Akhir</button>         
         <span class="info-box-text" style="font-size: 25px">OPERATOR Kensa Akhir</span>
         <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b> 
       </div>
     </div>
     <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
        <b>Tag RFID</b> 
        {{-- <b>RFID</b> --}} <b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b><br>
        <input type="text" name="rfid" id="rfid" class="form-control"  autofocus style="text-align: center; font-size: 30px; height: 45px" placeholder="RFID"><br>
        <center><button class="btn btn-lg btn-primary" onclick="rf()">Change</button></center> <br>

      </div>
    </div>
  </div>

  <div class="col-xs-6 ">
   <div class="info-box">        
    <div class="info-box-content" style="margin:0px">  
      <div class="table-responsive">
        <table width="100%"  class="table table-bordered table-striped" border="0" style="margin :0px">
          <tr>
            <td colspan="2" style="background-color: rgba(126,86,134,.7); color: white;" >Total</td>
            <td  style="background-color: rgba(126,86,134,.7); color: white;" >OK</td>
            <td  style="background-color: rgba(126,86,134,.7); color: white;" >NG</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 45px; background-color: #F0FFF0;" valign="middle" id="total">0</td>
            <td  style="font-size: 45px; background-color: #F0FFF0;" valign="middle" id="bagus_total">0</td>
            <td  style="font-size: 45px; background-color: pink;" valign="middle" id="ng_total">0</td>
          </tr>
          <tr>
            <td style="background-color: rgba(126,86,134,.7); color: white;" >BIRI</td>
            <TD style="background-color: rgba(126,86,134,.7); color: white;" >OKTAF</TD>
            <TD style="background-color: rgba(126,86,134,.7); color: white;" >T.TINGGI</TD>
            <TD style="background-color: rgba(126,86,134,.7); color: white;" >T.RENDAH</TD>
          </tr>
          <tr>
            <td style="font-size: 45px; background-color: pink" valign="middle" id="biri" width="25%">0</td>
            <TD style="font-size: 45px; background-color: pink" valign="middle" id="oktaf" width="25%">0</TD>
            <TD style="font-size: 45px; background-color: pink" valign="middle" id="tinggi" width="25%">0</TD>
            <TD style="font-size: 45px; background-color: pink" valign="middle" id="rendah" width="25%">0</TD>
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
          <li class="active" style="width: 21%"><a href="#tab_1" data-toggle="tab">Biri</a></li>
          <li style="width: 21%"><a href="#tab_2" data-toggle="tab">Oktaf</a></li>
          <li style="width: 21%"><a href="#tab_3" data-toggle="tab">T. Rendah</a></li>
          <li style="width: 21%"><a href="#tab_4" data-toggle="tab">T. Tinggi</a></li>

          <li style="width: 10%"><button class="btn btn-lg btn-warning pull-right" style="width: 100%" onclick="simpan()"><i class="fa fa-save"></i>&nbsp;Save</button></a></li>

          <!-- <li ><button class="btn btn-lg btn-warning pull-right" onclick="simpan()"><i class="fa fa-save"></i>&nbsp;Save</button></a></li> -->


        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <div class="table-responsive">
              <table class="table no-margin table-bordered table-striped" border="0" id="Frame1">
               <tr>
                <td colspan="13"> </td>
              </tr>
              <tr style="background-color: #F0FFF0">
                <td class="f1" style="background-color: #F0FFF0">1</td>
                <td class="f1" style="background-color: #F0FFF0">2</td>
                <td class="f1" style="background-color: #F0FFF0">3</td>
                <td class="f1" style="background-color: #F0FFF0">4</td>
                <td class="f1" style="background-color: #F0FFF0">5</td>
                <td class="f1" style="background-color: #F0FFF0">6</td>
                <td class="f1" style="background-color: #F0FFF0">7</td>
                <td class="f1" style="background-color: #F0FFF0">8</td>
                <td class="f1" style="background-color: #F0FFF0">9</td>
                <td class="f1" style="background-color: #F0FFF0">10</td>
                <td class="f1" style="background-color: #F0FFF0">11</td>
                <td class="f1" style="background-color: #F0FFF0">12</td>
                <td class="f1" style="background-color: #F0FFF0">13</td>
              </tr>
              <tr>
                <td colspan="13"> </td>
              </tr>
              <tr style="background-color: #F0FFF0">
                <td class="f1" style="background-color: #F0FFF0">14</td>
                <td class="f1" style="background-color: #F0FFF0">15</td>
                <td class="f1" style="background-color: #F0FFF0">16</td>
                <td class="f1" style="background-color: #F0FFF0">17</td>
                <td class="f1" style="background-color: #F0FFF0">18</td>
                <td class="f1" style="background-color: #F0FFF0">19</td>
                <td class="f1" style="background-color: #F0FFF0">20</td>
                <td class="f1" style="background-color: #F0FFF0">21</td>
                <td class="f1" style="background-color: #F0FFF0">22</td>
                <td class="f1" style="background-color: #F0FFF0">23</td>
                <td class="f1" style="background-color: #F0FFF0">24</td>
                <td class="f1" style="background-color: #F0FFF0">25</td>
                <td class="f1" style="background-color: #F0FFF0">26</td>
              </tr>
              <tr>
                <td colspan="13"> </td>
              </tr>
              <tr>
               <td class="f1" style="background-color: #F0FFF0"></td>                
               <td class="f1" style="background-color: #F0FFF0">27</td>
               <td class="f1" style="background-color: #F0FFF0">28</td>
               <td class="f1" style="background-color: #F0FFF0">29</td>
               <td class="f1" style="background-color: #F0FFF0">30</td>
               <td class="f1" style="background-color: #F0FFF0">31</td>
               <td class="f1" style="background-color: #F0FFF0">32</td>
               <td class="f1" style="background-color: #F0FFF0">33</td>
               <td class="f1" style="background-color: #F0FFF0">34</td>
               <td class="f1" style="background-color: #F0FFF0">35</td>
               <td class="f1" style="background-color: #F0FFF0">36</td>
               <td class="f1" style="background-color: #F0FFF0">37</td>
               <td class="f1" style="background-color: #F0FFF0"></td> 
             </tr>
           </table>
         </div>
       </div>

       <div class="tab-pane " id="tab_2">
        <div class="table-responsive">
          <table class="table no-margin table-bordered table-striped" border="0" id="Frame2">
            <tr>
              <td colspan="13"> </td>
            </tr>
            <tr style="background-color: #F0FFF0">
              <td class="f1" style="background-color: #F0FFF0">1</td>
              <td class="f1" style="background-color: #F0FFF0">2</td>
              <td class="f1" style="background-color: #F0FFF0">3</td>
              <td class="f1" style="background-color: #F0FFF0">4</td>
              <td class="f1" style="background-color: #F0FFF0">5</td>
              <td class="f1" style="background-color: #F0FFF0">6</td>
              <td class="f1" style="background-color: #F0FFF0">7</td>
              <td class="f1" style="background-color: #F0FFF0">8</td>
              <td class="f1" style="background-color: #F0FFF0">9</td>
              <td class="f1" style="background-color: #F0FFF0">10</td>
              <td class="f1" style="background-color: #F0FFF0">11</td>
              <td class="f1" style="background-color: #F0FFF0">12</td>
              <td class="f1" style="background-color: #F0FFF0">13</td>
            </tr>
            <tr>
              <td colspan="13"> </td>
            </tr>
            <tr style="background-color: #F0FFF0">
              <td class="f1" style="background-color: #F0FFF0">14</td>
              <td class="f1" style="background-color: #F0FFF0">15</td>
              <td class="f1" style="background-color: #F0FFF0">16</td>
              <td class="f1" style="background-color: #F0FFF0">17</td>
              <td class="f1" style="background-color: #F0FFF0">18</td>
              <td class="f1" style="background-color: #F0FFF0">19</td>
              <td class="f1" style="background-color: #F0FFF0">20</td>
              <td class="f1" style="background-color: #F0FFF0">21</td>
              <td class="f1" style="background-color: #F0FFF0">22</td>
              <td class="f1" style="background-color: #F0FFF0">23</td>
              <td class="f1" style="background-color: #F0FFF0">24</td>
              <td class="f1" style="background-color: #F0FFF0">25</td>
              <td class="f1" style="background-color: #F0FFF0">26</td>
            </tr>
            <tr>
              <td colspan="13"> </td>
            </tr>
            <tr>
             <td class="f1" style="background-color: #F0FFF0"></td>                
             <td class="f1" style="background-color: #F0FFF0">27</td>
             <td class="f1" style="background-color: #F0FFF0">28</td>
             <td class="f1" style="background-color: #F0FFF0">29</td>
             <td class="f1" style="background-color: #F0FFF0">30</td>
             <td class="f1" style="background-color: #F0FFF0">31</td>
             <td class="f1" style="background-color: #F0FFF0">32</td>
             <td class="f1" style="background-color: #F0FFF0">33</td>
             <td class="f1" style="background-color: #F0FFF0">34</td>
             <td class="f1" style="background-color: #F0FFF0">35</td>
             <td class="f1" style="background-color: #F0FFF0">36</td>
             <td class="f1" style="background-color: #F0FFF0">37</td>
             <td class="f1" style="background-color: #F0FFF0"></td> 
           </tr>
         </table>
       </div>
     </div>

     <div class="tab-pane " id="tab_3">
      <div class="table-responsive">
        <table class="table no-margin table-bordered table-striped" border="0" id="Frame3">
         <tr>
          <td colspan="13"> </td>
        </tr>
        <tr style="background-color: #F0FFF0">
          <td class="f1" style="background-color: #F0FFF0">1</td>
          <td class="f1" style="background-color: #F0FFF0">2</td>
          <td class="f1" style="background-color: #F0FFF0">3</td>
          <td class="f1" style="background-color: #F0FFF0">4</td>
          <td class="f1" style="background-color: #F0FFF0">5</td>
          <td class="f1" style="background-color: #F0FFF0">6</td>
          <td class="f1" style="background-color: #F0FFF0">7</td>
          <td class="f1" style="background-color: #F0FFF0">8</td>
          <td class="f1" style="background-color: #F0FFF0">9</td>
          <td class="f1" style="background-color: #F0FFF0">10</td>
          <td class="f1" style="background-color: #F0FFF0">11</td>
          <td class="f1" style="background-color: #F0FFF0">12</td>
          <td class="f1" style="background-color: #F0FFF0">13</td>
        </tr>
        <tr>
          <td colspan="13"> </td>
        </tr>
        <tr style="background-color: #F0FFF0">
          <td class="f1" style="background-color: #F0FFF0">14</td>
          <td class="f1" style="background-color: #F0FFF0">15</td>
          <td class="f1" style="background-color: #F0FFF0">16</td>
          <td class="f1" style="background-color: #F0FFF0">17</td>
          <td class="f1" style="background-color: #F0FFF0">18</td>
          <td class="f1" style="background-color: #F0FFF0">19</td>
          <td class="f1" style="background-color: #F0FFF0">20</td>
          <td class="f1" style="background-color: #F0FFF0">21</td>
          <td class="f1" style="background-color: #F0FFF0">22</td>
          <td class="f1" style="background-color: #F0FFF0">23</td>
          <td class="f1" style="background-color: #F0FFF0">24</td>
          <td class="f1" style="background-color: #F0FFF0">25</td>
          <td class="f1" style="background-color: #F0FFF0">26</td>
        </tr>
        <tr>
          <td colspan="13"> </td>
        </tr>
        <tr>
         <td class="f1" style="background-color: #F0FFF0"></td>                
         <td class="f1" style="background-color: #F0FFF0">27</td>
         <td class="f1" style="background-color: #F0FFF0">28</td>
         <td class="f1" style="background-color: #F0FFF0">29</td>
         <td class="f1" style="background-color: #F0FFF0">30</td>
         <td class="f1" style="background-color: #F0FFF0">31</td>
         <td class="f1" style="background-color: #F0FFF0">32</td>
         <td class="f1" style="background-color: #F0FFF0">33</td>
         <td class="f1" style="background-color: #F0FFF0">34</td>
         <td class="f1" style="background-color: #F0FFF0">35</td>
         <td class="f1" style="background-color: #F0FFF0">36</td>
         <td class="f1" style="background-color: #F0FFF0">37</td>
         <td class="f1" style="background-color: #F0FFF0"></td> 
       </tr>
     </table>
   </div>
 </div>

 <div class="tab-pane " id="tab_4">
  <div class="table-responsive">
    <table class="table no-margin table-bordered table-striped" border="0" id="Frame4">
     <tr>
      <td colspan="13"> </td>
    </tr>
    <tr style="background-color: #F0FFF0">
      <td class="f1" style="background-color: #F0FFF0">1</td>
      <td class="f1" style="background-color: #F0FFF0">2</td>
      <td class="f1" style="background-color: #F0FFF0">3</td>
      <td class="f1" style="background-color: #F0FFF0">4</td>
      <td class="f1" style="background-color: #F0FFF0">5</td>
      <td class="f1" style="background-color: #F0FFF0">6</td>
      <td class="f1" style="background-color: #F0FFF0">7</td>
      <td class="f1" style="background-color: #F0FFF0">8</td>
      <td class="f1" style="background-color: #F0FFF0">9</td>
      <td class="f1" style="background-color: #F0FFF0">10</td>
      <td class="f1" style="background-color: #F0FFF0">11</td>
      <td class="f1" style="background-color: #F0FFF0">12</td>
      <td class="f1" style="background-color: #F0FFF0">13</td>
    </tr>
    <tr>
      <td colspan="13"> </td>
    </tr>
    <tr style="background-color: #F0FFF0">
      <td class="f1" style="background-color: #F0FFF0">14</td>
      <td class="f1" style="background-color: #F0FFF0">15</td>
      <td class="f1" style="background-color: #F0FFF0">16</td>
      <td class="f1" style="background-color: #F0FFF0">17</td>
      <td class="f1" style="background-color: #F0FFF0">18</td>
      <td class="f1" style="background-color: #F0FFF0">19</td>
      <td class="f1" style="background-color: #F0FFF0">20</td>
      <td class="f1" style="background-color: #F0FFF0">21</td>
      <td class="f1" style="background-color: #F0FFF0">22</td>
      <td class="f1" style="background-color: #F0FFF0">23</td>
      <td class="f1" style="background-color: #F0FFF0">24</td>
      <td class="f1" style="background-color: #F0FFF0">25</td>
      <td class="f1" style="background-color: #F0FFF0">26</td>
    </tr>
    <tr>
      <td colspan="13"> </td>
    </tr>
    <tr>
     <td class="f1" style="background-color: #F0FFF0"></td>                
     <td class="f1" style="background-color: #F0FFF0">27</td>
     <td class="f1" style="background-color: #F0FFF0">28</td>
     <td class="f1" style="background-color: #F0FFF0">29</td>
     <td class="f1" style="background-color: #F0FFF0">30</td>
     <td class="f1" style="background-color: #F0FFF0">31</td>
     <td class="f1" style="background-color: #F0FFF0">32</td>
     <td class="f1" style="background-color: #F0FFF0">33</td>
     <td class="f1" style="background-color: #F0FFF0">34</td>
     <td class="f1" style="background-color: #F0FFF0">35</td>
     <td class="f1" style="background-color: #F0FFF0">36</td>
     <td class="f1" style="background-color: #F0FFF0">37</td>
     <td class="f1" style="background-color: #F0FFF0"></td> 
   </tr>
 </table>
</div>
</div>

</div>
</div>

<!-- <div class="row">
  <div class="col-xs-12">
    <div class="box box-solid">
      <div class="box box-body">              
          <input id="ng" hidden></input><br>
        <div class="table-responsive">
          <table class="table no-margin table-bordered table-striped" border="0" id="tblMain"> 
            <tr style="background-color: rgba(126,86,134,.7); color: white;" hidden>
              @foreach($ng_list as $nomor => $ng)
              <b id="ng_lop" hidden>{{$loop->count}}</b>
              <th align="center" width="{{(95/$loop->count)}}%">{{$ng->id}}</th>
              @endforeach                                  
            </tr>                  
            <tr style="background-color: rgba(126,86,134,.7); color: white;" >
              @foreach($ng_list as $nomor => $ng)
              <b id="ng_lop" hidden>{{$loop->count}}</b>
              <th align="center" width="{{(95/$loop->count)}}%">{{$ng->ng_name}}</th>
              @endforeach                                  
            </tr>
            <tr style="background-color: #F0FFF0">
              @foreach($ng_list as $nomor => $ng)
              <td Style="font-size: 45px" valign="middle">0</td>
              @endforeach                                  
            </tr>                
          </table> <BR> 
          <BUTTON class="btn btn-lg btn-success pull-right"  style="margin: 0px 0px 0px 0px; " onclick="simpan()">Save</BUTTON>           
        </div>
      </div>            
    </div>
  </div>
</div> -->

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


<input id="ng" hidden="true"></input>
<input id="ngbiri" hidden="true"></input>
<input id="ngoktaf" hidden="true"></input>
<input id="ngrendah" hidden="true"></input>
<input id="ngtinggi" hidden="true"></input>
<input id="optuningInput" hidden="true"></input>
<input id="opkaretInput"hidden="true"></input>



@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >

  jQuery(document).ready(function() {
    gettotalng();
    $('#ngtinggi').val('A');
    $('#ngrendah').val('A');
    $('#ngoktaf').val('A');
    $('#ngbiri').val('A');
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
  var ngbiri = new Array();
  var ngoktaf = new Array();
  var ngrendah = new Array();
  var ngtinggi = new Array();
  var Biri = document.getElementById("Frame1");
  if (Biri != null) {              
    for (var j = 0; j < 13; j++) {
      Biri.rows[1].cells[j].onclick = function () { low(this,this.cellIndex,Biri,1); };  
      Biri.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Biri,1); };
      Biri.rows[5].cells[j].onclick = function () { low(this,this.cellIndex,Biri,1); }; 
    }
  } 

  var Oktaf = document.getElementById("Frame2");
  if (Oktaf != null) {              
    for (var j = 0; j < 13; j++) {
      Oktaf.rows[1].cells[j].onclick = function () { low(this,this.cellIndex,Oktaf,2); };  
      Oktaf.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Oktaf,2); };
      Oktaf.rows[5].cells[j].onclick = function () { low(this,this.cellIndex,Oktaf,2); }; 
    }
  }

  var Rendah = document.getElementById("Frame3");
  if (Rendah != null) {              
    for (var j = 0; j < 13; j++) {
      Rendah.rows[1].cells[j].onclick = function () { low(this,this.cellIndex,Rendah,3); };  
      Rendah.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Rendah,3); };
      Rendah.rows[5].cells[j].onclick = function () { low(this,this.cellIndex,Rendah,3); }; 
    }
  }

  var Tinggi = document.getElementById("Frame4");
  if (Tinggi != null) {              
    for (var j = 0; j < 13; j++) {
      Tinggi.rows[1].cells[j].onclick = function () { low(this,this.cellIndex,Tinggi,4); };  
      Tinggi.rows[3].cells[j].onclick = function () { low(this,this.cellIndex,Tinggi,4); };
      Tinggi.rows[5].cells[j].onclick = function () { low(this,this.cellIndex,Tinggi,4); }; 
    }
  }


  function low(data,nomor,rowData,idng) {

    var row = $(data).closest("tr").index();
    var tbl=rowData;

    // alert($(data).css( "background-color" ));
    var awal = parseInt(data.innerHTML);
    if (row == "1"){
      if($(data).css( "background-color" ) =="rgb(240, 255, 240)"){

        tbl.rows[1].cells[nomor].style.backgroundColor = "pink";
        var ng = (tbl.rows[1].cells[nomor].innerHTML);                
        ngar.push(ng);
        $('#ng').val(ngar);

        if (idng == 1) {
          ngbiri.push(ng);
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          ngoktaf.push(ng);
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          ngrendah.push(ng);
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          ngtinggi.push(ng);
          $('#ngtinggi').val(ngtinggi);
        }

      }else if($(data).css( "background-color" ) =="rgb(255, 192, 203)"){
        tbl.rows[1].cells[nomor].style.backgroundColor = "#F0FFF0";
        var abc = document.getElementById('ng').value.split(",");
        var ng = tbl.rows[1].cells[nomor].innerHTML; 
        var filteredAry = abc.filter(function(e) { return e !== ng })

        ngar = filteredAry;
        $('#ng').val(ngar);

        if (idng == 1) {
          var abc = document.getElementById('ngbiri').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngbiri = filteredAry;
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          var abc = document.getElementById('ngoktaf').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngoktaf = filteredAry;
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          var abc = document.getElementById('ngrendah').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngrendah = filteredAry;
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          var abc = document.getElementById('ngtinggi').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngtinggi = filteredAry;
          $('#ngtinggi').val(ngtinggi);
        }
      } 
    }else if (row == "3"){
      if($(data).css( "background-color" ) =="rgb(240, 255, 240)"){

        tbl.rows[3].cells[nomor].style.backgroundColor = "pink";
        var ng = (tbl.rows[3].cells[nomor].innerHTML);                
        ngar.push(ng);
        $('#ng').val(ngar);

        if (idng == 1) {
          ngbiri.push(ng);
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          ngoktaf.push(ng);
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          ngrendah.push(ng);
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          ngtinggi.push(ng);
          $('#ngtinggi').val(ngtinggi);
        }

      }else if($(data).css( "background-color" ) =="rgb(255, 192, 203)"){
        tbl.rows[3].cells[nomor].style.backgroundColor = "#F0FFF0";
        var abc = document.getElementById('ng').value.split(",");
        var ng = tbl.rows[3].cells[nomor].innerHTML; 
        var filteredAry = abc.filter(function(e) { return e !== ng })
        ngar = filteredAry;
        $('#ng').val(ngar);

        if (idng == 1) {
          var abc = document.getElementById('ngbiri').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngbiri = filteredAry;
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          var abc = document.getElementById('ngoktaf').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngoktaf = filteredAry;
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          var abc = document.getElementById('ngrendah').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngrendah = filteredAry;
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          var abc = document.getElementById('ngtinggi').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngtinggi = filteredAry;
          $('#ngtinggi').val(ngtinggi);
        }

      }
    }else if (row == "5"){
      if($(data).css( "background-color" ) =="rgb(240, 255, 240)"){

        tbl.rows[5].cells[nomor].style.backgroundColor = "pink";
        var ng = (tbl.rows[5].cells[nomor].innerHTML);                
        ngar.push(ng);
        $('#ng').val(ngar);

        if (idng == 1) {
          ngbiri.push(ng);
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          ngoktaf.push(ng);
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          ngrendah.push(ng);
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          ngtinggi.push(ng);
          $('#ngtinggi').val(ngtinggi);
        }

      }else if($(data).css( "background-color" ) =="rgb(255, 192, 203)"){
        tbl.rows[5].cells[nomor].style.backgroundColor = "#F0FFF0";
        var abc = document.getElementById('ng').value.split(",");
        var ng = tbl.rows[5].cells[nomor].innerHTML; 
        var filteredAry = abc.filter(function(e) { return e !== ng })
        ngar = filteredAry;
        $('#ng').val(ngar);

        if (idng == 1) {
          var abc = document.getElementById('ngbiri').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngbiri = filteredAry;
          $('#ngbiri').val(ngbiri);
        }else if(idng == 2){
          var abc = document.getElementById('ngoktaf').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngoktaf = filteredAry;
          $('#ngoktaf').val(ngoktaf);
        }else if(idng == 3){
          var abc = document.getElementById('ngrendah').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngrendah = filteredAry;
          $('#ngrendah').val(ngrendah);
        }else if(idng == 4){
          var abc = document.getElementById('ngtinggi').value.split(",");
          var filteredAry = abc.filter(function(e) { return e !== ng })

          ngtinggi = filteredAry;
          $('#ngtinggi').val(ngtinggi);
        }

      }
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
  var ngbiriI = $('#ngbiri').val();
  var ngoktafI = $('#ngoktaf').val();
  var ngrendahI = $('#ngrendah').val();
  var ngtinggiI = $('#ngtinggi').val();
  var a = "{{Auth::user()->name}}";
  var line = a.substr(a.length - 1);
  var location ="PN_Kensa_Akhir";
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
      ngtinggi:ngtinggiI,
      ngrendah:ngrendahI,
      ngoktaf:ngoktafI,
      ngbiri:ngbiriI
    }
    $.post('{{ url("index/SaveKensaAkhir") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
         $('#rfid').val("");
         $('#rfid').removeAttr('disabled');
         $('#rfid').focus();
         $('#ng').val('');
         $('#ngtinggi').val('A');
         $('#ngrendah').val('A');
         $('#ngoktaf').val('A');
         $('#ngbiri').val('A');
         ngar = [];
         ngbiri = [];
         ngoktaf = [];
         ngtinggi = [];
         ngrendah = []; 
         var x = document.getElementsByClassName("f1");

         for (var i = 0; i < x.length; i++) {

           x[i].style.backgroundColor = "#F0FFF0";
         }
         openSuccessGritter('Success!', result.message);
         gettotalng();
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
  op:'kensa akhir',
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
  location:'PN_Kensa_Akhir',
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
      $('#biri').text(result.model[0].total);
      $('#oktaf').text(result.model[1].total);
      $('#rendah').text(result.model[2].total);
      $('#tinggi').text(result.model[3].total);
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




</script>
@stop