@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <small>Create Form YMMJ</small>
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
    <form role="form" method="post" action="{{url('index/qa_ymmj/create_action')}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tgl kejadian<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="tgl_kejadian" name="tgl_kejadian" placeholder="Masukkan Tanggal kejadian" required>
            </div>
          </div>
          <label class="col-sm-1">Judul Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="judul_komplain" id="judul_komplain" placeholder="Judul / Subject Komplain" required="">
          </div>
        </div>

        <div class="form-group row" align="left">
          
          <label class="col-sm-1">Lokasi<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" style="width: 100%;" id="lokasi" name="lokasi" data-placeholder="Pilih Lokasi" required>
              <option></option>
              <option value='Assy SAX'>Assy SAX</option>
              <option value='Assy FL'>Assy FL</option>
              <option value='Assy CL'>Assy CL</option>
              <option value='Body Process'>Body Process</option>
              <option value='Buffing'>Buffing</option>
              <option value='CL Body'>CL Body</option>
              <option value='Lacquering'>Lacquering</optiofn>
              <option value='Part Process'>Part Process</option>
              <option value='Pianica'>Pianica</option>
              <option value='Plating'>Plating</option>
              <option value='Recorder'>Recorder</option>
              <option value='Sub Assy'>Sub Assy</option>
              <option value='Case KD'>Case KD</option>
              <option value='Venova'>Venova</option>
              <option value='Warehouse'>Warehouse</option>
              <option value='Welding'>Welding</option>
              <option value='Incoming Check QA'>Incoming Check QA</option>
              <option value='Other'>Other</option>
            </select>
          </div>

          <label class="col-sm-1">Nomor<span class="text-red">*</span></label>
         <div class="col-sm-5">
            <input type="text" class="form-control" name="nomor" id="nomor" placeholder="Nomor" required="">
          </div>
        </div>
        
        <div class="form-group row increment" align="left">
          <label class="col-sm-1">File</label>
          <div class="col-sm-5">
            <input type="file" name="files[]" multiple="">
          </div>
        </div>

        <div class="form-group row" align="left">
          <div class="col-sm-1"></div>
          <label class="col-sm-2">GMC<span class="text-red">*</span></label>
          <div class="col-sm-8">
            <select class="form-control select2" name="material_number" id="material_number" style="width: 100%;" data-placeholder="Pilih Material" required>
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
              <input type="text" class="form-control" name="material_description" id="material_description" placeholder="Material Description" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">No Invoice</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="No Invoice" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Cek</span></label>
            <div class="col-sm-8">
              <div class="input-group">
                <input type="number" class="form-control" id="sample_qty" name="sample_qty" placeholder="Jumlah Cek / Temuan" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Jumlah Defect</span></label>
            <div class="col-sm-8" align="left">
              <div class="input-group">
                <input type="number" class="form-control" id="defect_qty" name="defect_qty" placeholder="Jumlah Defect" onkeyup="getPersen()" required>
                <span class="input-group-addon">pc(s)</span>
              </div>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Presentase Defect (Persen)</label>
            <div class="col-sm-8" align="left">
              <input type="text" class="form-control" name="defect_presentase" id="defect_presentase" placeholder="Presentase Defect" required>
            </div>
          </div>
          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Detail Masalah<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea class="form-control" name="detail" id="detail_problem" placeholder="Detail Masalah" required></textarea>
            </div>
          </div>

          <div class="form-group row" align="left">
            <div class="col-sm-1"></div>
            <label class="col-sm-2">Penanganan Masalah</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="penanganan" name="penanganan" placeholder="Penanganan Masalah" required>
            </div>
          </div>

        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qa_ymmj') }}">Cancel</a>
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
    
    $('#tgl_kejadian').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true
    });

    $("#material_number").change(function(){
      $("#desc").show();
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

    $(function () {
      $('.select2').select2()
    })

    function getPersen() {
      var def = document.getElementById("defect_qty").value;
      var samp = document.getElementById("sample_qty").value;
      var hasil = parseInt(def) / parseInt(samp) * 100;
      var hasil2 = parseFloat(Math.round(hasil * 100) / 100).toFixed(2);
      if (!isNaN(hasil)) {
         document.getElementById('defect_presentase').value = hasil2;
      }

    }

    CKEDITOR.replace('detail_problem' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
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
        time: '3000'
      });
    }

  </script>
@stop

