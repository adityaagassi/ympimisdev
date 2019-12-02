@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    background-color: #9c27b0;
    color: white;
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
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Verifikasi {{ $page }}
    <small>Verifikasi CAR</small>
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
      <div class="box-body">
        
        <?php foreach ($cars as $cars): ?>

        <!-- <br> To : {{ $cars->posisi }} -->

        <a data-toggle="modal" data-target="#statusmodal{{$cars->id}}" class="btn btn-primary btn-sm pull-right">Cek Status Verifikasi</a>
        
        <a href="{{url('index/qc_car/print_car', $cars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" style="margin-right: 5px;" title="Lihat Report"  target="_blank">Preview CAR Report</a>

        <!-- Email Chief -->

        @if($cars->email_status == "SentChief" && $cars->checked_chief == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == $cars->verifikatorchief && $cars->email_status == "SentManager")
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Foreman -->

        @if($cars->email_status == "SentForeman" && $cars->checked_foreman == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == $cars->verifikatorforeman && $cars->email_status == "SentManager") <!-- Jika yang login Foreman dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Coordinator -->

        @if($cars->email_status == "SentCoordinator" && $cars->checked_coordinator == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == $cars->verifikatorcoordinator && $cars->email_status == "SentManager") <!-- Jika yang login Coordinator dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Manager -->        

        @if($cars->email_status == "SentManager" && $cars->checked_manager == "Checked") <!-- Manager -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke DGM" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke DGM</a>

        @elseif(Auth::user()->username == $cars->manager && $cars->email_status == "SentDGM") <!-- Jika yang login Manager dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email DGM -->

        @if($cars->email_status == "SentDGM" && $cars->approved_dgm == "Checked") <!-- DGM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke GM" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke GM</a>

        @elseif(Auth::user()->username == $cars->dgm && $cars->email_status == "SentGM") <!-- Jika yang login DGM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email GM --> 

        @if($cars->email_status == "SentGM" && $cars->approved_gm == "Checked") <!-- GM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke QA" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke QA</a>

          <!-- <a href="{{url('index/qc_car/sendemail/'.$cars['id'].'/'.$cars['posisi'])}}" class="btn btn-sm ">Email </a> -->

        @elseif(Auth::user()->username == $cars->gm && $cars->email_status == "SentQA") <!-- Jika yang login GM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim Ke QA</label>

        @endif

        <br/><br/>

        <table class="table table-hover">
          <form role="form" method="post" action="{{url('index/qc_car/checked/'.$cars->id)}}">
          	 <thead>
              <tr>
                <th colspan="6" style="background-color: #ef6c00; color: white; font-size: 20px;border: none"><b>VERIFIKASI CAR </b></th>
              </tr>
              <tr>
                <th colspan="1" style="width: 20%;border: none"><b>Point</b></th>
                <th colspan="4" style="width: 60%;border: none"><b>Content</b></th>
                <th colspan="1" style="width: 20%;border: none"><b>Checked</b></th>
              </tr>
            </thead>
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                <tr>
                  <td colspan="1">Deskripsi</td>
                  <td colspan="4"><?= $cars->deskripsi ?></td>
                  <td colspan="1">
                      @if(Auth::user()->username == $cars->verifikatorchief || Auth::user()->username == $cars->verifikatorforeman || Auth::user()->username == $cars->verifikatorcoordinator)
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->employee_id)
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1">Tindakan</td>
                  <td colspan="4"><?= $cars->tindakan ?></td>
                  <td colspan="1">
                  	@if(Auth::user()->username == $cars->verifikatorchief || Auth::user()->username == $cars->verifikatorforeman || Auth::user()->username == $cars->verifikatorcoordinator)
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->employee_id)
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1">Penyebab</td>
                  <td colspan="4"><?= $cars->penyebab ?></td>
                  <td colspan="1">
                  	@if(Auth::user()->username == $cars->verifikatorchief || Auth::user()->username == $cars->verifikatorforeman || Auth::user()->username == $cars->verifikatorcoordinator)
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->employee_id)
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1">Perbaikan</td>
                  <td colspan="4"><?= $cars->perbaikan ?></td>
                  <td colspan="1">
                  	@if(Auth::user()->username == $cars->verifikatorchief || Auth::user()->username == $cars->verifikatorforeman || Auth::user()->username == $cars->verifikatorcoordinator)
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->employee_id)
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
            </tbody>
        </table>
        
        <br>

        <div class="col-sm-12">
          <button type="submit" class="btn btn-success col-sm-14" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
        </div>

        <?php endforeach ?>
      </div>
    </form>
  </div>

  <div class="modal fade" id="statusmodal{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Status CPAR Sekarang</h4>
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
                            Leader
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
                            Foreman
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
                    @if(($cars->email_status == "SentManager" || $cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "manager" || $cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "QA"))
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
                    @if(($cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "QA"))
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
                    @if(($cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "gm" || $cars->posisi == "QA"))
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
                    @if($cars->email_status == "SentQA" && $cars->posisi == "QA")
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

  function sendemail(id) {
      var data = {
        id: id,
      };

      if (!confirm("Apakah anda yakin ingin mengirim CAR ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_car/sendemail/$cars->id/$cars->posisi") }}', data, function(result, status, xhr){
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