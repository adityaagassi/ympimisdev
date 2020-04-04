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

  .col-xs-2{
    padding-top: 5px;
  }
  .col-xs-3{
    padding-top: 5px;
  }
  .col-xs-5{
    padding-top: 5px;
  }
  .col-xs-6{
    padding-top: 5px;
  }
  .col-xs-7{
    padding-top: 5px;
  }
  .col-xs-8{
    padding-top: 5px;
  }
  
  #loading, #error { display: none; }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <small>Detail Form Ketidaksesuaian Material</small>
  </h1>
  <ol class="breadcrumb" style="width: 500px">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}

    <?php $user = STRTOUPPER(Auth::user()->username)?>

    @if($cpar->deskripsi_car != null && $cpar->penanganan_car != null)

     @if($cpar->posisi == "dept" && ($user == $cpar->pic_car || Auth::user()->role_code == "MIS"))
         <a class="btn btn-sm btn-success pull-right" data-toggle="tooltip" title="Send Email" onclick="sendemailcar({{ $cpar->id }})" style="width:200px">Send Email</a>
     @elseif($cpar->posisi != "dept" && ($user == $cpar->pic_car || Auth::user()->role_code == "MIS"))
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
     @else
         
     @endif

    @endif

     <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $cpar['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report Form</a>
 </ol>
    
</section>

@stop

@section('content')
<section class="content" style="padding: 10px">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible" style="margin-top: 10px">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible" style="margin-top: 10px">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible" style="margin-top: 10px">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

    <div class="row" style="margin-top: 10px">
      <div class="col-xs-12">
        <div class="box">
          <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/update_car', $cpar->id)}}" enctype="multipart/form-data">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <label class="col-sm-12" style="font-size: 24px">Penanganan Oleh Section Terkait</label>

              <div class="col-sm-12">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Identitas Pembuat</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <input type="text" id="subject" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly>
                  <input type="hidden" name="pic_car" class="form-control" value="{{$employee->employee_id}}" readonly>
                </div>
              </div>

              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Deskripsi Permasalahan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="deskripsi_car" placeholder="Masukkan Deskripsi" style="height: 100%"><?= $cpar->deskripsi_car ?></textarea>
                </div>
              </div>

              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Penanganan / Keputusan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="penanganan_car" placeholder="Masukkan Penanganan"><?= $cpar->penanganan_car ?></textarea>
                </div>
              </div>

              <div class="col-sm-12"> 
                <br>
                <center>
                  <button type="submit" class="btn btn-success" style="width: 40%;font-size: 20px;font-weight: bold">Update Data</button>
                </center>
              </div>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
</section>
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

  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {

    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');

    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
    
  });


    $(function () {
      $('.select2').select2()
    })
    
    $(function () {
      $('.select3').select2({
        dropdownParent: $('#createModal')
      });
      $('.select4').select2({
        dropdownParent: $('#EditModal')
      });
    })

    function sendemailcar(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Form ini?")) {
        return false;
      }

      $.get('{{ url("index/form_ketidaksesuaian/sendemailcar/$cpar->id") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }


    CKEDITOR.replace('deskripsi_car' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '400px'
    });

    CKEDITOR.replace('penanganan_car' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
      height: '400px'
    });

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
  @endsection