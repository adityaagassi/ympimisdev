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
                  <input style="text-align: center; font-size: 22px;" type="text" class="form-control" id="ot_id" value="{{ $ot_id }}" disabled> 
                </div>
              </div>
              <div class="form-group">
                <label for="section" class="col-sm-3 control-label">Section</label>
                <div class="col-sm-9">
                  <select id="section" class="form-control select2" style="width: 100%;" data-placeholder="Select a Section">
                    <option></option>
                    @foreach($sections as $section)
                    <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
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
                    <label for="exampleInputEmail1">Date</label>
                    <div class="input-group date">
                      <input type="text" class="form-control" id="ot_date" placeholder="Date">
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
                      <option value="-">-</option>
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
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-1">
                  <div class="form-group">
                    <label>Remark</label>
                  </div>
                </div>
                <div class="col-xs-11">
                  <div class="form-group">
                    <textarea class="form-control" rows="2" id="ot_remark" placeholder="Enter Remarks"></textarea>
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
                  <th style="width: 20%;">Name</th>
                  <th style="width: 6%;">From</th>
                  <th style="width: 6%;">To</th>
                  <th style="width: 1%;">Hour(s)</th>
                  <th style="width: 10%;">Transport</th>
                  <th style="width: 3%;">Food</th>
                  <th style="width: 3%;">Extra Food</th>
                  <th style="width: 15%;">Purpose</th>
                  <th>Remark</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr>
                  <td>W00000000</td>
                  <td>WWWWWW WWWWWW</td>
                  <td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to" class="form-control timepicker"></td>
                  <td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to" class="form-control timepicker"></td>
                  <td>00</td>
                  <td>
                    <select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;" id="transport">
                      <option value="-">-</option>
                      <option value="bangil">Bangil</option>
                      <option value="pasuruan">Pasuruan</option>
                    </select>
                  </td>
                  <td><input type="checkbox" class="minimal" id="extra_food"></td>
                  <td><input type="checkbox" class="minimal" id="extra_food"></td>
                  <td>
                   <select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;" id="transport">
                    <option value="bangil">WWWWWWWWW</option>
                    <option value="pasuruan">WWWWWWWWW</option>
                  </select>
                </td>
                <td>
                  <textarea class="form-control" rows="1" style="height: 22px; padding-bottom: 0; padding-top: 0;" id="remark" placeholder="Enter Remarks"></textarea>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </form>
  </div>
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
  
  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2();
    $('#sub_section').prop('disabled', true);
    $('#group').prop('disabled', true);

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue'
    });

    $('#ot_date').datepicker({
      autoclose: true,
      todayHighlight: true
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
        if($('#section').val() != '' && $('sub_section').val() != '' && $('ot_date').val() != '' && $('ot_from').val() != '' && $('ot_to').val() != '' && $('ot_purpose').val() != ''){
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
    $('#group').html('');
    $('#group').prop('disabled', true);
    var data = {
      parent:parent
    }
    $.get('{{ url("select/overtime/division_hierarchy") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          $('#sub_section').html('');
          var selectData = '';
          $.each(result.hierarchies, function(key, value) {
            selectData += '<option value="' + value.child + '">' + value.child + '</option>';
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
      parent:parent
    }
    $.get('{{ url("select/overtime/division_hierarchy") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          $('#group').html('');
          var selectData = '';
          $.each(result.hierarchies, function(key, value) {
            selectData += '<option value="' + value.child + '">' + value.child + '</option>';
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
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          var tableBody = "";
          tableBody += '<tr>';
          tableBody += '<td>' + result.employee.employee_id + '</td>';
          tableBody += '<td>' + result.employee.name + '</td>';
          tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to" class="form-control timepicker" value="' + ot_from + '"></td>';
          tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to" class="form-control timepicker" value="' + ot_to + '"></td>';
          tableBody += '<td>0 jam</td>';
          tableBody += '<td>';
          tableBody += '<select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;">';
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
          tableBody += '<td>' + result.employee.employee_id + '</td>';
          tableBody += '<td>' + result.employee.employee_id + '</td>';
          tableBody += '<td>';
          tableBody += '<select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;">';
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
          tableBody += '<td><textarea class="form-control" rows="1" style="height: 22px; padding-bottom: 0; padding-top: 0;" placeholder="Enter Remarks">' + ot_remark + '</textarea></td>';
          tableBody += '</tr>';

          $('#tableBody').append(tableBody).find('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
            interval: 30,
          });;

          $('#ot_employee_id').val('');
          $('#ot_employee_id').focus();
          openSuccessGritter('Success!', result.employee.employee_id + 'added.');
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