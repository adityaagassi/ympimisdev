<style type="text/css">
    td{
      padding-right: 5px;
      padding-left: 5px;
      padding-top: 0px;
      padding-bottom: 0px;
    }
    th{
      padding-right: 5px;
      padding-left: 5px;      
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
	<table class="table table-bordered" style="border:1px solid black; width: 80%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
	<tbody>
		      <tr>
            <td colspan="1" style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ;">Tanggal Diterima</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px; width: 25%">{{ $tanggal }}</td>
            <td colspan="1" style="border:1px solid black; font-size: 13px; width: 25%; font-weight: bold; background-color:  #e8daef ;">No PO</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px; width: 25%">{{ $no_po }}</td>
          </tr>
          <tr>
            <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ;">Nama Item</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $nama_item }}</td>
            <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ;">Kode Item</td>
            <td colspan="1"style="border:1px solid black; font-size: 12px;">{{ $no_item }}</td>
          </tr>
	</tbody>            	
	</table><br><br>
  <table class="table table-bordered" style="border:1px solid black; width: 80%; font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0">
  <tbody>
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; width: 50%; background-color:  #e8daef ">Description Item</td>
            <td colspan="2"style="border:1px solid black; font-size: 12px;">{{ $description }}</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ">Jumlah Item</td>
            <td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $qty }}</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ">Kondisi Item</td>
            <td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $condition }}</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ">Nama Penerima</td>
            <td colspan="2" style="border:1px solid black; font-size: 12px;">{{ $nama }}</td>
          </tr>
  </tbody>              
  </table>
	</center>
</div>
</body>
</html>

