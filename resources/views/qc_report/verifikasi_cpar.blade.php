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
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">
        
        <?php foreach ($cpars as $cpars): ?>
        
        <!-- From -->
        @if($cpars->posisi == "chief")
          From : {{ $cpars->emplo }}

        @elseif($cpars->posisi == "manager")
          From : {{ $cpars->emplo }}
        
        @elseif($cpars->posisi == "dgm")
          From : {{ $cpars->emplo }}

        @elseif($cpars->posisi == "gm")
          From : {{ $cpars->emplo }}
        
        @else
          From :
        @endif

        <br> To : {{ $cpars->posisi }}
        
        <a href="{{url('index/qc_report/print_cpar', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" title="Lihat Report"  target="_blank">Preview Report</a>

        <!-- Email Chief -->

        @if($cpars->email_status == "SentChief" && $cpars->checked_chief == "Checked") <!-- Bu Ratri -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif(Auth::user()->username == "pi1910003" && $cpars->email_status == "SentManager") <!-- Jika yang login Bu Ratri dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Manager -->        

        @if($cpars->email_status == "SentManager" && $cpars->checked_manager == "Checked") <!-- Manager -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke DGM" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke DGM</a>

        @elseif(Auth::user()->username == "pi1910003" && $cpars->email_status == "SentDGM") <!-- Jika yang login Manager dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email DGM -->

        @if($cpars->email_status == "SentDGM" && $cpars->approved_dgm == "Checked") <!-- DGM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke GM" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke GM</a>

        @elseif(Auth::user()->username == "pi1910003" && $cpars->email_status == "SentGM") <!-- Jika yang login DGM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email GM --> 

        @if($cpars->email_status == "SentGM" && $cpars->approved_gm == "Checked") <!-- GM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Bagian" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Bagian</a>

          <!-- <a href="{{url('index/qc_report/sendemail/'.$cpars['id'].'/'.$cpars['posisi'])}}" class="btn btn-sm ">Email </a> -->

        @elseif(Auth::user()->username == "pi1910003" && $cpars->email_status == "SentBagian") <!-- Jika yang login DGM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim Ke Bagian</label>

        @endif


        <br/><br/>

        @if(Auth::user()->username == "clark" || Auth::user()->username == "pi1910003") <!-- Jika Username antara chief - gm -->

        <table class="table table-hover">
          <form role="form" method="post" action="{{url('index/qc_report/checked/'.$cpars->id)}}">
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                <tr>
                  <td colspan="6" style="background-color: #ef6c00; color: white"><b>CPAR {{ $cpars->cpar_no }} Verification </b></td>
                </tr>
                <tr>
                    <td colspan="2" style="width: 33%"><b>Point</b></td>
                    <td colspan="2" style="width: 33%"><b>Content</b></td>
                    <td colspan="2" style="width: 33%"><b>Checked</b></td>
                </tr>
                <tr>
                  <td colspan="2">Manager</td>
                  <td colspan="2">{{ $cpars->name }}</td>
                  <td colspan="2">
                    <!-- Jika yang masuk adalah bu ratri dan posisi CPAR di chief -->
                      @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Category</td>
                  <td colspan="2">{{ $cpars->kategori }}</td>
                  <td colspan="2">
                    <!-- Jika yang masuk adalah bu ratri dan posisi CPAR di chief -->
                      @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Location</td>
                  <td colspan="2">{{ $cpars->lokasi }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Request Date</td>
                  <td colspan="2"><?php echo date('d F Y', strtotime($cpars->tgl_permintaan)); ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Request Due Date</td>
                  <td colspan="2"><?php echo date('d F Y', strtotime($cpars->tgl_balas)); ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Department</td>
                  <td colspan="2">{{ $cpars->department_name }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Sumber Komplain</td>
                  <td colspan="2">{{ $cpars->sumber_komplain }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                @foreach($parts as $part)
                <tr><td colspan="6"><b>MATERIAL</b></td></tr>
                <tr>
                  <td colspan="2">Part Item</td>
                  <td colspan="2">{{ $part->part_item }} - {{ $part->material_description }} - {{ $part->hpl }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">No Invoice</td>
                  <td colspan="2">{{ $part->no_invoice }}</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Lot Qty</td>
                  <td colspan="2">{{ $part->lot_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Sample Qty</td>
                  <td colspan="2">{{ $part->sample_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Defect Qty</td>
                  <td colspan="2">{{ $part->defect_qty }} Pcs</td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="2">Detail Problem</td>
                  <td colspan="2"><?= $part->detail_problem ?></td>
                  <td colspan="2">
                    @if(Auth::user()->username == "clarkchief") <!-- {{$cpars->chief}} --> <!-- Jika yang masuk adalah bu ratri -->
                        @if ($cpars->posisi == "chief")
                          @if($cpars->checked_chief == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Manager</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkma") <!-- {{$cpars->manager}} --><!-- Jika yang masuk adalah bu yayuk -->
                        @if ($cpars->posisi == "manager")
                          @if($cpars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke DGM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkdgm") <!-- {{$cpars->dgm}} --><!-- Jika yang masuk adalah pak budhi -->
                        @if ($cpars->posisi == "dgm")
                          @if($cpars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke GM</span>
                        @endif

                      @elseif(Auth::user()->username == "clarkgm") <!-- {{$cpars->gm}} --><!-- Jika yang masuk adalah pak hayakawa -->
                        @if ($cpars->posisi == "gm")
                          @if($cpars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirm Ke Bagian Terkait</span>
                        @endif
                      @endif
                  </td>
                </tr>
                @endforeach
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
        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_report/update',$cpars->id) }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Verified</button>
          </div>
        </div>
        @endif
        <?php endforeach ?>
      </div>
    </form>
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

      if (!confirm("Apakah anda yakin ingin mengirim CPAR ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/sendemail/$cpars->id/$cpars->posisi") }}', data, function(result, status, xhr){
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