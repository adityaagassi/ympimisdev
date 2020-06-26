<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 10px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 150px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>				
				<tr>
					<td colspan="3">&nbsp;</td>
					<td colspan="4" style="text-align: center;font-weight: bold;font-size: 16px">PURCHASE REQUISITION FORM</td>
					<td colspan="1" style="text-align: right;font-size: 12px">No:</td>
					<td colspan="2" style="text-align: right;font-size: 14px;font-weight: bold">{{ $pr[0]->no_pr }}</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>	
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Department</td>
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->department }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Group</td>
					<td>: {{ $pr[0]->group }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Date Of Submission</td>
					<td colspan="8" style="font-size: 12px;">: <?= date('d F Y', strtotime($pr[0]->submission_date)) ?></td>
				</tr>

				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Budget</td>
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->no_budget }}</td>
				</tr>

				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Receive Date</td>
					<td colspan="8" style="font-size: 12px;">: - </td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

			</thead>
		</table>
	</header>
	<main>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
			<thead>
				<tr style="font-size: 12px">
					<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Item Code</td>
					<td colspan="2" style="width:8%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Description & Specification</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Stock WIP</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Unit Price</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Amount</td>
					<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Request Date</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Status</td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($pr as $pr)
				<tr>
					<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
					<td colspan="1" style="border: 1px solid black;">{{ $pr->item_code }}</td>
					<td colspan="2" style="border: 1px solid black;">{{ $pr->item_desc }} - {{ $pr->item_spec }}</td>
					<td colspan="1" style="border: 1px solid black;">{{ $pr->item_stock }}</td>
					<td colspan="1" style="border: 1px solid black;">{{ $pr->item_qty }} {{ $pr->item_uom }}</td>
					<td colspan="1" style="border: 1px solid black;">({{$pr->item_currency}}) {{ $pr->item_price }}</td>
					<td colspan="1" style="border: 1px solid black;"><?= number_format($pr->item_amount,0,"","."); ?></td>
					<td colspan="1" style="border: 1px solid black;"><?= date('d F Y', strtotime($pr->item_request_date)) ?></td>
					<td colspan="1" style="border: 1px solid black;text-align: center;padding: 0;">OK</td>
				</tr>
				<?php $no++; ?>
				@endforeach


				<tr>
					<td colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td style="font-size: 12px;" colspan="10">Note :</td>
				</tr>
				<tr>
					<td colspan="10" style="font-size: 12px;"><?= $pr->note ?></td>
				</tr>

				<tr>
					<td colspan="10" style="font-size: 12px;" >Budget yang digunakan :</td>
				</tr>
				<tr>
					<td colspan="10" style="font-size: 12px;"></td>
				</tr>
			</tbody>
		</table>
	</main>
	<footer>
		<div class="footer">
			<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: center;" border="1">
				<thead>
					<tr>
						<td colspan="1" style="width:15%;height: 26px; border: 1px solid black;text-align: center;padding: 0">Applied By</td>
						<td colspan="1" style="width:15%;">Acknowledge By</td>
						<td colspan="1" style="width:15%;">Acknowledge By</td>
						<td colspan="1" style="width:15%;">Approve By</td>
						<td colspan="6" rowspan="3">&nbsp;</td>
					</tr>

				</thead>
				<tbody>
					<tr>
						<td colspan="1" style="height: 40px">
							@if($pr->posisi != "staff")
								<?= $pr->emp_name ?>
							@endif
						</td>
						<td colspan="1" style="height: 40px">&nbsp;</td>
						<td colspan="1" style="height: 40px">&nbsp;</td>
						<td colspan="1" style="height: 40px">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="1">User</td>
						<td colspan="1">Manager</td>
						<td colspan="1">Deputy GM</td>
						<td colspan="1">GM</td>
					</tr>
				</tbody>
			</table>
	        Page <span class="pagenum"></span>
	    </div>
	</footer>
</body>
</html>