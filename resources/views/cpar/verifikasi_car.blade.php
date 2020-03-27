@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  input[type=checkbox] {
    transform: scale(1.25);
  }
  thead>tr>th{
    /*text-align:center;*/
    background-color: #7e5686;
    color: white;
    border: none;
    border:1px solid black;
    border-bottom: 1px solid black !important;
  }
  tbody>tr>td{
    /*text-align:center;*/
    border: 1px solid black;
  }
  tfoot>tr>th{
    /*text-align:center;*/
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
  }

  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Verifikasi {{ $page }}
    <small>Verifikasi Penanganan</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}

   <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $car['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report</a>
 </ol>
</section>

@endsection
@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        <?php $user = STRTOUPPER(Auth::user()->username)?>

        <table class="table" style="border: 1px solid black;">
            <thead>
            <tr>
              
              <th colspan="2" class="centera" >
                <center><img width="150px" src="{{ asset('images/logo_yamaha3.png') }}" alt="" style="vertical-align: middle !important"></center>
              </th>

              <th colspan="8" style="text-align: center; vertical-align: middle;font-size: 22px;font-weight: bold">Form Laporan Ketidaksesuaian</th>
              @if(($user == $car->chief_car || $user == $car->foreman_car || Auth::user()->role_code == "MIS") && $car->approvalcf_car == null && $car->posisi == "deptcf")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @elseif(($user == $car->manager_car || Auth::user()->role_code == "MIS") && $car->approvalm_car == null && $car->posisi == "deptm")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @endif
            </tr>
          </thead>
          <tbody>
            <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/approvalcar/'.$car->id)}}">
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Keterangan Umum</b></td>
              @if(($user == $car->chief_car || $user == $car->foreman_car || Auth::user()->role_code == "MIS") && $car->approvalcf_car == null && $car->posisi == "deptcf")
              <td colspan="2" rowspan="6" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @elseif(($user == $car->manager_car || Auth::user()->role_code == "MIS") && $car->approvalm_car == null && $car->posisi == "deptm")
              <td colspan="2" rowspan="6" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>

              @endif
            </tr>
            <tr>
              <td colspan="2" style="border:none;width: 25%">Judul Komplain</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= strtoupper($car->judul) ?> (<?= $car->kategori ?>)</b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Tanggal Form Dibuat</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?php echo date('d F Y', strtotime($car->tanggal)) ?></b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Tanggal Penanganan Dibuat</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?php echo date('d F Y', strtotime($car->tanggal_car)) ?></b></td>
            </tr>
            <?php 
              $secfrom = explode('_',$car->section_from);
              $secto = explode('_',$car->section_to);
            ?>
            <tr>
              <td colspan="2" style="border:none">Section Pelapor</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b><?= $secfrom[0]; ?> - <?= $secfrom[1] ?></b></td>
            </tr>

            <tr>
              <td colspan="2"style="border:none">Section Yang Dituju</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b><?= $secto[0]; ?> - <?= $secto[1] ?></b></td>
            </tr>
            <?php 
              $jumlahitem = count($items);
            ?>
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Deskripsi Permasalahan</b></td>
              @if(($user == $car->chief_car || $user == $car->foreman_car || Auth::user()->role_code == "MIS") && $car->approvalcf_car == null && $car->posisi == "deptcf")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $car->manager_car || Auth::user()->role_code == "MIS") && $car->approvalm_car == null && $car->posisi == "deptm")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
            </tr>
            
            <tr>
              <td colspan="10" style="border:none">
                @if($car->deskripsi_car != "") 
                  <?= $car->deskripsi_car ?>
                @else
                  -
                @endif  
              </td>
            </tr>

            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Penanganan Yang Dilakukan</b></td>
              @if(($user == $car->chief_car || $user == $car->foreman_car || Auth::user()->role_code == "MIS") && $car->approvalcf_car == null && $car->posisi == "deptcf")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $car->manager_car || Auth::user()->role_code == "MIS") && $car->approvalm_car == null && $car->posisi == "deptm")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
            </tr>

            <tr>
              <td colspan="10" style="border:none">
                @if($car->penanganan_car != "") 
                  <?= $car->penanganan_car ?>
                @else
                  -
                @endif  
              </td>
            </tr>

            <tr>
              <td colspan="7" rowspan="3" style="border-top: 1px solid black">&nbsp;</td>
              <td style="border-top: 1px solid black">PIC</td>
              <td style="border-top: 1px solid black">Mengetahui</td>
              <td style="border-top: 1px solid black">Mengetahui</td>
              @if(($user == $car->chief_car || $user == $car->foreman_car || Auth::user()->role_code == "MIS") && $car->approvalcf_car == null && $car->posisi == "deptcf")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 0">
                <center>
                  <button class="btn btn-success" type="submit"  style="font-weight: bold;">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved{{$car->id}}" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Penanganan</a>
                </center>
              </td>

              @elseif(($user == $car->manager_car || Auth::user()->role_code == "MIS") && $car->approvalm_car == null && $car->posisi == "deptm")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 0">
                <center>
                  <button class="btn btn-success" type="submit"  style="font-weight: bold;">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved{{$car->id}}" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Penanganan</a>
                </center>
              </td>
              @endif
              
            </tr>
            <tr>
              <td style="vertical-align: middle;">
                @if($car->posisi == "dept" || $car->posisi == "deptcf" || $car->posisi == "deptm")
                  {{$pic}}
                @elseif($car->approvalcf == "Approved" || $car->approvalm == "Approved")
                  {{$pic}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($car->approvalcf_car == "Approved" || $car->approvalm_car == "Approved")
                  {{$cfcar}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($car->approvalm_car == "Approved")
                  {{$mcar}}
                @else
                  
                @endif
              </td>
            </tr>
            <tr>
              <td>Leader / Staff</td>
              <td>Foreman / Chief</td>
              <td>Manager</td>
            </tr>
          </tbody>
       </table>
      </div>
    </form>
  </div>

  <div class="modal modal-danger fade" id="notapproved{{$car->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/notapprovecar/'.$car->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui penanganan ini</h4>
                <textarea class="form-control" required="" name="alasan_car" style="height: 250px;"></textarea> 
                *Form Penanganan Akan Dikirim kembali lagi ke PIC masing - masing departemen
            </div>    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline">Not Approved</a>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  $(document).ready(function() {

    $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
    });
    $('body').toggleClass("sidebar-collapse");
  });


  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  function sendemail(id) {
      var data = {
        id: id,
      };

      if (!confirm("Apakah anda yakin ingin mengirim car ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/sendemail/$car->id/$car->posisi") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
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