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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

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

         <a href="{{url('index/qc_car/print_car_new', $cars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank">Preview CAR Report</a>

         <a data-toggle="modal" data-target="#statusmodal{{$cars->id}}" class="btn btn-primary btn-md" style="color:white;margin-right: 5px">Cek Status Verifikasi</a>

         <!-- <a href="{{url('index/qc_car/sendemail/'.$cars['id'].'/'.$cars['posisi'])}}" class="btn btn-sm ">Email </a> -->
         @if($cars->deskripsi != null && $cars->tindakan != null && $cars->penyebab != null && $cars->perbaikan != null )
           @if(($cars->email_status == "SentStaff" && $cars->posisi == "staff") || ($cars->email_status == "SentForeman" && $cars->posisi == "foreman"))
              <a class="btn btn-md btn-default" data-toggle="tooltip" title="Send Email" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email</a>
              <!-- <a class="btn btn-success" data-toggle="modal" data-target="#email{{$cars->id}}">Send Email</a> -->
           @else
               <label class="label label-success" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
           @endif
          @endif
         
         <br/><br/>

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
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" 
              <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 1) {
                  echo 'checked';
                }
              }?> name="tinjauan4m[]" value="1" id="tinjauan4m">Man
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="2" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 2) {
                  echo 'checked';
                }
              }?>>Machine
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="3" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 3) {
                  echo 'checked';
                }
              }?>>Material
            </label>
            <label class="checkbox-inline">
              <input type="checkbox" class="tinjauan4mCheckbox" name="tinjauan4m[]" value="4" id="tinjauan4m" <?php $tinjauan = explode(',',$cars->tinjauan);
              foreach ($tinjauan as $key) {
                if ($key == 4) {
                  echo 'checked';
                }
              }?>>Method
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
            <input type="file" name="files[]" multiple>
            <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button>
            <!-- {{ $cars->file }} -->
            <!-- <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button> -->
          </div>
        </div>
        <div class="clone hide">
          <div class="form-group row control-group" style="margin-top:10px">
            <label class="col-sm-1">File</label>
            <div class="col-sm-6">
              <input type="file" name="files[]">
              <div class="input-group-btn"> 
                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
              </div>
            </div>
          </div>
        </div>

        <?php if ($cars->file != null){ ?>
            <br><br>
              <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">File Yang Telah Diupload</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i> </button>
                  </div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                  <?php $data = json_decode($cars->file);
                    for ($i = 0; $i < count($data); $i++) { ?>
                    <div class="col-md-12">
                      <div class="col-md-3">
                        <div class="isi">
                          <?= $data[$i] ?>
                        </div>
                      </div>
                      <div  class="col-md-2">
                          <a href="{{ url('/files/car/'.$data[$i]) }}" class="btn btn-primary pull-right">Download / Preview</a>
                      </div>
                      <div class="col-md-1">
                          <a href="javascript:void(0)" onclick="hapus('{{$data[$i]}}','{{$cars->id}}')" class="btn btn-danger pull-left">
                            <i class="fa fa-trash"></i></a>
                      </div>                      
                    </div>
                    <br><br><br>
                  <?php } ?>                       
                </div>
              </div>    
          <?php } ?>
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
          <h4 class="modal-title" id="myModalLabel">Pilih PIC Yang Mengerjakan CPAR {{$cpars->cpar_no}}</h4>
        </div>
        <div class="modal-body">

          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="cars" value="{{ $cars->id }}">
            <center><h4><b> Komplain : {{ $cpars->judul_komplain }} </b></h4></center>
            <center><h5>Sumber : {{ $cpars->sumber_komplain }} </h5></center>
            <br>

            <center><a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain"  target="_blank">Preview CPAR Report</a></center><br><br>

            <!-- Kategori : {{$cpars->kategori}}
            Lokasi : {{$cpars->lokasi}} -->
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">
                  Staff / Foreman
                  <span class="text-red">*</span></label>
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
            <button type="button" onclick="create_pic()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-send"></i>    Confirm And Send</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="statusmodal{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Status CAR</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <table class="table table-hover">
              <tbody>
                <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                  <tr style="background-color: #4caf50;color: white">
                      <td colspan="2" style="width: 33%"><b>Position</b></td>
                      <td colspan="2" style="width: 33%"><b>Action</b></td>
                      <td colspan="2" style="width: 33%"><b>Email</b></td>
                  </tr>
                  <tr>
                      <td colspan="2"><b>
                        @if($cars->car_cpar->kategori == "Internal") 
                              Staff / Leader
                          @elseif($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") 
                              Staff
                          @endif
                      </b></td>
                      @if(($cars->email_status == "SentStaff" && $cars->posisi == "staff") || ($cars->email_status == "SentForeman" && $cars->posisi == "foreman")) 
                      <td colspan="2"><b><span class="label label-success">On Progress</span></b></td>
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-warning">Verification</span></b></td>
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2">
                        <b>
                          @if($cars->car_cpar->kategori == "Internal") 
                              Chief / Foreman
                          @elseif($cars->car_cpar->kategori == "Eksternal") 
                              Chief
                          @elseif($cars->car_cpar->kategori == "Supplier")
                              Coordinator
                          @endif
                        </b>
                      </td>
                      <td colspan="2"><b>
                        @if($cars->checked_chief == "Checked" || $cars->checked_foreman == "Checked" || $cars->checked_coordinator == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentManager" || $cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "manager" || $cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>Manager</b></td>
                      <td colspan="2"><b>
                        @if($cars->checked_manager == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>DGM</b></td>
                      <td colspan="2"><b>
                        @if($cars->approved_dgm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "gm" || $cars->posisi == "qa"))
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>GM</b></td>
                      <td colspan="2"><b>
                        @if($cars->approved_gm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if($cars->email_status == "SentQA" && $cars->posisi == "qa")
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
              </tbody>
          </table>
          </div>    
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

<!-- <div class="modal fade" id="email{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Pilih Verifikator yang dituju</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
           <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <label class="col-sm-3">Chief / Foreman / Manager<span class="text-red"> *</span></label>
              <div class="col-sm-8">
                <select class="form-control select2" id="cf" name="cf" style="width: 100%;" data-placeholder="Pilih Chief / Foreman / Manager" required>
                  <option value=""></option>
                  @foreach($cfm as $cfm)
                  <option value="{{ $cfm->employee_id }}">{{ $cfm->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div> -->

  <?php } ?>
  
  @endsection
  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script type="text/javascript">

    $(".plusdata").click(function(){ 
        var html = $(".clone").html();
        $(".increment").after(html);
    });

     $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

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
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '300px'
    });

    CKEDITOR.replace('tindakan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '300px'
    });

    CKEDITOR.replace('penyebab' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '300px'
    });

    CKEDITOR.replace('perbaikan' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
        height: '300px'
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

    function hapus(nama_file,idcar){
        var data = {
          nama_file : nama_file,
          idcar : idcar
        };
        $.post('{{ url("index/qc_car/deletefiles") }}', data, function(result, status, xhr){
          if(result.status){
            openSuccessGritter('Success Hapus File', result.message);
            location.reload();
          }
          else{
            openErrorGritter('Error!', result.message);
          }
        })
      }
    
    function sendemail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim CAR ini?")) {
        return false;
      }

      $("#loading").show();

      $.get('{{ url("index/qc_car/sendemail/$cars->id/$cars->posisi") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

  </script>
@stop