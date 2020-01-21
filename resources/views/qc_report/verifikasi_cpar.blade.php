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
    background-color: #7e5686;
    color: white;
    border: none;
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
    <small>Verifikasi CPAR</small>
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
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">
        
        <?php foreach ($cparss as $cpars): ?>

        <br> To : {{ $cpars->posisi }}

        <a data-toggle="modal" data-target="#statusmodal{{$cpars->id}}" class="btn btn-primary btn-sm pull-right">Cek Status Verifikasi</a>

        <a href="{{url('index/qc_report/print_cpar', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" title="Lihat Report"  target="_blank" style="color:white;margin-right: 5px">Preview CPAR Report</a>

        <!-- Email Chief -->

        @if($cpars->email_status == "SentChief" && $cpars->checked_chief == "Checked") <!-- Bu Ratri -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == $cpars->chief && $cpars->email_status == "SentManager") <!-- Jika yang login Bu Ratri dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Foreman -->

        @if($cpars->email_status == "SentForeman" && $cpars->checked_foreman == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == $cpars->foreman && $cpars->email_status == "SentManager") <!-- Jika yang login Foreman dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Manager -->        

        @if($cpars->email_status == "SentManager" && $cpars->checked_manager == "Checked") <!-- Manager -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke DGM" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke DGM</a>

        @elseif(Auth::user()->username == $cpars->manager && $cpars->email_status == "SentDGM") <!-- Jika yang login Manager dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email DGM -->

        @if($cpars->email_status == "SentDGM" && $cpars->approved_dgm == "Checked") <!-- DGM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke GM" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke GM</a>

        @elseif(Auth::user()->username == $cpars->dgm && $cpars->email_status == "SentGM") <!-- Jika yang login DGM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email GM --> 

        @if($cpars->email_status == "SentGM" && $cpars->approved_gm == "Checked") <!-- GM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Bagian" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Bagian</a>

          <!-- <a href="{{url('index/qc_report/sendemail/'.$cpars['id'].'/'.$cpars['posisi'])}}" class="btn btn-sm ">Email </a> -->

        @elseif(Auth::user()->username == $cpars->gm && $cpars->email_status == "SentBagian") <!-- Jika yang login GM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim Ke Bagian</label>

        @endif

        <br/><br/>

        @if(Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader || Auth::user()->username == $cpars->chief || Auth::user()->username == $cpars->foreman || Auth::user()->username == $cpars->manager || Auth::user()->username == $cpars->dgm || Auth::user()->username == $cpars->gm || Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "QA" || Auth::user()->role_code == "QA-SPL")

        <table class="table table-hover">
          <form role="form" method="post" action="{{url('index/qc_report/checked/'.$cpars->id)}}">

            <thead>
              <tr>
                <th colspan="6" style="background-color: #ef6c00; color: white; font-size: 20px;border: none"><b>VERIFIKASI CPAR {{ $cpars->cpar_no }} </b></th>
              </tr>
              <tr>
                  <th colspan="2" style="width: 33%;border: none"><b>Point</b></th>
                  <th colspan="2" style="width: 33%;border: none"><b>Content</b></th>
                  <th colspan="2" style="width: 33%;border: none"><b>Checked</b></th>
              </tr>
            </thead>
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                
                <tr>
                  <td colspan="2">Manager yang Dituju</td>
                  <td colspan="2">{{ $cpars->name }}</td>
                  <td colspan="2">
                    <!-- Jika yang masuk adalah bu ratri dan posisi CPAR di chief -->
                      @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Kategori Komplain</td>
                  <td colspan="2">{{ $cpars->kategori }}</td>
                  <td colspan="2">
                    <!-- Jika yang masuk adalah bu ratri dan posisi CPAR di chief -->
                      @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Lokasi</td>
                  <td colspan="2">{{ $cpars->lokasi }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">CPAR Issue Date</td>
                  <td colspan="2"><?php echo date('d F Y', strtotime($cpars->tgl_permintaan)); ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Request Due Date (CPAR Return to QA)</td>
                  <td colspan="2"><?php echo date('d F Y', strtotime($cpars->tgl_balas)); ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Departemen</td>
                  <td colspan="2">{{ $cpars->department_name }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Sumber Komplain</td>
                  <td colspan="2">{{ $cpars->sumber_komplain }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <?php $no = 1; ?>                
                @foreach($parts as $part)

                <tr><td colspan="6"><b>MATERIAL <?= $no ?></b></td></tr>
                <tr>
                  <td colspan="2">Part Item</td>
                  <td colspan="2">{{ $part->part_item }} - {{ $part->material_description }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">No Invoice / No Lot</td>
                  <td colspan="2">{{ $part->no_invoice }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Lot Qty</td>
                  <td colspan="2">{{ $part->lot_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Sample / Check Qty</td>
                  <td colspan="2">{{ $part->sample_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Defect Qty</td>
                  <td colspan="2">{{ $part->defect_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Detail Problem</td>
                  <td colspan="2"><?= $part->detail_problem ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == $cpars->chief) <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->foreman) <!-- {{$cpars->foreman}} --> <!-- Jika yang masuk adalah foreman -->
                        @if ($cpars->posisi == "foreman")
                          @if($cpars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif  

                      @elseif(Auth::user()->username == $cpars->manager) <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->dgm) <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == $cpars->gm || Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader) <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cpars->posisi == "staff" || $cpars->posisi == "leader")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Leader</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <?php $no++; ?>@endforeach
            </tbody>
        </table>
        
        <!-- <hr>Checked all -->
        <br>
        <!-- <label class="col-sm-2">Nomor CPAR<span class="text-red">*</span></label>
        <div class="col-sm-3">
            {{ $cpars->cpar_no }}
        </div>
        <label class="col-sm-1">
            <div class="custom-control custom-checkbox">
                <span class="label success">
                  <input type="checkbox" class="custom-control-input" id="customCheck" name="approve[]" value="">
                  Approve
                </span>
            </div>
        </label> -->
        <?php if ($cpars->file != null){ ?>

        <div class="box box-warning box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">File Terlampir</h3>

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
              <div class="col-md-12">
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                </div>
              </div>
            <?php } ?>                       
          </div>
        </div> 
          
        <?php } ?>
   
        <div class="col-sm-12">
          <!-- <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_report/update',$cpars->id) }}">Cancel</a>
          </div> -->
          

          @if(($cpars->posisi == "chief" && $cpars->checked_chief == null) || ($cpars->posisi == "foreman" && $cpars->checked_foreman == null))
          <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CPAR</a>
          
          @elseif($cpars->posisi == "manager" && $cpars->checked_manager == null)
          <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CPAR</a>

          @elseif($cpars->posisi == "dgm" && $cpars->approved_dgm == null)
          <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CPAR</a>

          @elseif($cpars->posisi == "gm" && $cpars->approved_gm == null)
          <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CPAR</a>          

          @endif
        </div>
        @endif
      </div>
    </form>
  </div>

  <div class="modal modal-danger fade" id="notapproved{{$cpars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/qc_report/unchecked/'.$cpars->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui CPAR ini</h4>
                <textarea class="form-control" required="" name="alasan"></textarea> 
                *CPAR Akan Dikirim kembali lagi ke Leader / Staff masing - masing departemen
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

  <div class="modal fade" id="statusmodal{{$cpars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <td colspan="2">
                      <b>@if($cpars->staff != NULL) 
                            Staff
                         @elseif($cpars->leader != NULL) 
                            Leader
                          @endif
                      </b>
                    </td>
                    <td colspan="2"><b><span class="label label-success">Already Created</span></b></td>
                    @if($cpars->email_status == NULL && $cpars->posisi == "staff")
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>
                          @if($cpars->staff != NULL) 
                              Chief
                          @elseif($cpars->leader != NULL) 
                              Foreman
                          @endif</b></td>
                      <td colspan="2"><b>
                        @if($cpars->checked_chief == "Checked" || $cpars->checked_foreman == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cpars->email_status == "SentManager" || $cpars->email_status == "SentDGM" || $cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") &&  ($cpars->posisi == "manager" || $cpars->posisi == "dgm" || $cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>Manager</b></td>
                      <td colspan="2"><b>
                        @if($cpars->checked_manager == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cpars->email_status == "SentDGM" || $cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") && ($cpars->posisi == "dgm" || $cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>DGM</b></td>
                      <td colspan="2"><b>
                        @if($cpars->approved_dgm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if(($cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") && ($cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>GM</b></td>
                      <td colspan="2"><b>
                        @if($cpars->approved_gm == "Checked")
                        <span class="label label-success">Checked</span>
                        @else
                        <span class="label label-danger">Not Checked</span>
                        @endif</b>
                      </td>
                      @if($cpars->email_status == "SentBagian" && $cpars->posisi == "bagian")
                        <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                      @else
                        <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                      @endif
                  </tr>
                  <tr>
                      <td colspan="2"><b>Bagian</b></td>
                      <td colspan="2"><b>
                        @if($cpars->received_manager == "Received")
                        <span class="label label-success">Received</span>
                        @else
                        <span class="label label-danger">Not Received</span>
                        @endif</b>
                      </td>
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

        <?php endforeach ?>

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