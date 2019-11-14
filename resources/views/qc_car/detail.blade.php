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
         <?php if ($cars->pic == NULL) { ?>
           <a class="btn btn-danger" data-toggle="modal" href="#modalkaryawan">Pilih Karyawan</a>
         <?php } ?>
         <input type="hidden" name="checkpic" id="checkpic" value="{{$cars->pic}}">

         <a href="{{url('index/qc_report/print_cpar', $cpar[0]->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain"  target="_blank">Preview CPAR Report</a>

         <a href="{{url('index/qc_car/print_car', $cars->id)}}" data-toggle="tooltip" class="btn btn-info btn-md" target="_blank">Print CAR</a><br/><br/>

         <?php if ($cars->pic != NULL) { ?>
            <b>PIC</b> : <label class="label label-success"> {{$cars->employee_pic->name}} </label>
            <br><br>
         

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
              
              if($tinjauan != NULL){
                $split = explode (",", $tinjauan);
                $hitungsplit = count($split);
              }else{
                $split = 0;
              }
            ?>

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

        <?php } ?>
      </div>
    </form>
  </div>
  
  <?php foreach ($cpar as $cpars){ ?>
  <div class="modal fade" id="modalkaryawan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Pilih Karyawan Yang Mengerjakan CPAR {{$cpars->cpar_no}}</h4>
        </div>
        <div class="modal-body">

          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="cars" value="{{ $cars->id }}">
            <center><a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain"  target="_blank">Preview CPAR Report</a></center><br><br>

            <!-- Kategori : {{$cpars->kategori}}
            Lokasi : {{$cpars->lokasi}} -->
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Foreman<span class="text-red">*</span></label>
              <div class="col-sm-8">
                <select class="form-control select3" id="pic" name="pic" style="width: 100%;" data-placeholder="Pilih PIC" required>
                  <option value=""></option>
                  @foreach($pic as $pic)
                    <option value="{{ $pic->employee_id }}">{{ $pic->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <button type="button" onclick="create_pic()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php } ?>
  
  @endsection
  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script type="text/javascript">

    var checkpic = $("#checkpic").val()

    if(checkpic == "") {
      $(window).on('load',function(){
          $('#modalkaryawan').modal('show');
      });
    }
  </script>

  <script>
    
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function () {
      $('.select2').select2()
    })

    $(function () {
      $('.select3').select2({
        dropdownParent: $('#modalkaryawan')
      });
    })

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

    function create_pic() {

      var data = {
        id: $("#id").val(),
        pic: $("#pic").val()
      };

      // console.log(data);

      $.post('{{ url("index/qc_car/create_pic/".$cars->id) }}', data, function(result, status, xhr){
        // console.log(result.status);
        if (result.status == true) {
          openSuccessGritter("Success","Pic has been choosen");
          window.location.reload();
        } else {
          openErrorGritter("Error","Cannot Create PIC");
        }
      })
    }

    CKEDITOR.replace('deskripsi' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('tindakan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('penyebab' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('perbaikan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
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
@stop