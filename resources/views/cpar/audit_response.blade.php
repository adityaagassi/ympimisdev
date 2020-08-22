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
     Response {{ $page }}
    <small><b>E</b>lectronic-<b>I</b>nternal <b>R</b>equest <b>C</b>orrective <b>A</b>ction</small>
  </h1>
  <ol class="breadcrumb" style="width: 500px">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}

    <?php $user = STRTOUPPER(Auth::user()->username)?>

    @if($std->auditor_penyebab != null && $std->auditee_perbaikan != null && $std->auditee_pencegahan != null && $std->auditee_perbaikan != null)

     @if($std->posisi == "auditee")
         <a class="btn btn-sm btn-success pull-right" data-toggle="tooltip" title="Send Email" onclick="sendemailpenanganan({{ $std->id }})" style="width:200px">Send Email</a>
     @elseif($std->posisi != "auditee")
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
     @else
         
     @endif

    @endif

     <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/audit_iso/print', $std['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report PDF</a>
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
          <form role="form" method="post" action="{{url('index/audit_iso/update_response', $std->id)}}" enctype="multipart/form-data">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <label class="col-sm-12" style="font-size: 24px">Penanganan Oleh Auditee</label>

              <div class="col-sm-12">              
                <div class="col-sm-6" style="padding-left: 0">
                  <label class="col-sm-6" style="font-size: 18px;padding-left: 0">Identitas Auditee</label>
                  <input type="text" id="subject" class="form-control" value="{{$std->auditee}} - {{$std->auditee_name}}" readonly>
                </div>
                <div class="col-sm-6" style="padding-left: 0">
                  <label class="col-sm-6" style="font-size: 18px;padding-left: 0">Due Date</label>
                  <input type="text" id="subject" class="form-control" value="{{$std->auditee_due_date}}" readonly>
                </div>
              </div>


              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Penyebab Permasalahan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="auditor_penyebab" placeholder="Penyebab Permasalahan"><?= $std->auditor_penyebab  ?></textarea>
                </div>
              </div>

              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Tindakan Perbaikan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="auditee_perbaikan" placeholder="Tindakan Perbaikan" style="height: 100%"><?= $std->auditee_perbaikan ?></textarea>
                </div>
              </div>


              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Tindakan Pencegahan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="auditee_pencegahan" placeholder="Tindakan Pencegahan"><?= $std->auditee_pencegahan  ?></textarea>
                </div>
              </div>

              <div class="col-sm-6" style="margin-top: 15px">              
                <label class="col-sm-12" style="font-size: 18px;padding-left: 0">Biaya Yang Dikeluarkan Untuk Perbaikan / Uraian Perbaikan</label>
                <div class="col-sm-12" style="padding-left: 0">
                  <textarea type="text" class="form-control" name="auditee_biaya" placeholder="Masukkan Biaya"><?= $std->auditee_biaya  ?></textarea>
                </div>
              </div>

              <div class="col-sm-12"> 
                <br>
                <center>
                  <button type="submit" class="btn btn-success" style="width: 40%;font-size: 20px;font-weight: bold">Save Data</button>
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

    function sendemailpenanganan(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Form ini?")) {
        return false;
      }

      $.get('{{ url("index/audit_iso/sendemailpenanganan/$std->id") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }


    CKEDITOR.replace('auditor_penyebab' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('auditee_perbaikan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '350px'
    });

    CKEDITOR.replace('auditee_pencegahan' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
      height: '350px'
    });

    CKEDITOR.replace('auditee_biaya' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
      height: '350px'
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