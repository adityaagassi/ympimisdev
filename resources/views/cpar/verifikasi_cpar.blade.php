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
    <small>Verifikasi Form</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}

   <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $cpar['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report Form</a>
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
              @if(($user == $cpar->chief || $user == $cpar->foreman || Auth::user()->role_code == "MIS") && $cpar->approvalcf == null && $cpar->posisi == "cf")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @elseif(($user == $cpar->manager || Auth::user()->role_code == "MIS") && $cpar->approvalm == null && $cpar->posisi == "m")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @endif
            </tr>
          </thead>
          <tbody>
            <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/approval/'.$cpar->id)}}">
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Keterangan Umum</b></td>
              @if(($user == $cpar->chief || $user == $cpar->foreman || Auth::user()->role_code == "MIS") && $cpar->approvalcf == null && $cpar->posisi == "cf")
              <td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @elseif(($user == $cpar->manager || Auth::user()->role_code == "MIS") && $cpar->approvalm == null && $cpar->posisi == "m")
              <td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
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
              <td colspan="2" style="border:none;width: 25%">Subject / Judul</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= strtoupper($cpar->judul) ?> (<?= $cpar->kategori ?>)</b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Tanggal Dibuat</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?php echo date('d F Y', strtotime($cpar->tanggal)) ?></b></td>
            </tr>
            <?php 
              $secfrom = explode('_',$cpar->section_from);
              $secto = explode('_',$cpar->section_to);
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
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Material / Item</b></td>
              @if(($user == $cpar->chief || $user == $cpar->foreman || Auth::user()->role_code == "MIS") && $cpar->approvalcf == null && $cpar->posisi == "cf")
              <td colspan="2" rowspan="{{ 3 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="3">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $cpar->manager || Auth::user()->role_code == "MIS") && $cpar->approvalm == null && $cpar->posisi == "m")
              <td colspan="2" rowspan="{{ 3 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
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
              <td colspan="4" style="border-top: 1px solid black">GMC - Nama Material</td>
              <td colspan="2" style="border-top: 1px solid black">Jumlah Cek</td>
              <td colspan="2" style="border-top: 1px solid black">Jumlah NG</td>
              <td colspan="2" style="border-top: 1px solid black">% NG</td>
            </tr>
            
            <?php 
            $jumlahitem = count($items);
            if($jumlahitem != 0) { 

            ?>
            @foreach($items as $item)
            <tr>
              <td colspan="4"><b>{{$item->item}} - {{$item->item_desc}}</b></td>
              <td colspan="2"><b>{{$item->jml_cek}} Pcs</b></td>
              <td colspan="2"><b>{{$item->jml_ng}} Pcs</b></td>
              <td colspan="2"><b>{{$item->presentase_ng}} %</b></td>
            </tr>
            @endforeach
            <?php }
            else { 
            ?>
            <tr>
              <td colspan="4">-</td>
              <td colspan="2">-</td>
              <td colspan="2">-</td>
              <td colspan="2">-</td>
            </tr>
            <tr></tr>
            <?php } ?>
            <?php if($jumlahitem != 0) { ?> 
            <tr>
              <td colspan="10" style="border: 1px solid black"><p style="font-size: 14px">Detail Ketidaksesuaian : </p><b><?= $item->detail ?></b></td>
            </tr>
            <?php } else { ?>
            <tr>
              <td colspan="10" style="border: 1px solid black"><p style="font-size: 14px;">Detail Ketidaksesuaian : - </p></td>
            </tr> 
            <?php } ?>
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Penanganan Oleh Produksi</b></td>
              @if(($user == $cpar->chief || $user == $cpar->foreman || Auth::user()->role_code == "MIS") && $cpar->approvalcf == null && $cpar->posisi == "cf")
              <td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $cpar->manager || Auth::user()->role_code == "MIS") && $cpar->approvalm == null && $cpar->posisi == "m")
              <td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
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
              <td colspan="2" style="border:none">Target Perhari</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b>
                @if($cpar->target != "") 
                  {{ $cpar->target }} Pcs/hari
                @else
                  -
                @endif
                </b>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border:none">Jumlah Perkiraan Keterlambatan</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none">
                <b>
                @if($cpar->jumlah != "") 
                  {{ $cpar->jumlah }} Pcs
                @else
                  -
                @endif
                </b>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border:none">Waktu Penanganan Masalah</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none;"><b> 
                @if($cpar->waktu != "") 
                  {{ $cpar->waktu }} Menit
                @else
                  -
                @endif
              </b></td>
            </tr>
            <tr>
              <td colspan="2" style="border:none">Corrective Action Oleh Section Pelapor</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none">
                <b>
                @if($cpar->aksi != "") 
                  <?= $cpar->aksi ?>
                @else
                  -
                @endif  
                </b>  
              </td>
            </tr>
            <tr>
              <td colspan="7" rowspan="3" style="border-top: 1px solid black">&nbsp;</td>
              <td style="border-top: 1px solid black">Pelapor</td>
              <td style="border-top: 1px solid black">Mengetahui</td>
              <td style="border-top: 1px solid black">Mengetahui</td>
              @if(($user == $cpar->chief || $user == $cpar->foreman || Auth::user()->role_code == "MIS") && $cpar->approvalcf == null && $cpar->posisi == "cf")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 0">
                <center>
                  <button class="btn btn-success" type="submit"  style="font-weight: bold;">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved{{$cpar->id}}" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Form</a>
                </center>
              </td>

              @elseif(($user == $cpar->manager || Auth::user()->role_code == "MIS") && $cpar->approvalm == null && $cpar->posisi == "m")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 0">
                <center>
                  <button class="btn btn-success" type="submit"  style="font-weight: bold;">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved{{$cpar->id}}" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Form</a>
                </center>
              </td>
              @endif
              
            </tr>
            <tr>
              <td style="vertical-align: middle;">
                @if($cpar->posisi == "sl" || $cpar->posisi == "cf" || $cpar->posisi == "m")
                  {{$sl}}
                @elseif($cpar->approvalcf == "Approved" || $cpar->approvalm == "Approved")
                  {{$sl}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($cpar->approvalcf == "Approved" || $cpar->approvalm == "Approved")
                  {{$cf}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($cpar->approvalm == "Approved")
                  {{$m}}
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
        <!-- <?php if ($cpar->file != null){ ?>

        <div class="box box-warning box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">File Terlampir</h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="box-body">
            <?php $data = json_decode($cpar->file);
              for ($i = 0; $i < count($data); $i++) { ?>
              <div class="col-md-12">
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/'.$data[$i]) }}" target="_blank" class="btn btn-primary">Download / Preview</a>
                </div>
              </div>
            <?php } ?>                       
          </div>
        </div> 
          
        <?php } ?> -->
   
        <!-- <div class="col-sm-12">

         
          <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          <a data-toggle="modal" data-target="#notapproved{{$cpar->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject Form</a>          

        </div> -->
      </div>
    </form>
  </div>

  <div class="modal modal-danger fade" id="notapproved{{$cpar->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/notapprove/'.$cpar->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui form ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Akan Dikirim kembali ke Pembuat Form Ketidaksesuaian
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

      if (!confirm("Apakah anda yakin ingin mengirim CPAR ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/sendemail/$cpar->id/$cpar->posisi") }}', data, function(result, status, xhr){
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