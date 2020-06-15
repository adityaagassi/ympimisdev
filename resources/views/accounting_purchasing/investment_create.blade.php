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
    <small>{{$title_jp}}</small>
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
      <h2 class="box-title"><b>Investment-Expense Apllication</b></h2>
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4">
            <label for="form_identitas">Applicant</label>
            <input type="text" id="form_identitas" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}} - {{$employee->department}}" readonly>
            <input type="hidden" id="applicant_id" class="form-control" value="{{$employee->employee_id}}" readonly>
            <input type="hidden" id="applicant_name" class="form-control" value="{{$employee->name}}" readonly>
            <input type="hidden" id="applicant_department" class="form-control" value="{{$employee->department}}" readonly>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
            <label for="form_bagian">Submission Date</label>
            <input type="text" id="date" class="form-control" value="<?= date('d F Y')?>" readonly>
            <input type="hidden" id="submission_date" class="form-control" value="<?= date('Y-m-d')?>" readonly>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
            <label for="form_bagian">Reff Number</label>
            <input type="text" class="form-control" id="reff_number" placeholder="Reff Number">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-3">
            <label for="form_kategori">Kind Of Application</label>
            <select class="form-control select2" id="category" data-placeholder='Choose Category' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="investment">Investment</option>
              <option value="expense">Expense</option>
            </select>
          </div>
          <div class="col-xs-5">
            <label for="form_judul">Subject</label>
            <input type="text" id="subject" class="form-control" placeholder="Subject">
          </div>
          <div class="col-xs-4">
            <label for="form_kategori">Class Of Assets / Kind Of Expense</label>
            <select class="form-control select2" id="type" data-placeholder='Choose Type' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="building">Building</option>
              <option value="machine & equipment">Machine & Equipment</option>
              <option value="vehicle">Vehicle</option>
              <option value="tools, jigs & furniture">Tools, Jigs & Furniture</option>
              <option value="moulding">Moulding</option>
              <option value="pc & printer">PC & Printer</option>

              <option value="office supplies">Office Supplies</option>
              <option value="repair & maintenance">Repair & Maintenance</option>
              <option value="constool">Constool</option>
              <option value="professional fee">Proffesional Fee</option>
              <option value="miscellaneous">Miscellaneous</option>
              <option value="others">Others</option>
            </select>
          </div>
          <div class="col-xs-3">
            <label for="form_grup">Main Objective</label>
            <select class="form-control select2" id="objective" data-placeholder='Choose objective' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="safety">Safety & Prevention of Pollution & Disaster</option>
              <option value="RD">R & D</option>
              <option value="prod">Production of new model</option>
              <option value="rationalization">Rationalization</option>
              <option value="increase">Production Increase</option>
              <option value="repair">Repair & Modification</option>
            </select>
          </div>
          <div class="col-xs-5">
            <label for="form_judul">Objective Explanation</label>
            <input type="text" id="objective_detail" class="form-control" placeholder="Objective Explanation">
          </div>
          <div class="col-xs-4">
            <label for="form">Vendor</label>
            <select class="form-control select2" id="vendor" data-placeholder='Choose Supplier' style="width: 100%">
              <option value="">&nbsp;</option>
              @foreach($vendor as $ven)
              <option>{{$ven->supplier_name}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- <div class="row">
          <div class="col-xs-2">
            <span style="font-weight: bold; font-size: 16px;">PKP Status</span>
            <div style="height: 40px;vertical-align: middle;">
              <label class="radio" style="margin-top: 5px;margin-left: 5px">Yes
                <input type="radio" checked="checked" id="pkp" name="pkp" value="yes">
                <span class="checkmark"></span>
              </label>
              &nbsp;&nbsp;
              <label class="radio" style="margin-top: 5px">No
                <input type="radio" id="pkp" name="pkp" value="no">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
        </div> -->
        
        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('investment') }}">Cancel</a>
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
    $('body').toggleClass("sidebar-collapse");
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

      if ($("#applicant_name").val() == "") {
        $("#loading").hide();
        alert("Kolom Nama Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#applicant_department").val() == "") {
        $("#loading").hide();
        alert("Akun Anda Tidak Memiliki Departemen");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#reff_number").val() == "") {
        $("#loading").hide();
        alert("Kolom Reff Number Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#category").val() == "") {
        $("#loading").hide();
        alert("Kolom Kategori Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#subject").val() == "") {
        $("#loading").hide();
        alert("Kolom Subject Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#type").val() == "") {
        $("#loading").hide();
        alert("Kolom Tipe Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective").val() == "") {
        $("#loading").hide();
        alert("Kolom Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective_detail").val() == "") {
        $("#loading").hide();
        alert("Kolom Detail Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        applicant_id: $("#applicant_id").val(),
        applicant_name: $("#applicant_name").val(),
        applicant_department: $("#applicant_department").val(),
        submission_date: $("#submission_date").val(),
        reff_number: $("#reff_number").val(),
        category: $("#category").val(),
        subject: $("#subject").val(),
        type: $("#type").val(),
        objective: $("#objective").val(),
        objective_detail: $("#objective_detail").val(),
        desc_supplier: $("#vendor").val()
      };

      $.post('{{ url("investment/create_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Dibuat");
          setTimeout(function(){  window.location = "{{url('investment/detail')}}/"+result.id; }, 1000); 
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

