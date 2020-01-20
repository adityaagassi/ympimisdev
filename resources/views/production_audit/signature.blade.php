@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
<link rel="stylesheet" href="{{ url("css/app_style.css")}}">
<script src="{{ asset('/ckeditor-full/ckeditor.js') }}"></script>
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

#btnSaveSign {
  color: #fff;
  background: purple;
  padding: 5px;
  border: none;
  border-radius: 5px;
  font-size: 20px;
  margin-top: 10px;
}
#signArea{
  width:304px;
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
    Signature Foreman {{ $page }}
    <small>??</small>
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
    <div class="all-content-wrapper">
    <!-- #END# Top Bar -->
    <section class="container">
      <div class="form-group custom-input-space has-feedback">
        <div class="page-heading">
          <h3 class="post-title">Tanda Tangan</h3>
        </div>
        <div class="page-body clearfix">
          <div class="row">
            <div class="col-md-offset-1 col-md-10">
              <div class="panel panel-default">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <div class="panel-heading">Digital Signature:</div>
                <div class="panel-body center-text">

                <div id="signArea" >
                  <h2 class="tag-ingo">Put signature below,</h2>
                  <div class="sig sigWrapper" style="height:auto;">
                    <div class="typed"></div>
                    <canvas class="sign-pad" id="sign-pad" width="300" height="100"></canvas>
                  </div>
                </div>
                
                
                <button id="btnSaveSign">Save Signature</button>
                <!-- <button type="button" class="button clear" data-action="clear">Clear</button> -->


                </div>
              </div>
            </div>


          </div>
        </div>
      </div>
    </section>
    </div>
   </div>
    @endsection

    @section('scripts')
  
  <!-- Jquery Core Js -->
    <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

    <!-- Bootstrap Select Js -->
    <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>

<!--     <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
  <script src="{{ url("bower_components/jquery-ui/jquery-ui.min.js")}}"></script>
  
  
    <link rel="stylesheet" href="{{ url("css/jquery.signaturepad.css")}}">
  <script src="{{ url("js/numeric-1.2.6.min.js")}}"></script>
  <script src="{{ url("js/bezier.js")}}"></script>
  <script src="{{ url("js/jquery.signaturepad.js")}}"></script>

  <script src="{{ url("js/html2canvas.js")}}"></script>
  <!-- <script src="./js/json2.min.js"></script> -->
  
  <script>


  $(document).ready(function(e){
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(document).ready(function() {
      $('#signArea').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:90});
    });
    
    $("#btnSaveSign").click(function(e){
      html2canvas([document.getElementById('sign-pad')], {
        onrendered: function (canvas) {
          var canvas_img_data = canvas.toDataURL('image/png');
          var img_data = canvas_img_data.replace(/^data:image\/(png|jpg);base64,/, "");
          //ajax call to save image inside folder
          $.ajax({
            url: '{{ url('index/production_audit/save_signature') }}',
            data: { img_data:img_data },
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