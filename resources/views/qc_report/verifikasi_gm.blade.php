@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">


<style type="text/css">
  /*thead input {
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
  }*/
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }

  #btnSaveSign {
  /*  color: #fff;
    background: #f99a0b;
    padding: 5px;
    border: none;
    border-radius: 5px;
    font-size: 20px;
    margin-top: 10px;*/
  }
  #signArea{
    width: 504px;
    margin: 15px auto;
  }
  .sign-container {
    width: 90%;
    margin: auto;
  }
  .sign-preview {
    width: 150px;
    height: 50px;
    border: solid 1px #CFCFCF;
    margin: 10px 5px;
  }
  .tag-ingo {
    font-family: cursive;
    font-size: 12px;
    text-align: left;
    font-style: oblique;
  }
  .center-text {
    text-align: center;
  }

  @media print {
  .table {-webkit-print-color-adjust: exact;}
  }

    table tr td,
    table tr th{
      font-size: 12pt;
      border: 1px solid black !important;
      border-collapse: collapse;
    }
    .centera{
      text-align: center;
      vertical-align: middle !important;
    }
    .square {
      height: 5px;
      width: 5px;
      border: 1px solid black;
      background-color: transparent;
    }
    table {
      page-break-inside: avoid;
    }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Verifikasi {{ $page }}
    <small>Verification</small>
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

        <table class="table table-bordered">
      <thead>
        <tr>
          <td colspan="2" class="centera" >
            <!-- <img width="120" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important"> -->
            <img width="150px" src="{{ asset('waves.jpg') }}" alt="">
          </td>
          <td colspan="6" style="text-align: center; vertical-align: middle;font-size: 22px;font-weight: bold">CORRECTIVE & PREVENTIVE ACTION REQUEST</td>
          <td colspan="2" style="font-size: 14px;">
            No Dokumen : YMPI/QA/FM/988 <br>
            Revisi : 01<br>
            Tanggal : 08 Oktober 2019<br>
          </td>
        </tr>
      </thead>
      <tbody>
        <?php $i=1;
        $jumlahparts = count($parts);

        if($jumlahparts < 2)
          $jumlah = 0;
        else if($jumlahparts == 2)
          $jumlah = 2;
        else if($jumlahparts == 3)
          $jumlah = 4;
        else if($jumlahparts == 4)
          $jumlah = 6;
        else if($jumlahparts == 5)
          $jumlah = 8;
        else if($jumlahparts == 6)
          $jumlah = 10;
        else if($jumlahparts == 7)
          $jumlah = 12;
        else if($jumlahparts == 8)
          $jumlah = 14;
        ?>

        @foreach($cpars as $cpar)
        <tr>
          <td rowspan="{{ 12 + $jumlah }}" style="width: 50px !important">{{ $i++ }}</td>
          <td colspan="5" style="border: none !important;width: 20px">To : <b>{{$cpar->name}}</b></td>
          <td colspan="4" style="border: none !important; border-right: 1px solid black !important;">CPAR No : <b>{{$cpar->cpar_no}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border: none !important">Location : <b>{{$cpar->lokasi}}</b></td>
          <td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Source Of Complaint : <b>{{$cpar->sumber_komplain}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border: none !important">Issue Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_permintaan)) ?></b></td>
          <td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Department : <b>{{$cpar->department_name}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border: none !important">Request Due Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_balas)) ?></b><br>(CPAR Return to QA)</td>
          <td colspan="4" style="border: none !important; border-right: 1px solid black !important;">
            @if($cpar->destination_code != null)
              Customer : <b>{{$cpar->destination_name}}</b>
            @elseif($cpar->vendor != null)
              Vendor : <b>{{$cpar->vendorname}}</b>
            @endif
          </td>
        </tr>
        <tr>
          <td width="150">Part Item</td>
          <td colspan="2">Part Description</td>
          <td>Invoice / Lot No</td>
          <td>Sample / Check Qty</td>
          <td>Defect Qty</td>
          <td>% Defect</td>
          <td colspan="2"></td>
        </tr>

        <?php 
        $jumlahparts = count($parts);
        if($jumlahparts != 0) { 

        ?>
        @foreach($parts as $part)
        <tr>
          <td rowspan="2">{{$part->part_item}}</td>
          <td rowspan="2" colspan="2">{{$part->material_description}}</td>
          <td rowspan="2">{{$part->no_invoice}}</td>
          <td rowspan="2">{{$part->sample_qty}}</td>
          <td rowspan="2">{{$part->defect_qty}}</td>
          <td rowspan="2">{{$part->defect_presentase}}</td>
          <td rowspan="2" colspan="2"></td>
        </tr>
        <tr></tr>
        @endforeach
        <?php }
        else { 
        ?>
        <tr>
          <td rowspan="2">&nbsp;</td>
          <td rowspan="2" colspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2" colspan="2"></td>
        </tr>
        <tr></tr>
        <?php } ?>
        <?php if($jumlahparts != 0) { ?> 
        <tr>
          <td colspan="9"><p style="font-size: 12px">Detail Problem : </p><?= $part->detail_problem ?></td>
        </tr>
        <?php } else { ?>
        <tr>
          <td colspan="9">&nbsp;</td>
        </tr> 
        <?php } ?>
        <!-- <tr><td colspan="8"></td></tr> -->
        <tr>
          <td>Prepared By</td>
          <td>Checked By</td>
          <td>Checked By</td>
          <td>Approved By</td>
          <td>Approved By</td>
          <td>Received By</td>
          <td colspan="3" rowspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td rowspan="2" style="vertical-align: middle;">
            @if($cpar->staff != null)
              {{$cpar->staffname}}
            @elseif($cpar->leader != null)
              {{$cpar->leadername}}
            @else
              &nbsp;
            @endif
          </td>
          <td rowspan="2" style="vertical-align: middle;">
            @if($cpar->checked_chief == "Checked")
              {{$cpar->chiefname}}
            @elseif($cpar->checked_foreman == "Checked")
              {{$cpar->foremanname}}
            @else
              &nbsp;
            @endif
          </td>
          <td rowspan="2" style="vertical-align: middle;">
            @if($cpar->checked_manager == "Checked")
              {{$cpar->managername}}
            @else
              &nbsp;
            @endif
          </td>
          <td rowspan="2" style="vertical-align: middle;">
            @if($cpar->approved_dgm == "Checked")
              {{$cpar->dgmname}}
            @else
              &nbsp;
            @endif
          </td>
          <td rowspan="2" style="vertical-align: middle;text-align: center;">
            @if($cpar->approved_gm == "Checked")
              <img src="{{url($cparss[0]['ttd'])}}" width="150px">
              <br>{{$cpar->gmname}}
            @else
              &nbsp;
            @endif
          </td>
          <td rowspan="2" style="vertical-align: middle;">
            @if($cpar->received_manager == "Received")
              {{$cpar->name}}
            @else
              &nbsp;
            @endif
          </td>
          <!-- <td colspan="2" rowspan="2" style="vertical-align: middle;">&nbsp;</td> -->
            
          </td>
        </tr>
        <tr></tr>
        <tr>
          @if($cpar->kategori == "Internal")
          <td>Staff / Leader</td>
          <td>Chief / Foreman</td>
          @else
          <td>Staff</td>
          <td>Chief</td>        
          @endif
          <td>Manager</td>
          <td>DGM</td>
          <td>GM</td>
          <td>Manager</td>
          <!-- <td colspan="2"></td> -->
        </tr>
        <tr>
          <td rowspan="2">2</td>
          <td colspan="9">Immediate Action (Filled By QA)</td>
        </tr>
        <tr>
          <td colspan="9"><?= $cpar->tindakan ?></td>
        </tr>
        <tr>
          <td rowspan="2">3</td>
          <td colspan="9">Verification Status</td>
        </tr>
        <tr>
          <td colspan="9">{{$cpar->status_name}}</td>
        </tr>
        <tr>
          <td rowspan="6">4</td>
          <td colspan="9">Cost Estimation</td>
        </tr>
        <tr>
          <td colspan="9"><?= $cpar->cost ?> </td>
        </tr>
        <tr>
          <td>Prepared By</td>
          <td>Checked By</td>
          <td>Known By</td>
          <td colspan="6"></td>
        </tr>
        <tr>
          <td rowspan="2">
            @if($cpar->posisi == "QA" || $cpar->posisi == "QA2" || $cpar->posisi == "QAmanager")
              @if($cpar->staff != null)
                {{$cpar->staffname}}
              @elseif($cpar->leader != null)
                {{$cpar->leadername}}
              @else
                &nbsp;
              @endif
            @endif
          </td>
          <td rowspan="2">
            @if($cpar->posisi == "QA2" || $cpar->posisi == "QAmanager")
              @if($cpar->staff != null)
                {{$cpar->chiefname}}
              @elseif($cpar->leader != null)
                {{$cpar->foremanname}}
              @else
                &nbsp;
              @endif
            @endif
          </td>
          <td rowspan="2">
            @if($cpar->posisi == "QAmanager")
              {{$cpar->managername}}
            @else
              &nbsp;
            @endif
          </td>
          <td colspan="6" rowspan="2"></td>
        </tr>
        <tr></tr>
        <tr>
          <td>Staff</td>
          <td>Chief</td>
          <td>Manager</td>
          <td colspan="6"></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="col-md-12" style="text-align: right;">
      <span style="font-size: 20px">No FM : YMPI/QA/FM/899</span>
    </div>
        
        <?php foreach ($cparss as $cpars): ?>

        <!-- <a href="{{url('index/qc_report/print_cpar_new', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-md " title="Lihat Report"  target="_blank" style="color:white;margin-right: 5px">Preview CPAR Report</a> -->

        @if($cpars->email_status == "SentGM" && $cpars->approved_gm == "Checked") <!-- GM -->
          <a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Send Email Ke Bagian" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Bagian</a>

          <!-- <a href="{{url('index/qc_report/sendemail/'.$cpars['id'].'/'.$cpars['posisi'])}}" class="btn btn-sm ">Email </a> -->

        @elseif(Auth::user()->username == $cpars->gm && $cpars->email_status == "SentBagian") <!-- Jika yang login GM dan status-->
          <label class="label label-success" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim Ke Bagian</label>

        @endif

        <br/><br/>

        @if(Auth::user()->username == $cpars->staff || Auth::user()->username == $cpars->leader || Auth::user()->username == $cpars->gm || Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "QA" || Auth::user()->role_code == "QA-SPL")

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
   
        @endif

        <?php if ($cpars->ttd == null) { ?>

        <table class="table table-hover">
            <thead>
              <tr>
                <th colspan="6" style="background-color: green; color: white; font-size: 20px;border: none"><b>VERIFIKASI CPAR {{ $cpars->cpar_no }} </b></th>
              </tr>
            </thead>
        </table>
      
       <div class="box box-primary" style="padding: 0;border: 0">
          <div class="all-content-wrapper">
            <!-- #END# Top Bar -->
              <div class="form-group custom-input-space has-feedback">
                <div class="page-heading">
                  <h3 class="post-title">Tanda Tangan</h3>
                </div>
                <div class="page-body clearfix">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel panel-default">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" />
                        <div class="panel-heading">Digital Signature : </div>
                        <div class="panel-body center-text"  style="padding: 0">
                          <div id="signArea">
                            <h2 class="tag-ingo">Put signature here,</h2>
                            <div class="sig sigWrapper" style="height:204px;">
                              <div class="typed"></div>
                              <canvas class="sign-pad" id="sign-pad" width="500" height="190"></canvas>
                            </div>
                          </div>
                          
                          <input type="hidden" name="cpar_no" id="cpar_no" value="{{$cpars->cpar_no}}">
                          <input type="hidden" name="id_verif" id="id_verif" value="{{$cpars->id}}">
                          <button class="btn btn-success" id="btnSaveSign" >Verify CPAR</button>
                          <a href="{{ url('index/qc_report/verifikasigm', $cpars['id']) }}" class="btn btn-danger">Clear</a>
                          <!-- <button onclick="clearSign()" class="btn btn-danger">Clear</button> -->
                          <!-- <button class="clearButton" href="#clear">Clear</button> -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>

      <?php } ?>

      <?php endforeach; ?>
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

<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
<script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
<script src="{{ url("bower_components/jquery-ui/jquery-ui.min.js")}}"></script>

<link rel="stylesheet" href="{{ url("css/jquery.signaturepad.css")}}">
<script src="{{ url("js/numeric-1.2.6.min.js")}}"></script>
<script src="{{ url("js/bezier.js")}}"></script>
<script src="{{ url("js/jquery.signaturepad.js")}}"></script>

<script src="{{ url("js/html2canvas.js")}}"></script>
  <!-- <script src="./js/json2.min.js"></script> -->
  
<script>
  $(document).ready(function() {

    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');

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

  <script>
  $(document).ready(function(e){
    $(document).ready(function() {
      $('#signArea').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:190});
    });
    
    $("#btnSaveSign").click(function(e){
      html2canvas([document.getElementById('sign-pad')], {
        onrendered: function (canvas) {
          var canvas_img_data = canvas.toDataURL('image/png');
          var img_data = canvas_img_data.replace(/^data:image\/(png|jpg);base64,/, "");
          var cpar_no = $("#cpar_no").val();
          var id = $("#id_verif").val();
        //ajax call to save image inside folder
        $.ajax({
          url: '{{ url('index/qc_report/save_sign') }}',
          data: { 
            id:id,
            img_data:img_data,
            cpar_no:cpar_no
          },
          type: 'post',
          dataType: 'json',
          success: function (response) {
            window.location.reload();
          }
        });
      }
      });
    });

  });

  function clearSign() {
    $('#signArea').siganturePad(clearCanvas());
  }
</script>

  @stop