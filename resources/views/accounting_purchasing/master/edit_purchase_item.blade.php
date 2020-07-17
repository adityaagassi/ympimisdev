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
    Edit {{ $page }}
    <small>Submit Item Here</small>
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
      <h2 class="box-title"><b>Edit Purchase Item</b></h2>
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="row">
          <label class="col-sm-2 col-md-offset-1">Kategori Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id="item_category" data-placeholder='Category' style="width: 100%" onchange="pilihKategori(this)">
              <option value="">&nbsp;</option>
              @foreach($item_category as $cat)
              @if($cat->category_id == $item->kategori)
              <option value="{{$cat->category_id}}" selected="">{{$cat->category_id}} - {{$cat->category_name}}</option>
              @else
              <option value="{{$cat->category_id}}">{{$cat->category_id}} - {{$cat->category_name}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Kode Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" class="form-control" id="item_code" placeholder="Kode Item" readonly="" value="{{$item->kode_item}}">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Deskripsi Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_desc" name="item_desc" class="form-control" placeholder="Deskripsi" value="{{$item->deskripsi}}">
          </div>
        </div>
        
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Spesifikasi Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_spec" class="form-control" placeholder="Spesifikasi" value="{{$item->spesifikasi}}">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Currency<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id="item_currency" data-placeholder='Currency' style="width: 100">
              <option value="">&nbsp;</option>
              @if($item->currency == "USD")
              <option value="USD" selected="">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY">JPY</option>
              @elseif($item->currency == "IDR")
              <option value="USD">USD</option>
              <option value="IDR" selected="">IDR</option>
              <option value="JPY">JPY</option>
              @elseif($item->currency == "JPY")
              <option value="USD">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY" selected="">JPY</option>
              @endif
            </select>
          </div>
        </div>
          
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Harga Item<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="number" id="item_price" name="item_price" class="form-control" placeholder="Harga" value="{{$item->harga}}" >
          </div>
        </div>
          
        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Leadtime<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <div class="input-group">
                <input type="text" id="item_leadtime" class="form-control" placeholder="Leadtime" value="{{$item->leadtime}}">
                <span class="input-group-addon">Day (s)</span>
              </div>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">UOM<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <select class="form-control select2" id='item_uom' data-placeholder="Select UOM" style="width: 100%;">
              <option></option>
              @foreach($uom as $um)
              @if($um == $item->uom)
              <option value="{{ $um }}" selected="">{{ $um }}</option>
              @else
              <option value="{{ $um }}">{{ $um }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">Lot<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_lot" name="item_lot" class="form-control" placeholder="Lot" value="{{$item->lot}}">
          </div>
        </div>

        <div class="row" style="margin-top: 10px">
          <label class="col-sm-2 col-md-offset-1">MOQ<span class="text-red">*</span></label>
          <div class="col-sm-6" align="left">
            <input type="text" id="item_moq" name="item_moq" class="form-control" placeholder="MOQ" value="{{$item->moq}}">
          </div>
        </div>

        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/purchase_item') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <input type="hidden" id="id_edit" value="{{$item->id}}">
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

    function pilihKategori(elem)
    {
      var category = $('#item_category').val();
      var kode_item = document.getElementById("item_code");

      $.ajax({
          url: "{{ url('index/purchase_item/get_kode_item') }}?kategori="+category,
          method: 'GET',
          success: function(data) {
              var json = data,
              obj = JSON.parse(json);

              var no = obj.no_urut;
              kode_item.value = category + no;
          }
        });
    }

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

      if ($("#item_code").val() == "") {
        $("#loading").hide();
        alert("Kode Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_category").val() == "") {
        $("#loading").hide();
        alert("Kategori Item Tidak Boleh Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_desc").val() == "") {
        $("#loading").hide();
        alert("Kolom Deskripsi Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_spec").val() == "") {
        $("#loading").hide();
        alert("Kolom Spesifikasi Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_currency").val() == "") {
        $("#loading").hide();
        alert("Kolom Currency Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_price").val() == "") {
        $("#loading").hide();
        alert("Kolom Harga Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_leadtime").val() == "") {
        $("#loading").hide();
        alert("Kolom Leadtime Item Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_uom").val() == "") {
        $("#loading").hide();
        alert("Kolom UOM Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_lot").val() == "") {
        $("#loading").hide();
        alert("Kolom Lot Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#item_moq").val() == "") {
        $("#loading").hide();
        alert("Kolom MOQ Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        id: $("#id_edit").val(),
        item_code: $("#item_code").val(),
        item_category: $("#item_category").val(),
        item_desc: $("#item_desc").val(),
        item_spec: $("#item_spec").val(),
        item_currency: $("#item_currency").val(),
        item_price: $("#item_price").val(),
        item_leadtime: $("#item_leadtime").val(),
        item_uom: $("#item_uom").val(),
        item_lot: $("#item_lot").val(),
        item_moq: $("#item_moq").val()
      };

      $.post('{{ url("index/purchase_item/edit_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Dibuat");
          setTimeout(function(){ window.location = "{{ url('index/purchase_item') }}" }, 2000); 
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

