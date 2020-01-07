@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
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
    {{ $page }}
    <small>Verifikasi Corrective Action Report</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
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
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New CPAR</h3> --}}
    </div>
    @if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader || Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman)
    
    @if(Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader)
    <form role="form" method="post" enctype='multipart/form-data' action="{{url('index/qc_report/close1', $cpars->id)}}">
    @elseif(Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman)
    <form role="form" method="post" enctype='multipart/form-data' action="{{url('index/qc_report/close2', $cpars->id)}}">
    @endif 

      <div class="box-body">

        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">No CPAR<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" placeholder="Nasukkan Nomor CPAR" value="{{ $cpars->cpar_no }}" readonly="">
          </div>
          <a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain" target="_blank">CPAR Report</a>

         <a href="{{url('index/qc_car/print_car_new', $cars[0]->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank" >CAR Report</a>
         
         @if($cpars->posisi == "QA")
         <a class="btn btn-md btn-primary" data-toggle="tooltip" title="Send Email Ke Chief / Foreman" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email</a>
         @endif
        </div>

        


        @foreach($cars as $cars)

        <?php if ($cpars->file != null){ ?>
          <div class="box box-primary box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">File CPAR Yang Telah Diupload</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php $data = json_decode($cpars->file);
                for ($i = 0; $i < count($data); $i++) { ?>
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                </div>
              <?php } ?>                       
            </div>
          </div>    
        <?php } ?>

        <?php if ($cars->file != null){ ?>
          <div class="box box-success box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">File CAR Yang Telah Diupload</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php $data = json_decode($cars->file);
                for ($i = 0; $i < count($data); $i++) { ?>
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/car/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                </div>
              <?php } ?>                       
            </div>
          </div>    
        <?php } ?>


        @if($cpars->status_code != 1)
          <div class="form-group row" align="left">
            <div class="col-xs-12" style="margin-top: 3%; margin-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;">
                <label style="font-weight: bold; font-size: 18px;">
                  <span><i class="fa fa-photo"></i> Verifikasi</span>
                </label>
              </div>
              <div class="col-xs-1" style="padding: 0px;">
                <a class="btn btn-success" onclick='addVerifikasi();'><i class='fa fa-plus' ></i></a>
                <input type="text" id="jumlahVerif" name="jumlahVerif">
              </div>
            </div>
            <div id='verif'></div>
          </div>


          <div class="form-group row" align="left">
            <label class="col-sm-4">Cost Estimation (optional)</span></label>
            <div class="col-sm-12">
              <textarea type="text" class="form-control" name="cost">{{$cpars->cost}}</textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-success col-sm-14">Edit</button>

        @endif

        <!-- <table class="table table-striped table-bordered " style="border: 1px solid #f4f4f4">
          <thead>
            <tr style="background-color: #ff9800;border: none">
              <td width="75" style="text-align: center;border: none">Nomor</td>
              <td width="600" style="text-align: center;border: none">CAR</td>
              <td width="400">Upload Foto untuk Verifikasi</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td style="text-align: left">Deskripsi : <br><?= $cars->deskripsi ?></td>
              <td style="vertical-align: middle;">
              </td>
            </tr>

            <tr>
              <td>2</td>
              <td style="text-align: left">Immediately Action : <br><?= $cars->tindakan ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

            <tr>
              <td>3</td>
              <td style="text-align: left">Possibilty Cause : <br><?= $cars->penyebab ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

            <tr>
              <td>4</td>
              <td style="text-align: left">Corrective Action : <br><?= $cars->perbaikan ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

          </tbody>
        </table> -->
        
        @endforeach


      </div>
    </form>
    @endif
  </div>
  
  @endsection
  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script>
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function () {
      $('.select2').select2()
    })

    CKEDITOR.replace('cost' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah')
                    .attr('src', e.target.result)
                    .width(200)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
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

    function sendemail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/emailverification/$cpars->id") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    var ver = 1;
    var jumlahVerif = 1;
    function addVerifikasi() {
      $add = '<div class="col-xs-12" id="add_ver_'+ ver +'"> <div class="col-xs-3" style="color: black; padding: 0px; padding-right: 1%;"> <input type="file" id="gambar_'+ ver +'" name="gambar_'+ ver +'" data-placeholder="Upload File" style="width: 100%; height: 33px; font-size: 15px; text-align: center;"> </div>    <div class="col-xs-1" style="color: black; padding: 0px; padding-right: 1%;">Keterangan</div><div class="col-xs-4" style="color: black; padding: 0px; padding-right: 1%;"> <div class="form-group"> <input type="text" id="ket_'+ ver +'" name="ket_'+ ver +'" data-placeholder="Keterangan" style="width: 100%; height: 33px; font-size: 15px; text-align: center;" class="form-control"> </div></div><div class="col-xs-1" style="padding: 0px;"> <button class="btn btn-danger" onclick="remove('+ver+')"><i class="fa fa-close"></i></button> </div></div>';

      $('#verif').append($add);

      $('#jumlahVerif').val(jumlahVerif);
      jumlahVerif++;
      ver++;
    }

    function remove(id) {
    $("#add_ver_"+id).remove();

    if(ver != id){
      for (var i = id; i < ver; i++) {
        document.getElementById("add_ver_"+ (i+1)).id = "add_ver_"+ i;
        document.getElementById("gambar_"+ (i+1)).id = "gambar_"+ i;
        document.getElementById("ket"+ (i+1)).id = "ket"+ i;
      }   
    }
    ver--;
  }

  </script>
@stop








