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
          <span class="info-box-text">Model</span>

        </div>
      </div>
    </div>


    <div class="col-md-3 ">
      <div class="info-box">
        <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Op Bensuki</span>

        </div>
      </div>
    </div>

    <div class="col-md-3 ">
      <div class="info-box">
        <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i class="fa fa-envelope-o"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Op Reed Plate</span>

        </div>
      </div>
    </div>

  </div>

  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-left ">
      <li class="active"><a href="#low" data-toggle="tab"><i class="fa fa-folder"></i><b>LOW</b></a></li>
      <li><a href="#high" data-toggle="tab"><i class="fa fa-folder"></i><b> High</b></a></li>
    </ul>
    <div class="tab-content no-padding">

      <div class="chart tab-pane active" id="low" style="position: relative; ">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin table-bordered table-striped" border="0">

            <tr>
                <td height="100px" style="background-color: white; padding: 0px" width="3.125%"> <div style="background-color: white; height:90%;">
                   </div> <h6>Reed No.</h6> </td>
                <?php for ($i=32; $i >= 17; $i--) {  ?>
                <td height="100px" style="background-color: #DAA520; padding: 0px" width="3.125%"> <div style="background-color: black; height:90%;">
                  <div style="margin-left: 5px;margin-right: 5px; background-color: white; height: 90%">    
              @php
              $p = 'images/bundar.png';
              @endphp 
              <img src="{{ url($p) }}" class="user-image " alt="7 Poin" align="middle" width="15"></div>
                </div><b><?php echo $i ?></b> </td>
                <td height="100px" style="background-color: #DAA520";"width="3.125%"></td>
                <?php 
              }   
              ?>
             
             </tr>
             <tr id="Lepas">
               <td>Lepas</td>
             </tr>
              <tr id="Longgar">
               <td>Longgar</td>
             </tr>
             <tr id="P.Menempel">
               <td>P.Menempel</td>
             </tr>
              <tr id="Panjang">
               <td>Panjang</td>
             </tr>
             <tr id="Melekat">
               <td>Melekat</td>
             </tr >
              <tr id="Uj.Menempel">
               <td>Uj.Menempel</td>
             </tr>
             <tr id="Lengkung">
               <td>Lengkung</td>
             </tr>
              <tr id="Terbalik">
               <td>Terbalik</td>
             </tr>
             <tr id="C.Lebar">
               <td>C.Lebar</td>
             </tr>
              <tr id="Slh.Posisi">
               <td>Slh.Posisi</td>
             </tr>
             <tr id="Kp.Rusak">
               <td>Kp.Rusak</td>
             </tr>
              <tr id="Patah">
               <td>Patah</td>
             </tr>
             <tr id="Lekukan">
               <td>Lekukan</td>
             </tr>
              <tr id="Kotor">
               <td>Kotor</td>
             </tr>
             <tr id="C.Sempit">
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
                <td height="100px" style="background-color: white; padding: 0px" width="3.125%"> <div style="background-color: white; height:90%;">
                   </div> <h6>Reed No.</h6> </td>
                <?php for ($i=32; $i >= 17; $i--) {  ?>
                <td height="100px" style="background-color: #DAA520; padding: 0px" width="3.125%"> <div style="background-color: black; height:90%;">
                  <div style="margin-left: 5px;margin-right: 5px; background-color: white; height: 90%">    
              @php
              $p = 'images/bundar.png';
              @endphp 
              <img src="{{ url($p) }}" class="user-image " alt="7 Poin" align="middle" width="15"></div>
                </div><b><?php echo $i ?></b> </td>
                <td height="100px" style="background-color: #DAA520";"width="3.125%"></td>
                <?php 
              }   
              ?>
             
             </tr>
          
         </table>
       </div>
    
   </div>
 </div>


 @endsection

 @section('scripts')
 <script >
  jQuery(document).ready(function() {
   $('body').toggleClass("sidebar-collapse");

  });

 function myFunction() {
      var row = document.getElementById("cargo");
      row.style.border="solid";
      var nomor = 0;
      for (i = 8; i < 38; i++) {
        nomor++;
        var x = row.insertCell(i);
        x.innerHTML = nomor;

      }

 </script>
 @stop