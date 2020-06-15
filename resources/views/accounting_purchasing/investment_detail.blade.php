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
    Detail {{ $page }}
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
      <h2 class="box-title"><b>Investment-Expense Apllication</b></h2>
    </div>  
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
            <input type="text" id="date" class="form-control" value="{{ date('d F Y', strtotime($investment->submission_date)) }}" readonly>
            <input type="hidden" id="submission_date" class="form-control" value="{{ $investment->submission_date }}">
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
            <label for="form_bagian">Reff Number</label>
            <input type="text" class="form-control" id="reff_number" placeholder="Reff Number" value="{{ $investment->reff_number }}">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-3">
            <label for="form_kategori">Kind Of Application</label>
            <select class="form-control select2" id="category" data-placeholder='Choose Category' style="width: 100%">
              <option value="investment" <?php if($investment->category == "investment") echo "selected"; ?>>Investment</option>
              <option value="expense"<?php if($investment->category == "expense") echo "selected"; ?>>Expense</option>
            </select>
          </div>
          <div class="col-xs-5">
            <label for="form_judul">Subject</label>
            <input type="text" id="subject" class="form-control" placeholder="Subject" value="{{ $investment->subject }}">
          </div>
          <div class="col-xs-4">
            <label for="form_kategori">Class Of Assets / Kind Of Expense</label>
            <select class="form-control select2" id="type" data-placeholder='Choose Type' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="building" <?php if($investment->type == "building") echo "selected"; ?>>Building</option>
              <option value="machine & equipment" <?php if($investment->type == "machine & equipment") echo "selected"; ?>>Machine & Equipment</option>
              <option value="vehicle" <?php if($investment->type == "vehicle") echo "selected"; ?>>Vehicle</option>
              <option value="tools, jigs & furniture" <?php if($investment->type == "tools, jigs & furniture") echo "selected"; ?>>Tools, Jigs & Furniture</option>
              <option value="moulding" <?php if($investment->type == "moulding") echo "selected"; ?>>Moulding</option>
              <option value="pc & printer" <?php if($investment->type == "pc & printer") echo "selected"; ?>>PC & Printer</option>

              <option value="office supplies" <?php if($investment->type == "office supplies") echo "selected"; ?>>Office Supplies</option>
              <option value="repair & maintenance" <?php if($investment->type == "repair & maintenance") echo "selected"; ?>>Repair & Maintenance</option>
              <option value="constool" <?php if($investment->type == "constool") echo "selected"; ?>>Constool</option>
              <option value="professional fee" <?php if($investment->type == "professional fee") echo "selected"; ?>>Proffesional Fee</option>
              <option value="miscellaneous" <?php if($investment->type == "miscellaneous") echo "selected"; ?>>Miscellaneous</option>
              <option value="others" <?php if($investment->type == "others") echo "selected"; ?>>Others</option>
            </select>
          </div>
          <div class="col-xs-3">
            <label for="form_grup">Main Objective</label>
            <select class="form-control select2" id="objective" data-placeholder='Choose objective' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="safety" <?php if($investment->objective == "safety") echo "selected"; ?>>Safety & Prevention of Pollution & Disaster</option>
              <option value="RD" <?php if($investment->objective == "RD") echo "selected"; ?>>R & D</option>
              <option value="prod" <?php if($investment->objective == "prod") echo "selected"; ?>>Production of new model</option>
              <option value="rationalization" <?php if($investment->objective == "rationalization") echo "selected"; ?>>Rationalization</option>
              <option value="increase" <?php if($investment->objective == "increase") echo "selected"; ?>>Production Increase</option>
              <option value="repair" <?php if($investment->objective == "repair") echo "selected"; ?>>Repair & Modification</option>
            </select>
          </div>
          <div class="col-xs-5">
            <label for="form_judul">Objective Explanation</label>
            <input type="text" id="objective_detail" class="form-control" placeholder="Objective Explanation" value="{{ $investment->objective_detail }}">
          </div>
          <div class="col-xs-4">
            <label for="form">Vendor</label>
            <select class="form-control select2" id="vendor" data-placeholder='Choose Supplier' style="width: 100%">
              @foreach($vendor as $ven)
              @if($ven->supplier_name == $investment->desc_supplier)
              <option selected>{{ $ven->supplier_name }}</option>
              @else
              <option value="">&nbsp;</option>
              <option>{{$ven->supplier_name}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>


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

        <div class="row" style="margin-top: 10px">
          <div class="col-xs-12">
          <a data-toggle="modal" data-target="#createModal" class="btn btn-primary col-sm-3" style="color:white;font-weight: bold; font-size: 20px;margin-bottom: 20px">Tambahkan Item</a>
          <table id="item" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>Reff No</th>
                  <th>No Item</th>
                  <th>Detail</th>    
                  <th>Qty</th>
                  <th>Price</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th> 
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>

            </div>
        </div>
        
      </div>
  </div>

  <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1100px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><center>Input Item<b></b></center></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Reff Number<span class="text-red">*</span></label>
              <div class="col-sm-8">
                {{$investment->reff_number}}
                <input type="hidden" value="{{ $investment->reff_number }}" id="reff_number">
             </div>
           </div>
           <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Nomor Item<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select3" id="kode_item" name="kode_item" style="width: 100%;" data-placeholder="Pilih Nomor Item" required>
                <option value=""></option>
                 @foreach($items as $item)
                <option value="{{ $item->kode_item }}">{{ $item->kode_item }} - {{ $item->deskripsi }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="left" id="desc">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Detail Item<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="detail_item" placeholder="Detail Item" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Qty</span></label>
            <div class="col-sm-8">
              <div class="input-group">
                <input type="number" class="form-control" id="jumlah_item" placeholder="Jumlah Item" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Price</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <input type="number" class="form-control" id="price_item" placeholder="Harga" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Amount</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" id="amount_item" placeholder="Total" disabled required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
      </div>
    </div>
  </div>
</div>

  <div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1100px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><center>Update Item<b></b></center></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Reff Number<span class="text-red">*</span></label>
              <div class="col-sm-8">
                {{$investment->reff_number}}
             </div>
           </div>
           <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Nomor Item<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select4" id="kode_item_edit" name="kode_item_edit" style="width: 100%;" data-placeholder="Pilih Nomor Item" required>
                <option value=""></option>
                 @foreach($items as $item)
                  <option value="{{ $item->kode_item }}">{{ $item->kode_item }} - {{ $item->deskripsi }}</option>
                  @endforeach
              </select>
            </div>
            </div>
            <div class="form-group row" align="left" id="desc">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Detail Item<span class="text-red">*</span></label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="detail_item_edit" placeholder="Detail Item" required>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Qty</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="number" class="form-control" id="jumlah_item_edit" placeholder="Jumlah Item" onkeyup="getPersenEdit()" required>
                  <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Price</span></label>
              <div class="col-sm-8" align="left">
                <div class="input-group">
                  <input type="number" class="form-control" id="price_item_edit" placeholder="Harga" onkeyup="getPersenEdit()" required>
                  <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Amount</label>
              <div class="col-sm-8" align="left">
                <input type="text" class="form-control" id="amount_item_edit" placeholder="Total" disabled required>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_edit">
          <button type="button" onclick="edit()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-pencil"></i> Update</button>
        </div>
      </div>
    </div>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script type="text/javascript">

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

      $('body').toggleClass("sidebar-collapse");
      $("#navbar-collapse").text('');
        $('.select2').select2({
          language : {
            noResults : function(params) {
              // return "There is no cpar with status 'close'";
            }
          }
        });

        $("#kode_item").change(function(){
              $.ajax({
                  url: "{{ route('admin.getitemdesc') }}?kode_item=" + $(this).val(),
                  method: 'GET',
                  success: function(data) {
                    var json = data,
                    obj = JSON.parse(json);
                    $('#detail_item').val(obj.detail);
                  }
              });
          });

        $("#kode_item_edit").change(function(){
              $.ajax({
                  url: "{{ route('admin.getitemdesc') }}?kode_item=" + $(this).val(),
                  method: 'GET',
                  success: function(data) {
                    var json = data,
                    obj = JSON.parse(json);
                    $('#detail_item_edit').val(obj.detail);
                  }
              });
          });

    $('#item tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    });

    var table = $('#item').DataTable({
      "order": [],
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "get",
        "url" : "{{ url("investment/fetch_investment_item",$investment->id) }}"
      },
      "columns": [
      { "data": "reff_number" },
      { "data": "kode_item"},
      { "data": "detail" },
      { "data": "qty" },
      { "data": "price" },
      { "data": "amount" },
      { "data": "action", "width": "10%" }
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      }
    });

    table.columns().every( function () {
      var that = this;

      $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
          that
          .search( this.value )
          .draw();
        }
        });
      });
      $('#item tfoot tr').appendTo('#item thead');

    });

</script>
  <script>

    $(function () {
      $('.select2').select2()
    });

    $(function () {
      $('.select3').select2({
        dropdownParent: $('#createModal')
      });
      $('.select4').select2({
        dropdownParent: $('#EditModal')
      });
    })

    function getPersen() {
      var qty = document.getElementById("jumlah_item").value;
      var prc = document.getElementById("price_item").value;
      var hasil = parseInt(qty) * parseInt(prc);
      if (!isNaN(hasil)) {
         document.getElementById('amount_item').value = hasil;
      }
    }

    function getPersenEdit() {
      var qty = document.getElementById("jumlah_item_edit").value;
      var prc = document.getElementById("price_item_edit").value;
      var hasil = parseInt(qty) * parseInt(prc);
      if (!isNaN(hasil)) {
         document.getElementById('amount_item_edit').value = hasil;
      }
    }

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
        id: "{{ $investment->id }}",
        investment_no: $("#investment_no").val(),
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
        desc_supplier : $("#vendor").val()
      };

      $.post('{{ url("investment/update_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Data Berhasil Diubah");
          location.reload(); 
        }
        else {
          $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
        
      });

    });

    function create() {

      var data = {
        reff_number: $("#reff_number").val(),
        kode_item: $("#kode_item").val(),
        detail_item: $("#detail_item").val(),
        jumlah_item : $("#jumlah_item").val(),
        price_item : $("#price_item").val(),
        amount_item : $("#amount_item").val()
      };

      // console.log(data);

      $.post('{{ url("investment/create_investment_item") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#item').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","New item has been created.");
        } else {
          openErrorGritter("Error","Item not created.");
        }
      })
    }

     function modalEdit(id) {
      $('#EditModal').modal("show");
      var data = {
        id:id
      };
      
      $.get('{{ url("investment/edit_investment_item") }}', data, function(result, status, xhr){
        $("#id_edit").val(id);
        $("#kode_item_edit").val(result.datas.kode_item).trigger('change.select2');
        $("#detail_item_edit").val(result.datas.detail);
        $("#jumlah_item_edit").val(result.datas.qty);
        $("#price_item_edit").val(result.datas.price);
        $("#amount_item_edit").val(result.datas.amount);
        $.ajax({
            url: "{{ route('admin.getitemdesc') }}?kode_item=" + $(this).val(),
            method: 'GET',
            success: function(data) {
              var json = data,
              obj = JSON.parse(json);
              $('#detail_item_edit').val(obj.detail);
            }
        });
      });
    }

    function edit() {

      var data = {
        id: $("#id_edit").val(),
        kode_item: $("#kode_item_edit").val(),
        detail_item: $("#detail_item_edit").val(),
        jumlah_item: $("#jumlah_item_edit").val(),
        price_item: $("#price_item_edit").val(),
        amount_item: $("#amount_item_edit").val(),
      };

      $.post('{{ url("investment/edit_investment_item") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#item').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Item has been edited.");
        } else {
          openErrorGritter("Error",result.datas);
        }
      })
    }


   

    function modalDelete(id) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yakin ingin menghapus material ini?")) {
        return false;
      }

      $.post('{{ url("investment/delete_investment_item") }}', data, function(result, status, xhr){
        $('#item').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","BErhasil Hapus Item");
      })
    }

    $.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };


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

