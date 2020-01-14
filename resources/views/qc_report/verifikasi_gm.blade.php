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
        
        <?php foreach ($cparss as $cpars): ?>

        <a href="{{url('index/qc_report/print_cpar_new', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-md " title="Lihat Report"  target="_blank" style="color:white;margin-right: 5px">Preview CPAR Report</a>

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

        <table class="table table-hover">
            <thead>
              <tr>
                <th colspan="6" style="background-color: green; color: white; font-size: 20px;border: none"><b>VERIFIKASI CPAR {{ $cpars->cpar_no }} </b></th>
              </tr>
            </thead>
        </table>

        <?php if ($cpars->ttd == null) { ?>
      
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
                          <button id="btnSaveSign" class="btn btn-success">Verify CPAR</button>
                          <a href="{{ url('index/qc_report/verifikasigm', $cpars['id']) }}" class="btn btn-danger">Clear</a>
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
</script>

  @stop