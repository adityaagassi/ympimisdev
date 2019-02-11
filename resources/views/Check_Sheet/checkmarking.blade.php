@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style >
.select2-results__option[aria-disabled=true] {
  color: red; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    MARKING {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa  fa-paper-plane-o"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">CONSIGNEE & ADDRESS</span>          
          <span class="info-box-number">{{$time->destination}}</span>
        </div>
      </div>
    </div>


    <div class="col-md-3 ">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">SIHHPED FROM  </span>
          <span class="info-box-number">{{$time->shipped_from}}</span>
          <span class="info-box-text">SIHHPED TO  </span>
          <span class="info-box-number">{{$time->shipped_to}}</span>
          <span class="info-box-text">cARRIER  </span>
          <span class="info-box-number">{{$time->shipmentcondition->shipment_condition_name}}</span>
          <span class="info-box-text">ON OR ABOUT  </span>
          <span class="info-box-number">{{date('d-M-Y', strtotime($time->etd_sub))}}</span>
        </div>
      </div>
    </div>

    <div class="col-md-3 ">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">INVOICE NO  </span>
          <span class="info-box-number">{{$time->invoice}}</span>
          <span class="info-box-text">DATE   </span>
          <span class="info-box-number">{{date('d-M-Y', strtotime($time->Stuffing_date))}}</span>
          <span class="info-box-text">PAYMENT  </span>
          <span class="info-box-number">{{$time->payment}}</span>
          <span class="info-box-text">SHIPPER  </span>
          <span class="info-box-number">PT YMPI</span>
        </div>
      </div>
    </div>


  </div>

  
    <div class="nav-tabs-custom">
      <!-- Tabs within a box -->
      <ul class="nav nav-tabs pull-left bg-aqua">
        <li class="active"><a href="#cargo" data-toggle="tab"><i class="fa fa-folder"></i><b> CONDITION OF CARGO</b></a></li>
        <li><a href="#container" data-toggle="tab"><i class="fa fa-folder"></i><b> CONDITION OF CONTAINER</b></a></li>
      <!--   <li><a href="#inspection" data-toggle="tab"><i class="fa fa-folder"></i><b> CONTAINER INSPECTION</b></a></li>
       -->
      </ul>
      <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="cargo" style="position: relative; ">
          <div class="box-body">
            <div class="table-responsive">
              <table class="table no-margin table-bordered table-striped">
                <thead>
                  <tr>
                    <th>DEST</th>
                    <th>INVOICE</th>
                    <th>GMC</th>
                    <th>DESCRIPTION OF GOODS</th>
                    <th>MARKING NO.</th>
                    <th colspan="2">PACKAGE</th>
                    <th colspan="2">QUANTITY</th>
                    <th colspan="2">Check</th>
                    <th>Total</th>
                    <th>Confirm</th>
                    <th colspan="2">Diff</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                   @foreach($detail as $nomor => $detail)

                   <td>{{$detail->destination}}</td>
                   <td>{{$detail->invoice}}</td>
                   <td>{{$detail->gmc}}</td>
                   <td>{{$detail->goods}}</td>
                   <td ><p id="marking{{$nomor + 1}}">{{$detail->marking}}</p></td>
                   <td class="{{$detail->package_set}}">{{$detail->package_qty}}</td>
                   <td>{{$detail->package_set}}</td>
                   <td class="{{$detail->qty_set}}">{{$detail->qty_qty}}</td>
                   <td>{{$detail->qty_set}} </td>
                   <td><button class="btn btn-block btn-primary btn-sm" id="likes{{$nomor + 1}}" onclick="check({{$nomor + 1}}); hide({{$nomor + 1}});" style="display: none;"> Checks</button>
                    <button class="btn btn-block btn-primary btn-sm" id="like{{$nomor + 1}}" onclick="addMarking({{$nomor + 1}});" style="display: block;"> Check</button><br>
                     <select id="theSelect{{$nomor + 1}}" onchange="check({{$nomor + 1}}); hide({{$nomor + 1}});" class="form-control select2">
                     </select>
                   </td>
                   <td><button class="btn btn-block btn-warning btn-sm" id="likeun{{$nomor + 1}}" onclick="uncheck({{$nomor + 1}}) ; hide({{$nomor + 1}});" disabled> Uncheck</button><br>
                    <select id="theSelectun{{$nomor + 1}}" onchange="uncheck({{$nomor + 1}});hide({{$nomor + 1}});" class="form-control select2">
                      <option value="" >Uncheck</option>
                    </select>
                  </td>
                  <td class="{{$detail->package_set}}"><p id="total{{$nomor + 1}}">{{$detail->package_qty}}</p></td>
                  <td><p id="confirm{{$nomor + 1}}">0</p></td>
                  <td><p id="diff{{$nomor + 1}}"></p>
                  </td>
                  <td>
                    <span data-toggle="tooltip"  class="badge bg-green" id="y{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw fa-check"></i></span>
                    <span data-toggle="tooltip"  class="badge bg-red" id="n{{$nomor + 1}}" style="display: none;"><i class="fa fa-fw  fa-close"></i></span></td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5" rowspan="2"> <CENTER>REMAIN PALLET & CTN</CENTER></th>                    
                  <th><p id="plte"></p></th>
                  <th>PLT</th>
                  <th><p id="sete"></p></th>
                  <th>SET</th>
                </tr>
                <tr>

                  <th><p id="ctne"></p></th>
                  <th>CTN</th>
                  <th><p id="pcse"></p></th>
                  <th>PCS</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>

     <!--  <div class="chart tab-pane" id="inspection" style="position: relative;">

        <div class="box-body">
          <div class="table-responsive">
           @php
           $p = 'images/7poin.jpg';
           @endphp
           <img src="{{ url($p) }}" class="user-image" alt="7 Poin" align="middle">
         </div>
       </div>

     </div> -->
     
     <div class="chart tab-pane" id="container" style="position: relative;">

      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>AREA OF INSPECTION</th>
                <th >ACCEPTABLE</th>
                <th >REMARK</th>
              </tr>
            </thead>
            <tbody>
              @foreach($container as $nomor => $container)
              <tr>
                <td>{{$container->area}}</td>
                <td ><input type="checkbox" checked data-toggle="toggle" data-on="GOOD" data-off="NOT GOOD" data-onstyle="success" data-offstyle="danger" data-width="100"></td>
                <td> <TEXTAREA id="remark{{$nomor + 1}}" ></TEXTAREA></td>
                  <input type="text" id="count" value="{{$loop->count}}" hidden></input>
                  <td id="rows{{$nomor + 1}}" hidden><img src="{{ url($p) }}" class="user-image" alt="7 Poin" align="middle" width="500"></td>
                @endforeach
              </tr>

            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.box-header -->

<!-- /.box -->

</div>
<!-- /.col -->

<!-- Tabs within a box -->

@endsection

@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>

<script >

  jQuery(document).ready(function() {
    $('.select2').select2({
    dropdownAutoWidth : true,
    width: 'auto'
    });
    $('#rows1').removeAttr('hidden');
    var count = document.getElementById("count").value;
    document.getElementById("rows1").rowSpan = count;
    var plt = 0;
    var ctn = 0;
    var set = 0;
    var pcs = 0;
    $(".PLT").each(function() {
      plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
    });
    $('#plte').html("" + plt);

    $(".CTN").each(function() {
      ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
    });
    $('#ctne').html("" + ctn);

    $(".SET").each(function() {
      set += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
    });
    $('#sete').html("" + set);

    $(".PCS").each(function() {
      pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
    });
    $('#pcse').html("" + pcs);
  });
  
  // function add(id){

  //   var a = id;
  //   var i =parseInt(document.getElementById('inc'+a).value);
  //   var aa = i+1;
  //   document.getElementById('inc'+a).value = aa;

  // }

  // function minus(id){

  //   var a = id;
  //   var i =parseInt(document.getElementById('inc'+a).value);
  //   var aa = i-1;
  //   document.getElementById('inc'+a).value = aa;

  // }

  // function minusdata(id){
  //   var a = id;
  //   var total =parseInt(document.getElementById('total'+a).value);
  //   var confirm =parseInt(document.getElementById('inc'+a).value);
  //   var aa = confirm - total ;
  //   var aaa= 0;
  //   if(aa > 0){
  //     aaa = " + " + aa;
  //   }else{
  //     aaa = " " + aa;
  //   }
  //   document.getElementById('diff'+a).value = aaa;
  // }

  function hide(id){
   var a = id;
   var confirm =parseInt(document.getElementById("diff"+a).innerHTML);
   var y = document.getElementById("y"+a);
   var n = document.getElementById("n"+a);
   if (confirm == 0) {
    y.style.display = "block";
    n.style.display = "none";
  } else {
    y.style.display = "none";
    n.style.display = "block";
  }

}


function addMarking(id){
  var a = id;
  var confirm =document.getElementById('marking'+a).innerHTML;
  var lowEnd = Number(confirm.split('-')[0]);
  var highEnd = Number(confirm.split('-')[1]);
  var list = [];
  var options = ['<option>Checked</option>'];
  var jum = document.getElementById("theSelectun"+a).length;  
  if (jum == 1){
    $("#likeun"+a).prop("disabled", true);
  }else{
    $("#likeun"+a).prop("disabled", false);
  }
  for (var i = lowEnd; i <= highEnd; i++) {
    list.push(i);
    options.push('<option id="checked', i, '" value="', i, '"  >',i, '</option>');
  }
  $("#theSelect"+a).html(options.join(''));
  $("#like"+a).attr("disabled", true);  
  // document.getElementById("likes"+a).style.display="block";
  // document.getElementById("like"+a).style.display="none";
  
} 

function check(id){
  var a = id;
  var options = ['<option>Unchecked</option>'];
  var value = $('#theSelect'+a).val();
  var jum = document.getElementById("theSelectun"+a).length;  
  var diff = parseInt(document.getElementById("total"+a).innerHTML);
  if (jum  > 1){
    $("#likeun"+a).prop("disabled", true);
  }else{
    $("#likeun"+a).prop("disabled", false);
  }
  if (value === '') return;
  var option = $("option[value='" + value + "']", '#theSelect'+a);
  option.attr("disabled","disabled");
  $('#theSelectun'+a).append('<option value="'+value+'" selected="selected">'+value+'</option>');
  document.getElementById('confirm'+a).innerHTML = jum ;
  document.getElementById('diff'+a).innerHTML =  jum - diff ;

  var $disabledResults = $("#theSelect"+a);
  $disabledResults.select2({
    dropdownAutoWidth : true,
    width: 'auto'
    });
  // document.getElementById("likes"+a).style.display="block";
  // document.getElementById("like"+a).style.display="none";
}

function uncheck(id){
  var a = id;
  var value = $('#theSelectun'+a).val();
  var jum = document.getElementById("theSelectun"+a).length;
  var diff = parseInt(document.getElementById("total"+a).innerHTML);
  if (jum > 1){
    $("#likeun"+a).prop("disabled", true);
  }else{
    $("#likeun"+a).prop("disabled", false);
  }
  $('#theSelectun'+a).find('[value="'+value+'"]').remove();
  $('#theSelect'+a).find('[value="'+value+'"]').removeAttr('disabled');
  document.getElementById('confirm'+a).innerHTML = jum -2;
  document.getElementById('diff'+a).innerHTML =  (jum - 2) - diff ;

   var $disabledResults = $("#theSelect"+a);
  $disabledResults.select2({
    dropdownAutoWidth : true,
    width: 'auto'
    });
  // document.getElementById("likes"+a).style.display="block";
  // document.getElementById("like"+a).style.display="none";
}

</script>
@stop