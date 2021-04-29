<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 12px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		.line{
		   width: 100%; 
		   text-align: center; 
		   border-bottom: 1px solid #000; 
		   line-height: 0.1em;
		   margin: 10px 0 20px;  
		}

		.line span{
		   background:#fff; 
		   padding:0 10px;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>

		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
			<thead>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
					<td colspan="1" style="text-align: left;font-size: 11px;width: 16%"><b>Nomor Tanda Terima</b></td>
					<td colspan="3" style="text-align: left;font-size: 11px"><b>: {{$id}}</b></td>
				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
					<td colspan="1" style="text-align: left;font-size: 11px;width: 16%"><b>Tanggal</b></td>
					<td colspan="3" style="text-align: left;font-size: 11px"><b>: <?= date('d-M-y', strtotime($invoice->invoice_date)) ?></b></td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
				</tr>

				<tr>
					<td colspan="10" style="text-align: center;font-size: 30px"><b><u>Tanda Terima</u></b></td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

				<tr>
					<td colspan="10" >Yang Bertanda Tangan di bawah ini telah menerima, dokumen dari :</td>
				</tr>

				<tr>
					<td colspan="10"><b>Supplier :</b></td>
				</tr>

				<tr>
					<td colspan="6">&nbsp;&nbsp;&nbsp;<b>{{$invoice->supplier_name}}</b></td>
					<td colspan="4"><b>Telp :</b> {{$invoice->supplier_phone}}</td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;&nbsp;&nbsp;<b>{{$invoice->supplier_address}}</b></td>
					<td colspan="4"><b>Fax &nbsp;:</b> {{$invoice->supplier_fax}}</td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;&nbsp;&nbsp;<b>{{$invoice->supplier_city}}</b></td>
					<td colspan="4"><b>Attn :</b> {{$invoice->contact_name}}</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10">Dengan Perincian Sebagai Berikut :</td>
				</tr>
				<tr>
					<td colspan="2"><b>No. Invoice / Faktur / Nota</b></td>
					<td colspan="8">: {{$invoice->invoice_no}}</td>
				</tr>
				<tr>
					<td colspan="2"><b>Mata Uang</b></td>
					<td colspan="8">: {{$invoice->currency}}</td>
				</tr>
				<tr>
					<td colspan="2"><b>Nilai</b></td>
					<td colspan="8">: {{$invoice->amount}}</td>
				</tr>
				<tr>
					<td colspan="2"><b>No. NPWP</b></td>
					<td colspan="8">: {{$invoice->npwp}}</td>
				</tr>
				<tr>
					<td colspan="2"><b>No PO</b></td>
					<td colspan="8">: {{$invoice->po_number}}</td>
				</tr>
				<tr>
					<td colspan="2"><b>Pembayaran / Jatuh Tempo</b></td>
					<td colspan="4">: {{$invoice->payment_term}}</td>
					<td colspan="4"><b>/ (<?= date('d-M-y', strtotime($invoice->due_date)) ?>)</b></td>
				</tr>
				<tr>
					<td colspan="3"><b>&nbsp;&nbsp;&nbsp;1. No.Kwitansi/Official Receipt</b></td>
					<td colspan="7">: {{$invoice->kwitansi}}</td>
				</tr>
				<tr>
					<td colspan="3"><b>&nbsp;&nbsp;&nbsp;2. No.DO/Surat Jalan/Pengantar</b></td>
					<td colspan="7">: {{$invoice->surat_jalan}}</td>
				</tr>
				<tr>
					<td colspan="3"><b>&nbsp;&nbsp;&nbsp;3. No.Berita Acara Penyelesaian</b></td>
					<td colspan="7">: {{$invoice->bap}}</td>
				</tr>								
				<tr>
					<td colspan="3"><b>&nbsp;&nbsp;&nbsp;4. No.Faktur Pajak</b></td>
					<td colspan="7">: {{$invoice->faktur_pajak}}</td>
				</tr>	
				<tr>
					<td colspan="7"></td>
					<td colspan="3"><center>Pasuruan, <?= date('d-M-y', strtotime($invoice->invoice_date)) ?></center></td>
				</tr>
				<tr>
					<td colspan="7"></td>
					<td colspan="3"><center>Penerima</center></td>
				</tr>
				<tr>
					<td colspan="10"><br><br></td>
				</tr>
				<tr>
					<td colspan="7"></td>
					<td colspan="3"><center>Shega Erik Wicaksono</center></td>
				</tr>
				<tr>
					<td colspan="7"></td>
					<td colspan="3"><center>Telp: 0343-740290 Ext:1129</center></td>
				</tr>
				<tr>
					<td colspan="10">-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td>
				</tr>

			</thead>
		</table>
	</header>
	
</body>
</html>