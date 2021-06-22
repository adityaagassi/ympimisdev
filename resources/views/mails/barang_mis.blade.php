<style type="text/css">
h3{
  font-family: sans-serif;
}
 
table {
  font-family: Arial, Helvetica, sans-serif;
  color:  #17202a;
  text-shadow: 1px 1px 0px #fff;
  background: #eaebec;
  border: #ccc 1px solid;
}
 
table th {
  padding: 15px 35px;
  border-left:1px solid #e0e0e0;
  border-bottom: 1px solid #e0e0e0;
  background: #ededed;
}
 
table th:first-child{  
  border-left:none;  
}
 
table tr {
  text-align: center;
  padding-left: 20px;
}
 
table td:first-child {
  text-align: center;
  padding-left: 20px;
  border-left: 0;
}
 
table td {
  padding: 15px 35px;
  border-top: 1px solid #ffffff;
  border-bottom: 1px solid #e0e0e0;
  border-left: 1px solid #e0e0e0;
  background: #fafafa;
  background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
  background: -moz-linear-gradient(top, #fbfbfb, #fafafa);
}
 
table tr:last-child td {
  border-bottom: 0;
}
 
table tr:last-child td:first-child {
  -moz-border-radius-bottomleft: 3px;
  -webkit-border-bottom-left-radius: 3px;
  border-bottom-left-radius: 3px;
}
 
table tr:last-child td:last-child {
  -moz-border-radius-bottomright: 3px;
  -webkit-border-bottom-right-radius: 3px;
  border-bottom-right-radius: 3px;
}
 
table tr:hover td {
  background: #f2f2f2;
  background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
  background: -moz-linear-gradient(top, #f2f2f2, #f0f0f0);
}
</style>

<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div>
	<center>
	@foreach($data as $datas)
		<?php $description = $datas->description ?>
		<?php $qty = $datas->qty?>
		<?php $condition = $datas->condition ?>
		<?php $nama = $datas->nama ?>
		<?php $tanggal = $datas->tanggal ?>
		<?php $no_po = $datas->no_po ?>
		<?php $no_item = $datas->no_item ?>
		<?php $nama_item = $datas->nama_item ?>
	@endforeach
	<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
	<p style="font-size: 18px;">
	<br>(Last Update: {{ date('d-M-Y H:i:s') }})
	</p>
	This is an automatic notification. Please do not reply to this address.
	<center><h3>Receive Barang To Inventory MIS</h3></center>
	<table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
	<tbody>
		  <tr>
            <td colspan="1" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Tanggal Diterima</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $tanggal }}</td>
            <td colspan="1" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">No PO</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $no_po }}</td>
          </tr>
          <tr>
            <td colspan="1" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Nama Item</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $nama_item }}</td>
            <td colspan="1" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Kode Item</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $no_item }}</td>
          </tr>
		  <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Description Item</td>
            <td colspan="2"style="border:1px solid black; font-size: 12px;">{{ $description }}</td>
          </tr>
          <tr>
          	<td colspan="2" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Jumlah Item</td>
          	<td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $qty }}</td>
          </tr>
          <tr>
          	<td colspan="2" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Kondisi Item</td>
          	<td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $condition }}</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px;width: 18%; font-weight: bold">Nama Penerima</td>
            <td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $nama }}</td>
          </tr>
	</tbody>            	
	</table>
	</center>
</div>
</body>
</html>

