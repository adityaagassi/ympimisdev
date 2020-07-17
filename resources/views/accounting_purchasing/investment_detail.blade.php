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
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
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
          <div class="col-xs-4 col-sm-4 col-md-4 col-md-offset-1">
            <label for="form_identitas">Applicant</label>
            <input type="text" id="form_identitas" class="form-control" value="{{$investment->applicant_id}} - {{$investment->applicant_name}} - {{$investment->applicant_department}}" readonly>
            <input type="hidden" id="applicant_id" class="form-control" value="{{$investment->applicant_id}}" readonly>
            <input type="hidden" id="applicant_name" class="form-control" value="{{$investment->applicant_name}}" readonly>
            <input type="hidden" id="applicant_department" class="form-control" value="{{$investment->applicant_department}}" readonly>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3">
            <label for="form_bagian">Submission Date</label>
            <input type="text" id="date" class="form-control" value="{{ date('d F Y', strtotime($investment->submission_date)) }}" readonly>
            <input type="hidden" id="submission_date" class="form-control" value="{{ $investment->submission_date }}">
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3">
            <label for="form_bagian">Reff Number</label>
            <input type="text" class="form-control" id="reff_number" placeholder="Reff Number" value="{{ $investment->reff_number }}" readonly="">
          </div>
        </div>
        <div class="row">

          <div class="col-xs-4 col-xs-offset-1">
            <label for="form_judul">Subject</label>
            <input type="text" id="subject" class="form-control" placeholder="Subject" value="{{ $investment->subject }}">
          </div>
          <div class="col-xs-3">
            <label for="form_kategori">Kind Of Application</label>
            <select class="form-control select2" id="category" data-placeholder='Choose Category' style="width: 100%" onchange="getNomor()">
              <option value="Investment" <?php if($investment->category == "Investment") echo "selected"; ?>>Investment</option>
              <option value="Expense"<?php if($investment->category == "Expense") echo "selected"; ?>>Expense</option>
            </select>
          </div>
          <div class="col-xs-3">
            <label for="form_kategori">Class Of Assets / Kind Of Expense</label>
            <select class="form-control select2" id="type" data-placeholder='Choose Type' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="Building" <?php if($investment->type == "Building") echo "selected"; ?>>Building</option>
              <option value="Machine & Equipment" <?php if($investment->type == "Machine & Equipment") echo "selected"; ?>>Machine & Equipment</option>
              <option value="Vehicle" <?php if($investment->type == "Vehicle") echo "selected"; ?>>Vehicle</option>
              <option value="Tools, Jigs & Furniture" <?php if($investment->type == "Tools, Jigs & Furniture") echo "selected"; ?>>Tools, Jigs & Furniture</option>
              <option value="Moulding" <?php if($investment->type == "Moulding") echo "selected"; ?>>Moulding</option>
              <option value="Pc & Printer" <?php if($investment->type == "Pc & Printer") echo "selected"; ?>>PC & Printer</option>

              <option value="Office Supplies" <?php if($investment->type == "Office Supplies") echo "selected"; ?>>Office Supplies</option>
              <option value="Repair & Maintenance" <?php if($investment->type == "Repair & Maintenance") echo "selected"; ?>>Repair & Maintenance</option>
              <option value="Constool" <?php if($investment->type == "Constool") echo "selected"; ?>>Constool</option>
              <option value="Professional Fee" <?php if($investment->type == "Professional Fee") echo "selected"; ?>>Proffesional Fee</option>
              <option value="Miscellaneous" <?php if($investment->type == "Miscellaneous") echo "selected"; ?>>Miscellaneous</option>
              <option value="Others" <?php if($investment->type == "Others") echo "selected"; ?>>Others</option>
            </select>
          </div>
          <div class="col-xs-4 col-xs-offset-1">
            <label for="form_grup">Main Objective</label>
            <select class="form-control select2" id="objective" data-placeholder='Choose objective' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="Safety & Prevention of Pollution & Disaster" <?php if($investment->objective == "Safety & Prevention of Pollution & Disaster") echo "selected"; ?>>Safety & Prevention of Pollution & Disaster</option>
              <option value="R & D" <?php if($investment->objective == "R & D") echo "selected"; ?>>R & D</option>
              <option value="Production of New Model" <?php if($investment->objective == "Production of New Model") echo "selected"; ?>>Production of new model</option>
              <option value="Rationalization" <?php if($investment->objective == "Rationalization") echo "selected"; ?>>Rationalization</option>
              <option value="Production Increase" <?php if($investment->objective == "Production Increase") echo "selected"; ?>>Production Increase</option>
              <option value="Repair & Modification" <?php if($investment->objective == "Repair & Modification") echo "selected"; ?>>Repair & Modification</option>
            </select>
          </div>
          <div class="col-xs-3">
            <label for="form_judul">Objective Explanation</label>
            <input type="text" id="objective_detail" class="form-control" placeholder="Objective Explanation" value="{{ $investment->objective_detail }}">
          </div>
          <div class="col-xs-3">
            <label for="form">Vendor</label>
            <select class="form-control select2" id="vendor" data-placeholder='Choose Supplier' style="width: 100%"  onchange="getSupplierEdit(this)">
              @foreach($vendor as $ven)
              @if($ven->vendor_code == $investment->supplier_code)
              <option value="{{$ven->vendor_code}}" selected>{{ $ven->supplier_name }}</option>
              @else
              <option value="">&nbsp;</option>
              <option value="{{$ven->vendor_code}}">{{$ven->supplier_name}}</option>
              @endif
              @endforeach
            </select>

            <input type="hidden" class="form-control" id="vendor_name" name="vendor_name" readonly="" value="{{$investment->supplier_name}}">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-4 col-xs-offset-1">
            <label for="form">Payment Term</label>
            <input type="text" id="payment_term" name="payment_term" class="form-control" placeholder="Payment Term" required="" readonly="" value="{{$investment->payment_term}}">
          </div>
          <div class="col-xs-3">
            <label for="form_grup">Date Order</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right datepicker" id="date_order" name="date_order" placeholder="Date of Order" required="" value="{{$investment->date_order}}">
            </div>
          </div>
          <div class="col-xs-3">
            <label for="form_judul">Date Delivery</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right datepicker" id="date_delivery" name="date_delivery" placeholder="Date of Delivery" value="{{$investment->delivery_order}}" required="">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form">Subject (Japanese Version)</label>
            <input type="text" id="subject_jpy" name="subject_jpy" class="form-control" placeholder="Subject (Japan Version)" required="" value="{{$investment->subject_jpy}}">
          </div>

          <div class="col-xs-5">
            <label for="form">Objective (Japanese Version)</label>
            <input type="text" id="objective_detail_jpy" name="objective_detail_jpy" class="form-control" placeholder="Objective (Japan Version)" required="" value="{{$investment->objective_detail_jpy}}">
          </div>
        </div>

        <?php if ($investment->file != null){ ?>

        <br>
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
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
                <?php $data = json_decode($investment->file);
                  for ($i = 0; $i < count($data); $i++) { ?>
                      <div class="col-md-3">
                        <div class="isi">
                          <?= $data[$i] ?>
                        </div>
                      </div>
                      <div  class="col-md-2">
                          <a href="{{ url('/files/investment/'.$data[$i]) }}" class="btn btn-primary" target="_blank">Download / Preview</a>
                      </div> 
                <?php } ?>                       
              </div>
            </div>   
          </div> 
        </div>
        <?php } ?>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
              <label>Note (Optional)</label>
              <textarea class="form-control pull-right" id="note" name="note">{{$investment->note}}</textarea>
          </div>
          <div class="col-xs-5">
              <label>Quotation (Optional)</label>
              <textarea class="form-control pull-right" id="quotation_supplier" name="quotation_supplier">{{$investment->quotation_supplier}}</textarea>
          </div>
        </div>

        <hr style="height:1px;border:none;color:#333;background-color:#eee;" >

        <div class="row">
          <div class="col-xs-10 col-xs-offset-1">
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

        <div class="row">
          <div class="col-xs-5 col-sm-5 col-md-5 col-md-offset-1">
            <label for="form_budget">Budget</label>
            <select class="form-control select2" data-placeholder="Pilih Nomor Budget" name="budget_no" id="budget_no" style="width: 100% height: 35px;" required> 
              <option value="{{$investment->budget_no}}">{{$investment->budget_no}}</option>
            </select>
          </div>
          <div class="col-xs-5 col-sm-5 col-md-5">
            <label for="form_bagian">Currency</label>
             <select class="form-control select2" id="currency" data-placeholder='Currency' style="width: 100%" onchange="currency()">
              <option value="">&nbsp;</option>
              @if($investment->currency == "USD")
              <option value="USD" selected="">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY">JPY</option>
              @elseif($investment->currency == "IDR")
              <option value="USD">USD</option>
              <option value="IDR" selected="">IDR</option>
              <option value="JPY">JPY</option>
              @elseif($investment->currency == "JPY")
              <option value="USD">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY" selected="">JPY</option>
              @else
              <option value="USD">USD</option>
              <option value="IDR">IDR</option>
              <option value="JPY">JPY</option>)
              @endif
            </select>
          </div>
        </div>


        <div class="row">
          <div class="col-sm-12 text-center" style="padding-top: 10px">
            
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('investment') }}"><i class="fa fa-close"></i>&nbsp;Cancel</a>
            </div>

            <div class="btn-group">
              <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Save </button>
            </div>

            <div class="btn-group">
              <a href="{{ url('investment/report/'.$investment->id) }}" target="_blank" class="btn btn-warning" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Lihat Report Investment</a>
            </div>

            @if($investment->posisi == "user" && ($investment->budget_no == null || $investment->currency == null || $investment->subject_jpy == null || $investment->objective_detail_jpy == null))
            <div class="btn-group">
              <button type="button"class="btn btn-success" onclick="sendEmail({{$investment->id}})" data-toggle="tooltip" title="Send Email" disabled=""><i class="fa fa-envelope"></i> Kirim Email Ke Accounting</button>
            </div>

            @elseif($investment->posisi == "user" && ($investment->budget_no != null || $investment->currency != null || $investment->subject_jpy != null || $investment->objective_detail_jpy != null))

            <div class="btn-group">
              <button type="button"class="btn btn-success" onclick="sendEmail({{$investment->id}})" data-toggle="tooltip" title="Lengkapi Data Untuk Send Email"><i class="fa fa-envelope"></i> Kirim Email Ke Accounting</button>
            </div>

            @else
            
              <label class="label label-success"> Email Berhasil Dikirim Ke Accounting</label>
            
            @endif
          </div>
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
            <label class="col-sm-2">Deskripsi Item<span class="text-red">*</span></label>
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
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript">

    budget_list = "";

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

      getBudget();

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

      CKEDITOR.replace('note' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '100px'
      });

      CKEDITOR.replace('quotation_supplier' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '100px'
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
      { "data": "no_item"},
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
        dropdownParent: $('#createModal'),
        dropdownAutoWidth : true,
        allowClear:true,
        minimumInputLength: 3
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

    function getSupplierEdit(elem){

      $.ajax({
        url: "{{ route('admin.pogetsupplier') }}?supplier_code="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#vendor_name').val(obj.name);
          $('#payment_term').val(obj.duration);
        } 
      });
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
      autoclose: true,
      todayHighlight: true,
      format: "yyyy-mm-dd",
      orientation: 'bottom auto',
    });

    function getNomor() {
      var kode = "";
      var jenis = "";

      var cat = document.getElementById("category");
      var category = cat.options[cat.selectedIndex].value;

      var jen = document.getElementById("type");
      var jen2 = jen.options[jen.selectedIndex].value;

      if (category == "Investment") {
        kode = "F";
      }
      else if (category == "Expense"){
        kode = "E";
      }

      if (jen2 == "Building") {
        jenis = "B";
      }
      else if(jen2 == "Machine & Equipment"){
        jenis = "M";
      }
      else if(jen2 == "Vehicle"){
        jenis = "V";
      }
      else if(jen2 == "Tools, Jigs & Furniture"){
        jenis = "T";
      }
      else if(jen2 == "Moulding"){
        jenis = "MD";
      }
      else if(jen2 == "PC & Printer"){
        jenis = "PC";
      }

      if (jen2 == "Office Supplies") {
        jenis = "O";
      }
      else if(jen2 == "Repair & Maintenance"){
        jenis = "R";
      }
      else if(jen2 == "Constool"){
        jenis = "C";
      }
      else if(jen2 == "Professional Fee"){
        jenis = "P";
      }
      else if(jen2 == "Miscellaneous"){
        jenis = "etc";
      }
      else if(jen2 == "Others"){
        jenis = "etc";
      }

      var reff_no = document.getElementById("reff_number");

      reff_no.value = 'N'+kode+'-'+jenis;
        
    }

    function getBudget() {

      data = {
        category:"{{ $investment->category }}",
        department:"{{ $investment->applicant_department }}"
      }

      $.get('{{ url("fetch/investment/invbudgetlist") }}', data, function(result, status, xhr) {
        $.each(result.budget, function(index, value){
          budget_list += "<option value="+value.budget_no+">"+value.budget_no+" - "+value.description+"</option> ";
        });
        $('#budget_no').append(budget_list);
      })
    }


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

      if ($("#date_order").val() == "") {
        $("#loading").hide();
        alert("Kolom Order Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#date_delivery").val() == "") {
        $("#loading").hide();
        alert("Kolom Delivery Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#payment_term").val() == "") {
        $("#loading").hide();
        alert("Kolom Payment Term Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#reff_number").val() == "") {
        $("#loading").hide();
        alert("Kolom Reff Number Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#budget_no").val() == "") {
        $("#loading").hide();
        alert("Kolom Budget Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#currency").val() == "") {
        $("#loading").hide();
        alert("Kolom Currency Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        id: "{{ $investment->id }}",
        applicant_id: $("#applicant_id").val(),
        applicant_name: $("#applicant_name").val(),
        applicant_department: $("#applicant_department").val(),
        submission_date: $("#submission_date").val(),
        reff_number: $("#reff_number").val(),
        category: $("#category").val(),
        subject: $("#subject").val(),
        subject_jpy: $("#subject_jpy").val(),
        type: $("#type").val(),
        objective: $("#objective").val(),
        objective_detail: $("#objective_detail").val(),
        objective_detail_jpy: $("#objective_detail_jpy").val(),
        supplier: $("#vendor").val(),
        supplier_name: $("#vendor_name").val(),
        date_order: $("#date_order").val(),
        date_delivery: $("#date_delivery").val(),
        payment_term: $("#payment_term").val(),
        note: CKEDITOR.instances.note.getData(),
        quotation_supplier: CKEDITOR.instances.quotation_supplier.getData(),
        currency: $("#currency").val(),
        budget_no: $("#budget_no").val(),
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
        $("#kode_item_edit").val(result.datas.no_item).trigger('change.select2');
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

    function selectClass(elem){
        var isi = elem.value;

        list = "";
        list += "<option></option> ";
        if (isi == "Investment") {
          list += "<option value='Building'>Building</option>";
          list += "<option value='Machine & Equipment'>Machine & Equipment</option>";
          list += "<option value='Vehicle'>Vehicle</option>";          
          list += "<option value='Tools, Jigs & Furniture'>Tools, Jigs & Furniture</option>";
          list += "<option value='Moulding'>Moulding</option>";
          list += "<option value='PC & Printer'>PC & Printer</option>";
        }
        else if (isi == "Expense"){
          list += "<option value='Office Supplies'>Office Supplies</option>";
          list += "<option value='Repair & Maintenance'>Repair & Maintenance</option>";
          list += "<option value='Constool'>Constool</option>";
          list += "<option value='Professional Fee'>Proffesional Fee</option>";
          list += "<option value='Miscellaneous'>Miscellaneous</option>";
          list += "<option value='Meal'>Meal</option>";
          list += "<option value='Handling charge'>Handling charge</option>";
          list += "<option value='Technical Assistant'>Technical Assistant</option>";
          list += "<option value='Rent'>Rent</option>";
          list += "<option value='Transport Expense'>Transport Expense</option>";
          list += "<option value='Postage & Telecomunication'>Postage & Telecomunication</option>";
          list += "<option value='Bussiness Trip'>Bussiness Trip</option>";
          list += "<option value='Information System'>Information System</option>";
          list += "<option value='Packaging Cost'>Packaging Cost</option>";
          list += "<option value='Electricity, Water, & Gas'>Electricity, Water, & Gas</option>";
          list += "<option value='Insurance'>Insurance</option>";
          list += "<option value='Meeting&Guest'>Meeting & Guest</option>";
          list += "<option value='Book&periodical'>Book & periodical</option>";
          list += "<option value='Tax&Publicdues'>Tax & Publicdues</option>";
          list += "<option value='Medical'>Medical</option>";
          list += "<option value='Photocopy&printing'>Photocopy & printing</option>";
          list += "<option value='Expatriate permittance'>Expatriate permittance</option>";
          list += "<option value='Wellfare'>Wellfare</option>";
          list += "<option value='Training&Development'>Training & Development</option>";
          list += "<option value='Recruitment'>Recruitment</option>";
          list += "<option value='Others'>Others</option>";
        }

        $('#type').html(list);

    }

    //menjadikan angka ke romawi
    function romanize (num) {
      if (!+num)
        return false;
      var digits = String(+num).split(""),
        key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
               "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
               "","I","II","III","IV","V","VI","VII","VIII","IX"],
        roman = "",
        i = 3;
      while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
      return Array(+digits.join("") + 1).join("M") + roman;
    }

    function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Form Investmen Ke Bagian Accounting")) {
        return false;
      }

      $.get('{{ url("investment/sendemail") }}', data, function(result, status, xhr){

        openSuccessGritter("Success","Email Has Been Sent");
        setTimeout(function(){  window.location.reload() }, 3000);
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
          time: '2000'
        });
      }

  </script>
@stop

