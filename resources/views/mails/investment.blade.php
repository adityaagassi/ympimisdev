<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		
		table {
			border-collapse: collapse;
		}

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
</head>
<body>
	<div>
		<center>
			@foreach($data as $datas)
				<?php $id = $datas->id ?>
				<?php $posisi = $datas->posisi ?>
				<?php $reff_number = $datas->reff_number ?>
				<?php $applicant_id = $datas->applicant_id ?>
				<?php $applicant_name = $datas->applicant_name ?>
				<?php $applicant_department = $datas->applicant_department ?>
				<?php $submission_date = $datas->submission_date ?>
				<?php $category = $datas->category ?>
				<?php $subject = $datas->subject ?>
				<?php $subject_jpy = $datas->subject_jpy ?>
				<?php $type = $datas->type ?>
				<?php $objective = $datas->objective ?>
				<?php $objective_detail = $datas->objective_detail ?>
				<?php $objective_detail_jpy = $datas->objective_detail_jpy ?>
				<?php $supplier_code = $datas->supplier_code ?>
				<?php $supplier_name = $datas->supplier_name ?>
				<?php $delivery_order = $datas->delivery_order ?>
				<?php $date_order = $datas->date_order ?>
				<?php $reject_note = $datas->reject_note ?>
				<?php $comment = $datas->comment ?>
				<?php $comment_note = $datas->comment_note ?>
				<?php $reply = $datas->reply ?>
				<?php $budget_no = $datas->budget_no ?>
				<?php $budget_name = $datas->budget_name ?>
				<?php $category_budget = $datas->category_budget ?>
				<?php $total_budget = $datas->total_budget ?>
				<?php $total_pengeluaran = $datas->total_pengeluaran ?>
				<?php $status = $datas->status ?>
			@endforeach

			@if($posisi == "user")

				<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
				
				<p style="font-size: 18px;">Informasi Terkait Investment<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
				This is an automatic notification. Please do not reply to this address.
				<br>
				<h2>Investment No : {{$reff_number}}<h2>
				@if($reject_note != "")
					<h2>Not Approved</h2>
					<h3>Reason : <?= $reject_note ?></h3>
				@elseif($comment != "")
				<?php 
					$commentby = explode("/", $comment)
				?>
				<h2>Commended By : <?= $commentby[2] ?></h2>
				<h3>Question : <?= $comment_note ?></h3>
				@else
					<h2>Not Approved</h2>
				@endif
				<hr>
				<span style="font-weight: bold; background-color: orange;" style="font-size: 20px;">&#8650; <i>Click Here For</i> &#8650;</span><br>
				
				@if($comment != "")
					<a href="{{ url("investment/comment/".$id) }}" style="font-size: 20px">Reply This Message</a><br>
				@else
					<a href="{{ url('investment') }}" style="font-size: 20px">Check Investment</a><br>
				@endif


			@elseif($posisi == "acc_budget")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}}</h2>
			<h3>Subject : <?= ucfirst($subject) ?></h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Vendor</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 1%; border:1px solid black;">Budget Number</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Beginning Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget,2,",",".") ?> </td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; text-align: left;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Ending Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td>
					</tr>
					@else					
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
			<a href="{{url('investment/check/'.$id)}}">Check Investment</a><br>


			@elseif($posisi == "acc_pajak")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}} <br>
			Subject : <?= ucfirst($subject) ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Vendor</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 1%; border:1px solid black;">Budget Number</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Beginning Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Ending Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
			<a href="{{ url('investment/check/'.$id) }}">Check Investment Tax</a><br>

			@elseif($posisi == "manager" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}} <br>
			Subject : <?= ucfirst($subject) ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Vendor</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 1%; border:1px solid black;">Budget Number</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Beginning Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Ending Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>
			
			<br>

			<span style="font-weight: bold;"><i>Do you want to Approve this Investment Request?</i></span><br>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject &nbsp;</a>
	
			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>
	

			@elseif($posisi == "dgm" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}} <br>
			Subject : <?= ucfirst($subject) ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Vendor</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 1%; border:1px solid black;">Budget Number</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Beginning Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Ending Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>
			
			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>

			<br>

			<span style="font-weight: bold;"><i>Do you want to Approve this Investment Request?</i></span><br>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvedgm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>

			@elseif($posisi == "gm" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>投資・経費申請の承認<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。<br>

			<h2>投資番号 : {{$reff_number}} <br>
			件名 : <?= $subject_jpy ?> <br> 
			Subject : <?= ucfirst($subject) ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant (申請者) </td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Department (部門)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Submission Date (提出日)</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category (部類)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Type (種目)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Objective (目的)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail_jpy ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Vendor (サプライヤー)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information 予算情報</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget Number (予算番号)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Beginning Balance (最初残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Ending Balance (最終残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>
			
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>(下をクリックしてください)<br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvegm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject (却下) &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>

			@elseif($posisi == "manager_acc" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}} <br>
			Subject : <?= ucfirst($subject) ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Vendor</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 1%; border:1px solid black;">Budget Number</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Beginning Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Ending Balance</td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 1%; border:1px solid black;">Category Budget</td>
						<td style="width: 4%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black;">Amount </td>
						<td style="width: 4%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>
			
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvemanageracc/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>

			@elseif($posisi == "direktur_acc" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>投資・経費申請の承認<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。<br>

			<h2>投資番号 : {{$reff_number}} <br>
			件名 : <?= $subject_jpy ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant (申請者) </td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Department (部門)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Submission Date (提出日)</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category (部類)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Type (種目)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Objective (目的)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail_jpy ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Vendor (サプライヤー)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information 予算情報</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget Number (予算番号)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Beginning Balance (最初残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Ending Balance (最終残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>
			
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvediracc/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject (却下) &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>

			@elseif($posisi == "presdir" && $status == "approval")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Approval Investment - Expense Application <br>投資・経費申請の承認<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。<br>

			<h2>投資番号 : {{$reff_number}} <br>
			件名 : <?= $subject_jpy ?></h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant (申請者) </td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Department (部門)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Submission Date (提出日)</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category (部類)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Type (種目)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Objective (目的)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $objective ?> - <?= $objective_detail_jpy ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Vendor (サプライヤー)</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
			
				</tbody>
			</table>
			<br>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Budget Information 予算情報</th>
					</tr>
				</thead>
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget Number (予算番号)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $budget_no ?> - <?= $budget_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Beginning Balance (最初残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Ending Balance (最終残高)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_budget - $total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@else					
					<tr>
						<td style="width: 2%; border:1px solid black;">Category Budget (予算種類)</td>
						<td style="width: 3%; border:1px solid black; text-align: left !important;"><?= $category_budget ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Amount (費用)</td>
						<td style="width: 3%; border:1px solid black; text-align: right;">$ <?= number_format($total_pengeluaran,2,",",".") ?></td></td>
					</tr>
					@endif

				</tbody>
			</table>
			<br>

			<table style="border-collapse: collapse;" width="80%">
				<tbody>
					@if($category_budget != "Out Of Budget")
					<tr>
						<td style="width: 5%; text-align: left;color: blue">Amount Equivalent To Rupiah : Rp <?= number_format($total_pengeluaran * 14500,2,",",".") ?></td>
					</tr>
					<tr>
						<td style="width: 5%; text-align: left;color: blue">This Investment Contain Purchase goods / service more than Rp 10.000.000 or equivalent</td>
					</tr>
					@endif

				</tbody>
			</table>

			<br>
			
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> no&#8650;</span><br><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvepresdir/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment (保留・コメント) &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject (却下) &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring</a>

			@elseif($status == "comment" && ($posisi == "manager" || $posisi == "dgm" || $posisi == "gm" || $posisi == "manager_acc" || $posisi == "direktur_acc" || $posisi == "presdir"))

				<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
				
				<p style="font-size: 18px;">Investment Information<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
				This is an automatic notification. Please do not reply to this address.
				<br>
				<h2>Investment No : {{ $reff_number }}<h2>
				<h2>Has Been Answered By Apllicant ({{ $applicant_name}})</h2> 
				<h2>Question : <?= $comment_note ?></h2>
				<h2>Answer : <?= $reply ?></h2>
				<hr>
				
				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>

				@if($posisi == "manager")
				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				@elseif($posisi == "dgm")

				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvedgm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				@elseif($posisi == "gm")

				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvegm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				@elseif($posisi == "manager_acc")

				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvemanageracc/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				@elseif($posisi == "direktur_acc")

				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvediracc/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				@elseif($posisi == "presdir")

				<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px;" href="{{ url("investment/approvepresdir/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				@endif
				
				<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/comment/".$id) }}">&nbsp;&nbsp;&nbsp; Hold & Comment &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

				<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("investment/reject/".$id) }}">&nbsp; Reject &nbsp;</a><br>

			@elseif($posisi == "finished")

			<!-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br> -->
			<p style="font-size: 18px;">Investment Approval Completed<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment {{$reff_number}}</h2>
			<h2>Fully Approved</h2>
			<h3>Please Check Your Investment</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below To</i> &#8650;</span><br><br>
			<a href="{{ url('investment/check_pch') }}/{{ $id }}">Receive Investment</a><br>
			<a href="{{ url('investment/control') }}">Investment Monitoring & Control</a><br>
			<a href="{{ url('investment') }}">List Investment</a><br>

			<!-- Tolak -->

			@elseif($posisi == "adagio")

			<!-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br> -->
			<p style="font-size: 18px;">Informasi Terkait Investment<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment No : {{$reff_number}}</h2>
			<h3>Telah Di Approve Oleh Bagian Accounting</h3>
			<hr>
			<h2>Mohon Untuk Segera Di Upload Ke Adagio</h2>
			<h3>Kemudian Upload Bukti Approval Adagio Ke MIRAI</h3>

			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br>
			<a href="{{url('investment')}}">Upload Bukti Approval Adagio</a><br>


		@endif
			
		</center>
	</div>
</body>
</html>