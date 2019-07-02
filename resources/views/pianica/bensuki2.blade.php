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
    <a href="javascript:void(0)"  data-toggle="modal" data-target="#edit" class="btn btn-warning btn-sm">Input</a>
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
    <div class="col-xs-3">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <span class="info-box-text" >Model</span>
          <div class="col-xs-12" style="padding: 0px">
            @foreach($models as $model) 
            <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-warning" onclick="model(this.id)" id="{{$model}}" style="width:100%;">{{$model}}</button></div>
            @endforeach
            <br>
            Input <br>
            <input type="text" name="" class="form-control"><br>
            <button class="btn btn-warning btn-md pull-right">Save</button>
          </div>
          &nbsp;
        </div>
      </div>
    </div>

    <div class="col-xs-4 ">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <span class="info-box-text">Op Bensuki </span>
          <div class="table-responsive">
            <table>

              <tr>
                <td rowspan="2" style="padding: 2px">LOW</td>
                @foreach($lows  as $nomor => $lows)
                @if($lows->warna =="M" )
                <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $lows->nama}}" onclick="opben('LOW',this.id,)">
                  {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
                  @endif                
                  @endforeach
                </tr>
                <tr>
                  @foreach($low  as $nomor => $low)
                  @if($low->warna =="H" )
                  <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $low->nama}}"onclick="opben('LOW',this.id,)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
                  @endif                
                  @endforeach                
                </tr>
                <tr>
                  <td rowspan="2" style="padding: 2px">MIDDLE</td>
                  @foreach($middles  as $nomor => $middles)
                  @if($middles->warna =="M" )
                  <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $middles->nama}}" onclick="opben('MIDDLE',this.id,)">
                    {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
                    @endif                
                    @endforeach
                  </tr>
                  <tr>
                    @foreach($middle  as $nomor => $middle)
                    @if($middle->warna =="H" )
                    <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $middle->nama}}"onclick="opben('MIDDLE',this.id,)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
                    @endif                
                    @endforeach                
                  </tr>

                  <tr>
                    <td rowspan="2" style="padding: 2px">HIGH</td>
                    @foreach($highs  as $nomor => $highs)
                    @if($highs->warna =="M" )
                    <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $highs->nama}}" onclick="opben('HIGH',this.id,)">
                      {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
                      @endif                
                      @endforeach
                    </tr>
                    <tr>
                      @foreach($high  as $nomor => $high)
                      @if($high->warna =="H" )
                      <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $high->nama}}"onclick="opben('HIGH',this.id,)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
                      @endif                
                      @endforeach                
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-5 ">
            <div class="info-box">        
              <div class="info-box-content" style="margin:0px">

                <div class="col-xs-12" style="padding: 0px">
                  <span class="info-box-text" >Model  </span>
                  @foreach($models as $model) 
                  <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-warning" onclick="model(this.id)" id="{{$model}}" style="width:100%;">{{$model}}</button></div>
                  @endforeach
                </div>

                Op Reed Plate  <br>
                <div class="col-xs-12" style="padding: 0px">
                  @foreach($bennukis  as $nomor => $bennukis)
                  <div class="col-xs-2"style="padding: 5px 5px 0px 5px; " ><button class="btn btn-LG btn-primary" onclick="opred(this.id)" id="{{ $bennukis->nama}}" style="width:100%; background-color: #8A2BE2">{{ $bennukis->kode}}</button></div>
                  @endforeach
                </div>

                Shift<br>
                <div class="col-xs-12" style="padding: 0px">
                 @foreach($shifts as $shifts) 
                 <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-info" onclick="shift(this.id)" id="{{$shifts}}" style="width:100%;">{{$shifts}}</button></div>
                 @endforeach
               </div>

               Mesin<br>
               <div class="col-xs-12" style="padding: 0px">
                 @foreach($mesins as $mesins) 
                 <div class="col-xs-2"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-success" onclick="mesin(this.id)" id="{{$mesins}}" style="width:100%;">{{$mesins}}</button></div>
                 @endforeach
               </div>
               &nbsp;
             </div>
           </div>

         </div>
       </div>

       <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
          <li class="active" style="width: 90%"><a href="#low" data-toggle="tab"><i class="fa fa-music"></i> [ Model ] - <b id="modelb"></b> [ Op Bensuki ] - <b id="posisi"></b><b id="opben"></b><b> [ Op Reed Plate ] - </b><b id="opred"></b><b id="shift"></b> <b id="mesin"></b></a></li>
          {{-- <li style="width: 45%"><a href="#high"  data-toggle="tab"><i class="fa fa-music"></i><b> High</b></a></li> --}}
          <li style="width: 8%"><button class="btn btn-success" style="width: 100%">Save</button></li>
        </ul>
        <div class="tab-content no-padding">
          <div class="chart tab-pane active" id="low" style="position: relative; ">
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin table-bordered table-striped" border="0">
                  <tr>
                    <td >Lepas</td>
                    <td >Longgar</td>
                    <td >P.Menempel</td>
                    <td >Panjang</td>
                    <td >Melekat</td>
                    <td >Uj.Menempel</td>
                    <td >Lengkung</td>
                    <td >Terbalik</td>
                    <td >C.Lebar</td>
                    <td >Slh.Posisi</td>
                    <td >Kp.Rusak</td>
                    <td >Patah</td>
                    <td >Lekukan</td>
                    <td >Kotor</td>
                    <td >C.Sempit</td>
                  </tr>
                  <tr>
                    <td colspan="15"> LOW</td>
                  </tr>
                  <tr>
                    <td ><input type="text" name="LepasL" class="form-control"></td>
                    <td ><input type="text" name="LonggarL" class="form-control"></td>
                    <td ><input type="text" name="P.MenempelL" class="form-control"></td>
                    <td ><input type="text" name="PanjangL" class="form-control"></td>
                    <td ><input type="text" name="MelekatL" class="form-control"></td>
                    <td ><input type="text" name="Uj.MenempelL" class="form-control"></td>
                    <td ><input type="text" name="LengkungL" class="form-control"></td>
                    <td ><input type="text" name="TerbalikL" class="form-control"></td>
                    <td ><input type="text" name="C.LebarL" class="form-control"></td>
                    <td ><input type="text" name="Slh.PosisiL" class="form-control"></td>
                    <td ><input type="text" name="Kp.RusakL" class="form-control"></td>
                    <td ><input type="text" name="PatahL" class="form-control"></td>
                    <td ><input type="text" name="LekukanL" class="form-control"></td>
                    <td ><input type="text" name="KotorL" class="form-control"></td>
                    <td ><input type="text" name="C.SempitL" class="form-control"></td>
                  </tr>
                  <tr>
                    <td colspan="15"> High</td>
                  </tr>

                  <tr>
                    <td ><input type="text" name="LepasH" class="form-control"></td>
                    <td ><input type="text" name="LonggarH" class="form-control"></td>
                    <td ><input type="text" name="P.MenempelH" class="form-control"></td>
                    <td ><input type="text" name="PanjangH" class="form-control"></td>
                    <td ><input type="text" name="MelekatH" class="form-control"></td>
                    <td ><input type="text" name="Uj.MenempelH" class="form-control"></td>
                    <td ><input type="text" name="LengkungH" class="form-control"></td>
                    <td ><input type="text" name="TerbalikH" class="form-control"></td>
                    <td ><input type="text" name="C.LebarH" class="form-control"></td>
                    <td ><input type="text" name="Slh.PosisiH" class="form-control"></td>
                    <td ><input type="text" name="Kp.RusakH" class="form-control"></td>
                    <td ><input type="text" name="PatahH" class="form-control"></td>
                    <td ><input type="text" name="LekukanH" class="form-control"></td>
                    <td ><input type="text" name="KotorH" class="form-control"></td>
                    <td ><input type="text" name="C.SempitH" class="form-control"></td>
                  </tr>

                </table>
              </div>
            </div>
          </div>
        </div>  
      </div>

      <div class="modal modal-warning fade" id="edit">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Input</h4>
              </div>
              <div class="modal-body" >
                <input type="text" name="" class="form-control">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a>
              </div>
            </div>
          </div>
        </div>

        @endsection

        @section('scripts')
        <script >
          jQuery(document).ready(function() {
           $('body').toggleClass("sidebar-collapse");

         });


          function model(id) {
            $('#modelb').text(""+id+" ");
          }

          function opben(group,id) {

            $('#opben').text(" "+id+"");
            $('#posisi').text(" "+group+"-");

          }

          function shift(id) {

            $('#shift').text("-"+id+"-");
          }

          function mesin(id) {

            $('#mesin').text(" "+id+"");
          }

          function opred(id) {

            $('#opred').text(" "+id+"");
          }
        </script>
        @stop