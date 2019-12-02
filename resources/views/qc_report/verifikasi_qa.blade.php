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
    @if(Auth::user()->username == "clark" || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader || Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman)
    
    @if(Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader)
    <form role="form" method="post" action="{{url('index/qc_report/close1', $cpars->id)}}">
    @elseif(Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman)
    <form role="form" method="post" action="{{url('index/qc_report/close2', $cpars->id)}}">
    @endif 

      <div class="box-body">

        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">No CPAR<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" placeholder="Nasukkan Nomor CPAR" value="{{ $cpars->cpar_no }}" readonly="">
          </div>
          <a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain" target="_blank">CPAR Report</a>

         <a href="{{url('index/qc_car/print_car', $cars[0]->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank" >CAR Report</a>
         @if($cpars->posisi == "QA")
         <a class="btn btn-md btn-primary" data-toggle="tooltip" title="Send Email Ke Chief / Foreman" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email</a>
         @endif
        </div>

        @foreach($cars as $cars)

        @if($cpars->status_code != 1)

          <div class="form-group row" align="left">
            <label class="col-sm-2">Cost Estimation (optional)</span></label>
            <div class="col-sm-12">
              <textarea type="text" class="form-control" name="cost">{{$cpars->cost}}</textarea>
            </div>
          </div>
          @if(Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader)
              <button type="submit" class="btn btn-success col-sm-14">Edit</button>
          @elseif(Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman)
            <div class="col-sm-12">
              <button type="submit" class="btn btn-success col-sm-14" style="width: 100%; font-weight: bold; font-size: 20px">Close CPAR {{$cpars->cpar_no}}</button>
            </div>
          @endif

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

  </script>
@stop








