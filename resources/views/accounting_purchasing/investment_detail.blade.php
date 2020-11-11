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
  input.currency {
    text-align: left;
    padding-right: 15px;
  }
</style>
@endsection
@section('header')
<section class="content-header" style="margin-left: 10%">
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
  <div class="alert alert-success alert-dismissible" style="margin-left: 10%;width: 80%;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible" style="margin-left: 10%;width: 80%;">
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
  <div class="box box-primary" style="margin-left: 10%;width: 80%">
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
            <!-- <input type="text" id="category" class="form-control" placeholder="Category" value="{{ $investment->category }}"> -->

            <select class="form-control select2" id="category" data-placeholder='Choose Category' style="width: 100%" onchange="getNomor()">
              <option value="Investment" <?php if($investment->category == "Investment") echo "selected"; ?>>Investment</option>
              <option value="Expense"<?php if($investment->category == "Expense") echo "selected"; ?>>Expense</option>
            </select>
          </div>
          <div class="col-xs-3">
            <label for="form_kategori">Class Of Assets / Kind Of Expense</label>
            <!-- <input type="text" id="type" class="form-control" placeholder="Type" value="{{ $investment->type }}"> -->

            <select class="form-control select2" id="type" data-placeholder='Choose Type' style="width: 100%">
              <option value="">&nbsp;</option>
              <option value="Building" <?php if($investment->type == "Building") echo "selected"; ?>>Building</option>
              <option value="Machine and Equipment" <?php if($investment->type == "Machine and Equipment") echo "selected"; ?>>Machine and Equipment</option>
              <option value="Vehicle" <?php if($investment->type == "Vehicle") echo "selected"; ?>>Vehicle</option>
              <option value="Tools, Jigs and Furniture" <?php if($investment->type == "Tools, Jigs and Furniture") echo "selected"; ?>>Tools, Jigs and Furniture</option>
              <option value="Moulding" <?php if($investment->type == "Moulding") echo "selected"; ?>>Moulding</option>
              <option value="PC and Printer" <?php if($investment->type == "PC and Printer") echo "selected"; ?>>PC and Printer</option>

              <option value="Office Supplies" <?php if($investment->type == "Office Supplies") echo "selected"; ?>>Office Supplies</option>
              <option value="Repair and Maintenance" <?php if($investment->type == "Repair and Maintenance") echo "selected"; ?>>Repair and Maintenance</option>
              <option value="Constool" <?php if($investment->type == "Constool") echo "selected"; ?>>Constool</option>
              <option value="Professional Fee" <?php if($investment->type == "Professional Fee") echo "selected"; ?>>Proffesional Fee</option>
              <option value="Miscellaneous" <?php if($investment->type == "Miscellaneous") echo "selected"; ?>>Miscellaneous</option>
              <option value="Information System" <?php if($investment->type == "Information System") echo "selected"; ?>>Information System</option>
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
            <label for="form_bagian">Currency</label>
            <input type="text" id="currency" name="currency" class="form-control" placeholder="Currency" readonly="" value="{{$investment->currency}}">
            <input type="hidden" id="payment_term" name="payment_term" class="form-control" placeholder="Payment Term" required="" readonly="" value="{{$investment->payment_term}}">
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
              <label>Other Quotation (Optional)</label>
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
                  <th>No Item</th>
                  <th>Detail</th>    
                  <th>Qty</th>
                  <th>Uom</th>
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

          <div class="col-xs-3 col-xs-offset-8" style="padding:0">
            <h4>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Total : <span class="ket_harga_item"></span> <span id="total_amount_beli"></span></u></b>
            </h4>
          </div>
        </div>
        <hr style="height:1px;border:none;color:#333;background-color:#eee;" >

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form">Subject (Japanese Version)</label>
            <input type="text" id="subject_jpy" name="subject_jpy" class="form-control" placeholder="Subject (Japan Version)" required="" value="{{$investment->subject_jpy}}">
          </div>

          <div class="col-xs-5">
            <label for="form">Objective Explanation (Japanese Version)</label>
            <input type="text" id="objective_detail_jpy" name="objective_detail_jpy" class="form-control" placeholder="Objective Explanation (Japan Version)" required="" value="{{$investment->objective_detail_jpy}}">
          </div>
        </div>

        @if(count($investment_budget) == 0)
        <div class="row">
          <div class="col-xs-2 col-sm-2 col-md-2 col-xs-offset-1">
            <label for="form_budget_category">Budget Category</label>
            <select class="form-control select2" data-placeholder="Pilih Category Budget" name="budget_category1" id="budget_category1" onchange="selectbudget(this)" style="width: 100% height: 35px;" required> 
              <option value="">&nbsp;</option>
              <option value="On Budget">On Budget</option>
              <option value="Shifting">Shifting</option>
              <option value="Out Of Budget">Out Of Budget</option>
            </select>
          </div>

          <div class="col-xs-3 col-sm-3 col-md-3" id="budget_dana1">
            <label for="form_budget">Budget</label>
            <select class="form-control select2" data-placeholder="Pilih Nomor Budget" name="budget_no1" id="budget_no1" style="width: 100% height: 35px;" onchange="getBudgetName(this)" required> 
            </select>
            <input type="hidden" name="budget_name1" id="budget_name1">
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2" id="budget_sisa1">
            <label for="form_budget">Beginning Balance</label>
            <div class="input-group"> 
              <span class="input-group-addon" id="ket_sisa1" style="padding:6px">$</span>
              <input type="text" class="form-control currency" id="sisa_budget1" name="sisa_budget1" readonly="" placeholder="Beg Balance" style="padding: 6px 6px">
            </div>
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3" id="budget_total1">
            <label for="form_budget">Amount</label>
            <div class="input-group"> 
              <span class="input-group-addon" id="ket_harga1" style="padding:6px">$</span>
              <input type="text" class="form-control" id="amount_budget1" name="amount_budget1" placeholder="End Balance" readonly="">
            </div>
          </div>
          <!-- <div class="col-xs-2 col-sm-2 col-md-2" id="budget_konversi1">
            <label for="form_budget">Konversi</label>
            <div class="input-group"> 
              <span class="input-group-addon" id="dollar" style="padding:6px">$</span>
              <input type="text" class="form-control" id="konversi_dollar1" name="konversi_dollar1" placeholder="Konversi Dollar" readonly="">
            </div>
          </div> -->
          <!-- <div class="col-xs-1 col-sm-1 col-md-1">
            <label for="form_budget">Aksi</label>
            <br>
            
            <button type="button" class="btn btn-success" onclick='tambah("tambah","lop");' style="padding: 6px 8px"><i class='fa fa-plus'></i></button>
          </div> -->
          <input type="text" name="lop" id="lop" value="0" hidden>

        </div>


        <div id="tambah"></div>
        @else
          <?php $nomor = 1; ?>
          @foreach($investment_budget as $inv_budget)
            <div id="<?= $inv_budget->id ?>" class="row">

              <input type="hidden" class="form-control" id="id_budget" name="id_budget" value="{{$inv_budget->id}}">

              <div class="col-xs-2 col-sm-2 col-md-2 col-xs-offset-1">
                @if($nomor == 1)
                <label for="form_budget_category">Budget Category</label>
                @endif
                <input type="text" class="form-control"  name="budget_category<?= $nomor ?>" id="budget_category<?= $nomor ?>" value="{{$inv_budget->category_budget}}" readonly="">
                <!-- <select class="form-control select2" data-placeholder="Pilih Category Budget" name="budget_category<?= $nomor ?>" id="budget_category<?= $nomor ?>" onchange="selectbudget(this)" style="width: 100% height: 35px;" required> 
                  <option value="">&nbsp;</option>
                  @if($inv_budget->category_budget == "On Budget")
                  <option value="On Budget" selected="">On Budget</option>
                  <option value="Shifting">Shifting</option>
                  <option value="Out Of Budget">Out Of Budget</option>
                  @elseif($inv_budget->category_budget == "Shifting")
                  <option value="Shifting" selected="">Shifting</option>
                  <option value="Out Of Budget">Out Of Budget</option>
                  @elseif($inv_budget->category_budget == "Out Of Budget")
                  <option value="On Budget">On Budget</option>
                  <option value="Shifting">Shifting</option>
                  <option value="Out Of Budget" selected="">Out Of Budget</option>
                  @else
                  <option value="On Budget">On Budget</option>
                  <option value="Shifting">Shifting</option>
                  <option value="Out Of Budget">Out Of Budget</option>
                  @endif
                </select> -->
              </div>
              @if($inv_budget->category_budget != "Out Of Budget")
              <div class="col-xs-3 col-sm-3 col-md-3" id="budget_dana<?= $nomor ?>">
                @if($nomor == 1)
                <label for="form_budget">Budget</label>
                @endif
                <!-- <select class="form-control select2" data-placeholder="Pilih Nomor Budget" name="budget_no<?= $nomor ?>" id="budget_no<?= $nomor ?>" style="width: 100% height: 35px;"  onchange="getBudgetName(this)" required> 
                  <option value="{{$inv_budget->budget_no}}" selected="">{{$inv_budget->budget_no}} - {{$inv_budget->budget_name}}</option>
                </select> -->
                <input type="text" class="form-control"  name="budget_no_name<?= $nomor ?>" id="budget_no_name<?= $nomor ?>" value="{{$inv_budget->budget_no}} - {{$inv_budget->budget_name}}" readonly="">
                <input type="hidden" class="form-control"  name="budget_no<?= $nomor ?>" id="budget_no<?= $nomor ?>" value="{{$inv_budget->budget_no}}" readonly="">
                <input type="hidden" name="budget_name<?= $nomor ?>" id="budget_name<?= $nomor ?>" value="{{$inv_budget->budget_name}}">
              </div>
              <div class="col-xs-2 col-sm-2 col-md-2" id="budget_sisa<?= $nomor ?>">
                @if($nomor == 1)
                <label for="form_budget">Beginning Balance</label>
                @endif
                <div class="input-group"> 
                  <span class="input-group-addon" id="ket_sisa<?= $nomor ?>" style="padding:6px">$</span>
                  <input type="text" class="form-control currency" id="sisa_budget<?= $nomor ?>" name="sisa_budget<?= $nomor ?>" placeholder="Beginning Balance" value="{{$inv_budget->sisa}}" readonly="" style="padding: 6px 6px">
                </div>
              </div>
              @endif
              <div class="col-xs-3 col-sm-3 col-md-3" id="budget_total<?= $nomor ?>">
                @if($nomor == 1)
                <label for="form_budget">Amount</label>
                @endif
                <div class="input-group"> 
                  <span class="input-group-addon" id="ket_harga<?= $nomor ?>" style="padding:6px">$</span>
                  <input type="text" class="form-control" id="amount_budget<?= $nomor ?>" name="amount_budget<?= $nomor ?>" placeholder="Amount" value="{{$inv_budget->total}}" readonly="">
                </div>
              </div>
              <!-- <div class="col-xs-2 col-sm-2 col-md-2" id="budget_konversi1">
                @if($nomor == 1)
                <label for="form_budget">Konversi</label>
                @endif
                <div class="input-group"> 
                  <span class="input-group-addon" id="dollar" style="padding:6px">$</span>
                  <input type="text" class="form-control" id="konversi_dollar<?= $nomor ?>" name="konversi_dollar<?= $nomor ?>" placeholder="Konversi Dollar" readonly="">
                </div>
              </div> -->
              <div class="col-xs-1 col-sm-1 col-md-1">
                @if($nomor == 1)
                <label for="form_budget">Aksi</label><br>
                @endif
                <a href="javascript:void(0);" id="b" onclick='deleteConfirmation("<?= $inv_budget->category_budget ?>","<?= $inv_budget->id ?>");' class="btn btn-danger" data-toggle="modal" data-target='#modaldanger' style="padding: 6px"><i class='fa fa-trash'></i> </a> 
              </div>



            </div>
            <?php $nomor++; ?>
          @endforeach

          <input type="hidden" name="lop" id="lop" value="<?= $nomor-1 ?>">
          <div id="tambah"></div>
        @endif

        <!-- <div class="row">
          <div class="col-sm-10 col-sm-offset-1" style="padding-top: 20px">
            
            
            <h4>
                Total Budget &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : <span class="ket_harga_item"></span> <span id="total_amount_bayar"></span>
            </h4>
            
          </div>
        </div> -->

        <div class="row">
          <div class="col-sm-11" style="padding-top: 30px">
            @if($investment->posisi == "user" && ($investment->currency == null || $investment->subject_jpy == null || $investment->objective_detail_jpy == null || count($investment_budget) == 0 || count($investment_item) == 0))
            <div class="btn-group pull-right">
              <button type="button"class="btn btn-success" onclick="sendEmail({{$investment->id}})" data-toggle="tooltip" title="Send Email" disabled=""><i class="fa fa-envelope"></i> Kirim Email Ke Accounting</button>
            </div>

            @elseif($investment->posisi == "user" && ($investment->currency != null || $investment->subject_jpy != null || $investment->objective_detail_jpy != null || count($investment_budget) > 0  || count($investment_item) > 0))

            <div class="btn-group pull-right">
              <button type="button"class="btn btn-success" onclick="sendEmail({{$investment->id}})" data-toggle="tooltip" title="Lengkapi Data Untuk Send Email"><i class="fa fa-envelope"></i> Kirim Email Ke Accounting </button>
            </div>

            @else
            <div class="btn-group pull-right">
              <label class="label label-success pull-right"> Email Berhasil Dikirim Ke Accounting</label>
            </div>
            @endif

            <div class="btn-group pull-right">
              <a href="{{ url('investment/report/'.$investment->id) }}" target="_blank" class="btn btn-warning" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Check Report Investment</a>
            </div>

            <div class="btn-group pull-right">
              <button type="button" class="btn btn-primary pull-right" id="form_submit" style="margin-right:5px;"><i class="fa fa-edit"></i>&nbsp; Save </button>
            </div>

            <div class="btn-group pull-right">
              <a class="btn btn-danger" href="{{ url('investment') }}" style="margin-right:5px;"><i class="fa fa-arrow-circle-left"></i>&nbsp;Kembali</a>
            </div>

            
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
            <label class="col-sm-2">Deskripsi Item</label>
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
                <input type="number" class="form-control" id="jumlah_item" placeholder="Jumlah Item" onkeyup="getPersen()" required>
            </div>
          </div>
          <div class="form-group row" align="left" id="uom_data">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">UOM</span></label>
            <div class="col-sm-8">
              <select class="form-control select5" id="uom" name="uom" data-placeholder="UOM" style="width: 100%;">
                  <option></option>
                  @foreach($uom as $um)
                  <option value="{{ $um }}">{{ $um }}</option>
                  @endforeach
                </select>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Price</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <span class="input-group-addon ket_harga_item"></span>
                <!-- <input type="text" id="price_item" name="price_item" class="form-control currency" placeholder="Harga" data-number-to-fixed="2" data-number-stepfactor="100" onkeyup="getPersen()"> -->
                <input type="text" id="price_item" name="price_item" class="form-control" placeholder="Harga" onkeyup="getPersen()">
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Amount</label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <span class="input-group-addon ket_harga_item"></span>
                <!-- <input type="text" id="amount_item" name="amount_item" class="form-control currency" placeholder="Total" data-number-to-fixed="2" data-number-stepfactor="100"  disabled required> -->

                <input type="text" id="amount_item" name="amount_item" class="form-control" placeholder="Total" disabled required>
                <input type="hidden" id="dollar_item" name="dollar_item" class="form-control currency" placeholder="Dollar">
              </div>
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
                  <option></option>
                  @foreach($uom as $um)
                  <option value="{{ $um }}">{{ $um }}</option>
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
              <label class="col-sm-2">UOM</span></label>
              <div class="col-sm-8">
                <select class="form-control select4" id="uom_edit" name="uom_edit" style="width: 100%;" data-placeholder="Pilih UOM" required>
                <option value=""></option>
                  @foreach($uom as $um)
                  <option value="{{ $um }}">{{ $um }}</option>
                  @endforeach
              </select>
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
                <input type="hidden" id="dollar_item_edit" name="dollar_item_edit" class="form-control currency" placeholder="Dollar">
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


  <div class="modal modal-danger fade in" id="modaldanger">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <h4 class="modal-title">Hapus Budget</h4>
        </div>
        <div class="modal-body" id="modalDeleteBody">
          <p>Are You Sure Want to Delete?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
          <a id="a" name="modalDeleteButton" href="" type="button" onclick="delete_budget(this.id)" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <!-- <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script> -->
  <script type="text/javascript">

    var no = 2;
    exchange_rate = [];

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });


      var harga = document.getElementById("price_item");

      // harga.addEventListener("keyup", function(e) {
      //   harga.value = formatRupiah(this.value, "");
      // });

      getExchangeRate();

      $('body').toggleClass("sidebar-collapse");
      $("#navbar-collapse").text('');

      $(function () {
        $('.select2').select2({
            dropdownAutoWidth : true,
            allowClear:true,
          });

        $('.select5').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#uom_data"),
          allowClear:true,
        });

      });

      if ($('#currency').val() == "USD") {
        $('.ket_harga_item').text("$");
      }else if ($('#currency').val() == "JPY") {
        $('.ket_harga_item').text("¥");
      }else if ($('#currency').val() == "IDR"){
        $('.ket_harga_item').text("Rp.");
      }

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

      if($("#budget_category1").val() == "Out Of Budget"){
        $("#budget_dana1").hide();
        $("#budget_sisa1").hide();
        $("#budget_total1").hide();
      }

      $("#kode_item").change(function(){
            $.ajax({
                url: "{{ route('admin.getitemdesc') }}?kode_item=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                  var json = data,
                  obj = JSON.parse(json);
                  $('#detail_item').val(obj.detail);
                  $('#price_item').val(obj.harga);
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
                  $('#price_item_edit').val(obj.harga);
                }
            });
        });

      // CKEDITOR.replace('note' ,{
      //   filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      //   height: '100px'
      // });

      // CKEDITOR.replace('quotation_supplier' ,{
      //   filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      //   height: '100px'
      // });


      gettotalamount();

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
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': true,
      'order': [],
      'info': false,
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
      { "data": "no_item"},
      { "data": "detail" },
      { "data": "qty" },
      { "data": "uom" },
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

    function getPersen() {

      var qty = $("#jumlah_item").val();
      var price = $("#price_item").val();

      var currency = $('#currency').val();
      // var prc = price.replace(/\D/g, "");

      var hasil = parseInt(qty) * parseFloat(price);

      if (!isNaN(hasil)) {
          $("#amount_item").val(hasil);

          var total = document.getElementById("amount_item");
          // total.value = formatRupiah(total.value, "");

          var hasil_konversi = parseFloat(konversi(currency,"USD", hasil));
          $('#dollar_item').val(hasil_konversi);
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

      var qty = $("#jumlah_item_edit").val();
      var price = $("#price_item_edit").val();

      var hasil = parseInt(qty) * parseFloat(price);

      if (!isNaN(hasil)) {
         $("#amount_item_edit").val(hasil);

         var hasil_konversi_edit = parseFloat(konversi(currency,"USD", hasil));
          $('#dollar_item_edit').val(hasil_konversi_edit);
      }
    }

    function gettotalamount(){

      var total_beli = 0;

      $.ajax({
          url: "{{ route('admin.gettotalamount') }}?reff_number="+ $("#reff_number").val(),
          method: 'GET',
          success: function(data) {
            // $('#total_amount_beli').text(formatRupiah(data,""));
            $('#total_amount_beli').text(data,"");
            total_beli += parseInt(data);


            var currency = $('#currency').val();
            var total_amount_budget = parseFloat(konversi(currency,"USD",total_beli));
            $('#amount_budget1').val(total_amount_budget);
          }
      });
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
      else if(jen2 == "Machine and Equipment"){
        jenis = "M";
      }
      else if(jen2 == "Vehicle"){
        jenis = "V";
      }
      else if(jen2 == "Tools, Jigs and Furniture"){
        jenis = "T";
      }
      else if(jen2 == "Moulding"){
        jenis = "MD";
      }
      else if(jen2 == "PC and Printer"){
        jenis = "PC";
      }

      if (jen2 == "Office Supplies") {
        jenis = "O";
      }
      else if(jen2 == "Repair and Maintenance"){
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

      $.ajax({
        url: "{{ url('investment/get_nomor_investment') }}", 
        type : 'GET', 
        success : function(data){
          var obj = jQuery.parseJSON(data);
          var tahun = obj.tahun;
          var bulan = obj.bulan;
          var no_urut = obj.no_urut;

          var no = parseInt(no_urut);
          var romawi = romanize(bulan);
          reff_no.value = 'N'+tahun+no_urut+'/'+romawi+'/'+kode+'-'+jenis;
        }
      });

      // reff_no.value = 'N'+kode+'-'+jenis;
        
    }

    function getBudget(budget,no) {

      data = {
        budget:budget,
        category:"{{ $investment->category }}",
        department:"{{ $investment->applicant_department }}",
        type:"{{ $investment->type }}"
      }
      
      budget_list = "";
      $('#budget_no'+no).empty();
      
      budget_list += "<option></option> ";
      $.get('{{ url("fetch/investment/invbudgetlist") }}', data, function(result, status, xhr) {  
        $.each(result.budget, function(index, value){
          budget_list += "<option value="+value.budget_no+">"+value.budget_no+" - "+value.description+"</option> ";
        });
        $('#budget_no'+no).append(budget_list);
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



      if ($("#currency").val() == "") {
        $("#loading").hide();
        alert("Kolom Currency Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var jml = $("#lop").val();

      if ($("#budget_category1").val() != "") {
          jml = 1;   
      }

      var budget_cat = [];
      var budget = [];
      var budget_name = [];
      var sisa = [];
      var amount = [];

      for(var i = 1;i <= jml; i++){

        if ($("#budget_category"+i).val() == null) {
          $("#loading").hide();
          openErrorGritter("Error!", "Semua Kolom Harus Diisi.");
          return false;
        }

        if ($("#amount_budget"+i).val() == null ) {
          $("#loading").hide();
          openErrorGritter("Error!", "Semua Kolom Harus Diisi.");
          return false;
        }

        if($("#amount_budget"+i).val() == "0" || $("#sisa_budget"+i).val() == "0"){
          $("#loading").hide();
          openErrorGritter("Error!", "Tidak Boleh ada yang 0");
          return false;
        }

        if (parseInt($("#amount_budget"+i).val()) > parseInt($("#sisa_budget"+i).val( ))) {
          $("#loading").hide();
          openErrorGritter("Error!", "Budget Tidak Mencukupi, Silahkan Lakukan Transfer Budget Jika Diperlukan");
          return false;
        }

        budget_cat.push($("#budget_category"+i).val());
        budget.push($("#budget_no"+i).val());
        budget_name.push($("#budget_name"+i).val());
        sisa.push($("#sisa_budget"+i).val());
        amount.push($("#amount_budget"+i).val());
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
        note : $("#note").val(),
        quotation_supplier : $("#quotation_supplier").val(),
        currency: $("#currency").val(),
        jumlah: jml,
        budget_cat: budget_cat,
        budget: budget,
        budget_name: budget_name,
        sisa: sisa,
        amount: amount,
        // budget_category: $("#budget_category").val(),
        // budget_no: $("#budget_no").val(),
      };

      
        // note: CKEDITOR.instances.note.getData(),
        // quotation_supplier: CKEDITOR.instances.quotation_supplier.getData(),

      $.post('{{ url("investment/update_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success", result.datas);
          location.reload(); 
          // console.log(data);
        }
        else {
          $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
        
      });

    });

    function create() {

      if ($("#detail_item").val() == "") {
        alert("Kolom Detail Item Harap diisi");
        return false;
      }

      var price = $("#price_item").val();
      // var price_number = price.replace(/\D/g, "");

      var amount = $("#amount_item").val();
      // var amount_number = amount.replace(/\D/g, "");

      var data = {
        reff_number: $("#reff_number").val(),
        kode_item: $("#kode_item").val(),
        detail_item: $("#detail_item").val(),
        jumlah_item : $("#jumlah_item").val(),
        uom : $("#uom").val(),
        price_item : price,
        amount_item : amount,
        dollar : $("#dollar_item").val()
      };

      $.post('{{ url("investment/create_investment_item") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#item').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","New item has been created.");
          gettotalamount();
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
        $("#uom_edit").val(result.datas.uom).trigger('change.select2');
        $("#price_item_edit").val(result.datas.price);
        $("#amount_item_edit").val(result.datas.amount);
        $("#dollar_item_edit").val(result.datas.dollar);

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
        uom: $("#uom_edit").val(),
        price_item: $("#price_item_edit").val(),
        amount_item: $("#amount_item_edit").val(),
        dollar : $("#dollar_item_edit").val(),
      };

      $.post('{{ url("investment/edit_investment_item") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#item').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Item has been edited.");
          gettotalamount();
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
        openSuccessGritter("Success","Berhasil Hapus Item");
        gettotalamount();
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
          list += "<option value='Machine and Equipment'>Machine and Equipment</option>";
          list += "<option value='Vehicle'>Vehicle</option>";          
          list += "<option value='Tools, Jigs and Furniture'>Tools, Jigs and Furniture</option>";
          list += "<option value='Moulding'>Moulding</option>";
          list += "<option value='PC and Printer'>PC and Printer</option>";
          list += "<option value='Land Acquisition'>Land Acquisition</option>";
        }
        else if (isi == "Expense"){
          list += "<option value='Office Supplies'>Office Supplies</option>";
          list += "<option value='Repair and Maintenance'>Repair and Maintenance</option>";
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

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
      var number_string = angka.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
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

      if (!confirm("Apakah anda yakin ingin mengirim Form Investment Ke Bagian Accounting?")) {
        return false;
      }

      $("#loading").show();

      $.get('{{ url("investment/sendemail") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Email Has Been Sent");
        setTimeout(function(){  window.location.href = '{{url("investment")}}'; }, 2000);
      })
    }

    function selectbudget(elem) {

        var no = elem.id.match(/\d/g);
        no = no.join("");

        // if ($('#currency').val() == "USD") {
        //   $('#ket_harga'+no).text("$");
        // }else if ($('#currency').val() == "JPY") {
        //   $('#ket_harga'+no).text("¥");
        // }else if ($('#currency').val() == "IDR"){
        //   $('#ket_harga'+no).text("Rp.");
        // }

        var budget_category = document.getElementById("budget_category"+no);
        var bdg = budget_category.options[budget_category.selectedIndex].value;

        if (bdg == "On Budget" || bdg == "Shifting") {
          $("#budget_dana"+no).show();
          $("#budget_sisa"+no).show();
          $("#budget_total"+no).show();
          getBudget(bdg,no);

        } else if (bdg == "Out Of Budget"){
          $("#budget_dana"+no).hide();
          $("#budget_sisa"+no).hide();
          // $("#budget_total"+no).hide();
          $("#budget_no"+no).val("").trigger('change.select2');
        }

      }



    function tambah(id,lop) {
      var id = id;

      var lop = "";

      if (id == "tambah"){
        lop = "lop";
      }else{
        lop = "lop2";
      }

      var divdata = $("<div id='"+no+"' class='row'><div class='col-xs-2 col-sm-2 col-md-2 col-xs-offset-1'><select class='form-control select3' data-placeholder='Pilih Category Budget' name='budget_category"+no+"' id='budget_category"+no+"' onchange='selectbudget(this)' style='width: 100% height: 35px;' required> <option value=''>&nbsp;</option><option value='Shifting'>Shifting</option><option value='Out Of Budget'>Out of Budget </option></select></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_dana"+no+"'><select class='form-control select3' data-placeholder='Pilih Nomor Budget' name='budget_no"+no+"' id='budget_no"+no+"' onchange='getBudgetName(this)' style='width: 100% height: 35px;' onchange='' required> <option value='{{$investment->budget_no}}'>{{$investment->budget_no}}</option></select><input type='hidden' name='budget_name"+no+"' id='budget_name"+no+"'></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_sisa"+no+"'><div class='input-group'><span class='input-group-addon' id='ket_sisa"+no+"' style='padding:6px'>$</span> <input type='text' class='form-control currency' id='sisa_budget"+no+"' name='sisa_budget"+no+"' placeholder='Beginning Balance' style='padding: 6px 6px' readonly=''></div></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_total"+no+"'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"' style='padding:6px'>?</span><input type='text' class='form-control' id='amount_budget"+no+"' name='amount_budget"+no+"' placeholder='Total Pembelian'></div></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_konversi"+no+"'><div class='input-group'><span class='input-group-addon' id='dollar"+no+"' style='padding:6px'>$</span><input type='text' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Konversi Dollar' readonly=''></div></div><div class='col-xs-1 col-sm-1 col-md-1'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger' style='padding:6px 8px'><i class='fa fa-close'></i> </button> <button type='button' class='btn btn-success' style='padding:6px 8px' onclick='tambah(\""+id+"\",\""+lop+"\"); '><i class='fa fa-plus' ></i></button></div> </div>")

      $("#"+id).append(divdata);

      $(function () {
        $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#"+id),
          allowClear:true,
        });
      })

    document.getElementById(lop).value = no;
    no+=1;
  }

  function tambahDetail(id,lop,nomor) {

      var num = nomor+1;
      var id = id;
      var lop = "";

      if (id == "tambah"){
        lop = "lop";
      }else{
        lop = "lop2";
      }

      var divdata = $("<div id='"+num+"' class='row'><div class='col-xs-2 col-sm-2 col-md-2 col-xs-offset-1'><select class='form-control select3' data-placeholder='Pilih Category Budget' name='budget_category"+num+"' id='budget_category"+num+"' onchange='selectbudget(this)' style='width: 100% height: 35px;' required> <option value=''>&nbsp;</option><option value='Shifting'>Shifting</option><option value='Out Of Budget'>Out of Budget </option></select></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_dana"+num+"'><select class='form-control select3' data-placeholder='Pilih nomor Budget' name='budget_no"+num+"' id='budget_no"+num+"' onchange='getBudgetName(this)' style='width: 100% height: 35px;' required> <option value='{{$investment->budget_no}}'>{{$investment->budget_no}}</option></select><input type='hidden' name='budget_name"+num+"' id='budget_name"+num+"'></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_sisa"+num+"'><div class='input-group'><span class='input-group-addon' id='ket_sisa"+num+"' style='padding:6px'>$</span> <input type='text' class='form-control currency' id='sisa_budget"+num+"' name='sisa_budget"+num+"' placeholder='Beginning Balance' style='padding: 6px 6px' readonly=''></div></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_total"+num+"'><div class='input-group'><span class='input-group-addon' id='ket_harga"+num+"' style='padding:6px'>?</span><input type='text' class='form-control' id='amount_budget"+num+"' name='amount_budget"+num+"' placeholder='Total Pembelian'></div></div><div class='col-xs-2 col-sm-2 col-md-2' id='budget_konversi"+no+"'><div class='input-group'><span class='input-group-addon' id='dollar"+num+"' style='padding:6px'>$</span><input type='text' class='form-control' id='konversi_dollar"+num+"' name='konversi_dollar"+num+"' placeholder='Konversi Dollar' readonly=''></div></div><div class='col-xs-1 col-sm-1 col-md-1'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger' style='padding:6px 8px'><i class='fa fa-close'></i> </button> <button type='button' class='btn btn-success' style='padding:6px 8px' onclick='tambahDetail(\""+id+"\",\""+lop+"\", "+num+"); '><i class='fa fa-plus'></i></button></div> </div>")

      $("#"+id).append(divdata);

      $(function () {
        $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#"+id),
          allowClear:true,
        });
      })

    document.getElementById(lop).value = num;
  }

  function kurang(elem,lop) {

    var lop = lop;
    var ids = $(elem).parent('div').parent('div').attr('id');
    var oldid = ids;
    $(elem).parent('div').parent('div').remove();
    var newid = parseInt(ids) + 1;

    $("#"+newid).attr("id",oldid);
    $("#budget_category"+newid).attr("name","budget_category"+oldid);
    $("#budget_no"+newid).attr("name","budget_no"+oldid);
    $("#sisa_budget"+newid).attr("name","sisa_budget"+oldid);
    $("#budget_total"+newid).attr("name","budget_total"+oldid);

    $("#budget_category"+newid).attr("id","budget_category"+oldid);
    $("#budget_no"+newid).attr("id","budget_no"+oldid);
    $("#sisa_budget"+newid).attr("id","sisa_budget"+oldid);
    $("#budget_total"+newid).attr("id","budget_total"+oldid);

    no-=1;
    var a = no -1;

    for (var i =  ids; i <= a; i++) { 
      var newid = parseInt(i) + 1;
      var oldid = newid - 1;

      $("#"+newid).attr("id",oldid);
      $("#budget_category"+newid).attr("name","budget_category"+oldid);
      $("#budget_no"+newid).attr("name","budget_no"+oldid);
      $("#sisa_budget"+newid).attr("name","sisa_budget"+oldid);
      $("#budget_total"+newid).attr("name","budget_total"+oldid);

      $("#budget_category"+newid).attr("id","budget_category"+oldid);
      $("#budget_no"+newid).attr("id","budget_no"+oldid);
      $("#sisa_budget"+newid).attr("id","sisa_budget"+oldid);
      $("#budget_total"+newid).attr("id","budget_total"+oldid);
    }
    document.getElementById(lop).value = a;
  }

  function deleteConfirmation(name, id) {
      $('#modalDeleteBody').text("Are you sure want to delete ' " + name + " '");
      $('[name=modalDeleteButton]').attr("id",id);
  }

  function delete_budget(id) {

      $("#loading").show();

      var data = {
        id:id,
      }

      $.post('{{ url("delete/investment_budget") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Budget Berhasil Dihapus");
          location.reload();
        }
        else {
          openErrorGritter('Error!');
        }
        
      });

      // $('#modaldanger').modal('hide');
      // $('#'+id).css("display","none");

  }

  function getkonversi(elem)
  {
    var num = elem.id.match(/\d/g);
    num = num.join("");

    var currency = $('#currency').val();
    var price = parseFloat($("#amount_budget"+num).val());

    var total = 0;
    for (var i = 2; i <= no; i++) {
      var a = i-1;
      total += parseFloat($("#amount_budget"+a).val());
    }

    $('#total_amount_bayar').text(formatRupiah(total.toString(),"")); //var total gaoleh int, kudu text

    var harga_konversi = parseFloat(konversi(currency,"USD", price));
    $('#konversi_dollar'+num).val(harga_konversi);
  }

  function getExchangeRate(){
    $.ajax({
      url: "{{ url('purchase_requisition/get_exchange_rate') }}", 
      type : 'GET', 
      success : function(data){
        var obj = jQuery.parseJSON(data);
        for (var i = 0; i < obj.length; i++) {
                var currency = obj[i].currency; // currency
                var rate = obj[i].rate; //nilai tukar

                exchange_rate.push({
                  'currency' :  obj[i].currency, 
                  'rate' :  obj[i].rate,
                });
              }
          }
      });
  }

  function konversi(from,to,amount){
    var obj = exchange_rate;

        // console.log(obj);
    for (var i = 0; i < obj.length; i++) {
        var currency = obj[i].currency; // currency
          var rate = obj[i].rate; //nilai tukar

          if (from == currency) {
            fromrate = rate;
          }

          if (to == currency) {
            torate = rate;
          }
        }
        hasil_konversi = (amount / fromrate) * torate;
        return hasil_konversi.toFixed(2);       
    }

  function getBudgetName(elem){

    var no = elem.id.match(/\d/g);
    no = no.join("");

    $.ajax({
      url: "{{ route('admin.getbudget') }}?budget="+elem.value,
      method: 'GET',
      success: function(data) {
        var json = data,
        obj = JSON.parse(json);
        $('#budget_name'+no).val(obj.budget_desc);
        $('#sisa_budget'+no).val(obj.budget_now);
      } 
    });
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

