@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .col-xs-1,
  .col-xs-2,
  .col-xs-3,
  .col-xs-4,
  .col-xs-5,
  .col-xs-6,
  .col-xs-7,
  .col-xs-8,
  .col-xs-9,
  .col-xs-10 {
    padding-top: 5px;
  }

  input.currency {
    text-align: left;
    padding-right: 15px;
}

  .radio {
      display: inline-block;
      position: relative;
      padding-left: 35px;
      margin-bottom: 12px;
      cursor: pointer;
      font-size: 16px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    /* Hide the browser's default radio button */
    .radio input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 25px;
      width: 25px;
      background-color: #ccc;
      border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    .radio:hover input ~ .checkmark {
      background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio input:checked ~ .checkmark {
      background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio input:checked ~ .checkmark:after {
      display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio .checkmark:after {
      top: 9px;
      left: 9px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: white;
    }
  
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <small>{{ $title_jp }}</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
    <div class="box-header" style="margin-top: 10px;text-align: center">
      <h2 class="box-title"><b>Data Supplier</b></h2>
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        
        <div class="row">
          <label class="col-sm-2 col-md-offset-1">Vendor Code<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" class="form-control" id="vendor_code" placeholder="Vendor Code">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Supplier Name<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" class="form-control" id="supplier_name" placeholder="Supplier Name">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Address<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" class="form-control" id="supplier_address" placeholder="Address">
          </div>
        </div>
        
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">City<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="supplier_city" name="supplier_city" class="form-control" placeholder="City">
          </div>
        </div>
        
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Phone Number<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="supplier_phone" class="form-control" placeholder="Phone Number">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Fax</label>
          <div class="col-sm-6" align="left">
            <input type="text" id="supplier_fax" class="form-control" placeholder="Faximile">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Contact Person<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="contact_name" class="form-control" placeholder="Contact Person">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">NPWP</label>
          <div class="col-sm-6" align="left">
            <input type="text" id="supplier_npwp" class="form-control" placeholder="NPWP">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Payment Duration</label>
          <div class="col-sm-6" align="left">
            <input type="text" id="supplier_duration" class="form-control" placeholder="Duration">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Position</label>
          <div class="col-sm-6" align="left">
            <input type="text" id="position" class="form-control" placeholder="Position">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Status<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id="supplier_status" data-placeholder='Status' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="PKP">PKP</option>
              <option value="Non PKP">Non PKP</option>
              <option value="Import">Import</option>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/supplier') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-success pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });
    });

</script>
  <script>
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    jQuery(document).ready(function() {
      $("#navbar-collapse").text('');
        $('.select2').select2({
          language : {
            noResults : function(params) {
              // return "There is no cpar with status 'close'";
            }
          }
        });
    });

    $(function () {
      $('.select2').select2()
    });


    $('.datepicker').datepicker({
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      orientation: 'bottom auto',
    });


    $("#form_submit").click( function() {
      $("#loading").show();

      if ($("#supplier_name").val() == "") {
        $("#loading").hide();
        alert("Kode Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#supplier_address").val() == "") {
        $("#loading").hide();
        alert("Kategori Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#supplier_city").val() == "") {
        $("#loading").hide();
        alert("Kolom Deskripsi Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#supplier_phone").val() == "") {
        $("#loading").hide();
        alert("Kolom Spesifikasi Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }


      if ($("#contact_name").val() == "") {
        $("#loading").hide();
        alert("Kolom Leadtime Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#supplier_status").val() == "") {
        $("#loading").hide();
        alert("Kolom UOM Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        vendor_code: $("#vendor_code").val(),
        supplier_name: $("#supplier_name").val(),
        supplier_address: $("#supplier_address").val(),
        supplier_city: $("#supplier_city").val(),
        supplier_phone: $("#supplier_phone").val(),
        supplier_fax: $("#supplier_fax").val(),
        contact_name: $("#contact_name").val(),
        supplier_npwp: $("#supplier_npwp").val(),
        supplier_duration: $("#supplier_duration").val(),
        position: $("#position").val(),
        supplier_status: $("#supplier_status").val()
      };

      $.post('{{ url("index/supplier/create_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Dibuat");
          setTimeout(function(){ window.location = "{{ url('index/supplier') }}" }, 1000); 
        }
        else {
          $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
        
      });

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
          time: '2000'
        });
      }

  </script>
@stop

