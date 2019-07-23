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
    Edit Overtime Forms <span class="text-purple">Japanese</span>
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
                  <input style="text-align: center; font-size: 22px;" type="text" class="form-control" id="ot_id" value="{{ $datas[0]->overtime_id }}" readonly> 
                </div>
              </div>
              <div class="form-group">
                <label for="section" class="col-sm-3 control-label">Section</label>
                <div class="col-sm-9">
                  <input type="tex" value="{{$datas[0]->section}}" id="section" class="form-control" style="width: 100%;" data-placeholder="Select a Section" disabled>
                </div>
              </div>
              <div class="form-group">
                <label for="sub_section" class="col-sm-3 control-label">Sub Section</label>
                <div class="col-sm-9">
                  <input type="tex" value="{{$datas[0]->subsection}}" id="sub_section" class="form-control" style="width: 100%;" data-placeholder="Select a SubSection" disabled>
                </div>
              </div>
              <div class="form-group">
                <label for="group" class="col-sm-3 control-label">Group</label>
                <div class="col-sm-9">
                  <input type="tex" value="{{$datas[0]->subsection}}" id="group" class="form-control" style="width: 100%;" data-placeholder="Select a Group" disabled>
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Overtime Date</label>
                    <div class="input-group date">
                      <input type="text" class="form-control" id="ot_date" placeholder="Overtime Date" value="{{$datas[0]->overtime_date}}" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>From</label>
                    <div class="input-group date">
                      <input style="text-align: center;" type="text" id="ot_from" class="form-control" value="{{$datas[0]->start_time}}" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>To</label>
                    <div class="input-group">
                      <input style="text-align: center;" type="text" id="ot_to" class="form-control" value="{{$datas[0]->end_time}}" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Day</label>
                    <input type="tex" value="{{$datas[0]->day_status}}" id="ot_day" class="form-control" style="width: 100%;" disabled>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <label>Shift</label>
                    <input type="tex" value="{{$datas[0]->shift}}" id="ot_shift" class="form-control" style="width: 100%;" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>Transport</label>
                    <input type="tex" value="-" id="ot_transport" class="form-control" style="width: 100%;" disabled>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <center>
                      <label>Food</label>
                      <div class="input-group">
                        <input type="checkbox" class="minimal" id="ot_food" disabled>
                      </div>
                    </center>
                  </div>
                </div>
                <div class="col-xs-2">
                  <div class="form-group">
                    <center>
                      <label>Extra Food</label>
                      <div class="input-group date">
                        <input type="checkbox" class="minimal" id="ot_extra_food" disabled>
                      </div>
                    </center>
                  </div>
                </div>
                <div class="col-xs-2 offset-xs-1">
                  <center>
                    <label>Lembur Awal</label>
                    <div class="input-group date">
                      <label><input type="radio" class="minimal" id="awal" name="lembur" disabled></label>
                    </div>
                  </center>
                </div>
                <div class="col-xs-2">
                  <center>
                    <label>Lembur Akhir</label>
                    <div class="input-group date">
                      <label><input type="radio" class="minimal" id="akhir" name="lembur" disabled></label>
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
                    <input type="tex" value="{{$datas[0]->purpose}}" id="ot_purpose" class="form-control" style="width: 100%;" disabled>
                  </div>
                </div>
                <div class="col-xs-7">
                  <label>Note</label>
                  <div class="form-group">
                    <textarea class="form-control" id="ot_remark" placeholder="Enter Remarks" disabled>{{$datas[0]->remark}}</textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <hr style="border: 1px solid rgba(126,86,134,.7);">
            </div>
            <div class="col-xs-12">
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
                  <th>Notes</th>
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
  arrContent = [];
  transports = [];
  purposes = [];
  var shift;
  var arrContent = []
  
  jQuery(document).ready(function() {
    arrContent = <?php echo json_encode($datas); ?>;

    $('#ot_save').hide();
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2();

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

    drawTable();
    console.log(arrContent);
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function drawTable() {
    $("#tableBody").html("");

    var tableBody = "";
    // var counter = 1;

    $.each(arrContent, function(key, value) {
      shift = value.shift;
      var food = "", ext_food = "", first = "", last = "";   
      // alert(counter);
      tableBody += '<tr id="'+counter+'">';
      tableBody += '<td><p id="ot_employee_id'+counter+'">' + value.employee_id + '</p></td>';
      tableBody += '<td><p id="ot_employee_name'+counter+'">' + value.name + '</p></td>';
      tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_from'+counter+'" onchange="hour_to('+counter+','+value.shift+')" class="form-control timepicker" value="' + value.start_time + '"></td>';
      tableBody += '<td><input style="text-align: center; padding-top: 0; padding-bottom: 0; height: 22px;" type="text" id="ot_to'+counter+'" onchange="hour_to('+counter+','+value.shift+')" class="form-control timepicker" value="' + value.end_time + '"></td>';
      tableBody += '<td><p id="ot_hour'+counter+'"></p></td>';
      tableBody += '<td>';
      tableBody += '<select class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;" id="ot_transport'+counter+'">';

      <?php foreach ($transports as $trans) { ?>
        var ot_transport = '<?php echo $trans; ?>';
        if (value.transport == ot_transport) {
          tableBody += '<option selected>' + ot_transport + '</option>';
        } else {
          tableBody += '<option>' + ot_transport + '</option>';
        }
        <?php } ?>

        tableBody += '</select>';
        tableBody += '</td>';

        if (value.food == 1) {
          food = "checked";
        }

        tableBody += '<td><center><input type="checkbox" class="minimal" '+food+' id="ot_food'+counter+'"></center></td>';

        if (value.ext_food == 1) {
          ext_food = "checked";
        }

        tableBody += '<td><center><input type="checkbox" '+ext_food+' class="minimal" id="ot_extra_food'+counter+'"></center></td>';
        tableBody += '<td>';
        tableBody += '<select id="ot_purpose'+counter+'" class="form-control" style="width: 100%; height: 22px; padding-top: 0; padding-bottom: 0;">';

        <?php foreach ($purposes as $purpose) { ?>
          var ot_purpose = '<?php print_r($purpose->purpose); ?>';
          if (value.purpose == ot_purpose) {
            tableBody += '<option selected>' + ot_purpose + '</option>';
          } else {
            tableBody += '<option>' + ot_purpose + '</option>';
          }
          <?php } ?>

          tableBody += '</select>';
          tableBody += '</td>';
          tableBody += '<td><textarea class="form-control" rows="1" style="height: 22px; padding-bottom: 0; padding-top: 0;" placeholder="Enter Remarks" id="ot_remark'+counter+'">' + value.remark + '</textarea></td>';

          if (value.ot_status == "first") {
            first = "checked";
          } else if(value.ot_status == "last") {
            last = "checked";
          }

          tableBody += '<td><label><input type="radio" class="minimal" id="awal'+counter+'" value="first" name="lembur'+counter+'" '+first+'></label></td>';
          tableBody += '<td><label><input type="radio" class="minimal" id="akhir'+counter+'" value="last" name="lembur'+counter+'" '+last+'></label></td>';


          tableBody += '<td><button class="btn btn-xs btn-danger" onclick="deleteRow(this)" id="ot_delete'+counter+'"><i class="fa fa-minus"></i></button></td>';
          tableBody += '</tr>';

          counter++;

          arrNik.push(value.employee_id);
        });

    $('#tableBody').append(tableBody).find('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      interval: 30,
    });

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue'
    }); 

    $('input[type="radio"].minimal').iCheck({
      radioClass: 'iradio_minimal-blue'
    });

    $('#ot_save').show();
  }

  function hour_to(id, shift)
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
      ot_purposes:arrPurpose,
      ot_statuses:arrStatus,
      ot_remarks:arrRemark,
    }

    $.post('{{ url("edit/overtime_detail") }}', data_details, function(result, status, xhr){
      if(xhr.status == 200) {
        openSuccessGritter('Success', 'Data Changed');
        var wndw = '{{ url("index/overtime/print/") }}/'+ot_id;
        window.open(wndw , '_blank');
      }
    })

    console.log(data_details);
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

    document.getElementById('ot_from'+newid).setAttribute('onchange','hour_to('+oldid+','+shift+');');
    jQuery("#ot_from"+newid).attr("id","ot_from"+oldid);

    document.getElementById('ot_to'+newid).setAttribute('onchange','hour_to('+oldid+','+shift+');');
    jQuery("#ot_to"+newid).attr("id","ot_to"+oldid);

    jQuery("#ot_hour"+newid).attr("id","ot_hour"+oldid);
    jQuery("#ot_transport"+newid).attr("id","ot_transport"+oldid);
    jQuery("#ot_food"+newid).attr("id","ot_food"+oldid);
    jQuery("#ot_extra_food"+newid).attr("id","ot_extra_food"+oldid);
    jQuery("#ot_purpose"+newid).attr("id","ot_purpose"+oldid);
    jQuery("#ot_remark"+newid).attr("id","ot_remark"+oldid);
    jQuery("#awal"+newid).attr("id","awal"+oldid).attr('name', 'lembur'+oldid);
    jQuery("#akhir"+newid).attr("id","akhir"+oldid).attr('name', 'lembur'+oldid);
    // jQuery("#exfood"+newid).attr("id","exfood"+oldid);
    jQuery("#ot_delete"+newid).attr("id","ot_delete"+oldid);

      // console.log(no);

      // $('#totalsemua').text("Total : "+nomorali);
      
      counter-=1;
      var z = counter - 1;

      for (var i = ids; i <= z; i++) { 
        var newid = parseInt(i)  + 1;
        var oldid = newid - 1;
        jQuery("#"+newid).attr("id",oldid);
        jQuery("#ot_employee_id"+newid).attr("id","ot_employee_id"+oldid);
        jQuery("#ot_employee_name"+newid).attr("id","ot_employee_name"+oldid);

        jQuery("#ot_from"+newid).attr("id","ot_from"+oldid);
        jQuery("#ot_to"+newid).attr("id","ot_to"+oldid);

        jQuery("#ot_hour"+newid).attr("id","ot_hour"+oldid);
        jQuery("#ot_transport"+newid).attr("id","ot_transport"+oldid);
        jQuery("#ot_food"+newid).attr("id","ot_food"+oldid);
        jQuery("#ot_extra_food"+newid).attr("id","ot_extra_food"+oldid);
        jQuery("#ot_purpose"+newid).attr("id","ot_purpose"+oldid);
        jQuery("#ot_remark"+newid).attr("id","ot_remark"+oldid);
        jQuery("#awal"+newid).attr("id","awal"+oldid).attr('name', 'lembur'+oldid);
        jQuery("#akhir"+newid).attr("id","akhir"+oldid).attr('name', 'lembur'+oldid);
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