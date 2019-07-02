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
    <div class="col-xs-3">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <span class="info-box-text" style="font-size: 30px">Model <b id="modelb">[ ]</b></span>
          <div class="col-xs-12" style="padding: 0px">
            @foreach($models as $model) 
            <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-warning" onclick="model(this.id)" id="{{$model}}" style="width:100%;">{{$model}}</button></div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="col-xs-4 ">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <span class="info-box-text"style="font-size: 30px">Op Bensuki <b id="posisi">[ ]</b> <br><b id="opben">[ ]</b> </span>
          <div class="table-responsive">
            <table class="table ">

              <tr>
                <td rowspan="2">LOW</td>
                @foreach($lows  as $nomor => $lows)
                @if($lows->warna =="M" )
                <td><button class="btn btn-sm btn-danger" id="{{ $lows->nama}}" onclick="opben('LOW',this.id,)">
                  {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
                  @endif                
                  @endforeach
                </tr>
                <tr>
                  @foreach($low  as $nomor => $low)
                  @if($low->warna =="H" )
                  <td><button class="btn btn-sm " style="background-color: black; color: white" id="{{ $low->nama}}"onclick="opben('LOW',this.id,)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
                  @endif                
                  @endforeach                
                </tr>
                <tr>
                  <td rowspan="2">MIDDLE</td>
                  @foreach($middles  as $nomor => $middles)
                  @if($middles->warna =="M" )
                  <td><button class="btn btn-sm btn-danger" id="{{ $middles->nama}}" onclick="opben('MIDDLE',this.id,)">
                    {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
                    @endif                
                    @endforeach
                  </tr>
                  <tr>
                    @foreach($middle  as $nomor => $middle)
                    @if($middle->warna =="H" )
                    <td><button class="btn btn-sm " style="background-color: black; color: white" id="{{ $middle->nama}}"onclick="opben('MIDDLE',this.id,)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
                    @endif                
                    @endforeach                
                  </tr>

                  <tr>
                    <td rowspan="2">HIGH</td>
                    @foreach($highs  as $nomor => $highs)
                    @if($highs->warna =="M" )
                    <td><button class="btn btn-sm btn-danger" id="{{ $highs->nama}}" onclick="opben('HIGH',this.id,)">
                      {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
                      @endif                
                      @endforeach
                    </tr>
                    <tr>
                      @foreach($high  as $nomor => $high)
                      @if($high->warna =="H" )
                      <td><button class="btn btn-sm " style="background-color: black; color: white" id="{{ $high->nama}}"onclick="opben('HIGH',this.id,)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
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
                  <span class="info-box-text" style="font-size: 30px">Op Reed Plate <br><b id="opred">[ ]</b> </span>
                  @foreach($bennukis  as $nomor => $bennukis)
                  <div class="col-xs-2"style="padding: 5px 5px 0px 5px; " ><button class="btn btn-LG btn-primary" onclick="opred(this.id)" id="{{ $bennukis->nama}}" style="width:100%; background-color: #8A2BE2">{{ $bennukis->kode}}</button></div>
                  @endforeach
                </div>
                &nbsp;
              </div>
            </div>

            <div class="info-box">        
              <div class="info-box-content" style="margin:0px">   
                <div class="col-xs-12" style="padding: 0px">
                  <span class="info-box-text" style="font-size: 30px">SHIFT <b id="shift">[ ]</b> </span>
                  @foreach($shifts as $shifts) 
                  <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-info" onclick="shift(this.id)" id="{{$shifts}}" style="width:100%;">{{$shifts}}</button></div>
                  @endforeach
                </div>
              </div>
            </div>


            <div class="info-box">        
              <div class="info-box-content" style="margin:0px">   
                <div class="col-xs-12" style="padding: 0px">
                  <span class="info-box-text" style="font-size: 30px">MESIN <b id="mesin">[ ]</b> </span>
                  @foreach($mesins as $mesins) 
                  <div class="col-xs-2"style="padding: 0px 5px 0px 5px" ><button class="btn btn-LG btn-success" onclick="mesin(this.id)" id="{{$mesins}}" style="width:100%;">{{$mesins}}</button></div>
                  @endforeach
                </div>
              </div>


            </div>
          </div>
        </div>

        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
            <li class="active" style="width: 45%"><a href="#low" data-toggle="tab"><i class="fa fa-music"></i><b> LOW</b></a></li>
            <li style="width: 45%"><a href="#high"  data-toggle="tab"><i class="fa fa-music"></i><b> High</b></a></li>
            <li style="width: 8%"><button class="btn btn-success" style="width: 100%">Save</button></li>
          </ul>
          <div class="tab-content no-padding">
            <div class="chart tab-pane active" id="low" style="position: relative; ">
              <div class="box-body">
                <div class="table-responsive">
                  <table class="table no-margin table-bordered table-striped" border="0">
                    <tr>
                      <td height="150px" style="background-color: white; padding: 0px" width="3.125%"> <div style="background-color: white; height:90%;">
                      </div> <h6>Reed No.</h6> </td>
                      <?php for ($i=16; $i >= 1; $i--) {  ?>
                      <td height="150px" style="background-color: #B8860B; padding: 0px" width="3.125%"> <div style="background-color: black; height:90%;">
                        <div style="margin-left: 5px;margin-right: 5px; background-color: white; height: 90%">    
                          @php
                          $p = 'images/bundar.png';
                          @endphp 
                          <img src="{{ url($p) }}" class="user-image " alt="7 Poin" align="middle" width="15"></div>
                        </div><b><?php echo $i ?></b> </td>
                        <td height="150px" style="background-color: #B8860B";"width="3.125%"></td>
                        <?php 
                      }   
                      ?>
                    </tr>
                    <tr id="LepasL">
                     <td>Lepas</td>
                   </tr>
                   <tr id="LonggarL" style="background-color: rgb(240, 255, 240);" val="aaaa">
                     <td >Longgar</td>
                   </tr>
                   <tr id="P.MenempelL">
                     <td>P.Menempel</td>
                   </tr>
                   <tr id="PanjangL" style="background-color: rgb(240, 255, 240)">
                     <td >Panjang</td>
                   </tr>
                   <tr id="MelekatL">
                     <td>Melekat</td>
                   </tr >
                   <tr id="Uj.MenempelL" style="background-color: rgb(240, 255, 240)">
                     <td >Uj.Menempel</td>
                   </tr>
                   <tr id="LengkungL">
                     <td>Lengkung</td>
                   </tr>
                   <tr id="TerbalikL" style="background-color: rgb(240, 255, 240)">
                     <td >Terbalik</td>
                   </tr>
                   <tr id="C.LebarL">
                     <td>C.Lebar</td>
                   </tr>
                   <tr id="Slh.PosisiL" style="background-color: rgb(240, 255, 240)">
                     <td >Slh.Posisi</td>
                   </tr>
                   <tr id="Kp.RusakL">
                     <td>Kp.Rusak</td>
                   </tr>
                   <tr id="PatahL" style="background-color: rgb(240, 255, 240)">
                     <td >Patah</td>
                   </tr>
                   <tr id="LekukanL">
                     <td>Lekukan</td>
                   </tr>
                   <tr id="KotorL" style="background-color: rgb(240, 255, 240)">
                     <td >Kotor</td>
                   </tr>
                   <tr id="C.SempitL">
                     <td>C.Sempit</td>
                   </tr>             
                 </table>
               </div>
             </div>

           </div>


           <div class="chart tab-pane" id="high" style="position: relative;">
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <tr>
                    <td height="150px" style="background-color: white; padding: 0px" width="3.125%"> <div style="background-color: white; height:90%;">
                    </div> <h6>Reed No.</h6> </td>
                    <?php for ($i=32; $i >= 17; $i--) {  ?>
                    <td height="150px" style="background-color: #B8860B; padding: 0px" width="3.125%"> <div style="background-color: black; height:90%;">
                      <div style="margin-left: 5px;margin-right: 5px; background-color: white; height: 90%">    
                        @php
                        $p = 'images/bundar.png';
                        @endphp 
                        <img src="{{ url($p) }}" class="user-image " alt="7 Poin" align="middle" width="15"></div>
                      </div><b><?php echo $i ?></b> </td>
                      <td height="150px" style="background-color: #B8860B";"width="3.125%"></td>
                      <?php 
                    }   
                    ?>

                  </tr>

                </tr>
                <tr id="LepasH">
                 <td>Lepas</td>
               </tr>
               <tr id="LonggarH" style="background-color: rgb(240, 255, 240)">
                 <td >Longgar</td>
               </tr>
               <tr id="P.MenempelH">
                 <td>P.Menempel</td>
               </tr>
               <tr id="PanjangH" style="background-color: rgb(240, 255, 240)">
                 <td >Panjang</td>
               </tr>
               <tr id="MelekatH">
                 <td>Melekat</td>
               </tr >
               <tr id="Uj.MenempelH" style="background-color: rgb(240, 255, 240)">
                 <td >Uj.Menempel</td>
               </tr>
               <tr id="LengkungH">
                 <td>Lengkung</td>
               </tr>
               <tr id="TerbalikH" style="background-color: rgb(240, 255, 240)">
                 <td >Terbalik</td>
               </tr>
               <tr id="C.LebarH">
                 <td>C.Lebar</td>
               </tr>
               <tr id="Slh.PosisiH" style="background-color: rgb(240, 255, 240)">
                 <td >Slh.Posisi</td>
               </tr>
               <tr id="Kp.RusakH">
                 <td>Kp.Rusak</td>
               </tr>
               <tr id="PatahH" style="background-color: rgb(240, 255, 240)">
                 <td >Patah</td>
               </tr>
               <tr id="LekukanH">
                 <td>Lekukan</td>
               </tr>
               <tr id="KotorH" style="background-color: rgb(240, 255, 240)">
                 <td >Kotor</td>
               </tr>
               <tr id="C.SempitH">
                 <td>C.Sempit</td>
               </tr>
             </table>
           </div>
         </div>
       </div>

     </div>     
     <center><button class="btn btn-lg btn-success pull-right" style="margin: 0px 5px 10px 0px">Save</button></center> <br>
     &nbsp; <br> &nbsp; <br>

   </div>
   @endsection

   @section('scripts')
   <script >
    jQuery(document).ready(function() {
     $('body').toggleClass("sidebar-collapse");
     myFunction('LepasL');
     myFunction('LonggarL');
     myFunction('P.MenempelL');
     myFunction('PanjangL');
     myFunction('MelekatL');
     myFunction('Uj.MenempelL');
     myFunction('LengkungL');
     myFunction('TerbalikL');
     myFunction('C.LebarL');
     myFunction('Slh.PosisiL');
     myFunction('Kp.RusakL');
     myFunction('PatahL');
     myFunction('LekukanL');
     myFunction('KotorL');
     myFunction('C.SempitL');
     myFunction2('LepasH');
     myFunction2('LonggarH');
     myFunction2('P.MenempelH');
     myFunction2('PanjangH');
     myFunction2('MelekatH');
     myFunction2('Uj.MenempelH');
     myFunction2('LengkungH');
     myFunction2('TerbalikH');
     myFunction2('C.LebarH');
     myFunction2('Slh.PosisiH');
     myFunction2('Kp.RusakH');
     myFunction2('PatahH');
     myFunction2('LekukanH');
     myFunction2('KotorH');
     myFunction2('C.SempitH');

   });

    function myFunction(car) {
      var row = document.getElementById(car);
      // row.style.border="solid";
      var nomor = 16;
      for (i = 1; i < 17; i++) {

        var x = row.insertCell(i);
        x.style="padding:0px";
        x.colSpan = 2;
        if (car == 'LepasL'){
          x.innerHTML = "<button id='Lepas"+"-"+i+"' name='1' onclick='ubah(this.id,this)' class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white' ><h2 style='margin:0px'>"+nomor+"</h2></button>";

        }
        else if (car == 'LonggarL'){
          x.innerHTML = "<button id='Longgar"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'P.MenempelL'){
          x.innerHTML = "<button id='PMenempel"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'PanjangL'){
          x.innerHTML = "<button id='Panjang"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";

        }
        else if (car == 'MelekatL'){
          x.innerHTML = "<button id='Melekat"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Uj.MenempelL'){
          x.innerHTML = "<button id='UjMenempel"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'LengkungL'){
          x.innerHTML = "<button id='Lengkung"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'TerbalikL'){
          x.innerHTML = "<button id='Terbalik"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'C.LebarL'){
          x.innerHTML = "<button id='CLebar"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Slh.PosisiL'){
          x.innerHTML = "<button id='SlhPosisi"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Kp.RusakL'){
          x.innerHTML = "<button id='KpRusak"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'PatahL'){
          x.innerHTML = "<button id='Patah"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'LekukanL'){
          x.innerHTML = "<button id='Lekukan"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'KotorL'){
          x.innerHTML = "<button id='Kotor"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'C.SempitL'){
          x.innerHTML = "<button id='CSempit"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        nomor--;
      }
    }

    function myFunction2(car) {
      var row = document.getElementById(car);
      // row.style.border="solid";
      var nomor = 32;
      for (i = 1; i < 17; i++) {

        var x = row.insertCell(i);
        x.style="padding:0px";
        x.colSpan = 2;
        if (car == 'LepasH'){
          x.innerHTML = "<button id='LepasH"+"-"+i+"' name='1' onclick='ubah(this.id,this)' class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white' ><h2 style='margin:0px'>"+nomor+"</h2></button>";
          //$('#Lepas').css('height', $('#Lepas').parent('td').height());
        }
        else if (car == 'LonggarH'){
          x.innerHTML = "<button id='LonggarH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'P.MenempelH'){
          x.innerHTML = "<button id='PMenempelH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'PanjangH'){
          x.innerHTML = "<button id='PanjangH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";

        }
        else if (car == 'MelekatH'){
          x.innerHTML = "<button id='MelekatH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Uj.MenempelH'){
          x.innerHTML = "<button id='UjMenempelH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'LengkungH'){
          x.innerHTML = "<button id='LengkungH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0; background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'TerbalikH'){
          x.innerHTML = "<button id='TerbalikH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'C.LebarH'){
          x.innerHTML = "<button id='CLebarH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Slh.PosisiH'){
          x.innerHTML = "<button id='SlhPosisiH"+"-"+i+"'name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'Kp.RusakH'){
          x.innerHTML = "<button id='KpRusakH"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'PatahH'){
          x.innerHTML = "<button id='PatahH"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'LekukanH'){
          x.innerHTML = "<button id='LekukanH"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'KotorH'){
          x.innerHTML = "<button id='KotorH"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:#F0FFF0'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        else if (car == 'C.SempitH'){
          x.innerHTML = "<button id='CSempitH"+"-"+i+"' name='1' onclick='ubah(this.id,this)'class='btn btn-xs btn-default' style='width:100%; padding: 0 0 0 0;background-color:white'><h2 style='margin:0px'>"+nomor+"</h2></button>";
        }
        nomor--;
      }
    }


    function ubah (id,elem){
      // var hijau = id.
      var nama = $('#'+id).attr('name');
      if (nama == "1"){
        $('#'+id).css({'background-color':'pink'});
        $('#'+id).attr("name", "0");
      }else if(nama == "0"){       
       var a = $(elem).closest('tr').find('td:first').css("background-color");       
       $('#'+id).css({'background-color':a});       
       $('#'+id).attr("name", "1");
     }
      // alert(nama);
    }

    function model(id) {
      $('#modelb').text("["+id+"]");
    }

    function opben(group,id) {

      $('#opben').text("["+id+"]");
      $('#posisi').text("["+group+"]");
      
    }

    function shift(id) {

      $('#shift').text("["+id+"]");
    }

    function mesin(id) {

      $('#mesin').text("["+id+"]");
    }

    function opred(id) {

      $('#opred').text("["+id+"]");
    }
  </script>
  @stop