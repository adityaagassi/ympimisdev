@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
  thead>tr>th{
    text-align:center;
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    text-align: center;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  /*.dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
    }*/
  </style>
  @stop
  @section('header')
  <section class="content-header">
    <h1>
      {{ $title }}
      <small><span class="text-purple"> {{ $title_jp }}</span></small>
    </h1>
  </section>
  @stop
  @section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-solid">
          <div class="box-body">
            <!-- <h1>Utility</h1> -->

            <div class="col-md-4">
              <div class="box box-primary box-solid">
                <div class="box-body">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Mulai Dari</label>
                      <div class="input-group date" style="width: 100%;">
                        <input type="text" placeholder="Pilih Tanggal" class="form-control datepicker" name="reqFrom" id="reqFrom">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Mulai Sampai</label>
                      <div class="input-group date" style="width: 100%;">
                        <input type="text" placeholder="Pilih Tanggal" class="form-control datepicker" name="reqTo" id="reqTo">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-8">
              <div class="box box-primary box-solid">
                <div class="box-body">

                  <div class="col-xs-4">
                    <div class="form-group">
                      <label>Lokasi</label>
                      <select class="form-control select3" id="location_filter" style="width: 100%" data-placeholder="Pilih Lokasi Mesin">
                        <option value=""></option>
                        @foreach($location as $loc)
                        <option value="{{ $loc->location }}">{{ $loc->location }}</option>
                        @endforeach
                      </select>                      
                    </div>
                  </div>

                  <div class="col-xs-5">
                    <div class="form-group">
                      <label>Mesin</label>
                      <select class="form-control select3" id="machineName" style="width: 100%" data-placeholder="Pilih Mesin">
                        <option value=""></option>
                        @foreach($machine as $mac)
                        <option value="{{ $mac->machine_id }}">{{ $mac->machine_id }} - {{ $mac->description }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-xs-12">
              <button class="btn btn-success" data-toggle="modal" data-target="#createModal"><i class="fa fa-plus"></i>&nbsp; Tambah</button>
              <button class="btn btn-primary pull-right" onclick="loadTable()"><i class="fa fa-search"></i>&nbsp; Cari</button><br><br>
            </div>

            <div class="col-md-12">
              <table class="table table-bordered" id="table_history">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th width="3%">No.</th>
                    <th>Nama Mesin</th>
                    <th>Lokasi</th>
                    <th width="5%">Mulai</th>
                    <th width="5%">Selesai</th>
                    <th>Kerusakan</th>
                    <th>Penanganan</th>
                    <th>Pencegahan</th>
                    <th>Part</th>
                  </tr>
                </thead>
                <tbody id="body_history"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="createModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <div class="col-xs-12" style="background-color: #3c8dbc;">
              <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Tambah History Mesin</h1>
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <!-- <div class="col-xs-12">
                tes
              </div>
            </div> -->
            <div class="col-xs-12">
              <div class="col-xs-3" align="right" >
                <span style="font-weight: bold; font-size: 16px;">Lokasi <span class="text-red">*</span></span>
              </div>
              <div class="col-xs-9">
                <select class="form-control select2" data-placeholder="Pilih Lokasi Mesin" id="location" style="width: 100%" onchange="getMachine(this)">
                  <option value=""></option>
                  @foreach($location as $loc)
                  <option value="{{ $loc->location }}">{{ $loc->location }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12">

              <div class="col-xs-3" align="right" >
                <span style="font-weight: bold; font-size: 16px;">Nama Mesin <span class="text-red">*</span></span>
              </div>
              <div class="col-xs-9">
                <select class="form-control select2" data-placeholder="Pilih Mesin" id="mesin" style="width: 100%">
                  <option value=""></option>
                </select>
              </div>
            </div>

            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Waktu Mulai <span class="text-red">*</span></span>
                </div>
                <div class="col-xs-6">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" placeholder="Pilih Tanggal" class="form-control datepicker" id="tgl_mulai">
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" placeholder="Pilih Jam" class="form-control timepicker" id="jam_mulai">
                  </div>
                </div>
              </center>
            </div>


            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Waktu Selesai <span class="text-red">*</span></span>
                </div>
                <div class="col-xs-6">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" placeholder="Pilih Tanggal" class="form-control datepicker" id="tgl_selesai">
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" placeholder="Pilih Jam" class="form-control timepicker" id="jam_selesai">
                  </div>
                </div>
              </center>
            </div>

            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Kerusakan <span class="text-red">*</span></span>
                </div>
                <div class="col-xs-9">
                  <textarea placeholder="Isikan Detail Kerusakan" class="form-control" id="kerusakan"></textarea>
                </div>
              </center>
            </div>

            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Penanganan <span class="text-red">*</span></span>
                </div>
                <div class="col-xs-9">
                  <textarea placeholder="Isikan Detail Penanganan" class="form-control" id="penanganan"></textarea>
                </div>
              </center>
            </div>

            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Pencegahan <span class="text-red">*</span></span>
                </div>
                <div class="col-xs-9">
                  <textarea placeholder="Isikan Detail Pencegahan" class="form-control" id="pencegahan"></textarea>
                </div>
              </center>
            </div>

            <div class="col-xs-12">
              <center>
                <div class="col-xs-3" align="right" >
                  <span style="font-weight: bold; font-size: 16px;">Part</span>
                </div>
                <div class="col-xs-9">
                  <input type="text" placeholder="Pilih Part" class="form-control" id="part">
                </div>
              </center>
            </div>

            <div class="col-xs-12">
              <br>
              <button class="btn btn-success pull-right" onclick="simpan()"><i class="fa fa-check"></i>&nbsp; Simpan</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var machine_arr = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true
    });

    $('.timepicker').timepicker({
      use24hours: true,
      showInputs: false,
      showMeridian: false,
      minuteStep: 1,
      defaultTime: '00:00',
      timeFormat: 'hh:mm'
    })

    $('.select2').select2({
      dropdownParent: $("#createModal")
    });

    $('.select3').select2({
      allowClear: true
    });

    machine_arr = <?php echo json_encode($machine); ?>;

    loadTable();
  });

  function loadTable() {
    var data = {
      reqFrom : $("#reqFrom").val(),
      reqTo : $("#reqTo").val(),
      machineName : $("#machineName").val(),
      location_filter : $("#location_filter").val()
    }

    $.get('{{ url("fetch/maintenance/machine/history") }}', data, function(result, status, xhr) {
      $('#table_history').DataTable().clear();
      $('#table_history').DataTable().destroy();
      $("#body_history").empty();
      var body = "";

      $(result.logs).each(function(index, value) {
        body += "<tr>";
        body += "<td>"+(index+1)+"</td>";
        body += "<td>"+value.machine_name+"</td>";
        body += "<td>"+value.location+"</td>";
        body += "<td>"+value.started_time+"</td>";
        body += "<td>"+value.finished_time+"</td>";
        body += "<td>"+value.defect+"</td>";
        body += "<td>"+value.handling+"</td>";
        body += "<td>"+(value.prevention || '')+"</td>";
        body += "<td>"+(value.part || '')+"</td>";
        body += "</tr>";
      })
      $("#body_history").append(body);

      var table = $('#table_history').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
        "order": [[ 0, 'asc' ]]
      });
    })

  }

  function getMachine(elem) {
    var params = $(elem).val();
    $("#mesin").empty();

    machine = "<option value=''></option>";

    $(machine_arr).each(function(index, value) {
      if (value.location == params) {
        machine += "<option value='"+value.machine_id+"'>"+value.machine_id+" - "+value.description+"</option>";
      }
    })

    $("#mesin").append(machine);
  }

  function simpan() {
    var data = {
      id_mesin : $("#mesin").val(),
      nama_mesin : $("#mesin option:selected").text(),
      lokasi : $("#location").val(),
      mulai : $("#tgl_mulai").val()+" "+$("#jam_mulai").val(),
      selesai : $("#tgl_selesai").val()+" "+$("#jam_selesai").val(),
      kerusakan : $("#kerusakan").val(),
      penanganan : $("#penanganan").val(),
      pencegahan : $("#pencegahan").val(),
      part : $("#part").val()
    }

    if ($("#kerusakan").val() == "" || $("#penanganan").val() == "" || $("#mesin").val() == "" ||
      $("#location").val() == "" || $("#tgl_mulai").val() == "" || $("#jam_mulai").val() == "" || $("#tgl_selesai").val() == "" || 
      $("#jam_selesai").val() == "") {
      openErrorGritter("Gagal", "Terdapat Kolom yang belum diisi");
    return false;
  }

  $.post('{{ url("post/maintenance/machine/history") }}', data, function(result, status, xhr) {
    if (result.status) {
      openSuccessGritter("Success", "Berhasil Menambahkan Data");

      $("#createModal").modal('hide');
      loadTable();
    }
  })
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
@endsection