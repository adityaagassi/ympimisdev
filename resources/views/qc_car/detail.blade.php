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
    Detail {{ $page }}
    <small>Detail Corrective Action Report</small>
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
    <form role="form" method="post" action="{{url('index/qc_car/detail_action', $cars->id)}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">No CPAR<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" placeholder="Nasukkan Nomor CPAR" value="{{ $cars->cpar_no }}" readonly="">
          </div>

          <label class="col-sm-2">Tinjauan 4M<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <?php 
              $tinjauan = $cars->tinjauan; 
              $split = explode (",", $tinjauan);?>
            <label class="checkbox-inline">
              <?php if ($split[0] == "0" ){ ?>
              <input type="hidden" name="tinjauan[]" value="0" id="manhidden">
              <?php } ?>
              <input type="checkbox" name="tinjauan[]" value="1" id="man" 
              <?php if ($split[0] == "1" ){
                echo "checked"; 
              } ?>>Man<br>
            </label>
            <label class="checkbox-inline">
              <?php if ($split[1] == "0" ){ ?>
              <input type="hidden" name="tinjauan[]" value="0" id="machinehidden">
              <?php } ?>
              
              <input type="checkbox" name="tinjauan[]" value="1" id="machine" <?php if ($split[1] == "1" ){
                echo "checked"; 
              } ?>>Machine<br>
            </label>
            <label class="checkbox-inline">
              <?php if ($split[2] == "0" ){ ?>
              <input type="hidden" name="tinjauan[]" value="0" id="materialhidden">
              <?php } ?>
              <input type="checkbox" name="tinjauan[]" value="1" id="material" <?php if ($split[2] == "1" ){
                echo "checked"; 
              } ?>>Material<br>
            </label>
            <label class="checkbox-inline">
              <?php if ($split[3] == "0" ){ ?>
              <input type="hidden" name="tinjauan[]" value="0" id="methodhidden">
              <?php } ?>
              <input type="checkbox" name="tinjauan[]" value="1" id="method" <?php if ($split[3] == "1" ){
                echo "checked"; 
              } ?>>Method<br>
            </label>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Deskripsi<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="deskripsi" placeholder="Masukkan Deskripsi">{{ $cars->deskripsi }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Immediately Action<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="tindakan" placeholder="Masukkan Tindakan Segera">{{ $cars->tindakan }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Possibility Cause<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="penyebab" placeholder="Masukkan Penyebab">{{ $cars->penyebab }}</textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Corrective Action<span class="text-red">*</span></label>
          <div class="col-sm-11">
            <textarea type="text" class="form-control" name="perbaikan" placeholder="Masukkan perbaikan">{{ $cars->perbaikan }}</textarea>
          </div>
        </div>
        <div class="form-group row increment" align="left">
          <label class="col-sm-1">File</label>
          <div class="col-sm-5">
            <input type="file" name="file">
            {{ $cars->file }}
            <!-- <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button> -->
          </div>
        </div>

        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_car') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
    
  </div>

  @endsection

  
  @section('scripts')      
  <script>
    
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function(){
        $('#man').click(function() {
            if($(this).is(':checked'))
                $("#manhidden").remove();
            else
                document.getElementById("man").value = "0";
        });
        
        $('#machine').click(function() {
            if($(this).is(':checked'))
                $("#machinehidden").remove();
            else
                document.getElementById("machine").value = "0";
        });

        $('#material').click(function() {
            if($(this).is(':checked'))
                $("#materialhidden").remove();
            else
                document.getElementById("material").value = "0";
        });

        $('#method').click(function() {
            if($(this).is(':checked'))
                $("#methodhidden").remove();
            else
                document.getElementById("method").value = "0";
        });
    });

    $(function () {
      $('.select2').select2()
    })

    CKEDITOR.replace('deskripsi' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

  </script>
@stop