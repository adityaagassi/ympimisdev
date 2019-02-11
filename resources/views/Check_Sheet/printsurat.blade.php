<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <style type="text/css">
    @media print {
  body {
    background: none;
    -ms-zoom: 1.665;
  }
  div.portrait, div.landscape {
    margin: 0;
    padding: 0;
    border: none;
    background: none;
  }
  div.landscape {
    transform: rotate(270deg) translate(-276mm, 0);
    transform-origin: 0 0;
  }
}
  </style>
</head>
<body>
  <BUTTON id="PRINT" onclick="printa();" style="display: block;" class="btn btn-primary btn-lg" style="color:white">PRINT</BUTTON>
  <div class="col-xs-15">

    <DIV class="col-xs-14">
      <TABLE width="99%" >
        <TR>
          <TD ><B><H4>PT.YAMAHA MUSICAL PRODUCTS INDONESIA</H4></B> </TD>
            <TD>
              Pasuruan,{{date('d F Y', strtotime($time->created_at))}} 
            </TD>
          </TR>
          <tr>
            <td>Jl. Rembang Industri I/36</td>
            <td >Kepada :</td>
          </tr>
          <tr>
            <td> Kawasan Industri PIER Pasuruan</td>
            <td rowspan="3">YTH : </td>
          </tr>
          <tr>
            <td>Phone:(0343) 740290</td>
          </tr>
          <tr>
            <td>Fax: (0343)740291</td>
          </tr>
        </TABLE>
      </DIV ><br><br>
      <div class="col-xs-15"> 
        <table  width="99%">
          <THEAD>
            <th colspan="7" style="border: none"><h1><center>SURAT JALAN EKSPOR</center></h1></th>
          </THEAD>
          <tbody>
             <tr>
              <td style="border:none"> Surat Jalan No.:</td>              
            </tr>
            <tr>
              <td style="border:none"> Kendaraan / No.Pol.:</td>              
            </tr>
            <tr>
              <td style="border:none"> No.Container/Size : {{$time->countainer_number}}</td>              
            </tr>
            <tr>
            <td style="border:none">No. Segel :</td>        
            </tr>
          </tbody>
        </table>
      </div>

      <DIV class="col-xs-15">
        <table border="1" width="99%" >
          <thead>
          <tr id="cargo">
            <th style="border: 1px solid"><center>No</center></th>
            <th style="border: 1px solid"><center>No. Inv</center></th>
            <th style="border: 1px solid"><center>Kode</center></th>
            <th style="border: 1px solid"><center>Nama Barang</center></th>
            <th colspan="2" style="border: 1px solid"><center>PKG</center></th>
            <th colspan="2" style="border: 1px solid"><center>Jumlah</center></th>
            <th colspan="2" style="border: 1px solid"><center>Keterangan</center></th>
          </tr>
        </thead>
          <TBODY>
            @foreach($detail as $nomor => $detail)
            <input type="text" id="count" value="{{$loop->count}}" hidden></input>
            <TR id="cargo{{$nomor + 1}}">
              <TD style="border-right: 1px solid; border-left: 1px solid" align="center">{{$nomor + 1}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid">{{$detail->invoice}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid">{{$detail->gmc}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid">{{$detail->goods}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" class="{{$detail->package_set}}" align="RIGHT">{{$detail->package_qty}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid">{{$detail->package_set}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" class="{{$detail->qty_set}}" align="RIGHT">{{$detail->qty_qty}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid">{{$detail->qty_set}}</TD>
              <TD style="border-right: 1px solid; border-left: 1px solid" align="center"><p id="text{{$nomor + 1}}"></p></TD>
            </TR>
            @endforeach
          </TBODY>
        </table><br><br>
        <table width="75%"> 

          <tr>
            <td  style="border: none;">Penerima</td>
            <td  style="border: none;" align="right">Pengirim</td>
          </tr>
        </table>    
      </DIV>
      <p id="by" hidden> BY @if(isset($time->shipmentcondition->shipment_condition_name))
             {{$time->shipmentcondition->shipment_condition_name}}
             @else
            -
             @endif</p>
      
</body>
</html>

<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    text1();
     });

function text1(){
    var a=0;
    var count = document.getElementById("count").value;
    for (i = 1; i<=count;i++){
      a++;
    var text = document.getElementById("by").innerHTML;
    document.getElementById('text'+a).innerHTML = text;
    
    }
  }

  function printa(){
    document.getElementById('PRINT').style.display = 'none';
    window.print();
  }
</script>

