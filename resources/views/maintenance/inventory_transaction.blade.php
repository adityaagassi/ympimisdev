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
    border:1px solid #ffd700;
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

  table.table > thead > tr > th{
    border:1px solid #ffd700;
    color: #ffd700; 
    background-color: #3c3c3c;
  }

  table.table > tbody > tr > td{
    border:1px solid #ffd700;
    background-color: #3c3c3c;
    color: white;
  }

  table.table > tbody > tr > td > input{
    color: black;
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
      <?php 
      if (Request::segment(4) == 'in')
        echo '<a class="btn btn-xs btn-danger" href="'.url("index/maintenance/inventory/out").'">Go to OUT</a>';
      else
        if ($permission == 1) 
          echo '<a class="btn btn-xs btn-success" href="'.url("index/maintenance/inventory/in").'">Go to IN</a>';
        else 
          echo '';
        ?>

        <center>
          <h2 style="color: white; font-weight: bold; margin-bottom: 0"><b style="color: #FFD700;">[</b> Maintenance <b style="color: #FFD700;">]</b></h2>
          <span style="color: #FFD700; font-size: 12pt;">Sparepart Transaction</span><br>
        </center>
      </div>
      <div class="row" style="padding: 0 20px 0 20px">
        <div class="col-xs-4" id="left">
          <div class="box box-solid" style="margin-top: 10px">
            <div class="box-header with-border" style="background-color: #1caf9a; color: white" id="head">
              <center><h4 class="box-title">Sparepart <b style="color: #FFD700" class="stat">IN</b></h4></center>
            </div>
            <div class="box-body" style="background-color: #3c3c3c; border: 1px solid #ffd700; border-top: none">
              <input type="text" class="form-control input-lg" placeholder="Barcode Here. . ." id="txt_code">
            </div>
          </div>

        </div>
        <div class="col-xs-8" id="tengh">
          <div class="box box-solid" style="margin-top: 10px">
            <div class="box-header with-border" style="background-color: #FFD700; border: 1px solid #FFD700;">
              <center><h4 class="box-title" style="font-size: 15px; color:  #3c3c3c"><b style="color:white" class="stat">IN</b> Sparepart List</h4></center>
            </div>
            <div class="box-body" style="padding: 0px;">
              <table class="table" style="width: 100%">
                <thead>
                  <tr>
                    <th width="10%">Sparepart Number</th>
                    <th>Sparepart Name</th>
                    <th width="3%">Stock</th>
                    <th width="5%">Qty</th>
                    <th width="5%"></th>
                  </tr>
                </thead>
                <tbody id="body_transaction">
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-6 pull-right">
            <br><br>
            <div class="col-xs-12" @if(Request::segment(4) == 'in') style="display: none" @endif>
              <center style="color: #FFD700">
                <label class="radio-list"><input type="radio" name="category" style="margin-right: 5px" value="SPK" checked>SPK</label>
                <label class="radio-list" style="margin-left: 50px"><input type="radio" name="category" style="margin-right: 5px" value="Planned Maintenance">Planned Maintenance</label>
              </center>
            </div>
            <div class="col-xs-12" @if(Request::segment(4) == 'in') style="display: none" @endif style="margin-top: 5px">
              <div class="row">
                <div class="col-xs-9">
                  <select class="form-control select2" id="spk" data-placeholder="Select Option">
                    <option></option>
                  </select>
                </div>
                <div class="col-xs-3">
                  <input type="text" class="form-control" placeholder="nomor mesin" id="no_mesin" style="display: none">
                </div>
              </div>
            </div>
            <div class="col-xs-12" style="margin-top: 5px">
              <button class="btn" style="width: 100%; background-color: #f75e4d; color: white" onclick="post('out')" id='btn_out'><i class="fa fa-arrow-up"></i>&nbsp;OUT</button>
              <button class="btn" style="width: 100%; background-color: #1caf9a; color: white" onclick="post('in')" id='btn_in'><i class="fa fa-arrow-down"></i>&nbsp;IN</button>
            </div>
            <div class="col-xs-6">
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

      scan_arr = [];

      jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        $(".select2").select2({
          tags: true,
          allowClear: true
        });

        if ("{{ Request::segment(4) }}" == 'in') {
          $("#head").css('background-color', '#1caf9a');
          $(".stat").text('IN');
          $("#btn_in").css('display','block');
          $("#btn_out").css('display','none');
        } else {
          $("#head").css('background-color', '#f75e4d');
          $(".stat").text('OUT');
          $("#btn_out").css('display','block');
          $("#btn_in").css('display','none');
        }

        $("#txt_code").focus();

        $("input[type=radio][name=category]").prop( "checked", false );

        $('input[type=radio][name=category]').change(function() {
          if (this.value == 'SPK') {
            getSPK();
            $("#no_mesin").val("");
            $("#no_mesin").hide();
          }
          else if (this.value == 'Planned Maintenance') {
            getMachine();
            $("#no_mesin").val("");
            $("#no_mesin").show();
          }
        });

      });

      $('#txt_code').keyup(function(e){
        if(e.keyCode == 13)
        {
          scan();
        }
      });

      function scan() {
        body = "";

        if ($("#txt_code").val() == "") {
          return false;
        }

        if (jQuery.inArray( $("#txt_code").val(), scan_arr ) != -1) {
        // console.log('Sudah Ada Dalam Array');
        openErrorGritter("Fail", "Parts Already Exist");
        return false
      }

      data = {
        code: $("#txt_code").val()
      }

      $.get('{{ url("fetch/maintenance/inven/code") }}', data, function(result, status, xhr){
        if (result.status) {
          if (result.datas) {
            scan_arr.push($("#txt_code").val());
            body += "<tr>";
            body += "<td id='num_"+result.datas.part_number+"'>"+result.datas.part_number+"</td>";
            body += "<td>"+result.datas.part_name+" ~ "+result.datas.specification+"</td>";
            body += "<td id='stock_"+result.datas.part_number+"'>"+result.datas.stock+"</td>";
            body += "<td><input type='text' value='1' maxlength='4' size='1' id='qty_"+result.datas.part_number+"' onkeypress='return isNumber(event)'></td>";
            body += "<td><button class='btn btn-xs btn-danger' onclick='delete_row(this)'><i class='fa fa-close'></i></button></td>";
            body += "</tr>";
          } else {
            openErrorGritter("Fail", "Parts not Registered");
          }
        } else {
          openErrorGritter("Error", result.message);
        }

        $("#body_transaction").append(body);

        $("#txt_code").val("");
      })
    }

    function delete_row(elem) {
      $(elem).parents("tr").remove();
      ido = $(elem).parent().parent().children().first().attr("id");

      scan_arr = $.grep(scan_arr, function(value) {
        return value != ido.split("_")[1];
      });
    }

    function post(stat) {
      var arr_post = [];
      var status = 1;

      $('#body_transaction').children('tr').each(function () {
       var prt_num = $(this).children().first().text();
       var qty = $("#qty_"+prt_num).val();

       var stock = $("#stock_"+prt_num).text();

       if (stat == 'out' && parseInt(qty) > parseInt(stock)) {
        openErrorGritter("Fail", "Stock to Low");
        status = 0;
      }

      arr_post.push([prt_num, qty]);
    });

      var ket2 = $("#spk").val();

      if (status == 0) 
        return false;

      // if (ket2 == null) {
      //   openErrorGritter("Fail", "All Field Must Be Filled");
      //   return false;
      // }

      // if ($('input[name=category]:checked').val() != "SPK") {
      //   if ($("#no_mesin").val() == "") {
      //     openErrorGritter("Fail", "All Field Must Be Filled");
      //     return false;
      //   }
      // }

      var data = {
        stat : stat,
        part : arr_post,
        ket : $('input[name=category]:checked').val(),
        ket2 : ket2+" - "+$("#no_mesin").val(),
      }

      $.post('{{ url("post/maintenance/inven/code") }}', data, function(result, status, xhr){
        if (result.status) {
          openSuccessGritter("Success", "Stock Has Been Updated");

          $("#body_transaction").empty();
          scan_arr = [];
        } else {
          openErrorGritter("Error", result.message);
        }
      })
    }

    function getSPK() {
      var option = "";
      var arr_option = [];

      var data = {
        remark : 4
      };

      $.get('{{ url("fetch/maintenance/list_spk") }}', data, function(result, status, xhr){
        $.each(result.tableData,function(index, value){
          arr_option.push(value.order_no);
        })
        var uniqueSPK = [];
        $.each(arr_option, function(i, el){
          if($.inArray(el, uniqueSPK) === -1) uniqueSPK.push(el);
        });

        $.each(uniqueSPK,function(index, value){
          option += "<option>"+value+"</option>";
        })
        $("#spk").empty();
        $("#spk").append(option);

      })
    }

    function getMachine() {
      var option = "";

      var data = {
        ctg : "machine"
      }

      $.get('{{ url("fetch/maintenance/list_pm") }}', data, function(result, status, xhr){
        $.each(result.datas,function(index, value){
          option += "<option>"+value.item_check+"</option>";
        })
        $("#spk").empty();
        $("#spk").append(option);
      })
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

    function isNumber(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      return true;
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