@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <small>Detail CPAR</small>
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
      <label class="label label-default">Posisi CPAR : <b>{{$cpars->posisi}}</b></label>
       
       <a href="{{url('index/qc_report/print_cpar', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" title="Lihat Report"  target="_blank">Preview Report</a>

       <a data-toggle="modal" data-target="#statusmodal{{$cpars->id}}" class="btn btn-primary btn-sm pull-right" style="color:white;margin-right: 5px">Cek Status Verifikasi</a>

       @if($cpars->email_status == NULL && $cpars->posisi == "staff" && Auth::user()->username == $cpars->staff) <!-- Mas Said -->
           <a class="btn btn-sm btn-info pull-right" data-toggle="tooltip" title="Send Email Ke Chief" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Chief</a>
          
          <!-- <a href="{{url('index/qc_report/sendemail/'.$cpars['id'].'/'.$cpars['posisi'])}}" class="btn btn-sm ">Email </a> -->

       @elseif($cpars->email_status == NULL && $cpars->posisi == "leader" && Auth::user()->username == $cpars->leader)
           <a class="btn btn-sm btn-info pull-right" data-toggle="tooltip" title="Send Email Ke Chief / Foreman" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email Ke Chief / Foreman</a>

       @else
           <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
       @endif

       <!-- <a href="{{url('index/qc_report/statuscpar', $cpars['id'])}}" data-toggle="tooltip" class="btn btn-primary btn-sm pull-right" title="Status Verifikasi" style="margin-right: 5px">Cek Status Verifikasi</a> -->
      
    </div>  

    <form role="form" method="post" action="{{url('index/qc_report/update_action', $cpars->id)}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">Kepada<span class="text-red">*</span></label>
          <div class="col-sm-5" align="left">
            <select class="form-control select2" name="employee_id" style="width: 100%;" data-placeholder="Pilih Manager" required>
              <option value=""></option>
              @foreach($managers as $manager)
              @if($manager->employee_id == $cpars->employee_id)
              <option value="{{ $manager->employee_id }}" selected="">{{ $manager->name }} - {{ $manager->position }} {{ $manager->department }}</option>
              @else
              <option value="{{ $manager->employee_id }}">{{ $manager->name }} - {{ $manager->position }} {{ $manager->department }}</option>
              @endif
              @endforeach
            </select>
          </div>
          <label class="col-sm-1">Judul Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="judul_komplain" id="judul_komplain" placeholder="Judul / Subject Komplain" value="{{$cpars->judul_komplain}}" required="">
            <input type="hidden" class="form-control" name="via_komplain" id="via_komplain" value="{{$cpars->via_komplain}}" required readonly>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Lokasi<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" style="width: 100%;" id="lokasi" name="lokasi" data-placeholder="Pilih Lokasi" required>
              <option value="{{$cpars->lokasi}}">{{ $cpars->lokasi }}</option>
              <option value='Office'>Office</option>
              <option value='Assy'>Assy</option>
              <option value='Body Process'>Body Process</option>
              <option value='Buffing'>Buffing</option>
              <option value='CL Body'>CL Body</option>
              <option value='Lacquering'>Lacquering</option>s
              <option value='Part Process'>Part Process</option>
              <option value='Pianica'>Pianica</option>
              <option value='Plating'>Plating</option>
              <option value='Recorder'>Recorder</option>
              <option value='Sub Assy'>Sub Assy</option>
              <option value='Case KD'>Case KD</option>
              <option value='Venova'>Venova</option>
              <option value='Warehouse'>Warehouse</option>
              <option value='Welding'>Welding</option>
              <option value='Other'>Other</option>
            </select>
          </div>
          <label class="col-sm-1">Departemen<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" name="department_id" id="department_id" style="width: 100%;" data-placeholder="Pilih Departemen" onchange="selectdepartemen()" required>
              <optgroup label="Production">
                @foreach($productions as $production)
                @if($production->id == $cpars->department_id)
                <option value="{{ $production->id }}" selected>{{ $production->department_name }}</option>
                @else
                <option value="{{ $production->id }}">{{ $production->department_name }}</option>
                @endif
                @endforeach
              </optgroup>
              <optgroup label="Procurement">
                @foreach($procurements as $procurment)
                @if($procurment->id == $cpars->department_id)
                <option value="{{ $procurment->id }}" selected>{{ $procurment->department_name }}</option>
                @else
                <option value="{{ $procurment->id }}">{{ $procurment->department_name }}</option>
                @endif
                @endforeach
              </optgroup>
              <optgroup label="Other">
                @foreach($others as $other)
                @if($other->id == $cpars->department_id)
                <option value="{{ $other->id }}" selected>{{ $other->department_name }}</option>
                @else
                <option value="{{ $other->id }}">{{ $other->department_name }}</option>
                @endif
                @endforeach
              </optgroup>
            </select>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tanggal Permintaan<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="tgl_permintaan" name="tgl_permintaan" placeholder="Masukkan Tanggal Permintaan" value="{{ date('d/m/Y', strtotime($cpars->tgl_permintaan)) }}" required>
            </div>
          </div>
          <label class="col-sm-1">Sumber Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" id="sumber_komplain" name="sumber_komplain" style="width: 100%;" data-placeholder="Sumber Komplain" onchange="selectsumber()" required>
              @if($cpars->sumber_komplain == "Eksternal Complaint")
              <option value="Eksternal Complaint" selected>Eksternal Complaint</option>
              <option value="Audit QA">Audit QA</option>
              <option value="Production Finding">Production Finding</option>
              @elseif($cpars->sumber_komplain == "Audit QA") 
              <option value="Eksternal Complaint">Eksternal Complaint</option>
              <option value="Audit QA" selected>Audit QA</option>
              <option value="Production Finding">Production Finding</option>
              @elseif($cpars->sumber_komplain == "Production Finding") 
              <option value="Eksternal Complaint">Eksternal Complaint</option>
              <option value="Audit QA">Audit QA</option>
              <option value="Production Finding" selected>Production Finding</option>
              @endif
            </select>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tanggal Balas<span class="text-red">*</span></label>
          <div class="col-sm-5">
           <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" id="tgl_balas" name="tgl_balas" placeholder="Masukkan Tanggal Balas" value="{{ date('d/m/Y', strtotime($cpars->tgl_balas)) }}" required>
          </div>
        </div>
        <label class="col-sm-1">No CPAR<span class="text-red">*</span></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="cpar_no" id="cpar_no" placeholder="Masukkan Nomor CPAR" value="{{ $cpars->cpar_no }}" readonly="">
          <input type="text" class="form-control" name="kategori" id="kategori" placeholder="kategori" value="{{ $cpars->kategori }}">
          <input type="hidden" class="form-control" name="nomordepan" id="nomordepan" placeholder="nomordepan" required>
          <input type="hidden" class="form-control" name="lastthree" id="lastthree" placeholder="lastthree" required>
        </div>
      </div>

      <div id="kategori_komplain"></div>

      <div class="form-group row increment" align="left">
        <label class="col-sm-1">File</label>
        <div class="col-sm-5">
          <input type="file" name="files[]" multiple="">
          <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button>
        </div>
        <span id="customer">
            <label class="col-sm-1">Customer<span class="text-red">*</span></label>
            <div class="col-sm-5" align="left">
              <select class="form-control select2" name="customer" style="width: 100%;" data-placeholder="Pilih Customer">
                @foreach($destinations as $destination)
                @if($destination->destination_code == $cpars->destination_code)
                <option value="{{ $destination->destination_code }}" selected>{{ $destination->destination_shortname }} - {{ $destination->destination_name }}</option>
                @else
                <option value=""></option>
                <option value="{{ $destination->destination_code }}">{{ $destination->destination_shortname }} - {{ $destination->destination_name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </span>
          <span id="supplier">
            <label class="col-sm-1">Supplier<span class="text-red">*</span></label>
            <div class="col-sm-5" align="left">
              <select class="form-control select2" name="supplier" style="width: 100%;" data-placeholder="Pilih Supplier">
                @foreach($vendors as $vendor)
                @if($vendor->vendor == $cpars->vendor)
                <option value="{{ $vendor->vendor }}" selected>{{ $vendor->name }}</option>
                @else
                <option value=""></option>
                <option value="{{ $vendor->vendor }}">{{ $vendor->name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </span>

          <div id="kategori_komplain_internal"></div>
      </div>
      <div class="clone hide">
        <div class="form-group row control-group" style="margin-top:10px">
          <label class="col-sm-1">File</label>
          <div class="col-sm-6">
            <input type="file" name="files[]">
            <div class="input-group-btn"> 
              <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
          </div>
        </div>
      </div>
      
        <!-- <div class="form-group row" align="left">
          <label class="col-sm-1">File</span></label>
          <div class="col-sm-5">
            <input type="file" name="file">
            {{ $cpars->file }}
          </div>
        </div> -->

        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_report') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Update</button>
          </div>
        </div>
        <?php if ($cpars->file != null){ ?>
            <br><br>
              <div class="box box-warning box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">File Yang Telah Diupload</h3>

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
                        <div class="col-md-3">
                          <div class="isi">
                            <?= $data[$i] ?>
                          </div>
                        </div>
                        <div  class="col-md-2">
                            <a href="{{ url('/files/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                        </div>
                        <div class="col-md-1">
                          <a href="javascript:void(0)" onclick="hapus('{{$data[$i]}}','{{$cpars->id}}')" class="btn btn-danger pull-left">
                            <i class="fa fa-trash"></i></a>
                      </div>   
                      </div>
                  <?php } ?>                       
                </div>
              </div>    
          <?php } ?>
      </div>

    </form>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <a data-toggle="modal" data-target="#createModal" class="btn btn-primary col-sm-12" style="width: 100%;color:white;font-weight: bold; font-size: 20px">Tambahkan Material</a>
            <br><br><br>
            <table id="example1" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>No CPAR</th>
                  <th>Part Item</th>    
                  <th>No Invoice</th>
                  <th>Jumlah Lot</th>
                  <th>Jumlah Cek</th>
                  <th>Detail Masalah</th>
                  <th>Jumlah Defect</th>
                  <th>Presentase Defect</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- @foreach($parts as $part)
                <tr>
                  <td>{{ $part->cpar_no }}</td>
                  <td>{{ $part->part_item }}</td>
                  <td>{{ $part->no_invoice }}</td>
                  <td>{{ $part->lot_qty }}</td>
                  <td>{{ $part->sample_qty }}</td>
                  <td>{{ $part->detail_problem }}</td>
                  <td>{{ $part->defect_qty }}</td>
                  <td>{{ $part->defect_presentase }} %</td>
                  <td style="width: 10%">
                    <center>
                      <a href="{{url('index/qc_report/update', $part['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                      <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/qc_report/delete") }}', '{{ $part['part_item'] }}', '{{ $part['id'] }}');">
                        Delete
                      </a>
                    </center>
                  </td>
                </tr>
                @endforeach -->
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
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 10px">
      <div class="col-xs-12">
        <div class="box">
          <form role="form" method="post" action="{{url('index/qc_report/update_deskripsi', $cpars->id)}}" enctype="multipart/form-data">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <label class="col-sm-2" style="font-size: 18px">Immediate Action<span class="text-red">*</span></label>
              <div class="col-sm-12">
                <textarea type="text" class="form-control" name="action" placeholder="Masukkan Deskripsi">{{ $cpars->tindakan }}</textarea>
                <br><div class="btn-group">
                  <button type="submit" class="btn btn-primary col-sm-14">Update</button>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1100px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><center>CPAR <b>{{ $cpars->cpar_no }}</b></center></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="cpar_no" value="{{ $cpars->cpar_no }}">
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">CPAR No<span class="text-red">*</span></label>
              <div class="col-sm-8">
               {{ $cpars->cpar_no }}
             </div>
           </div>
           <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Part Item<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select3" id="part_item" name="part_item" style="width: 100%;" data-placeholder="Pilih Material" required>
                <option value=""></option>
                @foreach($materials as $material)
                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="left" id="desc">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Material Description<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="material_description" placeholder="Material Description" required readonly>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">No Invoice / No Lot</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="no_invoice" placeholder="No Invoice" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Lot</span></label>
            <div class="col-sm-8">
              <div class="input-group">
                <input type="number" class="form-control" id="lot_qty" placeholder="Jumlah Lot" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Cek</span></label>
            <div class="col-sm-8">
              <div class="input-group">
                <input type="number" class="form-control" id="sample_qty" placeholder="Jumlah Cek / Temuan" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Detail Masalah<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea class="form-control" id="detail_problem" placeholder="Detail Masalah" required></textarea>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Defect</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <input type="number" class="form-control" id="defect_qty" placeholder="Jumlah Defect" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Presentase Defect (Persen)</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" id="defect_presentase" placeholder="Presentase Defect" disabled required>
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

<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Detail Material</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Nomor CPAR</label>
            <div class="col-sm-6" align="left" id="cpar_no_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Part Item</label>
            <div class="col-sm-6" align="left" id="part_item_view"></div>
          </div>          
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">No Invoice / No Lot</label>
            <div class="col-sm-6" align="left" id="no_invoice_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Jumlah Lot</label>
            <div class="col-sm-6" align="left" id="lot_qty_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Jumlah Cek</label>
            <div class="col-sm-6" align="left" id="sample_qty_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Detail Masalah</label>
            <div class="col-sm-6" align="left" id="detail_problem_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Jumlah Defect</label>
            <div class="col-sm-6" align="left" id="defect_qty_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Presentase Defect</label>
            <div class="col-sm-6" align="left" id="defect_presentase_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Last Update</label>
            <div class="col-sm-6" align="left" id="last_updated_view"></div>
          </div>
          <div class="form-group row" align="left">
            <label class="col-sm-4"></label>
            <label class="col-sm-2">Created At</label>
            <div class="col-sm-6" align="left" id="created_at_view"></div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="statusmodal{{$cpars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Status CPAR Sekarang</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <table class="table table-hover">
          <form role="form" method="post" action="{{url('index/qc_report/checked/'.$cpars->id)}}">
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                <tr style="background-color: #4caf50;color: white">
                    <td colspan="2" style="width: 33%"><b>Position</b></td>
                    <td colspan="2" style="width: 33%"><b>Action</b></td>
                    <td colspan="2" style="width: 33%"><b>Email</b></td>
                </tr>
                <tr>
                    <td colspan="2">
                      <b>
                        @if($cpars->staff != NULL) 
                            Staff
                        @elseif($cpars->leader != NULL) 
                            Staff / Leader
                        @endif
                      </b>
                    </td>
                    <td colspan="2"><b><span class="label label-success">Already Created</span></b></td>
                    @if($cpars->email_status == NULL && $cpars->posisi == "staff" || $cpars->posisi == "leader")
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @endif
                </tr>
                @if($cpars->chief != NULL || $cpars->foreman != NULL)
                <tr>
                    <td colspan="2"><b>
                        @if($cpars->staff != NULL) 
                            Chief
                        @elseif($cpars->leader != NULL) 
                            Chief / Foreman
                        @endif</b></td>
                    <td colspan="2"><b>
                      @if($cpars->checked_chief == "Checked" || $cpars->checked_foreman == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cpars->email_status == "SentManager" || $cpars->email_status == "SentDGM" || $cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") &&  ($cpars->posisi == "manager" || $cpars->posisi == "dgm" || $cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                @endif
                <tr>
                    <td colspan="2"><b>Manager</b></td>
                    <td colspan="2"><b>
                      @if($cpars->checked_manager == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cpars->email_status == "SentDGM" || $cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") && ($cpars->posisi == "dgm" || $cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>DGM</b></td>
                    <td colspan="2"><b>
                      @if($cpars->approved_dgm == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cpars->email_status == "SentGM" || $cpars->email_status == "SentBagian") && ($cpars->posisi == "gm" || $cpars->posisi == "bagian"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>GM</b></td>
                    <td colspan="2"><b>
                      @if($cpars->approved_gm == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if($cpars->email_status == "SentBagian" && $cpars->posisi == "bagian")
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>Bagian</b></td>
                    <td colspan="2"><b>
                      @if($cpars->received_manager == "Received")
                      <span class="label label-success">Received</span>
                      @else
                      <span class="label label-danger">Not Received</span>
                      @endif</b>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Edit Material CPAR</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">CPAR No<span class="text-red">*</span></label>
            <div class="col-sm-8">
             {{ $cpars->cpar_no }}
           </div>
         </div>
         <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Part Item<span class="text-red">*</span></label>
          <div class="col-sm-8">
            <select class="form-control select4" id="part_item_edit" style="width: 100%;" data-placeholder="Pilih Material" required>
              <option value=""></option>
              @foreach($materials as $material)
              <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Material Description<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="material_description_edit" placeholder="Material Description" required readonly>
            </div>
          </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">No Invoice / No Lot</span></label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="no_invoice_edit" placeholder="No Invoice" required>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Jumlah Lot</span></label>
          
          <div class="col-sm-8">
            <div class="input-group">
              <input type="number" class="form-control" id="lot_qty_edit" placeholder="Jumlah Lot" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Jumlah Cek</span></label>
          
          <div class="col-sm-8">
            <div class="input-group">
              <input type="number" class="form-control" id="sample_qty_edit" placeholder="Jumlah Cek / Temuan" onkeyup="getPersenEdit()" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Detail Masalah</span></label>
          <div class="col-sm-8" align="left">
            <textarea class="form-control" id="detail_problem_edit" placeholder="Detail Masalah" required></textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Jumlah Defect</span></label>
          <div class="col-sm-8" align="left">
            <div class="input-group">
              <input type="number" class="form-control" id="defect_qty_edit" placeholder="Jumlah Defect" onkeyup="getPersenEdit()" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>

        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Presentase Defect</span></label>
          <div class="col-sm-8" align="left">
            <input type="number" class="form-control" id="defect_presentase_edit" placeholder="Presentase Defect" disabled required>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      <input type="hidden" id="id_edit">
      <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
    </div>
  </div>
</div>
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
<script>
  $(document).ready(function() {

    $(".plusdata").click(function(){ 
      var html = $(".clone").html();
      $(".increment").after(html);
    });

    $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
    });

    // var kategori_cpar = document.getElementById("kategori")

    if (document.getElementById("kategori").value == "Eksternal") {
      $("#customer").show();
      $("#supplier").hide();

      $k = '{{ $cpars->kategori_komplain }}';

      $addeksternal = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><?php if($cpars->kategori_komplain == "KD Parts") { ?><option value="KD Parts" selected>KD Parts</option><option value="FG">FG</option><option value="NG Jelas">NG Jelas</option><option value="Claim Rate">Claim Rate</option> <?php } else if ($cpars->kategori_komplain == "FG") { ?> <option value="KD Parts">KD Parts</option><option value="FG" selected>FG</option><option value="NG Jelas">NG Jelas</option><option value="Claim Rate">Claim Rate</option><?php } else if ($cpars->kategori_komplain == "NG Jelas") { ?> <option value="KD Parts">KD Parts</option><option value="FG">FG</option><option value="NG Jelas" selected>NG Jelas</option><option value="Claim Rate">Claim Rate</option><?php } else if ($cpars->kategori_komplain == "Claim Rate") { ?><option value="KD Parts">KD Parts</option><option value="FG">FG</option><option value="NG Jelas">NG Jelas</option><option value="Claim Rate" selected>Claim Rate</option><?php } else { ?> <option></option><option value="KD Parts">KD Parts</option><option value="FG">FG</option><option value="NG Jelas">NG Jelas</option><option value="Claim Rate">Claim Rate</option><?php } ?>';

      $('#kategori_komplain').append($addeksternal);

    } else if (document.getElementById("kategori").value == "Supplier"){
      $("#supplier").show();
      $("#customer").hide();

      $addsupplier = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><?php if($cpars->kategori_komplain == "Non YMMJ") { ?><option value="Non YMMJ" selected>Non YMMJ</option><?php } else { ?><option></option><option value="Non YMMJ">Non YMMJ</option><?php } ?></select></div></div>';

      $('#kategori_komplain').append($addsupplier);


    } else if (document.getElementById("kategori").value == "Internal"){
      $("#customer").hide();
      $("#supplier").hide();


      $addinternal = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><?php if($cpars->kategori_komplain == "Ketidaksesuaian Kualitas") { ?><option value="Ketidaksesuaian Kualitas" selected>Ketidaksesuaian Kualitas</option><?php } else { ?><option></option><option value="Ketidaksesuaian Kualitas">Ketidaksesuaian Kualitas</option><?php } ?></select></div></div>';

      $('#kategori_komplain_internal').append($addinternal);
    }
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
    var table = $('#example1').DataTable({
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
        "url" : "{{ url("index/qc_report/fetch_item",$cpars->id) }}"
      },
      "columns": [
      { "data": "cpar_no" },
      { "data": "part_item"},
      { "data": "no_invoice" },
      { "data": "lot_qty" },
      { "data": "sample_qty" },
      { "data": "detail_problem" , "width": "15%" },
      { "data": "defect_qty" },
      { "data": "defect_presentase" },
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
      } );
    } );

      // var detail = json_decode($columns,true);
      // console.log($columns[4]);

      $('#example1 tfoot tr').appendTo('#example1 thead');
    });
  
  </script>
  <script>
        $("#desc").hide();
        $("#hp").hide();
        $("#part_item").change(function(){
          $("#desc").show();
          $("#hp").show();
          // console.log($(this).val());
            $.ajax({
                url: "{{ route('admin.getmaterialsbymaterialsnumber') }}?materials_number=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                  var json = data,
                  obj = JSON.parse(json);
                  console.log(obj);
                  $('#material_description').val(obj.material_description);
                }
            });
        });

        $("#part_item_edit").change(function(){
          $("#desc").show();
          $("#hp").show();
          // console.log($(this).val());
            $.ajax({
                url: "{{ route('admin.getmaterialsbymaterialsnumber') }}?materials_number=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                  var json = data,
                  obj = JSON.parse(json);
                  console.log(obj);
                  $('#material_description_edit').val(obj.material_description);
                }
            });
        });

    $(function () {
      $('.select2').select2()
    })
    
    $(function () {
      $('.select3').select2({
        dropdownParent: $('#createModal')
      });
      $('.select4').select2({
        dropdownParent: $('#EditModal')
      });
    })

    $('#tgl_permintaan').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true,
    });

    $('#tgl_balas').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true,
    });

    CKEDITOR.replace('detail_problem' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('detail_problem_edit' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function getPersen() {
      var def = document.getElementById("defect_qty").value;
      var samp = document.getElementById("sample_qty").value;
      var hasil = parseInt(def) / parseInt(samp) * 100;
      var hasil2 = parseFloat(Math.round(hasil * 100) / 100).toFixed(2);
      if (!isNaN(hasil)) {
         document.getElementById('defect_presentase').value = hasil2;
      }

    }

    function getPersenEdit() {
      var def = document.getElementById("defect_qty_edit").value;
      var samp = document.getElementById("sample_qty_edit").value;
      var hasiledit = parseInt(def) / parseInt(samp) * 100;
      var hasiledit2 = parseFloat(Math.round(hasiledit * 100) / 100).toFixed(2);
      if (!isNaN(hasiledit)) {
         document.getElementById('defect_presentase_edit').value = hasiledit2;
      }
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

    function addZero(i) {
      if (i < 10) {
        i = "0" + i;
      }
      return i;
    }

    function selectdepartemen(){

      $.ajax({
           url: "{{ url('index/qc_report/get_fiscal_year') }}", // your php file
           type : 'GET', // type of the HTTP request
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var lastthree = obj.substr(obj.length - 3);
              // nomorcpar.value = "no/"+lastthree+"."+kategori+"/"+romawi+"/"+year;
              $('#lastthree').val(lastthree);
           }
        });
    }


    function selectsumber() {

        var sumber = document.getElementById("sumber_komplain");
        var departemen = document.getElementById("department_id");
        var nomorcpar = document.getElementById("cpar_no");
        var kategori_cpar = document.getElementById("kategori");
        var getdepartemen = departemen.options[departemen.selectedIndex].value;
        var getsumber = sumber.options[sumber.selectedIndex].value;
        var kategori;

        var lastthree = $('#lastthree').val();

        if (getsumber == "Eksternal Complaint"){
          kategori = "E";
        }
        else if ((getdepartemen == 7 && getsumber == "Audit QA") || (getdepartemen == 7 && getsumber == "Production Finding")){
          kategori = "S";
        }
        else if (getdepartemen != 7 && getsumber == "Production Finding" || getsumber == "Audit QA"){
          kategori = "I";
        }

        if (kategori == "E") {
          kategori_cpar.value = "Eksternal";
          $("#customer").show();
          $("#supplier").hide();
        } else if (kategori == "S"){
          kategori_cpar.value = "Supplier";
          $("#supplier").show();
          $("#customer").hide();
        } else if (kategori == "I"){
          kategori_cpar.value = "Internal";
          $("#customer").hide();
          $("#supplier").hide();
        }

        var bulan = new Date().getMonth()+1;
        var romawi = romanize(bulan);
        var year = new Date().getFullYear();

        $.ajax({
           url: "{{ url('index/qc_report/get_nomor_depan') }}?kategori=" + kategori_cpar.value, 
           type : 'GET', 
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var nomordepan = obj;
              // if (nomordepan == "") {
              //   nomordepan = 1;
              // }
              var no = nomordepan.split("/");
              var number = parseInt(no[0])
              $('#nomordepan').val(number+1);
              var nomordepan = $('#nomordepan').val();
              var truenumber = addZero(nomordepan);
              // var nomorsplit = nomor.split("/");
              nomorcpar.value = truenumber+"/"+lastthree+"."+kategori+"/"+romawi+"/"+year;
           }
        });
    }


    function create() {

      var data = {
        cpar_no: $("#cpar_no").val(),
        part_item: $("#part_item").val(),
        no_invoice: $("#no_invoice").val(),
        lot_qty : $("#lot_qty").val(),
        sample_qty : $("#sample_qty").val(),
        detail_problem : CKEDITOR.instances.detail_problem.getData(),
        defect_qty : $("#defect_qty").val(),
        defect_presentase : $("#defect_presentase").val()
      };

      // console.log(data);

      $.post('{{ url("index/qc_report/create_item") }}', data, function(result, status, xhr){
        // console.log(result.status);
        if (result.status == true) {
          $('#example1').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","New Material has been created.");
        } else {
          openErrorGritter("Error","Material not created.");
        }
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

    // function modalView(id) {
    //   $("#ViewModal").modal("show");
    //   var data = {
    //     id:id
    //   }

    //   $.get('{{ url("index/qc_report/view_item") }}', data, function(result, status, xhr){
    //     $("#cpar_no_view").text(result.datas[0].cpar_no);
    //     $("#part_item_view").text(result.datas[0].part_item);
    //     $("#no_invoice_view").text(result.datas[0].no_invoice);
    //     $("#lot_qty_view").text(result.datas[0].lot_qty);
    //     $("#sample_qty_view").text(result.datas[0].sample_qty);
    //     $("#detail_problem_view").text(result.datas[0].detail_problem);
    //     $("#defect_qty_view").text(result.datas[0].defect_qty);
    //     $("#defect_presentase_view").text(result.datas[0].defect_presentase)
    //     $("#last_updated_view").text(result.datas[0].updated_at);
    //     $("#created_at_view").text(result.datas[0].created_at);
    //   })
    // }

    function modalEdit(id) {
      $('#EditModal').modal("show");
      var data = {
        id:id
      };
      
      $.get('{{ url("index/qc_report/edit_item") }}', data, function(result, status, xhr){
        $("#id_edit").val(id);
        $("#part_item_edit").val(result.datas.part_item).trigger('change.select2');
        $("#no_invoice_edit").val(result.datas.no_invoice);
        $("#lot_qty_edit").val(result.datas.lot_qty);
        $("#sample_qty_edit").val(result.datas.sample_qty);
        $("#detail_problem_edit").html(CKEDITOR.instances.detail_problem_edit.setData(result.datas.detail_problem));
        $("#defect_qty_edit").val(result.datas.defect_qty);
        $("#defect_presentase_edit").val(result.datas.defect_presentase);
        $.ajax({
                url: "{{ route('admin.getmaterialsbymaterialsnumber') }}?materials_number=" + result.datas.part_item,
                method: 'GET',
                success: function(data) {
                  var json = data,
                  obj = JSON.parse(json);
                  console.log(obj);
                  $('#material_description_edit').val(obj.material_description);
                }
            });
      });
    }

    function edit() {

      var data = {
        id: $("#id_edit").val(),
        part_item: $("#part_item_edit").val(),
        no_invoice: $("#no_invoice_edit").val(),
        lot_qty: $("#lot_qty_edit").val(),
        sample_qty: $("#sample_qty_edit").val(),
        detail_problem: CKEDITOR.instances.detail_problem_edit.getData(),
        defect_qty: $("#defect_qty_edit").val(),
        defect_presentase: $("#defect_presentase_edit").val()
      };

      $.post('{{ url("index/qc_report/edit_item") }}', data, function(result, status, xhr){

        // console.log(result.datas);
        if (result.status == true) {
          $('#example1').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Material has been edited.");
        } else {
          openErrorGritter("Error","Failed to edit material.");
        }
      })
    }

    function hapus(nama_file,idcpar){
        var data = {
          nama_file : nama_file,
          idcpar : idcpar
        };
        $.post('{{ url("index/qc_report/deletefiles") }}', data, function(result, status, xhr){
          if(result.status){
            openSuccessGritter('Success Hapus File', result.message);
            location.reload();
          }
          else{
            openErrorGritter('Error!', result.message);
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

      $.post('{{ url("index/qc_report/delete_item") }}', data, function(result, status, xhr){
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Delete Material");
      })
    }

    function sendemail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim CPAR ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/sendemail/$cpars->id/$cpars->posisi") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    CKEDITOR.replace('action' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function deleteConfirmation(url, name, id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id);
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
  @stop