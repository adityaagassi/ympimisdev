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
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
     <span class="text-purple"> 弁付き詳細</span>
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
    <div class="col-xs-3">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          Incomings Reed Plate <br>
          <span  >Model <b class="destroy" id="modelb2">[ ]</b></span><br>
          <div class="col-xs-12" style="padding: 0px">
            @foreach($models as $model) 
            <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-warning" onclick="model2(this.id)" id="{{$model}}" style="width:100%;">{{$model}}</button></div>
            @endforeach
            <br>
            Qty <br>
            <input type="text" name="qty" id="qty" class="form-control">
            Entry Date <br>
            <input type="text" name="entrydate" id="entrydate" class="form-control select2"><br>
            <button class="btn btn-warning btn-md pull-right" onclick="imcoming()">Save</button>
          </div>
          &nbsp;
        </div>
      </div>
    </div>

    <div class="col-xs-4 ">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <span >Op Bensuki </span><br>
          <div class="table-responsive">
            <table>

              <tr>
                <td rowspan="2" style="padding: 2px">LOW</td>
                @foreach($lows  as $nomor => $lows)
                @if($lows->warna =="M" )
                <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $lows->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">
                  {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
                  @endif                
                  @endforeach
                </tr>
                <tr>
                  @foreach($low  as $nomor => $low)
                  @if($low->warna =="H" )
                  <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $low->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
                  @endif                
                  @endforeach                
                </tr>
                <tr>
                  <td rowspan="2" style="padding: 2px">MIDDLE</td>
                  @foreach($middles  as $nomor => $middles)
                  @if($middles->warna =="M" )
                  <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $middles->nama}}" name="{{ $middles->nik}}"  onclick="opben('MIDDLE',this.id,this.name,this)">
                    {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
                    @endif                
                    @endforeach
                  </tr>
                  <tr>
                    @foreach($middle  as $nomor => $middle)
                    @if($middle->warna =="H" )
                    <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $middle->nama}}" name="{{ $middle->nik}}"onclick="opben('MIDDLE',this.id,this.name,this)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
                    @endif                
                    @endforeach                
                  </tr>

                  <tr>
                    <td rowspan="2" style="padding: 2px">HIGH</td>
                    @foreach($highs  as $nomor => $highs)
                    @if($highs->warna =="M" )
                    <td style="padding: 2px"><button class="btn btn-md btn-danger" id="{{ $highs->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">
                      {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
                      @endif                
                      @endforeach
                    </tr>
                    <tr>
                      @foreach($high  as $nomor => $high)
                      @if($high->warna =="H" )
                      <td style="padding: 2px"><button class="btn btn-md " style="background-color: black; color: white" id="{{ $high->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
                      @endif                
                      @endforeach                
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-5 ">
            <div class="box box-solid">        
              <div class="box-body" style="margin:0px">

                <div class="col-xs-12" style="padding: 0px">
                  <span >Model  </span><br>
                  @foreach($models as $model) 
                  <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-warning" onclick="model(this.id)" id="{{$model}}" style="width:100%;">{{$model}}</button></div>
                  @endforeach
                </div>

                Op Reed Plate  <br>
                <div class="col-xs-12" style="padding: 0px">
                  @foreach($bennukis  as $nomor => $bennukis)
                  <div class="col-xs-2"style="padding: 5px 5px 0px 5px; " ><button class="btn btn-LG btn-primary" onclick="opred(this.id,this,this.name)" id="{{ $bennukis->nama}}" name="{{ $bennukis->nik}}" style="width:100%; background-color: #8A2BE2">{{ $bennukis->kode}}</button></div>
                  @endforeach
                </div>

                Shift<br>
                <div class="col-xs-12" style="padding: 0px">
                 @foreach($shifts as $shifts) 
                 @if($shifts =="B")
                 <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ></div>
                 @else
                  <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-info" onclick="shift(this.id,this)" id="{{$shifts}}" style="width:100%;">{{$shifts}}</button></div>
                  @endif
                 @endforeach
               </div>

               Mesin<br>
               <div class="col-xs-12" style="padding: 0px">
                 @foreach($mesins as $mesins) 
                 <div class="col-xs-2"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-success" onclick="mesin(this.id,this)" id="{{$mesins}}" style="width:100%;">{{$mesins}}</button></div>
                 @endforeach
               </div>
               
             </div>
           </div>

         </div>
       </div>
       <input type="text" name="ng" id="ng" value="" hidden="">
       <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
          <li class="active" style="width: 90%"><a href="#low" data-toggle="tab"><i class="fa fa-music"></i><b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b><b id="opbentetx" style="color:red"> [ Op Bensuki ] - </b><b class="destroy" id="posisi"></b><b class="destroy" id="opben"></b><b><b id="opredtext" style="color:red"> [ Op Reed Plate ] - </b></b><b class="destroy" id="opred"></b><b class="destroy" id="shift" style="color:red">[Shift]</b> <b class="destroy" id="mesin" style="color:red">-[Mesin]</b> <b class="destroy" id="nikbensuki" hidden></b>  <b class="destroy" id="nikplate" hidden></b> <b  class="destroy" id="kode" hidden></b>  <b class="destroy" id="kode2" hidden></b><b class="destroy" id="kodemesin" hidden></b><b class="destroy" id="kodeshift" hidden></b></a></li>
          <li style="width: 8%"><button class="btn btn-success" style="width: 100%" onclick="save();">Save</button></li>
        </ul>
        <div class="tab-content no-padding">
          <div class="chart tab-pane active" id="low" style="position: relative; ">
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin table-bordered table-striped" border="0" id="tblMain">                  
                  <tr style="background-color: rgba(126,86,134,.7); color: white;" >
                    <th align="center" width="6.6%">Lepas</th>
                    <th align="center" width="6.6%">Longgar</th>
                    <th align="center" width="6.6%">Pangkal Menempel</th>
                    <th align="center" width="6.6%">Panjang</th>
                    <th align="center" width="6.6%">Melekat</th>
                    <th align="center" width="6.6%">Ujung Menempel</th>
                    <th align="center" width="6.6%">Lengkung</th>
                    <th align="center" width="6.6%">Terbalik</th>
                    <th align="center" width="6.6%">Celah Lebar</th>
                    <th align="center" width="6.6%">Salah Posisi</th>
                    <th align="center" width="6.6%">Kepala Rusak</th>
                    <th align="center" width="6.6%">Patah</th>
                    <th align="center" width="6.6%">Lekukan</th>
                    <th align="center" width="6.6%">Kotor</th>
                    <th align="center" width="6.6%">Celah Sempit</th>
                  </tr>


                  <tr>
                    <td colspan="15"> LOW</td>
                  </tr>
                  <tr style="background-color: #F0FFF0">
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                  </tr>
                  <tr>
                    <td colspan="15"> High</td>
                  </tr>
                  <tr style="background-color: #F0FFF0">
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
                    <td >0</td>
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
        <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
        <script >
          jQuery(document).ready(function() {
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
          var tbl = document.getElementById("tblMain");
          if (tbl != null) {              
            for (var j = 0; j < 15; j++) {
              tbl.rows[2].cells[j].onclick = function () { low(this,this.cellIndex); };  
              tbl.rows[4].cells[j].onclick = function () { low(this,this.cellIndex); };             
            }
          }

          function low(data,nomor,row) {
            var row = $(data).closest("tr").index();
            if (row == "2"){
              var awal = parseInt(data.innerHTML);
              if(awal =="0"){
                tbl.rows[2].cells[nomor].innerHTML ="1";
                tbl.rows[2].cells[nomor].style.backgroundColor = "pink";
                var ng = (tbl.rows[0].cells[nomor].innerHTML);                
                ngar.push(ng+"-"+"LOW");
                $('#ng').val(ngar);
              }else{
                tbl.rows[2].cells[nomor].innerHTML ="0";
                tbl.rows[2].cells[nomor].style.backgroundColor = "#F0FFF0";
                var abc = document.getElementById('ng').value.split(",");
                var ng = ((tbl.rows[0].cells[nomor].innerHTML)+"-"+"LOW"); 
                var filteredAry = abc.filter(function(e) { return e !== ng })
                ngar = filteredAry;
                $('#ng').val(ngar);
              }
            }
            else if (row == "4"){
              var awal = parseInt(data.innerHTML);
              if(awal =="0"){
                tbl.rows[4].cells[nomor].innerHTML ="1";
                tbl.rows[4].cells[nomor].style.backgroundColor = "pink";
                var ng = (tbl.rows[0].cells[nomor].innerHTML);                
                ngar.push(ng+"-"+"HIGH");
                $('#ng').val(ngar);
              }else{
                tbl.rows[4].cells[nomor].innerHTML ="0";
                tbl.rows[4].cells[nomor].style.backgroundColor = "#F0FFF0";
                var abc = document.getElementById('ng').value.split(",");
                var ng = ((tbl.rows[0].cells[nomor].innerHTML)+"-"+"HIGH"); 
                var filteredAry = abc.filter(function(e) { return e !== ng })
                ngar = filteredAry;
                $('#ng').val(ngar);
              }
            }
          } 


          function model(id) {
            $('#modelb').text(""+id+" ");
            $('#textmodel').css({'color':'black'})
          }

          function model2(id) {
            $('#modelb2').text(id);
          }

          function opben(group,id,nik,kode) {
            var code = $(kode).text().trim()+"-"+group;
            $('#opben').text(" "+id+"");
            $('#posisi').text(" "+group+"-");
            $('#nikbensuki').text(nik);
            $('#kode').text(code); 
            $('#opbentetx').css({'color':'black'})           

          }

          function shift(id,kode) {

           var code = $(kode).text().trim();
           $('#shift').text("-"+id+"-");
           $('#shift').css({'color':'black'})
           $('#kodeshift').text(code);  
         }

         function mesin(id,kode) {
           var code = $(kode).text().trim();
           $('#mesin').text(" "+id+"");
           $('#mesin').css({'color':'black'})
           $('#kodemesin').text(code);  
         }

         function opred(id,kode,nik) {
          var code = $(kode).text().trim();
          $('#nikplate').text(nik);
          $('#opred').text(" "+id+"");
          $('#kode2').text(code); 
          $('#opredtext').css({'color':'black'}) 
        }

        function openSuccessGritter(title,text){
          jQuery.gritter.add({
            title: title,
            text: text,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '3000'
          });
        }

        function destroy() {
          ngar = [];
          $('#ng').val(''); 
          $('.destroy').text('');
          for (var i = 0; i < 15; i++) {
            tbl.rows[4].cells[i].innerHTML ="0";
            tbl.rows[4].cells[i].style.backgroundColor = "#F0FFF0";
            tbl.rows[2].cells[i].innerHTML ="0";
            tbl.rows[2].cells[i].style.backgroundColor = "#F0FFF0";
          }
        }

        function destroy2() {
          $('#modelb2').text('');
          $('#qty').val(''); 
          $('#entrydate').val('');
          for (var i = 0; i < 15; i++) {
            tbl.rows[4].cells[i].innerHTML ="0";
            tbl.rows[4].cells[i].style.backgroundColor = "#F0FFF0";
            tbl.rows[2].cells[i].innerHTML ="0";
            tbl.rows[2].cells[i].style.backgroundColor = "#F0FFF0";
          }
          
        }


        function save() {
          var model = $('#modelb').text();
          var kodebensuki = $('#kode').text(); 
          var nikbensuki = $('#nikbensuki').text();
          var kodeplate = $('#kode2').text();
          var nikplate = $('#nikplate').text(); 
          var shift = $('#kodeshift').text();
          var mesin = $('#kodemesin').text();
          var ng = $('#ng').val();  
          var a = "{{Auth::user()->name}}";
          var line = a.substr(a.length - 1);  
          if(model == '' || kodebensuki == '' || nikbensuki == '' || kodeplate == '' || nikplate == '' || shift == '' || mesin == ''){
            alert('All field must be filled');  
          }else{
            var data = {
              model:model,
              kodebensuki:kodebensuki,
              nikbensuki:nikbensuki,
              kodeplate:kodeplate,
              nikplate:nikplate,
              shift:shift,
              mesin:mesin,
              ng:ng,
              line:line
            }
            $.post('{{ url("index/Save") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status="oke"){                
                  openSuccessGritter('Success!','Input NG-Rate Success');
                  destroy();
                  $('#shift').text("[Shift]-");
                  $('#mesin').text("[Mesin]");
                  $('#textmodel').css({'color':'red'});
                  $('#opbentetx').css({'color':'red'});
                  $('#opredtext').css({'color':'red'});
                  $('#shift').css({'color':'red'});
                  $('#mesin').css({'color':'red'});

                }
                else{
                  alert('Attempt to retrieve data failed');
                }
              }
              else{
              }
            });
          }        
        }

        function imcoming() {
          var model = $('#modelb2').text();
          var qty = $('#qty').val(); 
          var entrydate = $('#entrydate').val();

          if(model == '' || qty == '' || entrydate == '' ){
            alert('All field must be filled');  
          }else{
            var data = {
              model:model,
              entrydate:entrydate,
              qty:qty,
              
            }
            $.post('{{ url("index/Incoming") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status="oke"){                
                  openSuccessGritter('Success!','Input Incoming Success');
                  destroy2();
                }
                else{
                  alert('Attempt to retrieve data failed');
                }
              }
              else{
              }
            });
          }           

        }
      </script>
      @stop