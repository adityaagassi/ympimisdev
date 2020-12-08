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

  .col-xs-2{
    padding-top: 5px;
  }
  .col-xs-3{
    padding-top: 5px;
  }
  .col-xs-5{
    padding-top: 5px;
  }
  .col-xs-6{
    padding-top: 5px;
  }
  .col-xs-7{
    padding-top: 5px;
  }
  .col-xs-8{
    padding-top: 5px;
  }
  
  #loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <small>Detail Form Ketidaksesuaian</small>
  </h1>
  <ol class="breadcrumb" style="width: 500px">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}

   <?php $user = STRTOUPPER(Auth::user()->username)?>

   @if($cpar->posisi == "sl" && ($cpar->kategori == "Service" || $cpar->kategori == "Kualitas") && ($user == $cpar->pelapor || Auth::user()->role_code == "MIS"))
   <a class="btn btn-sm btn-success pull-right" data-toggle="tooltip" title="Send Email" onclick="sendemail({{ $cpar->id }})" style="width:200px">Send Email</a>

   @elseif($cpar->posisi == "sl" && ($cpar->kategori == "Kualitas_Spec" || $cpar->kategori == "Kualitas_Part" || $cpar->kategori == "Kualitas_Fungsi" || $cpar->kategori == "Kualitas_Luka" || $cpar->kategori == "Kualitas_Recheck") && ($user == $cpar->pelapor || Auth::user()->role_code == "MIS"))
   <a class="btn btn-sm btn-success pull-right" data-toggle="tooltip" title="Send Email" onclick="sendemailqa({{ $cpar->id }})" style="width:200px">Send Email</a>

   @elseif($cpar->posisi != "sl" && ($user == $cpar->pelapor || Auth::user()->role_code == "MIS"))
   <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
   @else
   
   @endif

   

   <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $cpar['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Form Report</a>
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary" style="background-color:#eeeeee">

    <form role="form">
      <!-- method="post" action="{{url('index/request_qa/update_action', $cpar->id)}}" -->
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="tgl">Tanggal</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" placeholder="" value="{{ date('d F Y', strtotime($cpar->tanggal)) }}" disabled>
              <input type="hidden" class="form-control pull-right" id="id_cpar" name="id_cpar" placeholder="" value="{{ $cpar->id }}">
            </div>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="subject">Identitas<span class="text-red">*</span></label>
            <input type="text" id="subject" class="form-control" value="{{$cpar->pelapor}} - {{$emp->name}}" readonly>
          </div>
        </div>

        <div  class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="subject">Kategori<span class="text-red">*</span></label>
            <!-- <select class="form-control select2" style="width: 100%;" id="cpar_kategori" name="cpar_kategori" data-placeholder="Pilih Kategori" disabled="">
                <option value="Critical" <?php if($cpar->kategori == "Critical") echo "selected"; ?>>Critical (Berhubungan Dengan Safety, Fungsi Dan Ketidaksesuaian Dimensi/Design)</option>
                <option value="Major" <?php if($cpar->kategori == "Major") echo "selected"; ?>>Major (Berhubungan Dengan Visual pada Area yang langsung dapat terlihat)</option>
                <option value="Minor" <?php if($cpar->kategori == "Minor") echo "selected"; ?>>Minor (Berhubungan Dengan Visual pada Area tidak langsung dapat terlihat)</option>
              </select> -->
              <input type="text" id="cpar_kategori" class="form-control" value="{{$cpar->kategori}}" readonly>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label for="subject">Judul Komplain<span class="text-red">*</span></label>
              <input type="text" class="form-control" name="cpar_judul" id="cpar_judul" placeholder="Judul Ketidaksesuaian" value="{{$cpar->judul}}" disabled="">
            </div>
          </div>

          <div class="row" align="left">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label for="section_from">Section From<span class="text-red">*</span></label>
              <select class="form-control select2" style="width: 100%;" id="cpar_secfrom" name="cpar_secfrom" data-placeholder="Pilih Section Pelapor" disabled="">
                <option></option>
                <?php 
                $secfrom = explode("_",$cpar->section_from);
                
                ?>
                @foreach($sections as $section)
                @if($section->group == null)
                @if($section->department == $secfrom[0] && $section->section == $secfrom[1])
                <option value="{{ $section->department }}_{{ $section->section }}" selected>{{ $section->department }} - {{ $section->section }}</option>
                @else
                <option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
                @endif
                @else
                @if($section->group == $secfrom[0] && $section->section == $secfrom[1])
                <option value="{{ $section->section }}_{{ $section->group }}" selected>{{ $section->section }} - {{ $section->group }}</option>
                @else
                <option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
                @endif
                @endif
                @endforeach
              </select>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <label for="section_to">Section To<span class="text-red">*</span></label>
              <select class="form-control select2" style="width: 100%;" id="cpar_secto" name="cpar_secto" data-placeholder="Pilih Section" disabled="">
                <option></option>
                <?php 
                $secfrom = explode("_",$cpar->section_to);
                
                ?>
                @foreach($sections as $section)
                @if($section->group == null)
                @if($section->department == $secfrom[0] && $section->section == $secfrom[1])
                <option value="{{ $section->department }}_{{ $section->section }}" selected>{{ $section->department }} - {{ $section->section }}</option>
                @else
                <option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
                @endif
                @else
                @if($section->group == $secfrom[0] && $section->section == $secfrom[1])
                <option value="{{ $section->section }}_{{ $section->group }}" selected>{{ $section->section }} - {{ $section->group }}</option>
                @else
                <option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
                @endif
                @endif
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
            <a data-toggle="modal" data-target="#createModal" class="btn btn-primary col-sm-12" style="width: 20%;color:white;font-weight: bold; font-size: 20px">Tambahkan Material</a>
            <br><br><br>
            <table id="example1" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>No Item</th>
                  <th>Material</th>
                  <th>Detail Ketidaksesuaian</th>
                  <th>Jumlah Cek</th>
                  <th>Jumlah NG</th>
                  <th>Presentase NG</th>
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

    <div class="row" style="margin-top: 10px">
      <div class="col-xs-12">
        <div class="box">
          <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/update_detail', $cpar->id)}}" enctype="multipart/form-data">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="form-group row" align="left">
                <label class="col-sm-12" style="font-size: 18px">Penanganan Oleh Section Pelapor</label>
                
                <div class="col-sm-6">
                  <label class="col-sm-12" style="font-size: 14px;padding-left: 0">
                    Target Per Hari
                  </label>  

                  <div class="col-sm-10" style="padding-left: 0">
                    <input type="text" class="form-control" name="target" placeholder="Masukkan Target Per Hari" value="{{ $cpar->target }}">
                  </div>
                  <div class="col-sm-2" style="padding-left: 0; vertical-align: middle;margin-top: 0.5vw">
                    Pcs / Hari
                  </div>

                  <label class="col-sm-12" style="font-size: 14px;padding-left: 0">
                    Jumlah Perkiraan Keterlambatan
                  </label> 

                  <div class="col-sm-10" style="padding-left: 0">
                    <input type="text" class="form-control" name="jumlah" placeholder="Masukkan Jumlah Perkiraan Keterlambatan" value="{{ $cpar->jumlah }}">
                  </div>
                  
                  <div class="col-sm-2" style="padding-left: 0; vertical-align: middle;margin-top: 0.5vw">
                    Pcs
                  </div> 

                  <label class="col-sm-12" style="font-size: 14px;padding-left: 0">
                    Waktu Yang Dibutuhkan Untuk Penanganan Masalah
                  </label>  

                  <div class="col-sm-10" style="padding-left: 0">
                    <input type="text" class="form-control" name="waktu" placeholder="Masukkan Waktu Yang Dibutuhkan" value="{{ $cpar->waktu }}">
                  </div>                 
                  <div class="col-sm-2" style="padding-left: 0; vertical-align: middle;margin-top: 0.5vw">
                    Menit
                  </div> 
                </div>

                <div class="col-sm-6">              
                  <label class="col-sm-12" style="font-size: 14px;padding-left: 0">Penanganan Oleh Section Terkait (Pelapor)</label>
                  <div class="col-sm-12" style="padding-left: 0">
                    <textarea type="text" class="form-control" name="aksi" placeholder="Masukkan Deskripsi"><?= $cpar->aksi ?></textarea>
                  </div>
                </div>

                <div class="col-sm-12"> 
                  <br>
                  <center>
                    <button type="submit" class="btn btn-success" style="width: 40%;font-size: 20px;font-weight: bold">Save Data</button>
                  </center>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 900px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel"><center>Detail Ketidaksesuaian</b></center></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">No Item<span class="text-red">*</span></label>
              <div class="col-sm-8">
                <select class="form-control select3" id="item" name="item" style="width: 100%;" data-placeholder="Pilih Item" required>
                  <option value=""></option>
                  @foreach($materials as $material)
                  <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row" align="left">
              <div class="col-sm-1"></div>
              <label class="col-sm-2">Nama Material<span class="text-red">*</span></label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="item_desc" placeholder="Nama Material" required readonly>
              </div>
            </div>
          <!-- <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Supplier</span></label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="supplier" style="width: 100%;" placeholder="Supplier" required>
            </div>
          </div> -->
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Detail Ketidaksesuaian<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea class="form-control" id="detail" placeholder="Detail Ketidaksesuaian" required></textarea>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Cek</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <input type="number" class="form-control" id="jumlah_cek" placeholder="Jumlah Cek" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah NG</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <input type="number" class="form-control" id="jumlah_ng" placeholder="Jumlah NG" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Presentase NG (Persen)</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" id="presentase_ng" placeholder="Presentase NG" disabled required>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Edit Detail Ketidaksesuaian</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">No Item<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <select class="form-control select4" id="item_edit" style="width: 100%;" data-placeholder="Pilih Material" required>
                <option value=""></option>
                @foreach($materials as $material)
                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Nama Material<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="item_desc_edit" placeholder="Nama Material" required readonly>
            </div>
          </div>
        <!-- <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Supplier</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="supplier_edit" style="width: 100%;" placeholder="Supplier" required>
          </div>
        </div> -->
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Detail Ketidaksesuaian<span class="text-red">*</span></label>
          <div class="col-sm-8" align="left">
            <textarea class="form-control" id="detail_edit" placeholder="Detail Ketidaksesuaian" required></textarea>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Jumlah Cek</span></label>
          <div class="col-sm-8">
            <div class="input-group">
              <input type="number" class="form-control" id="jumlah_cek_edit" placeholder="Jumlah Cek" onkeyup="getPersenEdit()" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>
        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Jumlah NG</span></label>
          <div class="col-sm-8" align="left">
            <div class="input-group">
              <input type="number" class="form-control" id="jumlah_ng_edit" placeholder="Jumlah NG" onkeyup="getPersenEdit()" required>
              <span class="input-group-addon">pc(s)</span>
            </div>
          </div>

        </div>
        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">Presentase NG</span></label>
          <div class="col-sm-8" align="left">
            <input type="number" class="form-control" id="presentase_ng_edit" placeholder="Presentase NG" disabled required>
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
</section>
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

    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');

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
        "url" : "{{ url("index/form_ketidaksesuaian/fetch_item",$cpar->id) }}"
      },
      "columns": [
      { "data": "item"},
      { "data": "item_desc"},
      // { "data": "supplier" },
      { "data": "detail" },
      { "data": "jml_cek" },
      { "data": "jml_ng"},
      { "data": "presentase_ng" },
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
    $("#item").change(function(){
      $.ajax({
        url: "{{ route('admin.getmaterialsbymaterialsnumber') }}?materials_number=" + $(this).val(),
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc').val(obj.material_description);
        }
      });
    });

    $("#item_edit").change(function(){
      $.ajax({
        url: "{{ route('admin.getmaterialsbymaterialsnumber') }}?materials_number=" + $(this).val(),
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc_edit').val(obj.material_description);
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

    function sendemail(id) {

      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim CPAR ini?")) {
        return false;
      }

      $("#loading").show();

      $.get('{{ url("index/form_ketidaksesuaian/sendemail/$cpar->id") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    function sendemailqa(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim CPAR ini ke QA?")) {
        return false;
      }

      $("#loading").show();

      $.get('{{ url("index/form_ketidaksesuaian/sendemailqa/$cpar->id") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    CKEDITOR.replace('detail' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('detail_edit' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('aksi' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function getPersen() {
      var def = document.getElementById("jumlah_ng").value;
      var samp = document.getElementById("jumlah_cek").value;
      var hasil = parseInt(def) / parseInt(samp) * 100;
      var hasil2 = parseFloat(Math.round(hasil * 100) / 100).toFixed(2);
      if (!isNaN(hasil)) {
       document.getElementById('presentase_ng').value = hasil2;
     }
   }

   function getPersenEdit() {
    var def = document.getElementById("jumlah_ng_edit").value;
    var samp = document.getElementById("jumlah_cek_edit").value;
    var hasiledit = parseInt(def) / parseInt(samp) * 100;
    var hasiledit2 = parseFloat(Math.round(hasiledit * 100) / 100).toFixed(2);
    if (!isNaN(hasiledit)) {
     document.getElementById('presentase_ng_edit').value = hasiledit2;
   }
 }

 function create() {

  var data = {
    id_cpar: $("#id_cpar").val(),
    item: $("#item").val(),
    item_desc: $("#item_desc").val(),
        // supplier : $("#supplier").val(),
        detail : CKEDITOR.instances.detail.getData(),
        jml_cek : $("#jumlah_cek").val(),
        jml_ng : $("#jumlah_ng").val(),
        presentase_ng : $("#presentase_ng").val()
      };

      // console.log(data);

      $.post('{{ url("index/form_ketidaksesuaian/create_item") }}', data, function(result, status, xhr){
        // console.log(result.status);
        if (result.status == true) {
          $('#example1').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","New item has been created.");
        } else {
          openErrorGritter("Error","item not created.");
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

    function modalEdit(id) {
      $('#EditModal').modal("show");
      var data = {
        id:id
      };
      
      $.get('{{ url("index/form_ketidaksesuaian/edit_item") }}', data, function(result, status, xhr){
        $("#id_edit").val(id);
        $("#item_edit").val(result.datas.item).trigger('change.select2');
        $("#item_desc_edit").val(result.datas.item_desc);
        // $("#supplier_edit").val(result.datas.supplier);
        $("#detail_edit").html(CKEDITOR.instances.detail_edit.setData(result.datas.detail));
        $("#jumlah_cek_edit").val(result.datas.jml_cek);
        $("#jumlah_ng_edit").val(result.datas.jml_ng);
        $("#presentase_ng_edit").val(result.datas.presentase_ng);
      });
    }

    function edit() {
      var data = {
        id: $("#id_edit").val(),
        item: $("#item_edit").val(),
        item_desc: $("#item_desc_edit").val(),
        // supplier: $("#supplier_edit").val(),
        detail: CKEDITOR.instances.detail_edit.getData(),
        jml_cek: $("#jumlah_cek_edit").val(),
        jml_ng: $("#jumlah_ng_edit").val(),
        presentase_ng: $("#presentase_ng_edit").val()
      };

      $.post('{{ url("index/form_ketidaksesuaian/edit_item") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#example1').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Item has been edited.");
        } else {
          openErrorGritter("Error","Failed to edit Item.");
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

      $.post('{{ url("index/form_ketidaksesuaian/delete_item") }}', data, function(result, status, xhr){
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Delete Item");
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