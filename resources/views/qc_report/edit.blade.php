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
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Edit {{ $page }}
    <small>Edit CPAR</small>
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
          <label class="col-sm-1">Via Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" name="via_komplain" style="width: 100%;" data-placeholder="Via Komplain" required>
              @if($cpars->via_komplain == "Email")
              <option value="Email" selected>Email</option>
              <option value="Telepon">Telepon</option>
              @elseif($cpars->via_komplain == "Telepon") 
              <option value="Email">Email</option>
              <option value="Telepon" selected>Telepon</option>
              @endif
            </select>
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
              <option value='Lacquering'>Lacquering</option>
              <option value='Meeting Room'>Meeting Room</option>
              <option value='Part Process'>Part Process</option>
              <option value='Pianica'>Pianica</option>
              <option value='Plating'>Plating</option>
              <option value='Recorder'>Recorder</option>
              <option value='Sub Assy'>Sub Assy</option>
              <option value='TR Room'>TR Room</option>
              <option value='Venova'>Venova</option>
              <option value='Warehouse'>Warehouse</option>
              <option value='Welding'>Welding</option>
              <option value='Other'>Other</option>
            </select>
          </div>
          <label class="col-sm-1">CPAR Kepada<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" name="department_id" style="width: 100%;" data-placeholder="Pilih Departemen" required>
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
            <select class="form-control select2" name="sumber_komplain" style="width: 100%;" data-placeholder="Sumber Komplain" required>
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
            <input type="text" class="form-control" name="cpar_no" placeholder="Masukkan Nomor CPAR" value="{{ $cpars->cpar_no }}" readonly="">
          </div>
        </div>

        <div class="form-group row increment" align="left">
          <label class="col-sm-1">File</label>
          <div class="col-sm-5">
            <input type="file" name="files[]">
            {{ $cpars->file }}
            <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button>
          </div>
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
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm" style="color:white">Create Material</a>
            <br><br>
            <table id="example1" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>No CPAR</th>
                  <th>Part Item</th>    
                  <th>No Invoice</th>
                  <th>Lot Qty</th>
                  <th>Sample Qty</th>
                  <th>Detail Problem</th>
                  <th>Defect Qty</th>
                  <th>Defect Presentase</th>
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
  </div>

  <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
              <div class="col-sm-2"></div>
              <label class="col-sm-2">CPAR No<span class="text-red">*</span></label>
              <div class="col-sm-6">
                 {{ $cpars->cpar_no }}
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Part Item<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <select class="form-control select2" id="part_item" style="width: 100%;" data-placeholder="Pilih Material" required>
                  <option value=""></option>
                  @foreach($materials as $material)
                      <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">No Invoice<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="no_invoice" placeholder="No Invoice" required>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Lot Qty<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <div class="input-group">
                <input type="number" class="form-control" id="lot_qty" placeholder="Lot Quantity" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Sample Qty<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <div class="input-group">
                  <input type="number" class="form-control" id="sample_qty" placeholder="Sample Quantity" required>
                  <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Detail Masalah<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <textarea class="form-control" id="detail_problem" placeholder="Detail Masalah" required></textarea>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Defect Qty<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <div class="input-group">
                  <input type="number" class="form-control" id="defect_qty" placeholder="Defect Quantity" required>
                  <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Defect Presentase<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <input type="number" class="form-control" id="defect_presentase" placeholder="Defect Presentase" required>
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
              <label class="col-sm-2">No Invoice</label>
              <div class="col-sm-6" align="left" id="no_invoice_view"></div>
            </div>
            <div class="form-group row" align="left">
              <label class="col-sm-4"></label>
              <label class="col-sm-2">Lot Qty</label>
              <div class="col-sm-6" align="left" id="lot_qty_view"></div>
            </div>
            <div class="form-group row" align="left">
              <label class="col-sm-4"></label>
              <label class="col-sm-2">Sample Qty</label>
              <div class="col-sm-6" align="left" id="sample_qty_view"></div>
            </div>
            <div class="form-group row" align="left">
              <label class="col-sm-4"></label>
              <label class="col-sm-2">Detail Problem</label>
              <div class="col-sm-6" align="left" id="detail_problem_view"></div>
            </div>
            <div class="form-group row" align="left">
              <label class="col-sm-4"></label>
              <label class="col-sm-2">Defect Qty</label>
              <div class="col-sm-6" align="left" id="defect_qty_view"></div>
            </div>
            <div class="form-group row" align="left">
              <label class="col-sm-4"></label>
              <label class="col-sm-2">Defect Presentase</label>
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

  <div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Create Material CPAR</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">CPAR No<span class="text-red">*</span></label>
              <div class="col-sm-6">
                 {{ $cpars->cpar_no }}
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Part Item<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <select class="form-control select2" id="part_item_edit" style="width: 100%;" data-placeholder="Pilih Material" required>
                  <option value=""></option>
                  @foreach($materials as $material)
                      <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">No Invoice<span class="text-red">*</span></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="no_invoice_edit" placeholder="No Invoice" required>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Lot Qty<span class="text-red">*</span></label>
              
              <div class="col-sm-6">
                <div class="input-group">
                <input type="number" class="form-control" id="lot_qty_edit" placeholder="Lot Quantity" required>
                <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Sample Qty<span class="text-red">*</span></label>
              
              <div class="col-sm-6">
                <div class="input-group">
                <input type="number" class="form-control" id="sample_qty_edit" placeholder="Sample Quantity" required>
                <span class="input-group-addon">pc(s)</span>
                </div>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Detail Masalah<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <textarea class="form-control" id="detail_problem_edit" placeholder="Detail Masalah" required></textarea>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Defect Qty<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <div class="input-group">
                <input type="number" class="form-control" id="defect_qty_edit" placeholder="Defect Quantity" required>
              <span class="input-group-addon">pc(s)</span>
                </div>
              </div>

            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-2"></div>
              <label class="col-sm-2">Defect Presentase<span class="text-red">*</span></label>
              <div class="col-sm-6" align="left">
                <input type="number" class="form-control" id="defect_presentase_edit" placeholder="Defect Presentase" required>
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
        { "data": "detail_problem" , "width": "10%" },
        { "data": "defect_qty" },
        { "data": "defect_presentase" },
        { "data": "action", "width": "15%" }
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



    $(function () {
      $('.select2').select2()
    })

    $('#tgl_permintaan').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true
    });

    $('#tgl_balas').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true
    });

    CKEDITOR.replace('detail_problem' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('detail_problem_edit' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });


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

    function modalView(id) {
      $("#ViewModal").modal("show");
      var data = {
        id:id
      }

      $.get('{{ url("index/qc_report/view_item") }}', data, function(result, status, xhr){
        $("#cpar_no_view").text(result.datas[0].cpar_no);
        $("#part_item_view").text(result.datas[0].part_item);
        $("#no_invoice_view").text(result.datas[0].no_invoice);
        $("#lot_qty_view").text(result.datas[0].lot_qty);
        $("#sample_qty_view").text(result.datas[0].sample_qty);
        $("#detail_problem_view").text(result.datas[0].detail_problem);
        $("#defect_qty_view").text(result.datas[0].defect_qty);
        $("#defect_presentase_view").text(result.datas[0].defect_presentase)
        $("#last_updated_view").text(result.datas[0].updated_at);
        $("#created_at_view").text(result.datas[0].created_at);
      })
    }

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
      })
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
        if (result.status == true) {
          $('#example1').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Material has been edited.");
        } else {
          openErrorGritter("Error","Failed to edit material.");
        }
      })
    }

    function modalDelete(id) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yaking ingin menghapus material ini?")) {
        return false;
      }

      $.post('{{ url("index/qc_report/delete_item") }}', data, function(result, status, xhr){
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Delete Material");
      })
    }

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