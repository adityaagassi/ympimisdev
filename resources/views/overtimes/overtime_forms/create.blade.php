@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
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
    /*padding-left: 0;*/
    /*padding-right: 0;*/
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  #loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    Create Overtime Forms <span class="text-purple">Japanese</span>
  </h1>
  <ol class="breadcrumb">
{{--  <li>
<a href="{{ url("create/overtime/overtime_form")}}" class="btn btn-success btn-sm" style="color:white">Create {{ $page }}</a>
</li> --}}
</ol>
</section>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <div class="col-xs-5">
            <form class="form-horizontal">
              <div class="form-group">
                <label for="ot_id" class="col-sm-3 control-label">Overtime ID</label>
                <div class="col-sm-9">
                  <input style="text-align: center; font-size: 22px;" type="text" class="form-control" id="ot_id" value="{{ $ot_id }}" readonly> 
                </div>
              </div>
              <div class="form-group">
                <label for="section" class="col-sm-3 control-label">Section</label>
                <div class="col-sm-9">
                  <select id="section" class="form-control select2" style="width: 100%;" data-placeholder="Select a Section">
                    <option></option>
                    @foreach($sections as $section)
                    <option value="{{ $section->status }}">{{ $section->child_code }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="sub_section" class="col-sm-3 control-label">Sub Section</label>
                <div class="col-sm-9">
                  <select id="sub_section" class="form-control select2" style="width: 100%;" data-placeholder="Select a Sub Section">
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="group" class="col-sm-3 control-label">Group</label>
                <div class="col-sm-9">
                  <select id="group" class="form-control select2" style="width: 100%;" data-placeholder="Select a Group">
                    <option></option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Overtime Date</label>
                    <div class="input-group date">
                      <input type="text" class="form-control" id="ot_date" placeholder="Overtime Date">
                    </div>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>From</label>
                    <div class="input-group date">
                      <input style="text-align: center;" type="text" id="ot_from" class="form-control timepicker" value="00:00">
                    </div>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>To</label>
                    <div class="input-group date">
                      <input style="text-align: center;" type="text" id="ot_to" class="form-control timepicker" value="00:00">
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Day</label>
                    <select class="form-control select2" style="width: 100%;" id="ot_day">
                      @foreach($day_statuses as $day_status)
                      <option value="{{ $day_status }}">{{ $day_status }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>Shift</label>
                    <select class="form-control select2" style="width: 100%;" id="ot_shift">
                      @foreach($shifts as $shift)
                      <option value="{{ $shift }}">{{ $shift }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Transport</label>
                    <select class="form-control select2" style="width: 100%;" id="ot_transport">
                      @foreach($transports as $transport)
                      <option value="{{ $transport }}">{{ $transport }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <center>
                      <label>Food</label>
                      <div class="input-group">
                        <input type="checkbox" class="minimal" id="ot_food">
                      </div>
                    </center>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <center>
                      <label>Extra Food</label>
                      <div class="input-group date">
                        <input type="checkbox" class="minimal" id="ot_extra_food">
                      </div>
                    </center>
                  </div>
                </div>
                <div class="col-xs-2 offset-xs-1">
                  <center>
                    <label>Lembur Awal</label>
                    <div class="input-group date">
                      <label><input type="radio" class="minimal" id="awal" name="lembur"></label>
                    </div>
                  </center>
                </div>
                <div class="col-xs-2">
                  <center>
                    <label>Lembur Akhir</label>
                    <div class="input-group date">
                      <label><input type="radio" class="minimal" id="akhir" name="lembur"></label>
                    </div>
                  </center>
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-5">
                  <div class="form-group">
                    <label>Purpose (Problem)</label>
                    <select class="form-control select2" style="width: 100%;" id="ot_purpose">
                      <option></option>
                      @foreach($purposes as $purpose)
                      <option value="{{ $purpose->purpose }}">{{ $purpose->purpose }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-xs-7">
                  <label>Remark</label>
                  <div class="form-group">
                    <textarea class="form-control" id="ot_remark" placeholder="Enter Remarks"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <hr style="border: 1px solid rgba(126,86,134,.7);">
            </div>
            <div class="col-xs-12">
              <div class="input-group col-md-8 col-md-offset-2" style="margin-bottom: 10px;">
                <div style="border-color: rgba(126,86,134,.7);"  class="input-group-addon" id="icon-serial" style="font-weight: bold">
                  <i class="glyphicon glyphicon-barcode"></i>
                </div>
                <input type="text" style="text-align: center; font-size: 22; border-color: rgba(126,86,134,.7);" class="form-control" id="ot_employee_id" placeholder="Scan Employee ID Here..." required>
                <div style="border-color: rgba(126,86,134,.7);"  class="input-group-addon" id="icon-serial">
                  <i class="glyphicon glyphicon-ok"></i>
                </div>
              </div>
              <table id="poListTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                <thead style="background-color: rgba(126,86,134,.7);">
                 <tr>
                  <th style="width: 5%;">Emp. ID</th>
                  <th style="width: 17%;">Name</th>
                  <th style="width: 6%;">From</th>
                  <th style="width: 6%;">To</th>
                  <th style="width: 1%;">Hour(s)</th>
                  <th style="width: 10%;">Transport</th>
                  <th style="width: 3%;">Food</th>
                  <th style="width: 3%;">Extra Food</th>
                  <th style="width: 12%;">Purpose</th>
                  <th>Remark</th>
                  <th>First</th>
                  <th>Last</th>
                  <th style="width: 3%">Action</th>
                </tr>
              </thead>
              <tbody id="tableBody">
              </tbody>
            </table>
            <button  type="button" onclick="save_print()" class="btn btn-primary btn-lg col-lg-12" id="ot_save"><i class="fa fa-print"></i> Save &  Print</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

  var counter = 1;
  var count = 0;
  arrNik = [];
  
  jQuery(document).ready(function() {
    $('#ot_save').hide();
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2();
    $('#sub_section').prop('disabled', true);
    $('#group').prop('disabled', true);

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue'
    });

    $('input[type="radio"].minimal').iCheck({
      radioClass   : 'iradio_minimal-blue'
    })

    $('#ot_date').datepicker({
      autoclose: true,
      format: "dd-mm-yyyy",
      todayHighlight: true,
    });

    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  $('#ot_employee_id').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#ot_employee_id").val().length > 7){
        if($('#section').val() != '' && $('sub_section').val() != '' && $('ot_date').val() != '' && $('ot_from').val() != '' && $('ot_to').val() != '' && $('ot_purpose').val() != '' && $('[name=lembur]').is(':checked')){
          scanEmployeeId($('#ot_employee_id').val());
          return false;
        }
        else{
          openErrorGritter('Error!', 'There is parameters that should be filled.');
          audio_error.play();
          $("#ot_employee_id").val("");
        }
      }
      else{
        openErrorGritter('Error!', 'Employee ID Invalid');
        audio_error.play();
        $("#ot_employee_id").val("");
      }
    }
  });

  $('#section').change(function(){
    var parent = $('#section').val();
    $('#group').empty();
    $('#group').prop('disabled', true);
    var data = {
      parent:parent,
      remark:'sub_section'
    }
    $.get('{{ url("select/overtime/division_hierarchy") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          $('#sub_section').empty();
          var selectData = '';
          $.each(result.hierarchies, function(key, value) {
            selectData += '<option value="' + value.status + '">' + value.child_code + '</option>';
          });
          $('#sub_section').append(selectData);
          $('#sub_section').prop('disabled', false);
        }
        else{
          audio_error.play();
          openErrorGritter('Error!', result.message);
        }
      }
      else{
        audio_error.play();
        alert('Disconnected from server.')
      }
    });
  });

  $('#sub_section').change(function(){
    var parent = $('#sub_section').val();
    var data = {
      parent:parent,
      remark:'group'
    }
    $.get('{{ url("select/overtime/division_hierarchy") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          $('#group').empty();
          var selectData = '';
          $.each(result.hierarchies, function(key, value) {
            selectData += '<option value="' + value.status + '">' + value.child_code + '</option>';
          });
          $('#group').append(selectData);
          $('#group').prop('disabled', false);
        }
        else{
          audio_error.play();
          openErrorGritter('Error!', result.message);
        }
      }
      else{
        audio_error.play();
        alert('Disconnected from server.')
      }
    });
  });

  function scanEmployeeId(employee_id){
    ot_from = $('#ot_from').val();
    ot_to = $('#ot_to').val();
    ot_transport = $('#ot_transport').val();
    ot_purpose = $('#ot_purpose').val();
    ot_remark = $('#ot_remark').val();
    var data = {
      employee_id:employee_id
    }
    $.get('{{ url("fetch/overtime/employee") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          for (var z = 0; z < arrNik.length; z++) {
            if (arrNik[z] == result.employee.employee_id) {
              openErrorGritter("Error!", "Employee Already Inserted");
              return false;
            }
          }

          arrNik.push(result.employee.employee_id);
          console.log(arrNik);


          var tableBody = "";
          tableBody += '<tr id="'+counter+'">';
          tableBody += '<td><p id="ot_employee_id'+counter+'">' + result.employee.employee_id + '</p></td>';
          tableBody += '<td><p id="ot_employee_name'+counter+'">' + result.employee.name + '</p></td>';
          tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_from'+counter+'" onchange="hour_to('+counter+')" class="form-control timepicker" value="' + ot_from + '"></td>';
          tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to'+counter+'" onchange="hour_to('+counter+')" class="form-control timepicker" value="' + ot_to + '"></td>';
          tableBody += '<td><p id="ot_hour'+counter+'"></p></td>';
          tableBody += '<td>';
          tableBody += '<select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;" id="ot_transport'+counter+'">';
          $.each(result.transports, function(key, value) {
            if(value == ot_transport){
              tableBody += '<option selected>' + value + '</option>';
            }
            else{
              tableBody += '<option>' + value + '</option>';
            }
          });
          tableBody += '</select>';
          tableBody += '</td>';
          tableBody += '<td><center><input type="checkbox" class="minimal" id="ot_food'+counter+'"></center></td>';
          tableBody += '<td><center><input type="checkbox" class="minimal" id="ot_extra_food'+counter+'"></center></td>';
          tableBody += '<td>';
          tableBody += '<select id="ot_purpose'+counter+'" class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;">';
          $.each(result.purposes, function(key, value) {
            if(value.purpose == ot_purpose){
              tableBody += '<option selected>' + value.purpose + '</option>';
            }
            else{
              tableBody += '<option>' + value.purpose + '</option>';
            }
          });
          tableBody += '</select>';
          tableBody += '</td>';
          tableBody += '<td><textarea class="form-control" rows="1" style="height: 22px; padding-bottom: 0; padding-top: 0;" placeholder="Enter Remarks" id="ot_remark'+counter+'">' + ot_remark + '</textarea></td>';
          tableBody += '<td><label><input type="radio" class="minimal" id="awal'+counter+'" value="first" name="lembur'+counter+'"></label></td>';
          tableBody += '<td><label><input type="radio" class="minimal" id="akhir'+counter+'" value="last" name="lembur'+counter+'"></label></td>';
          tableBody += '<td><button class="btn btn-xs btn-danger" onclick="deleteRow(this)" id="ot_delete'+counter+'"><i class="fa fa-minus"></i></button></td>';
          tableBody += '</tr>';

          $('#tableBody').append(tableBody).find('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
            interval: 30,
          });;

          $('#ot_employee_id').val('');
          $('#ot_employee_id').focus();
          openSuccessGritter('Success!', result.employee.employee_id + ' added.');
          $('#ot_save').show();

          $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
          });

          $('input[type="radio"].minimal').iCheck({
            radioClass: 'iradio_minimal-blue'
          });

          if ($('#ot_food').is(':checked')){
            $('#ot_food'+counter).iCheck('check');
          }

          if ($('#ot_extra_food').is(':checked')){
            $('#ot_extra_food'+counter).iCheck('check');
          }

          if ($('#awal').is(':checked')){
            $('#awal'+counter).iCheck('check');
          }

          if ($('#akhir').is(':checked')){
            $('#akhir'+counter).iCheck('check');
          }


          counter++;
          // $('#totalsemua').text("Total : "+count);
        }
        else{
          audio_error.play();
          openErrorGritter('Error!', result.message);
        }
      }
      else{
        audio_error.play();
        alert('Disconnected from server.')
      }
    });
}

function hour_to(id)
{
  var sampai1 = $('#ot_to'+id).val();
  var dari1 = $('#ot_from'+id).val();
  var sampaipost ="";
  var daripost ="";
  if (sampai1.split(":")[0]=="0") {
    sampaipost = "24:"+sampai1.split(":")[0];
  }else{
    sampaipost = sampai1;
  }

  if (dari1.split(":")[0]=="23") {
    daripost = "0:"+dari1.split(":")[0];
  }else{
    daripost = dari1;
  }

  var tgl = $('#ot_date').val();
  var shift = $("#ot_shift").find(':selected')[0].value;


  jam = sampai1.split(":")[0] - dari1.split(":")[0];
  menit = sampai1.split(":")[1] - dari1.split(":")[1];

  menit = menit.toString().length<2?'0'+menit:menit;
  if (menit<0){
    jam--;
    menit = 60 + menit;        
  }

  jam = jam.toString().length<2?'0'+jam:jam;
  if( jam < 0){
    ab = jam + 24;
  }else
  {
    ab = jam ;
  }


    //$('#hour'+id).text(ab+"."+menit);


    $.ajax({
      type: 'POST',
      url: '{{ url("fetch/overtime/break") }}',
      data: {
        'tgl': tgl,
        'from': daripost,
        'to': sampaipost,
        'shift': shift
      },
      success: function (data) {

        var istirahat = data.break.istirahat;
        var jam2 = ab+"."+menit;

        var jamasli = (jam2.split(".")[0]*60)*60;
        var menitasli = jam2.split(".")[1]*60;

        var  jamtotal = jamasli + menitasli;
        var  jamfix = jamtotal - istirahat;
        var  jamsatuan = secondsTimeSpanToHMS(jamfix);

        var jamsatuanfix = jamsatuan.split(":")[0];
        var menitsatuanfix = jamsatuan.split(":")[1];

        if (menitsatuanfix >= 0 && menitsatuanfix < 16){
          menitsatuanfix = 0;
        }else if (menitsatuanfix >= 16 && menitsatuanfix <= 45){
          menitsatuanfix = 5;
        }else{
          menitsatuanfix = 0;
          jamsatuanfix=parseInt(jamsatuanfix)+1;
        }

        var jamsatuanfix2 = jamsatuanfix+"."+menitsatuanfix



        $('#ot_hour'+id).text(jamsatuanfix2);
        // var sampai = $('#sampai').val();

      }
    });

  }

  function save_print() {
    var ot_id = document.getElementById('ot_id').value;
    var tgl = document.getElementById('ot_date').value;    
    var sec = $('#section').find(':selected')[0].text;
    var subsec = $('#sub_section').find(':selected')[0].text;

    if($('#group').val() != null){
      var group = $('#group').find(':selected')[0].text;
    }
    else{
      var group = '';
    }
    // var shift = document.getElementById('ot_shift').value;
    var hari = document.getElementById('ot_day').value;
    arrId = [];
    arrFood = [];
    arrEFood = [];
    arrTransport = [];
    arrStartTime = [];
    arrEndTime = [];
    arrHour = [];
    arrPurpose = [];
    arrRemark = [];
    arrStatus = [];

    var data = {
      ot_id:ot_id,
      ot_date:tgl,
      ot_day:hari,
      section:sec,
      sub_section:subsec,
      group:group
    }

    for (var i = 1; i < counter; i++) {
      arrId.push($('#ot_employee_id'+i).text());
      arrStartTime.push($('#ot_from'+i).val());
      arrEndTime.push($('#ot_to'+i).val());
      arrHour.push($('#ot_hour'+i).text());
      arrTransport.push($("#ot_transport"+i).find(':selected')[0].value);
      arrStatus.push($("input[name='lembur"+i+"']:checked").val());

      if ($('#ot_food'+i).is(':checked'))
        arrFood.push('1');
      else
        arrFood.push('0');

      if ($('#ot_extra_food'+i).is(':checked'))
        arrEFood.push('1');
      else
        arrEFood.push('0');

      arrPurpose.push($("#ot_purpose"+i).find(':selected')[0].value);
      arrRemark.push($('#ot_remark'+i).val());

    }

    var data_details = {
      ot_id:ot_id,
      emp_ids:arrId,
      ot_starts:arrStartTime,
      ot_ends:arrEndTime,
      ot_hours:arrHour,
      ot_transports:arrTransport,
      ot_foods:arrFood,
      ot_efoods:arrEFood,
      ot_porposes:arrPurpose,
      ot_statuses:arrStatus,
      ot_remarks:arrRemark,
    }

    console.log(data_details);

    $.post('{{ url("save/overtime") }}', data, function(result, status, xhr){
      if(xhr.status == 200) {
        $.post('{{ url("save/overtime_detail") }}', data_details, function(result, status, xhr){
          if(xhr.status == 200) {
            openSuccessGritter('Success', 'Data Saved');
            window.open('{{ url("index/overtime/print") }}' , '_blank');
          }
        })
      }
    })
  }


  function deleteRow(elem) {

    var ids = $(elem).parent('td').parent('tr').attr('id');

    var oldid = ids;
    count-=1;
    if (count ==0){
      arrNik = [];
    }

    var removed = arrNik.splice(parseInt(ids) - 1,1);
    console.log(arrNik);
    $(elem).parent('td').parent('tr').remove();

    var newid = parseInt(ids) + 1;
    jQuery("#"+newid).attr("id",oldid);
    jQuery("#ot_employee_id"+newid).attr("id","ot_employee_id"+oldid);
    jQuery("#ot_employee_name"+newid).attr("id","ot_employee_name"+oldid);
    jQuery("#ot_from"+newid).attr("id","ot_from"+oldid);
    jQuery("#ot_to"+newid).attr("id","ot_to"+oldid);
    jQuery("#ot_hour"+newid).attr("id","ot_hour"+oldid);
    jQuery("#ot_transport"+newid).attr("id","ot_transport"+oldid);
    jQuery("#ot_purpose"+newid).attr("id","ot_purpose"+oldid);
    jQuery("#ot_remark"+newid).attr("id","ot_remark"+oldid);
    // jQuery("#exfood"+newid).attr("id","exfood"+oldid);
    jQuery("#ot_delete"+newid).attr("id","ot_delete"+oldid);

      // console.log(no);

      // $('#totalsemua').text("Total : "+nomorali);
      
      counter-=1;
      var z = counter - 1;

      for (var i =  ids; i <= z; i++) { 
        var newid = parseInt(i)  + 1;
        var oldid = newid - 1;
        jQuery("#"+newid).attr("id",oldid);
        jQuery("#ot_employee_id"+newid).attr("id","ot_employee_id"+oldid);
        jQuery("#ot_employee_name"+newid).attr("id","ot_employee_name"+oldid);
        jQuery("#ot_from"+newid).attr("id","ot_from"+oldid);
        jQuery("#ot_to"+newid).attr("id","ot_to"+oldid);
        jQuery("#ot_hour"+newid).attr("id","ot_hour"+oldid);
        jQuery("#ot_transport"+newid).attr("id","ot_transport"+oldid);
        jQuery("#ot_purpose"+newid).attr("id","ot_purpose"+oldid);
        jQuery("#ot_remark"+newid).attr("id","ot_remark"+oldid);
        // jQuery("#exfood"+newid).attr("id","exfood"+oldid);
        jQuery("#ot_delete"+newid).attr("id","ot_delete"+oldid);
      }

      if(arrNik.length == 0) {
        $("#ot_save").hide();
      }

    }

    function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
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

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '2000'
    });
  }

</script>
@endsection