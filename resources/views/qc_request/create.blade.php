@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    font-size: 16px;
  }

  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
  }

  input[type=number] {
    -moz-appearance:textfield; /* Firefox */
  }
  input[type="radio"] {
  }

  #loading { display: none; }

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

  img {
      width: 100%;
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <!-- <small>Create CPAR</small> -->
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
    <form role="form" method="post" action="{{url('index/request_qa/create_action')}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        
        <div class="form-group row" align="left">
          <label class="col-sm-2">Tanggal</label>
          <div class="col-sm-10">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" placeholder="" value="<?= date('d F Y') ?>" disabled>
              <input type="hidden" class="form-control pull-right" id="tgl" name="tgl" placeholder="" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
        </div>

        <div class="form-group row" align="left">
          <label class="col-sm-2">Subject / Judul<span class="text-red">*</span></label>
          <div class="col-sm-10" align="left">
            <div style="height: 80px;vertical-align: middle;">
              <label class="radio" style="margin-top: 5px;margin-left: 5px">Ketidak Sesuaian Material Awal
                <input type="radio" checked="checked" id="subject" name="subject" value="Ketidak Sesuaian Material Awal">
                <span class="checkmark"></span>
              </label>
              <br>&nbsp;
              <label class="radio" style="margin-top: 5px">Ketidak Sesuaian Material Proses
                <input type="radio" id="subject" name="subject" value="Ketidak Sesuaian Material Proses">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
        </div>

        <div  class="form-group row" align="left">
          <label class="col-sm-2">Judul Komplain<span class="text-red">*</span></label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="judul" id="judul" placeholder="Judul / Subject Komplain" required="">
          </div>
        </div>

        <div class="form-group row" align="left">
          <label class="col-sm-2">Section Pelapor<span class="text-red">*</span></label>
          <div class="col-sm-10">
            <select class="form-control select2" style="width: 100%;" id="section_from" name="section_from" data-placeholder="Pilih Section Pelapor" required>
                <option></option>
                @foreach($sec_from as $sf)
                <option value="{{ $sf }}">{{ $sf }}</option>
                @endforeach
            </select>
          </div>
          
        </div>

        <div class="form-group row" align="left">
          <label class="col-sm-2">Section Dituju<span class="text-red">*</span></label>
          <div class="col-sm-10">
            <select class="form-control select2" style="width: 100%;" id="section_to" name="section_to" data-placeholder="Pilih Section Dituju" required>
                <option></option>
                @foreach($sec_to as $st)
                <option value="{{ $st }}">{{ $st }}</option>
                @endforeach
            </select>
          </div>
        </div>

        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/request_qa') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')

  <script type="text/javascript">
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });
    });

</script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    
    $('#tgl').datepicker({
      <?php $tgl_max = date('d-m-Y') ?>
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true,
      endDate: '<?php echo $tgl_max ?>'
    });

  </script>
@stop

