@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
    background-color: #7e5686;
    color: #FFD700;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }
  #queueTable.dataTable {
    margin-top: 0px!important;
  }
  #loading, #error { display: none; }
  .description-block {
    margin-top: 0px
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
      </p>
    </div>
    <div class="col-xs-12">
      <h3 style="color: white; text-align: center">Daftar APAR Yang Akan Kadaluarsa <br>(使用期限が切れた消火器・消火栓の一覧)</h3><br>
    </div>
    <div class="col-xs-6">
      <button class="btn btn-default btn-lg pull-right" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id='f1' onclick="page(this.id)">Factory I</button>
    </div>
    <div class="col-xs-6">
      <button class="btn btn-default btn-lg" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id='f2' onclick="page(this.id)">Factory II</button>
    </div>
    <div class="col-xs-12" style="padding-top: 10px;" id="fact1">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th colspan="9" style="font-size: 20pt">Factory I</th>
          </tr>
          <tr>
            <th>APAR Code</th>
            <th>APAR Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Exp. Date</th>
            <th>Exp. Remaining</th>
            <th>PR Status</th>
          </tr>
        </thead>
        <tbody id="exp_f1">
        </tbody>
      </table>
    </div>

    <div class="col-xs-12" style="padding-top: 10px; display: none" id="fact2">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th colspan="9" style="font-size: 20pt">Factory II</th>
          </tr>
          <tr>            
            <th>APAR Code</th>
            <th>APAR Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Exp. Date</th>
            <th>Exp. Remaining</th>
            <th>PR Status</th>
          </tr>
        </thead>
        <tbody id="exp_f2">
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade in" id="modalDetail" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
            <h4 class="modal-title" id="modalTitle">APAR Detail</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group row">
                      <label class="col-xs-2" style="margin-top: 1%;">Code</label>
                      <div class="col-xs-5">
                        <input type="text" class="form-control" id="code" readonly>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xs-2" style="margin-top: 1%;">Name</label>
                      <div class="col-xs-5">
                        <input type="text" class="form-control" id="name" readonly>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xs-2" style="margin-top: 1%;">Location</label>
                      <div class="col-xs-10">
                        <input type="text" class="form-control" id="location" readonly>
                        <input type="hidden" id="ids">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xs-2" style="margin-top: 1%;">Capacity</label>
                      <div class="col-xs-5">
                        <div class="input-group">
                          <input type="text" class="form-control" id="capacity" readonly>
                          <span class="input-group-addon bg-purple">Kg</span>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-left" id="btn_save" onclick="ordering('order')"><i class="fa fa-check"></i> Order</button>
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade in" id="modalReady" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span></button>
              <h4 class="modal-title" id="modalTitle">APAR Detail</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-xs-12">
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="form-group row">
                        <label class="col-xs-2" style="margin-top: 1%;">Code</label>
                        <div class="col-xs-5">
                          <input type="text" class="form-control" id="ready_code" readonly>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-xs-2" style="margin-top: 1%;">Name</label>
                        <div class="col-xs-5">
                          <input type="text" class="form-control" id="ready_name" readonly>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-xs-2" style="margin-top: 1%;">Location</label>
                        <div class="col-xs-10">
                          <input type="text" class="form-control" id="ready_location" readonly>
                          <input type="hidden" id="ready_ids">
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-xs-2" style="margin-top: 1%;">Capacity</label>
                        <div class="col-xs-5">
                          <div class="input-group">
                            <input type="text" class="form-control" id="ready_capacity" readonly>
                            <span class="input-group-addon bg-purple">Kg</span>
                          </div>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="col-xs-2" style="margin-top: 1%;">Order Date</label>
                        <div class="col-xs-5">
                          <input type="text" class="form-control" id="ready_order" readonly>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success pull-left" onclick="ordering('ready')"><i class="fa fa-check"></i> Ready</button>
              <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endsection
    @section('scripts')
    <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
    <script src="{{ url("js/jsQR.js")}}"></script>
    <script>
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      var check = [];

      jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $('#check').children().hide();

        check = <?php echo json_encode($check_list); ?>;

        get_expire_data();
        $("#modalContent").hide();

      });

      $(function () {
        jQuery( ".datepicker" ).datepicker({autoclose: true, format: "yyyy-mm-dd" }).attr('readonly','readonly');
      })

      function get_expire_data() {
        var fi = "", fii = "";

        $("#exp_f1").empty();
        $("#exp_f2").empty();
        var data = {
          mon: '3'
        }

        $.get('{{ url("fetch/maintenance/apar/expire") }}', data, function(result, status, xhr) {
          $.each(result.expired_list, function(index, value){

           if (value.exp < 0) {
            color = '#fa6161';
          } else if(value.exp == 0) {
            color = '#f79b5e';
          } else if(value.exp == 1) {
            color = '#fcdc65';
          } else {
            color = '#fffdd1';
          }


          // if (value.order_status === null) {
          //   klik = 'onclick="openOrderModal(\''+value.id+'\', \''+value.utility_code+'\', \''+value.utility_name+'\', \''+value.type+'\', \''+value.capacity+'\', \''+value.group+'\', \''+value.exp_date+'\')"';
          // } else if(value.order_status == "Ordering") {
          //   klik = 'onclick="openReadyModal(\''+value.id+'\', \''+value.utility_code+'\', \''+value.utility_name+'\', \''+value.type+'\', \''+value.capacity+'\', \''+value.group+'\', \''+value.exp_date+'\', \''+value.order_date+'\')"';
          // } else {
            klik = '';
          // }

          if (value.location == "Factory I") {
            fi += "<tr style='background-color: "+color+";'>";
            fi += "<td>"+value.utility_code+"</td>";
            fi += "<td>"+value.utility_name+"</td>";
            fi += "<td>"+value.type+"</td>";
            fi += "<td>"+value.capacity+" Kg</td>";
            fi += "<td>"+value.group+"</td>";
            fi += "<td>"+value.exp_date+"</td>";
            fi += "<td>"+value.exp+" Month Left</td>";
            fi += "<td>"+(value.no_pr || '-')+"</td>";
            fi += "</tr>";
          } else {
            fii += "<tr style='background-color: "+color+"'>";
            fii += "<td>"+value.utility_code+"</td>";
            fii += "<td>"+value.utility_name+"</td>";
            fii += "<td>"+value.type+"</td>";
            fii += "<td>"+value.capacity+" Kg</td>";
            fii += "<td>"+value.group+"</td>";
            fii += "<td>"+value.exp_date+"</td>";
            fii += "<td>"+value.exp+" Month Left</td>";
            fii += "<td>"+(value.no_pr || '-')+"</td>";
            fii += "</tr>";
          }
        })

          $("#exp_f1").append(fi);
          $("#exp_f2").append(fii);
        })
      }

      function ordering(stat) {
        var data = {
          utility_id : $("#ids").val(),
          order_date : $("#ready_order").val(),
          param : stat
        }

        $.post('{{ url("post/maintenance/apar/order") }}', data, function(result, status, xhr) {
          if (result.status) {
            openSuccessGritter("Success", "APAR Successfully Ordered");
            $("#modalDetail").modal("hide");
            $("#modalReady").modal("hide");

          } else {
            openErrorGritter("Error", result.message);
          }

        })
      }

      function openOrderModal(id, code, name, type, capacity, location, exp) {
        $("#modalDetail").modal("show");

        $("#code").val(code);
        $("#name").val(name);
        $("#location").val(location);
        $("#type").val(type);
        $("#capacity").val(capacity);
        $("#ids").val(id);
      }

      function openReadyModal(id, code, name, type, capacity, location, exp, order_date) {
        $("#modalReady").modal("show");

        $("#ready_code").val(code);
        $("#ready_name").val(name);
        $("#ready_location").val(location);
        $("#ready_type").val(type);
        $("#ready_capacity").val(capacity);
        $("#ids").val(id);
        $("#ready_order").val(order_date);
      }

      function page(id) {
        if (id == "f1") {
          $("#fact1").show();
          $("#fact2").hide();
        } else {
          $("#fact2").show();
          $("#fact1").hide();
        }
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