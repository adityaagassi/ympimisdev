@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Scan Employee
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/font-awesome.css') }}">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/bootstrap.min.css') }}">
<script src="{{ asset('/bower_components/qrcode/js/jquery.min.js') }}"></script>
<script src="{{ asset('/bower_components/qrcode/js/jquery.min.js') }}"></script>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Detail User</h3> --}}
    </div>  
      <div class="box-body">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">Arahkan Kode QR Ke Kamera!</h3>
            </div>
            <div class="panel-body text-center" >
              <canvas></canvas>
              <hr>
              <select></select>
            </div>
            <div class="panel-footer">
                {{-- <center><a class="btn btn-danger" href="{{ url('index/training_report/details/') }}">Kembali</a></center> --}}
            </div>
          </div>
        </div>
    </div>
  </div>
  <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/jquery.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/qrcodelib.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/webcodecamjquery.js') }}"></script>
  <script type="text/javascript">
      var arg = {
          resultFunction: function(result) {
              //$('.hasilscan').append($('<input name="noijazah" value=' + result.code + ' readonly><input type="submit" value="Cek"/>'));
             // $.post("../cek.php", { noijazah: result.code} );
             window.location.href = "../materials/cek/"+result.code;
              // var redirect = '../materials/cek';
              // $.redirectPost(redirect, {materials_code: result.code});
          }
      };
      
      var decoder = $("canvas").WebCodeCamJQuery(arg).data().plugin_WebCodeCamJQuery;
      decoder.buildSelectMenu("select");
      decoder.play();
      /*  Without visible select menu
          decoder.buildSelectMenu(document.createElement('select'), 'environment|back').init(arg).play();
      */
      $('select').on('change', function(){
          decoder.stop().play();
      });

      // jquery extend function
      $.extend(
      {
          redirectPost: function(location, args)
          {
              var form = '';
              $.each( args, function( key, value ) {
                  form += '<input type="hidden" name="'+key+'" value="'+value+'">';
              });
              $('<form action="'+location+'" method="POST">'+form+'</form>').appendTo('body').submit();
          }
      });

  </script>
  @endsection
