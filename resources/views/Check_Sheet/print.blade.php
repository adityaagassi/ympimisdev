<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <style type="text/css">
table
{
    border-collapse: separate ;
}

table td.last, table th.last 
{
    border-right: 1px solid black;
    border-left: 1px solid black;
}

table tr.last td
{
    border-bottom: 1px solid black;
}


#isi{
  font-size:10px;
}
</style>
</head>
<body>

  <div class="col-xs-15">
    <BUTTON id="PRINT" onclick="printa();" style="display: block;" class="btn btn-primary btn-lg" style="color:white">PRINT</BUTTON>
    <DIV class="col-xs-14">
      <TABLE width="100%">
        <TR>
          <TD><B><H1>PT. YMPI</H1><B></TD>
            <TD>
              @php
              $p = 'images/aeo.png';
              @endphp
              <img src="{{ url($p) }}" class="user-image pull-right" alt="7 Poin" align="middle" width="100">
            </TD>
          </TR>
        </TABLE><br>
      </DIV >

      <div class="col-xs-15"> 
        <table border="1" width="100%">
          <THEAD>
            <th colspan="7" style="border: 1px solid"><center>CONTAINER & CARGO CHECK SHEET</center></th>
          </THEAD>
          <tbody>
            <tr>
              <td style="border:none" align="center"> CONSIGNEE & ADDRESS</td>
              <td align="RIGHT" style="border:none"> SHIPPED FROM</td>
              <td style="border:none">:</td>
              <td style="border:none"><b>{{$time->shipped_from}}</b></td>
              <td style="border:none">INVOICE NUMBER</td>
              <td style="border:none">:</td>
              <td style="border:none"><b>{{$time->invoice}}</b></td>
            </tr>
            <TR>
              <td rowspan="3" style="border:none" align="center"> <B>{{$time->destination}}</B></td>
              <td align="RIGHT" style="border:none"> TO</td>
              <td style="border:none">:</td>
              <td style="border:none"><b>{{$time->shipped_to}}</b></td>
              <td style="border:none">DATE</td>
              <td style="border:none">:</td>
              <td style="border:none"><b>{{date('d-M-Y', strtotime($time->Stuffing_date))}}</b></td>
            </TR>
            <tr>
              <td align="RIGHT" style="border:none"> CARRIER</td>
              <td style="border:none">:</td>
              <td style="border:none"><b>@if(isset($time->shipmentcondition->shipment_condition_name))
               {{$time->shipmentcondition->shipment_condition_name}}
               @else
               -
             @endif</b></td>
             <td style="border:none">PAYMENT</td>
             <td style="border:none">:</td>
             <td style="border:none"><b>{{$time->payment}}</b></td>
           </tr>
           <tr>
            <td align="RIGHT" style="border:none"> ON OR ABOUT</td>
            <td style="border:none">:</td>
            <td style="border:none"><b>{{date('d-M-Y', strtotime($time->etd_sub))}}</b></td>
            <td style="border:none">SHIPPER</td>
            <td style="border:none">:</td>
            <td style="border:none"><b>PT. YMPI</b></td>
          </tr>
          <tr>        
          </tr>
        </tbody>
      </table>
    </div>

    <DIV class="col-xs-15">
      <table border="1" width="100%" >
        <thead>
          <tr>
            <th colspan="60" style="border: 1px solid"><center>CONDITION OF CARGO</center></th>
          </tr>
          <tr id="cargo">
            <th style="border: 1px solid" ><center> DEST.</center></th>
            <th style="border: 1px solid"><center>INVOICE</center></th>
            <th style="border: 1px solid"><center>GMC</center></th>
            <th style="border: 1px solid" ><center>DESCRIPTION OF GOODS</center></th>
            <th style="border: 1px solid"><center>MARKING NO.</center></th>
            <th colspan="2" style="border: 1px solid"><center>PACKAGE</center></th>
            <th colspan="2" style="border: 1px solid"><center>QUANTITY</center></th>
            <th style="border: 1px solid" hidden>1</th>
          </tr>
        </thead>
        <tbody id="isi">
          @foreach($detail as $nomor => $detail)
          <input type="text" id="count" value="{{$loop->count}}" hidden></input>
          <TR id="cargo{{$nomor + 1}}">
            <TD style="border-right: 1px solid; border-left: 1px solid" align="center" width="3%"><center>{{$detail->destination}}</center></TD>
            <TD style="border-right: 1px solid; border-left: 1px solid" width="5%"><center >{{$detail->invoice}}</center></TD>
            <TD style="border-right: 1px solid; border-left: 1px solid" width="5%"><center >{{$detail->gmc}}</center></TD>
            <TD style="border-right: 1px solid; border-left: 1px solid" >{{$detail->goods}}</TD>
            <TD style="border-right: 1px solid; border-left: 1px solid" width="7%"><center>{{$detail->marking}}</center></TD>
            @if($detail->package_set =="PL")
            <td class="PLT" width="5%" style="border-right: 1px solid; border-left: 1px solid"align="center" >{{$detail->package_qty}}</td>
            @elseif($detail->package_set =="C/T")
            <td class="CTN" width="5%" style="border-right: 1px solid; border-left: 1px solid"align="center" >{{$detail->package_qty}}</td>
            @else
            <td class="{{$detail->package_qty}}" width="5%" style="border-right: 1px solid; border-left: 1px solid" align="center">{{$detail->package_qty}}</td>
            @endif
            {{-- <TD style="border-right: 1px solid; border-left: 1px solid" class="{{$detail->package_set}}" align="center">{{$detail->package_qty}}</center></TD> --}}
            <TD style="border-right: 1px solid; border-left: 1px solid" width="3%"><center>{{$detail->package_set}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" class="{{$detail->qty_set}}" align="center">{{$detail->qty_qty}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" width="3%"><center>{{$detail->qty_set}}</center></TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" hidden id="total{{$nomor + 1}}">{{$detail->confirm}}</TD>
            </TR>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" rowspan="2" style="border: 1px solid;"> <CENTER>REMAIN PALLET & C/T</CENTER></th>                    
              <th style="border-left: 1px solid; border-top: 1px solid"><center><b id="plte"></b></center></th>
              <th style="border-right: 1px solid; border-top: 1px solid"><center>PLT</center></th>
              <th style="border-left: 1px solid; border-top: 1px solid"><center><b id="sete"></center></b></th>
              <th style="border-right: 1px solid; border-top: 1px solid"><center>SET</center></th>
              <th style="border-right: 1px solid; border-bottom: 1px solid; border-top: 1px solid"" colspan="31" rowspan="2"></th>
            </tr>
            <tr>

              <th style="border-left: 1px solid; border-bottom: 1px solid"><center><b id="ctne"></center></b></th>
              <th style="border-right: 1px solid; border-bottom: 1px solid"><center>C/T</center></th>
              <th style="border-left: 1px solid; border-bottom: 1px solid"><center><b id="pcse"></center></b></th>
              <th style="border-right: 1px solid; border-bottom: 1px solid"><center>PC</center></th>

            </tr>
          </tfoot>
        </table>    
      </DIV>

      <div class="col-xs-15">
        <table BORDER="1"  width="100%">
          <thead>
            <TR>
              <th colspan="7" style="border: 1px solid"><CENTER><B>CONDITION OF CONTAINER</B></CENTER></th>
            </TR>
            <tr>
              <th style="border: 1px solid"> CONTAINER NO : {{$time->countainer_number}}</th>
              <th colspan="4" style="border: 1px solid"> SEAL NO : {{$time->seal_number}}</th>
              <th  style="border: 1px solid"> NO POL : {{$time->no_pol}}</th>
              <th> INSPECTOR : @if($time->status == 1) 
                @if(isset($time->user3->name))
                <!--  {{$time->created_by}} - --> {{$time->user3->name}}
                @else
                <!--  {{$time->created_by}} - --> Not registered
              @endif</th>
              @endif
            </tr>
            <tr>
              <th style="border: 1px solid">Area of Inspection :</th>
              <th colspan="4" style="border: 1px solid"><center>Acceptable</center></th>
              <th colspan="2" style="border: 1px solid">Remark / Keterangan</th>
            </tr>
          </thead>
          <TBODY>
           @foreach($container as $nomor => $container)
           <TR>
            <TD width="30%">{{$container->area}}</TD>
            <TD width="3%" style="border-right: none"><CENTER><p id="g{{$nomor +1}}" ></p> {{-- <p id="gg{{$nomor +1}}" >&#91; &#45; &#93;</p> --}}</CENTER></TD>
            <TD width="6%" style="border-left: none"><CENTER>GOOD</CENTER></TD>
            <TD width="3%" style="border-right: none"><CENTER><p id="ng{{$nomor +1}}" >&#91; &#10004; &#93;</p> {{-- <p id="ngg{{$nomor +1}}" >&#91; &#45; &#93;</p> --}}</CENTER></TD>
            <TD width="8%" style="border-left: none"><CENTER>NOT GOOD</CENTER></TD>
            <td colspan="2"> <p id="text{{$nomor +1}}"></p></td>

          </TR>
          @endforeach

        </TBODY>
      </table>
      <table width="100%" border="1">
        <thead>
          <th style="border:none">No Dok : YMPI/EXIM/FM/0008</th>
          <th style="border:none">Rev. : 00</th>
          <th style="border:none">Tanggal : 27/08/2018</th>
        </thead>
        <tbody>
          <tr>
            <td colspan="3" style="border:none">@php
            $p = 'images/7poin.png';
            @endphp
            <br><center> <img src="{{ url($p) }}" class="user-image" alt="7 Poin" align="middle" width="500"></center><br>
          </td>
        </tr>
        <tr>
          <td  colspan="3" style="border:none" class="text-right"><b> {{$time->id_checkSheet}}</b></td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
@foreach($inspection as $nomor => $inspection)
<p id="inspec1" hidden>{{$inspection->inspection1}}</p>
<p id="inspec2"hidden>{{$inspection->inspection2}}</p>
<p id="inspec3"hidden>{{$inspection->inspection3}}</p>
<p id="inspec4"hidden>{{$inspection->inspection4}}</p>
<p id="inspec5"hidden>{{$inspection->inspection5}}</p>
<p id="inspec6"hidden>{{$inspection->inspection6}}</p>
<p id="inspec7"hidden>{{$inspection->inspection7}}</p>
<p id="inspec8"hidden>{{$inspection->inspection8}}</p>
<p id="inspec9"hidden>{{$inspection->inspection9}}</p>

<p id="remark1" hidden>{{$inspection->remark1}}</p>
<p id="remark2"hidden>{{$inspection->remark2}}</p>
<p id="remark3"hidden>{{$inspection->remark3}}</p>
<p id="remark4"hidden>{{$inspection->remark4}}</p>
<p id="remark5"hidden>{{$inspection->remark5}}</p>
<p id="remark6"hidden>{{$inspection->remark6}}</p>
<p id="remark7"hidden>{{$inspection->remark7}}</p>
<p id="remark8"hidden>{{$inspection->remark8}}</p>
<p id="remark9"hidden>{{$inspection->remark9}}</p>
@endforeach


</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script >  
  jQuery(document).ready(function() {
    text1();
    hidden();
    myFunction();
    myFunction2();
    myFunction3();
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

    $(".PC").each(function() {
      pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
    });
    $('#pcse').html("" + pcs);    
  });

  function myFunction() {
    var row = document.getElementById("cargo");
    row.style.border="solid";
    var nomor = 0;
    for (i = 8; i < 38; i++) {
      nomor++;
      var x = row.insertCell(i);
      x.innerHTML = nomor;
      x.style.textAlign = "center";

    }
  }

  function myFunction3() {
    var count = document.getElementById("count").value
    var row = document.getElementById("cargo");
    row.style.border="solid";
    var nomor = "Total";
    var x = row.insertCell(38);
    x.innerHTML = "Total";
    for (i = 1; i <= count; i++) {
      var row = document.getElementById("cargo"+i);        
      var x = row.insertCell(40);
      var Cells = row.getElementsByTagName("td");
      var total = document.getElementById("total"+i).innerHTML;
      if (total == "0"){
        x.style.textAlign = "center";
        x.innerHTML = "&#10004;";
      }else{
        x.style.textAlign = "center";
        x.innerHTML = total;
      }        
    }

  }

  function myFunction2() {  
    var count = document.getElementById("count").value;         
    var nomor = 1;
    for (var z = nomor; z <= count; z++) {
      var s = 1; 
      for (i = 10; i < 40; i++) {
        var row = document.getElementById("cargo"+z);        
        var x = row.insertCell(i);
        var Cells = row.getElementsByTagName("td");
        if (s <= Cells[9].innerText) 
        x.innerHTML = "&#10004;";
        x.style.width ="15pt";
        x.style.textAlign = "center";
        s++;
      }

    }

  }



  function hidden(){
    var a=0;
    for (i = 1; i<=9;i++){
      a++;
      var text = document.getElementById("inspec"+a).innerHTML;
      if (text == 0){
        document.getElementById("g"+i).innerHTML ="[ - ]";
        document.getElementById("ng"+i).innerHTML ="[ "+"&#10004;"+" ]";
          // $("#g"+i).text("[ - ]");
          // $("#ng"+i).text("[ V ]");
          
        }else {
          // $("#g"+i).text("[ V ]");
          // $("#ng"+i).text("[ - ]");
          document.getElementById("g"+i).innerHTML ="[ "+"&#10004;"+" ]";
          document.getElementById("ng"+i).innerHTML ="[ - ]";
        }
        
      }
    }

    function text1(){
      var a=0;
      for (i = 1; i<=9;i++){
        a++;
        var text = document.getElementById("remark"+a).innerHTML;
        document.getElementById('text'+a).innerHTML = text;
        
      }
    }

    function printa(){
      document.getElementById('PRINT').style.display = 'none';
      window.print();
    }



  </script>

